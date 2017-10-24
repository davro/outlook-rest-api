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
        $this->code = 'OAQABAAIAAABHh4kmS_aKT5XrjzxRAtHzXuxisiHCl3iUQ4NAcKtyiL2Nl7rguLVpRh7DU1zoiGKuTgXJJtfYUW6zMVkuiSEufPnbw6KNVAy4zjYMiGhBcfx7enfrlNr2t5vBTWTGqH94Xc74Wc0zYdtzDtBg7faFHPRO5zH8JHiqSYs5ApT22Si8FTTnfpeFQEi3XvDT4WqeydYtx83BbfP90L6Q-G5n9QK4lvFPYpcPTZAe_iD4gKVmiylIuBK4CKxHLMZNBx4lsmfDuNv-TPauShYRBZzRpZqsP5QFuo8i5MGxGAG0Qvb4hNBZs27Gn0L2U34rBn1F2YW3F2ItyIBXHMMxm8-D-6UmEjSMmwLZEU4aibkXxVIrzORqmzu8z2dDsN7rO7p6evC4_HDPivwGv9EyNDeA1y6fYUy5UBSBeS2kTuY6ZjZyqtarhiQOvZ3oGha8IXWipb9N02wxGW9ktjD4NP8XBV8l-NDTmmNDtlWaJsLQHA5buqUl6bGOLmWLSyVP9VSNAEa9NAP1eQFx80KtBkoo9CEixcNQ8VFumkv1fPiofKXvy-ihEfIkzv1oTAHHlGBK9MJD2VMEVkww8z1DzBBQYcoeNSTAqBGFRZS1K-zeZfn-7PzfW3dqAkBsQMbHAPRMu6vehJyo4KsSkxikKN2QcizRfwoA8jZPO0oRQUGu8tk2hCM1RTVjyoHjjFv24J_pwY6vEX0Iz4t8xqgcya_fMSHIOOiVu6X5bgoMzBzUSwvVDmLqZ20LsLx737pfwYUohVoais1efpSEZ17A4pqcIAA';
    }
    
    public function testInstance()
    {
        $service = new RestService('');
        $this->assertInstanceOf('Outlook\RestService', $service);
    }
    
    public function testMessageExample()
    {
        $authService = new AuthService;
        $authService->setClientId($this->clientId);
        $authService->setClientSecret($this->clientSecret);
        
        $tokens = $authService->getTokenFromAuthCode($this->code, 'https://workspace.local/login');

        $restService = new RestService($tokens);        
        
        $messages = $restService->getMessages(1);
        var_dump($messages); 
    }
    
//    public function testCalendarExample()
//    {
//        $authService = new AuthService;
//        $authService->setClientId($this->clientId);
//        $authService->setClientSecret($this->clientSecret);
//        
//        $tokens = $authService->getTokenFromAuthCode($this->code, 'https://workspace.local/login');
//
//        $restService = new RestService($tokens);        
//        
//
//    }
    
    
}
