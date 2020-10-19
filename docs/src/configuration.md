# Configuration

## oAuthClient Configuration
The oAuthClient can be configured to use any of the authentication methods provided by zoho.

See [https://github.com/Weble/ZohoClient](https://github.com/Weble/ZohoClient) for more details on the various authentication types

In the most common use cases, you'll be setting up a server to server communication by using the **offline mode** and providing an already generated refresh token.

You can also provide which region your zoho account is in, by using the options listed in the ```\Weble\ZohoClient\Enums\Region``` class.

```php
$oAuthClient = new \Weble\ZohoClient\OAuthClient('[CLIENT_ID]', '[CLIENT_SECRET]');
$oAuthClient->setRefreshToken('[REFRESH_TOKEN]');
$oAuthClient->setRegion(\Weble\ZohoClient\Enums\Region::eu());
$oAuthClient->useCache($yourPSR6CachePool);
$oAuthClient->offlineMode();
```

It's **HIGHLY RECOMMENDED** to setup a **cache pool** to avoid having to refresh the access token across requests, and just refresh it when it's nearly expired.

The library will do this automatically on its own.

## Zoho Crm Client Configuration

### Mode
You can also configure the Zoho Crm client class based on your needs.

The most common use case is to choose which zoho apis we're working against.
You can choose between:
 - ```production``` which targets the normal zoho crm apis 
 - ```sandbox``` which goes into your own [crm sandbox](https://help.zoho.com/portal/en/kb/crm/process-management/sandbox/articles/using-sandbox) 
 - ```developer``` which goes to the special [crm developer edition](https://www.zoho.com/crm/developer/docs/dev-edition.html)

```php
// setup the zoho crm client
$client = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$client->setMode(\Webleit\ZohoCrmApi\Enums\Mode::production()); 
```

### Throttling

Since zoho rate limits its API calls, you can set the throttling that the library will use to limit the speed at which calls the zoho apis.

To set a maximum of 1 call per second, you can do so:
```php
$client = new \Webleit\ZohoCrmApi\Client($oAuthClient);
$maxRequests = 1;
$durationInSeconds = 1;
$client->throttle($maxRequests, $durationInSeconds);

``` 
