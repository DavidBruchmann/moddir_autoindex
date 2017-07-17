<?php

// @author Josh Sean
// @see http://php.net/manual/en/function.mime-content-type.php
function generateUpToDateMimeArray($url){
	$s=array();
	foreach(@explode("\n",@file_get_contents($url))as $x)
		if(isset($x[0])&&$x[0]!=='#'&&preg_match_all('#([^\s]+)#',$x,$out)&&isset($out[1])&&($c=count($out[1]))>1)
			for($i=1;$i<$c;$i++)
				$s[]="\t".'\''.$out[1][$i].'\' => \''.$out[1][0].'\'';
	$content = @sort($s)?'$mime_types = array('."\n".implode(','."\n",$s)."\n".');':false;
	return '<?php'."\n\n".'/*'."\n".' * Last Update:'.strftime ('%d.%m.%Y %H:%M:%S',time())."\n".' */'."\n".$content."\n\n".'?>';
}
