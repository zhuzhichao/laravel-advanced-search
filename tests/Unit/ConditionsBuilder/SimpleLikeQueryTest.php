<?php

namespace Tests\Unit\ConditionsBuilder;

use Tests\DBTestCase;
use Tests\Utils\Models\Company;
use Tests\Utils\Models\User;

class SimpleLikeQueryTest extends DBTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Company::factory()->make([
            'name' => 'Apple',
        ])->save();
        Company::factory()->make([
            'name' => 'Mi',
        ])->save();

        User::factory()->make([
            'name' => 'Guilherme Pressutto',
        ])->save();
        User::factory()->make([
            'name' => 'Zhichao Zhu',
        ])->save();
    }

    public function test_model_with_search_keyword_method()
    {
        self::assertEquals(1, User::advanced(['keyword' => 'herme'])->count());
    }

    public function test_model_without_search_keyword_method()
    {
        self::assertEquals(Company::query()->count(), Company::advanced([
            'keyword' => 'Chou',
        ])->count());
    }
}
