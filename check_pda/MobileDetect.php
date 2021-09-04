<?php

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

require_once 'lib/Mobile_Detect.php';

class Twig_Extension_MobileDetect extends AbstractExtension
{
    protected $detector;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->detector = new Mobile_Detect();
    }

    /**
     * Twig functions
     *
     * @return array
     */
    public function getFunctions()
    {
        $functions = [
            new TwigFunction('get_available_devices', 'getAvailableDevices'),
            new TwigFunction('is_mobile', 'isMobile'),
            new TwigFunction('is_tablet', 'isTablet'),
        ];

        foreach ($this->getAvailableDevices() as $device => $fixedName) {
            $methodName = 'is'.$device;
            $twigFunctionName = 'is_'.$fixedName;
            $functions[] = new TwigFunction($twigFunctionName, 'is'.$methodName);
        }

        return $functions;
    }

    /**
     * Returns an array of all available devices
     *
     * @return array
     */
    public function getAvailableDevices()
    {
        $availableDevices = array();
        $rules = array_change_key_case($this->detector->getRules());

        foreach ($rules as $device => $rule) {
            $availableDevices[$device] = static::fromCamelCase($device);
        }

        return $availableDevices;
    }

    /**
     * Pass through calls of undefined methods to the mobile detect library
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->detector, $name), $arguments);
    }

    /**
     * Converts a string to camel case
     *
     * @param $string
     * @return mixed
     */
    protected static function toCamelCase($string)
    {
        return preg_replace('~\s+~', '', lcfirst(ucwords(strtr($string, '_', ' '))));
    }

    /**
     * Converts a string from camel case
     *
     * @param $string
     * @param string $separator
     * @return string
     */
    protected static function fromCamelCase($string, $separator = '_')
    {
        return strtolower(preg_replace('/(?!^)[[:upper:]]+/', $separator.'$0', $string));
    }

    /**
     * The extension name
     *
     * @return string
     */
    public function getName()
    {
        return 'mobile_detect.twig.extension';
    }
}
