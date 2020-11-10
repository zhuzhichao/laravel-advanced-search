<?php

namespace Tests\Unit\ConditionsBuilder;

use Tests\DBTestCase;
use Tests\Utils\Models\Company;
use Tests\Utils\Models\User;

class WhereQueryTest extends DBTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->count(10)->create();
        User::factory()->make([
            'name' => 'zhuzhichao',
            'email' => 'me@zhuzhichao.com',
        ])->save();
    }

    public function test_model_without_search_keyword_method()
    {
        self::assertEquals(Company::count(), Company::advanced([
            'keyword' => 'Chou'
        ])->count());
    }

    public function test_model_with_search_keyword_method()
    {
        self::assertEquals(1, User::advanced([
            'keyword' => 'zhichao'
        ])->count());
    }
}
