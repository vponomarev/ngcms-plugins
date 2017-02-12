<?php
if (!defined('NGCMS')) die ('HAL');

class Text_ReplaceNewsFilter extends NewsFilter {

	function Text_ReplaceNewsFilter() {

		$this->text = array();
		$this->p_count = 0;
		$this->c_replace = 0;
		$this->str_url = "<a href=''>error</a>";
		$this->text = extra_get_param('text_replace', 'replace');
		$this->p_count = extra_get_param('text_replace', 'p_count');
		$this->c_replace = extra_get_param('text_replace', 'c_replace');
		$this->str_url = extra_get_param('text_replace', 'str_url');
		$this->text = array_map('trim', explode("\n", $this->text));
	}

	function showNews($newsID, $SQLnews, &$tvars, $mode) {

		foreach (array('short-story', 'full-story') as $varKeyName) {
			if (!isset($tvars['vars'][$varKeyName])) {
				continue;
			}
			foreach ($this->text as $text) {
				if (isset($text) && $text) {
					$text = array_map('trim', explode("|", $text));
					$str_url = str_replace(array('%search%', '%replace%', '%root%', '%scriptLibrary%', '%home%'), array($text[0], $text[1], root, scriptLibrary, home), $this->str_url);
					$tvars['vars'][$varKeyName] = $this->text_replace($tvars['vars'][$varKeyName], $text[0], $str_url, $text[2] ? $text[2] : $this->p_count);
				}
			}
		}
		foreach (array('short', 'full') as $varKeyName) {
			if (!isset($tvars['vars']['news'][$varKeyName])) {
				continue;
			}
			foreach ($this->text as $text) {
				if (isset($text) && $text) {
					$text = array_map('trim', explode("|", $text));
					$str_url = str_replace(array('%search%', '%replace%', '%root%', '%scriptLibrary%', '%home%'), array($text[0], $text[1], root, scriptLibrary, home), $this->str_url);
					$tvars['vars']['news'][$varKeyName] = $this->text_replace($tvars['vars']['news'][$varKeyName], $text[0], $str_url, $text[2] ? $text[2] : $this->p_count);
				}
			}
		}

		return 1;
	}

	function text_replace(&$text, $search, $replace, $p_count = 3, $pos_s = 0, $i = 1) {

		while ($i <= $p_count) {
			$i++;
			switch ($this->c_replace) {
				case 0:
					$pos = mb_stripos($text, $search, $pos_s);
					break;
				case 1:
					$pos = mb_stripos($text, $search, $pos_s);
					break;
				case 2:
					$pos = strpos($text, $search, $pos_s);
					break;
				default:
					$pos = strpos($text, $search, $pos_s);
			}
			if ($pos === false) continue;
			$pos1 = strrpos(substr($text, 0, $pos), '<a ');
			if ($pos1 !== false) {
				$pos2 = strpos($text, '</a>', $pos1);
				if ($pos2 !== false && $pos2 > $pos) return $this->text_replace($text, $search, $replace, $p_count, $pos + 1, $i - 1);
			}
			if ($this->c_replace <> 0) {
				if ($text{$pos + strlen($search)} == ''
					or $text{$pos + strlen($search)} == ' '
					or $text{$pos + strlen($search)} == '<'
				) {
				} else return $this->text_replace($text, $search, $replace, $p_count, $pos + 1, $i - 1);
			}
			$text = substr_replace($text, $replace, $pos, strlen($search));
		}

		return $text;
	}
	/* function replace(&$text, &$word, $start = 0){
		$pos = strpos($text, ' '.$word, $start);
		if ($pos === false) return false;
		$pos1 = strrpos(substr($text, 0, $pos), '<img ');
		if ($pos1 !== false){
			$pos2 = strpos($text, '>', $pos1);
			if ($pos2 !== false && $pos2 > $pos) return $this->replace($text, $word, $pos + 1);			
		}
		$pos1 = strrpos(substr($text, 0, $pos), '<a ');
		if ($pos1 !== false){
			$pos2 = strpos($text, '</a>', $pos1);
			if ($pos2 !== false && $pos2 > $pos) return $this->replace($text, $word, $pos + 1);			
		}
		$text = substr_replace($text, ' <a href="'.home.'">'.$word.'</a>', $pos, strlen(' '.$word));
		return true;
	} */
}

register_filter('news', 'text_replace', new Text_ReplaceNewsFilter);