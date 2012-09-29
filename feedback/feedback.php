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
	global $template, $lang, $mysql, $userROW, $PFILTERS, $twig, $SYSTEM_FLAGS;

	$output = '';
	$hiddenFields = array();
	$ptpl_url = admin_url.'/plugins/feedback/tpl';

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('site.form', 'site.notify'), 'feedback', pluginGetVariable('feedback', 'localsource'));

	$SYSTEM_FLAGS['info']['title']['group']		= $lang['feedback:header.title'];


	$form_id = intval($_REQUEST['id']);
	$xt = $twig->loadTemplate($tpath['site.notify'].'site.notify.tpl', $conversionConfig);

	// Get form data
	if (!is_array($frow = $mysql->record("select * from ".prefix."_feedback where active = 1 and id = ".$form_id))) {
		$tVars = array(
			'title' => $lang['feedback:form.no.title'],
			'ptpl_url' => $ptpl_url,
			'entries' => $lang['feedback:form.no.description'],
		);

		$template['vars']['mainblock']      =  $xt->render($tVars);
		return 1;
	}

	$SYSTEM_FLAGS['info']['title']['item']	= $frow['title'];

	// Unpack form data
	$fData = unserialize($frow['struct']);
	if (!is_array($fData)) $fData = array();

	// Process link with news
	$link_news = intval(substr($frow['flags'], 3, 1));
	$nrow = '';
	$xfValues = array();
	if ($link_news > 0) {
		$linked_id = intval($_REQUEST['linked_id']);
		if (!$linked_id || !is_array($nrow = $mysql->record("select * from ".prefix."_news where (id = ". db_squote($linked_id) . ") and (approve = 1)"))) {
			// No link is provided, but link if required
			if ($link_news == 2) {

				$tVars = array(
					'title' => $lang['feedback:form.nolink.title'],
					'ptpl_url' => $ptpl_url,
					'entries' => $lang['feedback:form.nolink.description'],
				);

				$template['vars']['mainblock']      =  $xt->render($tVars);
				return 1;
			}
		} else {
			// Got data
			if (function_exists('xf_decode'))
				$xfValues = xf_decode($nrow['xfields']);

			$hiddenFields['linked_id']= $linked_id;
		}
	}

	// XFields values from user's profile
	$xfUserValues = array();
	if (function_exists('xf_decode') && isset($userROW['xfields']) && ($userROW['xfields'] != ''))
		$xfUserValues = xf_decode($userROW['xfields']);


	// Choose template to use
	if ($frow['template'] && file_exists(root.'plugins/feedback/tpl/templates/'.$frow['template'].'.tpl')) {
		$tP = root.'plugins/feedback/tpl/templates/';
		$tN = $frow['template'];
	} else {
		$tP = $tpath['site.form'];
		$tN = 'site.form';
	}

	$xt = $twig->loadTemplate($tP.$tN.'.tpl');
	$tVars = array(
		'ptpl_url'		=> $ptpl_url,
		'title'			=> $frow['title'],
		'name'			=> $frow['name'],
		'description'	=> $frow['description'],
		'id'			=> $frow['id'],
		'form_url'		=> generateLink('core', 'plugin', array('plugin' => 'feedback', 'handler' => 'post'), array()),
		'errorText'	=> $errorText,
		'flags'			=> array(
				'error'		=> ($errorText)?1:0,
				'link_news'	=> $link_news,
		),
	);

	if ($link_news) {
		$tVars['news'] = array(
			'id'		=> $nrow['id'],
			'title'		=> $nrow['title'],
			'url'		=> newsGenerateLink($nrow),

		);
	}

	$tEntries = array();

	$FBF_DATA = array();
	foreach ($fData as $fName => $fInfo) {
		$tEntry = array(
			'name'		=> 'fld_'.$fInfo['name'],
			'title'		=> $fInfo['title'],
			'type'		=> $fInfo['type'],
		);


		$FBF_DATA[$fName] = array($fInfo['type'], intval($fInfo['required']), iconv('Windows-1251', 'UTF-8', $fInfo['title']));

		// Fill value
		$setValue = '';

		if ($mode && (!$fInfo['block'])) {
			// FILLED EARLIER
			$setValue = secure_html($_REQUEST['fld_'.$fInfo['name']]);
		} else {
			// INITIAL SHOW
			$setValue = secure_html($fInfo['default']);

			// If 'by parameter' mode is set, check if this variable was passed in GET
			if (($fInfo['auto'] == 1) && isset($_REQUEST['v_'.$fInfo['name']])) {
				$setValue = secure_html($_REQUEST['v_'.$fInfo['name']]);
			} else if ($fInfo['auto'] == 2) {
				$setValue = secure_html($xfValues[$fInfo['name']]);
			} else if ($fInfo['auto'] == 3) {
				$setValue = secure_html($xfUserValues[$fInfo['name']]);
			}
		}

		switch ($fInfo['type']) {
			case 'text':
			case 'textarea':
			case 'email':
				$tEntry['value']	= $setValue;
				break;

			case 'date':
				// Prepare parsed date for show (in `show again` mode)
				$setValueDay	= $fInfo['default:vars']['day'];
				$setValueMonth	= $fInfo['default:vars']['month'];
				$setValueYear	= $fInfo['default:vars']['year'];
				if ($mode) {
					if ((intval($_REQUEST['fld_'.$fInfo['name'].':day']) >= 1) &&
						(intval($_REQUEST['fld_'.$fInfo['name'].':day']) <= 31) &&
						(intval($_REQUEST['fld_'.$fInfo['name'].':month']) >= 1) &&
						(intval($_REQUEST['fld_'.$fInfo['name'].':month']) <= 12) &&
						(intval($_REQUEST['fld_'.$fInfo['name'].':year']) >= 1970) &&
						(intval($_REQUEST['fld_'.$fInfo['name'].':year']) <= 2012)) {
						$setValueDay	= intval($_REQUEST['fld_'.$fInfo['name'].':day']);
						$setValueMonth	= intval($_REQUEST['fld_'.$fInfo['name'].':month']);
						$setValueYear	= intval($_REQUEST['fld_'.$fInfo['name'].':year']);
					}
				}

				$opts = $fInfo['required']?'':'<option value="">--</option>';
				for ($di = 1; $di <= 31; $di++) { $opts .= '<option value="'.$di.'"'.($di == $setValueDay?' selected="selected"':'').'>'.sprintf('%02u',$di).'</option>'; }
				$tEntry['options']['day'] = $opts;

				$opts = $fInfo['required']?'':'<option value="">--</option>';
				for ($di = 1; $di <= 12; $di++) { $opts .= '<option value="'.$di.'"'.($di == $setValueMonth?' selected="selected"':'').'>'.sprintf('%02u',$di).'</option>'; }
				$tEntry['options']['month'] = $opts;

				$opts = $fInfo['required']?'':'<option value="">--</option>';
				for ($di = 1970; $di <= 2012; $di++) { $opts .= '<option value="'.$di.'"'.($di == $setValueYear?' selected="selected"':'').'>'.$di.'</option>'; }
				$tEntry['options']['year'] = $opts;

				break;

			case 'select':
				$opts = '';
				if (is_array($fInfo['options']))
					foreach ($fInfo['options'] as $k => $v) {
						$opts .= '<option value="'.secure_html($v).'"'.($v == $setValue?' selected="selected"':'').'>'.secure_html($v).'</option>';
					}
				$tEntry['options']['select'] = $opts;
		}

		$tEntry['flags'] = array(
			'is_text'		=> ($fInfo['type'] == 'text'    )?1:0,
			'is_textarea'	=> ($fInfo['type'] == 'textarea')?1:0,
			'is_select'		=> ($fInfo['type'] == 'select')?1:0,
			'is_date'		=> ($fInfo['type'] == 'date')?1:0,
		);

		$tEntries []= $tEntry;

	}

	// Feel entries
	$tVars['entries'] = $tEntries;
	$tVars['FBF_DATA'] = json_encode($FBF_DATA);

	// Check if we need to check variable content via JScript
	if (substr($frow['flags'], 0, 1)) {
		$tVars['flags']['jcheck'] = 1;
	}

	// Check if we need captcha
	if (substr($frow['flags'],1,1)) {
		$tVars['flags']['captcha'] = 1;
		$tVars['captcha_url'] = admin_url."/captcha.php?id=feedback";
		$tVars['captcha_rand'] = rand(00000, 99999);

		$_SESSION['captcha.feedback'] = $tVars['captcha_rand'];
	}

	// Check if we need to show `select destination notification address` menu
	$em = unserialize($frow['emails']);
	if ($em === false) {
		$em[1]= array(1, '', preg_split("# *(\r\n|\n) *#", $frow['emails']));
	}

	if (count($em) > 1) {
		$tVars['flags']['recipients'] = 1;
		$options = '';
		foreach ($em as $er) {
			$options .= '<option value="'.$er[0].'">'.(($er[1] == '')?(join(', ', $er[2])):$er[1]).'</option>';
		}
		$tVars['recipients_list'] = $options;
	}


	// Prepare hidden fields
	$hF = '';
	foreach ($hiddenFields as $k => $v) {
		$hF .= '<input type="hidden" name="'.$k.'" value="'.htmlspecialchars($v).'"/>'."\n";
	}

	$tVars['hidden_fields'] = $hF;


	// Process filters (if any)
	if (is_array($PFILTERS['feedback']))
		foreach ($PFILTERS['feedback'] as $k => $v) { $v->onShow($form_id, $frow, $fData, &$tVars); }

	$template['vars']['mainblock']      =  $xt->render($tVars);

}


//
// Post feedback message
function plugin_feedback_post() {
	global $template, $lang, $mysql, $userROW, $SYSTEM_FLAGS, $PFILTERS, $twig;

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('site.form', 'site.notify', 'mail.html', 'mail.text'), 'feedback', pluginGetVariable('feedback', 'localsource'));
	$ptpl_url = admin_url.'/plugins/feedback/tpl';

	$form_id = intval($_REQUEST['id']);
	$SYSTEM_FLAGS['info']['title']['group']		= $lang['feedback:header.title'];


	$xt = $twig->loadTemplate($tpath['site.notify'].'site.notify.tpl');

	// Get form data
	if (!is_array($frow = $mysql->record("select * from ".prefix."_feedback where active = 1 and id = ".$form_id))) {
		$tVars = array(
			'title' => $lang['feedback:form.no.title'],
			'ptpl_url' => $ptpl_url,
			'entries' => $lang['feedback:form.no.description'],
		);

		$template['vars']['mainblock']      =  $xt->render($tVars);
		return 1;
	}

	$SYSTEM_FLAGS['info']['title']['item']	= str_replace('{title}', $frow['title'], $lang['feedback:header.send']);

	// Unpack form data
	$fData = unserialize($frow['struct']);
	if (!is_array($fData)) $fData = array();

	// Process link with news
	$link_news = intval(substr($frow['flags'], 3, 1));
	$nrow = '';
	$xfValues = array();
	if ($link_news > 0) {
		$linked_id = intval($_REQUEST['linked_id']);
		if (!$linked_id || !is_array($nrow = $mysql->record("select * from ".prefix."_news where (id = ". db_squote($linked_id) . ") and (approve = 1)"))) {
			// No link is provided, but link if required
			if ($link_news == 2) {

				$tVars = array(
					'title' => $lang['feedback:form.nolink.title'],
					'ptpl_url' => $ptpl_url,
					'entries' => $lang['feedback:form.nolink.description'],
				);

				$template['vars']['mainblock']      =  $xt->render($tVars);
				return 1;
			}
		} else {
			// Got data
			if (function_exists('xf_decode'))
				$xfValues = xf_decode($nrow['xfields']);
		}
	}


	// Check if captcha check if needed
	if (substr($frow['flags'],1,1)) {
		$vcode = $_REQUEST['vcode'];
		if ((!$vcode) || ($vcode != $_SESSION['captcha.feedback'])) {
			// Wrong CAPTCHA code (!!!)
			plugin_feedback_showScreen(1, $lang['feedback:sform.captcha.badcode']);
			return;
		}
	}

	// Check if user requested HTML message format
	$flagHTML = substr($frow['flags'], 2, 1) ? true : false;
	$mailTN = 'mail.'.($flagHTML?'html':'text');

	// Scan all fields and fill data. Prepare outgoing email.
	$output = '';
	$tVars = array(
		'flags' => array(
			'link_news' => ($linked_id>0)?1:0,
		),
		'form' => array(
			'id'			=> $frow['id'],
			'title'			=> $frow['title'],
			'description'	=> $frow['description'],

		),
		'values'	=> array(),
		'entries'	=> array(),
	);

	if ($linked_id > 0) {
		$tVars['news'] = array(
			'id'		=> $nrow['id'],
			'title'		=> $nrow['title'],
			'url'		=> newsGenerateLink($nrow, false, 0, true),
		);
	}

	$tEntries = array();
	$fieldValues = array();

	foreach ($fData as $fName => $fInfo) {
		switch ($fInfo['type']) {
			case 'date':	$fieldValue = $_REQUEST['fld_'.$fName.':day'] . '.' . $_REQUEST['fld_'.$fName.':month'] . '.' . $_REQUEST['fld_'.$fName.':year'];
		  					break;
			default:		$fieldValue = $_REQUEST['fld_'.$fName];
		}
		$fieldValues[$fName] = str_replace("\n", "<br/>\n", secure_html($fieldValue));

		$tEntry = array(
			'id' => $fName,
			'title' => secure_html($fInfo['title']),
			'value' => $fieldValues[$fName],
		);

		$tEntries []= $tEntry;
	}

	$tVars['entries'] = $tEntries;
	$tVars['values'] = $fieldValues;

	// Process filters (if any)
	if (is_array($PFILTERS['feedback']))
		foreach ($PFILTERS['feedback'] as $k => $v) { $v->onProcess($form_id, $frow, $fData, $flagHTML, &$tVars); }

	// Select recipient group
	$em = unserialize($frow['emails']);
	if ($em === false) {
		$em[1]= array(1, '', preg_split("# *(\r\n|\n) *#", $frow['emails']));
	}

	$elist = (isset($em[intval($_POST['recipient'])]))?$em[intval($_POST['recipient'])][2]:$em[1][2];
	$eGroupName = (isset($em[intval($_POST['recipient'])]))?$em[intval($_POST['recipient'])][1]:$em[1][1];


	// Prepare EMAIL content
	$mailSubject = str_replace(array('{name}', '{title}'), array($frow['name'], $frow['title']), $lang['feedback:mail.subj']);

	// Load template for ADMIN notification
	$xt = $twig->loadTemplate($tpath[$mailTN].$mailTN.'.tpl');
	// Render ADMIN email body
	$mailBody = $xt->render($tVars);


	$mailCount = 0;
	foreach ($elist as $email) {
		if (trim($email) == '')
			continue;

		$mailCount++;
		zzMail($email, $mailSubject, $mailBody, false, false, 'text/'.($flagHTML?'html':'plain'));
	}

	// Check if we need to send notification to user
	// -- list of user's email
	$eSendList = array();
	foreach ($fData as $fName => $fInfo) {
		$tfn = extras_dir.'/feedback/tpl/tmail/'.$fInfo['template'].'.'.($flagHTML?'html':'text').'.tpl';
		if (($fInfo['type'] == 'email') && ($fInfo['template'] != '') && (filter_var($fieldValues[$fName], FILTER_VALIDATE_EMAIL) !== false) && file_exists($tfn)) {
			$eSendList []= $fieldValues[$fName];
			$xtu = $twig->loadTemplate($tfn);
			// Render ADMIN email body
			$umailBody = $xtu->render($tVars);

			zzMail($fieldValues[$fName], $mailSubject, $umailBody, false, false, 'text/'.($flagHTML?'html':'plain'));
		}
	}

	$xt = $twig->loadTemplate($tpath['site.notify'].'site.notify.tpl');

	$tVars = array(
		'title' => $frow['title'],
		'ptpl_url' => $ptpl_url,
		'entries' => str_replace('{ecount}', $mailCount, $lang['feedback:confirm.message']),
		'usermail'	=> array(
			'count'		=> count($eSendList),
			'list'		=> $eSendList,
		),
	);

	$template['vars']['mainblock']      =  $xt->render($tVars);

	// Lock used captcha code if captcha is enabled
	if (substr($frow['flags'],1,1)) {
//		$_SESSION['captcha.feedback'] = rand(00000, 99999);
	}

	// Do post processing notification
	if (is_array($PFILTERS['feedback']))
		foreach ($PFILTERS['feedback'] as $k => $v) { $v->onProcessNotify($form_id); }


}