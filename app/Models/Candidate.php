<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'source',
        'owner',
        'created_by',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getAll(): mixed
    {
        return Cache::rememberForever('candidates.all', function () {
            return self::all();
        });
    }

    public static function getCandidatesByOwner(int $ownerId): Collection
    {
        return Cache::remember('candidates.owner.' . $ownerId, 3600, function () use ($ownerId) {
            return static::where('owner', $ownerId)->get();
        });
    }

    public static function getById(int $id): mixed
    {
        return Cache::rememberForever('candidate.'. $id, function () use ($id) {
            return self::findOrFail($id);
        });
    }
}
