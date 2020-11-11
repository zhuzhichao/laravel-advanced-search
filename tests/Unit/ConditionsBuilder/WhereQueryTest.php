<?php

namespace Tests\Unit\ConditionsBuilder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Tests\DBTestCase;
use Tests\Utils\Models\Company;
use Tests\Utils\Models\User;
use Zhuzhichao\LaravelAdvancedSearch\Meaningless;
use Zhuzhichao\LaravelAdvancedSearch\ModelScope;

class WhereQueryTest extends DBTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Company::factory()->make([
            'id' => 10,
            'name' => 'Apple',
            'age' => 30,
        ])->save();
        Company::factory()->make([
            'id' => 20,
            'name' => 'Mi',
            'age' => 10,
        ])->save();

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

    public function test_closure_value()
    {
        self::assertEquals(1, User::advanced([
            'wheres' => [
                function(Builder $builder) {
                    $builder->where('email', 'me@zhuzhichao.com');
                },
            ],
        ])->count());
    }

    public function test_expression_value()
    {
        self::assertEquals(0, User::advanced([
            'wheres' => [
                DB::raw('1=2'),
            ],
        ])->count());
    }

    public function test_model_scope_value()
    {
        self::assertEquals(1, User::advanced([
            'wheres' => [
                new ModelScope('name', 'zhuzhichao')
            ],
        ])->count());
    }

    public function test_field_with_dot_and_operator()
    {
        self::assertEquals(1, User::advanced([
            'wheres' => [
                'name.like' => '%zhi%',
                'email' => 'me@zhuzhichao.com',
                'age.le' => 18,
            ],
        ])->count());
    }

    public function test_field_without_dot_and_operator()
    {
        self::assertEquals(1, User::advanced([
            'wheres' => [
                'age' => [
                    'gt' => 19
                ],
            ],
        ])->count());
    }

    public function test_field_with_dollar_and_dot()
    {
        self::assertEquals(1, User::advanced([
            'wheres' => [
                'company$name.like' => '%pp%',
            ],
        ])->count());
    }

    public function test_field_with_dollar_in_array()
    {
        self::assertEquals(1, User::advanced([
            'wheres' => [
                'company$age' => [
                    'gte' => 1,
                    'lt' => 20,
                ],
            ],
        ])->count());
    }

    public function test_invalid_where_value()
    {
        $this->expectException(\LogicException::class);
        User::advanced([
            'wheres' => [
                'company$age' => new User(),
            ],
        ])->count();
    }

    public function test_null_where_value()
    {
        self::assertEquals(10, User::advanced([
            'wheres' => [
                'age' => null,
            ],
        ])->count());
    }

    public function test_Meaningless_where_value()
    {
        self::assertEquals(12, User::advanced([
            'wheres' => [
                'age' => new Meaningless,
            ],
        ])->count());
    }
}
