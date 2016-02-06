<?php
define('FLICKR_API_KEY', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
define('FLICKR_API_SECRET', 'XXXXXXXXXXXXXXXX');


class Flickr
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function authenticate()
    {
        if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']) {
            return;
        }

        if (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) {
            $this->getAccessToken();
        } else {
            $this->getRequestToken();
        }
    }

    public function uploadPhoto()
    {
        $oauth_data = $this->getOAuthParameters();
        $oauth_data['oauth_token'] = $_SESSION['oauth_access_token'];
        $oauth_data['title'] = $_POST['title'];
        $oauth_data['tags'] = $_POST['tags'];

        $upload_url = 'https://up.flickr.com/services/upload/';
        $oauth_data['oauth_signature'] = $this->getSignature('POST', $upload_url, $oauth_data);
        $oauth_data['photo'] = '@' . $_FILES['photo']['tmp_name'];

        $curl = new Curl();
        $curl->post($upload_url, $oauth_data);
        return $curl;
    }

    private function getOAuthParameters()
    {
        return array(
            'oauth_nonce' => md5(microtime() . mt_rand()),
            'oauth_timestamp' => time(),
            'oauth_consumer_key' => FLICKR_API_KEY,
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version' => '1.0',
        );
    }

    private function getSignature($request_method, $url, $parameters)
    {
        ksort($parameters, SORT_STRING);
        $request = implode('&', array(
            rawurlencode($request_method),
            rawurlencode($url),
            rawurlencode(http_build_query($parameters, '', '&', PHP_QUERY_RFC3986)),
        ));
        $key = FLICKR_API_SECRET . '&';
        if (!empty($_SESSION['oauth_access_token_secret'])) {
            $key .= $_SESSION['oauth_access_token_secret'];
        } elseif (!empty($_SESSION['oauth_token_secret'])) {
            $key .= $_SESSION['oauth_token_secret'];
        }
        $signature = base64_encode(hash_hmac('sha1', $request, $key, true));
        return $signature;
    }

    private function getRequestToken()
    {
        $oauth_data = $this->getOAuthParameters();
        $oauth_data['oauth_callback'] = implode('', array(
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http',
            '://',
            $_SERVER['SERVER_NAME'],
            $_SERVER['SCRIPT_NAME'],
        ));

        $request_token_url = 'https://www.flickr.com/services/oauth/request_token';
        $oauth_data['oauth_signature'] = $this->getSignature('POST', $request_token_url, $oauth_data);

        $curl = new Curl();
        $curl->post($request_token_url, $oauth_data);

        parse_str($curl->response, $parts);
        $_SESSION['oauth_token_secret'] = $parts['oauth_token_secret'];

        // Continue to Flickr for user's authorization.
        header('Location: https://secure.flickr.com/services/oauth/authorize?' . http_build_query(array(
            'oauth_token' => $parts['oauth_token'],
            'perms' => 'write',
        )));
        exit;
    }

    private function getAccessToken()
    {
        $oauth_data = $this->getOAuthParameters();
        $oauth_data['oauth_token'] = $_GET['oauth_token'];
        $oauth_data['oauth_verifier'] = $_GET['oauth_verifier'];

        $access_token_url = 'https://www.flickr.com/services/oauth/access_token';
        $oauth_data['oauth_signature'] = $this->getSignature('POST', $access_token_url, $oauth_data);

        $curl = new Curl();
        $curl->post($access_token_url, $oauth_data);

        parse_str($curl->response, $parts);
        $_SESSION['oauth_access_token'] = $parts['oauth_token'];
        $_SESSION['oauth_access_token_secret'] = $parts['oauth_token_secret'];
        $_SESSION['user_id'] = $parts['user_nsid'];
        $_SESSION['authenticated'] = true;
    }
}
