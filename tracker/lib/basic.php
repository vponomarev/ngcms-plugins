<?php

// BEncode class from PEAR File/Bittorrent library

class bt_bencode {
    function encode($mixed)
    {
        switch (gettype($mixed)) {
        case is_null($mixed):
            return $this->encode_string('');
        case 'string':
            return $this->encode_string($mixed);
        case 'integer':
        case 'double':
            return $this->encode_int(sprintf('%.0f', round($mixed)));
        case 'array':
            return $this->encode_array($mixed);
        default:
            return false;
        }
    }

    function encode_string($str)
    {
        return strlen($str) . ':' . $str;
    }

    function encode_int($int)
    {
        return 'i' . $int . 'e';
    }

    function encode_array(array $array)
    {
        // Check for strings in the keys
        $isList = true;
        foreach (array_keys($array) as $key) {
            if (!is_int($key)) {
                $isList = false;
                break;
            }
        }
        if ($isList) {
            // Wie build a list
            ksort($array, SORT_NUMERIC);
            $return = 'l';
            foreach ($array as $val) {
                $return .= $this->encode($val);
            }
            $return .= 'e';
        } else {
            // We build a Dictionary
            ksort($array, SORT_STRING);
            $return = 'd';
            foreach ($array as $key => $val) {
                $return .= $this->encode(strval($key));
                $return .= $this->encode($val);
            }
            $return .= 'e';
        }
        return $return;
    }
}

//
// Fetch status of requested torrent
// $info_hash		- torrent INFO_HASH parameter
// $mode			- processing mode
//		info		- return general info (status, number of leeches/seeders
//		report		- operate user's report, return all params for building answer
// $timeout			- peers cache timeout
function tracker_fetch_tstatus($info_hash, $mode, $timeout = 60, $params = array()) {
	global $ip;

	// Try to get cache directory name. Return false if it's not possible
	if (!($dir = get_plugcache_dir('tracker'))) {
		return false;
	}

	$fname = $dir.$info_hash.'.hash';
	// Try to open file
	if (($fn = @fopen($fname, 'r+')) == FALSE) {
		// For `info` mode - return false
		if ($mode == 'info')
			return false;

		// For `report` - create a file
		touch($fname);

		// Try to open file again
		if (($fn = @fopen($fname, 'r+')) == FALSE) {
			return false;
		}
	}

	// Try to make exclusive file lock. Return if failed
	if (@flock($fn, LOCK_EX) == FALSE) {
		fclose($fn);
		return false;
	}

	// Now we have exclusive access to data file. Let's read peer data
	$fsize = filesize($fname);

	if ($fsize) {
		$peers = unserialize(fread($fn, $fsize));
		if (!is_array($peers))
			$peers = array();
	} else {
		$peers = array();
	}

	// Generate new peers list
	$npeers = array();
	$flag_updated = false;
	$our_row = -1;
	$nrows = 0;

	foreach ($peers as $peer) {
		// Check for expiration
		if (($peer['last_update'] + intval($timeout*1.3)) < time()) {
			$flag_updated = true;
			continue;
		}

		// Check for us (in `report` mode)
		if (($mode == 'report') && ($peer['peer_id'] == $params['peer_id']) && ($peer['real_ip'] == $ip)) {
			$flag_updated = true;
			// Delete if event is `stopped`
			if (isset($params['event']) && ($params['event'] == 'stopped')) {
				$our_row = -2;
				continue;
			}
			$our_row = $nrows;
		}

		$npeers[] = $peer;
		$nrows++;
	}

	if ($mode == 'report') {
		// Allocate record code for us
		if ($our_row == -1) {
			$flag_updated = true;
			$our_row = $nrows;
		}

		// Write record only if we didn't deleted row a bit earlier
		if ($our_row >= 0) {
			foreach (array('peer_id', 'ip', 'port', 'uploaded', 'downloaded', 'left', 'agent') as $k) {
				if (isset($params[$k]))
					$npeers[$our_row][$k] = $params[$k];
			}
			$npeers[$our_row]['real_ip'] = $ip;
			$npeers[$our_row]['last_update'] = time();
		}
	}

	// Update file if needed
	if ($flag_updated) {
		// Truncate file
		ftruncate($fn, 0);
		rewind($fn);

		// Now let's write data back to file
		$data = serialize($npeers);
		fwrite($fn, $data);
	}

	// Unlock file
	flock($fn, LOCK_UN);
	fclose($fn);

	// Report results
	return $npeers;
}

if (!function_exists('hex2bin')) {
	function hex2bin($h) {
		if (!is_string($h)) return null;
		$r='';
		for ($a=0; $a<strlen($h); $a+=2) { $r.=chr(hexdec($h{$a}.$h{($a+1)})); }
		return $r;
	}
}


// Decode magnet link into parts
function magnetDecodeLink($link){
	if (!preg_match('#^magnet\:\?(.+)$#', $link, $m))
		return false;

	//print "magnetDecodeLink()<br>\n";
	$data = array();
	foreach (explode('&', $m[1]) as $kv) {
		list($k, $v) = explode('=', $kv, 2);
		//print "> `$k` = `$v`<br/>\n";
		switch ($k) {
			case 'xl':
				$data['size'] = intval($v);
				break;
			case 'tr':
				$data['torrent.tracker'] = urldecode($v);
				break;
			case 'dn':
				$data['name'] = urldecode($v);
				break;
			case 'xt':
				if (preg_match('#^urn\:btih\:([a-zA-Z0-9]{40})#', $v, $null)) {
					$data['torrent.infohash'] = $null[1];
				}
		}
	}
	//print "<pre>".var_export($data, true)."</pre>";
	return $data;
}


