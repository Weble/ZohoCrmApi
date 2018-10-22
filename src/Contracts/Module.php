<?php

namespace Webleit\ZohoCrmApi\Contracts;

use Webleit\ZohoCrmApi\Client;

interface Module
{
    /**
     * @return Client
     */
    public function getClient();
}