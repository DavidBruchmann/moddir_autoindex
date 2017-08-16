<?php

$conf = array(
	'defaultIconUrlPath' => '/icons/',
	'defaultIconUriPath' => '/___CGI___/apache/icons/',
	'imageMagick' => array(
		'path' => 'C:\Program Files\ImageMagick-6.8.9-Q16\\',
	),
	'symLinkOverlay' => array(
		'OverlayImagePath' => '../img/linkOverlay.png', // dirname( $_SERVER['SCRIPT_NAME'] ) . 
	),
	'absoluteUrls' => TRUE,
	'sorting' => array(
		'foldersAndFiles' => array(
			'conf' => '^^FoldersFirst^^',  // [ '^^FoldersFirst^^' | '^^FoldersLast^^' | '^^FilesFirst^^' | '^^FilesLast^^' | '^^FUNCTION^^' ]
			'function' => '',  // [ SORTING-FUNCTION ]
			'flag' => '',  // [ SORTING-FLAG ]
			'order' => ''  // [ SORTING-ORDER ]
		),
		'folders' => array(
			'conf' => '^^FUNCTION^^',  // [ ^^FUNCTION^^ ]
			'function' => 'natcasesort',  // [ SORTING-FUNCTION ]
			'flag' => '',  // [ SORTING-FLAG ]
			'order' => ''  // [ SORTING-ORDER ]
		),
		'files' => array(
			'conf' => '^^FUNCTION^^',  // [ ^^FUNCTION^^ ]
			'function' => 'natcasesort',  // [ SORTING-FUNCTION ]
			'flag' => '',  // [ SORTING-FLAG ]
			'order' => ''  // [ SORTING-ORDER ]
		)
	),
	'tmpPath' => '../tmp/', // dirname($_SERVER['SCRIPT_NAME']).
	'items' => array(
		'iconType' => 'png,gif', // png and gif as fallback
		'itemTypes' => array(
			'^^DIRECTORY^^' => array('iconName'=>'folder','description'=>''),
			'^^DIRECTORY_UP^^' => array('iconName'=>'back','description'=>''),
			'^^BLANK^^' => array('iconName'=>'blank','description'=>''),
			'^^UNKNOWN^^' => array('iconName'=>'unknown','description'=>''),
			'.a' => array('iconName'=>'a','description'=>''),
			'.c' => array('iconName'=>'c','description'=>''),
			// '.d' => array('iconName'=>'d','description'=>''),
			// '.diff' => array('iconName'=>'diff','description'=>''),
			// '.h' => array('iconName'=>'h','description'=>''),
			'.html' => array('iconName'=>'layout','description'=>'Hypertext Markup Language'),
			'.htm' => array('iconName'=>'layout','description'=>'Hypertext Markup Language'),
			'.p' => array('iconName'=>'p','description'=>''),
			'.patch' => array('iconName'=>'patch','description'=>''),
			'.pdf' => array('iconName'=>'pdf','description'=>''),
			'.php' => array('iconName'=>'php','description'=>'PHP'),
			'.pl' => array('iconName'=>'pl','description'=>'Perl'),
			'.ps' => array('iconName'=>'ps','description'=>''),
			'.py' => array('iconName'=>'py','description'=>'Python'),
			'.script' => array('iconName'=>'script','description'=>''),
			'.tex' => array('iconName'=>'tex','description'=>''),
			'.txt' => array('iconName'=>'text','description'=>'Text'),
			'.xml' => array('iconName'=>'xml','description'=>'Extensible Markup Language'),
		)
	),
);
