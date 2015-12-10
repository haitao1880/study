<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:PSys_DbAbstractRule.php
* 创建时间:下午5:28:32
* 字符编码:UTF-8
* 版本信息:$Id: PSys_DbAbstractRule.php 73 2014-07-03 07:53:02Z tony_ren $
* 修改日期:$LastChangedDate: 2014-07-03 15:53:02 +0800 (周四, 03 七月 2014) $
* 最后版本:$LastChangedRevision: 73 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/protected/module/psys/dbrule/PSys_DbAbstractRule.php $
* 摘    要:数据层抽象类
*/
require_once PUBLIB_PATH.'/database/DbFactory.php';
require_once PUBLIB_PATH.'/database/DbAbstractRule.php';

class PSys_DbAbstractRule extends DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
	}
	
}
?>