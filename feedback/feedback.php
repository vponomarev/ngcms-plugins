<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

register_plugin_page('feedback','','plugin_feedback_screen',0);
register_plugin_page('feedback','post','plugin_feedback_post',0);

loadPluginLang('feedback', 'main', '', '', ':');


//
// Show feedback form
function plugin_feedback_screen(){
	plugin_feedback_showScreen();
}

//
// Show feedback form screen
// Mode:
// * 0 - initial show
// * 1 - show filled earlier values (error filling some fields)
function plugin_feedback_showScreen($mode = 0, $errorText = '') {
 global $template, $tpl, $lang, $mysql, $userROW;

 $ptpl_url = admin_url.'/plugins/feedback/tpl';

 // Determine paths for all template files
 $tpath = locatePluginTemplates(array('site.infoblock', 'site.form.hdr', 'site.form.row', 'site.form.captcha'), 'feedback', extra_get_param('feedback', 'localsource'));

 $form_id = intval($_REQUEST['id']);

 if (!is_array($frow = $mysql->record("select * from ".prefix."_feedback where active = 1 and id = ".$form_id))) {
	$tpl->template('site.infoblock', $tpath['site.infoblock']);
	$tpl->vars('site.infoblock', array( 'vars' => array( 'title' => $lang['feedback:form.no.title'], 'ptpl_url' => $ptpl_url, 'entries' => $lang['feedback:form.no.description'])));
	$template['vars']['mainblock']      =  $tpl->show('site.infoblock');
	return 1;
 }

 // Unpack form data
 $fData = unserialize($frow['struct']);
 if (!is_array($fData)) $fData = array();

 $output = '';
 $FBF_DATA = array();
 $tpl->template('site.form.row', $tpath['site.form.row']);
 foreach ($fData as $fName => $fInfo) {
 	$tvars = array();
 	$tvars['vars']['ptpl_url'] = $ptpl_url;
	$tvars['vars']['name']	= $fInfo['name'];
	$tvars['vars']['title']	= $fInfo['title'];

	$FBF_DATA[$fName] = array($fInfo['type'], intval($fInfo['required']), iconv('Windows-1251', 'UTF-8', $fInfo['title']));

 	$setValue = $mode?$_REQUEST[$fInfo['name']]:$fInfo['default'];

	switch ($fInfo['type']) {
		case 'text':
		case 'textarea':
			$tvars['vars']['value']	= $setValue;
			break;

		case 'date':
			// Prepare parsed date for show (in `show again` mode)
			$setValueDay	= $fInfo['default:vars']['day'];
			$setValueMonth	= $fInfo['default:vars']['month'];
			$setValueYear	= $fInfo['default:vars']['year'];
			if ($mode) {
				if ((intval($_REQUEST[$fInfo['name'].':day']) >= 1) &&
					(intval($_REQUEST[$fInfo['name'].':day']) <= 31) &&
					(intval($_REQUEST[$fInfo['name'].':month']) >= 1) &&
					(intval($_REQUEST[$fInfo['name'].':month']) <= 12) &&
					(intval($_REQUEST[$fInfo['name'].':year']) >= 1970) &&
					(intval($_REQUEST[$fInfo['name'].':year']) <= 2012)) {
					$setValueDay	= intval($_REQUEST[$fInfo['name'].':day']);
					$setValueMonth	= intval($_REQUEST[$fInfo['name'].':month']);
					$setValueYear	= intval($_REQUEST[$fInfo['name'].':year']);
				}
			}

			$opts = $fInfo['required']?'':'<option value="">--</option>';
			for ($di = 1; $di <= 31; $di++) { $opts .= '<option value="'.$di.'"'.($di == $setValueDay?' selected="selected"':'').'>'.sprintf('%02u',$di).'</option>'; }
			$tvars['vars']['day_options'] = $opts;

			$opts = $fInfo['required']?'':'<option value="">--</option>';
			for ($di = 1; $di <= 12; $di++) { $opts .= '<option value="'.$di.'"'.($di == $setValueMonth?' selected="selected"':'').'>'.sprintf('%02u',$di).'</option>'; }
			$tvars['vars']['month_options'] = $opts;

			$opts = $fInfo['required']?'':'<option value="">--</option>';
			for ($di = 1970; $di <= 2012; $di++) { $opts .= '<option value="'.$di.'"'.($di == $setValueYear?' selected="selected"':'').'>'.$di.'</option>'; }
			$tvars['vars']['year_options'] = $opts;

			break;

		case 'select':
			$opts = '';
			if (is_array($fInfo['options']))
				foreach ($fInfo['options'] as $k => $v) {
					$opts .= '<option value="'.secure_html($v).'"'.($v == $setValue?' selected="selected"':'').'>'.secure_html($v).'</option>';
				}
			$tvars['vars']['options'] = $opts;
	}
	$tvars['regx']['#\[text\](.+?)\[\/text\]#is']			= ($fInfo['type'] == 'text'    )?'$1':'';
	$tvars['regx']['#\[textarea\](.+?)\[\/textarea\]#is']	= ($fInfo['type'] == 'textarea')?'$1':'';
	$tvars['regx']['#\[select\](.+?)\[\/select\]#is']		= ($fInfo['type'] == 'select'  )?'$1':'';
	$tvars['regx']['#\[date\](.+?)\[\/date\]#is']			= ($fInfo['type'] == 'date'  )?'$1':'';

	$tpl->vars('site.form.row', $tvars);
	$output .= $tpl->show('site.form.row');

 }
 // Check if we need captcha
 $captcha = '';

 if (substr($frow['flags'],1,1)) {
 	$tvars = array();
 	$tvars['vars']['rand'] = rand(00000, 99999);
 	$tvars['vars']['captcha_url'] = admin_url."/captcha.php?id=feedback";
 	$tpl->template('site.form.captcha', $tpath['site.form.captcha']);
 	$tpl->vars('site.form.captcha', $tvars);
 	$captcha = $tpl->show('site.form.captcha');

 	// Now let's generate our own code
 	$_SESSION['captcha.feedback'] = rand(00000, 99999);
 }

 // Prepare params
 $tvars = array();
 $tvars['vars']['ptpl_url']		= $ptpl_url;
 $tvars['vars']['captcha']		= $captcha;
 $tvars['vars']['id']			= $frow['id'];
 $tvars['vars']['description']	= $frow['description'];
 $tvars['vars']['entries']		= $output;
 $tvars['vars']['form_url']		= generateLink('core', 'plugin', array('plugin' => 'feedback', 'handler' => 'post'), array());

 $tvars['vars']['FBF_DATA'] = json_encode($FBF_DATA);
 $tvars['regx']['#\[jcheck\](.+?)\[\/jcheck\]#is']	= intval(substr($frow['flags'],0,1))?'$1':'';
 $tvars['vars']['errorText']	= $errorText;
 $tvars['regx']['#\[error\](.*?)\[\/error\]#is']	= ($errorText == '')?'':'$1';

 // Choose template to use
 if ($frow['template']) {
  $tP = root.'plugins/feedback/tpl/templates/';
  $tN = $frow['template'];
 } else {
  $tP = $tpath['site.form.hdr'];
  $tN = 'site.form.hdr';
 }


 // Show template of current form
 $tpl->template($tN, $tP);
 $tpl->vars($tN, $tvars);
 $output = $tpl->show($tN);

 $tvars = array();
 $tvars['vars']['ptpl_url']	= $ptpl_url;
 $tvars['vars']['title']	= $frow['title'];
 $tvars['vars']['entries']	= $output;

 $tpl->template('site.infoblock', $tpath['site.infoblock']);
 $tpl->vars('site.infoblock', $tvars);
 $template['vars']['mainblock']      =  $tpl->show('site.infoblock');

}


//
// Post feedback message
function plugin_feedback_post() {
	global $template, $tpl, $lang, $mysql, $userROW, $SYSTEM_FLAGS;

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('site.infoblock', 'site.form.hdr', 'site.form.row'), 'feedback', extra_get_param('feedback', 'localsource'));
	$ptpl_url = admin_url.'/plugins/feedback/tpl';

	$form_id = intval($_REQUEST['id']);
	$SYSTEM_FLAGS['info']['title']['group']		= $lang['feedback:header.title'];

	if (!is_array($frow = $mysql->record("select * from ".prefix."_feedback where active = 1 and id = ".$form_id))) {
		$tpl->template('site.infoblock', $tpath['site.infoblock']);
		$tpl->vars('site.infoblock', array( 'vars' => array( 'title' => $lang['feedback:form.no.title'], 'ptpl_url' => $ptpl_url, 'entries' => $lang['feedback:form.no.description'])));
		$template['vars']['mainblock']      =  $tpl->show('site.infoblock');
		return 1;
	}

	$SYSTEM_FLAGS['info']['title']['item']	= str_replace('{title}', $frow['title'], $lang['feedback:header.send']);

	// Unpack form data
	$fData = unserialize($frow['struct']);
	if (!is_array($fData)) $fData = array();

	// Check if captcha check if needed
	if (substr($frow['flags'],1,1)) {
		$vcode = $_REQUEST['vcode'];
		if ($vcode != $_SESSION['captcha.feedback']) {
			// Wrong CAPTCHA code (!!!)
			plugin_feedback_showScreen(1, $lang['feedback:sform.captcha.badcode']);
			return;
		}
	}

	// Scan all fields and fill data. Prepare outgoing email.
	$output = '';

	foreach ($fData as $fName => $fInfo) {
		switch ($fInfo['type']) {
			case 'date':	$fieldValue = $_REQUEST[$fName.':day'] . '.' . $_REQUEST[$fName.':month'] . '.' . $_REQUEST[$fName.':year'];
		  					break;
			default:		$fieldValue = $_REQUEST[$fName];
		}
		$output .= '['.$fName.'] '.$fInfo['title'].': '.$fieldValue."<br/>\n";
	}

	$mailSubject = str_replace(array('{name}', '{title}'), array($frow['name'], $frow['title']), $lang['feedback:mail.subj']);
	$mailBody = str_replace(array('\n'), array("\n"), $lang['feedback:mail.body.header']) . $output;

	$mailCount = 0;
	foreach (explode("\n", $frow['emails']) as $email) {
		if (trim($email) == '')
			continue;

		$mailCount++;
		zzMail($email, $mailSubject, $mailBody, 'text');
	}

	$tpl->template('site.infoblock', $tpath['site.infoblock']);
	$tpl->vars('site.infoblock', array( 'vars' => array( 'title' => $frow['title'], 'ptpl_url' => $ptpl_url, 'entries' => str_replace('{ecount}', $mailCount, $lang['feedback:confirm.message']))));
	$template['vars']['mainblock']      =  $tpl->show('site.infoblock');
}

