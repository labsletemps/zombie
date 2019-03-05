/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	 config.language = 'fr';
	 config.uiColor = '#e8e8e8';
	 config.toolbar = [
	 	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike'] },
	 	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
	 	{ name: 'links', items : [ 'Link','Unlink' ] },
		/*{ name: 'insert', items: [ 'Image']},*/
		 '/',
	 ];
	 config.contentsCss = 'body {color:#000; background-color#:FFF; margin: 30px 40px; }'; 
	 config.removePlugins = 'elementspath';
	 config.resize_enabled = false;
	 config.height = '200px';
};
