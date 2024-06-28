<?php

namespace Webleit\ZohoCrmApi\Modules;

use Exception;
use Illuminate\Support\Collection;
use Webleit\ZohoCrmApi\Client;
use Webleit\ZohoCrmApi\Enums\Trigger;
use Webleit\ZohoCrmApi\Exception\InvalidResourceKey;
use Webleit\ZohoCrmApi\Mixins\HasInflector;
use Webleit\ZohoCrmApi\Models\Model;
use Webleit\ZohoCrmApi\RecordCollection;
use Webleit\ZohoCrmApi\Request\Pagination;

abstract class Module implements \Webleit\ZohoCrmApi\Contracts\Module
{
    use HasInflector;

    /**
     * @var Client
     */
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     */
    public function getList(array $params = [], array $headers = []): RecordCollection
    {
        /** @var array<int|string,mixed> $list */
        $list = $this->client->getList($this->getUrl(), $params, $headers);

        $data = $list[$this->getResourceKey()] ?? null;
        if ($data === null) {
            throw new InvalidResourceKey(json_encode($list) ?: '');
        }

        /** @var array<int|string,mixed> $data */

        /** @var RecordCollection $collection */
        $collection = (new RecordCollection($data))
            ->mapWithKeys(function ($data) {
                /** @var array<int|string,mixed> $data */
                $item = $this->make($data);

                return [$item->getId() => $item];
            });

        /** @var array{"per_page"?: int, "page"?: int, "count"?: int, "more_records"?: bool} $info */
        $info = $list['info'] ?? [];
        return $collection->withPagination(new Pagination($info));

    }

    /**
     * @param array<string,mixed> $params
     */
    public function get(string $id, array $params = []): Model
    {
        $item = $this->client->get($this->getUrl(), $id, $params);
        if (!is_array($item)) {
            return $this->make();
        }

        /** @var array<int|string,array<int|string,mixed>> $items */
        $items = $item[$this->getResourceKey()] ?? [];

        /** @var array<int|string,mixed> $data */
        $data = array_shift($items);

        return $this->make($data ?: []);
    }

    /**
     * @param mixed[] $data
     * @param array<string,mixed> $params
     * @param string[] $triggers
     */
    public function create(array $data, array $params = [], array $triggers = [
        Trigger::APPROVAL,
        Trigger::WORKFLOW,
        Trigger::BLUEPRINT,
    ]): ?Model
    {
        return $this->createMany([$data], $params, $triggers)->first();
    }

    /**
     * @param array<string|int,mixed> $data
     * @param array<string,mixed> $params
     * @param string[] $triggers
     */
    public function createMany(mixed $data, array $params = [], array $triggers = [
        Trigger::APPROVAL,
        Trigger::WORKFLOW,
        Trigger::BLUEPRINT,
    ]): Collection
    {
        $data = [
            'data'    => (array)$data,
            'trigger' => $triggers,
        ];

        $data = $this->client->post($this->getUrl(), $data, $params);
        $data = $data['data'] ?? [];

        $results = [];
        foreach ($data as $row) {

            if (($row['code'] ?? '') !== Client::SUCCESS_CODE) {
                throw new \Exception(json_encode($row), 500);

            }

            $results[] = $this->make($row['details'] ?? []);
        }

        return collect($results);
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,mixed> $params
     * @param string[] $triggers
     */
    public function update(string $id, array $data, array $params = [], array $triggers = [
        Trigger::APPROVAL,
        Trigger::WORKFLOW,
        Trigger::BLUEPRINT,
    ]): Model
    {
        $data['id'] = $id;

        $data = [
            'data'    => [$data],
            'trigger' => $triggers,
        ];

        $data = $this->client->put($this->getUrl(), $data, $params);
        $data = $data['data'] ?? [];
        $row = array_shift($data);

        return $this->make($row['details']);
    }

    /**
     * @param array<string|int,mixed> $data
     * @param array<string,mixed> $params
     * @param string[] $triggers
     */
    public function updateMany(array $data, array $params = [], array $triggers = [
        Trigger::APPROVAL,
        Trigger::WORKFLOW,
        Trigger::BLUEPRINT,
    ]): Collection
    {
        $data = [
            'data'    => $data,
            'trigger' => $triggers,
        ];

        $data = $this->client->put($this->getUrl(), $data, $params);
        $items = [];
        foreach ($data['data'] ?? [] as $row) {
            $item = $row;
            if ($row['code'] === Client::SUCCESS_CODE) {
                $item = $this->make($row['details']);
            }
            $items[] = $item;
        }

        return collect($items);
    }

    public function delete(string $id): bool
    {
        $this->client->delete($this->getUrl(), $id);

        return true;
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string|int,mixed>
     */
    public function updateRelatedRecord(string $recordId, string $relationName, string $relatedRecordId, array $data = []): array
    {
        $data = array_merge($data, [
            'id' => $relatedRecordId,
        ]);

        $putData = [
            'data' => [
                $data,
            ],
        ];

        $result = $this->client->put($this->getUrl() . '/' . $recordId . '/' . $relationName . '/' . $relatedRecordId, $putData);
        if (!is_array($result)) {
            throw new Exception($result);
        }

        return $result;
    }

    /**
     * @return array<string|int,mixed>
     */
    public function getRelatedRecords(string $recordId, string $relationName): array
    {
        $result = $this->client->get($this->getUrl() . '/' . $recordId . '/' . $relationName) ?: [];

        if (!is_array($result)) {
            throw new Exception($result);
        }

        return $result;

    }

    public function getUrlPath(): string
    {
        // Module specific url path?
        if (isset($this->urlPath) && $this->urlPath) {
            return $this->urlPath;
        }

        // Class name
        return $this->getName();
    }

    public function getName(): string
    {
        return $this->inflector()->pluralize(strtolower((new \ReflectionClass($this))->getShortName()));
    }

    public function getUrl(): string
    {
        return $this->getUrlPath();
    }

    protected function getResourceKey(): string
    {
        return strtolower($this->getName());
    }

    /**
     * @param array<string|int,mixed> $data
     */
    public function make(array $data = []): Model
    {
        $class = $this->getModelClassName();

        /** @var Model $model */
        $model = new $class($data, $this);

        return $model;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function markAs(string $id, string $status, string $key = 'status'): bool
    {
        $this->client->post($this->getUrl() . '/' . $id . '/' . $key . '/' . $status);

        return true;
    }

    /**
     * @param array<string,mixed> $params
     */
    public function notes(string $id, array $params = []): RecordCollection
    {
        return $this->getRelatedResources('Notes', $id, $params);
    }

    /**
     * @param array<string,mixed> $params
     */
    public function attachments(string $id, array $params = []): RecordCollection
    {
        return $this->getRelatedResources('Attachments', $id, $params);
    }

    /**
     * @param array<string,mixed> $params
     */
    public function getRelatedResources(string $resource, string $id, array $params = []): RecordCollection
    {
        $data = $this->client->getList($this->getUrl() . '/' . $id . '/' . $resource, $params);
        if (!is_array($data)) {
            throw new \Exception($data);
        }

        /** @var RecordCollection $collection */
        $collection = (new RecordCollection($data['data'] ?? []))
            ->mapWithKeys(function ($item) {
                $item = $this->make($item);

                return [$item->getId() => $item];
            });

        return $collection->withPagination(new Pagination($data['info'] ?? []));
    }

    /**
     * @param array<string|int,mixed> $data
     * @param array<string|int,mixed> $params
     * @return array<int|string,mixed>
     */
    public function doAction(string $id, string $action, array $data = [], array $params = []): array
    {
        $data = $this->client->post($this->getUrl() . '/' . $id . '/actions/' . $action, $data, $params);

        if (!is_array($data)) {
            throw new \Exception($data);
        }

        return $data;
    }

    protected function getPropertyList(string $property, ?string $id = null, ?string $class = null, ?string $subProperty = null, ?\Webleit\ZohoCrmApi\Contracts\Module $module = null): Collection
    {
        if (!$class) {
            $class = $this->getModelClassName() . '\\' . ucfirst(strtolower($this->inflector()->singularize($property)));
        }

        if (!$module) {
            $module = $this;
        }

        if (!$subProperty) {
            $subProperty = $property;
        }

        $url = $this->getUrl();
        if ($id !== null) {
            $url .= '/' . $id;
        }
        $url .= '/' . $property;

        $list = $this->client->getList($url);

        return (new Collection($list[$subProperty]))
             ->mapWithKeys(function ($item) use ($class, $module) {
            /** @var Model $item */
            $item = new $class($item, $module);

            return [$item->getId() => $item];
        });
    }

    public function getModelClassName(): string
    {
        $className = (new \ReflectionClass($this))->getShortName();
        $class = '\\Webleit\\ZohoCrmApi\\Models\\' . ucfirst($this->inflector()->singularize($className));

        return $class;
    }
}
