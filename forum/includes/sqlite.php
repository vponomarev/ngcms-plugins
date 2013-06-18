<?php

if (!defined('NGCMS')) die ('HAL');

$sqlite_forum = new sqlite_forum;
$sqlite_forum->connect('forum.db');

class sqlite_forum {
	function connect($db) {
		$this->connect = sqlite_open(FORUM_CACHE.'/'.$db) or die('Нет подключения к БД');
	}
	
	function select($sql, $assocMode = 1) {
		$query = @sqlite_query($sql, $this->connect);
		
		$result = array();
		switch ($assocMode) {
			case -1: $am = MYSQL_NUM; break;
			case  1: $am = MYSQL_ASSOC; break;
			case  0:
			default: $am = MYSQL_BOTH;
		}

		while($item = sqlite_fetch_array($query, $am)) {
			$result[] = $item;
		}
		return $result;
	}
	
	function query($sql) {
		$query = @sqlite_query($sql, $this->connect);
		return $query;
	}
	
	function record($sql, $assocMode = 1) {
		$query = @sqlite_query($sql, $this->connect);
		
		switch ($assocMode) {
			case -1: $am = MYSQL_NUM; break;
			case  1: $am = MYSQL_ASSOC; break;
			case  0:
			default: $am = MYSQL_BOTH;
		}
		
		$item = sqlite_fetch_array($query, $am);
		
		return $item;
	}
}