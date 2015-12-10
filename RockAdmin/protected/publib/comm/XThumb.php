<?php
/**
* 摘要：图片缩略图类
* 
* author:terry
*/

class XThumb{
	protected $errno = 0;
    protected $error = array(
            1=>'图片不存在',
            2=>'不允许处理的图片格式',
           );
    /*
    *生成缩略图
    *parm $img 将要缩略的图片
    *parm $w   缩略后的宽度
    *parm $h   缩略后的高度
    */
    public function imgthumb($img,$w = 800,$h = 1280,$path=''){
        //获取图片信息
        $info = $this->imginfo($img);
        
        //判断图片是否使允许处理的图片
        if(!$info['ext']){
            $this->errno = 2;
            return false;
        }
        
        //拼凑用图片创建画布函数
        $pict = 'imagecreatefrom'.$info['ext'];
        $ori = $pict($img);

        //创建缩略图画布
        $small = imagecreatetruecolor($w,$h);
        $small_bg = imagecolorallocate($small,200,200,200);
        imagefill($small,0,0,$small_bg);

        //计算缩放比例
        if ($info['width'] >= $w) {
        	$dst_w = $w;
        }else{
        	$dst_w = $info['width'];
        }

        if ($info['height'] >= $h) {
        	$dst_h = $h;
        }else{
        	$dst_h = $info['height'];
        }
        // $bili = min($w/$info['width'],$h/$info['height']);

        // //计算缩放后的尺寸
        // $dst_w = $info['width'] * $bili;
        // $dst_h = $info['height'] * $bili;

        //计算留白的坐标
        $dst_x = ($w - $dst_w) / 2;
        $dst_y = ($h - $dst_h) / 2;

        //创建缩略图
        imagecopyresampled($small,$ori,$dst_x,$dst_y,0,0,$dst_w,$dst_h,$info['width'],$info['height']);

        //拼凑保存图片函数
        $save = 'image'.$info['ext'];
        if(!$path){
            $path = $img;
        }
       $save($small,$path);

       //销毁画布
       imagedestroy($ori);
       imagedestroy($small);
       return true;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param $img 待处理图片
	 * @param $width 目标图片宽度
	 * @param $height 目标图片高度
	 * @param $dst 目标图片存放路径
	 */
	
	public function imgZoom($img,$width,$height,$path)
	{
		//获取图片信息
		$img_info = $this->imginfo($img);
	  	//判断图片是否使允许处理的图片
        if(!$img_info['ext']){
            $this->errno = 2;
            return false;
        }
        
        //画布创建函数的自动生成
        $imgfun = 'imagecreatefrom' . $img_info['ext'];
        $res = $imgfun($img);
        //创建缩略图画布
        $dst = imagecreatetruecolor($width,$height);
        $dst_bg = imagecolorallocate($dst,255,255,255);
        imagefill($dst,0,0,$dst_bg);
        
        //创建处理后的图片
        imagecopyresampled($dst,$res,0,0,0,0,$width,$height,$img_info['width'],$img_info['height']);
        
        //拼凑保存图片函数
		if(!$path){
            $path = $img;
        }
        
        /*if($img_info['ext'] == 'jpg')
        {
        	$save = 'imagejpeg';
        	$save($dst,$path,100);
        }
        else
        {
        	$save = 'image'.$img_info['ext'];
        	$save($dst,$path);
        }*/
        
        imagepng($dst,$path,9);
        
	   //imagepng($dst,$path);
       //销毁画布
       imagedestroy($res);
       imagedestroy($dst);
       return true;
	}
	
	
	
	
     //获取图片信息
	public function imginfo($img){
	    
	    if(!$pic = getimagesize($img)){
	        $this->errno = 1;
	        return false;
	    }
	    $info['width'] = $pic[0];
	    $info['height'] = $pic[1];

	    switch($pic[2]){
	        case 1:
	            $info['ext'] = 'gif';
	            break;
	        case 2:
	            $info['ext'] = 'jpeg';
	            break;
	        case 3:
	            $info['ext'] = 'png';
	            break;
	        case 6:
	            $info['ext'] = 'wbmp';
	            break;
	        default:
	            $info['ext'] = false;
	    }
	    return $info;
	}

	//获取错误信息
	public function geterror(){
		return $this->error['errno'];
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>