<?php

namespace ApiAutoPilot\ApiAutoPilot;

use ApiAutoPilot\ApiAutoPilot\Exceptions\MalformedQueryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SearchResolver
{
    protected Model $model;
    protected array $queries;
    protected array $filters = [];

    public function setModel(Model $model): static
    {
        $this->model = $model;
        return $this;
    }

    public function setRequestQueries(array $queries): static
    {
        $this->queries = $queries;
        return $this;
    }


    /**
     * @throws MalformedQueryException
     */
    public function validateQuery(): static
    {
        foreach ($this->queries as $column => $value) {
            if (!is_array(($value))) {
                throw new MalformedQueryException();
            }
        }
        return $this;
    }

    public function reformOperators(): static
    {
        foreach ($this->queries as $column => $value) {
            $operator = $this->determineOperator(array_key_first($value));
            $this->filters[] = [$column, $operator, $operator === 'LIKE' ? '%' . current(array_values($value)) . '%' : current(array_values($value))];
        }
        return $this;
    }

    public function runQuery(): JsonResponse
    {
        $pagination = config('autopilot-api.settings.' . $this->model::class . '.pagination');
        $results = ($this->model)::where($this->filters);
        if ($pagination){
            return response()->json($results->paginate($pagination));
        }
        return response()->json($results->get());
    }


    protected function determineOperator(string $query): string
    {
        if (Str::contains($query, 'eq')) {
            return '=';
        } elseif (Str::contains($query, 'gt')) {
            return ">";
        } elseif (Str::contains($query, 'gte')) {
            return ">=";
        } elseif (Str::contains($query, 'lt')) {
            return "<";
        } elseif (Str::contains($query, 'lte')) {
            return "<=";
        } elseif (Str::contains($query, 'like')) {
            return 'LIKE';
        }
        return '';
    }
}
