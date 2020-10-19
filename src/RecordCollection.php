<?php


namespace Webleit\ZohoCrmApi;


use Illuminate\Support\Collection;
use Webleit\ZohoCrmApi\Request\Pagination;

class RecordCollection extends Collection
{
    /**
     * @var null|Pagination
     */
    protected $pagination;

    public function withPagination(Pagination $pagination): self
    {
        $this->pagination = $pagination;
        return $this;
    }

    public function pagination(): Pagination
    {
        return $this->pagination;
    }
}
