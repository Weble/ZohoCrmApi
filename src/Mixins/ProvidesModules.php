<?php

namespace Webleit\ZohoCrmApi\Mixins;

use Tightenco\Collect\Support\Collection;

trait ProvidesModules
{
    /**
     * Proxy any module call to the right api call
     * @param $name
     * @return mixed
     */
    public function createModule($name)
    {
        if ($this->getAvailableModules()->has($name)) {
            $class = $this->getAvailableModules()->get($name);

            return new $class($this->client);
        }

        return null;
    }

    /**
     * @return Collection
     */
    public function getAvailableModules()
    {
        return collect($this->availableModules);
    }
}
