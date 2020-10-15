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

            return new $class($this->client);
        }

        return null;
    }

    public function getAvailableModules(): Collection
    {
        return collect($this->availableModules);
    }
}
