<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
loadPluginLang('subscribe_comments', 'main', '', '', ':');
loadPluginLibrary('comments', 'lib');
function plugin_subscribe_comments_cron() {

	global $tpl, $cron, $mysql, $config, $lang, $parse, $PFILTERS;
	//var_dump($newsRec);
	//$row = $mysql->record("select * from ".prefix."_news where id=".db_squote($newsRec['id']));
	foreach ($mysql->select("select * from " . prefix . "_subscribe_comments_temp s left join " . prefix . "_news n on s.news_id=n.id") as $irow) {
		//Email informer
		$alink = ($irow['com_author_id']) ? generatePluginLink('uprofile', 'show', array('name' => $irow['com_author'], 'id' => $irow['com_author_id']), array(), false, true) : '';
		$newsLink = newsGenerateLink($irow, false, 0, true);
		$body = str_replace(
			array(
				'{username}',
				'[userlink]',
				'[/userlink]',
				'{comment}',
				'{newslink}',
				'{newstitle}',
				'{unsubscribelink}',
				'{unsubscribelink_all}'
			),
			array(
				$irow['com_author'],
				($irow['com_author_id']) ? '<a href="' . $alink . '">' : '',
				($irow['com_author_id']) ? '</a>' : '',
				secure_html($irow['com_text']),
				$newsLink,
				$irow['title'],
				home . generateLink('core', 'plugin', array('plugin' => 'subscribe_comments'), array('unsubscribe_me' => 1, 'subscribe_field' => $irow['user_email'], 'subscribe_news' => $irow['alt_name'])),
				home . generateLink('core', 'plugin', array('plugin' => 'subscribe_comments'), array('unsubscribe_me' => 2, 'subscribe_field' => $irow['user_email'])),
			),
			$lang['subscribe_comments:msg.notice']
		);
		$dump[$irow['user_email']] .= $body;
	}
	foreach ($dump as $ekey => $prow) {
		$ends = str_replace(
			array('{unsubscribelink_all}'),
			array(home . generateLink('core', 'plugin', array('plugin' => 'subscribe_comments'), array('unsubscribe_me' => 2, 'subscribe_field' => $ekey))),
			$lang['subscribe_comments:msg.notice_end']
		);
		$prow = $prow . $ends;
		zzMail($ekey, $lang['subscribe_comments:msg.newcomment'], $prow, 'html');
	}
	$mysql->query("DELETE FROM " . prefix . "_subscribe_comments_temp");
}

class ShowSubscribeForm extends NewsFilter {

	function showNews($newsID, $SQLnews, &$tvars, $mode = array()) {

		global $mysql, $tpl, $config;
		//plugin_subscribe_comments_cron();
		$subscribelink = generateLink('core', 'plugin', array('plugin' => 'subscribe_comments'));
		$tvars['vars']['post_url_f'] = $subscribelink;
	}
}

class SubscribeComments extends FilterComments {

	function addCommentsForm($newsID, &$tvars) {

		global $mysql;
		if ($nrow = $mysql->record("select * from " . prefix . "_subscribe_comments where news_id='" . $newsID . "' and user_email='" . secure_html(urldecode($_COOKIE['com_usermail'])) . "'")) {
			$subscribe_box = '1';
			$subscribe_box_checked = 'checked';
		} else {
			$subscribe_box = '0';
			$subscribe_box_checked = '';
		}
		$tvars['vars']['subscribe_box'] = $subscribe_box;
		$tvars['vars']['subscribe_box_checked'] = $subscribe_box_checked;
		$tvars['vars']['test_var'] = 'tested';
	}

	function addCommentsNotify($userRec, $newsRec, &$tvars, $SQL, $commID) {

		global $mysql, $lang;
		//var_dump($newsRec);
		if ($_REQUEST['subscribe_checked'] == 'true') {
			if (!($nrow = $mysql->record("select * from " . prefix . "_subscribe_comments where news_id='" . $newsRec['id'] . "' and news_altname='" . $newsRec['alt_name'] . "' and user_email='" . $SQL['mail'] . "'"))) {
				$mysql->query("insert into " . prefix . "_subscribe_comments (user_email, news_id, news_altname) values ('" . $SQL['mail'] . "', '" . $newsRec['id'] . "', '" . $newsRec['alt_name'] . "')");
			}
		} elseif ($_REQUEST['subscribe_checked'] == 'false') {
			//var_dump($_REQUEST); 
			if ($nrow = $mysql->record("select * from " . prefix . "_subscribe_comments where news_id='" . $newsRec['id'] . "' and news_altname='" . $newsRec['alt_name'] . "' and user_email='" . $SQL['mail'] . "'")) {
				$mysql->query("delete from " . prefix . "_subscribe_comments where user_email='" . $SQL['mail'] . "' and news_id='" . $newsRec['id'] . "' and news_altname='" . $newsRec['alt_name'] . "'");
				$mysql->query("delete from " . prefix . "_subscribe_comments_temp where user_email='" . $SQL['mail'] . "' and news_id='" . $newsRec['id'] . "' and news_altname='" . $newsRec['alt_name'] . "'");
			}
		}
		$delayed_send = pluginGetVariable('subscribe_comments', 'delayed_send');
		//var_dump($delayed_send);
		if ($delayed_send == 1) {
			foreach ($mysql->select("select * from " . prefix . "_subscribe_comments where news_id='" . $newsRec['id'] . "' and news_altname='" . $newsRec['alt_name'] . "'") as $srow) {
				//var_dump($srow);
				if (!($nrow = $mysql->record("select * from " . prefix . "_subscribe_comments_temp where com_id='" . $commID . "' and news_altname='" . $newsRec['alt_name'] . "' and user_email='" . $srow['user_email'] . "'"))) {
					$mysql->query("insert into " . prefix . "_subscribe_comments_temp (com_id, com_author, com_author_id, com_text, news_title, news_id, news_altname, user_email) values ('" . $commID . "', '" . $SQL['author'] . "', '" . $SQL['author_id'] . "', '" . $SQL['text'] . "', '" . $newsRec['title'] . "', '" . $newsRec['id'] . "', '" . $newsRec['alt_name'] . "', '" . $srow['user_email'] . "')");
				}
			}
		} else {
			//var_dump($newsRec);
			//$row = $mysql->record("select * from ".prefix."_news where id=".db_squote($newsRec['id']));
			// Рассылаем сразу
			$newsLink = newsGenerateLink($newsRec, false, 0, true);
			foreach ($mysql->select("select * from " . prefix . "_subscribe_comments where news_id='" . $newsRec['id'] . "' and news_altname='" . $newsRec['alt_name'] . "'") as $srow) {
				$alink = ($SQL['author_id']) ? generatePluginLink('uprofile', 'show', array('name' => $SQL['author'], 'id' => $SQL['author_id']), array(), false, true) : '';
				$newsLink = newsGenerateLink($newsRec, false, 0, true);
				$body = str_replace(
					array(
						'{username}',
						'[userlink]',
						'[/userlink]',
						'{comment}',
						'{newslink}',
						'{newstitle}',
						'{unsubscribelink}',
						'{unsubscribelink_all}'
					),
					array(
						$SQL['author'],
						($SQL['author_id']) ? '<a href="' . $alink . '">' : '',
						($SQL['author_id']) ? '</a>' : '',
						secure_html($SQL['text']),
						$newsLink,
						$newsRec['title'],
						home . generateLink('core', 'plugin', array('plugin' => 'subscribe_comments'), array('unsubscribe_me' => 1, 'subscribe_field' => $srow['user_email'], 'subscribe_news' => $newsRec['alt_name'])),
						home . generateLink('core', 'plugin', array('plugin' => 'subscribe_comments'), array('unsubscribe_me' => 2, 'subscribe_field' => $srow['user_email'])),
					),
					$lang['subscribe_comments:msg.notice_one']
				);
				zzMail($srow['user_email'], $lang['subscribe_comments:msg.newcomment'], $body, 'html');
			}
		}
	}
}

function plugin_subscribe_comments() {

	global $template, $config, $mysql, $tpl, $lang, $CurrentHandler;
	global $catmap, $catz, $config, $userROW, $template, $lang, $tpl;
	$timeout = 3;
	$altname = parse_url($_SERVER["HTTP_REFERER"]);
	$altname = preg_match('/^.*\/(.*)$/', $altname['path'], $matches);
	$altname = secure_html(str_replace('.html', '', $matches['1']));
	if (empty($altname)) {
		$altname = secure_html($_REQUEST['subscribe_news']);
	}
	$email = secure_html($_REQUEST['subscribe_field']);
	// msg(array("type" => "error", "text" => $lang['subscribe_comments:err.password']));
	if (!(filter_var($email, FILTER_VALIDATE_EMAIL)) || empty($email)) {
		msg(array("type" => "error", "text" => $lang['subscribe_comments:err.wrongemail']));
		header('Refresh: ' . $timeout . '; URL=' . secure_html($_SERVER["HTTP_REFERER"]) . '');

		return;
	}
	$unsubscribe = secure_html($_REQUEST['unsubscribe_me']);
	//var_dump($_REQUEST);
	switch ($unsubscribe) {
		case '1':
			if (!empty($email) && !empty($altname)) {
				if (empty($altname)) {
					msg(array("type" => "error", "text" => $lang['subscribe_comments:err.wrongparams']));

					return;
				}
				$row = $mysql->record("select * from " . prefix . "_news where alt_name='" . $altname . "'");
				if (empty($row)) {
					msg(array("type" => "error", "text" => $lang['subscribe_comments:err.wrongaltname']));

					return;
				}
				if ($nrow = $mysql->record("select * from " . prefix . "_subscribe_comments where news_id='" . $row['id'] . "' and news_altname='" . $row['alt_name'] . "' and user_email='" . $email . "'")) {
					$mysql->query("DELETE FROM " . prefix . "_subscribe_comments WHERE user_email='" . $email . "' and news_altname='" . $altname . "'");
					$mysql->query("DELETE FROM " . prefix . "_subscribe_comments_temp where user_email='" . $email . "' and news_altname='" . $altname . "'");
					msg(array("type" => "message", "text" => str_replace(array('{title}'), array($row['title']), $lang['subscribe_comments:msg.unsubscribepageok'])));

					return;
				} else {
					msg(array("type" => "error", "text" => $lang['subscribe_comments:err.unsubscribepageyet']));

					return;
				}
			} else {
				msg(array("type" => "error", "text" => $lang['subscribe_comments:err.wrongparams']));

				return;
			}
			break;
		case '2':
			if (!empty($email)) {
				if ($nrow = $mysql->record("select * from " . prefix . "_subscribe_comments where user_email='" . $email . "'")) {
					$mysql->query("DELETE FROM " . prefix . "_subscribe_comments WHERE user_email='" . $email . "'");
					$mysql->query("DELETE FROM " . prefix . "_subscribe_comments_temp where user_email='" . $email . "'");
					msg(array("type" => "message", "text" => $lang['subscribe_comments:msg.unsubscribeall']));

					return;
				} else {
					msg(array("type" => "error", "text" => $lang['subscribe_comments:err.unsubscribeall']));

					return;
				}
			} else {
				msg(array("type" => "error", "text" => $lang['subscribe_comments:err.wrongparams']));

				return;
			}
			break;
		default:
			if (empty($altname)) {
				msg(array("type" => "error", "text" => $lang['subscribe_comments:err.wrongparams']));
				header('Refresh: ' . $timeout . '; URL=' . secure_html($_SERVER["HTTP_REFERER"]) . '');

				return;
			}
			$row = $mysql->record("select * from " . prefix . "_news where alt_name='" . $altname . "'");
			if (empty($row)) {
				msg(array("type" => "error", "text" => $lang['subscribe_comments:err.wrongaltname']));
				header('Refresh: ' . $timeout . '; URL=' . secure_html($_SERVER["HTTP_REFERER"]) . '');

				return;
			}
			if (!($nrow = $mysql->record("select * from " . prefix . "_subscribe_comments where news_id='" . $row['id'] . "' and news_altname='" . $row['alt_name'] . "' and user_email='" . $email . "'"))) {
				$mysql->query("insert into " . prefix . "_subscribe_comments (user_email, news_id, news_altname) values ('" . $email . "', '" . $row['id'] . "', '" . $row['alt_name'] . "')");
				msg(array("type" => "message", "text" => $lang['subscribe_comments:msg.subscribepageok']));
				header('Refresh: ' . $timeout . '; URL=' . secure_html($_SERVER["HTTP_REFERER"]) . '');

				return;
			} else {
				msg(array("type" => "message", "text" => $lang['subscribe_comments:err.subscribepageyet']));
				header('Refresh: ' . $timeout . '; URL=' . secure_html($_SERVER["HTTP_REFERER"]) . '');

				return;
			}
			break;
	}
	/*
		$tpl->template('subscribe_comments', tpl_site.'plugins/subscribe_comments');
		$tpl->vars('subscribe_comments', $tcvars);
		$template['vars']['mainblock'] = $tpl->show('comments.subscribe');
	*/
}

register_filter('comments', 'subscribe_comments', new SubscribeComments);
register_filter('news', 'subscribe_comments', new ShowSubscribeForm);
register_plugin_page('subscribe_comments', '', 'plugin_subscribe_comments', 0);