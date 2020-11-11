<?php

namespace Tests\Unit\ConditionsBuilder;

use Tests\DBTestCase;
use Tests\Utils\Models\User;

class AttachGroupByTest extends DBTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->count(10)->create();
        User::factory()->make([
            'name' => 'zhuzhichao',
            'email' => 'me@zhuzhichao.com',
            'age' => 18,
            'company_id' => 10,
        ])->save();
        User::factory()->make([
            'name' => 'Taylor Otwell',
            'email' => 'taylor@laravel.com',
            'age' => 20,
            'company_id' => 20,
        ])->save();
    }

    public function test_group()
    {
        self::assertEquals(3, User::advanced([
            'group_by' => 'age',
        ])->get()->count());
    }
}
