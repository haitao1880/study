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
class Psys_NewsRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
		$this->SetDb('db-rht_idc');
		$this->SetTable("rhi_appnews");
	}
	/**
	 * 
	 * 获取待同步数据
	 * @param array $where 查询条件数组
	 * @return array $data 查询数组
	 */
	
	public function getSyncList($servicer)
	{
		$sql = 'SELECT * FROM ' . $this->GetTableName() . " WHERE servicer NOT LIKE '%" . $servicer . "%'";
		$data = $this->Query($sql);
		return $data;
	}
	
	
	/**
	 * 获取带同步数据总条数
	 */
	public function newsNum($servicer)
	{
		$sql = 'SELECT count(*) as num FROM ' . $this->GetTableName() . " WHERE servicer NOT LIKE '%" . $servicer . "%'";
		$data = $this->Query($sql);
		return $data[0];
	}
	
	
	
	
	/**
	 * 获取train下单条数据
	 * @param array $where 查询条件
	 * @param string $field 查询字段
	 */
	public function GetSyncOne($where,$field)
	{
		$this->SetDb('db-rht_train');
		$this->SetTable("rht_appnews");
		$one = $this->GetOne($field, $where);
		return $one;
	}
	/**
	 * 更新train下单条数据
	 * @param array $data 更新数据
	 * @param array $where 更新条件
	 * @return num
	 */
	public function UpdateSyncOne($data,$where)
	{
		$this->SetDb('db-rht_train');
		$this->SetTable("rht_appnews");
		$updateR = $this->Update($data,$where);
		return $updateR;
	}
	/**
	 * 新增train下单条数据
	 * @param array $data 新增数据数组
	 * 
	 */
	public function AddSyncOne($data)
	{
		$this->SetDb('db-rht_train');
		$this->SetTable("rht_appnews");
		$insertR = $this->Insert($data,true);
		return $insertR;		
	}
}

?>