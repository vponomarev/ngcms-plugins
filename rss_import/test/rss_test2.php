<?

class utf2win {
	function utf2win() {
		$conv = array(208,144,192,208,145,193,208,146,194,208,147,195,208,148,196,208,149,197,208,129,168,208,150,198,208,151,199,208,152,200,208,153,201,208,154,202,208,155,203,208,156,204,208,157,205,208,158,206,208,159,207,208,160,208,208,161,209,208,162,210,208,163,211,208,164,212,208,165,213,208,166,214,208,167,215,208,168,216,208,169,217,208,170,218,208,171,219,208,172,220,208,173,221,208,174,222,208,175,223,208,176,224,208,177,225,208,178,226,208,179,227,208,180,228,208,181,229,209,145,184,208,182,230,208,183,231,208,184,232,208,185,233,208,186,234,208,187,235,208,188,236,208,189,237,208,190,238,208,191,239,209,128,240,209,129,241,209,130,242,209,131,243,209,132,244,209,133,245,209,134,246,209,135,247,209,136,248,209,137,249,209,138,250,209,139,251,209,140,252,209,141,253,209,142,254,209,143,255);
		for ($i=0; $i<count($conv)/3; $i++) {
			$this->ac[chr($conv[$i*3]).chr($conv[$i*3+1])]=chr($conv[$i*3+2]);
		}
	}

	function conv($text) {
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


$u2w_ac=array();
//print var_dump($ac);

function u2w($line){
	global $ac;
	$u=0;
	$res='';
	$u1=chr(208); $u2=chr(209);
	for ($i=0; $i<strlen($line);$i++){
		if (($line[$i] == $u1)||($line[$i] == $u2)) { $u=1; continue; }
		if ($u) { $c=$ac[$line[$i-1].$line[$i]]; $res.= isset($c)?$c:'?'; $u=0; }
		else { $res.=$line[$i]; }
	}
	return $res;	


}


$lll = file_get_contents('ttt.txt');
$u2w = new utf2win();
print $u2w->conv($lll);



die;



$line = '�����Ũ����������������������������������������������������������';
$res = iconv('Windows-1251', 'UTF-8', $line);

for ($i=0; $i<strlen($line); $i++) {
	printf("%u,%u,%u,",ord($res[$i*2]),ord($res[$i*2+1]),ord($line[$i]));
}	









?>