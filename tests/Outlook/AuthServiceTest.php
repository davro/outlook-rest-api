<?php
namespace Tests\Outlook;

use Outlook\AuthService;

class AuthServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $service = new AuthService;
        $this->assertInstanceOf('Outlook\AuthService', $service);
    }
    
    public function testGetClientId()
    {
        $service = new AuthService();
        $this->assertEquals('', $service->getClientId());
    }
    
    public function testSetClientId()
    {
        $clientId = '1a3e0989-7bde-422f-afe7-156831706796';
        
        $service = new AuthService();
        $this->assertTrue($service->setClientId($clientId));
        $this->assertEquals($clientId, $service->getClientId($clientId));
    }
    
    public function testGetClientSecret() 
    {        
        $service = new AuthService();
        $this->assertEquals('', $service->getClientSecret());
    }
    
    public function testSetClientSecret()
    {
        $clientSecret = "k5dJD46DCb1BRqAhaWDmeN5";
        
        $service = new AuthService();
        $this->assertTrue($service->setClientSecret($clientSecret));
        $this->assertEquals($clientSecret, $service->getClientSecret());
    }
    
    public function testGetAuthority()
    {
        $authority = "https://login.microsoftonline.com";
        $service = new AuthService();
        $this->assertEquals($authority, $service->getAuthority());
    }
    
    public function testGetAuthorizeUrl()
    {
        $authorizeUrl = '/common/oauth2/v2.0/authorize?client_id=%1$s&redirect_uri=%2$s&response_type=code&scope=%3$s';
        $service = new AuthService();
        $this->assertEquals($authorizeUrl, $service->getAuthorizeUrl());
    }
    
    public function testDefaultScopes()
    {
        $service = new AuthService;
        $this->assertEquals(['openid'], $service->getScopes());
    }
    
    public function testAddingSingleScope()
    {
        $service = new AuthService();
        $this->assertTrue($service->addScope('mail.read'));
        
        $this->assertEquals(['openid', 'mail.read'], $service->getScopes());
    }
    
    public function testAddingMultipleScopes()
    {
        $service = new AuthService();
        
        $this->assertTrue(
            $service->addScopes(
                [
                    'https://outlook.office.com/mail.read',
                    'https://outlook.office.com/mail.write',
                    'https://outlook.office.com/calendars.readwrite',
                    'https://outlook.office.com/contacts.read'
                ]
            )
        );
                
        $this->assertEquals(
            [
                'openid', 
                'https://outlook.office.com/mail.read',
                'https://outlook.office.com/mail.write',
                'https://outlook.office.com/calendars.readwrite',
                'https://outlook.office.com/contacts.read'
            ],
            $service->getScopes()
        );
    }
}
