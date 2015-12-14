<?php

//
// Class for managing feedback forms
class FeedbackFilter {

	// Action executed when form is showed
	function onShow($formID, $formStruct, $formData, &$tvars) {

	}

	// Action executed when form is posted
	function onProcess($formID, $formStruct, $formData, $flagHTML, &$tVars) {

	}

	// EXTENDED: Action executed when form is posted, should return TRUE to continue processing
	function onProcessEx($formID, $formStruct, $formData, $flagHTML, &$tVars, &$tResult) {
		return true;
	}

	// EXTENDED: Send processor
	function onSendEx($formID, $formStruct, $formData, $msgData, $tVars, &$tResult) {
		return false;
	}

	// Action executed when post request is completed
	function onProcessNotify($formID){

	}

}


// Find list of available templates
function feedback_listTemplates() {

	// Scan available directories
	$dScan = array(
		tpl_site.'/plugins/feedback/custom' => ':',
		extras_dir.'/feedback/tpl/custom' => '',
	);

	$dList = array();

	// Check if there are custom templates in site template
	foreach ($dScan as $dPath => $dPrefix) {
		if (is_dir($dPath) && ($dh = opendir($dPath))) {
			while (($fn = readdir($dh)) !== false) {
				if (in_array($fn, array('.', '..')))
					continue;
				if (is_dir($dPath.'/'.$fn))
					$dList []= $dPrefix.$fn;
			}
		}
	}
	return $dList;
}

// Find location of template files for specific template
function feedback_locateTemplateFiles($tName, $flagHTML){

	// Determine template names/path, that will be used during form generation
	$tpath = locatePluginTemplates(array('site.form', 'site.notify', 'mail.html', 'mail.text'), 'feedback', pluginGetVariable('feedback', 'localsource'));

	// Default
	$tpDisplayPath	= $tpath['site.form'];
	$tpDisplay		= $tpDisplayPath.'site.form.tpl';
	$tpDisplayFound	= 0;

	$tpMailPath		= $tpath['mail.'.($flagHTML?'html':'text')];
	$tpMail			= $tpMailPath.'mail.'.($flagHTML?'html':'text').'.tpl';
	$tpMailFound	= 0;

	if ($tName) {
		// --- Site template
		if ((substr($tName, 0, 1) == ':')) {
			// -- Display template
			if (file_exists(tpl_site.'plugins/feedback/custom/'.substr($tName,1).'/site.form.tpl')) {
				$tpDisplayPath	= tpl_site.'plugins/feedback/custom/'.substr($tName, 1).'/';
				$tpDisplay		= $tpDisplayPath.'site.form.tpl';
				$tpDisplayFound	= 1;
			}
			// -- Mail template
			if (file_exists(tpl_site.'plugins/feedback/custom/'.substr($tName,1).'/mail.'.($flagHTML?'html':'text').'.tpl')) {
				$tpMailPath		= tpl_site.'plugins/feedback/custom/'.substr($tName, 1).'/';
				$tpMail			= $tpMailPath.'mail.'.($flagHTML?'html':'text').'.tpl';
				$tpMailFound = 1;
			}

		} else {
			// --- Plugin template
			// -- Display template
			if (file_exists(root.'plugins/feedback/tpl/custom/'.$tName.'/site.form.tpl')) {
				$tpDisplayPath	= root.'plugins/feedback/tpl/custom/'.$tName.'/';
				$tpDisplay		= $tpDisplayPath . 'site.form.tpl';
				$tpDisplayFound = 1;
			}
			// -- Mail template
			if (file_exists(root.'plugins/feedback/tpl/custom/'.$tName.'/mail.'.($flagHTML?'html':'text').'.tpl')) {
				$tpMailPath		= root.'plugins/feedback/tpl/custom/'.$tName.'/';
				$tpMail			= $tpMailPath.'mail.'.($flagHTML?'html':'text').'.tpl';
				$tpMailFound = 1;
			}
		}
	}

	return array(
		'site'	=> array(
			'path'		=> $tpDisplayPath,
			'file'		=> $tpDisplay,
			'isFound'	=> $tpDisplayFound,
		),
		'mail'	=> array(
			'path'		=> $tpMailPath,
			'file'		=> $tpMail,
			'isFound'	=> $tpMailFound,
		),
	);
}