<?php
class UploadCutImgTool{
	public $upload_dir = ""; 				// The directory for the images to be saved in
	public $upload_path = "";				// The path to where the image will be saved
	public $large_image_prefix = "resize_"; 			// The prefix name to large image
	public $thumb_image_prefix = "thumbnail_";			// The prefix name to the thumb image
	public $large_image_name = "";     // New name of the large image (append the timestamp to the filename)
	public $thumb_image_name = "";     // New name of the thumbnail image (append the timestamp to the filename)
	
	public $max_file = "3"; 							// Maximum file size in MB
	public $max_width = "500";							// Max width allowed for the large image
	public $thumb_width = "120";						// Width of thumbnail image
	public $thumb_height = "120";						// Height of thumbnail image
	// Only one of these image types should be allowed for upload
	public $allowed_image_types =array('image/pjpeg'=>"jpg",
               'image/jpeg'=>"jpg",
               'image/jpg'=>"jpg",
               'image/png'=>"png",
               'image/x-png'=>"png",
               'image/gif'=>"gif");
	//TODO 要处理
	public $allowed_image_ext = array(); // do not change this
	public $image_ext = "";	// initialise variable, do not change this.
	
	public $large_image_location = "";
	public $thumb_image_location = "";
	
	public $large_photo_exists = "";
	public $thumb_photo_exists = "";
    public function __construct(){
        global $G_X;
        $this->max_file=$G_X['uploadface']['filesize'];
        $this->allowed_image_types=$G_X['uploadface']['filetypes'];
        $this->thumb_width=$G_X['uploadface']['thumb']['width'];
        $this->thumb_height=$G_X['uploadface']['thumb']['height'];
        
        
    }
	
	public function initImageType($random_key,$user_file_ext){
	    
		//$this->upload_dir = PUBLIC_PATH."pweb/style/default/images/upload_pic";
		
		//$this->upload_path = $this->upload_dir."/";
		
	    
		$this->large_image_name = $this->large_image_name.$random_key;
		$this->thumb_image_name = $this->thumb_image_name.$random_key;
		$this->allowed_image_ext = array_unique($this->allowed_image_types);
		$this->large_image_location = $this->upload_path.$this->large_image_name.$user_file_ext;
		$this->thumb_image_location = $this->upload_path.$this->thumb_image_name.$user_file_ext;
		foreach ($this->allowed_image_ext as $mime_type => $ext) {
		    $this->image_ext.= strtoupper($ext)." ";
		}
		if(!is_dir($this->upload_dir)){
			mkdir($this->upload_dir, 0777);
			chmod($this->upload_dir, 0777);
		}
		
		//Check to see if any images with the same name already exist
		if (file_exists($this->large_image_location)){
			if(file_exists($this->thumb_image_location)){
				$this->thumb_photo_exists = "<img src=\"".$this->upload_path.$this->thumb_image_name.$user_file_ext."\" alt=\"Thumbnail Image\"/>";
			}else{
				$this->thumb_photo_exists = "";
			}
		   	$this->large_photo_exists = "<img src=\"".$this->upload_path.$this->large_image_name.$user_file_ext."\" alt=\"Large Image\"/>";
		} else {
		   	$this->large_photo_exists = "";
			$this->thumb_photo_exists = "";
		}
	}
	
	public function resizeImage($image,$width,$height,$scale) {
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);
		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		switch($imageType) {
			case "image/gif":
				$source=imagecreatefromgif($image); 
				break;
		    case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source=imagecreatefromjpeg($image); 
				break;
		    case "image/png":
			case "image/x-png":
				$source=imagecreatefrompng($image); 
				break;
	  	}
		imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
		
		switch($imageType) {
			case "image/gif":
		  		imagegif($newImage,$image); 
				break;
	      	case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
		  		imagejpeg($newImage,$image,90); 
				break;
			case "image/png":
			case "image/x-png":
				imagepng($newImage,$image);  
				break;
	    }
		
		chmod($image, 0777);
		return $image;
	}
	//You do not need to alter these functions
	public function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);
		
		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		switch($imageType) {
			case "image/gif":
				$source=imagecreatefromgif($image); 
				break;
		    case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source=imagecreatefromjpeg($image); 
				break;
		    case "image/png":
			case "image/x-png":
				$source=imagecreatefrompng($image); 
				break;
	  	}
		imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
		switch($imageType) {
			case "image/gif":
		  		imagegif($newImage,$thumb_image_name); 
				break;
	      	case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
		  		imagejpeg($newImage,$thumb_image_name,90); 
				break;
			case "image/png":
			case "image/x-png":
				imagepng($newImage,$thumb_image_name);  
				break;
	    }
		chmod($thumb_image_name, 0777);
		return $thumb_image_name;
	}
	//You do not need to alter these functions
	public function getHeight($image) {
		$size = getimagesize($image);
		$height = $size[1];
		return $height;
	}
	//You do not need to alter these functions
	public function getWidth($image) {
		$size = getimagesize($image);
		$width = $size[0];
		return $width;
	}
	public function uploadImage($image,$random_key,$user_file_ext){
		
		$filename = basename($image['name']);
		$file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
		if(empty($user_file_ext))
		{
			$user_file_ext = '.'.$file_ext;
		}
		$this->initImageType($random_key,$user_file_ext);
		
		//Get the file information
		$userfile_name = $image['name'];
		$userfile_tmp = $image['tmp_name'];
		$userfile_size = $image['size'];
		$userfile_type = $image['type'];


		//Only process if the file is a JPG, PNG or GIF and below the allowed limit
		if((!empty($image)) && ($image['error'] == 0)) {
			
			foreach ($this->allowed_image_types as $mime_type => $ext) {
				//loop through the specified image types and if they match the extension then break out
				//everything is ok so go and check file size
				if($file_ext==$ext && $userfile_type==$mime_type){
					$error = "";
					break;
				}else{
					$error = "只有<strong>".$this->image_ext."</strong>可以上传<br />";
				}
			}
			
			//check if the file size is above the allowed limit
			if ($userfile_size > ($this->max_file*1048576)) {
				$error.= "Images must be under ".$this->max_file."MB in size";
			}
		}else{
			$error= "Select an image for upload";
		}

		//Everything is ok, so we can upload the image.
		if (strlen($error)==0){
			if (isset($image['name'])){
				//this file could now has an unknown file extension (we hope it's one of the ones set above!)
				//$this->large_image_location = $this->large_image_location.".".$file_ext;
				//$this->thumb_image_location = $this->thumb_image_location.".".$file_ext;

				//put the file ext in the session so we know what file to look for once its uploaded
				$this->user_file_ext=".".$file_ext;
				
				$flag = move_uploaded_file($userfile_tmp, $this->large_image_location);
				
				chmod($this->large_image_location, 0777);
				
				$width = $this->getWidth($this->large_image_location);
				$height = $this->getHeight($this->large_image_location);
				//Scale the image if it is greater than the width set above
				if ($width > $this->max_width){
					$scale = $this->max_width/$width;
					$uploaded = $this->resizeImage($this->large_image_location,$width,$height,$scale);
				}else{
					$scale = 1;
					$uploaded = $this->resizeImage($this->large_image_location,$width,$height,$scale);
				}
				//Delete the thumbnail file so the user can create a new one
				/*
				if (file_exists($this->thumb_image_location)) {
					unlink($this->thumb_image_location);
				}
				 */
			}
			$return = explode('upload'.DIRECTORY_SEPARATOR, $uploaded);
			$return[1] = str_replace(DIRECTORY_SEPARATOR, '/', $return[1]);
			//$return = explode("pweb/",$uploaded);
			return array("imgurl"=>$return[1],"width"=>$this->getWidth($uploaded),"height"=>$this->getHeight($uploaded));
		}else{
			return array("uploaderror"=>$error);
		}
	}
	public function uploadThumbImage($coordinate){
		$x1 = $coordinate["x1"];
		$y1 = $coordinate["y1"];
		$x2 = $coordinate["x2"];
		$y2 = $coordinate["y2"];
		$w = $coordinate["w"];
		$h = $coordinate["h"];

		$scale = $this->thumb_width/$w;
		
        if(!empty($this->thumb_image_name)&&!empty($this->upload_path)){
            $file_ext = strtolower(substr($coordinate['largeimglocation'], strrpos($coordinate['largeimglocation'], '.') ));
           
            $thumb_image_name=$this->upload_path.$this->thumb_image_name.$file_ext;
        }else{
             $thumb_image_name=$coordinate['thumbimglocation'];
        }
        
		$cropped = $this->resizeThumbnailImage($thumb_image_name, $coordinate['largeimglocation'],$w,$h,$x1,$y1,$scale);
		@unlink($coordinate['largeimglocation']);
		$return = explode("upload".DIRECTORY_SEPARATOR,$cropped);
		$return[1] = str_replace(DIRECTORY_SEPARATOR, '/', $return[1]);
		return $return[1];
	}
	public function uploadThumbPicture($coordinate){
		$x1 = $coordinate["x1"];
		$y1 = $coordinate["y1"];
		$x2 = $coordinate["x2"];
		$y2 = $coordinate["y2"];
		$w = $coordinate["w"];
		$h = $coordinate["h"];
		
		$scale = 1;
		
        if(!empty($this->thumb_image_name)&&!empty($this->upload_path)){
            $file_ext = strtolower(substr($coordinate['largeimglocation'], strrpos($coordinate['largeimglocation'], '.') ));
           
            $thumb_image_name=$this->upload_path.$this->thumb_image_name.$file_ext;
        }else{
             $thumb_image_name=$coordinate['thumbimglocation'];
        }
        
		$cropped = $this->resizeThumbnailImage($thumb_image_name, $coordinate['largeimglocation'],$w,$h,$x1,$y1,$scale);
		$return = explode("upload".DIRECTORY_SEPARATOR,$cropped);
		$return[1] = str_replace(DIRECTORY_SEPARATOR, '/', $return[1]);
		return $return[1];
	}
}
?>