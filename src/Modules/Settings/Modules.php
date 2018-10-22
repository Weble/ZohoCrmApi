<?php

namespace Webleit\ZohoCrmApi\Modules\Settings;

use Webleit\ZohoCrmApi\Modules\Module;

/**
 * Class Taxes
 * @package Webleit\ZohoBooksApi\Modules
 */
class Modules extends Module
{
    /**
     * @param $module
     * @return \Tightenco\Collect\Support\Collection|static
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     * @throws \Webleit\ZohoCrmApi\Exception\GrantCodeNotSetException
     */
    public function getRelatedListsForModule ($module)
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        $fieldsModule = new RelatedLists($this->getClient());
        return $fieldsModule->getList([
            'module' => $module
        ]);
    }

    /**
     * @param $module
     * @return \Tightenco\Collect\Support\Collection|static
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     * @throws \Webleit\ZohoCrmApi\Exception\GrantCodeNotSetException
     */
    public function getCustomViewsForModule ($module)
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        $fieldsModule = new CustomViews($this->getClient());
        return $fieldsModule->getList([
            'module' => $module
        ]);
    }

    /**
     * @param $module
     * @return \Tightenco\Collect\Support\Collection|static
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     * @throws \Webleit\ZohoCrmApi\Exception\GrantCodeNotSetException
     */
    public function getFieldsForModule ($module)
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        $fieldsModule = new Fields($this->getClient());
        return $fieldsModule->getList([
            'module' => $module
        ]);
    }

    /**
     * @param $module
     * @return \Tightenco\Collect\Support\Collection|static
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     * @throws \Webleit\ZohoCrmApi\Exception\GrantCodeNotSetException
     */
    public function getLayoutsForModule ($module)
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        $fieldsModule = new Layouts($this->getClient());
        return $fieldsModule->getList([
            'module' => $module
        ]);
    }

    /**
     * @param $module
     * @param $id
     * @return \Webleit\ZohoCrmApi\Models\Model
     */
    public function getLayoutForModule ($module, $id)
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        $fieldsModule = new Layouts($this->getClient());
        return $fieldsModule->get($id, [
            'module' => $module
        ]);
    }

    /**
     * @param $module
     * @param $id
     * @return \Webleit\ZohoCrmApi\Models\Model
     */
    public function getCustomViewForModule ($module, $id)
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        $fieldsModule = new CustomViews($this->getClient());
        return $fieldsModule->get($id, [
            'module' => $module
        ]);
    }

    /**
     * Get a single record for this module
     * @param string $id
     * @return \Webleit\ZohoCrmApi\Models\Settings\Module
     */
    public function get ($module, array $params = [])
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        return parent::get($module, $params);
    }

    /**
     * @return string
     */
    public function getUrlPath ()
    {
        return 'settings/modules';
    }

    /**
     * @return string
     */
    public function getModelClassName ()
    {
        return \Webleit\ZohoCrmApi\Models\Settings\Module::class;
    }
}