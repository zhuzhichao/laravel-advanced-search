<?php

namespace Tests\Unit\ConditionsBuilder;

use Tests\DBTestCase;
use Tests\Utils\Models\User;

class OrderByQueryTest extends DBTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        factory(User::class)->make([
            'id' => 1,
            'name' => 'zhuzhichao',
            'email' => 'me@zhuzhichao.com',
            'age' => 18,
            'company_id' => 10,
        ])->save();
        factory(User::class)->make([
            'id' => 2,
            'name' => 'jack',
            'email' => 'jack@lavale.com',
            'age' => 19,
            'company_id' => 10,
        ])->save();
        factory(User::class)->make([
            'id' => 3,
            'name' => 'Taylor Otwell',
            'email' => 'taylor@laravel.com',
            'age' => 20,
            'company_id' => 20,
        ])->save();
        factory(User::class)->make([
            'id' => 4,
            'name' => 'Tom',
            'email' => 'tom@laravel.com',
            'age' => 21,
            'company_id' => 20,
        ])->save();
    }

    public function test_order_string()
    {
        self::assertEquals(1, User::advanced([
            'order_by' => '+age',
        ])->value('id'));
        self::assertEquals(4, User::advanced([
            'order_by' => '-age',
        ])->value('id'));
    }

    public function test_order_array()
    {
        self::assertEquals(2, User::advanced([
            'order_by' => [
                '+company_id',
                '-age',
            ],
        ])->value('id'));
        self::assertEquals(1, User::advanced([
            'order_by' => [
                '+company_id',
                '+age',
            ],
        ])->value('id'));
        self::assertEquals(3, User::advanced([
            'order_by' => [
                '-company_id',
                '+age',
            ],
        ])->value('id'));
        self::assertEquals(4, User::advanced([
            'order_by' => [
                '-company_id',
                '-age',
            ],
        ])->value('id'));
    }
}
