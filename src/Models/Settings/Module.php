<?php

namespace Webleit\ZohoCrmApi\Models\Settings;

use Webleit\ZohoCrmApi\Models\Model;

/**
 * Class Module
 * @package Webleit\ZohoCrmApi\Models
 */
class Module extends Model
{
    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->getModule()->getFieldsForModule($this);
    }

    /**
     * @return mixed
     */
    public function getRelatedLists()
    {
        return $this->getModule()->getRelatedListsForModule($this);
    }

    /**
     * @return mixed
     */
    public function getCustomViews()
    {
        return $this->getModule()->getCustomViewsForModule($this);
    }

    /**
     * @return mixed
     */
    public function getLayouts()
    {
        return $this->getModule()->getLayoutsForModule($this);
    }

    /**
     * @return mixed
     */
    public function getLayout($id)
    {
        return $this->getModule()->getLayoutForModule($this, $id);
    }

    /**
     * @return mixed
     */
    public function getCustomView($id)
    {
        return $this->getModule()->getCustomViewForModule($this, $id);
    }
}
