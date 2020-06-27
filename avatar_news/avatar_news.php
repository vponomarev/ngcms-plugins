<?php

if (!defined('NGCMS')) exit('HAL');

class ShowAvatar_newsNewsFilter extends NewsFilter {
	function showNews($newsID, $SQLnews, &$tvars, $mode = array()){
		global $mysql, $config, $userROW;

/*		
$use_cache = 1;
//Р С™Р С•Р Т‘Р С‘РЎР‚РЎС“Р С� Р Р† md5
$cacheFileName = md5('avatar_news'.$config['theme'].$config['default_lang']).'.txt';
		
// Р В§РЎвЂљР ВµР Р…Р С‘Р Вµ (Р Р† Р С—Р В°РЎР‚Р В°Р С�Р ВµРЎвЂљРЎР‚Р В°РЎвЂ¦ Р С—Р В»Р В°Р С–Р С‘Р Р… РЎвЂ¦РЎР‚Р В°Р Р…Р С‘РЎвЂљ: cache - РЎвЂћР В»Р В°Р С– РЎРѓР С•Р С•Р В±РЎвЂ°Р В°РЎР‹РЎвЂ°Р С‘Р в„– Р Р…Р В°Р Т‘Р С• Р В»Р С‘ Р С‘РЎРѓР С—Р С•Р В»РЎРЉР В·Р С•Р Р†Р В°РЎвЂљРЎРЉ Р С”Р ВµРЎв‚¬, cacheExpire - Р Р†РЎР‚Р ВµР С�РЎРЏ Р В¶Р С‘Р В·Р Р…Р С‘ Р С”Р ВµРЎв‚¬Р В° Р Р† РЎРѓР ВµР С”РЎС“Р Р…Р Т‘Р В°РЎвЂ¦
     if ($use_cache)    {
        $cacheData = cacheRetrieveFile($cacheFileName, 300, 'avatar_news');
        if ($cacheData != false){
            // We got data from cache. Return it and stop
            $template['vars']['avatar_in_news'] = $cacheData;
            return;
        }
    }
	
//pluginGetVariable('avatar_news','cache')
//pluginGetVariable('avatar_news','cacheExpire')
*/


		$result=$mysql->record("select id, avatar from ".uprefix."_users where id = ".$SQLnews['author_id']." limit 1");
			
	// Check for new style of avatars storing
	if ($result['avatar']) {
		$uavatar = $result['avatar'];
	}

	// GRAVATAR.COM integration ** BEGIN **
	if ($result['avatar'] != '') {
		$avatar	= avatars_url.'/'.$uavatar;
	} else {
		if ($config['avatars_gravatar']) {
			$avatar	= 'http://www.gravatar.com/avatar/'.md5(strtolower($userROW['mail'])).'.jpg?s='.$config['avatar_wh'].'&d='.urlencode(avatars_url."/noavatar.gif");
		} else {
			$avatar = avatars_url."/noavatar.gif";
		}
	}
	$tvars['vars']['avatar_in_news'] = $avatar;
	
/*
	if ($use_cache) {
    // Р вЂ”Р В°Р С—Р С‘РЎРѓРЎРЉ
    cacheStoreFile($cacheFileName, $avatar, 'avatar_news');
}
*/
		
	}
}

register_filter('news','avatar_news', new ShowAvatar_newsNewsFilter);