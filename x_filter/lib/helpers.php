<?php

/**
 * Helpers for plugins of NGCMS.
 * Version 0.1.0: 2019/01/22
 *
 * __ - Translate the given message.
 * array_dotset - Set an array item to a given value using "dot" notation.
 * cache - Get / set the specified cache value.
 * cacheRemember - Get an item from the cache, or store the default value.
 * catz - Get a category or an array of all categories.
 * config - Get / set the specified configuration value of plugin.
 * database - Get a current connected database.
 * request - Get an input item from the global $_REQUEST.
 * value - Return the default value of the given value.
 * pageInfo - Set system info about the page.
 * view - Renders a template.
 */

namespace Plugins;

define('DS', DIRECTORY_SEPARATOR);

if (! function_exists('__')) {
    /**
     * Translate the given message.
     *
     * @param  string  $key
     * @return string
     */
    function __(string $key)
    {
        global $lang;

        return $lang[$key];
    }
}

if (! function_exists('array_dotset')) {
    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     */
    function array_dotset(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = preg_split('/\./', $key, -1, PREG_SPLIT_NO_EMPTY);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}

if (! function_exists('cache')) {
    /**
     * Get / set the specified cache value.
     * If an `int` as `$variety` is passed, we'll assume you want to retrieve from the cache.
     *
     * @param  string           $plugin     Id of plugin.
     * @param  string           $fname      File name to store/retrive.
     * @param  mixed            $data
     * @return mixed
     */
    function cache(string $plugin, string $fname, $data = null)
    {
        $cacheExpire = config($plugin, 'cache') ? (int) config($plugin, 'cacheExpire', 60) : 0;

        if (is_null($data) and !$cacheExpire ) {
            return false;
        }

        return is_null($data)
            ? unserialize(cacheRetrieveFile($fname, $cacheExpire, $plugin))
            : cacheStoreFile($fname, serialize($data), $plugin);
    }
}

if (! function_exists('cacheRemember')) {
    /**
     * Get an item from the cache, or store the default value.
     *
     * @param  string           $plugin     Id of plugin.
     * @param  string           $fname      File name to store/retrive.
     * @param  \Closure         $callback
     * @return mixed
     */
    function cacheRemember(string $plugin, string $fname, \Closure $callback)
    {
        if (! $value = cache($plugin, $fname)) {
            cache($plugin, $fname, $value = $callback());
        }

        return $value;
    }
}

if (! function_exists('catz')) {
    /**
     * Get a category or an array of all categories.
     *
     * @param  integer $id         Category id.
     * @return mixed               Category or an array of all categories.
     */
    function catz(int $id = null)
    {
        global $catz;

        return is_null($id) ? $catz : GetCategoryById($id);
    }
}

if (! function_exists('config')) {
    /**
     * Get / set the specified configuration value of plugin.
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  string           $plugin      Id of plugin.
     * @param  string|array     $variety
     * @param  mixed            $default
     * @return mixed
     */
    function config(string $plugin, $variety, $default = null)
    {
        if (is_string($variety)) {
            return pluginGetVariable($plugin, $variety) ?: $default;
        }

        // code

        // return pluginSetVariable($plugin, $variety, $value);
    }
}

if (! function_exists('database')) {
    /**
     * Get a current connected database.
     *
     * @return object
     */
    function database()
    {
        global $mysql;

        return $mysql;
    }
}

if (! function_exists('request')) {
    /**
     * Get an input item from the global $_REQUEST.
     *
     * @param  string           $key
     * @param  mixed            $default
     * @return string|array
     */
    function request(string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $_REQUEST;
        }

        return empty($_REQUEST[$key]) ? value($default) : $_REQUEST[$key];
    }
}

if (! function_exists('pageInfo')) {
    /**
     * Set system info about the page.
     *
     * @param  string           $section    System section.
     * @param  mixed            $info       Information.
     * @return void
     */
    function pageInfo(string $section, $info)
    {
        global $SYSTEM_FLAGS;

        array_dotset($SYSTEM_FLAGS, $section, $info);
    }
}

if (! function_exists('view')) {
    /**
     * Renders a template.
     *
     * @param  string  $name       The template name.
     * @param  array   $context    An array of parameters to pass to the template.
     * @return string              The rendered template.
     */
    function view(string $name, array $context = [], array $mergeData = [])
    {
        global $twig;

        return $twig->render($name, array_merge($context, $mergeData));
    }
}

if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}
