<?php

namespace App\Filters\V1;

use Illuminate\Support\Facades\Log;

class ApiFilter
{
    protected $safeParams = [];

    protected $columnMap = [];

    protected $operatorMap = [];
    public function transform($request)
    {
        $eloQuery = [];
        foreach ($this->safeParams as $keys => $operators) {
            $query = $request->query($keys);

            if (!isset($query)) {
                continue;
            }

            $column = $this->columnMap[$keys] ?? $keys;

            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    $eloQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }
        return $eloQuery;
    }
}
