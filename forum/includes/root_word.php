<?php
########################
##  ќ–Ќ» »— ќћџ’ —Ћќ¬ ##
########################
if (!defined('NGCMS')) die ('HAL');

class Lingua_Stem_Ru {

	public $VERSION = "0.02";
	public $Stem_Caching = 0;
	public $Stem_Cache = array();
	public $VOWEL = '/аеиоуыэю€/';
	public $PERFECTIVEGROUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[а€])(в|вши|вшись)))$/';
	public $REFLEXIVE = '/(с[€ь])$/';
	public $ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|ему|ому|их|ых|ую|юю|а€|€€|ою|ею)$/';
	public $PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[а€])(ем|нн|вш|ющ|щ)))$/';
	public $VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ен|ило|ыло|ено|€т|ует|уют|ит|ыт|ены|ить|ыть|ишь|ую|ю)|((?<=[а€])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/';
	public $NOUN = '/(а|ев|ов|ие|ье|е|и€ми|€ми|ами|еи|ии|и|ией|ей|ой|ий|й|и€м|€м|ием|ем|ам|ом|о|у|ах|и€х|€х|ы|ь|ию|ью|ю|и€|ь€|€)$/';
	public $RVRE = '/^(.*?[аеиоуыэю€])(.*)$/';
	public $DERIVATIONAL = '/[^аеиоуыэю€][аеиоуыэю€]+[^аеиоуыэю€]+[аеиоуыэю€].*(?<=о)сть?$/';

	public function s(&$s, $re, $to) {

		$orig = $s;
		$s = preg_replace($re, $to, $s);

		return $orig !== $s;
	}

	public function m($s, $re) {

		return preg_match($re, $s);
	}

	public function stem_word($word) {

		$word = strtolower($word);
		$word = strtr($word, 'Є', 'е');
		if ($this->Stem_Caching && isset($this->Stem_Cache[$word])) {
			return $this->Stem_Cache[$word];
		}
		$stem = $word;
		do {
			if (!preg_match($this->RVRE, $word, $p)) break;
			$start = $p[1];
			$RV = $p[2];
			if (!$RV) break;
			if (!$this->s($RV, $this->PERFECTIVEGROUND, '')) {
				$this->s($RV, $this->REFLEXIVE, '');
				if ($this->s($RV, $this->ADJECTIVE, '')) {
					$this->s($RV, $this->PARTICIPLE, '');
				} else {
					if (!$this->s($RV, $this->VERB, '')) {
						$this->s($RV, $this->NOUN, '');
					}
				}
			}
			$this->s($RV, '/и$/', '');
			if ($this->m($RV, $this->DERIVATIONAL)) {
				$this->s($RV, '/ость?$/', '');
			}
			if (!$this->s($RV, '/ь$/', '')) {
				$this->s($RV, '/ейше?/', '');
				$this->s($RV, '/нн$/', 'н');
			}
			$stem = $start . $RV;
		} while (false);
		if ($this->Stem_Caching) $this->Stem_Cache[$word] = $stem;

		return $stem;
	}

	public function stem_caching($parm_ref) {

		$caching_level = @$parm_ref['-level'];
		if ($caching_level) {
			if (!$this->m($caching_level, '/^[012]$/')) {
				die(__CLASS__ . "::stem_caching() - Legal values are '0','1' or '2'. '$caching_level' is not a legal value");
			}
			$this->Stem_Caching = $caching_level;
		}

		return $this->Stem_Caching;
	}

	public function clear_stem_cache() {

		$this->Stem_Cache = array();
	}
}