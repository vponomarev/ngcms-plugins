<?php if (!defined('NGCMS')) die('No direct script access allowed');
/*
 * Syntax Highlighter for NGCMS
 * Copyright (C) 2013 Maksym Dogadailo (http://dogadaylo.com)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */
function syntax_highlighter() {

	global $mod, $skin_header;
	if ($mod != 'templates') return;
	$template = '';
	$is_jquery = false;
	$is_jquery = !!(strpos($skin_header, 'jquery'));
	if (!$is_jquery) $template .= '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.2.3/jquery.min.js"></script>';
	$template .= file_get_contents(dirname(__FILE__) . '/tpl/tags.tpl');
	$template .= '</head>';
	$skin_header = preg_replace('!</head>!i', $template, $skin_header);
}

add_act('admin_header', 'syntax_highlighter');