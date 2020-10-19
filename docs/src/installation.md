# Installation

```bash
composer require webleit/zohocrmapi
```

In order to use the library, just require the composer autoload file, and then fire up the library itself.


## Authentication

For the library to work, you need to be authenticated with the zoho crm apis.

Here is a basic example on how to initialize the library.

See [https://github.com/Weble/ZohoClient](https://github.com/Weble/ZohoClient) for more details on the various authentication types

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



