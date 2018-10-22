<?php

namespace Webleit\ZohoCrmApi\Modules;

use Webleit\ZohoCrmApi\Client;

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
    public function __construct (Client $client, $module = '')
    {
        parent::__construct($client);

        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        $this->module = $module;
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
    protected function getResourceKey ()
    {
        return 'data';
    }
}