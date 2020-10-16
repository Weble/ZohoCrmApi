# Usage

The client exposes a list of properties that match the official zoho api names.

Each of these properties is a ```module``` that lets you interact with the respective zoho module.

For example, ```$client->leads``` is the Lead module in the CRM, and the ```$client->salesorders``` property is the SalesOrders module in CRM.

Each of the ```records``` module has a set of common methods you can use:


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
