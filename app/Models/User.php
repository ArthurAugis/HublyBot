<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'discord_id', 'avatar', 'discord_token', 'discord_refresh_token'])]
#[Hidden(['password', 'remember_token', 'discord_token', 'discord_refresh_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function bots()
    {
        return $this->hasMany(Bot::class);
    }

    public function activePlan()
    {
        $activeOrder = $this->orders()->where('status', 'paid')->with('plan')->latest()->first();
        return $activeOrder ? $activeOrder->plan : null;
    }

    public function maxBotsLimit()
    {
        $plan = $this->activePlan();
        if (!$plan) {
            return 1; // Free tier limit
        }
        if ($plan->slug === 'premium') {
            return 3;
        }
        if ($plan->slug === 'pro') {
            return 10;
        }
        return 1;
    }
}
