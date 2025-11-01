<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'qr_code_id',
        'ip_address',
        'user_agent',
        'device_type',
        'device_model',
        'os',
        'os_version',
        'browser',
        'browser_version',
        'is_robot',
        'country',
        'city',
        'region',
        'region_code',
        'postal_code',
        'timezone',
        'latitude',
        'longitude',
        'isp',
        'organization',
        'as_number',
        'is_mobile_connection',
        'is_proxy',
        'is_hosting',
        'language',
        'referer',
        'protocol',
        'is_unique',
        'scanned_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_unique' => 'boolean',
            'is_robot' => 'boolean',
            'is_mobile_connection' => 'boolean',
            'is_proxy' => 'boolean',
            'is_hosting' => 'boolean',
            'scanned_at' => 'datetime',
        ];
    }

    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(QrCode::class);
    }

    public function getLocationAttribute(): ?string
    {
        if ($this->city && $this->country) {
            return "{$this->city}, {$this->country}";
        }

        return null;
    }

    public function getCoordinatesAttribute(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return "{$this->latitude}, {$this->longitude}";
        }

        return null;
    }

    public function getFullLocationAttribute(): ?string
    {
        $parts = [];
        
        if ($this->city) {
            $parts[] = $this->city;
        }
        
        if ($this->region) {
            $parts[] = $this->region;
        }
        
        if ($this->country) {
            $parts[] = $this->country;
        }
        
        return !empty($parts) ? implode(', ', $parts) : null;
    }

    public function getBrowserWithVersionAttribute(): ?string
    {
        if (!$this->browser) {
            return null;
        }
        
        return $this->browser_version 
            ? "{$this->browser} {$this->browser_version}"
            : $this->browser;
    }

    public function getOsWithVersionAttribute(): ?string
    {
        if (!$this->os) {
            return null;
        }
        
        return $this->os_version 
            ? "{$this->os} {$this->os_version}"
            : $this->os;
    }
}