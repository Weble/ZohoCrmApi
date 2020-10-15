<?php

namespace Webleit\ZohoCrmApi\Modules;

use Illuminate\Support\Collection;
use Webleit\ZohoCrmApi\Client;
use Webleit\ZohoCrmApi\Models\Record;

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

    public function searchRaw(string $criteria): Collection
    {
        return $this->search($criteria, 'criteria');
    }

    public function search(string $criteria, string $key = 'criteria'): Collection
    {
        $list = $this->client->getList($this->getUrl() . '/search', [$key => $criteria]);

        $collection = new Collection($list[$this->getResourceKey()] ?? []);
        $collection = $collection->mapWithKeys(function ($item) {
            $item = $this->make($item);

            return [$item->getId() => $item];
        });

        return $collection;
    }

    public function searchEmail(string $criteria): Collection
    {
        return $this->search($criteria, 'email');
    }

    public function searchPhone(string $criteria): Collection
    {
        return $this->search($criteria, 'phone');
    }

    public function searchWord(string $criteria): Collection
    {
        return $this->search($criteria, 'word');
    }

    public function uploadPhoto($leadId, string $fileName, $fileContents): bool
    {
        $result = $this->client->processResult(
            $this->client->call($this->getUrl() . '/' . $leadId . '/photo', 'post', [
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => $fileContents,
                        'filename' => $fileName
                    ],
                ],
            ])
        );

        return (($result['code'] ?? '') === 'SUCCESS');
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
