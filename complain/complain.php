<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// Author     - Author of object under report
// Publisher  - Person, who made a reported. Can be anonymous
// Owner      - Person, who is busy with solving of this problem

// Flags:
// N - inform reporter about status changes of incident

function plugin_complain_resolve_error($id) {
 foreach (explode("\n",extra_get_param('complain', 'errlist')) as $erow) {
  if (preg_match('#^(\d+)\|(.+?)$#', trim($erow), $m) && ($m[1] == $id)) {
   return $m[2];
  }
 }
 return NULL;
}

function plugin_complain_screen() {
 global $template, $tpl, $lang, $mysql, $userROW;
 global $SUPRESS_TEMPLATE_SHOW;

 loadPluginLang('complain', 'main', '', '', ':');

 $SUPRESS_TEMPLATE_SHOW = 1;

 // Determine paths for all template files
 $tpath = locatePluginTemplates(array('list.entry', 'list.header', 'infoblock'), 'complain', extra_get_param('complain', 'localsource'));

 // No access for unregistered users
 if (!is_array($userROW)) {
  $tpl->template('infoblock', $tpath['infoblock']);
  $tpl->vars('infoblock', array( 'vars' => array( 'infoblock' => $lang['complain:error.regonly'])));
  $template['vars']['mainblock']      =  $tpl->show('infoblock');
  return 1;
 }

 // Fetch error list
 $elist = array();
 foreach (explode("\n",extra_get_param('complain', 'errlist')) as $erow) {
  if (preg_match('#^(\d+)\|(.+?)$#', trim($erow), $m)) {
   $elist[$m[1]] = $m[2];
  }
 }

 // Show list of complains
 $tpl->template('list.entry', $tpath['list.entry']);

 // Prepare filters
 $where = array ('(c.complete = 0)');

 // Populate admins array
 $admins = explode("\n", extra_get_param('complain', 'admins'));

 // Non admins will see only complains in which they are involved
 if (($userROW['status'] > 1)&&(!in_array($userROW['name'], $admins))) {
 	$where [] = '((c.publisher_id = '.intval($userROW['id']).') or (c.owner_id = '.intval($userROW['id']).') or (c.author_id = '.intval($userROW['id']).'))';
 }


 $entries = '';
 $etext = array();
// foreach ($mysql->select("select count(c.id) as ccount, c.id, c.status, c.complete, c.owner_id, (select name from ".uprefix."_users where id = c.owner_id) as owner_name, c.author_id, (select name from ".uprefix."_users where id = c.author_id) as author_name, c.publisher_id, (select name from ".uprefix."_users where id = c.publisher_id) as publisher_name, c.publisher_ip, date(c.date) as date, c.ds_id, c.entry_id, c.error_code, n.alt_name as n_alt_name, n.id as n_id, n.title as n_title, n.catid as n_catid, n.postdate as n_postdate from ".prefix."_complain c left join ".prefix."_news n on c.entry_id = n.id where ".join(" AND ", $where)." group by c.ds_id, c.entry_id, c.error_code") as $crow) {
 foreach ($mysql->select("select c.id, c.status, c.complete, c.owner_id, (select name from ".uprefix."_users where id = c.owner_id) as owner_name, c.author_id, (select name from ".uprefix."_users where id = c.author_id) as author_name, c.publisher_id, (select name from ".uprefix."_users where id = c.publisher_id) as publisher_name, c.publisher_ip, date(c.date) as date, time(c.date) as time, c.ds_id, c.entry_id, c.error_code, c.error_text, n.alt_name as n_alt_name, n.id as n_id, n.title as n_title, n.catid as n_catid, n.postdate as n_postdate from ".prefix."_complain c left join ".prefix."_news n on c.entry_id = n.id where ".join(" AND ", $where)) as $crow) {
  $tvars = array();
  $tvars['vars'] = array(
   'id'             => $crow['id'],
   'date'           => $crow['date'],
   'time'           => $crow['time'],
   'error'          => $elist[$crow['error_code']].($crow['error_text']?' (<span style="cursor: pointer;" onclick="alert(ETEXT['.$crow['id'].']);">*</span>)':''),
   'ccount'         => ($crow['ccount']>1)?('(<b>'.$crow['ccount'].'</b>)'):'',
   'title'          => $crow['n_title'],
   'link'           => newsGenerateLink(array('catid' => $crow['n_catid'], 'alt_name' => $crow['n_alt_name'], 'id' => $crow['n_id'], 'postdate' => $crow['n_postdate']), false, 0, true),
   'publisher_name' => $crow['publisher_id']?$crow['publisher_name']:'',
   'publisher_ip'	=> $crow['publisher_ip'],
   'author_name'    => $crow['author_name'],
   'owner_name'     => $crow['owner_id']?'<b>'.$crow['owner_name'].'</b>':$lang['complain:noowner'],
   'status'         => $lang['complain:status.'.$crow['status']],
  );
  if ($crow['error_text'])
  	$etext[$crow['id']] = iconv('Windows-1251', 'UTF-8', $crow['error_text']);

  // Check if user have enough permissions to make any changes in this report
  if (($userROW['status'] == 1) ||
      (in_array($userROW['name'], $admins)) ||
      ($userROW['id'] == $crow['owner_id']) ||
      (($crow['author_id'] == $userROW['id']) &&
	   (($crow['owner_id'] == $userROW['id'])||(!$crow['owner_id']))
	   )
	  ) {
	  $tvars['regx']['#\[perm\](.+?)\[\/perm\]#is'] = '$1';
  } else {
	  $tvars['regx']['#\[perm\](.+?)\[\/perm\]#is'] = '';
  }

  $tpl->vars('list.entry', $tvars);
  $entries .= $tpl->show('list.entry');
 }

 $sselect = '';
 for ($i = 2; $i < 5; $i++) $sselect .= '<option value="'.$i.'">'.$lang['complain:status.'.$i].'</option>';

 $tpl->template('list.header', $tpath['list.header']);
 $tvars = array();
 $tvars['vars'] = array( 'entries' => $entries, 'status_options' => $sselect, 'form_url' => generateLink('core', 'plugin', array('plugin' => 'complain', 'handler' => 'update')), 'ETEXT' => json_encode($etext));
 $tpl->vars('list.header', $tvars);
 $template['vars']['mainblock'] = $tpl->show('list.header');
}

function plugin_complain_add() {
 global $template, $tpl, $lang, $mysql, $userROW;
 global $SUPRESS_TEMPLATE_SHOW;

 loadPluginLang('complain', 'main', '', '', ':');

 $SUPRESS_TEMPLATE_SHOW = 1;

 // Determine paths for all template files
 $tpath = locatePluginTemplates(array('ext.form', 'infoblock'), 'complain', extra_get_param('complain', 'localsource'));

 // Check if we shouldn't show block for unregs
 if ((!is_array($userROW)) && (!extra_get_param('complain', 'allow_unreg'))) {
  $tpl->template('infoblock', $tpath['infoblock']);
  $tpl->vars('infoblock', array( 'vars' => array( 'infoblock' => $lang['complain:error.regonly'].$lang['complain:link.close'])));
  $template['vars']['mainblock']      =  $tpl->show('infoblock');
  return 1;
 }

 // Prepare error list
 $err = '';
 foreach (explode("\n",extra_get_param('complain', 'errlist')) as $erow) {
  if (preg_match('#^(\d+)\|(.+?)$#', trim($erow), $m)) {
   $err .= '<option value="'.$m[1].'">'.htmlspecialchars($m[2]).'</option>'."\n";
  }
 }

 $txvars = array();
 $txvars['vars'] = array ( 'ds_id' => intval($_REQUEST['ds_id']), 'entry_id' => intval($_REQUEST['entry_id']), 'errorlist' => $err );
 $txvars['regx']['#\[notify\](.*?)\[/notify\]#is'] = ((is_array($userROW)) &&(extra_get_param('complain', 'inform_reporter') == 2))?'$1':'';
 $txvars['regx']['#\[email\](.*?)\[/email\]#is'] = ((!is_array($userROW)) && extra_get_param('complain', 'allow_unreg_inform'))?'$1':'';
 $txvars['regx']['#\[text\](.*?)\[/text\]#is'] = ((is_array($userROW) && (extra_get_param('complain', 'allow_text')==1)) || (extra_get_param('complain', 'allow_text') == 2))?'$1':'';

 $txvars['vars']['form_url'] = generateLink('core', 'plugin', array('plugin' => 'complain', 'handler' => 'post'));

 $tpl->template('ext.form', $tpath['ext.form']);
 $tpl->vars('ext.form', $txvars);
 $template['vars']['mainblock']      =  $tpl->show('ext.form');
}


function plugin_complain_post() {
 global $template, $tpl, $mysql, $lang, $userROW, $ip, $config;
 global $SUPRESS_TEMPLATE_SHOW;

 loadPluginLang('complain', 'main', '', '', ':');

 $SUPRESS_TEMPLATE_SHOW = 1;

 // Determine paths for all template files
 $tpath = locatePluginTemplates(array('ext.form', 'infoblock', 'error.noentry', 'form.confirm'), 'complain', extra_get_param('complain', 'localsource'));

 // Check if we shouldn't show block for unregs
 if ((!is_array($userROW)) && (!extra_get_param('complain', 'allow_unreg'))) {
  $tpl->template('infoblock', $tpath['infoblock']);
  $tpl->vars('infoblock', array( 'vars' => array( 'infoblock' => $lang['complain:error.regonly'].$lang['complain:link.close'])));
  $template['vars']['mainblock']      =  $tpl->show('infoblock');
  return 1;
 }

 // Check if reference storage & entry exists, fetch entrie's params
 $cdata = array();
 switch (intval($_REQUEST['ds_id'])) {
  case 1:
  	if (is_array($dse = $mysql->record("select n.*, u.mail from ".prefix."_news n left join ".uprefix."_users u on n.author_id = u.id where n.id = ". db_squote($_REQUEST['entry_id'])." and n.approve=1"))) {
  	 $cdata['ds_id']       = intval($_REQUEST['ds_id']);
  	 $cdata['id']          = $dse['id'];
  	 $cdata['title']       = $dse['title'];
  	 $cdata['link']        = newsGenerateLink($dse, false, 0, true);
  	 $cdata['author']      = $dse['author'];
  	 $cdata['author_id']   = $dse['author_id'];
  	 $cdata['author_mail'] = $dse['mail'];
  	}
	break;
  default:
 }

 // Check if data entry was not found
 if (!isset($cdata['id'])) {
  $tpl->template('infoblock', $tpath['infoblock']);
  $tpl->vars('infoblock', array( 'vars' => array( 'infoblock' => $lang['complain:error.noentry'].$lang['complain:link.close'])));
  $template['vars']['mainblock']      =  $tpl->show('infoblock');
  return;
 }

 $errid   = intval($_REQUEST['error']);
 $errtext = plugin_complain_resolve_error($errid);

 // Do not accept unresolvable errors
 if ($errtext === NULL) {
  $tpl->template('infoblock', $tpath['infoblock']);
  $tpl->vars('infoblock', array( 'vars' => array( 'infoblock' => $lang['complain:error.unresolvable'].$lang['complain:link.close'])));
  $template['vars']['mainblock']      =  $tpl->show('infoblock');
  return;
 }

 // Check reporter notification mode
 if (is_array($userROW)) {
  $flagNotify = ((extra_get_param('complain', 'inform_reporter') == '1')||((extra_get_param('complain', 'inform_reporter') == '2') && ($_REQUEST['notify'])))?1:0;
  $publisherMail = $userROW['mail'];
 } else {
  if ((strlen($_REQUEST['mail']) < 70) && (preg_match("/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $_REQUEST['mail']))) {
   $publisherMail = $_REQUEST['mail'];
  } else {
   $publisherMail = '';
  }
  $flagNotify = (extra_get_param('complain', 'allow_unreg_inform') && $publisherMail)?1:0;
 }

 // Text error description
 $errorText = ((is_array($userROW) && (extra_get_param('complain', 'allow_text') == 1)) || (extra_get_param('complain', 'allow_text') == 2))? $_REQUEST['error_text'] : '';

 // Fill flags variable
 $flags = $flagNotify?'N':'';

 // Let's make a report
 $mysql->query("insert into ".prefix."_complain (author_id, publisher_id, publisher_ip, publisher_mail, date, ds_id, entry_id, error_code, error_text, flags) values (".db_squote($cdata['author_id']).", ".db_squote(is_array($userROW)?$userROW['id']:0).", ".db_squote($ip).", ".db_squote($publisherMail).", now(), ".db_squote($cdata['ds_id']).", ".db_squote($cdata['id']).", ".db_squote($errid).", ".db_squote($errorText).", ".db_squote($flags).")");

 // Write a mail (if needed)
 if (extra_get_param('complain', 'inform_author') || extra_get_param('complain', 'inform_admin') || extra_get_param('complain', 'inform_admins')) {

  $tmvars = array (
    'title' => $cdata['title'],
    'link'  => $cdata['link'],
    'link_admin' => generateLink('core', 'plugin', array('plugin' => 'complain'), array(), false, true),
    'error' => $errtext);

  $mail_text = str_replace(
   array( '\n', '{title}', '{link}', '{error}', '{link_admin}' ),
   array( "\n", $tmvars['title'], $tmvars['link'], $errtext, $tmvars['link_admin'] ),
   $lang['complain:mail.open.body']);

  // Inform author
  if (extra_get_param('complain', 'inform_author') && strlen($cdata['author_mail'])) {
   zzMail($cdata['author_mail'], $lang['complain:mail.open.subj'], $mail_text, 'text');
  }

  // Inform site admins
  if (extra_get_param('complain', 'inform_admin')) {
   // Send to all admins
   foreach ($mysql->select("select mail from ".uprefix."_users where status = 1") as $urow) {
    if (strlen($urow['mail'])) {
     zzMail($urow['mail'], $lang['complain:mail.open.subj'], $mail_text, 'text');
    }
   }
  }

  // Inform PLUGIN admins
  if (extra_get_param('complain', 'inform_admins')) {
  	foreach (explode("\n", extra_get_param('complain', 'admins')) as $admin_name) {
		if ($urow = $mysql->record("select mail from ".uprefix."_users where name = ".db_squote($admin_name))) {
		    if (strlen($urow['mail'])) {
		     zzMail($urow['mail'], $lang['complain:mail.open.subj'], $mail_text, 'text');
		    }
		}
	  }
  }

 }

 $tpl->template('infoblock', $tpath['infoblock']);
 $tpl->vars('infoblock', array( 'vars' => array( 'infoblock' => $lang['complain:info.accepted'].$lang['complain:link.close'])));
 $template['vars']['mainblock']      =  $tpl->show('infoblock');
}

function plugin_complain_update() {
 global $template, $config, $tpl, $mysql, $lang, $userROW;
 global $SUPRESS_TEMPLATE_SHOW;

 loadPluginLang('complain', 'main', '', '', ':');

 $SUPRESS_TEMPLATE_SHOW = 1;

 // Determine paths for all template files
 $tpath = locatePluginTemplates(array('infoblock'), 'complain', extra_get_param('complain', 'localsource'));

 $link_admin = str_replace('{link}', generateLink('core', 'plugin', array('plugin' => 'complain')), $lang['complain:link.admin']);

 // Only registered users are allowed here
 if (!is_array($userROW)) {
  $tpl->template('infoblock', $tpath['infoblock']);
  $tpl->vars('infoblock', array( 'vars' => array( 'infoblock' => $lang['complain:error.regonly'])));
  $template['vars']['mainblock']      =  $tpl->show('infoblock');
  return 1;
 }

 // Fetch list of affected incidents
 $ilist = array();

 foreach ($_REQUEST as $k => $v) {
  if (preg_match('#^inc_(\d+)$#', $k, $m) && ($v == "1"))
  	array_push($ilist, $m[1]);
 }

 // Exit if no incidents are marked
 if (!count($ilist)) {
  $tpl->template('infoblock', $tpath['infoblock']);
  $tpl->vars('infoblock', array( 'vars' => array( 'infoblock' => $lang['complain:info.nothing'].$link_admin)));
  $template['vars']['mainblock']      =  $tpl->show('infoblock');
  return 1;
 }

 // Populate admins list
 $admins = explode("\n", extra_get_param('complain', 'admins'));

 // ** Check requested actions **
 // Change ownership
 if ($_REQUEST['setowner'] == '1') {
  // Admins can change all ownerships, users - can set ownership only for their news
  // that are not already owned by anyone
  $mysql->query("update ".prefix."_complain set owner_id = ".db_squote($userROW['id'])." where id in (".join(",", $ilist).")".(($userROW['status']>1 && (!in_array($userROW['name'], $admins)))?' and owner_id = 0 and author_id='.db_squote($userROW['id']):''));
 }

 // Change status [ ONLY FOR NEWS OWNED BY ME ]
 if ($_REQUEST['setstatus'] == '1') {
  foreach ($mysql->select("select * from ".prefix."_complain where id in (".join(", ",$ilist).") and owner_id = ".db_squote($userROW['id'])) as $irow) {

   $newstatus = intval($_REQUEST['newstatus']);
   // If 'N' flag is set in `flags` field - we should make a notification of an author
   if (strpos($irow['flags'], 'N') !== false) {
    // // If links found and "inform_reporter" flag is ON and status is really changed - send message
    // if (extra_get_param('complain', 'inform_reporter') && $irow['publisher_id'] && (is_array($prec = $mysql->record("select * from ".uprefix."_users where id = ".db_squote($irow['publisher_id']))) && $prec['mail']) && ($irow['status'] != $newstatus)) {
    // We're ready to send mail
    // Check if reference storage & entry exists, fetch entrie's params
    $cdata = array();
    switch (intval($irow['ds_id'])) {
     case 1:
  	 if (is_array($dse = $mysql->record("select n.*, u.mail from ".prefix."_news n left join ".uprefix."_users u on n.author_id = u.id where n.id = ". db_squote($irow['entry_id'])))) {
  	  $cdata['ds_id']       = intval($_REQUEST['ds_id']);
  	  $cdata['id']          = $dse['id'];
  	  $cdata['title']       = $dse['title'];
  	  $cdata['link']        = newsGenerateLink($dse, false, 0, true);
  	  $cdata['author']      = $dse['author'];
  	  $cdata['author_id']   = $dse['author_id'];
  	  $cdata['author_mail'] = $dse['mail'];
  	 }
	 break;
     default:
    }

    $mail_text = str_replace(
     array( '\n', '{title}', '{link}', '{status}', '{error}' ),
     array( "\n", $cdata['title'], $cdata['link'], $lang['complain:status.'.$newstatus], plugin_complain_resolve_error($irow['error_code']) ),
     $lang['complain:mail.status.body']);

    zzMail($irow['publisher_mail'], $lang['complain:mail.status.subj'], $mail_text, 'html');
   }

   // Update report status
   $mysql->query("update ".prefix."_complain set status = ".db_squote($newstatus).((($newstatus==3)||($newstatus==4))?", complete = 1, rdate = now()":'')." where id = ".db_squote($irow['id']));
  }
 }

 $tpl->template('infoblock', $tpath['infoblock']);
 $tpl->vars('infoblock', array( 'vars' => array( 'infoblock' => $lang['complain:info.executed'].$link_admin)));
 $template['vars']['mainblock']      =  $tpl->show('infoblock');

}

//
// Фильтр новостей (для генерации блока "сообщить о проблеме")
//
class ComplainNewsFilter extends NewsFilter {
	function showNews($newsID, $SQLnews, &$tvars, $mode = array()) {
		global $tpl, $mysql, $userROW;

		// Show only in full news
		if ($mode['style'] != 'full') {
		        $tvars['vars']['plugin_complain'] = '';
			return 1;
		}

		// Check if we shouldn't show block for unregs
		if ((!is_array($userROW)) && (!extra_get_param('complain', 'allow_unreg'))) {
		        $tvars['vars']['plugin_complain'] = '';
			return 1;
		}

		// Determine paths for all template files
		$tpath = locatePluginTemplates(array('int.form', 'int.link'), 'complain', extra_get_param('complain', 'localsource'));

		// Check displayed information type - FORM or simple LINK
		if (extra_get_param('complain', 'extform')) {
			// External form
			$link = generateLink('core', 'plugin', array('plugin' => 'complain', 'handler' => 'add'), array('ds_id' => '1', 'entry_id' => $newsID));

			$txvars = array();
			$txvars['vars'] = array ( 'link' => $link );

			$tpl->template('int.link', $tpath['int.link']);
			$tpl->vars('int.link', $txvars);
			$tvars['vars']['plugin_complain']      =  $tpl->show('int.link');
			return;
		}

		// Prepare error list
		$err = '';
		foreach (explode("\n",extra_get_param('complain', 'errlist')) as $erow) {
			if (preg_match('#^(\d+)\|(.+?)$#', trim($erow), $m)) {
				$err .= '<option value="'.$m[1].'">'.htmlspecialchars($m[2]).'</option>'."\n";
			}
		}

		$txvars = array();
		$txvars['vars'] = array ( 'ds_id' => 1, 'entry_id' => $newsID, 'errorlist' => $err, 'form_url' => generateLink('core', 'plugin', array('plugin' => 'complain', 'handler' => 'post')) );

		$tpl->template('int.form', $tpath['int.form']);
		$tpl->vars('int.form', $txvars);
		$tvars['vars']['plugin_complain']      =  $tpl->show('int.form');
	}
}

register_filter('news','complain', new ComplainNewsFilter);
register_plugin_page('complain','','plugin_complain_screen',0);
register_plugin_page('complain','add','plugin_complain_add',0);
register_plugin_page('complain','post','plugin_complain_post',0);
register_plugin_page('complain','update','plugin_complain_update',0);
