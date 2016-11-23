<?php

namespace SocialAuther\Adapter;

class Twitter extends AbstractAdapter
{
    protected $urlRequestToken = 'https://api.twitter.com/oauth/request_token';
    protected $urlAuthenticate = 'https://api.twitter.com/oauth/authenticate';
    protected $urlAccessToken = 'https://api.twitter.com/oauth/access_token';
    protected $urlUserInfo = 'https://api.twitter.com/1.1/account/verify_credentials.json';

    private $token = '', $tokenSecret = '', $verifier;

    public function __construct($config)
    {
        parent::__construct($config);

        $this->socialFieldsMap = array(
            'socialId'   => 'id',
            'email'      => 'email',
            'name'       => 'name',
            'avatar'     => 'profile_image_url',
            'screenName' => 'screen_name'
        );

        $this->provider = 'twitter';

        $params = array(
            'oauth_callback'            => $this->redirectUri,
            'oauth_consumer_key'        => $this->clientId,
            'oauth_nonce'               => $this->_getNonce(),
            'oauth_signature'           => null,
            'oauth_signature_method'    => 'HMAC-SHA1',
            'oauth_timestamp'           => time(),
            'oauth_version'             => '1.0'
        );
        ksort($params);
        $params['oauth_signature'] = $this->_getSignature('GET', $this->urlRequestToken, $params);
        $params['oauth_callback'] = urlencode($this->redirectUri);

        parse_str($this->get($this->urlRequestToken, $params, false), $tokenInfo);
        
        if (count($tokenInfo) > 0 && isset($tokenInfo['oauth_token'])) {
            $this->token = $tokenInfo['oauth_token'];
            $this->tokenSecret = $tokenInfo['oauth_token_secret'];
        }
    }

    private function _getSignature($method, $uri, $params) {
        return urlencode(base64_encode(hash_hmac('sha1', strtoupper($method) . '&' . urlencode($uri) . '&' . urlencode(http_build_query(array_filter($params))),
                $this->clientSecret . '&' . $this->tokenSecret, true)));
    }

    private function _getNonce() {
        return md5(time() . rand());
    }

    /**
     * Get user name or null if it is not set
     *
     * @return string|null
     */
    public function getName()
    {
        $result = null;

        if (isset($this->userInfo['name'])) {
            $result = $this->userInfo['name'];
        }

        return $result;
    }

    /**
     * Get user social id or null if it is not set
     *
     * @return string|null
     */
    public function getSocialPage()
    {
        $result = null;

        if (isset($this->userInfo['screen_name'])) {
            $result = 'http://twitter.com/' . $this->userInfo['screen_name'];
        }

        return $result;
    }

    /**
     * Get url of user's avatar or null if it is not set
     *
     * @return string|null
     */
    public function getAvatar()
    {
        $result = null;

        if (isset($this->userInfo['profile_image_url'])) {
            $result = str_replace('_normal.', '.', $this->userInfo['profile_image_url']);
        }

        return $result;
    }

    /**
     * Authenticate and return bool result of authentication
     *
     * @return bool
     */
    public function authenticate()
    {
        $result = false;

        if (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) {

            $this->token = $_GET['oauth_token'];
            $this->verifier = $_GET['oauth_verifier'];

            $params = array(
                'oauth_consumer_key'        => $this->clientId,
                'oauth_nonce'               => $this->_getNonce(),
                'oauth_signature'           => null,
                'oauth_signature_method'    => 'HMAC-SHA1',
                'oauth_timestamp'           => time(),
                'oauth_token'               => $this->token,
                'oauth_verifier'            => $this->verifier,
                'oauth_version'             => '1.0'
            );
            ksort($params);
            $params['oauth_signature'] = $this->_getSignature('GET', $this->urlAccessToken, $params);

            parse_str($this->get($this->urlAccessToken, $params, false), $tokenInfo);

            if (count($tokenInfo) > 0 && isset($tokenInfo['oauth_token'])) {
                $this->token = $tokenInfo['oauth_token'];
                $this->tokenSecret = $tokenInfo['oauth_token_secret'];

                $params = array(
                    'oauth_consumer_key'        => $this->clientId,
                    'oauth_nonce'               => $this->_getNonce(),
                    'oauth_signature'           => null,
                    'oauth_signature_method'    => 'HMAC-SHA1',
                    'oauth_timestamp'           => time(),
                    'oauth_token'               => $this->token,
                    'oauth_verifier'            => $this->verifier,
                    'oauth_version'             => '1.0'
                );
                ksort($params);
                $params['oauth_signature'] = $this->_getSignature('GET', $this->urlUserInfo, $params);

                $userInfo = $this->get($this->urlUserInfo, $params);

                if (isset($userInfo['id'])) {
                    $this->userInfo = $userInfo;
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Prepare params for authentication url
     *
     * @return array
     */
    public function prepareAuthParams()
    {
        return array(
            'auth_url'    => $this->urlAuthenticate,
            'auth_params' => array(
                'oauth_token'   => $this->token
            )
        );
    }
}
