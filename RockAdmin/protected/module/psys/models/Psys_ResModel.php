<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月15日                                                 
* 作　  者:peter
* E-mail  :peter@rockhippo.net
* 文 件 名:Psys_ResModel.php                                                
* 创建时间:下午3:21:47                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: Psys_syslogModel.php 681 2014-07-14 06:21:42Z jing $                                                 
* 修改日期:$LastChangedDate: 2014-07-14 14:21:42 +0800 (周一, 14 七月 2014) $                                     
* 最后版本:$LastChangedRevision: 681 $                                 
* 修 改 者:$LastChangedBy: jing $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/models/Psys_ResModel.php $                                            
* 摘    要: 资源相关业务逻辑                                                      
*/
class Psys_ResModel extends Psys_AbstractModel {
	public function __construct() {
		parent::__construct ();
	}
	
	/**
	 * 新增音乐
	 * 
	 * @param $data	数据数组
	 */
	
	public function addMusic($data)
	{
		$returnId = $this->AddOne($data,'rhi_albummusic');
		$this->Record($data,$returnId,'db-rht_sync','albummusic','rhs_downsync');
		return $returnId;
	}
	
	/**
	 * 修改音乐
	 * 
	 * @param $data	数据数组
	 * @param $where	更新条件
	 */
	public function updateMusic($data,$where)
	{
		$affectNum = $this->UpdateOne($data,$where,'rhi_albummusic');
		$this->Record($data,$affectNum,'db-rht_sync','albummusic','rhs_downsync');
		return $affectNum;
	}
	
	/**
	 * 删除音乐
	 * 
	 * @param unknown_type $where	删除条件
	 */
	
	public function deleteOneMusic($where)
	{
		$affectNum = $this->deleteOne($where,'rhi_albummusic');
		return $affectNum;
	}
	/**
	 * 专辑添加
	 * 
	 * @param $data	专辑数据信息
	 */
	
	public function AddAlbum($data)
	{
		$returnId = $this->AddOne($data,'rhi_album');
		$this->Record($data,$returnId,'db-rht_sync','album','rhs_downsync');
	}
	
	/**
	 * 专辑信息编辑
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $where
	 */
	
	public function UpdateAlbum($data,$where)
	{
		$affectNum = $this->UpdateOne($data, $where,'rhi_album');
		$this->Record($data,$affectNum,'db-rht_sync','album','rhs_downsync');
	}
	
	
	
	/**
	 * 删除专辑
	 * Enter description here ...
	 * @param $where
	 */
	public function deleteOneAlbum($where)
	{
		$affectNum = $this->DeleteOne($where,'rhi_album');
		return $affectNum;
	}
	
	
	/**
	 * 新增游戏
	 * 
	 * @param unknown $data        	
	 */
	public function AddGame($data) {
		$this->SetDb("db-rht_train");
		//$this->SetTable("rht_apps");		
		$res = $this->AddOne ( $data, "rht_apps" );
		$this->SetDb('db-rht_idc');
		$this->Record($data,$res,'db-rht_sync','apps','rhs_downsync');		
		return $res;
	}
	/**
	 * 添加游戏PPT
	 * @param unknown $data
	 */
	public function AddGamePPT($data) {
		$this->SetDb("db-rht_train");
		$res = $this->AddOne ( $data, "rht_appimg" );
		$this->SetDb('db-rht_idc');
		return $res;
	}
	/**
	 * 删除APP的PPT信息
	 * @param unknown $where
	 */
	public function DelOneGamePPT($where) {
		$this->SetDb("db-rht_train");	
		$this->DeleteOne( $where, "rht_appimg" );
		$this->SetDb('db-rht_idc');
	}
	/**
	 * 修改游戏
	 * 
	 * @param unknown $data        	
	 * @param unknown $where        	
	 */
	public function UpdateGame($data, $where) {
		$this->SetDb("db-rht_train");
		$res = $this->UpdateOne ( $data, $where, "rht_apps" );
		$this->SetDb('db-rht_idc');
		$this->Record($data,$res,'db-rht_sync','apps','rhs_downsync');
		return $res;	
	}
	/**
	 * \
	 * 得到游戏APP信息
	 */
	public function GetOneGame($where,$ws = '*') {
		$this->SetDb("db-rht_train");
		$one=$this->GetOne ( $where,$ws,"rht_apps" );
		//$this->SetDb("db-rht_idc");
		return $one;
	}
	/**
	 * 删除APP信息
	 * @param unknown $where
	 */
	public function DelOneGame($where) {
		$this->SetDb("db-rht_train");
		$affectedNum = $this->DeleteOne( $where, "rht_apps" );
		$this->SetDb("db-rht_idc");
		return $affectedNum;
	}
	
	
	/**
	 * 新增视频
	 *
	 * @param unknown $data
	 */
	public function AddVideo($data) {
		$res = $this->AddOne ( $data, "rhi_videos" );
		$this->Record($data,$res,'db-rht_sync','videos','rhs_downsync');
		return $res;
	}
	/**
	 * 修改视频
	 *
	 * @param unknown $data
	 * @param unknown $where
	 */

	public function UpdateVideo($data, $where) {
		$res = $this->UpdateOne ( $data, $where, "rhi_videos" );
		$this->Record($data,$res,'db-rht_sync','videos','rhs_downsync');
		return $res;
	}
	/**
	 * 添加统计记录
	 *
	 * @param array $data
	 * @param unknown $res
	 * @param string $db
	 * @param string $res_table
	 * @param string $dst_table
	 */
	public function Record($data,$res,$db,$res_table,$dst_table){
		if ($res) {
					global $G_X;
					$write['syncdata'] = json_encode($data);
					$write['optype'] =$G_X['tableno'][$res_table];
					$this->SetDb($db);
					return $this->AddOne($write,$dst_table);
				}
	}
	/**
	 * \
	 * 得到视频信息
	 */
	public function GetOneVideo($where) {
		return 	$this->GetOne ( $where,'*',"rhi_videos" );
	}
	/**
	 * 删除视频信息
	 * @param unknown $where
	 */
	public function DelOneVideo($where) {
		$this->DeleteOne( $where, "rhi_videos" );
	}
	/**
	 * 根据appcol获取appid
	 * Enter description here ...
	 * @param unknown_type $appcol	应用类型
	 */
	public function GetAppid($appcol){
		$m = new Psys_ResRule();
		$appid = $m->GetAppid($appcol);
		return $appid;
	}

	// public function MusicList($where,$order,$page, $pagesize,$field,$tbname){
	// 	return $this->GetList($where,$order,$page, $pagesize,$field,$tbname);
	// }
	
	/**
	 * 
	 * 数据同步表写入
	 * @param int $id	类型ID
	 * @param bool $isdel	true 删除同步 false 更新同步
	 */
	
	public function syncDb($id,$isdel)
	{
		//获取指定数据
		$data = $this->GetOne(array('id'=>$id),'*','rhi_apps');		
		if($data['appcol'] == 1)
		{
			$type = 3;
		}
		else 
		{
			$type = 4;
		}
		$typeid = $id;
		//选择数据库
		$this->SetDb('db-rht_sync');
		
		
		if($isdel)	//删除同步
		{
			$method = 3;
			$data = array();
			$oldData = json_encode($data);			
			$syncData = array('type'=>$type,'typeid'=>$typeid,'method'=>$method,'dataInfo'=>$oldData,'flag'=>0,'ctime'=>time());
			$num = $this->AddOne($syncData,'rhs_sync');
			
			if($num)
			{
				return array('result'=>'SUCCESS');
			}
			else
			{
				return array('result'=>'ERROR');
			}		
		}
		else{		//更新同步
			$oldData = json_encode($data);
			if($data['utime'])
			{
				$method = 2;
			}
			else 
			{
				$method = 1;
			}
			//数据写入
			$syncData = array('type'=>$type,'typeid'=>$typeid,'method'=>$method,'dataInfo'=>$oldData,'flag'=>0,'ctime'=>time());
			$num = $this->AddOne($syncData,'rhs_sync');
			
			if($num)
			{
				$this->SetDb("db-rht_idc");
				$model = new Psys_ResModel();
				$data = array('unconfirm'=>0);
				$model->UpdateGame($data, array('id'=>$id));
				return array('result'=>'SUCCESS');
			}
			else
			{
				return array('result'=>'ERROR');
			}					
		}
	}
}

?>