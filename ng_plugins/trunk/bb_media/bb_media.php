<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');


function bbMediaProcess($content) {
	global $config, $parse;
	if (preg_match_all("#\[media(\=| *)(.*?)\](.*?)\[\/media\]#is", $content, $pcatch, PREG_SET_ORDER)) {
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
			$keys['file'] = ((!isset($keys['file']) || !$keys['file'])?$alt:$keys['file']);

			// Let's extract file extension and try to retrieve file type
			$fileExt = strtolower(substr(strrchr($keys['file'], "."), 1));
			switch ($fileExt) {
				case 'mp3':
				case 'aac':
					$keys['type'] = 'sound';
					break;
				case 'mp4':
					$keys['type'] = 'video';
					break;
				case 'pdf':
					$keys['type'] = 'pdf';
			};

			// Check required keys
			$kdefault = array(
				'width'		=> array('sound' => 320, 'video' => 320, 'pdf' => '100%'),
				'height'	=> array('sound' => 20, 'video' => 200, 'pdf' => '350')
			);

			foreach ($kdefault as $kscan => $kvalue) {
				if (isset($keys[$kscan]) && preg_match("#^(\d+)(\%){0,1}$#", $keys[$kscan], $m)) {
					if (isset($m[2]) && ($m[2] == '%')) {
						if (($m[1] > 5) && ($m[1] <= 100)) {
							$keys[$kscan] = $m[1].$m[2];
						} else {
							$keys[$kscan] = $kvalue[$keys['type']];
						}
					} else {
						if (($m[1] > 10) && ($m[1] < 2048)) {
							$keys[$kscan] = $m[1];
						} else {
							$keys[$kscan] = $kvalue[$keys['type']];
						}
					}
				} else {
					$keys[$kscan] = $kvalue[$keys['type']];
				}
			}

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
						$outkeys [] = $kn.'="'.$kv.'"';
						break;

				}
			}

			// Prepare output keys for `flashvars`
			$outfkeys = array();
			// - main file
			$outfkeys []= 'file='.urlencode($keys['file']);
			// - preview image
			if (isset($keys['preview']) && preg_match("#^http\:\/\/.*?\.(png|jpg)$#i", $keys['preview'], $m)) {
				$outfkeys []= 'image='.urlencode($keys['preview']);
			}


			// Fill an output replacing array
			if ($fileExt == 'pdf') {
				array_push($rdest, '<object type="application/pdf" data="'.$keys['file'].'" '.(implode(" ", $outkeys)).'>alt: <a href="'.$keys['file'].'">PDF document</a></object>');
			} else {
				//						array_push($rdest, '<embed type="application/x-shockwave-flash" src="'.$config['admin_url'].'/plugins/bb_media/swf/player.swf" quality="high" allowfullscreen="true" allowscriptaccess="always" flashvars="file='.urlencode($keys['file']).'" '.(implode(" ", $outkeys)).' />');
				array_push($rdest, '<embed type="application/x-shockwave-flash" src="'.$config['admin_url'].'/plugins/bb_media/swf/player.swf" quality="high" allowfullscreen="true" allowscriptaccess="always" flashvars="'.implode('&amp;', $outfkeys).'" '.(implode(" ", $outkeys)).' />');
			}
		}
		return	str_replace($rsrc, $rdest, $content);
	}
	return false;
}


class BBmediaNewsfilter extends NewsFilter {
	// Add {plugin_similar} variable into news
	function showNews($newsID, $SQLnews, &$tvars, $mode = array()) {
		if (($t = bbMediaProcess($tvars['vars']['short-story'])) !== false)		{	$tvars['vars']['short-story'] = $t;			}
		if (($t = bbMediaProcess($tvars['vars']['full-story'])) !== false)		{	$tvars['vars']['short-story'] = $t;			}

		if (($t = bbMediaProcess($tvars['vars']['news']['short'])) !== false)	{	$tvars['vars']['news']['short'] = $t;		}
		if (($t = bbMediaProcess($tvars['vars']['news']['full'])) !== false)	{	$tvars['vars']['news']['full'] = $t;		}
	}
}

// Preload plugin tags
register_filter('news','bb_media', new BBmediaNewsFilter);

