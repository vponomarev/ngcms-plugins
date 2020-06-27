<?php

namespace Plugins;

// Базовые расширения PHP.
use Closure;

/**
 * Помощники для плагинов системы NGCMS.
 *
 * @version: 0.2.0 от 2020/02/22
 * @author: https://github.com/russsiq
 *
 * array_dotset - Установить значение элементу массива, используя «dot» нотацию.
 * cache - Получить данные из кэша, либо сохранить указанные данные в кэш.
 * cacheRemember - Получить данные из кэша, либо выполнить замыкание и сохранить результат в кэш.
 * catz - Получить категорию по идентификатору, либо массив всех категорий.
 * config - Получить значение конфигурации системы.
 * database - Получить текущее подключение к базе данных.
 * dd - Распечатать переменную, массив переменных или объект и прекратить выполнение скрипта.
 * pageInfo - Установить системную информацию о текущей странице.
 * request - Получить значение из глобального $_REQUEST.
 * setting - Получить настройку плагина по ключу, либо задать массив настроек.
 * trans - Получить перевод строки.
 * value - Возвращает значение по умолчанию для переданного значения.
 * view - Выводит шаблон с заданным контекстом и возвращает его в виде строки.
 */

if (! function_exists(__NAMESPACE__.'\array_dotset')) {
    /**
     * Установить значение элементу массива, используя «dot» нотацию.
     * @source Illuminate\Support\Arr
     * @param  array  $array
     * @param  string  $key
     * @param  mixed  $value
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

if (! function_exists(__NAMESPACE__.'\cache')) {
    /**
     * Получить данные из кэша, либо сохранить указанные данные в кэш.
     * @param  string  $plugin  Идентификатор плагина.
     * @param  string  $filename  Имя файла для сохранения / возврата данных из кэша.
     * @param  mixed  $data
     * @return mixed
     */
    function cache(string $plugin, string $filename, $data = null)
    {
        $cacheExpire = setting($plugin, 'cache') ? (int) setting($plugin, 'cacheExpire', 60) : 0;

        if (! $cacheExpire) {
            return is_null($data) ? false : $data;
        }

        if (is_null($data)) {
            return unserialize(cacheRetrieveFile($filename, $cacheExpire, $plugin));
        }

        return cacheStoreFile($filename, serialize($data), $plugin);
    }
}

if (! function_exists(__NAMESPACE__.'\cacheRemember')) {
    /**
     * Получить данные из кэша, либо выполнить замыкание и сохранить результат в кэш.
     * @param  string  $plugin  Идентификатор плагина.
     * @param  string  $filename  Имя файла для сохранения / возврата данных из кэша.
     * @param  Closure  $callback
     * @return mixed
     */
    function cacheRemember(string $plugin, string $filename, Closure $callback)
    {
        if (! $value = cache($plugin, $filename)) {
            cache($plugin, $filename, $value = $callback());
        }

        return $value;
    }
}

if (! function_exists(__NAMESPACE__.'\catz')) {
    /**
     * Получить категорию по идентификатору, либо массив всех категорий.
     * @param  integer  $id
     * @return array
     */
    function catz(int $id = null)
    {
        global $catz;

        if (is_null($id)) {
            return $catz;
        }

        foreach ($catz as $cat) {
    		if ($id == $cat['id']) {
    			return $cat;
    		}
    	}

    	return [];
    }
}

if (! function_exists(__NAMESPACE__.'\config')) {
    /**
     * Получить значение конфигурации системы.
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function config(string $key, $default = null)
    {
        global $config;

        return array_key_exists($key, $config) ? $config[$key] : value($default);
    }
}

if (! function_exists(__NAMESPACE__.'\database')) {
    /**
     * Получить текущее подключение к базе данных.
     * @return object
     */
    function database()
    {
        global $mysql;

        return $mysql;
    }
}

if (! function_exists(__NAMESPACE__.'\dd')) {
    /**
     * Распечатать переменную,
     * массив переменных или объект
     * и прекратить выполнение скрипта.
     * @param  mixed  $vars
     * @return object
     */
    function dd(...$vars)
    {
        $style = 'style="
            background-color: #23241f;
            border-radius: 3px;
            color: #f8f8f2;
            margin-bottom: 15px;
            overflow: visible;
            padding: 5px 10px;
            white-space: pre-wrap;
        "';

        foreach ($vars as $v) {
            if (is_array($v) or is_object($v)) {
        		$printable = print_r($v, true);
        	} else {
                $printable = var_export($v, true);
            }

            echo "<pre {$style}>{$printable}</pre><br>\n";
        }

        die(1);
    }
}

if (! function_exists(__NAMESPACE__.'\pageInfo')) {
    /**
     * Установить системную информацию о текущей странице.
     * @param  string  $section
     * @param  mixed  $info
     * @return void
     */
    function pageInfo(string $section, $info)
    {
        global $SYSTEM_FLAGS;

        array_dotset($SYSTEM_FLAGS, $section, $info);
    }
}

if (! function_exists(__NAMESPACE__.'\request')) {
    /**
     * Получить значение из глобального $_REQUEST.
     * @param  string  $key
     * @param  mixed  $default
     * @return string|array
     */
    function request(string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $_REQUEST;
        }

        return array_key_exists($key, $_REQUEST) ? $_REQUEST[$key] : value($default);
    }
}

if (! function_exists(__NAMESPACE__.'\setting')) {
    /**
     * Получить настройку плагина по ключу, либо задать массив настроек.
     * @param  string  $plugin  Идентификатор плагина.
     * @param  string|array  $variety  Имя ключа получаемой настройки, либо массив сохраняемых настроек.
     * @param  mixed  $default
     * @return mixed
     */
    function setting(string $plugin, $variety, $default = null)
    {
        global $PLUGINS;

        if (pluginsLoadConfig()) {
            // pluginGetVariable
            if (is_string($variety)) {
                return $PLUGINS['config'][$plugin][$variety] ?? $default;
            }

            // pluginSetVariable
            if (is_array($variety)) {
                foreach ($variety as $key => $value) {
                    $PLUGINS['config'][$plugin][$key] = $value;
                }

                return true;
            }
        }

        return false;
    }
}

if (! function_exists(__NAMESPACE__.'\starts_with')) {
    /**
     * Determine if a given string starts with a given substring.
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    function starts_with($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && substr($haystack, 0, strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists(__NAMESPACE__.'\trans')) {
   /**
    * Получить перевод строки.
    * @param  string  $key
    * @return string
    */
   function trans(string $key)
   {
       global $lang;

       return array_key_exists($key, $lang) ? $lang[$key] : $key;
   }
}

if (! function_exists(__NAMESPACE__.'\value')) {
    /**
     * Возвращает значение по умолчанию для переданного значения.
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (! function_exists(__NAMESPACE__.'\view')) {
    /**
     * Выводит шаблон с заданным контекстом и возвращает его в виде строки.
     * @param  string  $name  Имя шаблона.
     * @param  array  $context  Массив передаваемых параметров шаблону.
     * @return string
     */
    function view(string $name, array $context = [], array $mergeData = [])
    {
        global $twig;

        return $twig->render($name, array_merge($context, $mergeData));
    }
}
