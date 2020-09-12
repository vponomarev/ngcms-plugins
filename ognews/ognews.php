<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
class OGNEWSNewsFilter extends NewsFilter {
    function showNews($newsID, $SQLnews, $tvars,$mode = array()) {
        global $CurrentHandler, $config;
        if (($CurrentHandler['handlerName'] == 'news')||($CurrentHandler['handlerName'] == 'print')) {
            if($SQLnews['alt_name'] == $CurrentHandler['params']['altname']) {
                if(isset($mode)) {
                    $alink = checkLinkAvailable('uprofile', 'show')?
                        generateLink('uprofile', 'show', array('name' => $SQLnews['author'], 'id' => $SQLnews['author_id'])):
                        generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $SQLnews['author'], 'id' => $SQLnews['author_id']));
                    register_htmlvar('plain','<meta property="og:type" content="article">');
                    register_htmlvar('plain','<meta property="og:url" content="'.home.newsGenerateLink($SQLnews).'">');
                    register_htmlvar('plain','<meta property="og:site_name" content="'.secure_html($config["home_title"]).'">');
                    register_htmlvar('plain','<meta property="og:title" content="'.secure_html(substr(strip_tags($SQLnews["title"]), 0, 50)).'">');
                    register_htmlvar('plain','<meta property="og:description" content="'.secure_html(substr(strip_tags(stripBBCode($SQLnews['description'])), 0, 220)).'">');
                    /*
                    register_htmlvar('plain','<meta property="og:description" content="'.secure_html(substr(strip_tags(stripBBCode($SQLnews["content"])), 0, 220)).'">');
                    */
                    register_htmlvar('plain','<meta property="article:author" content="'.home.$alink.'">');
                    register_htmlvar('plain','<meta property="article:section" content="'.explode(',',strip_tags(@GetCategories($SQLnews['catid'])))[0].'">');
                    register_htmlvar('plain','<meta property="article:tag" content="'.secure_html($SQLnews['keywords']).'">');
                    if($tvars['vars']['news']['embed']['imgCount'] > 0) {
                        foreach($tvars['vars']['news']['embed']['images'] as $img_item) {
                            register_htmlvar('plain','<meta property="og:image" content="'.$img_item.'" />');
                        }
                        /*
                        register_htmlvar('plain','<meta property="og:image" content="'.$tvars['vars']['news']['embed']['images'][0].'" />');
                        */
                    }
                    if(!empty($SQLnews['#images'])) {
                        foreach($SQLnews['#images'] as $img_item) {
                            register_htmlvar('plain','<meta property="og:image" content="'.home.'/uploads/dsn/'.$img_item['folder'].'/'.$img_item['name'].'" />');
                        }
                    }
                }
            }
        }
        return 1;
    }
}
function stripBBCode($text_to_search) {
    $pattern = '|[[\/\!]*?[^\[\]]*?]|si';
    $replace = '';
    return preg_replace($pattern, $replace, $text_to_search);
}
register_filter('news','ognews', new OGNEWSNewsFilter);
