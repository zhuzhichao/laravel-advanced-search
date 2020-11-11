<?php

namespace Zhuzhichao\LaravelAdvancedSearch;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ConditionsGenerator
{
    use ConditionsGeneratorTrait;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var array
     */
    private $customParams = [];

    public static $allowParamKeys = [
        'wheres',
        'order_by',
        'group_by',
        'having',
    ];
    /**
     * @var array
     */
    private $params;

    /**
     * ConditionsGenerator constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
        $this->parseParams();
    }

    public function getRequestConditions(): array
    {
        return $this->getConditions()->toArray();
    }

    private function parseParams(): void
    {
        foreach (self::$allowParamKeys as $key) {
            if (isset($this->params[$key])) {
                $this->customParams[$key] = $this->params[$key];
                unset($this->params[$key]);
            }
        }

        $this->customParams['wheres'] = array_merge($this->customParams['wheres'], $this->params);
    }

    protected function wheres(): array
    {
        return $this->customParams['wheres'] ?? [];
    }

    protected function order(): array
    {
        $order = $this->customParams['order_by'] ?? [];

        return Arr::wrap($order);
    }

    protected function groupBy(): array
    {
        $group = $this->customParams['group_by'] ?? [];

        return Arr::wrap($group);
    }

    protected function having(): array
    {
        $having = $this->customParams['having'] ?? [];

        return Arr::wrap($having);
    }
}
