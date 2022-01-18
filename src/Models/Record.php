<?php

namespace Webleit\ZohoCrmApi\Models;

class Record extends Model
{
    public function uploadPhoto(string $fileName, $fileContents): bool
    {
        return $this->getModule()->uploadPhoto($this->getId(), $fileName, $fileContents);
    }

    public function uploadAttachment(string $fileName, $fileContents): bool
    {
        return $this->getModule()->uploadAttachment($this->getId(), $fileName, $fileContents);
    }

    public function downloadAttachment(string $attachmentId, $resource): void
    {
        $this->getModule()->downloadAttachment($this->getId(), $attachmentId, $resource);
    }
}
