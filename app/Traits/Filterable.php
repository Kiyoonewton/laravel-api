<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

trait Filterable
{
    public function applyFilters(Builder $builder, array $filters): Builder
    {
        foreach ($filters as $config) {
            if (!empty($config['column']) && !empty($config['operator']) && isset($config['value'])) {
                // Special case for filtering by category.name using whereHas
                if ($config['column'] === 'category.name') {
                    $this->filterByCategory($builder, $config);
                } elseif ($config['column'] === 'tag.name') {
                    $this->filterByTag($builder, $config);
                } else {
                    $this->filterByColumn($builder, $config);
                }
            }
        }

        return $builder;
    }

    protected function filterByCategory(Builder $builder, array $config): void
    {
        $builder->whereHas('category', function ($query) use ($config) {
            $this->applyCondition($query, $config);
        });
    }

    protected function filterByTag(Builder $builder, array $config): void
    {
        Log::info('filterQuery', [$config]); // Log the filter for debugging

        $builder->whereHas('tags', function ($query) use ($config) {
            $this->applyCondition($query, $config);
        });
    }

    protected function filterByColumn(Builder $builder, array $config): void
    {
        $this->applyCondition($builder, $config);
    }

    protected function applyCondition(Builder $query, array $config): void
    {
        if ($config['operator'] === '!=') {
            $query->where($config['column'], '!=', $config['value']);
        } elseif ($config['operator'] === 'in') {
            $query->whereIn($config['column'], (array)$config['value']);
        } elseif ($config['operator'] === 'like') {
            $query->where($config['column'], 'like', '%' . $config['value'] . '%');
        } else {
            $query->where($config['column'], $config['operator'], $config['value']);
        }
    }
}
