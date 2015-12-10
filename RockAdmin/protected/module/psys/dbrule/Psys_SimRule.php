<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月20日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:Psys_SimRule.php                                                
* 创建时间:下午3:11:05                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id$                                                 
* 修改日期:$LastChangedDate$                                     
* 最后版本:$LastChangedRevision$                                 
* 修 改 者:$LastChangedBy$                                      
* 版本地址:$HeadURL$                                            
* 摘    要: SIM数据逻辑                                                      
*/
class Psys_SimRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
		$this->SetTable("rha_sim");
	}
}

?>