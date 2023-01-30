<?php


namespace Webleit\ZohoCrmApi\Request;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class ListParameters implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * @var array<string,mixed>
     */
    protected array $params = [
        'fields' => null,
        'ids' => [],
        'sort_order' => null,
        'sort_by' => null,
        'converted' => null,
        'approved' => null,
        'page' => 1,
        'per_page' => 200,
        'cvid' => '',
        'terrory_id' => '',
        'include_child' => null,
    ];

    /**
     * @param array<string,mixed> $params
     */
    public function __construct(array $params = [])
    {
        foreach ($params as $key => $value) {
            $this->params[$key] = $value;
        }
    }

    /**
     * @param string[] $fields
     */
    public function fields(array $fields): self
    {
        $this->params['fields'] = implode(",", $fields);

        return $this;
    }

    /**
     * @param string[] $ids
     * @return $this
     */
    public function ids(array $ids): self
    {
        $this->params['ids'] = implode(",", $ids);

        return $this;
    }

    public function sortBy(string $sort): self
    {
        $this->params['sort_by'] = $sort;

        return $this;
    }

    public function sortAsc(): self
    {
        $this->params['sort_order'] = 'asc';

        return $this;
    }

    public function sortDesc(): self
    {
        $this->params['sort_order'] = 'desc';

        return $this;
    }

    public function withConverted(): self
    {
        $this->params['converted'] = 'true';

        return $this;
    }

    public function withoutConverted(): self
    {
        $this->params['converted'] = 'false';

        return $this;
    }

    public function withBothConvertedAndNotConverted(): self
    {
        $this->params['converted'] = 'both';

        return $this;
    }

    public function withApproved(): self
    {
        $this->params['approved'] = 'true';

        return $this;
    }

    public function withoutApproved(): self
    {
        $this->params['approved'] = 'false';

        return $this;
    }

    public function withBothApprovedAndNotApproved(): self
    {
        $this->params['approved'] = 'both';

        return $this;
    }

    public function page(int $page): self
    {
        $this->params['page'] = max(1, $page);

        return $this;
    }

    public function perPage(int $perPage): self
    {
        $this->params['per_page'] = min($perPage, 200);

        return $this;
    }

    public function usingCustomView(string $id): self
    {
        $this->params['cvid'] = $id;

        return $this;
    }

    public function forTerrory(string $id): self
    {
        $this->params['terrory_id'] = $id;

        return $this;
    }

    public function includingChildTerritories(): self
    {
        $this->params['include_child'] = true;

        return $this;
    }

    public function withoutChildTerritories(): self
    {
        $this->params['include_child'] = false;

        return $this;
    }

    public function toArray(): array
    {
        return array_filter($this->params);
    }

    public function toJson($options = 0): string|false
    {
        return json_encode($options);
    }

    /**
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return array<string,mixed>
     */
    public function __toArray(): array
    {
        return $this->toArray();
    }
}
