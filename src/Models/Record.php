<?php

namespace Webleit\ZohoCrmApi\Models;

use Webleit\ZohoCrmApi\Contracts\Module;
use Webleit\ZohoCrmApi\Modules\Records;

class Record extends Model
{
    public function getModule(): Records
    {
        /** @var Records $module */
        $module = $this->module;
        return $module;
    }

    public function uploadPhoto(string $fileName, string $fileContents): bool
    {
        if (!$this->getId()) {
            return false;
        }

        return $this->getModule()->uploadPhoto($this->getId(), $fileName, $fileContents);
    }

    public function uploadAttachment(string $fileName, string $fileContents): bool
    {
        if (!$this->getId()) {
            return false;
        }

        return $this->getModule()->uploadAttachment($this->getId(), $fileName, $fileContents);
    }

    public function downloadAttachment(string $attachmentId, string $resource): void
    {
        if (!$this->getId()) {
            return;
        }

        $this->getModule()->downloadAttachment($this->getId(), $attachmentId, $resource);
    }
}
