<?php

namespace Webleit\ZohoCrmApi\Modules;

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

    public function get($id = null, $params = [])
    {
        $item = $this->client->get($this->getUrl());

        if (! is_array($item)) {
            return $item;
        }

        $data = array_shift($item[$this->getResourceKey()]);

        return $this->make($data);
    }
}
