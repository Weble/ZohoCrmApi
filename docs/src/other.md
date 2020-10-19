# Advanced Usage

## Pagination

The ```getList()``` method deals with pagination too.
It will return a special ```RecordCollection``` object, on which you'll get the pagination informations.

```php
$zohoCrm = new \Webleit\ZohoCrmApi\Client($oAuthClient);

// This is the collection of records
$leads = $zohoCrm->leads->getList();

// This is a pagination object related to the last request
$pagination = $leads->pagination();

$count = $pagination->count();
$page = $pagination->page();
$perPage = $pagination->perPage();

if ($pagination->hasMoreRecords()) { 
    // DO SOMETHING
}
```

## Change Pagination and Filters

You can also pass parameters to the ```getList()``` method:

```php
$zohoCrm = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$leads = $zohoCrm->leads->getList([
    // Array of fields to get in the request
    'fields' => null,
    // List of record ids to fetch
    'ids' => [],
    // Field by which ordering the result
    'sort_order' => null,
    // Sort direction. Can be "asc" or "desc"
    'sort_by' => null,
    // Show also the converted records (leads). Default: false. Can be "false", "true", "both"
    'converted' => null,
    // Show also the approved records (leads). Default: false,  Can be "false", "true", "both"
    'approved' => null,
    // Pagination
    'page' => 1,
    // Pagination
    'per_page' => 200,
    // Custom View ID to use
    'cvid' => '',
    // Territory id for territory management
    'terrory_id' => '',
    // Include child terrories in territory management
    'include_child' => null,
]);
```
It's way more convenient to use the dedicated ```ListParameters``` class though:

```php
$zohoCrm = new \Webleit\ZohoCrmApi\Client($oAuthClient);

$params = new \Webleit\ZohoCrmApi\Request\ListParameters();
$params
    ->sortBy('First_Name')
    ->sortAsc()
    ->fields(['First_Name', 'Last_Name', 'ID']);

$leads = $zohoCrm->leads->getList($params->toArray());
```

## Searching

You can also search for records:

```php
$zohoCrm = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$list = $zohoCrm->leads->searchRaw('Some Text');
$list = $zohoCrm->leads->searchEmail('text@example.com');
$list = $zohoCrm->leads->searchPhone('+391232131');
$list = $zohoCrm->leads->searchWord('Word');
```

## Single Record Actions

Any time you have a single ```Model```, the model itself will "carry on" the actions that can be performed on it.

For example if ```$lead``` will be an instance of ```Webleit\ZohoCrmApi\Models\Record``` that represents the lead itself, you will be able to call any record-related method on it: 

```php 
$lead->update($data);
$lead->delete();
$lead->updateRelatedRecord($relationName, $relatedRecordId, $relationData = []);
$lead->uploadPhoto($fileName, $fileContents);
```
