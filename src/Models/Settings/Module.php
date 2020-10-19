<?php

namespace Webleit\ZohoCrmApi\Models\Settings;

use Illuminate\Support\Collection;
use Webleit\ZohoCrmApi\Models\Model;

class Module extends Model
{
    public function getFields(): Collection
    {
        return $this->getModule()->getFieldsForModule($this);
    }

    public function getRelatedLists(): Collection
    {
        return $this->getModule()->getRelatedListsForModule($this);
    }

    public function getCustomViews(): Collection
    {
        return $this->getModule()->getCustomViewsForModule($this);
    }

    public function getLayouts(): Collection
    {
        return $this->getModule()->getLayoutsForModule($this);
    }

    public function getLayout(string $id): Model
    {
        return $this->getModule()->getLayoutForModule($this, $id);
    }

    public function getCustomView(string $id): Model
    {
        return $this->getModule()->getCustomViewForModule($this, $id);
    }
}
