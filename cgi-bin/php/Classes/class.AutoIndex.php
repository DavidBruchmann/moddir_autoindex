<?php

class AutoIndex {

	// required Parameter when instantiating this class
	protected $conf = array();
	
	// apache-default:
	protected $defaultIconUrlPath = '/icons/';

	// assumed this works for many people:
	protected $defaultIconUriPath = 'C:/xampp/apache/icons/';

	// $mimeTypes are populated once a week,
	// starting counting with the first call
	// and saved in the file mimeTypesCached.php
	// @see file mime_types.php
	protected $mimeTypes = array();

	// ONLY USE IT, DON'T CHANGE, built-in options
	protected $sortingOptions = array(
		'^^FoldersFirst^^', '^^FilesLast^^',
		'^^FilesFirst^^', '^^FoldersLast^^',
	);

	// ONLY USE IT, DON'T CHANGE, built-in options
	protected $sortingFunctions = array(
		'natcasesort',  // natural, case insensitive, NO FLAG
		'natsort',      // natural, NO FLAG
		'rsort',        // high to low
		'shuffle',      // random, NO FLAG
		'sort',         // low to high
		'usort',        // userdefined, NO FLAG
	);

	// DON'T CHANGE, built-in usage
	protected $itemTypes = array(
		'folders',
		'files'
	);

	// DON'T CHANGE, built-in usage
	protected $sortableSortingFunctions = array(
		'rsort', // high to low
		'sort', // low to high
	);

	// DON'T CHANGE, built-in usage
	protected $sortFlags = array(
		SORT_REGULAR,
		SORT_NUMERIC,
		SORT_STRING,
		SORT_LOCALE_STRING,
		SORT_NATURAL,
		SORT_FLAG_CASE
	);

	// DON'T CHANGE, built-in usage
	protected $sortOrder = array(
		SORT_ASC,
		SORT_DESC,
	);

	// Automatically assigned, DON'T CHANGE, built-in usage
	protected $fullRequestUri = NULL;

	// Automatically assigned, DON'T CHANGE, built-in usage
	protected $fullRequestUrl = NULL;
	
	public function __construct(array $conf=array()){
		$this->init($conf);
		//$this->main();
	}

	public function init(array $conf){
		if(count($conf)){
			$this->conf = $conf;
		}
		if(isset($conf['defaultIconUrlPath']) && $conf['defaultIconUrlPath']){
			$this->setDefaultIconUrlPath($conf['defaultIconUrlPath']);
		}
		if(isset($conf['defaultIconUriPath']) && $conf['defaultIconUriPath']){
			$this->setDefaultIconUriPath($conf['defaultIconUriPath']);
		}
		$this->fullRequestUri = $_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI'];
		$this->fullRequestUrl = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		require('mime_types.php');
		$this->mimeTypes = $mime_types;
	}
	
	public function getRenderedPage(){
		$docParts = array();
		$docParts[] = '<!DOCTYPE HTML>';
		$docParts[] = '<html>';
		//$docParts[] = $this->getRenderedIndex();
		$docParts[] = $this->getRenderedDocHead();
		$docParts[] = $this->getRenderedDocBody();
		$docParts[] = '</html>';
		$doc = implode("\n",$docParts);
		echo $doc;
	}
	
	public function getRenderedDocHead(){
		$lines = array();
		$docTitle = $this->getDocTitle();
		$lines[] = '<head>';
		$lines[] = '<meta charset="UTF-8" />';
		$lines[] = '<title>'.$docTitle.'</title>';
		$comment = $this->getRenderedPageComment();
		if($comment) {
			$lines[] = $comment;
		}
		$css = $this->getRenderedCssTags();
		if($css) {
			$lines[] = $css;
		}
		$js = $this->getRenderedJsLibs();
		if($js) {
			$lines[] = $js;
		}
		$lines[] = '</head>';
		return implode("\n",$lines);
	}
	
	public function getRenderedDocBody(){
		$sections = array();
		$sections[] = '<body>';
		$sections[] = '<h1>'.$this->getDocTitle().'</h1>';
		// @TODO: before index
		$sections[] = '<table>';
		$sections[] = $this->getRenderedIndexHead();
		$sections[] = $this->getRenderedIndexBody();
		$sections[] = '</table>';
		// @TODO: after index
		$sections[] = '</body>';
		return implode("\n",$sections);
	}
	
	public function getDocTitle(){
		// @TODO
		// var_dump($_SERVER);
		$scheme = $_SERVER['REQUEST_SCHEME'];
		$name = $_SERVER['SERVER_NAME'];
		$port = $_SERVER['SERVER_PORT'];
		$uri = $_SERVER['REQUEST_URI'];
		$title = '';
		if(!in_array($port,array('80','443'))){
			$title = $scheme.'://'.$name.':'.$port;
		} else {
			$title = $name;
		}
		$title.= $uri;
		return 'Index of '.$title;
	}
	
	public function getRenderedPageComment(){
		return '';
	}
	
	public function getRenderedCssTags(){
		// TODO: make path and file configurable
		$folderPath = '/moddir_autoindex/Resources/Public/css/';
		$filePath = $folderPath.'styles.css';
		$defaulTag = '<link rel="stylesheet" type="text/css" href="'.$filePath.'" />';
		return $defaulTag;
	}
	
	public function getRenderedJsLibs(){
		return '';
	}
	
	public function getRenderedIndexHead(){
		$cols = array();
		$cols[] = '<th></th>'; // icon
		$cols[] = '<th>Item</th>'; // item
		$cols[] = '<th>Size</th>'; // size
		$cols[] = '<th>Description</th>'; // description
		return implode("\n",$cols);
	}
	
	public function getRenderedIndexBody(){
		$items = $this->scandirSorted($this->fullRequestUri);
		if($_SERVER['REQUEST_URI'] !== '/'){
			array_unshift($items,'..');
		}
		$rows = '';
		foreach($items as $item){
			$itemConf = NULL;
			if($item != '.'){
				if(is_dir($this->fullRequestUri.$item)){
					$itemConf['icon'] = $this->getDirIcon($this->fullRequestUri.$item, $item);
				} elseif(is_file($this->fullRequestUri.$item)) {
					$itemConf['icon'] = $this->getFileIcon($this->fullRequestUri.$item);
				} else {
					$itemConf['icon'] = '[?]';
				}
				$itemConf['item'] = $item;
				$itemConf['url'] = $this->getItemUrl($item);
				$itemConf['link'] = $this->getRenderedItemLink($itemConf);
				$itemConf['iconLink'] = $this->getRenderedItemIconLink($itemConf);
				if(is_file($this->fullRequestUri.$item)) {
					// TODO:
					$itemConf['size'] = 'asd';
					$itemConf['description'] = 'asd';
				} else {
					$itemConf['size'] = '';
					$itemConf['description'] = '';
				}
				#if(is_dir($this->fullRequestUri.$item)){
				#	echo $item."<br>\n";
				#	var_dump($itemConf);
				#}
				$rows.= $this->renderItemRow($itemConf);
			}
		}
		return $rows;
	}
	
	function renderItemRow($itemConf){
		return  '<tr><td>'.$itemConf['iconLink'].
				'</td><td>'.$itemConf['link'].
				'</td><td>'.$itemConf['size'].
				'</td><td>'.$itemConf['description'].
				'</td></tr>'."\n";
	}
	
	protected function scandirSorted($dir){
		// var_dump($dir);
		$items = scandir($dir);
		if(in_array($this->conf['sorting']['foldersAndFiles']['conf'],$this->sortingOptions)){
			$folders = array();
			$files = array();
			foreach($items as $item){
				if($item != '.' && $item != '..'){
					if(is_dir($this->fullRequestUri.$item)){
						$folders[] = $item;
					} elseif(is_file($this->fullRequestUri.$item)) {
						$files[] = $item;
					} else {
						// @TODO: raise error
					}
				}
			}
			foreach($this->itemTypes as $itemType){
				if(isset($this->conf['sorting'][$itemType]['conf'])
					&& $this->conf['sorting'][$itemType]['conf'] == '^^FUNCTION^^'
					&& isset($this->conf['sorting'][$itemType]['function'])
				){
					if(in_array($this->conf['sorting'][$itemType]['function'],$this->sortingFunctions)){
						if(!in_array($this->conf['sorting'][$itemType]['function'],array('sort','rsort'))){
							$this->conf['sorting'][$itemType]['function']( ${$itemType} );
						} else {
							$flag = NULL;
							if(isset($this->conf['sorting'][$itemType]['flag'])
								&& preg_match('/(\s*?'.implode('\s*?[\|]*?\s*?',$this->sortFlags).'\s*?)+?/',$this->conf['sorting'][$itemType]['flag'])
							) {
								$flag = $this->conf['sorting'][$itemType]['flag'];
								$this->conf['sorting'][$itemType]['function']( ${$itemType}, $flag );
							} else {
								$this->conf['sorting'][$itemType]['function']( ${$itemType} );
							}
						}
					} else {
						// @TODO: raise error
					}
					if(isset($this->conf['sorting'][$itemType]['order'])
						&& in_array($this->conf['sorting'][$itemType]['order'],$this->sortOrder)
						// && $this->conf['sorting'][$itemType]['order'] == SORT_DESC
					){
						$reverse = NULL;
						if($this->conf['sorting'][$itemType]['order'] == SORT_DESC){
							if(in_array($this->conf['sorting'][$itemType]['function'],array( 'sort','natcasesort','natsort'))){
								$reverse = TRUE;
							}
						}
						elseif ($this->conf['sorting'][$itemType]['function'] == 'rsort'
							&& $this->conf['sorting'][$itemType]['order'] == SORT_ASC
						){
							$reverse = TRUE;
						}
						if($reverse){
							${$itemType} = array_reverse( ${$itemType} );
						}
					}
				}
			}
			//var_dump($folders);
			if(in_array($this->conf['sorting']['foldersAndFiles']['conf'],array('^^FoldersFirst^^', '^^FilesLast^^'))){
				$items = array_merge($folders,$files);
			}
			elseif(in_array($this->conf['sorting']['foldersAndFiles']['conf'],array('^^FoldersLast^^', '^^FilesFirst^^'))){
				$items = array_merge($files,$folders);
			}
		}
		elseif ($this->conf['sorting']['foldersAndFiles']['conf'] == '^^FUNCTION^^') {
			if(in_array($this->conf['sorting']['foldersAndFiles']['function'],$this->sortingFunctions)){
				// @TODO: sorting options like above
				$this->conf['sorting']['foldersAndFiles']['function']($items);
			}
			else {
				// @TODO: raise error
			}
		}
		return $items;
	}
	
	protected function getRenderedItemIconLink($itemConf){
		return '<a href="'.$itemConf['url'].'">'.$itemConf['icon'].'</a>';
	}
	
	protected function getRenderedItemLink($itemConf){
		return '<a href="'.$itemConf['url'].'">'.$itemConf['item'].'</a>';
	}
	
	protected function getItemUrl($item){
		$itemUrl = $item;
		if($this->conf['absoluteUrls']){
			$itemUrl = $this->fullRequestUrl.$item;
		}
		return $itemUrl;
	}

	protected function getIcon($item){
		if(is_dir($item)){
			$icon = $this->getDirIcon($item);
		} elseif(is_file($item)) {
			$icon = $this->getFileIcon($item);
		} else {
			return '[?]';
		}
		return $icon;
	}

	protected function is_link($item){
		// http://php.net/manual/en/function.is-link.php#83312
		// on windows:
		// http://php.net/manual/en/function.is-link.php#113263
		// http://php.net/manual/en/function.is-link.php#91249
		return is_link($item);
	}

	protected function getDirIcon($itemPath, $item){
		if($item =='..'){
			$key = '^^DIRECTORY_UP^^';
		} else {
			$key = '^^DIRECTORY^^';
		}
		$iconDir = $this->defaultIconUrlPath;
		if(isset($this->conf['items']['itemTypes'][$key]['iconDir']) && is_dir($this->conf['items']['itemTypes'][$key]['iconDir'])){
			$iconDir = $this->conf['items']['itemTypes'][$key]['iconDir'];
		}
		$iconName = $this->conf['items']['itemTypes'][$key]['iconName'];
		$iconSuffixes = explode(',',$this->conf['items']['iconType']);
		$n = 0;
		$iconSuffix = '';
		do {
			$iconSuffix = $iconSuffixes[$n];
			$iconFile = $iconDir.$iconName.'.'.$iconSuffix;
			if(is_file($this->defaultIconUriPath.$iconName.'.'.$iconSuffix)){
				break;
			} else {
				unset($iconFile);
			}
			$n++;
		} while($iconSuffixes[$n] && $n<=count($iconSuffixes)-1);
		#echo $item."<br>\n";
		#return $iconName; //($this->is_link($item) ? '[DIR LINK]' : '[DIR]');
		$iconTag = '<img src="'.$iconFile.'" width="16" height="16" alt="" />';
		return $iconTag;
	}

	protected function getFileIcon($item){
		$basename = pathinfo ( $item, PATHINFO_BASENAME);
		#$item = str_replace('\\','/',$item);
		$itemParts = explode('.',$basename);
		// case for some special files without suffix like README, CHANGELOG, etc.
		if(count($itemParts) === 1 && array_key_exists($basename, $this->conf['items']['itemTypes'])){
			$key = $basename;
		}
		// normal case: filename and file-suffix:
		elseif(count($itemParts) >= 2 && array_key_exists('.'.$itemParts[ count($itemParts)-1 ], $this->conf['items']['itemTypes'])){
			$suffix = $itemParts[ count($itemParts)-1 ];
			$key = '.'.$suffix;
		}
		// case for some archives: filename.tar.gz:
		if(count($itemParts) >= 3 && array_key_exists('.'.$itemParts[ count($itemParts)-2 ].'.'.$itemParts[ count($itemParts)-1 ], $this->conf['items']['itemTypes'])){
			$suffix = $itemParts[ count($itemParts)-2 ].'.'.$itemParts[ count($itemParts)-1 ];
			$key = '.'.$suffix;
		}
		// case for some very special cases perhaps:
		if(count($itemParts) >= 4 && array_key_exists('.'.$itemParts[ count($itemParts)-3 ].'.'.$itemParts[ count($itemParts)-2 ].'.'.$itemParts[ count($itemParts)-1 ], $this->conf['items']['itemTypes'])){
			$suffix = $itemParts[ count($itemParts)-3 ].'.'.$itemParts[ count($itemParts)-2 ].'.'.$itemParts[ count($itemParts)-1 ];
			$key = '.'.$suffix;
		}
		// case for some very very special cases perhaps:
		if(count($itemParts) >= 5 && array_key_exists('.'.$itemParts[ count($itemParts)-4 ].'.'.$itemParts[ count($itemParts)-3 ].'.'.$itemParts[ count($itemParts)-2 ].'.'.$itemParts[ count($itemParts)-1 ], $this->conf['items']['itemTypes'])){
			$suffix = $itemParts[ count($itemParts)-4 ].'.'.$itemParts[ count($itemParts)-3 ].'.'.$itemParts[ count($itemParts)-2 ].'.'.$itemParts[ count($itemParts)-1 ];
			$key = '.'.$suffix;
		}
		/*
		*/
		if(!isset($key)){
			$key = '^^UNKNOWN^^';
		}
		# echo '<h1>'.$item.'<br>'.$key.'</h1>';
		
		
		
		
		
		$iconDir = $this->defaultIconUrlPath;
		// '.xml' => array('iconName'=>'xml','description'=>'Extensible Markup Language'),
		if(@isset($this->conf['items']['itemTypes'][$key]['iconName'])){
			if(isset($this->conf['items']['itemTypes'][$key]['iconDir']) && is_dir($this->conf['items']['itemTypes'][$key]['iconDir'])){
				$iconDir = $this->conf['items']['itemTypes'][$key]['iconDir'];
			}
			$iconName = $this->conf['items']['itemTypes'][$key]['iconName'];
			$iconSuffixes = explode(',',$this->conf['items']['iconType']);
			$n = 0;
			$iconSuffix = '';
			do {
				$iconSuffix = $iconSuffixes[$n];
				$iconFile = $iconDir.$iconName.'.'.$iconSuffix;
				if(is_file($this->defaultIconUriPath.$iconName.'.'.$iconSuffix)){
					break;
				} else {
					unset($iconFile);
				}
				$n++;
			} while($iconSuffixes[$n] && $n<=count($iconSuffixes)-1);
		}
		else {
			/*
			// mime_content_type
			// if(@isset())
			$finfo = new finfo(FILEINFO_MIME, "/usr/share/misc/magic"); // return mime type ala mimetype extension
			// get mime-type for a specific file
			# $filename = "/usr/local/something.txt";
			echo $finfo->file($item);
			*/
			
			//require('mime_types.php');
			
			#if(!isset($suffix) && !isset($key)){
			#	echo '<h1>'.$item.'</h1>';
			#}
			$tmpKey = isset($suffix) ? $suffix : $key;
			if(@isset($this->mimeTypes[$tmpKey])){
				$key = $this->mimeTypes[$tmpKey];
				//echo basename.': '.$this->mimeTypes[$tmpKey].'<br>';
				if(@isset($this->conf['items']['itemTypes'][ $key ]['iconName'])){
					if(isset($this->conf['items']['itemTypes'][$key]['iconDir']) && is_dir($this->conf['items']['itemTypes'][$key]['iconDir'])){
						$iconDir = $this->conf['items']['itemTypes'][$key]['iconDir'];
					}
					$iconName = $this->conf['items']['itemTypes'][$key]['iconName'];
					$iconSuffixes = explode(',',$this->conf['items']['iconType']);
					$n = 0;
					$iconSuffix = '';
					do {
						$iconSuffix = $iconSuffixes[$n];
						$iconFile = $iconDir.$iconName.'.'.$iconSuffix;
						if(is_readable($iconFile)){
							#echo '<h1>readable: '.$iconFile.'</h1>';
							break;
						} else {
							unset($iconFile);
						}
						$n++;
					} while($iconSuffixes[$n] && $n<count($iconSuffixes)-1);
				}
			}
		}
		
		#if(!isset($iconFile)){
		#	$iconFile = $iconDir.$this->conf['items']['itemTypes']['^^UNKNOWN^^']['iconName'].'.png';
		#}
		
		// echo '<br>$item:'.$item.'<br>$itemParts[1]:'.$itemParts[1].'<br>key:'.$key.'<br>$iconFile:'.$iconFile.'<br>';
		$iconTag = '<img src="'.$iconFile.'" width="16" height="16" alt="" />';
		return $iconTag;
		
		//if(){ // preg_match('/'..'$/')}
		#return ($this->is_link($item) ? '[FILE LINK]' : '[FILE]');
	}
	
	protected function setDefaultIconUrlPath($defaultIconUrlPath){
		$this->defaultIconUrlPath = $defaultIconUrlPath;
	}
	
	protected function setDefaultIconUriPath($defaultIconUriPath){
		$this->defaultIconUriPath = $defaultIconUriPath;
	}

}
