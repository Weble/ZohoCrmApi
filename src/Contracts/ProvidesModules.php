<?php

namespace Webleit\ZohoCrmApi\Contracts;

use Illuminate\Support\Collection;

interface ProvidesModules
{
    public function createModule(string $name): ?Module;

    public function getAvailableModules(): Collection;
}
