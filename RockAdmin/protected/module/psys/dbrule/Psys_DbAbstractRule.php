<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月7日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:Psys_DbAbstractRule.php                                                
* 创建时间:下午5:09:08                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: Psys_DbAbstractRule.php 626 2014-07-09 09:06:35Z tony_ren $                                                 
* 修改日期:$LastChangedDate: 2014-07-09 17:06:35 +0800 (周三, 09 七月 2014) $                                     
* 最后版本:$LastChangedRevision: 626 $                                 
* 修 改 者:$LastChangedBy: tony_ren $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/dbrule/Psys_DbAbstractRule.php $                                            
* 摘    要:数据层抽象类                                                       
*/
require_once PUBLIB_PATH.'/database/DbFactory.php';
require_once PUBLIB_PATH.'/database/DbAbstractRule.php';

class Psys_DbAbstractRule extends DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
	}
	
}
?>