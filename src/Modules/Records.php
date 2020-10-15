<?php

namespace Webleit\ZohoCrmApi\Modules;

use Tightenco\Collect\Support\Collection;
use Webleit\ZohoCrmApi\Client;
use Webleit\ZohoCrmApi\Exception\GrantCodeNotSetException;

/**
 * Class Users
 * @package Webleit\ZohoCrmApi\Modules
 */
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

    /**
     * @param array $params
     * @return Collection|static
     * @throws GrantCodeNotSetException
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     */
    public function searchRaw(string $criteria)
    {
        return $this->search($criteria, 'criteria');
    }

    /**
     * @param array $params
     * @return Collection|static
     * @throws GrantCodeNotSetException
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     */
    public function search(string $criteria, string $key = 'criteria')
    {
        $list = $this->client->getList($this->getUrl() . '/search', [$key => $criteria]);

        $collection = new Collection($list[$this->getResourceKey()] ?? []);
        $collection = $collection->mapWithKeys(function ($item) {
            $item = $this->make($item);

            return [$item->getId() => $item];
        });

        return $collection;
    }

    /**
     * @param array $params
     * @return Collection|static
     * @throws GrantCodeNotSetException
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     */
    public function searchEmail(string $criteria)
    {
        return $this->search($criteria, 'email');
    }

    /**
     * @param string|int $leadId
     * @param string $fileName
     * @param string $fileContents
     * @return bool
     */
    public function uploadPhoto($leadId, string $fileName, $fileContents): bool
    {
        try {
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
        } catch(\Exception $e) {
            dd($e);
        }


        return (($result['code'] ?? '') === 'SUCCESS');
    }

    /**
     * @param array $params
     * @return Collection|static
     * @throws GrantCodeNotSetException
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     */
    public function searchPhone(string $criteria)
    {
        return $this->search($criteria, 'phone');
    }

    /**
     * @param array $params
     * @return Collection|static
     * @throws GrantCodeNotSetException
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     */
    public function searchWord(string $criteria)
    {
        return $this->search($criteria, 'word');
    }

    /**
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->module;
    }

    /**
     * @return string
     */
    public function getUrlPath()
    {
        return $this->module;
    }

    /**
     * @return string
     */
    public function getModelClassName()
    {
        return \Webleit\ZohoCrmApi\Models\Record::class;
    }

    /**
     * @return mixed|string
     */
    protected function getResourceKey()
    {
        return 'data';
    }
}
