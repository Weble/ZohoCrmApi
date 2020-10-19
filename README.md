# Zoho CRM API - PHP SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/webleit/zohocrmapi.svg?style=flat-square)](https://packagist.org/packages/webleit/zohocrmapi)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/weble/zohocrmapi/run-tests?label=tests&style=flat-square)](https://github.com/weble/zohocrmapi/actions?query=workflow%3Arun-tests)
[![Total Downloads](https://img.shields.io/packagist/dt/webleit/zohocrmapi.svg?style=flat-square)](https://packagist.org/packages/webleit/zohocrmapi)

This Library is a SDK in PHP that simplifies the usage of the Zoho CRM Api version 2 ([https://www.zoho.com/crm/help/api/v2](https://www.zoho.com/crm/help/api/v2))
It provides both an interface to ease the interaction with the APIs without bothering with the actual REST request, while packaging the various responses using very simple Model classes that can be then uses with any other library or framework.

## Installation 

```
composer require webleit/zohocrmapi
```

In order to use the library, just require the composer autoload file, and then fire up the library itself.
In order for the library to work, you need to be authenticated with the zoho crm apis.

```php
require './vendor/autoload.php';

// setup the generic zoho oath client
$oAuthClient = new \Weble\ZohoClient\OAuthClient('[CLIENT_ID]', '[CLIENT_SECRET]');
$oAuthClient->setRefreshToken('[REFRESH_TOKEN]');
$oAuthClient->setRegion(\Weble\ZohoClient\Enums\Region::us());
$oAuthClient->useCache($yourPSR6CachePool);
$oAuthClient->offlineMode();

// setup the zoho crm client
$client = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$client->setMode(\Webleit\ZohoCrmApi\Enums\Mode::production()); 

// Create the main class
$zohoCrm = new \Webleit\ZohoCrmApi\ZohoCrm($client);
```

## Authentication

See [https://github.com/Weble/ZohoClient](https://github.com/Weble/ZohoClient) for more details on the various authentication types

## Usage

To call any Api, just use the same name reported in the api docs. 
You can get the list of supported apis using the getAvailableModules() method

#### Example

```php
$users = $zohoCrm->users->getList();
$leads = $zohoCrm->leads->getList();
```

## Return Types

Any "list" api call returns a Collection object, which is taken for Laravel Collection package.
You can therefore use the result as Collection, which allows mapping, reducing, serializing, etc
    
## Contributing

Finding bugs, sending pull requests or improving the docs - any contribution is welcome and highly appreciated

## Versioning

Semantic Versioning Specification (SemVer) is used.

## Copyright and License

Copyright Weble Srl under the MIT license.
