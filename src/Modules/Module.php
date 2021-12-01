<?php

namespace Webleit\ZohoCrmApi\Modules;

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
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getList(array $params = [], array $headers = []): RecordCollection
    {
        $list = $this->client->getList($this->getUrl(), $params, $headers);

        $data = $list[$this->getResourceKey()] ?? null;
        if ($data === null) {
            throw new InvalidResourceKey(json_encode($list));
        }

        $collection = new RecordCollection($data ?? []);
        $collection = $collection->mapWithKeys(function ($item) {
            $item = $this->make($item);

            return [$item->getId() => $item];
        });

        $collection->withPagination(new Pagination($list['info'] ?? []));

        return $collection;
    }

    public function get(string $id, array $params = []): Model
    {
        $item = $this->client->get($this->getUrl(), $id, $params);

        $items = $item[$this->getResourceKey()] ?? [];

        $data = array_shift($items);

        return $this->make($data ?: []);
    }

    public function create(array $data, array $params = [], array $triggers = [
        Trigger::APPROVAL,
        Trigger::WORKFLOW,
        Trigger::BLUEPRINT,
    ]): ?Model
    {
        return $this->createMany([$data], $params, $triggers)->first();
    }

    public function createMany($data, $params = [], $triggers = [
        Trigger::APPROVAL,
        Trigger::WORKFLOW,
        Trigger::BLUEPRINT,
    ]): Collection
    {
        $data = [
            'data' => (array)$data,
            'trigger' => $triggers,
        ];

        $data = $this->client->post($this->getUrl(), $data, $params);
        $data = $data['data'] ?? [];

        $results = [];
        foreach ($data as $row) {
            $item = $row;

            if (($row['code'] ?? '') === Client::SUCCESS_CODE) {
                $item = $this->make($row['details'] ?? []);
            }

            $results[] = $item;
        }

        return collect($results);
    }

    public function update(string $id, array $data, array $params = [], array $triggers = [
        Trigger::APPROVAL,
        Trigger::WORKFLOW,
        Trigger::BLUEPRINT,
    ]): Model
    {
        $data['id'] = $id;

        $data = [
            'data' => [$data],
            'trigger' => $triggers,
        ];

        $data = $this->client->put($this->getUrl(), $data, $params);
        $row = array_shift($data['data']);

        return $this->make($row['details']);
    }

    public function updateMany(array $data, array $params = [], array $triggers = [
        Trigger::APPROVAL,
        Trigger::WORKFLOW,
        Trigger::BLUEPRINT,
    ]): Collection
    {
        $data = [
            'data' => $data,
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

        return $this->client->put($this->getUrl() . '/' . $recordId . '/' . $relationName . '/' . $relatedRecordId, $putData);
    }

    public function getRelatedRecords(string $recordId, string $relationName): array
    {
        return $this->client->get($this->getUrl() . '/' . $recordId . '/' . $relationName) ?? [];
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

    public function make(array $data = []): Model
    {
        $class = $this->getModelClassName();

        return new $class($data, $this);
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

    public function notes(string $id, array $params = []): RecordCollection
    {
        return $this->getRelatedResources('Notes', $id, $params);
    }

    public function attachments(string $id, array $params = []): RecordCollection
    {
        return $this->getRelatedResources('Attachments', $id, $params);
    }

    public function getRelatedResources(string $resource, string $id, array $params = []): RecordCollection
    {
        $data = $this->client->getList($this->getUrl() . '/' . $id . '/' . $resource, $params);

        $collection = new RecordCollection($data['data'] ?? []);
        $collection = $collection->mapWithKeys(function ($item) {
            $item = $this->make($item);

            return [$item->getId() => $item];
        });

        return $collection->withPagination(new Pagination($list['info'] ?? []));
    }

    public function doAction(string $id, string $action, array $data = [], array $params = []): array
    {
        return $this->client->post($this->getUrl() . '/' . $id . '/actions/' . $action, $data, $params);
    }

    protected function getPropertyList(string $property, ?string $id = null, ?string $class = null, ?string $subProperty = null, ?\Webleit\ZohoCrmApi\Contracts\Module $module = null)
    {
        if (! $class) {
            $class = $this->getModelClassName() . '\\' . ucfirst(strtolower($this->inflector()->singularize($property)));
        }

        if (! $module) {
            $module = $this;
        }

        if (! $subProperty) {
            $subProperty = $property;
        }

        $url = $this->getUrl();
        if ($id !== null) {
            $url .= '/' . $id;
        }
        $url .= '/' . $property;

        $list = $this->client->getList($url);

        $collection = new Collection($list[$subProperty]);
        $collection = $collection->mapWithKeys(function ($item) use ($class, $module) {
            /** @var Model $item */
            $item = new $class($item, $module);

            return [$item->getId() => $item];
        });

        return $collection;
    }

    public function getModelClassName(): string
    {
        $className = (new \ReflectionClass($this))->getShortName();
        $class = '\\Webleit\\ZohoCrmApi\\Models\\' . ucfirst($this->inflector()->singularize($className));

        return $class;
    }
}
