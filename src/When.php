<?php

namespace Zhuzhichao\LaravelAdvancedSearch;

use Closure;

/**
 * Class When
 * @package Zhuzhichao\LaravelAdvancedSearch
 */
class When
{
    private $whenCondition;
    private $successValue;
    private $failValue;

    /**
     * When constructor.
     *
     * @param $value
     * @param mixed ...$args
     */
    public function __construct($value, ...$args)
    {
        if ($value instanceof Closure) {
            $this->whenCondition = (bool)$value(...$args);
        } else {
            $this->whenCondition = (bool)$value;
        }

        $this->successValue = new Meaningless;
        $this->failValue = new Meaningless;
    }

    /**
     * Factory function for Request instance.
     *
     * @param $key
     * @return When
     */
    public static function request($key): When
    {
        $requestValue = request($key);
        $when = new static($requestValue);

        if ($requestValue) {
            $when->success($requestValue);
        }

        return $when;
    }

    /**
     * Factory function.
     *
     * @param mixed ...$args
     * @return When
     */
    public static function make(...$args): When
    {
        return new static(...$args);
    }

    /**
     * When success.
     *
     * @param $value
     * @return $this
     */
    public function success($value): self
    {
        $this->successValue = $value;

        return $this;
    }

    /**
     * When fail.
     *
     * @param $value
     * @return $this
     */
    public function fail($value): self
    {
        $this->failValue = $value;

        return $this;
    }

    /**
     * Send result.
     *
     * @param bool $handle
     * @return mixed
     */
    public function result($handle = false)
    {
        $result = $this->whenCondition === true ? $this->successValue : $this->failValue;

        if ($handle && $result instanceof Closure) {
            return $result();
        }

        return $result;
    }
}
