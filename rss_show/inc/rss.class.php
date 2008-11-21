<?php

$data = file_get_contents('./rss.xml');

$parser = xml_parser_create();
xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
xml_parse_into_struct($parser, $data, $values, $tags);
xml_parser_free($parser);

// Строим древовидную структуру



print "TAGS:\n";
var_dump($tags);

print "\n\nValue:\n";
var_dump($values);
