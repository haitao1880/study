<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:XMail.php
* 创建时间:下午5:33:23
* 字符编码:UTF-8
* 版本信息:$Id: XMail.php 10 2014-06-13 07:03:39Z tony_ren $
* 修改日期:$LastChangedDate: 2014-06-13 15:03:39 +0800 (周五, 13 六月 2014) $
* 最后版本:$LastChangedRevision: 10 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/protected/publib/comm/XMail.php $
* 摘    要:邮件类
*/
require_once(PUBLIB_PATH.'mail/class.phpmailer.php');
class XMail extends PHPMailer{
        public function __construct(){
            global $G_X;
            parent::__construct();
            $this->InitMail($G_X['email']);
        }
        public function InitMail($config){
            if($config['issmtp']){
                $this->CharSet=$config['charset'];
                $this->IsSMTP();
                $this->Host=$config['host'];
                $this->SMTPAuth=true;
                $this->Username=$config['username'];
                $this->Password=$config['password'];
                $this->Port=($config['port'])?$config['port']:25;
                $this->From = $config['username'];
                $this->FromName = $config['fromname'];
            }
        }
        /**
     * 给用户邮箱发送邮件
     * @author liyanbing
     * @param string $email 邮箱地址
     * @return array
     */
    public function SendEmail($ToEmail,$Subject,$Content,$isHTML=true){
        $this->AddAddress($ToEmail,"");
        $this->IsHTML($isHTML);
        $this->Subject=$Subject;
        $this->Body=$Content;
        return $this->Send();
        
    }
        
    

}
