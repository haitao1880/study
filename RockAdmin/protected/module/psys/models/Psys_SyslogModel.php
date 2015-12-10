<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月15日                                                 
* 作　  者:peter
* E-mail  :peter@rockhippo.net
* 文 件 名:Psys_syslogModel.php                                                
* 创建时间:下午3:21:47                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: Psys_syslogModel.php 681 2014-07-14 06:21:42Z jing $                                                 
* 修改日期:$LastChangedDate: 2014-07-14 14:21:42 +0800 (周一, 14 七月 2014) $                                     
* 最后版本:$LastChangedRevision: 681 $                                 
* 修 改 者:$LastChangedBy: jing $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/models/Psys_syslogModel.php $                                            
* 摘    要: 后台广告业务逻辑                                                      
*/

class Psys_SyslogModel extends Psys_AbstractModel
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * 写操作日志
	 * @param array $data
	 */
	public function admin_syslog($data)
	{
		
		$this->AddOne($data,"rha_syslog");
	}
}

?>