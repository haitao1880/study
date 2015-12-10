<?php

function renames($dir){
	$conn = opendir($dir);
	while (($row = readdir($conn)) != false) {
		if ($row == '.' || $row == '..') {
			continue;
		}
		$part = explode('-',$row);
		$num = count($part);
		if ($num == 3 && $part[1] == '07' && $part[2] > '07') {
			$newname = str_replace('_old','',$row);
			rename($dir.$row,$dir.$newname);
		}
	}
}
              

renames('/home/upload/aclog/qdjy/');

