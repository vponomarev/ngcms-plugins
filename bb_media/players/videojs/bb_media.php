<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
add_act('index', 'bbMediaInclude');
function bbMediaInclude() {

	register_htmlvar('css', admin_url . '/plugins/bb_media/players/videojs/lib/video-js.min.css');
	register_htmlvar('js', admin_url . '/plugins/bb_media/players/videojs/lib/ie8/videojs-ie8.min.js');
	register_htmlvar('js', admin_url . '/plugins/bb_media/players/videojs/lib/video.min.js');
	register_htmlvar('plain', '<script> videojs.options.flash.swf = "' . admin_url . '/plugins/bb_media/players/videojs/lib/video-js.swf"; </script>');
	register_htmlvar('js', admin_url . '/plugins/bb_media/players/videojs/lib/plugins/youtube/youtube.min.js');
}

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
				$jline = $paramLine . ']' . $alt;
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
						$alt = substr($jline, $ji + 1);
						break;
					}
				}
			}
			$outkeys = array();
			// Make a parametric line with url
			if (trim($paramLine)) {
				// Parse params
				$keys = $parse->parseBBCodeParams((($null == '=') ? 'file=' : '') . $paramLine);
			} else {
				// No params to scan
				$keys = array();
			}
			// Return an error if BB code is bad
			if (!is_array($keys)) {
				array_push($rdest, '[INVALID MEDIA BB CODE]');
				continue;
			}
			// Now let's compose a resulting URL
			$keys['file'] = ((!isset($keys['file']) || !$keys['file']) ? $alt : $keys['file']);
			// Let's extract file extension and try to retrieve file type
			$fileExt = strtolower(substr(strrchr($keys['file'], "."), 1));
			switch ($fileExt) {
				case 'mp4':
					$keys['type'] = 'video';
					break;
			};
			// Check required keys
			$kdefault = array(
				'width'  => array('audio' => 200, 'video' => 320, 'pdf' => '200'),
				'height' => array('audio' => 200, 'video' => 200, 'pdf' => '200')
			);
			foreach ($kdefault as $kscan => $kvalue) {
				if (isset($keys[$kscan]) && preg_match("#^(\d+)(\%){0,1}$#", $keys[$kscan], $m)) {
					if (isset($m[2]) && ($m[2] == '%')) {
						if (($m[1] > 5) && ($m[1] <= 100)) {
							$keys[$kscan] = $m[1] . $m[2];
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
				array_push($rdest, '[INVALID file for MEDIA BB CODE]');
				continue;
			}
			// youtube links
			$rx = '~
            ^(?:https?://)?             # Optional protocol
            (?:www\.)?                  # Optional subdomain
            (?:youtube\.com|youtu\.be)  # Mandatory domain name
            /(?:watch\?v=)?([^&]+)      # URI with video id as capture group 1
            ~x';
			$has_match_youtube = preg_match($rx, $keys['file'], $matches);
			if ($matches[1]) {
				$src = '';
				$data_setup = '{ "techOrder": ["youtube", "html5"], "sources": [{ "type": "video/youtube", "src": "' . $keys['file'] . '"}] }';
				if ($keys['width'] == null) {
					$keys['width'] = $kdefault['width']['video'];
				}
				if ($keys['height'] == null) {
					$keys['height'] = $kdefault['height']['video'];
				}
			} else {
				$src = "<source src='" . $keys['file'] . "' type='video/mp4' />";
				$data_setup = '';
			}
			// Now parse allowed tags and add it into output line
			foreach ($keys as $kn => $kv) {
				switch ($kn) {
					case 'width':
					case 'height':
					case 'autoplay':
					case 'loop':
					case 'muted':
						$outkeys [] = $kn . '="' . $kv . '"';
						break;
					case 'preload':
						//case 'autoplay':
						//case 'loop':
						//case 'muted':
						$outkeys [] = $kn;
						break;
				}
			}
			if (isset($keys['controls'])) {
				if ($keys['controls'] == "true") {
					$outkeys [] = 'controls';
				} else if ($keys['controls'] == "false") {
					;
				}
			} else {
				$outkeys [] = 'controls';
			}
			// - preview image
			if (isset($keys['preview']) && preg_match("#^http\:\/\/.*?\.(png|jpg|gif)$#i", $keys['preview'], $m)) {
				$poster = "poster='" . $keys['preview'] . "'";
			} else {
				$poster = "";
			}
			array_push($rdest, "<video class='video-js vjs-default-skin vjs-big-play-centered' " . (implode(' ', $outkeys)) . " " . $poster . " data-setup='" . $data_setup . "'>" . $src . "</video>");
		}

		return str_replace($rsrc, $rdest, $content);
	}

	return false;
}
