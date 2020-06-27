/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	config.toolbarGroups = [
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		'/',
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'about', groups: [ 'about' ] }
	];

	config.removeButtons = 'Source,Save,NewPage,Preview,Print,Templates,Cut,SelectAll,Maximize,ShowBlocks,About';
	config.filebrowserBrowseUrl = "plugins/wysiwyg/bb_code/ckeditor/kcfinder/browse.php?opener=ckeditor&type=files";
    config.filebrowserImageBrowseUrl = "plugins/wysiwyg/bb_code/ckeditor/kcfinder/browse.php?opener=ckeditor&type=images";
    config.filebrowserFlashBrowseUrl = "plugins/wysiwyg/bb_code/ckeditor/kcfinder/browse.php?opener=ckeditor&type=flash";
    config.filebrowserUploadUrl = "plugins/wysiwyg/bb_code/ckeditor/kcfinder/upload.php?opener=ckeditor&type=files";
    config.filebrowserImageUploadUrl = "plugins/wysiwyg/bb_code/ckeditor/kcfinder/upload.php?opener=ckeditor&type=images";
    config.filebrowserFlashUploadUrl = "plugins/wysiwyg/bb_code/ckeditor/kcfinder/upload.php?opener=ckeditor&type=flash";
};
