<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

class BBmediaNewsfilter extends NewsFilter {
	// Add {plugin_similar} variable into news
	function showNews($newsID, $SQLnews, &$tvars, $mode) {
		global $config, $parse;

		foreach (array('short-story', 'full-story') as $varKeyName) {
			if (preg_match_all("#\[media(\=| *)(.*?)\](.*?)\[\/media\]#is", $tvars['vars'][$varKeyName], $pcatch, PREG_SET_ORDER)) {
				$rsrc = array();
				$rdest = array();
				// Scan all URL tags
				foreach ($pcatch as $catch) {

					// Init variables
					list ($line, $null, $paramLine, $alt) = $catch;
					array_push($rsrc, $line);

					// Check for possible error in case of using "]" within params/url
					// Ex: [url="file[my][super].avi" target="_blank"]F[I]LE[/url] is parsed incorrectly
					if ((strpos($alt, ']') !== false) && (strpos($alt, "\"") !== false)) {
						// Possible bracket error. Make deep analysis
						$jline = $paramLine.']'.$alt;
						$brk = 0;
						$jlen = strlen($jline);
						for ($ji = 0; $ji < $jlen; $ji++) {
							if ($jline[$ji] == "\"") {
								$brk = !$brk;
								continue;
							}

							if ((!$brk) && ($jline[$ji] == ']')) {
								// Found correct delimiter
								$paramLine = substr($jline, 0, $ji);
								$alt = substr($jline, $ji+1);
								break;
							}
						}
					}

					$outkeys = array();

					// Make a parametric line with url
					if (trim($paramLine)) {
						// Parse params
						$keys = $parse->parseBBCodeParams((($null=='=')?'file=':'').$paramLine);
					} else {
						// No params to scan
						$keys = array();
					}
					                             
					// Return an error if BB code is bad
					if (!is_array($keys)) {
						array_push($rdest,'[INVALID MEDIA BB CODE]');
						continue;
					}

					// Now let's compose a resulting URL
					$keys['file'] = ((!$keys['file'])?$alt:$keys['file']);

					// Let's extract file extension and try to retrieve file type
					$fileExt = substr(strrchr($keys['file'], "."), 1);
					switch (strtolower($fileExt)) {
						case 'mp3':
						case 'aac':
							$keys['type'] = 'sound';
							break;
						case 'mp4':
							$keys['type'] = 'video';
							break;
					};

					// Check required keys
					$keys['width']  = (intval($keys['width']) > 10)?intval($keys['width']):'320';
					$keys['height'] = (intval($keys['height']) > 10)?intval($keys['height']):(($keys['type'] == 'sound')?'20':'200');

					// Return an error if BB code is bad
					if (!trim($keys['file'])) {
						array_push($rdest,'[INVALID file for MEDIA BB CODE]');
						continue;
					}


					// Now parse allowed tags and add it into output line
					foreach ($keys as $kn => $kv) {
						switch ($kn) {
							case 'width':
							case 'height':
								$outkeys [] = $kn.'="'.intval($kv).'"';
								break;

						}
					}
					// Fill an output replacing array
					
					array_push($rdest, '<embed type="application/x-shockwave-flash" src="'.$config['admin_url'].'/plugins/bb_media/swf/player.swf" quality="high" allowfullscreen="true" allowscriptaccess="always" flashvars="file='.urlencode($keys['file']).'" '.(implode(" ", $outkeys)).' />');
				}
				$tvars['vars'][$varKeyName] = str_replace($rsrc, $rdest, $tvars['vars'][$varKeyName]);
			}
		}
	}
}

// Preload plugin tags
register_filter('news','bb_media', new BBmediaNewsFilter);

