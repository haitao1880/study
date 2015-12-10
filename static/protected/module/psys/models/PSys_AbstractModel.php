<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:PSys_AbstractModel.php
* 创建时间:下午5:27:22
* 字符编码:UTF-8
* 版本信息:$Id: PSys_AbstractModel.php 73 2014-07-03 07:53:02Z tony_ren $
* 修改日期:$LastChangedDate: 2014-07-03 15:53:02 +0800 (周四, 03 七月 2014) $
* 最后版本:$LastChangedRevision: 73 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/protected/module/psys/models/PSys_AbstractModel.php $
* 摘    要:MODEL业务逻辑抽象类 
*/
require_once(PUBLIB_PATH."abstract".DIRECTORY_SEPARATOR."AbstractModel.php");
class PSys_AbstractModel extends AbstractModel
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
	
	public function SysLog($Message,$AddSql=true){
		$OpLogRule=$this->GetClassObj("PWeb_OpLogRule");
		if($AddSql)$Message.=' '.$this->GetQueryString();
		$OpLogRule->AddLog($Message);
	}
    
	/**
	 * 写操作日志
	 * @param array $data
	 */
	public function admin_syslog($data)
	{
		return;
		$dbrule = new Psys_SyslogModel();
		$dbrule->admin_syslog($data);
	
	}

	/**
	 * 根据车站id获取车站名
	 */
	public function GetStationName($stationid){
		$this->SetDb('rha_admin');
		$sql = "select stationname from rha_station where id = $stationid";
		$res = $this->GetOne($sql);
		return $res['stationname'];
	}
       
}