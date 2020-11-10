<?php

namespace Tests\Utils\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tests\Factories\CompanyFactory;
use Tests\Factories\UserFactory;

class User extends Authenticatable
{
    use SoftDeletes;
    use HasFactory;

    /**
     * @var mixed[]
     */
    protected $guarded = [];

    protected $dates = [
        'deleted_at',
    ];

    public function getTaskCountAsString(): string
    {
        if (! $this->relationLoaded('tasks')) {
            return 'This relation should have been preloaded via @with';
        }

        return "User has {$this->tasks->count()} tasks.";
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeCompanyName(Builder $query, array $args): Builder
    {
        return $query->whereHas('company', function (Builder $q) use ($args): void {
            $q->where('name', $args['company']);
        });
    }

    public function scopeSearchKeyword($q, $key)
    {
        $key = trim($key, ' ');
        $key = trim($key, '%');
        $key = "%{$key}%";
        $q->where('name', 'like', $key);

        return $q;
    }

    protected static function newFactory()
    {
        return new UserFactory;
    }
}
