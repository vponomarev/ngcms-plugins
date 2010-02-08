<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

class CatDescriptionNewsFilter extends NewsFilter {

	function showNewsPre($newsID, &$SQLnews, $mode = array()) 
	{ 
		global $template, $mysql, $CurrentHandler, $catmap, $parse, $config; 
		if ($mode['nCount'] == 1)
		{
			$catid = false;
			if ($CurrentHandler['pluginName'] == 'news')
			{
				switch ($CurrentHandler['handlerName'])
				{
					case 'main':
						$catid = 0;
						break;
					case 'by.category':
						if (isset($CurrentHandler['params']['category']))
							$catid = array_search($CurrentHandler['params']['category'], $catmap);
						else if (isset($CurrentHandler['params']['catid']))
							$catid = $CurrentHandler['params']['catid'];
						break;
				}
			}
			if ($catid === false || $catid === null)
				return 1;
			
			$cacheFileName = md5('cat_description'.$catid.$config['home_url'].$config['theme'].$config['default_lang']).'.txt';
			$cacheData = cacheRetrieveFile($cacheFileName, 3600, 'cat_description');
			if ($cacheData != false) {
				$template['vars']['mainblock'] .= $cacheData;
				return 1;
			}

			$description = '';

			foreach ($mysql->select('select `description` from '.prefix.'_cat_description where `catid`='.db_squote($catid).' and `is_on`=\'1\' limit 1') as $row)
			{
				$description = $row['description'];
				$description = $parse->htmlformatter($description);
				$description = $parse->bbcodes($description);
				$description = $parse->smilies($description);
			}
			$template['vars']['mainblock'] .= $description;
			cacheStoreFile($cacheFileName, $description, 'cat_description');
		}
		return 1; 
	}

}

register_filter('news','cat_description', new CatDescriptionNewsFilter);