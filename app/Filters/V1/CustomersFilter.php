<?php

namespace App\Filters\V1;
use App\Filters\V1\ApiFilter;

class CustomersFilter extends ApiFilter

{
    protected $safeParams = [
        'name' => ['eq'],
        'type' => ['eq'],
        'email' => ['eq'],
        'address' => ['eq'],
        'city' => ['eq'],
        'state' => ['eq'],
        'postalCode' => ['eq', 'gt', 'lt'],
    ];

    protected $columnMap = [
        'postalCode' => 'postal_code'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>='
    ];
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
