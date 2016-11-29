<?php

if (!defined('NGCMS')) die ( 'HAL' );

add_act('core', 'plugin_wysiwyg');

function plugin_wysiwyg()
{global $twig;
	
	$name_bb = pluginGetVariable('wysiwyg', 'bb_editor');
	$includ_bb = array();
	switch($name_bb){
		case 'wysibb':
			$js_array = array();
			foreach ( array('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js', admin_url.'/plugins/wysiwyg/bb_code/'.$name_bb.'/jquery.wysibb.js', admin_url.'/plugins/wysiwyg/bb_code/'.$name_bb.'/wysibb.js')  as $value) {
				$js_array[] = '<script type="text/javascript" src="'.$value.'"></script>';
				
			}
			$css_array = array();
			foreach ( array(admin_url.'/plugins/wysiwyg/bb_code/'.$name_bb.'/theme/default/wbbtheme.css')  as $value) {
				$css_array[] = '<link rel="stylesheet" href="'.$value.'" type="text/css" media="screen" />';
			}
		break;
		case 'jodit';
			$js_array = array();
			foreach ( array('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js', admin_url.'/plugins/wysiwyg/bb_code/'.$name_bb.'/jodit.min.js', admin_url.'/plugins/wysiwyg/bb_code/'.$name_bb.'/jodit.js')  as $value) {
				$js_array[] = '<script type="text/javascript" src="'.$value.'"></script>';
				
			}
			$css_array = array();
			foreach ( array(admin_url.'/plugins/wysiwyg/bb_code/'.$name_bb.'/jodit.min.css', 'http://fonts.googleapis.com/css?family=Lato:100,300,400,700,100italic,300italic,400italic,700italic')  as $value) {
				$css_array[] = '<link rel="stylesheet" href="'.$value.'" type="text/css" media="screen" />';
			}
		break;
		case 'tinymce':
			$js_array = array();
			foreach ( array('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js', admin_url.'/plugins/wysiwyg/bb_code/'.$name_bb.'/tinymce.min.js', admin_url.'/plugins/wysiwyg/bb_code/'.$name_bb.'/tinymce.js')  as $value) {
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
	//print '<pre>'.var_export($array_bb).'</pre>';
	
	$twig->addGlobalRef('includ_bb', implode($array_bb, "\n"));
	$twig->addFunction('DisplayTextForm', new Twig_Function_Function('EntryField'));
}

function EntryField($params){	
	//print '<pre>'.var_export($params).'</pre>';
	
	//print '<pre>'.var_export(pluginGetVariable('wysiwyg', 'bb_editor')).'</pre>';
	
	$name_bb = pluginGetVariable('wysiwyg', 'bb_editor');

	$value = $params['values'];
	$name = $params['name'];
	$id = $params['id'];
	$class = $params['class'];
	
	switch($name_bb){
		case 'wysibb':
		case 'jodit':
		case 'tinymce':
			if(empty($class))
				$class = 'bb_code';
			else
				$class .= ' bb_code';
		break;
		default: $id = (isset($params['id']))?$id:'ng_news_content';
	}
	
	$options = '';
	if(is_array($params['options']) && $params['options'])
		$options = ' '.implode($params['options'], ' ');
	else 
		$options = ' '.$params['options'];
	
 	switch($params['type']){
		case 'textarea':
			$form = "<textarea{$options}".(isset($name) && $name?' name="'.$name.'"':'').(isset($id) && $id?' id="'.$id.'"':'').(isset($class) && $class?' class="'.$class.'"':'').">{$value}</textarea>";
		break;
		case 'input':
			$form = "<input{$options}".(isset($value) && $value?' value="'.$value.'"':'').(isset($name) && $name?' name="'.$name.'"':'').(isset($id) && $id?' id="'.$id.'"':'').(isset($class) && $class?' class="'.$class.'"':'')." />";
		break;
		default: return '';
	}
	
	return $form;
}