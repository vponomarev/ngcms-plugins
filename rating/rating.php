<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
function plugin_rating_update() {

	global $mysql, $tpl, $userROW;
	LoadPluginLang('rating', 'site');
	// Security protection - limit rating values between 1..5
	$rating = intval($_REQUEST['rating']);
	$post_id = intval($_REQUEST['post_id']);
	if (($rating < 1) || ($rating > 5)) {
		return 'incorrect rating';
	}
	// Check if referred news exists
	if (!is_array($row = $mysql->record("select * from " . prefix . "_news where id = " . db_squote($post_id)))) {
		return 'referred news not found';
	}
	// Check if we try to make a duplicated rate
	if ($_COOKIE['rating' . $row['id']])
		return 'you already made your rate';
	// Check if we feet "register only" limitation
	if (extra_get_param('rating', 'regonly') && !is_array($userROW)) {
		return 'only registered users can rate news';
	}
	// Ok, everything is fine. Let's update rating.
	@setcookie('rating' . $post_id, 'voted', (time() + 31526000), '/');
	$mysql->query("update " . prefix . "_news set rating=rating+" . $rating . ", votes=votes+1 where id = " . db_squote($post_id));
	$data = $mysql->record("select rating, votes from " . prefix . "_news where id = " . db_squote($post_id));
	$localskin = extra_get_param('rating', 'localskin');
	if (!$localskin) $localskin = 'basic';
	$tpath = locatePluginTemplates(array('rating', ':rating.css'), 'rating', extra_get_param('rating', 'localsource'), $localskin);
	register_stylesheet($tpath['url::rating.css'] . '/rating.css');
	$tvars['vars']['tpl_url'] = $tpath['url::rating.css'];
	$tvars['vars']['home'] = home;
	$tvars['vars']['rating'] = ($data['rating'] == 0) ? 0 : round(($data['rating'] / $data['votes']), 0);
	$tvars['vars']['votes'] = $data['votes'];
	$tpl->template('rating', $tpath['rating']);
	$tpl->vars('rating', $tvars);

	return $tpl->show('rating');
}

function rating_show($newsID, $rating, $votes) {

	global $tpl, $userROW;
	LoadPluginLang('rating', 'site');
	$localskin = extra_get_param('rating', 'localskin');
	if (!$localskin) $localskin = 'basic';
	$tpath = locatePluginTemplates(array('rating', 'rating.form', ':rating.css'), 'rating', extra_get_param('rating', 'localsource'), $localskin);
	register_stylesheet($tpath['url::rating.css'] . '/rating.css');
	$tvars['vars']['tpl_url'] = $tpath['url::rating.css'];
	$tvars['vars']['home'] = home;
	$tvars['vars']['ajax_url'] = generateLink('core', 'plugin', array('plugin' => 'rating'), array());
	$tvars['vars']['post_id'] = $newsID;
	$tvars['vars']['rating'] = (!$rating || !$votes) ? 0 : round(($rating / $votes), 0);
	$tvars['vars']['votes'] = $votes;
	if ((isset($_COOKIE['rating' . $newsID]) && $_COOKIE['rating' . $newsID]) || (extra_get_param('rating', 'regonly') && !is_array($userROW))) {
		// Show
		$tpl->template('rating', $tpath['rating']);
		$tpl->vars('rating', $tvars);

		return $tpl->show('rating');
	} else {
		// Edit
		$tpl->template('rating.form', $tpath['rating.form']);
		$tpl->vars('rating.form', $tvars);

		return $tpl->show('rating.form');
	}

	return;
}

function plugin_rating_screen() {

	global $SUPRESS_TEMPLATE_SHOW, $template;
	@header('Content-type: text/html; charset="windows-1251"');
	if ($_REQUEST['post_id']) {
		$template['vars']['mainblock'] = plugin_rating_update();
		$SUPRESS_TEMPLATE_SHOW = 1;
	} else {
		$template['vars']['mainblock'] = 'unsupported action';
	}
}

//
// Фильтр новостей (для показа рейтинга)
//
class RatingNewsFilter extends NewsFilter {

    public function showNews($newsID, $SQLnews, &$tvars, $mode = []) {

		global $tpl, $mysql, $userROW;
		$tvars['vars']['plugin_rating'] = rating_show($SQLnews['id'], $SQLnews['rating'], $SQLnews['votes']);
	}
}

register_filter('news', 'raing', new RatingNewsFilter);
register_plugin_page('rating', '', 'plugin_rating_screen', 0);
