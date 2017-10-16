# Outlook-Rest-API
Outlook Resful API for OpenID and Application Scope.
Examples of using the basic buildin API calls getLoginUrl, getMessages, getContacts.

Scope for OpenID
```php
$redirectUri = 'https://workspace.local/login';
$hrefPath = CrickAuthService::getLoginUrl($redirectUri);
```


Scope for Mail
```php
$data = CrickOutlookService::getMessages(
    CrickAuthService::getAccessToken('https://workspace.local/login', $app), 
    $app['session']->get('user')['email']
);
```


Scope for Calander 
```php
$data = CrickOutlookService::getContacts(
    CrickAuthService::getAccessToken('https://workspace.local/login', 
    $app), $app['session']->get('user')['email']
);
```


