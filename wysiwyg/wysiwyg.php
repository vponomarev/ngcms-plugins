<?php
if (!defined('NGCMS')) die ('HAL');

function bb(&$tvars) {
    $mapping = [
        'wysibb' => [
            'js' => [
                'jquery.wysibb.js',
                'wysibb.js',
            ],
            'css' => [
                'theme/default/wbbtheme.css',
            ],
        ],
        'jodit' => [
            'js'    => [
                'jodit.min.js',
                'jodit.js',
            ],
            'css'   => [
                'jodit.min.css',
                ['http://fonts.googleapis.com/css?family=Lato:100,300,400,700,100italic,300italic,400italic,700italic', 'absolute'],
            ],
        ],
        'tinymce' => [
            'js'    => [
                'tinymce.min.js',
                'tinymce.js',
            ],
            'css' => [ ],
        ],
    ];

    $id = pluginGetVariable('wysiwyg', 'bb_editor');
    $list = [];
    if (isset($mapping[$id])) {
        foreach ($mapping[$id]['js'] as $v) {
            // Get URL
            $url = is_array($v)?$v[0]:$v;

            // Add leading part if URL is not absolute
            if (!is_array($v) || $v[1] != 'absolute') {
                $url = admin_url.'/plugins/wysiwyg/bb_code/'.$id.'/'.$url;
            }
            $list []= '<script type="text/javascript" src="'.$url.'"></script>';
        }

        foreach ($mapping[$id]['css'] as $v) {
            // Get URL
            $url = is_array($v)?$v[0]:$v;

            // Add leading part if URL is not absolute
            if (!is_array($v) || $v[1] != 'absolute') {
                $url = admin_url.'/plugins/wysiwyg/bb_code/'.$id.'/'.$url;
            }
            $list []= '<link rel="stylesheet" href="'.$url.'" type="text/css" media="screen" />';
        }
    }
    if (!count($list))
        return;

    $tvars['preloadRAW'] = (isset($tvars['preloadRAW'])?$tvars['preloadRAW']:'').join("\n", $list);
    $tvars['flags']['disableTagsSmilies'] = true;
    $tvars['editorClassName'] = 'bb_code';
    $tvars['isBBCode'] = true;
    $tvars['attributBB'] = 'bb_code';
    return;
}

class wysiwygNewsFilter extends NewsFilter {
	function addNewsForm(&$tvars) {
		bb($tvars);
		return true;
	}

	function editNewsForm($newsID, $SQLnews, &$tvars) {
		bb($tvars);
		return true;
	}
}

class wysiwygStaticFilter extends StaticFilter {
	function addStaticForm(&$tvars) {
		bb($tvars);
		return true;
	}

	function editStaticForm($staticID, $SQLnews, &$tvars) {
		bb($tvars);
		return true;
	}
}

if (getPluginStatusActive('comments')) {
	loadPluginLibrary('comments', 'lib');

	class wysiwygFilterComments extends FilterComments {
		function addCommentsForm($newsID, &$tvars) {
			bb($tvars);
			return true;
		}
	}

	register_filter('comments', 'wysiwyg', new wysiwygFilterComments);
}
register_filter('static', 'wysiwyg', new wysiwygStaticFilter);
register_filter('news', 'wysiwyg', new wysiwygNewsFilter);