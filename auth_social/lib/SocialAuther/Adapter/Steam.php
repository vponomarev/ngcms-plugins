<?php

namespace SocialAuther\Adapter;

class Steam extends AbstractAdapter
{

	
    public function __construct($config)
    {
        parent::__construct($config);
        

        $this->socialFieldsMap = array(
            'socialId'   => 'uid',
            'avatar'     => 'photo_big',
            'name' => 'screen_name',
            'socialPage' => 'link'
        );

        $this->provider = 'steam';
    }

    /**
     * Authenticate and return bool result of authentication
     *
     * @return bool
     */
    public function authenticate()
    {
        $result = false;
        
        $response = null;

		$params = array(
			'openid.assoc_handle' => $_GET['openid_assoc_handle'],
			'openid.signed'       => $_GET['openid_signed'],
			'openid.sig'          => $_GET['openid_sig'],
			'openid.ns'           => 'http://specs.openid.net/auth/2.0',
		);
		$signed = explode(',', $_GET['openid_signed']);
		foreach ($signed as $item) {
			$val = $_GET['openid_' . str_replace('.', '_', $item)];
			$params['openid.' . $item] = get_magic_quotes_gpc() ? stripslashes($val) : $val; 
		}
		$params['openid.mode'] = 'check_authentication';
		$data =  http_build_query($params);
		$context = stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => 
				"Accept-language: en\r\n".
				"Content-type: application/x-www-form-urlencoded\r\n" .
				"Content-Length: " . strlen($data) . "\r\n",
				'content' => $data,
				),
		));
		$result = file_get_contents('https://steamcommunity.com/openid/login', false, $context);
		preg_match("#^http://steamcommunity.com/openid/id/([0-9]{17,25})#", $_GET['openid_claimed_id'], $matches);
		$steamID64 = is_numeric($matches[1]) ? $matches[1] : 0;
		
		$params = array(
                'key' => $this->clientSecret,
                'steamids' => $steamID64
            );

		$json_object = $this->get('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/', $params);

		foreach ($json_object['response']['players'] as $player)
		{
			$this->userInfo['uid'] = $player['steamid'];
			$this->userInfo['photo_big'] = $player['avatarfull'];
			$this->userInfo['screen_name'] = $player['personaname'];
			$this->userInfo['link'] = $player['profileurl'];
		}
		
				
		//var_dump($this);
		
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
            'auth_url'    => 'https://steamcommunity.com/openid/login',
            'auth_params' => array(
				'openid.ns'         => 'http://specs.openid.net/auth/2.0',
				'openid.mode'       => 'checkid_setup',
				'openid.return_to'  => $this->redirectUri,
				'openid.realm'      => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'],
				'openid.ns.sreg'	=>	'http://openid.net/extensions/sreg/1.1',
				'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
				'openid.identity'   => 'http://specs.openid.net/auth/2.0/identifier_select',
            )
        );
    }
}
