<?php

namespace App\Services\Pagination;

class Pagination
{
    protected $total;

    protected $perPage;

    protected $currentPage;

    protected $lastPage;

    protected $from;

    protected $to;

    protected $data = [];

    public function __construct(
        int $total,
        int $perPage,
        int $currentPage,
        int $lastPage,
        int $from,
        int $to,
        array $data
    ) {
        $this->total = $total;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
        $this->lastPage = $lastPage;
        $this->to = $to;
        $this->from = $from;
        $this->data = $data;
    }

    public function toArray(string $dataKey = 'data'): array
    {
        return [
            'pagination' => [
                'total' => $this->total,
                'per_page' => $this->perPage,
                'current_page' => $this->currentPage,
                'last_page' => $this->lastPage,
                'from' => $this->from,
                'to' => $this->to,
            ],
            $dataKey => $this->data
        ];
    }
}