<?php

namespace Webleit\ZohoCrmApi\Modules;

use Webleit\ZohoCrmApi\Client;
use Webleit\ZohoCrmApi\Models\Model;
use Webleit\ZohoCrmApi\Models\Record;
use Webleit\ZohoCrmApi\RecordCollection;

class Records extends Module
{
    /**
     * @var string
     */
    protected $module;

    /**
     * Users constructor.
     * @param Client $client
     * @param string $module
     */
    public function __construct(Client $client, \Webleit\ZohoCrmApi\Models\Settings\Module|string $module = '')
    {
        parent::__construct($client);

        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        $this->module = $module;
    }

    /**
     * @param array<string,mixed> $params
     */
    public function get(string $id, array $params = [], ?string $externalField = null): Model
    {
        $options = [];
        if ($externalField !== null) {
            $options['headers'] = [
                "X-EXTERNAL" => $this->getModuleName() . "." . $externalField
            ];
        }

        $item = $this->client->get($this->getUrl(), $id, $params, $options);
        if (!is_array($item)) {
            return $this->make();
        }

        /** @var array<int,array<string,mixed>> $items */
        $items = $item[$this->getResourceKey()] ?? [];

        /** @var array<string,mixed> $data */
        $data = array_shift($items);

        return $this->make($data ?: []);
    }

    public function searchRaw(string $criteria): RecordCollection
    {
        return $this->search($criteria, 'criteria');
    }

    /**
     * @param array<string,mixed> $params
     */
    public function search(string $criteria, string $key = 'criteria', array $params = []): RecordCollection
    {
        $params = array_merge($params, [$key => $criteria]);
        $list = $this->client->getList($this->getUrl() . '/search', $params);
        if (!is_array($list)) {
            return new RecordCollection([]);
        }

        /** @var array<int,array<string|int,mixed>> $items */
        $items = $list[$this->getResourceKey()] ?? [];

        /** @var RecordCollection<string,Record> $collection */
        $collection = (new RecordCollection($items))
            ->mapWithKeys(function ($data) {
                /** @var array<int|string,mixed> $data */

                $item = $this->make($data);

                return [$item->getId() => $item];
            });

        return $collection;
    }

    /**
     * @param array<string,mixed> $params
     */
    public function searchEmail(string $criteria, array $params = []): RecordCollection
    {
        return $this->search($criteria, 'email', $params);
    }

    /**
     * @param array<string,mixed> $params
     */
    public function searchPhone(string $criteria, array $params = []): RecordCollection
    {
        return $this->search($criteria, 'phone', $params);
    }

    /**
     * @param array<string,mixed> $params
     */
    public function searchWord(string $criteria, array $params = []): RecordCollection
    {
        return $this->search($criteria, 'word', $params);
    }

    public function uploadPhoto(string $recordId, string $fileName, string $fileContents): bool
    {
        $result = $this->client->processResult(
            $this->client->call($this->getUrl() . '/' . $recordId . '/photo', 'post', [
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => $fileContents,
                        'filename' => $fileName,
                    ],
                ],
            ])
        );

        return (($result['code'] ?? '') === Client::SUCCESS_CODE);
    }

    public function uploadAttachment(string $recordId, string $fileName, string $fileContents): bool
    {
        $result = $this->client->processResult(
            $this->client->call($this->getUrl() . '/' . $recordId . '/Attachments', 'post', [
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => $fileContents,
                        'filename' => $fileName,
                    ],
                ],
            ])
        );

        return (($result['code'] ?? '') === Client::SUCCESS_CODE);
    }

    public function downloadAttachment(string $recordId, string $attachmentId, string $resource): void
    {
        $this->client->call($this->getUrl() . '/' . $recordId . '/Attachments/' . $attachmentId, 'get', [
            'sink' => $resource
        ]);
    }

    public function getModuleName(): string
    {
        return $this->module;
    }

    public function getUrlPath(): string
    {
        return $this->module;
    }

    public function getModelClassName(): string
    {
        return Record::class;
    }

    protected function getResourceKey(): string
    {
        return 'data';
    }
}
