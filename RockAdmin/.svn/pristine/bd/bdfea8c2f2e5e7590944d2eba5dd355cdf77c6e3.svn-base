<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月4日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:Psys_AdminUserModel.php                                                
* 创建时间:下午3:21:47                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: Psys_AdminUserModel.php 6814 2014-12-18 05:32:23Z allen $                                                 
* 修改日期:$LastChangedDate: 2014-12-18 13:32:23 +0800 (周四, 2014-12-18) $                                     
* 最后版本:$LastChangedRevision: 6814 $                                 
* 修 改 者:$LastChangedBy: allen $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/models/Psys_AdminUserModel.php $                                            
* 摘    要: 后台用户业务逻辑                                                      
*/

class Psys_AdminUserModel extends Psys_AbstractModel
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function EncrptPasswd($passwdPlain)
	{
		return sha1($passwdPlain);
	}	
	
	/**
	 * 用户登录
	 * @param string $username 用户名
	 * @param string $passwd 明文密码
	 * @param array $result 错误信息
	 * @return multitype:Array|NULL 用户信息
	 */
	public function Login($username,$passwd,array &$result)
	{
		$passwd = $this->EncrptPasswd($passwd);
		$w = array('username'=>$username,'passwd'=>$passwd,'flag'=>1);
		$one = $this->GetOne($w);
		if(!$one)
		{//不存在此用户
			MsgInfoConst::GetMsg(1001, $result);
			return array();
		}
				
		$result['result'] = 'SUCCESS';
		
		$one['passwd'] = null;
		
		//缓存判别登录状态
		$memcache = XMemCache::GetInstance();
		$memcache->Set('Cur_X_User', 'isLogin');		
		$this->SetSessionInfo($one);
		
		return $one;
	}
	
	public function SetSessionInfo(array $one)
	{		
		XSession::Set("Cur_X_User", $one);
	}

}

?>