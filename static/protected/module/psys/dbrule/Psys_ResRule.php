<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月15日                                                 
* 作　  者:peter
* E-mail  :peter@rockhippo.net
* 文 件 名:PSys_ResRule.php                                                
* 创建时间:下午3:08:29                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: PSys_AdsRule.php 626 2014-07-09 09:06:35Z peter $                                                 
* 修改日期:$LastChangedDate: 2014-07-09 17:06:35 +0800 (周三, 09 七月 2014) $                                     
* 最后版本:$LastChangedRevision: 626 $                                 
* 修 改 者:$LastChangedBy: peter $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/dbrule/PSys_ResRule.php $                                            
* 摘    要: 资源管理相关                                                      
*/

class PSys_ResRule extends PSys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
		$this->SetDb("db-rht_idc");		
	}
	
	/**
	 * 游戏列表
	 * @param num $typeid：1,游戏;2,应用
	 * @return Ambigous <array("allrow"=>array(),"allnum"=>0), NULL>
	 */
	public function GameList($typeid,$page=0,$pagesize=0,$field)
	{
		$this->SetDb("db-rht_train");
		$this->SetTable("rht_apps");		
		//$where=array('appcol'=>$typeid);
		$where=array();
		$list= $this->GetList($where,$field,'sortid desc, id desc',$pagesize,$page);
		$this->SetDb("db-rht_idc");
		return $list;
	}

	/**
	 * APP幻灯片列表
	 * @param unknown $appid
	 * @param number $page
	 * @param number $pagesize
	 * @param unknown $field
	 * @return Ambigous <array("allrow"=>array(),"allnum"=>0), NULL>
	 */
	public function GamePPTList($appid,$page=0,$pagesize=0,$field)
	{
		$this->SetDb("db-rht_train");		
		$this->SetTable("rht_appimg");
		$where=array('appid'=>$appid);
		$list= $this->GetList($where,$field,'sortid desc',$pagesize,$page);
		$this->SetDb("db-rht_idc");
		return $list;
		
	}
	/**
	 * 视频列表
	 * @param number $page
	 * @param number $pagesize
	 * @param unknown $field
	 */
	public function VideoList($page=0,$pagesize=0,$field)
	{
		$this->SetTable("rhi_videos");
		//$where=array('appcol'=>$typeid);
		$where=array();
		return $this->GetList($where,$field,'sortid desc',$pagesize,$page);
	}
	/**
	 * 根据appcol获取最大的appid;
	 * @param number $appcol
	 */
	public function GetAppid($appcol){
		$this->SetDb("db-rht_train");
		$sql = "select max(appid) as appid from rht_apps where appcol=".$appcol;
		$appid = $this->FetchColOne($sql);
		return $appid;
	}

}