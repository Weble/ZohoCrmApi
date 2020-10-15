<?php

namespace Webleit\ZohoCrmApi\Models;

/**
 * Class Module
 * @package Webleit\ZohoCrmApi\Models
 */
class Record extends Model
{
    public function uploadPhoto(string $fileName, $fileContents): bool
    {
        return $this->getModule()->uploadPhoto($this->getId(), $fileName, $fileContents);
    }
}
