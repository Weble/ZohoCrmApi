<?php

namespace Webleit\ZohoCrmApi\Mixins;

use Illuminate\Support\Collection;
use Webleit\ZohoCrmApi\Contracts\Module;

trait ProvidesModules
{
    public function createModule(string $name): ?Module
    {
        if ($this->getAvailableModules()->has($name)) {
            $class = $this->getAvailableModules()->get($name);

            /** @var Module $module */
            $module = new $class($this->client);
            return $module;
        }

        return null;
    }

    public function getAvailableModules(): Collection
    {
        return collect($this->availableModules);
    }
}
