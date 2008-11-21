<?
include "rss.inc.php";

$rss = new RSS_PROCESSOR;
if ($rss->scan("http://www.phpinside.ru/?q=rss.xml")) {
	$feed = $rss->result;
} else {
	$feed = '';
}

$u2w = new utf2win();

foreach ($feed['items'] as $news) {
	print "Title: ".$u2w->conv($news['title'])."\nDescription: ".$u2w->conv($news['description'])."\n\n";
}


print var_dump($rss);

?>