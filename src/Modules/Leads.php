<?php

namespace Webleit\ZohoCrmApi\Modules;

use Webleit\ZohoCrmApi\Client;

class Leads extends Records
{
    public function __construct(Client $client, string $module = 'Leads')
    {
        parent::__construct($client, $module);
    }

    /**
     * @see https://www.zoho.com/crm/developer/docs/api/convert-lead.html
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function convertLead(string $leadId, array $data = []): array
    {
        return $this->doAction($leadId, 'convert', [
                'data' => [
                    [
                        'overwrite' => false,
                    ],
                ],
            ])['data'][0] ?? [];
    }

    /**
     * @see https://www.zoho.com/crm/developer/docs/api/convert-lead.html
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function convert(string $leadId, array $data = []): array
    {
        return $this->convertLead($leadId, $data);
    }
}
