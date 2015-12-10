<?php
/**
* Copyright(c) 2014
* 日    期:2014年3月27日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:v.php                                                
* 创建时间:下午2:05:09                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: v.php 626 2014-07-09 09:06:35Z tony_ren $                                                 
* 修改日期:$LastChangedDate: 2014-07-09 17:06:35 +0800 (周三, 09 七月 2014) $                                     
* 最后版本:$LastChangedRevision: 626 $                                 
* 修 改 者:$LastChangedBy: tony_ren $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/public/psys/v.php $                                            
* 摘    要:验证码                                                       
*/
$pubcomm = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."protected".DIRECTORY_SEPARATOR."publib".DIRECTORY_SEPARATOR."comm".DIRECTORY_SEPARATOR;
require_once $pubcomm.'XSession.php';

   //随机生成一个4位数的数字验证码
    $num="";
    for($i=0;$i<4;$i++){
		$num .= rand(0,9);
    }
    
   //4位验证码也可以用rand(1000,9999)直接生成
   //将生成的验证码写入session，备验证页面使用
    //Session_start();
    XSession::Init();
    XSession::Set("avacode", $num);
    
   //创建图片，定义颜色值
    //Header("Content-type: image/PNG");
    srand((double)microtime()*1000000);
    $im =  imagecreatetruecolor(60,20);
    $black = ImageColorAllocate($im, 0,0,0);
    $gray = ImageColorAllocate($im, 200,200,200);
    imagefill($im,0,0,$gray);

    //随机绘制两条虚线，起干扰作用
    $style = array($black, $black, $black, $black, $black, $gray, $gray, $gray, $gray, $gray);
    imagesetstyle($im, $style);
    $y1=rand(0,20);
    $y2=rand(0,20);
    $y3=rand(0,20);
    $y4=rand(0,20);
    imageline($im, 0, $y1, 60, $y3, IMG_COLOR_STYLED);
    //imageline($im, 0, $y2, 60, $y4, IMG_COLOR_STYLED);

    //在画布上随机生成大量黑点，起干扰作用;
    for($i=0;$i<3;$i++)
    {
		imagesetpixel($im, rand(0,60), rand(0,20), $black);
    }
    //将四个数字随机显示在画布上,字符的水平间距和位置都按一定波动范围随机生成
    $strx=rand(3,8);
    for($i=0;$i<4;$i++){
		$strpos=rand(1,6);
		imagestring($im,5,$strx,$strpos, substr($num,$i,1), $black);
		$strx+=rand(8,12);
    }
    ob_start();
    imagepng($im);
    $basestr = ob_get_contents();
    ob_end_clean();
    ImageDestroy($im);
    $basestr = base64_encode($basestr);
    $result = array('result'=>'SUCCESS','imageBase64'=>$basestr,'code'=>$num);
    
    
    header('Content-type: application/json');
    exit (json_encode($result));
?>