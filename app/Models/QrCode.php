<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QrCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'folder_id',
        'team_id',
        'name',
        'short_code',
        'type',
        'is_dynamic',
        'content',
        'design',
        'custom_domain',
        'resolution',
        'format',
        'file_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_dynamic' => 'boolean',
            'content' => 'array',
            'design' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function scans(): HasMany
    {
        return $this->hasMany(QrScan::class);
    }

    public function getUrlAttribute(): string
    {
        $domain = $this->custom_domain ?: config('app.url');
        return "{$domain}/{$this->short_code}";
    }

    public function getTotalScansAttribute(): int
    {
        return $this->scans()->count();
    }

    public function getUniqueScansAttribute(): int
    {
        return $this->scans()->where('is_unique', true)->count();
    }

    public function getLastScanAttribute()
    {
        return $this->scans()->latest('scanned_at')->first();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    public function canBeEdited(): bool
    {
        return $this->is_dynamic && $this->user->canAccessAdvancedFeatures();
    }

    public function getContentForType(): string
    {
        $typeClass = "App\\Services\\QrTypes\\" . ucfirst($this->type) . "QrType";
        
        if (class_exists($typeClass)) {
            return (new $typeClass)->generateContent($this->content);
        }

        return $this->content['url'] ?? $this->content['text'] ?? '';
    }

    /**
     * Carregar estatísticas detalhadas do QR Code
     */
    public function loadStats(): void
    {
        // Carregar estatísticas em atributos dinâmicos para evitar N+1
        $this->setAttribute('stats_total_scans', $this->scans()->count());
        $this->setAttribute('stats_unique_scans', $this->scans()->where('is_unique', true)->count());
        $this->setAttribute('stats_today_scans', $this->scans()->whereDate('scanned_at', today())->count());
        $this->setAttribute('stats_this_week_scans', $this->scans()
            ->whereBetween('scanned_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count());
        $this->setAttribute('stats_this_month_scans', $this->scans()
            ->whereMonth('scanned_at', now()->month)
            ->whereYear('scanned_at', now()->year)
            ->count());
        $this->setAttribute('stats_last_month_scans', $this->scans()
            ->whereMonth('scanned_at', now()->subMonth()->month)
            ->whereYear('scanned_at', now()->subMonth()->year)
            ->count());
        $this->setAttribute('stats_last_scan', $this->scans()->latest('scanned_at')->first());
    }
}