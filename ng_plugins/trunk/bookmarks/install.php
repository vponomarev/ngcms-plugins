<?php
plugins_load_config();
if ($_REQUEST['action'] == 'commit') {
	if (($mysql->query("CREATE TABLE `".prefix."_bookmarks` (`user_id` int(8) default NULL, `news_id` int(8) default NULL) ENGINE=MyISAM")) ) {
		echo "Изменения в БД были успешно внесены!";
       plugin_mark_installed('bookmarks');
	}
} else {
	$text = "Плагин выводит закладки пользователя";
	generate_install_page('bookmarks', $text);
}
?>