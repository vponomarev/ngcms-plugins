<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

class BBmediaNewsfilter extends NewsFilter {

	public function __construct() {

		$player_name = pluginGetVariable('bb_media', 'player_name');
		$player_handler = __DIR__ . '/players/' . $player_name . '/bb_media.php';
		if (file_exists($player_handler)) {
			include_once($player_handler);
		}
	}

    public function showNews($newsID, $SQLnews, &$tvars, $mode = []) {

		if (($t = bbMediaProcess($tvars['vars']['short-story'])) !== false) {
			$tvars['vars']['short-story'] = $t;
		}
		if (($t = bbMediaProcess($tvars['vars']['full-story'])) !== false) {
			$tvars['vars']['full-story'] = $t;
		}
		if (($t = bbMediaProcess($tvars['vars']['news']['short'])) !== false) {
			$tvars['vars']['news']['short'] = $t;
		}
		if (($t = bbMediaProcess($tvars['vars']['news']['full'])) !== false) {
			$tvars['vars']['news']['full'] = $t;
		}
	}
}

// Preload plugin tags
register_filter('news', 'bb_media', new BBmediaNewsFilter);

