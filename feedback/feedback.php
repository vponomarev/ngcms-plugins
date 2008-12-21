<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

register_plugin_page('feedback','','plugin_feedback_screen',0);
register_plugin_page('feedback','post','plugin_feedback_post',0);

loadPluginLang('feedback', 'main', '', '', ':');

function plugin_feedback_screen() {
 global $template, $tpl, $lang, $mysql, $userROW;

 $ptpl_url = admin_url.'/plugins/feedback/tpl';

 // Determine paths for all template files
 $tpath = locatePluginTemplates(array('site.infoblock', 'site.form.hdr', 'site.form.row'), 'feedback', extra_get_param('feedback', 'localsource'));

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
 $tpl->template('site.form.row', $tpath['site.form.row']);
 foreach ($fData as $fName => $fInfo) {
 	$tvars = array();
 	$tvars['vars']['ptpl_url'] = $ptpl_url;
	$tvars['vars']['name']	= $fInfo['name'];
	$tvars['vars']['title']	= $fInfo['title'];

	switch ($fInfo['type']) {
		case 'text':
		case 'textarea':
			$tvars['vars']['value']	= $fInfo['default'];
			break;

		case 'select':
			$opts = '';
			if (is_array($fInfo['options']))
				foreach ($fInfo['options'] as $k => $v) {
					$opts .= '<option value="'.secure_html($v).'"'.($v == $fInfo['default']?' selected="selected"':'').'>'.secure_html($v).'</option>';
				}
			$tvars['vars']['options'] = $opts;
	}
	$tvars['regx']['#\[text\](.+?)\[\/text\]#is']			= ($fInfo['type'] == 'text'    )?'$1':'';
	$tvars['regx']['#\[textarea\](.+?)\[\/textarea\]#is']	= ($fInfo['type'] == 'textarea')?'$1':'';
	$tvars['regx']['#\[select\](.+?)\[\/select\]#is']		= ($fInfo['type'] == 'select'  )?'$1':'';

	$tpl->vars('site.form.row', $tvars);
	$output .= $tpl->show('site.form.row');

 }


 // Prepare params
 $tvars = array();
 $tvars['vars']['ptpl_url']		= $ptpl_url;
 $tvars['vars']['id']			= $frow['id'];
 $tvars['vars']['description']	= $frow['description'];
 $tvars['vars']['entries']		= $output;
 $tvars['vars']['form_url']		= GetLink('plugins', array('plugin_name' => 'feedback'));

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

function plugin_feedback_post() {
 global $template, $tpl, $lang, $mysql, $userROW;

 // Determine paths for all template files
 $tpath = locatePluginTemplates(array('site.infoblock', 'site.form.hdr', 'site.form.row'), 'feedback', extra_get_param('feedback', 'localsource'));

 $form_id = intval($_REQUEST['id']);

 if (!is_array($frow = $mysql->record("select * from ".prefix."_feedback where active = 1 and id = ".$form_id))) {
	$tpl->template('site.infoblock', $tpath['site.infoblock']);
	$tpl->vars('site.infoblock', array( 'vars' => array( 'title' => $lang['feedback:form.no.title'], 'entries' => $lang['feedback:form.no.description'])));
	$template['vars']['mainblock']      =  $tpl->show('site.infoblock');
	return 1;
 }

 // Unpack form data
 $fData = unserialize($frow['struct']);
 if (!is_array($fData)) $fData = array();

 // Scan all fields and fill data. Prepare outgoing email.
 $output = '';

 foreach ($fData as $fName => $fInfo) {
  $output .= '['.$fName.'] '.$fInfo['title'].': '.$_REQUEST[$fName]."<br/>\n";
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
 $tpl->vars('site.infoblock', array( 'vars' => array( 'title' => $frow['title'], 'entries' => str_replace('{ecount}', $mailCount, $lang['feedback:confirm.message']))));
 $template['vars']['mainblock']      =  $tpl->show('site.infoblock');
}
