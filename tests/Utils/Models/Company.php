<?php

namespace Tests\Utils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\Factories\CompanyFactory;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeName($query, $value)
    {
        $query->where('name', $value);

        return $query;
    }

    protected static function newFactory()
    {
        return new CompanyFactory;
    }
}
