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
$restService = new RestService($tokens);
$messages = $restService->getMessages(1);
```

Scope for Calander 
```php
$restService = new RestService($tokens);        
$events = $restService->getCalendar();
```

