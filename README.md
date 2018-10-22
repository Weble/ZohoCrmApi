# Zoho Sign API - PHP SDK

This Library is a SDK in PHP that simplifies the usage of the Zoho Sign Api version 1 ([https://www.zoho.com/sign/api/](https://www.zoho.com/sign/api/))
It provides both an interface to ease the interaction with the APIs without bothering with the actual REST request, while packaging the various responses using very simple Model classes that can be then uses with any other library or framework.

## Installation 

```
composer require webleit/zohosignapi
```

In order to use the library, just require the composer autoload file, and then fire up the library itself.
In order for the library to work, you need to be authenticated with the zoho sign apis.

```php
require './vendor/autoload.php';
$zohoSign = new ZohoSign('[CLIENT_ID]', '[CLIENT_SECRET]', '[REFRESH_TOKEN]');
```

## Authentication

Zoho Sign Api use oAuth2 as the authentication method, as described [here](https://www.zoho.com/sign/api/).
In order to authenticate the library, you can use two different methods:


#### 1. Refresh Token

If you want to, you can skip steps 1-7 and directly use Step 8, but you need to generate the refresh token yourself 
manually, using the instruction provided by zoho: [https://www.zoho.com/sign/api/#getting-started](https://www.zoho.com/sign/api/#getting-started)

```php
$zohoSign = new ZohoSign('[CLIENT_ID]', '[CLIENT_SECRET]', '[REFRESH_TOKEN]');
```


#### 2. Grant Token / Authorization Page

This is the best way, even if it requires more work.
1. Go to [https://accounts.zoho.com/developerconsole] (https://accounts.zoho.com/developerconsole)
2. Create a Client Id. **Remember the redirect url you set, you will need it**
3. Use the library to create a Grant token Url, and create a page (probably for the web application administrator?) 
that redirects to the given url, to allow the user to authenticate with his zoho sign credentials.
    ```php
    use \Webleit\ZohoSignApi\ZohoSign;
    
    $zohoSign = new ZohoSign('[CLIENT_ID]', '[CLIENT_SECRET]');
    $redirectUrl = $zohoSign->getGrantCodeConsentUrl('[YOUR_REDIRECT_URI]');
    
    // Redirect your user here to $redirectUrl
    ``` 
4. In your web application, create an endpoind for the redirect url you set in step 2.
5. When a request comes in to the new endpoint at `[REDIRECT_URL]`, you can use the library to parse the grant token from the url

    ```php
    ...
      $grantToken = \Webleit\ZohoSignApi\ZohoSign::parseGrantTokenFromUrl($fullUri);
      
      // or, alternatively, $grantToken = $_GET['code'];
    ...
    ```

6. Give the Grant Code to the library
    
    ```php
    $zohoSign = new ZohoSign('[CLIENT_ID]', '[CLIENT_SECRET]');
    $zohoSign->setGrantCode($grantToken);

    ```
    
7. Get the fresh token, and store it.
    
    ```php
    $refreshToken = $zohoSign->getRefreshToken();
    ```

8. From now on, use the refresh Token to create the library instance

    ```php
    $zohoSign = new ZohoSign('[CLIENT_ID]', '[CLIENT_SECRET]', $refreshToken);
    ```
    
## Usage

To call any Api, just use the same name reported in the api docs. 
You can get the list of supported apis using the getAvailableModules() method

#### Example

```php
$zohoSign = new ZohoSign('[CLIENT_ID]', '[CLIENT_SECRET]', '[REFRESH_TOKEN]');
$requests = $zohoSign->requests->getList();
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
