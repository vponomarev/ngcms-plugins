<?php
plugins_load_config();
if ($_REQUEST['action'] == 'commit') {
	if (($mysql->query("CREATE TABLE `".prefix."_bookmarks` (`user_id` int(8) default NULL, `news_id` int(8) default NULL) ENGINE=MyISAM")) ) {
		echo "��������� � �� ���� ������� �������!";
       plugin_mark_installed('bookmarks');
	}
} else {
	$text = "������ ������� �������� ������������";
	generate_install_page('bookmarks', $text);
}
?>