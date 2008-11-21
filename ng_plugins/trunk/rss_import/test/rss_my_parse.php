<?

require "XML/RSS.php";

class XML_RSS_Enc extends XML_RSS {
	function defaultHandler($xp, $cdata) {
	        if ((substr($cdata,0,6) == '<?xml ')&&(preg_match("/ encoding=(\"|')(.+)(\"|')/",$cdata,$m))) {
	                $this->encoding = $m[2];
	        }	
	}	
}


$rss = new XML_RSS_Enc('c:\temp\bash-rss.xml');
$rss->parse();



die;
