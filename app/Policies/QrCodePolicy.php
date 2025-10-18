<?php

namespace App\Policies;

use App\Models\QrCode;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QrCodePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, QrCode $qrCode): bool
    {
        return $user->id === $qrCode->user_id || 
               ($qrCode->team_id && $user->teams()->where('team_id', $qrCode->team_id)->exists());
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, QrCode $qrCode): bool
    {
        return $user->id === $qrCode->user_id || 
               ($qrCode->team_id && $user->teams()->where('team_id', $qrCode->team_id)->wherePivot('role', '!=', 'member')->exists());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, QrCode $qrCode): bool
    {
        return $user->id === $qrCode->user_id || 
               ($qrCode->team_id && $user->teams()->where('team_id', $qrCode->team_id)->wherePivot('role', 'owner')->exists());
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, QrCode $qrCode): bool
    {
        return $user->id === $qrCode->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, QrCode $qrCode): bool
    {
        return $user->id === $qrCode->user_id;
    }
}
