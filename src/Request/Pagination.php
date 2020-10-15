<?php


namespace Webleit\ZohoCrmApi\Request;


class Pagination
{
    protected $perPage = 200;
    protected $page = 1;
    protected $count = 0;
    protected $moreRecords = false;

    public function __construct(array $params = [])
    {
        $this->perPage = $params['per_page'] ?? $this->perPage;
        $this->page = $params['page'] ?? $this->page;
        $this->count = $params['count'] ?? $this->count;
        $this->moreRecords = $params['more_records'] ?? $this->moreRecords;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function count(): int
    {
        return $this->count;
    }

    public function hasMoreRecords(): bool
    {
        return $this->moreRecords;
    }
}
