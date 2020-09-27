<?php

namespace Zhuzhichao\LaravelAdvancedSearch;

use Illuminate\Http\Request;

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
        'search_where',
        'search_order',
        'search_group',
        'search_having',
    ];
    /**
     * @var array
     */
    private $params;

    /**
     * ConditionsGenerator constructor.
     * @param Request $request
     * @param array $params
     */
    public function __construct(Request $request, array $params = [])
    {
        $this->request = $request;
        $this->params  = $params;
        $this->parseParams();
    }

    public function getRequestConditions(): array
    {
        return $this->getConditions($this->request->input())->toArray();
    }

    private function parseParams(): void
    {
        foreach (self::$allowParamKeys as $key) {
            if (isset($this->params[$key])) {
                $this->customParams[$key] = $this->params[$key];
            }
        }

        if(empty($this->customParams)) {
            $this->customParams['search_where'] = $this->params;
        }
    }

    protected function wheres(): array
    {
        return $this->customParams['search_where'] ?? [];
    }

    protected function order(): array
    {
        return $this->customParams['search_order'] ?? [];
    }

    protected function groupBy(): array
    {
        return $this->customParams['search_group'] ?? [];
    }

    protected function having(): array
    {
        return $this->customParams['search_having'] ?? [];
    }
}
