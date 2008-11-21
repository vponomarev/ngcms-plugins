<?

include "XML/Parser.php";

$xml = new XML_Parser();

$res = $xml->parseString("<?xml version='1.0' ?><root>foo</root>", 1);
var_dump($xml);


//$res = $xml->parse("c:\\temp\\bash-rss.xml");

//print var_dump($res);

