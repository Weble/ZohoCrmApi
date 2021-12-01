# Usage

The client exposes a list of properties that match the official zoho api names.

Each of these properties is a ```module``` that lets you interact with the respective zoho module.

For example, ```$client->leads``` is the Lead module in the CRM, and the ```$client->salesorders``` property is the SalesOrders module in CRM.

Each of the ```records``` module has a set of common methods you can use. 

In the docs, we'll always use the ```leads``` module as an example, but **every record module will have the same possibilities**.


## Get a list of records

```php
$zohoCrm = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$leads = $zohoCrm->leads->getList();
```

This will return a ```\Illuminate\Support\Collection``` Collection of ```\Webleit\ZohoCrmApi\Contracts\Model``` models.

These models represent the record in zoho crm and provide you with a way to interact with the record and extract its data in an easy way.

For example, a ```$client->leads->getList()``` call with return a collection of models, that contains the exact data returned by the API:

```php
[
"1300272000000442074" => array:47 [
    "Owner" => array:3 [ …3]
    "Company" => null
    "Email" => null
    "Description" => null
    "$currency_symbol" => "USD"
    "Rating" => null
    "$review_process" => null
    "Website" => null
    "Twitter" => null
    "Salutation" => null
    "Last_Activity_Time" => null
    "First_Name" => "John"
    "Full_Name" => "John Doe"
    "Lead_Status" => null
    "Industry" => null
    "Record_Image" => null
    "Modified_By" => array:3 [ …3]
    "$review" => null
    "$state" => "save"
    "Skype_ID" => null
    "$converted" => false
    "$process_flow" => false
    "Phone" => null
    "Street" => null
    "Zip_Code" => null
    "id" => "1300272000000442074"
    "Email_Opt_Out" => false
    "$approved" => true
    "Designation" => null
    "$approval" => array:4 [ …4]
    "Modified_Time" => "2020-10-16T18:36:22+02:00"
    "Created_Time" => "2020-10-16T18:36:22+02:00"
    "$converted_detail" => []
    "$editable" => true
    "City" => null
    "No_of_Employees" => null
    "Mobile" => null
    "$orchestration" => null
    "Last_Name" => "Doe"
    "Layout" => array:2 [ …2]
    "State" => null
    "Lead_Source" => null
    "Country" => null
    "Created_By" => array:3 [ …3]
    "Fax" => null
    "Annual_Revenue" => null
    "Secondary_Email" => null
  ],
"1300272000000442073" => array:47 [
    "Owner" => array:3 [ …3]
    "Company" => null
    "Email" => null
    "Description" => null
    "$currency_symbol" => "USD"
    "Rating" => null
    "$review_process" => null
    "Website" => null
    "Twitter" => null
    "Salutation" => null
    "Last_Activity_Time" => null
    "First_Name" => "John"
    "Full_Name" => "John Doe"
    "Lead_Status" => null
    "Industry" => null
    "Record_Image" => null
    "Modified_By" => array:3 [ …3]
    "$review" => null
    "$state" => "save"
    "Skype_ID" => null
    "$converted" => false
    "$process_flow" => false
    "Phone" => null
    "Street" => null
    "Zip_Code" => null
    "id" => "1300272000000442073"
    "Email_Opt_Out" => false
    "$approved" => true
    "Designation" => null
    "$approval" => array:4 [ …4]
    "Modified_Time" => "2020-10-16T18:36:22+02:00"
    "Created_Time" => "2020-10-16T18:36:22+02:00"
    "$converted_detail" => []
    "$editable" => true
    "City" => null
    "No_of_Employees" => null
    "Mobile" => null
    "$orchestration" => null
    "Last_Name" => "Doe"
    "Layout" => array:2 [ …2]
    "State" => null
    "Lead_Source" => null
    "Country" => null
    "Created_By" => array:3 [ …3]
    "Fax" => null
    "Annual_Revenue" => null
    "Secondary_Email" => null
  ]
]

```

## Get a single record

```php
$zohoCrm = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$lead = $zohoCrm->leads->get('[ID OF THE LEAD]');
```

## Create a new Record

You can create a single record with an array of the record data. The array keys **must be the API names used in the CRM**. You can find those in `Settings > Developers > API > Api Names` in zoho crm itself.

```php
$data = [
    'First_Name' => 'John',     
    'Last_Name' => 'Doe',
    'Email' => 'test@example.com'
];


$zohoCrm = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$lead = $zohoCrm->leads->create($data);
````

## Update an existing Record

Same as with record creation, you can edit an existing record with an array of data.
```php
$data = [
    'First_Name' => 'John',     
    'Last_Name' => 'Doe',
    'Email' => 'test@example.com'
];

$zohoCrm = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$lead = $zohoCrm->leads->update('[LEAD ID]', $data);
```

## Mass Record Creation

You can also mass-created a list of records. Beware that this will "hide" any error occurring during the creation of the record.

```php
$data = [
    [
        'First_Name' => 'John',     
        'Last_Name' => 'Doe',
        'Email' => 'test@example.com'
    ],
    [
        'First_Name' => 'Jane',     
        'Last_Name' => 'Doe',
        'Email' => 'test2@example.com'
    ],
];

$zohoCrm = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$lead = $zohoCrm->leads->createMany($data);
```

## Mass Record Update

You can also mass-update a list of records. Beware that this will "hide" any error occurring during the saving of the record.

```php
$data = [
    '[ID OF THE RECORD]' => [
        'First_Name' => 'John',     
        'Last_Name' => 'Doe',
        'Email' => 'test@example.com'
    ],
    '[ID OF THE RECORD 2]' => [
        'First_Name' => 'Jane',     
        'Last_Name' => 'Doe',
        'Email' => 'test2@example.com'
    ],
];

$zohoCrm = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$lead = $zohoCrm->leads->updateMany($data);
```

## Deleting a record

You can delete a record like this:

```php
$zohoCrm = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$zohoCrm->leads->delete('[ID OF THE RECORD');
```

## Getting the list of related records

You can fetch the list of related records from a record like this:

```php
$zohoCrm = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$zohoCrm->leads->getRelatedRecords('[ID OF THE RECORD', '[NAME OF THE RELATION]');
```

## List and Download attachments

You can list and download the attachments of a record.

```php
$zohoCrm = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$lead = $zohoCrm->leads->get('[ID OF THE RECORD]');
$attachments = $lead->attachments();

$file = fopen('/path/to/file', 'w');
$lead->downloadAttachment($attachments->first()->getId(), $file);
```

## Upload attachment / photo

You can upload attachments to a record.

```php
$zohoCrm = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$lead = $zohoCrm->leads->get('[ID OF THE RECORD]');

$file = fopen('/path/to/file', 'w');
$lead->uploadAttachment('File Name', $file);
$lead->uploadPhoto('Photo Name', $file);
```

## List Notes

You can list the notes of a record.

```php
$zohoCrm = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$lead = $zohoCrm->leads->get('[ID OF THE RECORD]');
$notes = $lead->notes();
```

## Update a related record

```php
$zohoCrm = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$zohoCrm->leads->updateRelatedRecord('[ID OF THE RECORD', '[NAME OF THE RELATION]', '[ID OF THE RELATED RECORD]', $relationData = []);
