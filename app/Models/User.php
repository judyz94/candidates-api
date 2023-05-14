<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = [
        'username',
        'password',
        'last_login',
        'is_active',
        'role',
    ];

    public function createdCandidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'created_by');
    }

    public function getCandidates(): Collection
    {
        return ($this->role === 'manager') ? Candidate::all() : Candidate::where('owner', $this->id)->get();
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'role' => $this->role,
        ];
    }
}
