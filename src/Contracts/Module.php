<?php

namespace Webleit\ZohoCrmApi\Contracts;

use Webleit\ZohoCrmApi\Client;

interface Module
{
    public function getClient(): Client;
}
