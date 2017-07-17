<?php

if(!defined('APACHE_MIME_TYPES_URL')){
	define('APACHE_MIME_TYPES_URL','http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types');
}
require_once('./Functions/function.mimeTypes.php');
$cacheFile = '../tmp/mimeTypesCached.php';
$echoOutput = FALSE;

// int fileatime ( string $filename )
// Gets the last access time of the given file.
// Returns the time the file was last accessed, or FALSE on failure. The time is returned as a Unix timestamp.

// int filectime ( string $filename )
// Gets the inode change time of a file.
// Returns the time the file was last changed, or FALSE on failure. The time is returned as a Unix timestamp.

// int filemtime ( string $filename )
// This function returns the time when the data blocks of a file were being written to, that is, the time when the content of the file was changed.
// Returns the time the file was last modified, or FALSE on failure. The time is returned as a Unix timestamp, which is suitable for the date() function.

$diffTime = 0;
$maxAge = 60 * 60 * 24 * 7; // one week
$now = time();
if(is_file($cacheFile)){
	$mt = filemtime($cacheFile);
	$diffTime = $now - $mt;
}

if(!is_file($cacheFile) || $diffTime > $maxAge){
	file_put_contents($cacheFile,generateUpToDateMimeArray(APACHE_MIME_TYPES_URL));
}

require_once($cacheFile);

if($echoOutput){
	echo '<pre>';
	echo 'fileatime: '.fileatime($cacheFile)."\n";
	echo 'filectime: '.filectime($cacheFile)."\n";
	echo 'filemtime: '.filemtime($cacheFile)."\n";
	echo '$now: '.$now."\n";
	echo '$diffTime: '.$diffTime."\n";
	echo htmlentities(@file_get_contents($cacheFile));
	echo '</pre>';
}

?>