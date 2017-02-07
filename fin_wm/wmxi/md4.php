<?php
################################################################################
#                                                                              #
# MD4 pure PHP edition by DKameleon (http://dkameleon.com)                     #
#                                                                              #
# A PHP implementation of the RSA Data Security, Inc. MD4 Message              #
# Digest Algorithm, as defined in RFC 1320.                                    #
# Based on JavaScript realization taken from: http://pajhome.org.uk/crypt/md5/ #
#                                                                              #
# Updates and new versions: http://my-tools.net/md4php/                        #
#                                                                              #
# History of changes:                                                          #
# 2007.04.06                                                                   #
# - initial release                                                            #
# 2007.04.15                                                                   #
# - fixed safe_add function                                                    #
# 2007.08.26                                                                   #
# - changed code to single class implementation                                #
# - changed safe_add function a little                                         #
# - added self test function                                                   #
#                                                                              #
################################################################################
# MD4 class
class MD4 {

	var $mode = 0; // safe_add mode. got one report about optimization

	function MD4($selftest = true) {

		if ($selftest) {
			$this->SelfTest();
		}
	}

	function SelfTest() {

		$result = $this->Calc("12345678") == "012d73e0fab8d26e0f4d65e36077511e";
		$this->mode = $result ? 0 : 1;

		return $result;
	}

	function rhex($num) {

		$hex_chr = "0123456789abcdef";
		$str = "";
		for ($j = 0; $j <= 3; $j++)
			$str .= $hex_chr{($num >> ($j * 8 + 4)) & 0x0F} .
				$hex_chr{($num >> ($j * 8)) & 0x0F};

		return $str;
	}

	function str2blks($str) {

		$nblk = ((strlen($str) + 8) >> 6) + 1;
		for ($i = 0; $i < $nblk * 16; $i++) $blks[$i] = 0;
		for ($i = 0; $i < strlen($str); $i++)
			$blks[$i >> 2] |= ord($str{$i}) << (($i % 4) * 8);
		$blks[$i >> 2] |= 0x80 << (($i % 4) * 8);
		$blks[$nblk * 16 - 2] = strlen($str) * 8;

		return $blks;
	}

	function safe_add($x, $y) {

		if ($this->mode == 0) {
			return ($x + $y) & 0xFFFFFFFF;
		}
		$lsw = ($x & 0xFFFF) + ($y & 0xFFFF);
		$msw = ($x >> 16) + ($y >> 16) + ($lsw >> 16);

		return ($msw << 16) | ($lsw & 0xFFFF);
	}

	function zeroFill($a, $b) {

		$z = hexdec(80000000);
		if ($z & $a) {
			$a >>= 1;
			$a &= (~$z);
			$a |= 0x40000000;
			$a >>= ($b - 1);
		} else {
			$a >>= $b;
		}

		return $a;
	}

	function rol($num, $cnt) {

		return ($num << $cnt) | ($this->zeroFill($num, (32 - $cnt)));
	}

	function cmn($q, $a, $b, $x, $s, $t) {

		return $this->safe_add($this->rol($this->safe_add($this->safe_add($a, $q), $this->safe_add($x, $t)), $s), $b);
	}

	function ffMD4($a, $b, $c, $d, $x, $s) {

		return $this->cmn(($b & $c) | ((~$b) & $d), $a, 0, $x, $s, 0);
	}

	function ggMD4($a, $b, $c, $d, $x, $s) {

		return $this->cmn(($b & $c) | ($b & $d) | ($c & $d), $a, 0, $x, $s, 1518500249);
	}

	function hhMD4($a, $b, $c, $d, $x, $s) {

		return $this->cmn($b ^ $c ^ $d, $a, 0, $x, $s, 1859775393);
	}

	function Calc($str) {

		$x = $this->str2blks($str);
		$a = 1732584193;
		$b = -271733879;
		$c = -1732584194;
		$d = 271733878;
		for ($i = 0; $i < count($x); $i += 16) {
			$olda = $a;
			$oldb = $b;
			$oldc = $c;
			$oldd = $d;
			$a = $this->ffMD4($a, $b, $c, $d, $x[$i + 0], 3);
			$d = $this->ffMD4($d, $a, $b, $c, $x[$i + 1], 7);
			$c = $this->ffMD4($c, $d, $a, $b, $x[$i + 2], 11);
			$b = $this->ffMD4($b, $c, $d, $a, $x[$i + 3], 19);
			$a = $this->ffMD4($a, $b, $c, $d, $x[$i + 4], 3);
			$d = $this->ffMD4($d, $a, $b, $c, $x[$i + 5], 7);
			$c = $this->ffMD4($c, $d, $a, $b, $x[$i + 6], 11);
			$b = $this->ffMD4($b, $c, $d, $a, $x[$i + 7], 19);
			$a = $this->ffMD4($a, $b, $c, $d, $x[$i + 8], 3);
			$d = $this->ffMD4($d, $a, $b, $c, $x[$i + 9], 7);
			$c = $this->ffMD4($c, $d, $a, $b, $x[$i + 10], 11);
			$b = $this->ffMD4($b, $c, $d, $a, $x[$i + 11], 19);
			$a = $this->ffMD4($a, $b, $c, $d, $x[$i + 12], 3);
			$d = $this->ffMD4($d, $a, $b, $c, $x[$i + 13], 7);
			$c = $this->ffMD4($c, $d, $a, $b, $x[$i + 14], 11);
			$b = $this->ffMD4($b, $c, $d, $a, $x[$i + 15], 19);
			$a = $this->ggMD4($a, $b, $c, $d, $x[$i + 0], 3);
			$d = $this->ggMD4($d, $a, $b, $c, $x[$i + 4], 5);
			$c = $this->ggMD4($c, $d, $a, $b, $x[$i + 8], 9);
			$b = $this->ggMD4($b, $c, $d, $a, $x[$i + 12], 13);
			$a = $this->ggMD4($a, $b, $c, $d, $x[$i + 1], 3);
			$d = $this->ggMD4($d, $a, $b, $c, $x[$i + 5], 5);
			$c = $this->ggMD4($c, $d, $a, $b, $x[$i + 9], 9);
			$b = $this->ggMD4($b, $c, $d, $a, $x[$i + 13], 13);
			$a = $this->ggMD4($a, $b, $c, $d, $x[$i + 2], 3);
			$d = $this->ggMD4($d, $a, $b, $c, $x[$i + 6], 5);
			$c = $this->ggMD4($c, $d, $a, $b, $x[$i + 10], 9);
			$b = $this->ggMD4($b, $c, $d, $a, $x[$i + 14], 13);
			$a = $this->ggMD4($a, $b, $c, $d, $x[$i + 3], 3);
			$d = $this->ggMD4($d, $a, $b, $c, $x[$i + 7], 5);
			$c = $this->ggMD4($c, $d, $a, $b, $x[$i + 11], 9);
			$b = $this->ggMD4($b, $c, $d, $a, $x[$i + 15], 13);
			$a = $this->hhMD4($a, $b, $c, $d, $x[$i + 0], 3);
			$d = $this->hhMD4($d, $a, $b, $c, $x[$i + 8], 9);
			$c = $this->hhMD4($c, $d, $a, $b, $x[$i + 4], 11);
			$b = $this->hhMD4($b, $c, $d, $a, $x[$i + 12], 15);
			$a = $this->hhMD4($a, $b, $c, $d, $x[$i + 2], 3);
			$d = $this->hhMD4($d, $a, $b, $c, $x[$i + 10], 9);
			$c = $this->hhMD4($c, $d, $a, $b, $x[$i + 6], 11);
			$b = $this->hhMD4($b, $c, $d, $a, $x[$i + 14], 15);
			$a = $this->hhMD4($a, $b, $c, $d, $x[$i + 1], 3);
			$d = $this->hhMD4($d, $a, $b, $c, $x[$i + 9], 9);
			$c = $this->hhMD4($c, $d, $a, $b, $x[$i + 5], 11);
			$b = $this->hhMD4($b, $c, $d, $a, $x[$i + 13], 15);
			$a = $this->hhMD4($a, $b, $c, $d, $x[$i + 3], 3);
			$d = $this->hhMD4($d, $a, $b, $c, $x[$i + 11], 9);
			$c = $this->hhMD4($c, $d, $a, $b, $x[$i + 7], 11);
			$b = $this->hhMD4($b, $c, $d, $a, $x[$i + 15], 15);
			$a = $this->safe_add($a, $olda);
			$b = $this->safe_add($b, $oldb);
			$c = $this->safe_add($c, $oldc);
			$d = $this->safe_add($d, $oldd);
		}

		return $this->rhex($a) . $this->rhex($b) . $this->rhex($c) . $this->rhex($d);
	}
}

# MD4 class
?>