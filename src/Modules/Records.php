<?php

namespace Webleit\ZohoCrmApi\Modules;

use Webleit\ZohoCrmApi\Client;
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

    public function searchRaw(string $criteria): RecordCollection
    {
        return $this->search($criteria, 'criteria');
    }

    public function search(string $criteria, string $key = 'criteria'): RecordCollection
    {
        $list = $this->client->getList($this->getUrl() . '/search', [$key => $criteria]);

        $collection = new RecordCollection($list[$this->getResourceKey()] ?? []);
        $collection = $collection->mapWithKeys(function ($item) {
            $item = $this->make($item);

            return [$item->getId() => $item];
        });

        return $collection;
    }

    public function searchEmail(string $criteria): RecordCollection
    {
        return $this->search($criteria, 'email');
    }

    public function searchPhone(string $criteria): RecordCollection
    {
        return $this->search($criteria, 'phone');
    }

    public function searchWord(string $criteria): RecordCollection
    {
        return $this->search($criteria, 'word');
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
