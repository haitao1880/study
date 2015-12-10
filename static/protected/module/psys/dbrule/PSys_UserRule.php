<?php
/**
* Copyright(c) 2014
* 日    期:2014年10月20日
* 文 件 名:{PSys_User}Rule.php
* 创建时间:11:23
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Nick (nick@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:游戏用户数据逻辑
*/
class PSys_UserRule extends PSys_DbAbstractRule{
    /**
    *
    * 继承构造函数
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
	public function __construct(){
		parent::__construct();
        $this->SetDb("db");
		$this->SetTable("ta_user");
	}
    
    /**
    *
    * proceduce create user
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param $data user
    * @return -
    *
    */
    public function RAddUser($data){
        
        $user = array();

		$user['username'] = $data['username'];
		$user['password'] = $data['password'];
		$user['email']    = $data['email'];
		$user['role_id']  = $data['role_id'];
        
		$user['nick']     = $data['nick'];
		$user['phone']    = $data['phone'];
		$user['address']  = $data['address'];
		$user['photo']    = $data['photo'];
		$user['age']      = $data['age'];
		$user['sex']      = $data['sex'];

		$user['creater_id']      = $data['creater_id'];
		$user['createtime']      = $data['createtime'];
		
		$str = "'".implode("','",$user)."',@output";
		
		return $this->QueryOneSql("call procedure_create_user(".$str.")");
        
    }
    
    /**
    *
    * proceduce edit user
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param $data user
    * @return -
    *
    */
    public function REditUser($data){
        
        $user = array();

		$user['password'] = $data['password'];
		$user['email']    = $data['email'];
		$user['role_id']  = $data['role_id'];
        
		$user['nick']     = $data['nick'];
		$user['phone']    = $data['phone'];
		$user['address']  = $data['address'];
		$user['photo']    = $data['photo'];
		$user['age']      = $data['age'];
		$user['sex']      = $data['sex'];
        
		$user['id']      = $data['id'];
		
		$str = "'".implode("','",$user)."',@output";
		
		return $this->QueryOneSql("call procedure_edit_user(".$str.")");
        
    }

}