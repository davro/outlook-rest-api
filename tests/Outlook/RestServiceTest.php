<?php
namespace Tests\Outlook;

use Outlook\AuthService;
use Outlook\RestService;

class RestServiceTest extends \PHPUnit_Framework_TestCase
{
    public $clientId;
    public $clientSecret;
    
    public function setUp()
    {
        // Example application client, id, secret.
        $this->clientId     = "1a3e0989-7bde-422f-afe7-156831706796";
        $this->clientSecret = "k5dJD46DCb1BRqAhaWDmeN5";
        
        // Token nicked from 'David' auth return code.
        $this->code = 'OAQABAAIAAABHh4kmS_aKT5XrjzxRAtHz18VfqqzjtkrVTHxelmOaoqjQ9j05k5jpDIklN_T3LJVY7B1MYzMX5HVhJJJqC7TgHIUEUd5xHWerdeTnP6V5HLrG-5-iixgxkqRakppkEVfchpbnAt3eZIzRxhqyrexUiKSdL35HROslVXZAFvC74oS1FU9lxMh9Dv6rRtRtBif0yjSEg_Ddm135NCO8cHvan2DOHbbkiwKRtiQx8JBZi4cLXsKDl5Tri3x7hb1soWKNEYaYRKEY8J_asMFzwZeUBDsPgehW-2kkO2FzamcVL6plfRZP2DJoSWiSUEY7FjwqBK4DxU44N1Vx5YMncF7hzRXRVQagZ15c2zgXbKJ_t3GX38un9gcK8CmbSmj2SEyPjb_z2TRCBEdI6c4T3yzOyMSEJoVyG8DhUEom1OzQfJxrFFlv4KawiKNUewDjHVLY6YTrGQ8DHoNVtp70wNnsOQEyK4_x0hSTOMAwzMQ-l6DKp1idNwKHVRRXGwNa6b-Rfs6fnKImryDYBRoSzbH1STAV9O77eR9zgzi7NMSpTKIQAXYDnGQ_ytIykRwL0X8zpQ6yAJ9EEbyb3PGuHsnoOek53bJxKtejXOYenf0Y8zMBd3Zqkwq6-I4uMetDaZikIHfuBcUzv_UrBbAQBzTjD4BfrseIwkwo0p13660Yt7FYdMwZRRnEzmq1lJwzgPRCyqC6XnCLwGxFxet3JNR2iaB0rsRWnV8BPjKSswuxjFLg2hSpy1dEvI7DQNrMHVLz8A2MIzy7xvpCcs9wcVQfIAA';
    }
    
    public function testInstance()
    {
        $service = new RestService('');
        $this->assertInstanceOf('Outlook\RestService', $service);
    }
    
    public function XtestMessageExample()
    {
        $authService = new AuthService;
        $authService->setClientId($this->clientId);
        $authService->setClientSecret($this->clientSecret);
        
        $tokens = $authService->getTokenFromAuthCode($this->code, 'https://workspace.local/login');

        $restService = new RestService($tokens);
        $messages = $restService->getMessages(1);
        var_dump($messages); 
    }
    
    // DateStart DateEnd not working ...
    public function XtestCalendarExample()
    {
        $authService = new AuthService;
        $authService->setClientId($this->clientId);
        $authService->setClientSecret($this->clientSecret);
        
        $tokens = $authService->getTokenFromAuthCode($this->code, 'https://workspace.local/login');

        $restService = new RestService($tokens);        
        $events = $restService->getCalendar();
        var_dump($events);
    }
    
    public function XtestCalendarCreateExample()
    {
        $authService = new AuthService;
        $authService->setClientId($this->clientId);
        $authService->setClientSecret($this->clientSecret);
        
        $tokens = $authService->getTokenFromAuthCode($this->code, 'https://workspace.local/login');

        $restService = new RestService($tokens);   
        
        $payload = '{
  "Subject": "Discuss the Calendar REST API (Just Testing)",
  "Body": {
    "ContentType": "HTML",
    "Content": "I think it will meet our requirements!"
  },
  "Start": {
      "DateTime": "2017-10-26T18:00:00",
      "TimeZone": "Pacific Standard Time"
  },
  "End": {
      "DateTime": "2017-10-26T19:00:00",
      "TimeZone": "Pacific Standard Time"
  },
  "Attendees": [
    {
      "EmailAddress": {
        "Address": "oli.chowdhury@crick.ac.uk",
        "Name": "Sir Oli"
      },
      "Type": "Required"
    }
  ]
}';
        
        $events = $restService->createCalendarEntry($payload);
        var_dump($events);
    }
    
}
