<?php

//config
$G_X['upload'] = array(

		'unique_salt' 	=> 'Nr!2y[ad',
		'targetFolder' 	=> '/upload',
		'maxSize'		=> 1024 * 1024 * 48,
		'picture_type'	=> array('jpg','jpeg','png','JPG','JPEG','PNG'),


);

//时间验证
$verifyToken = md5($G_X['upload']['unique_salt'] . $_POST['timestamp']);

if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
	$file_name = $_FILES['Filedata']['name'];
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	$new_file_name = date("YmdHis")."-".rand(1,99)."-".rand(100,999).".".$fileParts['extension'];
	
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $G_X['upload']['targetFolder'] . '/' . date("Y") . '/' . date("m") . '/' . date("d") . '/';

    if(!file_exists($targetPath)){
        mkdir($targetPath,0777,true);   
    }
    
	$targetFile = rtrim($targetPath,'/') . '/' .$new_file_name;
    
    $backFilePath = '/' . date("Y") . '/' . date("m") . '/' . date("d") . '/' .$new_file_name;;
    
	//$targetFile = iconv('utf-8','GBK',$targetFile);
	$maxSize = $G_X['upload']['maxSize'];
	 
	// Validate the file type
	$fileTypes = $G_X['upload']['picture_type']; // File extensions
	
	if($_FILES["Filedata"]["size"]>$maxSize){
		$jsonStr['code'] = 0;
		$jsonStr['msg']  = 'BEYOND_THE_LIMIT_OF_SIZE';
		die(json_encode($jsonStr));
	}

	if (in_array($fileParts['extension'],$fileTypes)) {
		move_uploaded_file($tempFile,$targetFile);
		$jsonStr['code']= 1;
		$jsonStr['msg'] = "UPLOAD SUCCESS";
		$jsonStr['url'] = $backFilePath;
		die(json_encode($jsonStr));
	} else {
		$jsonStr['code'] = 0;
		$jsonStr['msg']  = 'UPLOAD FALE';
		die(json_encode($jsonStr));
	}
}else{
	$jsonStr['code'] = 0;
	$jsonStr['msg']  = 'UPLOAD TOKEN ERROR';
	die(json_encode($jsonStr));
}