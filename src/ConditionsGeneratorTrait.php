<?php

namespace Zhuzhichao\LaravelAdvancedSearch;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;

/**
 * Laravel advanced search condition generator.
 *
 * Trait ConditionsGeneratorTrait
 */
trait ConditionsGeneratorTrait
{
    /**
     * Exporting conditions.
     *
     * @var array
     */
    protected $conditions = [
        'wheres' => [],
    ];

    /**
     * Request input arguments.
     *
     * @var array
     */
    protected $inputArgs;

    /**
     * Append conditions.
     *
     * @param $appendItems
     *
     * @return static
     */
    public function appendConditions($appendItems)
    {
        // Merge wheres.
        $this->conditions['wheres'] = array_merge($this->conditions['wheres'], Arr::get($appendItems, 'wheres', []));
        Arr::forget($appendItems, 'wheres');

        // Merge conditions to root.
        $this->conditions = array_merge($this->conditions, $appendItems);

        return $this;
    }

    /**
     * @param array $inputArgs
     *
     * @return static
     */
    public function setInputArgs(array $inputArgs)
    {
        $this->inputArgs = $inputArgs;

        return $this;
    }

    protected function wheres(): array
    {
        return [];
    }

    protected function order(): array
    {
        return [];
    }

    protected function groupBy(): array
    {
        return [];
    }

    protected function having(): array
    {
        return [];
    }

    /**
     * Get input value after fired.
     *
     * @param         $requestKey
     * @param Closure|null $fire
     *
     * @return array|mixed|string
     */
    protected function fireInput($requestKey, Closure $fire = null)
    {
        if (! $this->isValidInput($requestKey)) {
            return null;
        }

        $inputArg = $this->getInputArgs($requestKey);

        return $fire ? $fire($inputArg) : $inputArg;
    }

    /**
     * Get where conditions.
     *
     * @param $args
     *
     * @return Collection
     */
    public function getConditions($args): Collection
    {
        $this->inputArgs = $args;

        // Handle input arguments.
        $this->handleInputArgs();

        // Sort order by params.
        $this->handleSort();

        // Group by.
        $this->handleGroupBy();

        // Handle having
        $this->handleHaving();

        // Merge where from static object.
        $this->conditions['wheres'] = array_merge($this->conditions['wheres'], $this->wheres());

        // Map where key and value, export structure for Laravel advanced search builder.
        $this->conditions['wheres'] = collect($this->conditions['wheres'])
            ->filter(function($item) {
                return $item !== null && $item !== [];
            })
            ->mapWithKeys(function ($item, $key) {
                return $this->generateWhereKeyValue($item, $key);
            })->all();

        return collect($this->conditions);
    }

    /**
     * Handle where array.
     *
     * @param $item
     * @param $key
     *
     * @return array
     */
    private function generateWhereKeyValue($item, $key): array
    {
        // Fire When object.
        if ($item instanceof When) {
            $item = $item->result();
        }

        if (is_int($key) && ($item instanceof Closure || $item instanceof Expression || $item instanceof ModelScope)) {
            return [$key => $item];
        }

        // Default index, item is the field ; other index, key is the field.
        $field = is_int($key) ? $item : $key;

        if (is_null($field)) {
            return [];
        }

        // Get input value for query.
        $value = is_int($key) ?
            $this->getInputArgs($field) :
            ($item instanceof Closure ? $item() : $item);

        // Filter invalid where condition.
        // If `is null` or `is not null` , please pass any chars except null.
        if ($value === '' || is_null($value)) {
            return [];
        }

        return [$field => $value];
    }

    /**
     * Sort conditions.
     *
     * @return $this
     */
    protected function handleSort(): self
    {
        $sorts = [];
        // todo: `sort` & `sorts` path can be from config.
        if (Arr::has($this->inputArgs, 'paginator.sort')) {
            $sorts = array_merge([$this->getInputArgs('paginator.sort')], $sorts);
            Arr::forget($this->inputArgs, 'paginator.sort');
        }
        if (Arr::has($this->inputArgs, 'paginator.sorts')) {
            $sorts = array_merge($this->getInputArgs('paginator.sorts'), $sorts);
            Arr::forget($this->inputArgs, 'paginator.sorts');
        }
        $sorts = collect(array_values(array_unique($sorts)))->filter();
        $sorts = collect($sorts)->merge($this->order());
        $orders = [];
        foreach ($sorts as $sort) {
            if (is_string($sort)) {
                if (! Str::startsWith($sort, ['+', '-'])) {
                    continue;
                }
                $field = substr($sort, 1);
                if (! array_key_exists($field, $orders)) {
                    $orders[$field] = strpos($sort, '+') === 0 ? 'asc' : 'desc';
                }
            }

            if ($sort instanceof Expression) {
                $orders[] = $sort;
            }
        }

        $this->appendConditions(['order' => $orders]);

        return $this;
    }

    /**
     * Handle group by params.
     *
     * @return $this
     */
    protected function handleGroupBy(): self
    {
        $groupBy = $this->groupBy();

        // Filter groupBy params.
        if (!is_string($groupBy) && !is_array($groupBy) && !($groupBy instanceof When) && !($groupBy instanceof Expression)) {
            $groupBy = [];
        }

        if (!is_array($groupBy)) {
            $groupBy = [$groupBy];
        }

        $this->appendConditions([
            'groupBy' => collect($groupBy)->filter()->map(function ($item) {
                    if ($item instanceof When) {
                        $item = $item->result();
                    }

                    return $item;
                })->unique()->values()->all()
        ]);

        return $this;
    }

    /**
     * Handle having params.
     *
     * @return $this
     */
    protected function handleHaving(): self
    {
        $having = $this->having();

        if (!is_array($having)) {
            $having = [$having];
        }

        $having = collect($having)->filter()->map(function ($item) {
            if ($item instanceof When) {
                $item = $item->result();
            }

            return $item;
        })->all();

        $havings = [];

        foreach ($having as $index => $item) {
            if (is_int($index) && is_array($item)) {
                $havings = array_merge($havings, $item);
            } else {
                $havings[$index] = $item;
            }
        }

        $this->appendConditions([
            'having' => $havings
        ]);

        return $this;
    }

    /**
     * Handle input arguments.
     *
     * @return $this
     */
    protected function handleInputArgs(): self
    {
        // Handle more.
        $moreInputs = $this->getInputArgs('more');
        if (is_array($moreInputs) && ! empty($moreInputs)) {
            unset($this->inputArgs['more']);
            $this->inputArgs = array_merge($this->inputArgs, $moreInputs);
        }

        return $this;
    }

    /**
     * Get input arguments.
     *
     * @param null $key
     * @param null $default
     *
     * @return array|mixed
     */
    public function getInputArgs($key = null, $default = null)
    {
        return is_null($key) ? $this->inputArgs : Arr::get($this->inputArgs, $key, $default);
    }

    /**
     * Check the input is valid.
     *
     * @param $requestKey
     *
     * @return bool
     */
    protected function isValidInput($requestKey): bool
    {
        return Arr::has($this->inputArgs, $requestKey)
            && Arr::get($this->inputArgs, $requestKey) !== []
            && Arr::get($this->inputArgs, $requestKey) !== null
            && Arr::get($this->inputArgs, $requestKey) !== '';
    }

    /**
     * Append something to input value' tail.
     *
     * @param        $requestKey
     * @param string $append
     *
     * @return array|mixed|string
     */
    protected function appendInput($requestKey, $append = '')
    {
        return $this->fireInput($requestKey, function ($value) use ($append) {
            return $value.$append;
        });
    }

    /**
     * When `$value` equal true, execute callback.
     *
     * @param $value
     * @param $callback
     * @param $default
     *
     * @return mixed
     */
    protected function when($value, $callback, $default = null)
    {
        $value = is_string($value) ? trim($value) : $value;
        if ($value !== '' && $value !== [] && ! is_null($value)) {
            return $callback instanceof Closure ? $callback($this->inputArgs) : $callback;
        }

        if ($default) {
            return $default instanceof Closure ? $default($this->inputArgs) : $default;
        }

        return null;
    }
}
