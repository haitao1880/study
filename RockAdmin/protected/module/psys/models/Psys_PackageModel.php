<?php
/**                                  
* 摘    要: 礼包管理                                                      
*/

class Psys_PackageModel extends Psys_AbstractModel
{
	public function __construct()
	{
		parent::__construct();
	}
	//获取列表
	public function GetPackageList($data = array(), $orderby = 'id DESC',$page, $pagesize){
		$where = ' 1=1 ';
		if(!empty($data['keyword'])){
			$where.=" and (name LIKE '%".$data['keyword']."%' or details LIKE '%".$data['keyword']."%')";
		}
		/*if(!empty($data['indate'])){
			$where.=' and ctime >= '.strtotime($data['indate']);
		}
		if(!empty($data['todate'])){
			$where.=' and ctime <= '.strtotime($data['todate']);
		}*/
		$da = $this -> GetList($where, $orderby,$page, $pagesize,"*");

		return $da;
	}
	//编辑
	public function editPackage($id,$data){
		$id = (int)$id;
		if($id > 0){
			$where = array();
			$where['id'] = $id;		
			$pk_arr = array_filter($where);
			return $this->UpdateOne($data, $where);
		}
		return '';
	}
	//获取单条
	public function GetPackageInfo($id){
		$id = (int)$id;
		if($id > 0){
			$where = array();
			$where['id'] = $id;		
			$where = array_filter($where);
			return $this->GetOne($where,"*");
		}
		return '';
	}
	//获取APP列表
	public function GetApplist($keyword=''){
		$this->SetDb('db-rht_train');
		$where = ' flag = 1 and appcol = 1';
		if(!empty($keyword)){
			$where.=" and (appname LIKE '%".$keyword."%')";
		}
		$apps = $this -> GetList($where, 'id desc',0, 0,"id,appid,appname",'rht_apps');
		return $apps;
	}
	
	public function UpdateIsPack($id)
	{
		if(isset($id)){
			$this->SetDb('db-rht_train');
			$where = array('appid'=>$id);
			return $tNum = $this->UpdateOne(array('ispack'=>1), $where,'rht_apps');
		}
		return null;
	}
	
	//获取最大id
	public function GetMaxPackID(){
		return $this->GetOne(array(),"max(id) as max");
	}
}

?>