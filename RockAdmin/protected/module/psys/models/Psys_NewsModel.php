<?php
/**                                  
* 摘    要: 设备管理                                                      
*/

class Psys_NewsModel extends Psys_AbstractModel
{
	public function __construct()
	{
		parent::__construct();
	}
	//获取列表
	public function GetNewsList($data = array(), $orderby = 'id DESC',$page, $pagesize){
		$where = ' 1=1 ';
		if(!empty($data['keyword'])){
			$where.=" and (title LIKE '%".$data['keyword']."%' or content LIKE '%".$data['keyword']."%')";
		}
		if(!empty($data['news_type'])){
			$where.=' and newstype = '.$data['news_type'];
		}
		if(!empty($data['indate'])){
			$where.=' and ctime >= '.strtotime($data['indate']);
		}
		if(!empty($data['todate'])){
			$where.=' and ctime <= '.strtotime($data['todate']);
		}
		$da = $this -> GetList($where, $orderby,$page, $pagesize,"*");
		return $da;
	}
	//编辑新闻
	public function editNews($id,$data){
		$id = (int)$id;
		if($id > 0){
			$where = array();
			$where['id'] = $id;		
			$pk_arr = array_filter($where);
			return $this->UpdateOne($data, $where,'rhi_appnews');
		}
		return '';
	}
	//获取单条新闻
	public function GetNewInfo($id){
		$id = (int)$id;
		if($id > 0){
			$where = array();
			$where['id'] = $id;		
			$where = array_filter($where);
			return $this->GetOne($where,"*",'rhi_appnews');
		}
		return '';
	}
	
	/**
	 * 
	 * 获取待同步数据
	 * @param array $where 查询条件数组
	 * @return array $data 查询数组
	 */
	
	public function getSyncList($servicer)
	{
		$rule = new Psys_NewsRule();
		$data = $rule->getSyncList($servicer);
		return $data;
	}
	
	/**
	 * 获取待同步总条数
	 */
	public function newsNum($servicer)
	{
		$rule = new Psys_NewsRule();
		$data = $rule->newsNum($servicer);
		return $data;
	}
	
	/**
	 * 获取train数据库下数据
	 */
	public function GetSyncOne($where,$field)
	{
		$rule = new Psys_NewsRule();
		$one = $rule->GetSyncOne($where,$field);
		return $one;
	}
	/**
	 * 更新train下数据
	 */
	public function UpdateSyncOne($data,$where)
	{
		$rule = new Psys_NewsRule();
		$updateR = $rule->UpdateSyncOne($data,$where);
		return $updateR;
	}
	/**
	 * 新增train下一条数据
	 * 
	 * 
	 */
	public function AddSyncOne($data)
	{
		$rule = new Psys_NewsRule();
		$insertR = $rule->AddSyncOne($data);
		return $insertR;
	}
	
}

?>