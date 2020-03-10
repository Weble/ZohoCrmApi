<?php

namespace Webleit\ZohoCrmApi\Modules;

use Webleit\ZohoCrmApi\Client;

class Leads extends Records
{
    public function __construct(Client $client, $module = 'Leads')
    {
        parent::__construct($client, $module);
    }

    /**
     * @see https://www.zoho.com/crm/developer/docs/api/convert-lead.html
     */
    public function convertLead($leadId, $data = [])
    {
        return $this->doAction($leadId, 'convert', [
            'data' => [[
                'overwrite' => false
            ]]
        ])['data'][0] ?? [];
    }
}
