<?php

namespace Tests\Unit\ConditionsBuilder;

use Tests\DBTestCase;
use Tests\Utils\Models\User;

class HavingQueryTest extends DBTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        factory(User::class, 10)->create();
        factory(User::class)->make([
            'name' => 'zhuzhichao',
            'email' => 'me@zhuzhichao.com',
            'age' => 20,
            'company_id' => 10,
        ])->save();
        factory(User::class)->make([
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
                'age_count.gt' => 1,
            ],
        ])->selectRaw('count(`age`) as `age_count`, `age`')->get()->count());
    }
}
