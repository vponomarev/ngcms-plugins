<?php

//
// Configurable HTTP GET with timeout support
//

class http_get {
	// Split URL into host, port and path
	function parse_url($url){
		$host = $path = '';
		$port = 80;
		if (preg_match('/^http\:\/\/(.+?)\/(.*)$/',$url,$match)) {
			$host = $match[1];
			$path = $match[2];
			if (preg_match('/^(.+?)\:(\d+)$/', $host, $match)) {
				$host = $match[1];
				$port = $match[2];
			}
		}
		return array($host, $port, $path);
	}

	function get($url, $timeout = 3) {
		// Split URL into host and path
		list ($host, $port, $path) = http_get::parse_url($url);
		if (!$host) { return ''; }
		$fp = @fsockopen($host, $port);
		if (!$fp) { return false; }
		fputs($fp,"GET /$path HTTP/1.0\r\nHost: $host\r\nConnection: close\r\n\r\n");
		socket_set_timeout($fp, $timeout);

		// Try to read data, not more than 1 Mb
		$data = fread($fp, 1024 * 1024);
		fclose($fp);

		// Try to parse data
		if ($pos = strpos($data, "\r\n\r\n")) {
			$header = substr($data,0,$pos);
			$data = substr($data,$pos+4);
		} else {
			// HTTP header/body splitter not found. No body given
			$header = $data;
			$data = '';
		}

		// Let's analyse header
		$hdr = explode("\r\n",$header);
		$status = 0;
		if ($hdr[0] && preg_match('/^HTTP\/1.\d +(\d+) +(.+)$/i', $hdr[0], $match)) {
			// Found status string
			$status = $match[1];
		}

		if ($status != 200) {
			return false;
		}

		return $data;
	}
}

?>
