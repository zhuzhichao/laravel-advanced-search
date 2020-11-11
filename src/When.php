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
    private $whenValue;
    private $successValue;
    private $failValue;

    /**
     * When constructor.
     *
     * @param $whenCondition
     * @param null $param
     */
    public function __construct($whenCondition, $param = null)
    {
        $this->whenCondition = (bool)$whenCondition;
        $this->whenValue = $param ?? $whenCondition;
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
     * @return mixed
     */
    public function result()
    {
        $result = $this->whenCondition === true ? $this->successValue : $this->failValue;

        if ($result instanceof Closure) {
            return $result($this->whenValue);
        }

        return $result;
    }
}
