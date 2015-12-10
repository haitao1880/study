<?php
/**                                  
* 摘    要:线路管理                                                      
*/

class Psys_TripModel extends Psys_AbstractModel
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 城市列表
	 */
	public function getTripList($where,$page,$pagesize){
		$list = array();
		$list = $this -> GetList($where, 'id ASC',$page, $pagesize,"*");
		return $list;
	}
	/**
	 * 城市列表
	 */
	public function getCityList($where,$page,$pagesize){
		$list = array();
		$list = $this -> GetList($where, 'szm ASC',$page, $pagesize,"*",'rht_trainstation');
		return $list;
	}
	/**
	 * 修改经纬度座标值 
	 */
	public function updateLng($data,$where){
		return $this->UpdateOne($data,$where,'rht_trainstation');
	}

}

?>