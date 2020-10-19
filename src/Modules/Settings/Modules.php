<?php

namespace Webleit\ZohoCrmApi\Modules\Settings;

use Illuminate\Support\Collection;
use Webleit\ZohoCrmApi\Exception\NonExistingModule;
use Webleit\ZohoCrmApi\Models\Model;
use Webleit\ZohoCrmApi\Modules\Module;

/**
 * Class Taxes
 * @package Webleit\ZohoBooksApi\Modules
 */
class Modules extends Module
{
    /**
     * @param $module
     * @return \Illuminate\Support\Collection|static
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     * @throws \Webleit\ZohoCrmApi\Exception\GrantCodeNotSetException
     */
    public function getRelatedListsForModule($module)
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        $fieldsModule = new RelatedLists($this->getClient());

        return $fieldsModule->getList([
            'module' => $module,
        ]);
    }

    /**
     * @param $module
     * @return \Illuminate\Support\Collection|static
     * @throws \Webleit\ZohoCrmApi\Exception\ApiError
     * @throws \Webleit\ZohoCrmApi\Exception\GrantCodeNotSetException
     */
    public function getCustomViewsForModule($module)
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        $fieldsModule = new CustomViews($this->getClient());

        return $fieldsModule->getList([
            'module' => $module,
        ]);
    }

    /**
     * @param string|Module $module
     * @return Collection
     */
    public function getFieldsForModule($module): Collection
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        $fieldsModule = new Fields($this->getClient());

        try {
            return $fieldsModule->getList([
                'module' => $module,
            ]);
        } catch (NonExistingModule $e) {
            return collect([]);
        }
    }

    /**
     * @param string|Module $module
     * @return Collection
     */
    public function getLayoutsForModule($module): Collection
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        $fieldsModule = new Layouts($this->getClient());

        return $fieldsModule->getList([
            'module' => $module,
        ]);
    }

    /**
     * @param string|Module $module
     * @param string $id
     * @return mixed|string|\Webleit\ZohoCrmApi\Models\Model
     */
    public function getLayoutForModule($module, string $id)
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        $fieldsModule = new Layouts($this->getClient());

        return $fieldsModule->get($id, [
            'module' => $module,
        ]);
    }

    /**
     * @param string|Module $module
     * @param string $id
     * @return Model
     */
    public function getCustomViewForModule($module, string $id): Model
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        $fieldsModule = new CustomViews($this->getClient());

        return $fieldsModule->get($id, [
            'module' => $module,
        ]);
    }

    /**
     * @param string|Module $module
     * @param array $params
     * @return Model
     */
    public function get($module, array $params = []): Model
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->module_name;
        }

        return parent::get($module, $params);
    }

    public function getUrlPath(): string
    {
        return 'settings/modules';
    }

    public function getModelClassName(): string
    {
        return \Webleit\ZohoCrmApi\Models\Settings\Module::class;
    }
}
