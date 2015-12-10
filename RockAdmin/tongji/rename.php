<?php

function renames($dir){
	$conn = opendir($dir);
	while (($row = readdir($conn)) != false) {
		if ($row == '.' || $row == '..') {
			continue;
		}
		$part = explode('-',$row);
		$num = count($part);
		if ($num == 4 && $part[1] == '07') {
			$newname = str_replace('_old','',$row);
			rename($dir.$row,$dir.$newname);
		}
	}
}
              
$dirs = array('/home/upload/aclog/jn/','/home/upload/aclog/jnx/','/home/upload/aclog/qdb/','/home/upload/aclog/qdjy/','/home/upload/aclog/qdn/','/home/upload/aclog/wf/','/home/upload/aclog/yt/','/home/upload/aclog/zb/');
foreach($dirs as $v){
	renames($v);
}
