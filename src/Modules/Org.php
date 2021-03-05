<?php

namespace Webleit\ZohoCrmApi\Modules;

use Webleit\ZohoCrmApi\Models\Model;

class Org extends Module
{
    public function getUrlPath(): string
    {
        return 'org';
    }

    protected function getResourceKey(): string
    {
        return 'org';
    }

    public function getModelClassName(): string
    {
        return \Webleit\ZohoCrmApi\Models\Org::class;
    }

    public function get(string $id, array $params = []): Model
    {
        $item = $this->client->get($this->getUrl());

        if (! is_array($item)) {
            return $this->make([]);
        }

        $data = array_shift($item[$this->getResourceKey()]);

        return $this->make($data);
    }
}
