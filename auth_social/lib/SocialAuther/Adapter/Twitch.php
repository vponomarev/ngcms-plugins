<?php

namespace SocialAuther\Adapter;

class Twitch extends AbstractAdapter
{

    public function __construct($config)
    {
        parent::__construct($config);
        

        $this->socialFieldsMap = array(
            'socialId'   => 'uid',
            'avatar'     => 'photo_big',
            'name' => 'screen_name',
            'email'      => 'email',
            'socialPage' => 'link'
        );

        $this->provider = 'twitch';
    }

    /**
     * Authenticate and return bool result of authentication
     *
     * @return bool
     */
    public function authenticate()
    {
        $result = false;

		$params = array(
			'client_id' => $this->clientId,
			'client_secret' => $this->clientSecret,
			'grant_type' => 'authorization_code',
			'redirect_uri' => $this->redirectUri,
			'code' => $_GET['code']
		);

		$token = $this->post('https://api.twitch.tv/kraken/oauth2/token/', $params);

		$params = array(
			'oauth_token' => $token['access_token']
		);

		$user = $this->get('https://api.twitch.tv/kraken/user', $params);
		
		$params = NULL;
		$user_info = $this->get('https://api.twitch.tv/kraken/users/'.$user['name'], $params);
		//var_dump($user_info);
		
		$this->userInfo['uid'] = $user['name'];
		$this->userInfo['photo_big'] = $user_info['logo']?$user_info['logo']:'';
		$this->userInfo['screen_name'] = $user['display_name'];
		$this->userInfo['email'] = $user['email'];
		$this->userInfo['link'] = "http://twitch.tv/".$user['name']."/profile";
		//var_dump($this->userInfo);
       
		
		$result = true;

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
            'auth_url'    => 'https://api.twitch.tv/kraken/oauth2/authorize',
            'auth_params' => array(
				'response_type'         => 'code',
				'client_id'       => $this->clientId,
				'scope'      => 'user_read',
				'redirect_uri'  => $this->redirectUri,
				
            )
        );
    }
}
