<?php
namespace Outlook;

/**
 * Outlook Auth Service
 *
 * This class requires the web application to be configured with the Microsoft Apps.
 * So you can populate clientId, clientSecret and also the login and logout URI's
 *
 * https://apps.dev.microsoft.com/#/appList
 *
 * @author    David Stevens <mail.davro@gmail.com>
 * @package   OutlookRestApi
 * @licence   LGPL
 *
 */
class AuthService
{
    protected $clientId = "";
    protected $clientSecret = "";
    protected $authority = "https://login.microsoftonline.com";
    protected $authorizeUrl = '/common/oauth2/v2.0/authorize?client_id=%1$s&redirect_uri=%2$s&response_type=code&scope=%3$s';
    protected $tokenUrl = "/common/oauth2/v2.0/token";
    
    // Application scopes, default openid.
    protected $scopes = array(
        "openid"
//        "https://outlook.office.com/mail.read",
//        "https://outlook.office.com/mail.send",
//        "https://outlook.office.com/calendars.readwrite",
//        "https://outlook.office.com/contacts.readwrite",
    );
    
    public function getClientId()
    {
        return $this->clientId;
    }

    public function setClientId($value)
    {
        $this->clientId = $value;
        return true;
    }
    
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    public function setClientSecret($value)
    {
        $this->clientSecret = $value;
        return true;
    }
    
    public function getAuthority()
    {
        return $this->authority;
    }
    
    public function getAuthorizeUrl()
    {
        return $this->authorizeUrl;
    }
    
    public function getScopes()
    {
        return $this->scopes;
    }
    
    public function addScope($name)
    {
        $this->scopes[] = $name;
        return true;
    }
    
    public function addScopes($scopes)
    {
        if (count($scopes) > 0) {
            foreach ($scopes as $key => $scope) {
                $this->addScope($scope);
            }
            return true;
        }
        return false;
    }
    
    public function getLoginUrl($redirectUri, $scopes)
    {

        $scopestr = implode(" ", $this->scopes);

        $loginUrl = $this->authority .
            sprintf(
                $this->authorizeUrl,
                $this->clientId,
                urlencode($redirectUri),
                urlencode($scopestr)
            );
        return $loginUrl;
    }

    public function getToken($grantType, $code, $redirectUri)
    {

        // Build the form data to post to the OAuth2 token endpoint
        $tokenRequestData = array(
            "grant_type"    => $grantType,
            "code"          => $code,
            "redirect_uri"  => $redirectUri,
            "scope"         => implode(" ", $this->scopes),
            "client_id"     => $this->clientId,
            "client_secret" => $this->clientSecret
        );

        // HTTP http_build_query is important to get the data formatted as expected.
        $tokenRequestBody = http_build_query($tokenRequestData);

        $curl = curl_init($this->authority . $this->tokenUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $tokenRequestBody);

        $response = curl_exec($curl);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode >= 400) {
            return array('errorNumber' => $httpCode,
                'error' => 'Token request returned HTTP error ' .
                    $httpCode .
                    '<pre>' . var_export($tokenRequestData, true) . '</pre>'
            );
        }

        // CURL :: Check error
        $curl_errno = curl_errno($curl);
        $curl_err   = curl_error($curl);
        if ($curl_errno) {
            $msg = $curl_errno . ": " . $curl_err;
            return array('errorNumber' => $curl_errno,
                'error' => $msg);
        }

        curl_close($curl);

        // The response is a JSON payload, so decode it into an array.
        $json_vals = json_decode($response, true);
        foreach ($json_vals as $key => $value) {
            error_log("  " . $key . ": " . $value);
        }

        return $json_vals;
    }
    
    // \/ NEW \/
    
    public  function getTokenFromAuthCode($authCode, $redirectUri)
    {
        return $this->getToken("authorization_code", $authCode, $redirectUri);
    }

    public static function getAccessToken($redirectUri, $app)
    {
        // Is there an access token in the session?

        $current_token = $app['session']->get('user')['tokens']['access_token'];
        // disable as it keep clearing token ......
        return $current_token;

        // NEEDS TESTING (Kept renewing token commented out)
//        if (!is_null($current_token)) {
//            // Check expiration
//            $expiration = $app['session']->get('user')['tokens']['token_expires'];
//            if ($expiration < time()) {
//                error_log('Token expired! Refreshing...');
//                // Token expired, refresh
//                $refresh_token = $app['session']->get('user')['tokens']['refresh_token'];
//                $new_tokens = self::getTokenFromRefreshToken($refresh_token, $redirectUri);
//
//                // Update the stored tokens and expiration
//                $app['session']->set('user')['tokens']['access_token'] = $new_tokens['access_token'];
//                $app['session']->set('user')['tokens']['refresh_token'] = $new_tokens['refresh_token'];
//
//                // expires_in is in seconds
//                // Get current timestamp (seconds since Unix Epoch) and
//                // add expires_in to get expiration time
//                // Subtract 5 minutes to allow for clock differences
//                $expiration = time() + $new_tokens['expires_in'] - 300;
//                $app['session']->set('user')['tokens']['token_expires'] = $expiration;
//
//                // Return new token
//                return $new_tokens['access_token'];
//            } else {
//                // Token is still valid, return it
//                return $current_token;
//            }
//        } else {
//            return null;
//        }
    }

    public static function getTokenFromRefreshToken($refreshToken, $redirectUri)
    {
        return self::getToken("refresh_token", $refreshToken, $redirectUri);
    }
}
