<?php

/**
 * Suggest helper for Add/Edit news.
 */
rpcRegisterFunction('plugin.tags.suggest', function (string $params): array {
    global $userROW, $mysql;

    // Only registered users can use suggest
    if (!is_array($userROW)) {
        return [
            'status' => 0,
            'errorCode' => 1,
            'errorText' => 'Permission denied',

        ];
    }

    // Check if suggest module is enabled
    if (pluginGetVariable('tags', 'suggestHelper')) {
        return [
            'status' => 0,
            'errorCode' => 2,
            'errorText' => 'Suggest helper is not enabled',

        ];
    }

    $output = [];

    // Check if tag is specified
    if ($params) {
        $searchTag = db_squote($params.'%');

        $sql = "select * from ".prefix."_tags where tag like ".$searchTag." order by posts desc limit 20";

        foreach ($mysql->select($sql) as $row) {
            $output[] = [
                $row['tag'],
                $row['posts'].' постов'
            ];
        }
    }

    return [
        'status' => 1,
        'errorCode' => 0,
        'data' => [
            $params,
            $output,

        ],

    ];
});
