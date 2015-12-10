<?php
/**
* Copyright(c) 2015
* 日    期:2015年04月17日
* 文 件 名:{Psys_User}Model.php
* 创建时间:11:33
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Nick (nick@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:用户模块模型层
*/
class PSys_UserModel extends PSys_AbstractModel{
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
	}
	
	/**
	 *
	 * @do Login api
	 *
	 * @access public
	 * @author Nick
	 * @copyright rockhippo
	 * @param $username username
	 * @param $password password
	 * @return result
	 *
	 */
    public function loginVerify($username,$password){
    	
    	GLOBAL $G_X;
    	$where = array();
    	$where['username'] = $username;
    	$where['password'] = md5($G_X['passAddTo'].$password);

    	//用户信息
    	$result = array();
    	$result = $this->GetOne($where,"id,username,nick,role_id,photo,email,sex,phone,status,app_array","view_user");
        
    	return $result;
    	
    }
    
    /**
	 *
	 * @do proceduce create user
	 *
	 * @access public
	 * @author Nick
	 * @copyright rockhippo
	 * @param $data array
	 * @return result
	 *
	 */
    public function MAddUser($data){
    	
    	$PSys_UserRule = new PSys_UserRule();
        
        return $PSys_UserRule->RAddUser($data);
        
    }
    
    /**
	 *
	 * @do proceduce edit user
	 *
	 * @access public
	 * @author Nick
	 * @copyright rockhippo
	 * @param $data array
	 * @return result
	 *
	 */
    public function MEditUser($data){
    	
    	$PSys_UserRule = new PSys_UserRule();
        
        return $PSys_UserRule->REditUser($data);
        
    }
    
    /**
	 *
	 * @do delete user
	 *
	 * @access public
	 * @author Nick
	 * @copyright rockhippo
	 * @param $id int
	 * @return result
	 *
	 */
    public function DeleteUser($id){
    	
        $pk_arr['id'] = $pk_arr_i['user_id'] = $id;
        
    	if($this->DeleteOne($pk_arr) && $this->DeleteOne($pk_arr_i,"ta_user_information")){
    	   return true;
    	}else{
    	   return false;
    	}
        
    }
    
    
    
	
    
}