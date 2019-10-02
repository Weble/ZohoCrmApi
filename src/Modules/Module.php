<?php

namespace Webleit\ZohoCrmApi\Modules;

use Doctrine\Common\Inflector\Inflector;
use Tightenco\Collect\Support\Collection;
use Webleit\ZohoCrmApi\Client;
use Webleit\ZohoCrmApi\Exception\GrantCodeNotSetException;
use Webleit\ZohoCrmApi\Models\Model;

/**
 * Class Module
 * @package Webleit\ZohoSignApi\Modules
 */
abstract class Module implements \Webleit\ZohoCrmApi\Contracts\Module
{
    /**
     * Response types
     */
    const RESPONSE_OPTION_PAGINATION_ONLY = 2;

    const TRIGGER_WORKFLOW = 'workflow';
    const TRIGGER_BLUEPRINT = 'blueprint';
    const TRIGGER_APPROVAL = 'approval';

    /**
     * @var Client
     */
    protected $client;

    /**
     * Module constructor.
     * @param Client $client
     */
    function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $params
     * @return Collection|static
     * @throws GrantCodeNotSetException
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     */
    public function getList($params = [])
    {
        $list = $this->client->getList($this->getUrl(), $params);

        $collection = new Collection($list[$this->getResourceKey()]);
        $collection = $collection->mapWithKeys(function($item) {
            $item = $this->make($item);
            return [$item->getId() => $item];
        });

        return $collection;
    }

    /**
     * @param $id
     * @param array $params
     * @return array|mixed|string|Model
     * @throws GrantCodeNotSetException
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     */
    public function get($id, array $params = [])
    {
        $item = $this->client->get($this->getUrl(), $id, $params);

        if (!is_array($item)) {
            return $item;
        }

        $data = array_shift($item[$this->getResourceKey()]);

        return $this->make($data);
    }

    /**
     * Get the total records for a module
     * @return int
     */
    public function getTotal()
    {
        $list = $this->client->getList($this->getUrl(), null, ['response_option' => self::RESPONSE_OPTION_PAGINATION_ONLY]);
        return $list['page_context']['total'];
    }

    /**
     * @param $data
     * @param array $params
     * @param array $triggers
     * @return Model
     * @throws GrantCodeNotSetException
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     * @throws \Webleit\ZohoCrmApi\Exception\NonExistingModule
     */
    public function create($data, $params = [], $triggers = [self::TRIGGER_APPROVAL, self::TRIGGER_WORKFLOW, self::TRIGGER_BLUEPRINT])
    {
        return $this->createMany([$data], $params, $triggers)->first();
    }

    /**
     * @param $data
     * @param array $params
     * @param array $triggers
     * @return Collection
     * @throws GrantCodeNotSetException
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     * @throws \Webleit\ZohoCrmApi\Exception\NonExistingModule
     */
    public function createMany($data, $params = [], $triggers = [self::TRIGGER_APPROVAL, self::TRIGGER_WORKFLOW, self::TRIGGER_BLUEPRINT])
    {
        $data = [
            'data' => (array) $data,
            'triggers' => $triggers
        ];

        $data = $this->client->post($this->getUrl(), $data, $params);
        $data = $data['data'];

        $results = [];
        foreach ($data as $row) {
            $item = $row;

            if ($row['code'] == 'SUCCESS') {
                $item = $this->make($row['details']);
            }

            $results[] = $item;
        }

        return collect($results);
    }

    /**
     * @param $id
     * @param $data
     * @param array $params
     * @param array $triggers
     *
     * @return Model
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     * @throws \Webleit\ZohoCrmApi\Exception\GrantCodeNotSetException
     * @throws \Webleit\ZohoCrmApi\Exception\NonExistingModule
     */
    public function update($id, $data, $params = [], $triggers = [self::TRIGGER_APPROVAL, self::TRIGGER_WORKFLOW, self::TRIGGER_BLUEPRINT])
    {
        $data['id'] = $id;

        $data = [
            'data' => [$data],
            'triggers' => $triggers
        ];

        $data = $this->client->put($this->getUrl(), $data, $params);
        $row = array_shift($data['data']);


        $item = null;
        if ($row['code'] == 'SUCCESS') {
            $item = $this->make($row['details']);
        }

        return $item;
    }

    /**
     * @param $id
     * @param $data
     * @param array $params
     * @param array $triggers
     *
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     * @throws \Webleit\ZohoCrmApi\Exception\GrantCodeNotSetException
     * @throws \Webleit\ZohoCrmApi\Exception\NonExistingModule
     */
    public function updateMany($data, $params = [], $triggers = [self::TRIGGER_APPROVAL, self::TRIGGER_WORKFLOW, self::TRIGGER_BLUEPRINT])
    {
        $data = [
            'data' => $data,
            'triggers' => $triggers
        ];

        $data = $this->client->put($this->getUrl(), $data, $params);
        $items = [];
        foreach ($data['data'] as $row) {

            $item = $row;
            if ($row['code'] == 'SUCCESS') {
                $item = $this->make($row['details']);
            }
            $items[] = $item;
        }

        return collect($items);
    }

    /**
     * Deletes a record for this module
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $this->client->delete($this->getUrl(), $id);

        // all is ok if we've reached this point
        return true;
    }

    public function updateRelatedRecord($relationName, $relatedRecordId, $data = [])
    {
        $data = [
            'data' => [$data],
        ];

        dd($data);

        return $this->client->post($this->getUrl() . '/' . $relationName . '/' . $relatedRecordId, $data);
    }

    /**
     * Get the url path for the api of this module (ie: /organizations)
     * @return string
     */
    public function getUrlPath()
    {
        // Module specific url path?
        if (isset($this->urlPath) && $this->urlPath) {
            return $this->urlPath;
        }

        // Class name
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return Inflector::pluralize(strtolower((new \ReflectionClass($this))->getShortName()));
    }

    /**
     * Get the full api url to this module
     * @return string
     */
    public function getUrl()
    {
        return $this->getUrlPath();
    }

    /**
     * @return string
     */
    protected function getResourceKey()
    {
        return strtolower($this->getName());
    }

    /**
     * @param  array $data
     * @return Model
     */
    public function make($data = [])
    {
        $class = $this->getModelClassName();

        return new $class($data, $this);
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param $id
     * @param $status
     * @param string $key
     * @return bool
     */
    public function markAs($id, $status, $key = 'status')
    {
        $this->client->post($this->getUrl() . '/' . $id . '/' . $key . '/' . $status);
        // If we arrive here without exceptions, everything went well
        return true;
    }

    /**
     * @param $id
     * @param $action
     * @param array $data
     * @param array $params
     * @return bool
     */
    public function doAction($id, $action, $data = [], $params = [])
    {
        $this->client->post($this->getUrl() . '/' . $id . '/' . $action, null, $data, $params);

        // If we arrive here without exceptions, everything went well
        return true;
    }

    /**
     * @param $property
     * @param null $id
     * @param null $class
     * @param null $subProperty
     * @param null $module
     * @return Collection
     */
    protected function getPropertyList($property, $id = null, $class = null, $subProperty = null, $module = null)
    {
        if (!$class) {
            $class = $this->getModelClassName() . '\\' . ucfirst(strtolower(Inflector::singularize($property)));
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

        $collection = new Collection($list[$subProperty]);
        $collection = $collection->mapWithKeys(function ($item) use ($class, $module) {
            /** @var Model $item */
            $item = new $class($item, $module);
            return [$item->getId() => $item];
        });

        return $collection;
    }

    /**
     * @return string
     */
    public function getModelClassName()
    {
        $className = (new \ReflectionClass($this))->getShortName();
        $class = '\\Webleit\\ZohoSignApi\\Models\\' . ucfirst(Inflector::singularize($className));

        return $class;
    }
}
