<?php

if (!defined('2z')) { die("Don't you figure you're so cool?"); }

add_act('comments', 'user_ranks');

function user_ranks(){
	global $tvars, $row;

	// Check if unregistered user

	// Scan ranks
	for ($i = 7; $i >= 0; $i--) {
		$rank = array(	'name'	=> extra_get_param('user_ranks', 'rank_'.$i.'_name'),
						'com'	=> extra_get_param('user_ranks', 'rank_'.$i.'_com'),
						'news'	=> extra_get_param('user_ranks', 'rank_'.$i.'_news'),
		);


	}

	$ranks_arr = array(extra_get_param('user_ranks','rank_val1') => extra_get_param('user_ranks','rank_name1'), extra_get_param('user_ranks','rank_val2') => extra_get_param('user_ranks','rank_name2'), extra_get_param('user_ranks','rank_val3') => extra_get_param('user_ranks','rank_name3'), extra_get_param('user_ranks','rank_val4') => extra_get_param('user_ranks','rank_name4'), extra_get_param('user_ranks','rank_val5') => extra_get_param('user_ranks','rank_name5'), extra_get_param('user_ranks','rank_val6') => extra_get_param('user_ranks','rank_name6'), extra_get_param('user_ranks','rank_val7') => extra_get_param('user_ranks','rank_name7'));

	foreach($ranks_arr as $k => $v) {
		if ($row['reg'] == "1") {
			if (extra_get_param('user_ranks','rank_type') == "by_com") {
				if ($row['com'] >= $k) { $tvars['vars']['rank'] = $v; }
				if ($row['com'] < $k) { break; }
			}
			else {
				if ($row['news'] >= $k) { $tvars['vars']['rank'] = $v; }
				if ($row['news'] < $k) { break; }
			}
		}
		else {
			$tvars['vars']['rank'] = extra_get_param('user_ranks','rank_guest');
		}
	}
}
?>