<?php
if (!defined('NGCMS')) die ('HAL');

function generate_entries_cnt_cache($load = false)
{global $mysql, $config;
	
	$zboard_dir = get_plugcfg_dir('zboard');

	if(!file_exists($zboard_dir.'/cache_entries_cnt.php') or $load){
		$result = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_zboard WHERE active = \'1\' ');
		file_put_contents($zboard_dir.'/cache_entries_cnt.php', serialize($result));
	}
	
}

function generate_catz_cache($load = false)
{global $mysql, $config;

	$zboard_dir = get_plugcfg_dir('zboard');

	if(!file_exists($zboard_dir.'/cache_catz.php') or $load){
		
		$catt = array(); 
		foreach ($mysql->select('SELECT cat_id, COUNT(id) as num FROM '.prefix.'_zboard WHERE active = \'1\' GROUP BY cat_id ') as $rows)
		{
			$catt[$rows['cat_id']] .= $rows['num'];
		}
		
		foreach ($mysql->select('SELECT * FROM '.prefix.'_zboard_cat ORDER BY position ASC') as $cat_row)
		{

			$catlink = checkLinkAvailable('zboard', '')?
					generateLink('zboard', '', array('cat' => $cat_row['id'])):
					generateLink('core', 'plugin', array('plugin' => 'zboard'), array('cat' => $cat_row['id']));
			
			
			$cats_ID[$cat_row['id']][] = $cat_row;
			$cats[$cat_row['parent_id']][$cat_row['id']] =  $cat_row;
			$cats[$cat_row['parent_id']][$cat_row['id']]['url'] =  $catlink;
			$cats[$cat_row['parent_id']][$cat_row['id']]['num'] =  $catt[$cat_row['id']]?$catt[$cat_row['id']]:'0';
		}
		
		$catz_tree = build_tree_catz($cats,0);
		file_put_contents($zboard_dir.'/cache_catz.php', serialize($catz_tree));
	}

}

function build_tree_catz($cats,$parent_id,$only_parent = false){
    if(is_array($cats) and isset($cats[$parent_id])){
        $tree = '<ul>';
        if($only_parent==false){
            foreach($cats[$parent_id] as $cat){
                $tree .= '<li><a href="'.$cat['url'].'">'.$cat['cat_name'].'</a> ('.$cat['num'].')';
                $tree .=  build_tree_catz($cats,$cat['id']);
                $tree .= '</li>';
            }
        }elseif(is_numeric($only_parent)){
            $cat = $cats[$parent_id][$only_parent];
            $tree .= '<li>'.$cat['cat_name'].' #'.$cat['id'];
            $tree .=  build_tree_catz($cats,$cat['id']);
            $tree .= '</li>';
        }
        $tree .= '</ul>';
    }
    else return null;
    return $tree;
}