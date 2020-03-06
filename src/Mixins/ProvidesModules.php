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

    public function getAvailableModules(): Collection
    {
        return ($this->availableModules instanceof Collection) ? $this->availableModules : collect($this->availableModules);
    }
}
