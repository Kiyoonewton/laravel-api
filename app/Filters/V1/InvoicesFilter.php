<?php

namespace App\Filters\V1;

use App\Filters\V1\ApiFilter;

class InvoicesFilter extends ApiFilter
{
    protected $safeParams = [
        'customerId' => ['eq'],
        'status' => ['eq', 'ne'],
        'billedDate' => ['eq', 'gt', 'lt', 'gte', 'lte'],
        'paidDate' => ['eq', 'gt', 'lt', 'gte', 'lte'],
        'amount' => ['eq', 'gt', 'lt', 'gte', 'lte'],
    ];

    protected $columnMap = [
        'postalCode' => 'postal_code',
        'paidDate' => 'paid_date',
        'billedDate' => 'billed_date'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
        'ne' => '!='
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
