<?php

namespace Webleit\ZohoCrmApi\Modules\Settings;

use Webleit\ZohoCrmApi\Exception\NonExistingModule;
use Webleit\ZohoCrmApi\Models\Model;
use Webleit\ZohoCrmApi\Models\Record;
use Webleit\ZohoCrmApi\Modules\Module;
use Webleit\ZohoCrmApi\RecordCollection;

/**
 * Class Taxes
 * @package Webleit\ZohoBooksApi\Modules
 */
class Modules extends Module
{
    public function getRelatedListsForModule(\Webleit\ZohoCrmApi\Models\Settings\Module|string $module): RecordCollection
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->api_name;
        }

        $fieldsModule = new RelatedLists($this->getClient());

        return $fieldsModule->getList([
            'module' => $module,
        ]);
    }

    public function getCustomViewsForModule(\Webleit\ZohoCrmApi\Models\Settings\Module|string $module): RecordCollection
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->api_name;
        }

        $fieldsModule = new CustomViews($this->getClient());

        return $fieldsModule->getList([
            'module' => $module,
        ]);
    }

    public function getFieldsForModule(\Webleit\ZohoCrmApi\Models\Settings\Module|string $module): RecordCollection
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->api_name;
        }

        $fieldsModule = new Fields($this->getClient());

        try {
            return $fieldsModule->getList([
                'module' => $module,
            ]);
        } catch (NonExistingModule $e) {
            return new RecordCollection([]);
        }
    }

    public function getLayoutsForModule(\Webleit\ZohoCrmApi\Models\Settings\Module|string $module): RecordCollection
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->api_name;
        }

        $fieldsModule = new Layouts($this->getClient());

        return $fieldsModule->getList([
            'module' => $module,
        ]);
    }

    public function getLayoutForModule(\Webleit\ZohoCrmApi\Models\Settings\Module|string $module, string $id): Model
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->api_name;
        }

        $fieldsModule = new Layouts($this->getClient());

        return $fieldsModule->get($id, [
            'module' => $module,
        ]);
    }

    public function getCustomViewForModule(\Webleit\ZohoCrmApi\Models\Settings\Module|string $module, string $id): Model
    {
        if ($module instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $module = $module->api_name;
        }

        $fieldsModule = new CustomViews($this->getClient());

        return $fieldsModule->get($id, [
            'module' => $module,
        ]);
    }

    /**
     * @param \Webleit\ZohoCrmApi\Models\Settings\Module|string $id
     * @param array<string,mixed> $params
     * @return Model
     */
    public function get(\Webleit\ZohoCrmApi\Models\Settings\Module|string $id, array $params = []): Model
    {
        if ($id instanceof \Webleit\ZohoCrmApi\Models\Settings\Module) {
            $id = $id->api_name;
        }

        return parent::get($id, $params);
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
