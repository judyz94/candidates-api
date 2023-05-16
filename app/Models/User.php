<?php

namespace App\Models;

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

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isOwner(int $candidateId): bool
    {
        return $this->id === Candidate::find($candidateId)->owner;
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
        ];
    }
}
