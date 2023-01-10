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
    public function __construct(Client $client, $module = '')
    {
        parent::__construct($client);

        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        $this->module = $module;
    }

    public function get(string $id, array $params = [], ?string $externalField = null): Model
    {
        $options = [];
        if ($externalField !== null) {
            $options['headers'] = [
                "X-EXTERNAL" => $this->getModuleName() . "." . $externalField
            ];
        }

        $item = $this->client->get($this->getUrl(), $id, $params, $options);

        $items = $item[$this->getResourceKey()] ?? [];

        $data = array_shift($items);

        return $this->make($data ?: []);
    }

    public function searchRaw(string $criteria): RecordCollection
    {
        return $this->search($criteria, 'criteria');
    }

    public function search(string $criteria, string $key = 'criteria', array $params = []): RecordCollection
    {
        $params = array_merge($params, [$key => $criteria]);
        $list = $this->client->getList($this->getUrl() . '/search', $params);

        $collection = new RecordCollection($list[$this->getResourceKey()] ?? []);
        $collection = $collection->mapWithKeys(function ($item) {
            $item = $this->make($item);

            return [$item->getId() => $item];
        });

        return $collection;
    }

    public function searchEmail(string $criteria, array $params = []): RecordCollection
    {
        return $this->search($criteria, 'email', $params);
    }

    public function searchPhone(string $criteria, array $params = []): RecordCollection
    {
        return $this->search($criteria, 'phone', $params);
    }

    public function searchWord(string $criteria, array $params = []): RecordCollection
    {
        return $this->search($criteria, 'word', $params);
    }

    public function uploadPhoto(string $recordId, string $fileName, $fileContents): bool
    {
        $result = $this->client->processResult(
            $this->client->call($this->getUrl() . '/' . $recordId . '/photo', 'post', [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => $fileContents,
                        'filename' => $fileName,
                    ],
                ],
            ])
        );

        return (($result['code'] ?? '') === Client::SUCCESS_CODE);
    }

    public function uploadAttachment(string $recordId, string $fileName, $fileContents): bool
    {
        $result = $this->client->processResult(
            $this->client->call($this->getUrl() . '/' . $recordId . '/Attachments', 'post', [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => $fileContents,
                        'filename' => $fileName,
                    ],
                ],
            ])
        );

        return (($result['code'] ?? '') === Client::SUCCESS_CODE);
    }

    public function downloadAttachment(string $recordId, string $attachmentId, $resource): void
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
