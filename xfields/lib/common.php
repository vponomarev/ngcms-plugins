<?php

// Global XF variables
global $XF;
global $XF_loaded;
$XF = [];        // $XF - array with configuration
$XF_loaded = 0;        // $XF_loaded - flag if config is loaded
// Load fields definition
function xf_configLoad()
{
    global $lang, $XF, $XF_loaded;
    if ($XF_loaded) {
        return $XF;
    }
    if (!($confdir = get_plugcfg_dir('xfields'))) {
        return false;
    }
    if (!file_exists($confdir.'/config.php')) {
        $XF_loaded = 1;

        return ['news' => []];
    }
    include $confdir.'/config.php';
    $XF_loaded = 1;
    $XF = is_array($xarray) ? $xarray : ['news' => []];

    return $XF;
}

// Save fields definition
function xf_configSave($xf = null)
{
    global $lang, $XF, $XF_loaded;
    if (!$XF_loaded) {
        return false;
    }
    if (!($confdir = get_plugcfg_dir('xfields'))) {
        return false;
    }
    // Open config
    if (!($fn = fopen($confdir.'/config.php', 'w'))) {
        return false;
    }
    // Write config
    fwrite($fn, "<?php\n\$xarray = ".var_export(is_array($xf) ? $xf : $XF, true).";\n");
    fclose($fn);

    return true;
}

/**
 * Декодирование поля из текстовой строки.
 * @param  string|null  $text
 * @return array
 */
function xf_decode(string $text = null): array
{
    // Если строка пустая, то и массив возвращаем пустым.
    if (empty($text)) {
        return [];
    }

    // Если предоставленая строка является псевдо-серилизованной строкой.
    if (mb_substr($text, 0, 4) == "SER|") {
        // Обрезаем маркер серилизованной строки.
        $serialized = mb_substr($text, 4);

        // Пытаемся десериализовать строку.
        $xfields = unserialize($serialized);

        // Если успешно десериализовали, то возвращаем.
        if (is_array($xfields)) {
            return $xfields;
        }

        // Если не получилось конвертировать, то пытаемся изменить кодировку.
        $converted = mb_convert_encoding($serialized, 'CP1251');

        // Пытаемся десериализовать строку.
        $xfields = unserialize($converted);

        // Если успешно десериализовали, то проблема была в кодировке.
        if (is_array($xfields)) {
            return array_map(function ($xfield) {
                // Обратно конвертируем и возвращаем каждое поле.
                return mb_convert_encoding($xfield, 'UTF-8', 'CP1251');
            }, $xfields);
        }

        // Если ничего не помогло, возвращаем пустой массив.
        return [];
    }
    // OLD METHOD. OBSOLETE but supported for reading
    $xfieldsdata = explode('||', $text);
    foreach ($xfieldsdata as $xfielddata) {
        list($xfielddataname, $xfielddatavalue) = explode('|', $xfielddata);
        $xfielddataname = str_replace('&#124;', '|', $xfielddataname);
        $xfielddataname = str_replace('__NEWL__', "\r\n", $xfielddataname);
        $xfielddatavalue = str_replace('&#124;', '|', $xfielddatavalue);
        $xfielddatavalue = str_replace('__NEWL__', "\r\n", $xfielddatavalue);
        $data[$xfielddataname] = $xfielddatavalue;
    }

    return $data;
}

// Encode fields into text
function xf_encode($fields)
{
    if (!is_array($fields)) {
        return '';
    }

    return 'SER|'.serialize($fields);
}

function xf_getTableBySectionID($sectionID)
{
    switch ($sectionID) {
        case 'news':
            return prefix.'_news';
        case 'users':
            return prefix.'_users';
        case 'tdata':
            return prefix.'_xfields';
    }

    return false;
}

//
// Class for managing xfields data processing
class XFieldsFilter
{
    //
    public function showTableEntry($newsID, $SQLnews, $rowData, &$rowVars)
    {
    }
}
