<?php

namespace Webleit\ZohoCrmApi\Modules;

use Webleit\ZohoCrmApi\Modules\Settings as SettingsModules;
use Webleit\ZohoCrmApi\Modules\Module;

/**
 * Class Users
 * @package Webleit\ZohoCrmApi\Modules
 */
class Org extends Module
{
    /**
     * @return string
     */
    public function getUrlPath()
    {
        return 'org';
    }

    /**
     * @return string
     */
    protected function getResourceKey()
    {
        return 'org';
    }

    /**
     * @return string
     */
    public function getModelClassName()
    {
        return \Webleit\ZohoCrmApi\Models\Org::class;
    }

    /**
     * @param null $id
     * @param array $params
     * @return array|mixed|string|\Webleit\ZohoCrmApi\Models\Model
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     * @throws \Webleit\ZohoCrmApi\Exception\GrantCodeNotSetException
     */
    public function get ($id = null, $params = [])
    {
        $item = $this->client->get($this->getUrl());

        if (!is_array($item)) {
            return $item;
        }

        $data = array_shift($item[$this->getResourceKey()]);

        return $this->make($data);
    }


}
