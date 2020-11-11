<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Zhuzhichao\LaravelAdvancedSearch\When;

class WhenTest extends TestCase
{
    private $successString = 'This is success string';
    private $failString = 'This is fail string';

    public function test_fail()
    {
        $when = When::make(false)->success($this->successString)->fail($this->failString);

        self::assertEquals($this->failString, $when->result());
        self::assertNotEquals($this->successString, $when->result());
    }

    public function test_success()
    {
        $when = When::make(true)->success($this->successString)->fail($this->failString);

        self::assertEquals($this->successString, $when->result());
        self::assertNotEquals($this->failString, $when->result());
    }

    public function test_success_and_fail_closure()
    {
        $when = When::make('name')->success(function ($value) {
            return $value.':success';
        })->fail(function ($value) {
            return $value.':fail';
        });

        self::assertEquals('name:success', $when->result());

        $when = When::make(false, 'name')->success(function ($value) {
            return $value.':success';
        })->fail(function ($value) {
            return $value.':fail';
        });

        self::assertEquals('name:fail', $when->result());
    }
}
