<?php


namespace Webleit\ZohoCrmApi\Request;

class Pagination
{
    protected int $perPage = 200;
    protected int $page = 1;
    protected int $count = 0;
    protected bool $moreRecords = false;

    /**
     * @param array{"per_page"?: int, "page"?: int, "count"?: int, "more_records"?: bool} $params
     */
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
