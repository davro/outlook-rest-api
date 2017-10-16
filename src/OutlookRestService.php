<?php
/**
 * Outlook Auth Service
 * 
 * This class requires the web application to be configured with the Microsoft Apps.
 * So you can populate clientId, clientSecret and also the login and logout URI's
 * 
 * https://apps.dev.microsoft.com/#/appList
 *
 * @author	David Stevens <mail.davro@gmail.com>
 * @package    OutlookRestApi
 * @licence	LGPL
 *
 */
class OutlookRestService
{
    private static $outlookApiUrl = "https://outlook.office.com/api/v2.0";

    public static function makeApiCall($access_token, $user_email, $method, $url, $payload = NULL) {
        $headers = array(
            "User-Agent: Outlook Rest API/1.0",        // Sending a User-Agent header is a best practice.
            "Authorization: Bearer " . $access_token,  // Always need our auth token!
            "Accept: application/json",                // Always accept JSON response.
            "client-request-id: " . self::makeGuid(),  // Stamp each new request with a new GUID.
            "return-client-request-id: true",          // Tell the server to include our request-id GUID in the response.
            "X-AnchorMailbox: " . $user_email          // Provider user's email to optimize routing of API call
        );

        $curl = curl_init($url);

        switch (strtoupper($method)) {
            case "GET":
                // Nothing to do, GET is the default and needs no
                break;
            case "POST":
                // Add a Content-Type header (IMPORTANT!)
                $headers[] = "Content-Type: application/json";
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                break;
            case "PATCH":
                // Add a Content-Type header (IMPORTANT!)
                $headers[] = "Content-Type: application/json";
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                break;
            case "DELETE":
                error_log("Doing DELETE");
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
                error_log("INVALID API METHOD: " . $method);
                exit;
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curl);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($httpCode >= 400) {
            return array(
                'errorNumber' => $httpCode,
                'error' => 'API Request returned HTTP error ' . $httpCode,
                '' => $headers
            );
        }

        $curl_errno = curl_errno($curl);
        $curl_err = curl_error($curl);

        if ($curl_errno) {
            $msg = $curl_errno . ": " . $curl_err;
            curl_close($curl);
            return array('errorNumber' => $curl_errno,
                'error' => $msg);
        } else {
            curl_close($curl);
            return json_decode($response, true);
        }
    }

    // This function generates a random GUID.
    public static function makeGuid() {
        if (function_exists('com_create_guid')) {
            return strtolower(trim(com_create_guid(), '{}'));
        } else {
            $charid = strtolower(md5(uniqid(rand(), true)));
            $hyphen = chr(45);
            $uuid = substr($charid, 0, 8) . $hyphen
                  . substr($charid, 8, 4) . $hyphen
                  . substr($charid, 12, 4) . $hyphen
                  . substr($charid, 16, 4) . $hyphen
                  . substr($charid, 20, 12);

            return $uuid;
        }
    }

    public static function getUser($access_token) {
        $getUserParameters = array(
            // Only return the user's display name and email address
            "\$select" => "DisplayName,EmailAddress"
        );
        $getUserUrl = self::$outlookApiUrl . "/Me?" . http_build_query($getUserParameters);

        return self::makeApiCall($access_token, "", "GET", $getUserUrl);
    }

    public static function getMessages($access_token, $user_email) {
        $getParameters = array(
            "\$select" => "Subject,Body,ReceivedDateTime,From",
            "\$orderby" => "ReceivedDateTime DESC",
            "\$top" => "15"
        );
        $getUrl = self::$outlookApiUrl . "/Me/MailFolders/Inbox/Messages?" . http_build_query($getParameters);

        return self::makeApiCall($access_token, $user_email, "GET", $getUrl);
    }

    public static function getCalendar($access_token, $user_email) {

        $getParameters = array(
            "\$select" => "Subject,Organizer,Start,End"
        );
        $getUrl = self::$outlookApiUrl . "/Me/Events?" . http_build_query($getParameters);

        return self::makeApiCall($access_token, $user_email, "GET", $getUrl);
    }

    public static function getContacts($access_token, $user_email) {
        $getParameters = array(
            "\$select" => "EmailAddresses,GivenName,Surname"
        );
        $getUrl = self::$outlookApiUrl . "/Me/Contacts?" . http_build_query($getParameters);

        return self::makeApiCall($access_token, $user_email, "GET", $getUrl);
    }

}

