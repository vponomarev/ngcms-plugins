<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// Load LIBRARY
loadPluginLibrary('comments', 'lib');

// Filter class
class clFilterComments extends FilterComments {
	function addComments($userRec, $newsRec, &$tvars, &$SQL) {
		global $lang;

		// Manage filtering
		$flagUpdated = false;
		$c = $SQL['text'];

		foreach (explode("\n",extra_get_param('filter','replace')) as $line) {
			list($rsrc, $rdest) = explode('|',$line);
			if ($rsrc && $rdest) {
				$c = str_replace($rsrc, $rdest, $c);
				$flagUpdated = true;
			}
		}

		// Manage blocking
		foreach (explode("\n",extra_get_param('filter','block')) as $line) {
			if ($line && strpos(' '.$c,trim($line))) {
				loadPluginLang('filter', 'filter', '', '', ':');
				msg(array("type" => "error", "text" => str_replace('%lock%', trim($line), $lang['filter:block'])));
				return 0;
			}
		}

		if ($flagUpdated)
			$SQL['text'] = $c;

		return 1;
	}
}

// Activate interceptor
register_filter('comments','filter', new clFilterComments);

