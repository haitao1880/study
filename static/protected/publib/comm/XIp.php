<?php
/**
* Copyright(c) 2014
* 日    期:2014年11月12日
* 文 件 名:ip.php
* 创建时间:11:57
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Nick (nick@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:ip类
*/
class XIp{
    
    /**
    *
    * 获取当前访问ip
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function XGetIP(){
        
        if(!empty($_SERVER["HTTP_CLIENT_IP"])){
            
          $ip = $_SERVER["HTTP_CLIENT_IP"];
          
        }elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
            
          $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
          
        }elseif(!empty($_SERVER["REMOTE_ADDR"])){
            
          $ip = $_SERVER["REMOTE_ADDR"];
          
        }else{
            
          $ip = "none";
          
        }
        
        return $ip;
    }
    
}
