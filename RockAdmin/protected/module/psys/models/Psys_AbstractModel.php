<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月4日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:Psys_AbstractModel.php                                                
* 创建时间:下午5:07:31                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: Psys_AbstractModel.php 736 2014-07-15 09:37:14Z yangpan $                                                 
* 修改日期:$LastChangedDate: 2014-07-15 17:37:14 +0800 (周二, 15 七月 2014) $                                     
* 最后版本:$LastChangedRevision: 736 $                                 
* 修 改 者:$LastChangedBy: yangpan $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/models/Psys_AbstractModel.php $                                            
* 摘    要: 业务逻辑抽象类                                                      
*/
require_once(PUBLIB_PATH."abstract".DIRECTORY_SEPARATOR."AbstractModel.php");
class Psys_AbstractModel extends AbstractModel
{
	/**
	 * 构造函数
	 * @param string $cls RULE名，如PSys_UserRule,则cls="User"
	 * 暂不用获取调用类
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	public function __get($name)
	{
		if(strtolower($name) == "dbrule")
		{
			return new $this->clsname();
		}
	}
	
	/**
	 * 写操作日志
	 * @param array $data
	 */
	public function admin_syslog($data)
	{
		$dbrule = new Psys_SyslogModel();
		$dbrule->admin_syslog($data);
	
	}
}
?>