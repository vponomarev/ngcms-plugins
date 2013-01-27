<?php

class autokeyword {
	var $contents;
	var $encoding;
	var $keywords;
	var $wordLengthMin;
	var $wordOccuredMin;
	var $wordLengthMax;
	var $wordGoodArray;
	var $wordBlockArray;
	var $wordMaxCount;
	var $wordB;
	var $wordAddTitle;
	var $wordTitle;
	function autokeyword($params, $encoding)
	{
		$this->wordGoodArray = array();
		$this->wordBlockArray = array();
		$this->encoding = $encoding;
		$this->wordLengthMin = $params['min_word_length'];
		$this->wordLengthMax = $params['max_word_length'];
		$this->wordMaxCount = $params['word_count'];
		if($params['good_b']) {$this->wordB = 1;}
		if($params['add_title'] > 0) {$this->wordAddTitle = $params['add_title']; $this->wordTitle = $params['title'];
		for($i=0; $i<$this->wordAddTitle;$i++){$content .= $this->wordTitle.' ';}
		$params['content'] = $content.' '.$params['content'];
		}
		if($params['good_array'] && $params['good_word'] == true){$this->wordGoodArray = explode("\r\n",$params['good_array']);}
		if($params['block_array'] && $params['block_word'] == true){$this->wordBlockArray = explode("\r\n",$params['block_array']);}
		$this->contents = $this->replace_chars($params['content']);


	}

	function replace_chars($content)
	{
		$content = strtolower($content);
		$content = strip_tags($content);
		if($this->wordB == 1){$content = preg_replace('![b](.*)[/b]!si','$1 $1',$content); }
		$punctuations = array(',', ')', '(', '.', "'", '"',
		'<', '>', ';', '!', '?', '/', '-',
		'_', '[', ']', ':', '+', '=', '#',
		'$', '&quot;', '&copy;', '&gt;', '&lt;',
		chr(10), chr(13), chr(9));
		$punctuations = array_merge($this->wordBlockArray,$punctuations);
		$content = str_replace($punctuations, " ", $content);
		$content = preg_replace('/ {2,}/si', " ", $content);
		return $content;
	}

	function parse_words()
	{
		$common = array("aaaaaaa","aaaaaaa");
		$s = split(" ", $this->contents);
		$k = array();
		foreach( $s as $key=>$val ) {
			if(strlen(trim($val)) >= $this->wordLengthMin && strlen(trim($val)) <= $this->wordLengthMax  && !in_array(trim($val), $common)  && !is_numeric(trim($val))) {
				$k[] = trim($val);
			}
		}
		$k = array_count_values($k);
		$occur_filtered = $this->occure_filter($k, $this->wordOccuredMin);
		arsort($occur_filtered);
		$occur_filtered = array_flip($this->wordGoodArray) + $occur_filtered;
		array_splice($occur_filtered,$this->wordMaxCount);
		$imploded = $this->implode(", ", $occur_filtered);
		unset($k);
		unset($s);
		return $imploded;
	}

	function occure_filter($array_count_values, $min_occur)
	{
		$occur_filtered = array();
		foreach ($array_count_values as $word => $occured) {
			if ($occured >= $min_occur) {
				$occur_filtered[$word] = $occured;
			}
		}
		return $occur_filtered;
	}

	function implode($gule, $array)
	{
		$c = "";
		foreach($array as $key=>$val) {
			@$c .= $key.$gule;
		}
		return $c;
	}
}

function akeysGetKeys($params){
	$cfg = array(
		'content'			=> $params['content'].' this is content',
		'title'				=> $params['title'],
		'min_word_length'	=> (intval(pluginGetVariable('autokeys','length'))) ? intval(pluginGetVariable('autokeys','length')) : 5,
		'max_word_length'	=> (intval(pluginGetVariable('autokeys','sub'))) ? intval(pluginGetVariable('autokeys','sub')) : 100,
		'min_word_occur'	=> (intval(pluginGetVariable('autokeys','occur'))) ? intval(pluginGetVariable('autokeys','occur')) : 2,
		'word_sum'			=> (intval(pluginGetVariable('autokeys','sum'))) ? intval(pluginGetVariable('autokeys','sum')) : 245,
		'block_word'		=> pluginGetVariable('autokeys','block_y') ? pluginGetVariable('autokeys','block_y') : false,
		'block_array'		=> pluginGetVariable('autokeys','block'),
		'good_word'			=> pluginGetVariable('autokeys','good_y') ? pluginGetVariable('autokeys','good_y') : false,
		'good_array'		=> pluginGetVariable('autokeys','good'),
		'add_title'			=> (intval(pluginGetVariable('autokeys','add_title'))) ? intval(pluginGetVariable('autokeys','add_title')) : 0,
		'word_count'		=> (intval(pluginGetVariable('autokeys','count'))) ? intval(pluginGetVariable('autokeys','count')) : 245,
		'good_b'			=> pluginGetVariable('autokeys','good_b') ? pluginGetVariable('autokeys','good_b') : false,

	);

	$keyword = new autokeyword($cfg, "windows-1251");
	$words = substr($keyword->parse_words(),0,$cfg['word_sum']);
	$words = substr($words,0,strrpos($words, ', '));
	return $words;
}
