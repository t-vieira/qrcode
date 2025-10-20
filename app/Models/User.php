<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'trial_ends_at',
        'subscription_status',
        'subscription_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationships
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'authorized');
    }

    public function qrCodes()
    {
        return $this->hasMany(QrCode::class);
    }

    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_user')->withPivot('role', 'permissions');
    }

    public function ownedTeams()
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function customDomains()
    {
        return $this->hasMany(CustomDomain::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * Business Logic
     */
    public function isOnTrial(): bool
    {
        return $this->subscription_status === 'trialing' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscription_status === 'active' || $this->isOnTrial();
    }

    public function canAccessAdvancedFeatures(): bool
    {
        return $this->hasActiveSubscription();
    }

    public function startTrial(): void
    {
        $this->update([
            'subscription_status' => 'trialing',
            'trial_ends_at' => now()->addDays(7),
        ]);
    }

    /**
     * Verificar se deve mostrar informações de trial
     */
    public function shouldShowTrialInfo(): bool
    {
        return $this->subscription_status === 'trialing' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    /**
     * Obter dias restantes do trial
     */
    public function getTrialDaysRemaining(): int
    {
        if (!$this->trial_ends_at) {
            return 0;
        }
        
        return max(0, $this->trial_ends_at->diffInDays(now()));
    }
}
