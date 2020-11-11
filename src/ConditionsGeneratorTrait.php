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
     * Get where conditions.
     *
     * @return Collection
     */
    public function getConditions(): Collection
    {
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
                return !($item instanceof Meaningless) && $item !== [];
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
            When::request($field) :
            ($item instanceof Closure ? $item() : $item);

        // Filter invalid where condition.
        // If `is null` or `is not null` , please pass any chars except null.
        if ($value instanceof Meaningless) {
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
}
