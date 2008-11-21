<?

//
// RSS parser library
//

define('2z', '1');
include_once 'Y:\home\snap.align.ru\www\2z\includes\classes\parse.class.php';

class RSS_Parser {
	// Parse RSS feed.
	// Params:
	// * $content - data content
	// * $tgtenc  - target encoding. If specified - try to convert data
	function RSS_Parser($content, $tgtenc = '') {
		// First - check for XML header
		if (!preg_match('/^\s*\<\?xml (.*?)\?\>/', $content, $match)) {
			// Non XML header
			return false;
		}

		// Now let's scan params line & fetch encoding
		$params = parse::parseBBCodeParams($match[1]);
		$srcenc = strtolower($params['encoding']);

		// Ok, now we are ready to run XML parser
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		$result = xml_parse_into_struct($parser, $content, $values, $tags);
		xml_parser_free($parser);

		// Return if parsing was failed
		if (!$result)
			return false;

		print "TRUE";

	}
}


$data = file_get_contents('Y:\home\snap.align.ru\www\2z\extras\rss_show\inc\rss.xml');
$rss = new RSS_Parser($data);

/*
class XML_RSS_Enc extends XML_RSS {
	function utf2win_init() {
		$conv = array(208,144,192,208,145,193,208,146,194,208,147,195,208,148,196,208,149,197,208,129,168,208,150,198,208,151,199,208,152,200,208,153,201,208,154,202,208,155,203,208,156,204,208,157,205,208,158,206,208,159,207,208,160,208,208,161,209,208,162,210,208,163,211,208,164,212,208,165,213,208,166,214,208,167,215,208,168,216,208,169,217,208,170,218,208,171,219,208,172,220,208,173,221,208,174,222,208,175,223,208,176,224,208,177,225,208,178,226,208,179,227,208,180,228,208,181,229,209,145,184,208,182,230,208,183,231,208,184,232,208,185,233,208,186,234,208,187,235,208,188,236,208,189,237,208,190,238,208,191,239,209,128,240,209,129,241,209,130,242,209,131,243,209,132,244,209,133,245,209,134,246,209,135,247,209,136,248,209,137,249,209,138,250,209,139,251,209,140,252,209,141,253,209,142,254,209,143,255);
		for ($i=0; $i<count($conv)/3; $i++) {
			$this->ac[chr($conv[$i*3]).chr($conv[$i*3+1])]=chr($conv[$i*3+2]);
		}
	}


	//
	// catch encoding
	//
	var $srcRusEnc = '';
	var $tgtRusEnc = '';

	function defaultHandler($xp, $cdata) {
	        if ((substr($cdata,0,6) == '<?xml ')&&(preg_match("/ encoding=(\"|')(.+)(\"|')/",$cdata,$m))) {
	                $this->encoding = strtolower($m[2]);
	        }
	        return parent::defaultHandler($xp, $cdata);
	}

	//
	// New constructor
	//
	function XML_RSS_Enc($handle = '', $srcenc = null, $tgtenc = null) {
		if (strtolower($srcenc) == 'windows-1251') {
			$this->srcRusEnc = 'windows-1251';
			$srcenc = null;
		}
		if (strtolower($tgtenc) == 'windows-1251') {
			$this->tgtRusEnc = 'windows-1251';
			$tgtenc = null;
		}

		//
		// Use own socket reading mechanism to avoid possible big timeouts in default scheme
		//
	        if (eregi('^(http|ftp)://', substr($handle, 0, 10))) {
	        	@include_once "httpget.inc.php";
	        	$http = new http_get;
	        	$handle = $http->get($handle);
	        }
	        //
	        // Avoid error message due to bug in XML_Parser class - wrong call file_exists()
	        //
	        @error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);
		$res = parent::XML_RSS($handle);
		@error_reporting (E_ALL ^ E_NOTICE);
		return $res;
	}

	//
	// Parser catcher. Do after-process conversion if needed
	//
	function parse() {
		$res = parent::parse();

		// Exit if we don't need a conversion
		if (!(($this->encoding == 'utf-8') && ($this->tgtRusEnc == 'windows-1251'))) {
			return $res;
		}

		// Init utf2win converter
		XML_RSS_Enc::utf2win_init();

		if (is_array($this->channel)) {
			if (isset($this->channel['title'])) { $this->channel['title'] = XML_RSS_Enc::utf2win($this->channel['title']); }
			if (isset($this->channel['description'])) { $this->channel['description'] = XML_RSS_Enc::utf2win($this->channel['description']); }
		}

		if(is_array($this->items)) {
			for ($i=0; $i<count($this->items);$i++) {
				$item = &$this->items[$i];
				if (isset($item['title']))       { $item['title']       = XML_RSS_Enc::utf2win($item['title']); };
				if (isset($item['description'])) { $item['description'] = XML_RSS_Enc::utf2win($item['description']); };
			}
		}

		return $res;
	}

	function utf2win($text) {
		$u=0;
		$res='';
		$u1=chr(208); $u2=chr(209);
		for ($i=0; $i<strlen($text);$i++){
			if (($text[$i] == $u1)||($text[$i] == $u2)) { $u=1; continue; }
			if ($u) { $c=$this->ac[$text[$i-1].$text[$i]]; $res.= isset($c)?$c:'?'; $u=0; }
			else { $res.=$text[$i]; }
		}
		return $res;
	}
}
*/
?>