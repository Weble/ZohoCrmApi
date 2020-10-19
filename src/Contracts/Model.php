<?php

namespace Webleit\ZohoCrmApi\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

interface Model extends Arrayable, Jsonable, JsonSerializable
{
    public function getModule(): Module;

    public function getData(): array;

    public function isNew(): bool;

    public function getId(): ?string;

    public function getKeyName(): string;

    public function getName(): string;
}
