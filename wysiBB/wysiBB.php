<?php

if (!defined('NGCMS')) die ('HAL');

global $LIST_BBCODE;

class wysiBBNewsFilter extends NewsFilter {
	private function bb(&$tvars){
		$js_array = array();
		foreach ( array('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js', admin_url.'/plugins/wysiBB/jquery.wysibb.js')  as $value) {
			$js_array[] = $value;
		}
		
		$css_array = array();
		foreach ( array(admin_url.'/plugins/wysiBB/theme/default/wbbtheme.css')  as $value) {
			$css_array[] = $value;
		}
		//print '<pre>'.var_export($js_array).'</pre>';
		
		$tvars['include_bb'] = '<script>
			$(document).ready(function() {
				var wbbOpt = {
					lang : 	 "ru"
				}
				$("#editor").wysibb(wbbOpt)
			})
			</script>';
		$tvars['js_array'] = $js_array;
		$tvars['css_array'] = $css_array;
		$tvars['tag_bb'] = 'editor';
	}
	
	function addNewsForm(&$tvars)
	{
		$this->bb($tvars);
		
		return true;
	}
	
	function editNewsForm($newsID, $SQLnews, &$tvars)
	{ 
		$this->bb($tvars);
		
		return true;
	}
}

$LIST_BBCODE['wysiBB'] = new wysiBBNewsFilter;