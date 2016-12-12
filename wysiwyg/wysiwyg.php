<?php

if (!defined('NGCMS')) die ( 'HAL' );

function bb(&$tvars){
	$name_bb = pluginGetVariable('wysiwyg', 'bb_editor');
	$includ_bb = array();
	$isBBCode = false;
	switch($name_bb){
		case 'wysibb':
			$js_array = array();
			foreach ( array(admin_url.'/plugins/wysiwyg/bb_code/'.$name_bb.'/jquery.wysibb.js', admin_url.'/plugins/wysiwyg/bb_code/'.$name_bb.'/wysibb.js')  as $value) {
				$js_array[] = '<script type="text/javascript" src="'.$value.'"></script>';
				
			}
			$css_array = array();
			foreach ( array(admin_url.'/plugins/wysiwyg/bb_code/'.$name_bb.'/theme/default/wbbtheme.css')  as $value) {
				$css_array[] = '<link rel="stylesheet" href="'.$value.'" type="text/css" media="screen" />';
			}
		break;
		case 'jodit';
			$js_array = array();
			foreach ( array(admin_url.'/plugins/wysiwyg/bb_code/'.$name_bb.'/jodit.min.js', admin_url.'/plugins/wysiwyg/bb_code/'.$name_bb.'/jodit.js')  as $value) {
				$js_array[] = '<script type="text/javascript" src="'.$value.'"></script>';
				
			}
			$css_array = array();
			foreach ( array(admin_url.'/plugins/wysiwyg/bb_code/'.$name_bb.'/jodit.min.css', 'http://fonts.googleapis.com/css?family=Lato:100,300,400,700,100italic,300italic,400italic,700italic')  as $value) {
				$css_array[] = '<link rel="stylesheet" href="'.$value.'" type="text/css" media="screen" />';
			}
		break;
		case 'tinymce':
			$js_array = array();
			foreach ( array(admin_url.'/plugins/wysiwyg/bb_code/'.$name_bb.'/tinymce.min.js', admin_url.'/plugins/wysiwyg/bb_code/'.$name_bb.'/tinymce.js')  as $value) {
				$js_array[] = '<script type="text/javascript" src="'.$value.'"></script>';
				
			}
			$css_array = array();
			foreach ( array()  as $value) {
				$css_array[] = '<link rel="stylesheet" href="'.$value.'" type="text/css" media="screen" />';
			}
		break;
		default: $array_bb = array();
		break;
	}
	
	$array_bb = array_merge($js_array, $css_array);
	
	if(!is_array($array_bb)) return;
	
	$tvars['includ_bb'] = implode($array_bb, "\n");
	$tvars['attributBB'] = 'bb_code';
	$tvars['isBBCode'] = true;
}

class wysiwygNewsFilter extends NewsFilter {
	function addNewsForm(&$tvars)
	{
		bb($tvars);
		
		return true;
	}
	
	function editNewsForm($newsID, $SQLnews, &$tvars)
	{ 
		bb($tvars);
		
		return true;
	}
}

class wysiwygStaticFilter extends StaticFilter {
	function addStaticForm(&$tvars)
	{ 
		bb($tvars);
		
		return true;
	}
	
	function editStaticForm($staticID, $SQLnews, &$tvars)
	{
		bb($tvars);
		
		return true; 
	}
}

if(getPluginStatusActive('comments')){
	loadPluginLibrary('comments', 'lib');
	
	class wysiwygFilterComments extends FilterComments {
		function addCommentsForm($newsID, &$tvars)
		{
			bb($tvars);
			
			return true;
		}
	}
	
	register_filter('comments', 'wysiwyg', new wysiwygFilterComments);
}

register_filter('static', 'wysiwyg', new wysiwygStaticFilter);
register_filter('news', 'wysiwyg', new wysiwygNewsFilter);