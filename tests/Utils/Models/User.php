<?php

namespace Tests\Utils\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tests\Factories\UserFactory;

/**
 * Tests\Utils\Models\Company.
 *
 * @property int                             $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User searchKeyword($key)
 */
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

    public function scopeName($query, $value)
    {
        $query->where('name', $value);

        return $query;
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
