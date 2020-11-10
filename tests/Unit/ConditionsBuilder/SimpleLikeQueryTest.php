<?php

namespace Tests\Unit\ConditionsBuilder;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use MatrixLab\LaravelAdvancedSearch\ModelScope;
use Tests\DBTestCase;
use Tests\Utils\Models\Company;
use Tests\Utils\Models\Post;
use Tests\Utils\Models\User;

class SimpleLikeQueryTest extends DBTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Company::factory()->make([
            'name' => 'this name is testing for search, first name',
        ])->save();
        Company::factory()->make([
            'name' => 'this name is testing for search, double first name',
        ])->save();
        Company::factory()->make([
            'name' => 'this name is testing for search, second name',
        ])->save();

        User::factory()->make([

        ]);
    }

    public function test_without_scope()
    {
        self::assertEquals(2, Company::advanced(['wheres' => [
            'name.like' => '%first%',
        ]])->count());
    }

    public function test_with_cope()
    {

    }
}
