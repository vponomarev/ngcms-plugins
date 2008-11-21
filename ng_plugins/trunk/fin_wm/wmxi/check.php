<?php
	header("Content-Type: text/plain;");

	print("> CURL: ".(function_exists("curl_init") ? "passed [+]." : "failed [-].")."\n");
	print("> BCMath: ".(function_exists("bcmod") ? "passed [+]." : "failed [-].")."\n");
	print("> MBString: ".(function_exists("mb_convert_encoding") ? "passed [+]." : "failed [-].")."\n");
	print("> XML support: ".(function_exists("xml_parse") ? "passed [+]." : "failed [-].")."\n");

	if (file_exists("md4.php")) {
		include_once("md4.php");
		$md = new MD4(true);
	}
	$mda = function_exists("mhash");
	$mdb = function_exists("hash");
	$mdc = class_exists("MD4") && $md->SelfTest();

	print("> Looking for available MD4 implementations:\n");
	print(($mda ? "  + MHash" : "  - MHash")."\n");
	print(($mdb ? "  + Hash" : "  - Hash")."\n");
	print(($mdc ? "  + MD4 Class" : "  - MD4 Class")."\n");
	print("  Summary: ".($mda || $mdb || $mdc ? "passed [+]." : "failed [-].")."\n");


?>