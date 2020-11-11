<?php

namespace Tests\Unit\ConditionsBuilder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Tests\DBTestCase;
use Tests\Utils\Models\Company;
use Tests\Utils\Models\User;
use Zhuzhichao\LaravelAdvancedSearch\Meaningless;
use Zhuzhichao\LaravelAdvancedSearch\ModelScope;

class HavingQueryTest extends DBTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->count(10)->create();
        User::factory()->make([
            'name' => 'zhuzhichao',
            'email' => 'me@zhuzhichao.com',
            'age' => 20,
            'company_id' => 10,
        ])->save();
        User::factory()->make([
            'name' => 'Taylor Otwell',
            'email' => 'taylor@laravel.com',
            'age' => 20,
            'company_id' => 20,
        ])->save();
    }

    public function test_having()
    {
        self::assertEquals(1, User::advanced([
            'group_by' => 'age',
            'having' => [
                'age_count.gt' => 1
            ]
        ])->selectRaw('count(`age`) as `age_count`, `age`')->get()->count());
    }
}
