<?php

//
// Process RSS file [ build 1 2007-08-25 ]
//

class RSS_PROCESSOR{
	function scan($url){
		// try to include XML/RSS
		@include_once("XML/RSS.php");
		if (class_exists('XML_RSS')) {
			include_once("rss.rus.inc.php");
			// We have PEAR XML/RSS class
			$rss = new XML_RSS_Enc($url,'','Windows-1251');
			if ($rss->parse()) {
				$parseResult = array ( 'channel' => $rss->getChannelInfo(), 'items' => $rss->getItems());
			}
		} else {
			// We do not have PEAR XML/RSS class
	        	@include_once "httpget.inc.php";
	        	$http = new http_get;
	        	$line = $http->get("http://dev.2z-project.com/helper/rss.php?url=".$url);

			if ($line) {
			        $parseResult = unserialize($line);
			}
		}
		$this->result = $parseResult;
		if (is_array($parseResult)) {
			return 1;
		} else {
			return 0;
		}	
	}
}

?>