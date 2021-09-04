<?php
/**
 * SocialAuther (http://socialauther.stanislasgroup.com/)
 *
 * @author: Stanislav Protasevich
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace SocialAuther\Adapter;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * Social Client ID
     *
     * @var string null
     */
    protected $clientId = null;

    /**
     * Social Client Secret
     *
     * @var string null
     */
    protected $clientSecret = null;

    /**
     * Social Redirect Uri
     *
     * @var string null
     */
    protected $redirectUri = null;

    /**
     * Name of auth provider
     *
     * @var string null
     */
    protected $provider = null;

    /**
     * Social Fields Map for universal keys
     *
     * @var array
     */
    protected $socialFieldsMap = array();

    /**
     * Storage for user info
     *
     * @var array
     */
    protected $userInfo = null;

    /**
     * Constructor
     *
     * @param array $config
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($config)
    {
        if (!is_array($config))
            throw new Exception\InvalidArgumentException(
                __METHOD__ . ' expects an array with keys: `client_id`, `client_secret`, `redirect_uri`'
            );

        foreach (array('client_id', 'client_secret', 'redirect_uri') as $param) {
            if (!array_key_exists($param, $config)) {
                throw new Exception\InvalidArgumentException(
                    __METHOD__ . ' expects an array with key: `' . $param . '`'
                );
            } else {
                $property = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $param))));
                $this->$property = $config[$param];
            }
        }
    }

    /**
     * Get user social id or null if it is not set
     *
     * @return string|null
     */
    public function getSocialId()
    {
        $result = null;

        if (isset($this->userInfo[$this->socialFieldsMap['socialId']])) {
            $result = $this->userInfo[$this->socialFieldsMap['socialId']];
        }

        return $result;
    }

    /**
     * Get user email or null if it is not set
     *
     * @return string|null
     */
    public function getEmail()
    {
        $result = null;

        if (isset($this->userInfo[$this->socialFieldsMap['email']])) {
            $result = $this->userInfo[$this->socialFieldsMap['email']];
        }

        return $result;
    }

    /**
     * Get user name or null if it is not set
     *
     * @return string|null
     */
    public function getName()
    {
        $result = null;

        if (isset($this->userInfo[$this->socialFieldsMap['name']])) {
            $result = $this->userInfo[$this->socialFieldsMap['name']];
        }

        return $result;
    }

    /**
     * Get user social page url or null if it is not set
     * @return string|null
     */
    public function getSocialPage()
    {
        $result = null;

        if (isset($this->userInfo[$this->socialFieldsMap['socialPage']])) {
            $result = $this->userInfo[$this->socialFieldsMap['socialPage']];
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

        if (isset($this->userInfo[$this->socialFieldsMap['avatar']])) {
            $result = $this->userInfo[$this->socialFieldsMap['avatar']];
        }

        return $result;
    }

    /**
     * Get user sex or null if it is not set
     *
     * @return string|null
     */
    public function getSex()
    {
        $result = null;

        if (isset($this->userInfo[$this->socialFieldsMap['sex']])) {
            $result = $this->userInfo[$this->socialFieldsMap['sex']];
        }

        return $result;
    }

    /**
     * Get user birthday in format dd.mm.YYYY or null if it is not set
     *
     * @return string|null
     */
    public function getBirthday()
    {
        $result = null;

        if (isset($this->userInfo[$this->socialFieldsMap['birthday']])) {
            $result = date('d.m.Y', strtotime($this->userInfo[$this->socialFieldsMap['birthday']]));
        }

        return $result;
    }

    /**
     * Get user phone number
     *
     * @author Andrey Izman <cyborgcms@gmail.com>
     * @return string|null
     */
    public function getPhone()
    {
        return $this->getInfoVar('phone');
    }

    /**
     * Get user country name
     *
     * @author Andrey Izman <cyborgcms@gmail.com>
     * @return string|null
     */
    public function getCountry()
    {
        return $this->getInfoVar('country');
    }

    /**
     * Get user city name
     *
     * @author Andrey Izman <cyborgcms@gmail.com>
     * @return string|null
     */
    public function getCity()
    {
        return $this->getInfoVar('city');
    }

    /**
     * Get user scren name or null if it is not set
     *
     * @return string|null
     */
    public function getScreenName()
    {
        $result = null;
        if (isset($this->socialFieldsMap['screenName']) && isset($this->userInfo[$this->socialFieldsMap['screenName']])) {
            $result = $this->userInfo[$this->socialFieldsMap['screenName']];
        }
        return $result;
    }


    /**
     * Return name of auth provider
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Get authentication url
     *
     * @return string
     */
    public function getAuthUrl()
    {
        $config = $this->prepareAuthParams();
        
        //var_dump($config['auth_url'] . '?' . urldecode(http_build_query($config['auth_params'])));

        return $result = $config['auth_url'] . '?' . urldecode(http_build_query($config['auth_params']));
    }

    /**
     * Make post request and return result
     *
     * @param string $url
     * @param array $params
     * @param bool $parse
     * @return array|string
     */
    protected function post($url, $params, $parse = true)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        // boost vk with ipv4 http://habrahabr.ru/sandbox/56971/
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        // need cURL >= 7.10.8
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        curl_close($curl);

        if ($parse) {
            $result = json_decode($result, true);
        }

        return $result;
    }

    /**
     * Make get request and return result
     *
     * @param $url
     * @param $params
     * @param bool $parse
     * @return mixed
     */
    protected function get($url, $params, $parse = true)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url . '?' . urldecode(http_build_query($params)));
        // boost vk with ipv4 http://habrahabr.ru/sandbox/56971/
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        // need cURL >= 7.10.8
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        curl_close($curl);

        if ($parse) {
            $result = json_decode($result, true);
        }

        return $result;
    }
    
    
	public function getUserInfoRaw()
    {
        return $this->userInfo;
    }

    protected function getInfoVar($name)
    {
        $name = lcfirst($name);
        if (isset($this->socialFieldsMap[$name])) {
            $name = $this->socialFieldsMap[$name];
        }
        if (!isset($this->userInfo[$name])) {
            return null;
        }
        return $this->userInfo[$name];
    }
    
    
}
