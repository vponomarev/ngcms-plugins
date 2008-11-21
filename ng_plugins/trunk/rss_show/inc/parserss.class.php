<?php

//
// RSS parsing class
//
class parseRSS {

	// CONSTRUCTOR
	// Calling params - array with config
	// default_incoming_encoding - encoding to be used if not specified in <?xml info
	// outgoing_encoding - encoding that will be used as output
	function parseRSS($params = array()) {
		$this->config = array();
		$this->config['default_incoming_encoding'] = (isset($params['default_incoming_encoding']) && in_array($params['default_incoming_encoding'], array('utf-8', 'windows-1251')))?$params['default_incoming_encoding']:'utf-8';
		$this->config['outgoing_encoding'] = (isset($params['outgoing_encoding']) && in_array($params['outgoing_encoding'], array('utf-8', 'windows-1251')))?$params['outgoing_encoding']:'utf-8';
	}

	function scanURL($url){
		@include_once root.'includes/inc/httpget.inc.php';
		$http = new http_get();
		list ($status, $header, $body) = $http->request('GET', $url);

		if (!$status)
			return (array('error' => array(99, 'Socket fetch error')));
		return $this->scanData($body);
	}

	function scanData($data){
		// Check for correct XML header and charset
		if (!preg_match('/^ *<\?xml (.+?)\?>/', $data, $match)) {
			// No XML header
			return (array('error' => array(1, 'No XML header')));
		}
		$params = parseParams($match[1]);

		// Determine encoding
		$encoding = (isset($params['encoding'])&&(in_array($params['encoding'], array('utf-8', 'windows-1251'))))?$params['encoding']:$this->config['default_incoming_encoding'];

		//
		if ($encoding != $this->config['outgoing_encoding'])
			$this->utf2win_init();

		// Parse XML
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		$parseResult = xml_parse_into_struct($parser, $data, $values, $tags);
		xml_parser_free($parser);

		// Check if parsing is completed
		if (!$parseResult) {
			return (array('error' => array(2, 'Error parsing XML')));
		}

		// Now check if we have RSS header (it should be the first tag) and tag count is not less than 4
		if (count($values) < 4) {
			return (array('error' => array(3, 'Too few tags - non-RSS XML document')));
		}

		if (($values[0]['tag'] != 'rss')||($values[0]['type'] != 'open')) {
			return (array('error' => array(4, 'Non-RSS XML document')));
		}

		// Make version check. At the current moment we can work only with versions 0.9 and 2.0
		if (isset($values[0]['attributes']['version']) && ($values[0]['attributes']['version'] != '2.0') && ($values[0]['attributes']['version'] != '0.9')) {
			return (array('error' => array(5, 'Only RSS versions 0.9 and 2.0 are supported at the current moment')));
		}


		// Start scanning tags
		// We're interested with tags within tag "channel" (level = 3)
		$cnt = count($values);

		// State of "end state machine"
		// 0 - Waiting for 'item' tag
		// 1 - Scanning 'item' tag
		$cstatus = 0;
		$tagIndex = 0;
		$tagName = '';

		$rssChannelInfo = array();
		$rssItems = array();

		for ($i=2; $i <= $cnt; $i++) {
			$tag = &$values[$i];
			if (!$cstatus) {
				// Check for opening of tag 'item'
				if (($tag['tag'] == 'item')&&($tag['type'] == 'open')) {
					$cstatus = 1;
					$tagIndex = $i;
					$tagName = 'item';
					$itemData = array();
					continue;
				}

				// Channel parameters, should be on level 3
				if	(($tag['level'] == 3)&&
					($tag['type'] == 'complete')&&
					(in_array($tag['tag'], array('title', 'link', 'description', 'language', 'pubDate', 'lastBuildDate', 'language', 'generator'))))
					{
							$rssChannelInfo[$tag['tag']] = ($encoding != $this->config['outgoing_encoding'])?iconv($encoding, $this->config['outgoing_encoding'], $tag['value']):$tag['value'];
					}
			} else {
				// Check for closing of tag 'item'
				if (($tag['tag'] == 'item')&&($tag['type'] == 'close')) {
					$cstatus = 0;
					$rssItems[] = $itemData;
					continue;
				}

				// Отрабатываем только теги на уровне [3] (внутри item'а)
				if (($tag['level'] == 4)&&($tag['type'] == 'complete')&&(in_array($tag['tag'], array('category', 'title', 'link', 'description', 'pubdate', 'guid', 'pubDate')))) {
					if (isset($tag['value'])) {
						$itemData[$tag['tag']] = ($encoding != $this->config['outgoing_encoding'])?iconv($encoding, $this->config['outgoing_encoding'], $tag['value']):$tag['value'];
					}
				}
			}
		}

		// Ok, return results
		return array('channel' => $rssChannelInfo, 'items' => $rssItems);
	}

	function utf2win_init() {
		$conv = array(208,144,192,208,145,193,208,146,194,208,147,195,208,148,196,208,149,197,208,129,168,208,150,198,208,151,199,208,152,200,208,153,201,208,154,202,208,155,203,208,156,204,208,157,205,208,158,206,208,159,207,208,160,208,208,161,209,208,162,210,208,163,211,208,164,212,208,165,213,208,166,214,208,167,215,208,168,216,208,169,217,208,170,218,208,171,219,208,172,220,208,173,221,208,174,222,208,175,223,208,176,224,208,177,225,208,178,226,208,179,227,208,180,228,208,181,229,209,145,184,208,182,230,208,183,231,208,184,232,208,185,233,208,186,234,208,187,235,208,188,236,208,189,237,208,190,238,208,191,239,209,128,240,209,129,241,209,130,242,209,131,243,209,132,244,209,133,245,209,134,246,209,135,247,209,136,248,209,137,249,209,138,250,209,139,251,209,140,252,209,141,253,209,142,254,209,143,255);
		for ($i=0; $i<count($conv)/3; $i++) {
			$this->convert_utf2win[chr($conv[$i*3]).chr($conv[$i*3+1])]=chr($conv[$i*3+2]);
		}
		$this->convert_win2utf = array_flip($this->convert_utf2win);
	}

	function utf2win($text) {
		$u=0;
		$res='';
		$u1=chr(208); $u2=chr(209);
		$len = strlen($text);
		for ($i=0; $i<$len;$i++){
			if (($text[$i] == $u1)||($text[$i] == $u2)) { $u=1; continue; }
			if ($u) { $c=$this->convert_utf2win[$text[$i-1].$text[$i]]; $res.= ($c!='')?$c:'?'; $u=0; }
			else { $res.=$text[$i]; }
		}
		return $res;
	}

	function win2utf($text){
		$len = strlen($text);
		$res = '';
		for ($i=0; $i<$len; $i++) {
			$sym = $text[$i];
			$res .= isset($this->convert_win2utf[$sym])?$this->convert_win2utf[$sym]:$sym;
		}
		return $res;
	}
}
