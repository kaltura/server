<?php

/**
 * Provides access to the Dailymotion API.
 *
 * @author Olivier Poitrey <rs@dailymotion.com>
 */
class Dailymotion
{
    const VERSION = '1.2';

    /**
     * An authorization is requested to the end-user by redirecting it to an authorization page hosted
     * on Dailymotion. Once authorized, a refresh token is requested by the API client to the token
     * server and stored in the end-user's cookie (or other storage technique implemented by subclasses).
     * The refresh token is then used to request time limited access token to the token server.
     */
    const GRANT_TYPE_AUTHORIZATION = 1;
    const GRANT_TYPE_TOKEN = 1; // deprecated name

    /**
     * This grant type is a 2 legs authentication: it doesn't allow to act on behalf of another user.
     * With this grant type, all API requests will be performed with the user identity of the API key owner.
     */
    const GRANT_TYPE_CLIENT_CREDENTIALS = 2;
    const GRANT_TYPE_NONE = 2; // Backward compat
    /**
     * This grant type allows to authenticate end-user by directly providing its credentials.
     * This profile is highly discouraged for web-server workflows. If used, the username and password
     * MUST NOT be stored by the client.
     */
    const GRANT_TYPE_PASSWORD = 3;

    /**
     * Activate debug output
     */
    public $debug = true;

    /**
     * Maximum number of secondes allowed for each HTTP requests
     */
    public $timeout = 10;

    /**
     * Maximum number of secondes allowed to wait for connection establishment of HTTP requests
     */
    public $connectionTimeout = 15;

    /**
     * An HTTP proxy to tunnel requests through (format: hostname[:port])
     */
    public $proxy = null;

    /**
     * The API enpoint URL
     */
    public $apiEndpointUrl = 'https://api.dailymotion.com/json';

    /**
     * The OAuth authorization server endpoint URL
     */
    public $oauthAuthorizeEndpointUrl = 'https://api.dailymotion.com/oauth/authorize';

    /**
     * The OAuth token server enpoind URL
     */
    public $oauthTokenEndpointUrl = 'https://api.dailymotion.com/oauth/token';

    /**
     * Domain of the cookie used to store the session
     */
    public $cookieDomain = '';

    /**
     * Life time of the cookie used to store the session
     */
    public $cookieLifeTime = 31536000; // 1 year

    protected
        $grantType = null,
        $grantInfo = null,
        $session = null,
        $storeSession = true;

    /**
     * Change the default grant type.
     *
     * To create an API key/secret pair, go to: http://www.dailymotion.com/profile/developer
     *
     * @param $type Integer can be one of Dailymotion::GRANT_TYPE_AUTHORIZATION, Dailymotion::GRANT_TYPE_CLIENT_CREDENTIALS
     *                      or Dailymotion::GRANT_TYPE_PASSWORD.
     * @param $apiKey the API key
     * @param $apiSecret the API secret
     * @param $scope mixed the permission scope requested (can be none or any of 'read', 'write', 'delete').
     *                     To requested several scope keys, use an array or separate keys by whitespaces.
     * @param $info Array info associated to the chosen grant type
     *
     * Info Keys:
     * - redirect_uri: if $type is Dailymotion::GRANT_TYPE_AUTHORIZATION, this key can be provided. If omited,
     *                 the current URL will be used. Make sure this value have to stay the same before
     *                 the user is redirect to the authorization page and after the authorization page
     *                 redirected to this provided URI (the token server will change this).
     * - username:
     * - password: if $type is Dailymotion::GRANT_TYPE_PASSWORD, are used to define end-user credentials.
     *             If those argument as not provided, the DailymotionAuthRequiredException exception will
     *             be thrown if no valid session is available.
     *
     * @throws InvalidArgumentException if grant type is not supported or grant info is missing with required
     */
    public function setGrantType($type, $apiKey, $apiSecret, Array $scope = null, Array $info = null)
    {
        if ($type === null)
        {
            $this->grantType = null;
            $this->grantInfo = null;
            return;
        }

        switch ($type)
        {
            case self::GRANT_TYPE_AUTHORIZATION:
                if (!isset($info['redirect_uri']))
                {
                    $info['redirect_uri'] = $this->getCurrentUrl();
                }
                break;
            case self::GRANT_TYPE_CLIENT_CREDENTIALS:
            case self::GRANT_TYPE_PASSWORD:
                break;
            default:
                throw new InvalidArgumentException('Invalid grant type: ' . $type);
        }

        if (!isset($info))
        {
            $info = array();
        }

        if (!isset($apiKey) || !isset($apiSecret))
        {
            throw new InvalidArgumentException('Missing API key/secret');
        }

        $this->grantType = $type;
        if (isset($scope))
        {
            $info['scope'] = is_array($scope) ? implode(' ', $scope) : $scope;
        }
        $info['key'] = $apiKey;
        $info['secret'] = $apiSecret;
        $this->grantInfo = $info;
    }

    /**
     * Get an authorization URL for use with redirects. By default, full page redirect is assumed.
     * If you are using a generated URL with a window.open() call in Javascript, you can pass in display=popup.
     *
     * @param $scope Array a list of requested scope (allowed: create, read, update, delete)
     * @param $display String can be "page" (default, full page), "popup" or "mobile"
     */
    public function getAuthorizationUrl($redirectUri = null, $scope = array(), $display = 'page')
    {
        if ($this->grantType !== self::GRANT_TYPE_AUTHORIZATION)
        {
            throw new RuntimeException('This method can only be used with TOKEN grant type.');
        }

        return $this->oauthAuthorizeEndpointUrl . '?' . http_build_query(array
        (
            'response_type' => 'code',
            'client_id' => $this->grantInfo['key'],
            'redirect_uri' => $this->grantInfo['redirect_uri'],
            'scope' => is_array($scope) ? implode(' ', $scope) : $scope,
            'display' => $display,
        ), null, '&');
    }

    /**
     * Upload a file on the Dailymotion servers and generate an URL to be used with API methods.
     *
     * @param $filePath String a path to the file to upload
     *
     * @return String the resulting URL
     */
    public function uploadFile($filePath)
    {
		error_log("file to load: " . $filePath);
        $result = $this->call('file.upload');
        $timeout = $this->timeout;
        $this->timeout = null;
		$response = $this->httpRequest($result['upload_url'], array('file' => '@' . $filePath));
		error_log("dailmotion: " . $response);
        $result = json_decode($response, true);
        $this->timeout = $timeout;
        return $result['url'];
    }

    /**
     * Call a remote method.
     *
     * @param $method String the method name to call.
     * @param $args Array an associative array of arguments.
     *
     * @return mixed the method response
     *
     * @throws DailymotionApiException if API return an error
     * @throws DailymotionAuthException if can't authenticate the request
     * @throws DailymotionAuthRequiredException if not authentication info is available
     * @throws DailymotionTransportException if an error occurs during request.
     */
    public function call($method, $args = array())
    {
        $headers = array('Content-Type: application/json');
        $payload = json_encode(array
        (
            'call' => $method,
            'args' => $args,
        ));

        $status_code = null;
        try
        {
            $result = json_decode($this->oauthRequest($this->apiEndpointUrl, $payload, $this->getAccessToken(), $headers, $status_code), true);
        }
        catch (DailymotionAuthException $e)
        {
            if ($e->error === 'invalid_token')
            {
                // Retry by forcing the refresh of the access token
                $result = json_decode($this->oauthRequest($this->apiEndpointUrl, $payload, $this->getAccessToken(true), $headers, $status_code), true);
            }
            else
            {
                throw $e;
            }
        }

        if (!isset($result))
        {
            throw new DailymotionApiException('Invalid API server response.');
        }
        elseif ($status_code !== 200)
        {
            throw new DailymotionApiException('Unknown error: ' . $status_code, $status_code);
        }
        elseif (is_array($result) && isset($result['error']))
        {
            $message = isset($result['error']['message']) ? $result['error']['message'] : null;
            $code = isset($result['error']['code']) ? $result['error']['code'] : null;
            if ($code === 403)
            {
                throw new DailymotionAuthRequiredException($message, $code);
            }
            else
            {
                throw new DailymotionApiException($message, $code);
            }
        }
        elseif (!isset($result['result']))
        {
            throw new DailymotionApiException("Invalid API server response: no `result' key found.");
        }

        return $result['result'];
    }

    /**
     * Remove the right for the current API key to access the current user account.
     */
    public function logout()
    {
        $this->call('auth.logout');
        $this->setSession(null);
    }

    /**
     * Get the access token. If not access token is available, try to obtain one using refresh token
     * or code (depending on the state of the OAuth transaction). If no access token is available
     * and no refresh token or code can be found, an exception is thrown.
     *
     * @param Boolean $forceRefresh to force the refresh of the access token, event if not expired
     *
     * @return String access token or NULL if not grant type defined (un-authen API calls)
     *
     * @throws DailymotionAuthRequiredException can't get access token, client need to request end-user authorization
     * @throws DailymotionAuthRefusedException the user refused the authorization
     * @throws DailymotionAuthException an oauth error occurred
     */
    protected function getAccessToken($forceRefresh = false)
    {
        if ($this->grantType === null)
        {
            // No grant type defined, the request won't be authenticated
            return null;
        }

        $session = $this->getSession();

        // Check if session is present and if it was created for the same grant type
        // i.e: if the grant type to create the session was AUTHORIZATION and the current
        //      grant type is CLIENT_CREDENTIALS, we don't want to call method on the behalf another user
        if (isset($session) && isset($session['grant_type']) && $session['grant_type'] === $this->grantType)
        {
            if (isset($session['access_token']) && !$forceRefresh)
            {
                if (!isset($session['expires']) || time() < $session['expires'])
                {
                    return $session['access_token'];
                }
                // else: Token expired
            }

            // No valid access token found, try to refresh it
            if (isset($session['refresh_token']))
            {
                $origGrantType = $session['grant_type'];
                $session = $this->oauthTokenRequest(array
                (
                    'grant_type' => 'refresh_token',
                    'client_id' => $this->grantInfo['key'],
                    'client_secret' => $this->grantInfo['secret'],
                    'scope' => isset($this->grantInfo['scope']) ? $this->grantInfo['scope'] : null,
                    'refresh_token' => $session['refresh_token'],
                ));
                $session['grant_type'] = $origGrantType;
                $this->setSession($session);
                return $session['access_token'];
            }
        }

        try
        {
            if ($this->grantType === self::GRANT_TYPE_AUTHORIZATION)
            {
                if (isset($_GET['code']))
                {
                    // We've been called back by authorization server
                    $session = $this->oauthTokenRequest(array
                    (
                        'grant_type' => 'authorization_code',
                        'client_id' => $this->grantInfo['key'],
                        'client_secret' => $this->grantInfo['secret'],
                        'scope' => isset($this->grantInfo['scope']) ? $this->grantInfo['scope'] : null,
                        'code' => $_GET['code'],
                        'redirect_uri' => $this->grantInfo['redirect_uri'],
                    ));
                    $session['grant_type'] = $this->grantType;
                    $this->setSession($session);
                    return $session['access_token'];
                }
                elseif (isset($_GET['error']))
                {
                    $message = isset($_GET['error_description']) ? $_GET['error_description'] : null;
                    if ($_GET['error'] === 'access_denied')
                    {
                        $e = new DailymotionAuthRefusedException($message);
                    }
                    else
                    {
                        $e = new DailymotionAuthException($message);
                    }
                    $e->error = $_GET['error'];
                    throw $e;
                }
                else
                {
                    // Ask the client to request end-user authorization
                    throw new DailymotionAuthRequiredException();
                }
            }
            elseif ($this->grantType === self::GRANT_TYPE_CLIENT_CREDENTIALS)
            {
                $session = $this->oauthTokenRequest(array
                (
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->grantInfo['key'],
                    'client_secret' => $this->grantInfo['secret'],
                    'scope' => isset($this->grantInfo['scope']) ? $this->grantInfo['scope'] : null,
                ));
                $session['grant_type'] = $this->grantType;
                $this->setSession($session);
                return $session['access_token'];
            }
            elseif ($this->grantType === self::GRANT_TYPE_PASSWORD)
            {
                if (!isset($this->grantInfo['username']) || !isset($this->grantInfo['password']))
                {
                    // Ask the client to request end-user credentials
                    throw new DailymotionAuthRequiredException();
                }
                $session = $this->oauthTokenRequest(array
                (
                    'grant_type' => 'password',
                    'client_id' => $this->grantInfo['key'],
                    'client_secret' => $this->grantInfo['secret'],
                    'scope' => isset($this->grantInfo['scope']) ? $this->grantInfo['scope'] : null,
                    'username' => $this->grantInfo['username'],
                    'password' => $this->grantInfo['password'],
                ));
                $session['grant_type'] = $this->grantType;
                $this->setSession($session);
                return $session['access_token'];
            }
        }
        catch (DailymotionAuthException $e)
        {
            // clear session on error
            $this->setSession(null);
            throw $e;
        }
    }

    /**
     * Set the session and store it if storeSession is true.
     *
     * @param $session Array the session to set
     */
    protected function setSession(Array $session = null)
    {
        $this->session = $session;
        if ($this->storeSession)
        {
            $this->storeSession($session);
        }
    }

    /**
     * Get the session if any.
     *
     * @return Array the current session or null
     */
    protected function getSession()
    {
        if (!isset($this->session))
        {
            $this->session = $this->readSession();
        }

        return $this->session;
    }

    /**
     * Read the session from the session store. Default storage is Cookie, subclass can implement another
     * storage type if needed. Info stored in the session are useless without api the secret. Storing
     * those info on the client should thus be safe unless the API secret is kept... secret.
     *
     * @return Array the stored session or null if none found
     */
    protected function readSession()
    {
        $cookieName = 'dms_' . $this->grantInfo['key'];
        if (isset($_COOKIE[$cookieName]))
        {
            parse_str(trim(get_magic_quotes_gpc() ? stripslashes($_COOKIE[$cookieName]): $_COOKIE[$cookieName], '"'), $session);
            return $session;
        }
    }

    /**
     * Store the given session to the session store. Default storage is Cookie, subclass can implement another
     * storage type if needed. Info stored in the session are useless without api the secret. Storing
     * those info on the client should thus be safe unless the API secret is kept... secret.
     *
     * @param $session Array the session to store, if null passed, the session is removed from the session store
     */
    protected function storeSession(Array $session = null)
    {
        if ($session['grant_type'] != self::GRANT_TYPE_CLIENT_CREDENTIALS)
        {
            // Do not store session for grant type client_credentials as it would allow the end-user to perform
            // API calls on behalf of the API key user.
            return;
        }

        if (headers_sent())
        {
            if (php_sapi_name() !== 'cli')
            {
                error_log('Could not set session in cookie: headers already sent.');
            }
            return;
        }

        $cookieName = 'dms_' . $this->grantInfo['key'];
        if (isset($session))
        {
            $value = '"' . http_build_query($session, null, '&') . '"';
            $expires = time() + $this->cookieLifeTime;
        }
        else
        {
            if (!isset($_COOKIE[$cookieName]))
            {
                // No need to remove an unexisting cookie
                return;
            }
            $value = 'deleted';
            $expires = time() - 3600;
        }

        setcookie($cookieName, $value, $expires, '/', $this->cookieDomain);
    }

    /**
     * Perform a request to a token server complient with the OAuth 2.0 (draft 10) specification.
     *
     * @param $args Array arguments to send to the token server
     *
     * @return Array a configured session
     *
     * @throws DailymotionAuthException in case of token server error or invalid response
     */
    protected function oauthTokenRequest(Array $args)
    {
        $result = json_decode($response = $this->httpRequest($this->oauthTokenEndpointUrl, $args, null, $status_code, $response_headers), true);

        if (!isset($result))
        {
            throw new DailymotionAuthException("Invalid token server response: $response.");
        }
        elseif (isset($result['error']))
        {
            $message = isset($result['error_description']) ? $result['error_description'] : null;
            $e = new DailymotionAuthException($message);
            $e->error = $result['error'];
            throw $e;
        }
        elseif (isset($result['access_token']))
        {
            return array
            (
                'access_token' => $result['access_token'],
                'expires' => time() + $result['expires_in'],
                'refresh_token' => isset($result['refresh_token']) ? $result['refresh_token'] : null,
                'scope' => isset($result['scope']) ? explode(' ', $result['scope']) : array(),
            );
        }
        else
        {
            throw new DailymotionAuthException('No access token found in the token server response.');
        }
    }

    /**
     * Perform an OAuth 2.0 (draft 10) authenticated request.
     *
     * @param String $url the URL to perform the HTTP request to.
     * @param String $payload the encoded method request to POST.
     * @param String oauth access token to authenticate the request with.
     * @param Array list of headers to send with the request (format array('Header-Name: header value')).
     * @param Integer $status_code an reference variable to store the response status code in.
     * @param Array a reference variable to store the response headers.
     *
     * @return String the response body
     *
     * @throws DailymotionAuthException if an oauth error occurs
     * @throws DailymotionTransportException if an error occurs during request.
     */
    protected function oauthRequest($url, $payload, $accessToken = null, $headers = array(), &$status_code = null, &$response_headers = null)
    {
        if ($accessToken !== null)
        {
            $headers[] = 'Authorization: OAuth2 ' . $accessToken;
        }
        $result = $this->httpRequest($url, $payload, $headers, $status_code, $response_headers);

        switch ($status_code)
        {
            case 401: // Invalid or expired token
            case 400: // Invalid request
            case 403: // Insufficient scope
                $error = null;
                $message = null;
                if (preg_match('/error="(.*?)"(?:, error_description="(.*?)")?/', $response_headers['www-authenticate'], $match))
                {
                    $error = $match[1];
                    $message = $match[2];
                }

                $e = new DailymotionAuthException($message);
                $e->error = $error;
                throw $e;
        }

        return $result;
    }

    /**
     * Perform an HTTP request by posting the given payload and returning the result.
     * Override this method if you don't want to use curl.
     *
     * @param String $url the URL to perform the HTTP request to.
     * @param mixed $payload the data to POST. If it's an associative array, it will be urlencoded and the
     *              Content-Type header will automatically set to multipart/form-data. If it's a string
     *              make sure you set the correct Content-Type.
     * @param Array $headers list of headers to send with the request (format array('Header-Name: header value'))
     * @param Integer $status_code an reference variable to store the response status code in
     * @param Array a reference variable to store the response headers
     *
     * @return String the response body
     *
     * @throws DailymotionTransportException if an error occurs during request.
     */
    protected function httpRequest($url, $payload, $headers = null, &$status_code = null, &$response_headers = null)
    {
        $ch = curl_init();

        // Force removal of the Exept: 100-continue header automatically added by curl
        $headers[] = 'Expect:';

        curl_setopt_array
        (
            $ch, array
            (
                CURLOPT_CONNECTTIMEOUT => $this->connectionTimeout,
                CURLOPT_TIMEOUT => $this->timeout,
                CURLOPT_PROXY => $this->proxy,
                CURLOPT_USERAGENT => sprintf('Dailymotion-PHP/%s (PHP %s; %s)', self::VERSION, PHP_VERSION, php_sapi_name()),
                CURLOPT_HEADER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_VERBOSE => $this->debug,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => 2,
            )
        );

        $response = curl_exec($ch);
        if ($response === false)
        {
            $e = new DailymotionTransportException(curl_error($ch), curl_errno($ch));
            curl_close($ch);
            throw $e;
        }

        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $info = curl_getinfo($ch);
        curl_close($ch);

        $headers = array();
        $headers_str = substr($response, 0, $info['header_size']);
        strtok($headers_str, "\r\n"); // skip status code
        while(($name = trim(strtok(":"))) && ($value = trim(strtok("\r\n"))))
        {
            $headers[strtolower($name)] = (isset($headers[$name]) ? $headers[$name] . '; ' : '') . $value;
        }
        $response_headers = $headers;

        if ($this->debug)
        {
            error_log(substr($response, $info['header_size']));
        }

        return substr($response, $info['header_size']);
    }

    /**
     * Returns the current URL, stripping if of known OAuth parameters that should not persist.
     *
     * @return String the current URL
     */
    protected function getCurrentUrl()
    {
        $secure = false;
        if (isset($_SERVER['HTTPS']))
        {
            $secure = strtolower($_SERVER['HTTPS']) === 'on' || $_SERVER['HTTPS'] == 1;
        }
        elseif (isset($_SERVER['HTTP_SSL_HTTPS']))
        {
            $secure = strtolower($_SERVER['HTTP_SSL_HTTPS']) === 'on' || $_SERVER['HTTP_SSL_HTTPS'] == 1;
        }
        elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']))
        {
            $secure = strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https';
        }
        $scheme = $secure ? 'https://' : 'http://';
        $currentUrl = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $parts = parse_url($currentUrl);

        // Remove oauth callback params
        $query = '';
        if ($parts['query'] !== '')
        {
            parse_str($parts['query'], $params);
            foreach(array('code', 'scope', 'error', 'error_description', 'error_uri', 'state') as $name)
            {
                unset($params[$name]);
            }
            if (count($params) > 0)
            {
                $query = '?' . http_build_query($params, null, '&');
            }
        }

        // Use port if non default
        $port = isset($parts['port']) && ($secure ? $parts['port'] !== 80 : $parts['port'] !== 443) ? ':' . $parts['port'] : '';

        // rebuild
        return $scheme . $parts['host'] . $port . $parts['path'] . $query;
    }
}

class DailymotionApiException extends Exception {}
class DailymotionTransportException extends DailymotionApiException {}
class DailymotionAuthException extends DailymotionApiException {public $error = null;}
class DailymotionAuthRequiredException extends DailymotionAuthException {}
class DailymotionAuthRefusedException extends DailymotionAuthException {}
