<?php
/**                                           
* 摘    要: 全屏广告管理                                                      
*/
require_once COMMON_PATH."XThumb.php";
class fulladsController extends Psys_AbstractController {
	protected  $stations;
	public function __construct() {
		$this->stations = $this->getStation();
		parent::__construct ();
	}
	
	private function getStation(){
		$m = new Psys_AdsModel ();
		return $m->getStationByAppkey();
	}
	
	/**
	 * 广告列表
	 */
	public function indexAction() {
		$m = new Psys_AdsModel ();
		$list = array();
		$appkey = reqstr('appkey','');
		if($appkey && in_array($appkey,array_keys($this->stations))){
			$stationlist = array();
			$stationlist[$appkey] = $this->stations[$appkey];
			$this->smarty->assign ( 'appkey', $appkey);
		}else{
			$stationlist = $this->stations;
			$this->smarty->assign ( 'appkey', '');
		}
		$list = array();
		if($stationlist){
			foreach ($stationlist as $key=>$value){
				$list[$key]['name']= $value['name'];
				$list[$key]['flag']= $value['flag'];
				$list[$key]['id']= $value['id'];
				$list[$key]['adsone'] = $m->GetAdsOne($value['adsone']);
				$list[$key]['adstwo'] = $m->GetAdsTwo($value['adstwo']);
			}	
		}
		//var_dump($list);
		$this->smarty->assign ( 'list', $list);
		$this->smarty->assign('stations',$this->stations);
		$this->forward = "index";
		//self::inidate ( $list ['allnum'], $page, $pagesize, count ( $list ['allrow'] ) );
	}
	
	/**
	 * 添加广告
	 */
	public function addAction() {
		$m = new Psys_AdsRule();
		$addOnelist = $m->GetAllAds(array('colid'=>5));
		$addTwolist = $m->GetAllAds(array('colid'=>6));
		$this->smarty->assign('onelist',$addOnelist);
		$this->smarty->assign('twolist',$addTwolist);
		$this->forward = "add";
	}
	
	public function addstationadsAction(){
		$name = reqstr('name','');
		$appkey = reqstr('appkey','');
		//广告一
		$adsone = reqstr('adsone','');
		//广告二
		$adstwo = reqstr('adstwo','');
		$result = array (
				'result' => 'ERROR'
		);
		if(!name){
			return $result;
		}
		if(!$appkey){
			return $result;
		}
		if(!$adsone && $adstwo){
			return $result;
		}
		
		$m = new Psys_AdsModel();
		$data = array();
		$data['name'] = $name;
		$data['appkey'] = $appkey;
		$data['adsone'] = $adsone;
		$data['adstwo'] = $adstwo;
		$data['ctime'] = time();
		$data['utime'] = time();
		$flag = $m->addStationAds($data);
		if ($flag)
		{
		$result ['result'] = 'SUCCESS';
		}
		return $result;
	}
	
	/**
	 * 更新广告位信息
	 */
	public function updateAction() {
		$ispost = reqnum ( 'ispost', 0 );
		if ($ispost == 1) {
			$id = reqnum('id');
			if(!$id){
				echo 'id invalid!';exit;
			}
			//广告一
			$adsone = reqstr('adsone','');
			//广告二
			$adstwo = reqstr('adstwo','');
			
			$rule = new Psys_AdsRule();
			$data = array();
			$data['adsone'] = $adsone;
			$data['adstwo'] = $adstwo;
			$data['utime'] = time();
			$flag = $rule->updateStationAds($data,array('id'=>$id));
				
			$result = array (
					'result' => 'ERROR'
			);	
			if ($flag) 			
			{
				$result ['result'] = 'SUCCESS';
			} 
			return $result;
		}
	}
	
	/**
	 * 转到广告修改页面
	 */
	public function editAction() {
		$id = reqnum('id',0); // 获取参数
		if (! $id) {
			echo 'empty id';
			exit ();
		}
		$m = new Psys_AdsModel ();
		$stationads = $m->getStationadsById($id);
		$adsOne = explode(',',$stationads['adsone']);
		$adsTwo = explode(',',$stationads['adstwo']);
		$this->smarty->assign ( 'appkey', $stationads['appkey']);
		$this->smarty->assign ( 'id', $stationads['id']);
		$this->smarty->assign ( 'station', $stationads['name']);
		
		$r = new Psys_AdsRule();
		$addOnelist = $r->GetAllAds(array('colid'=>5));
		foreach ($addOnelist as &$var){
			if(in_array($var['id'], $adsOne)){
				$var['select'] = 1;
			}else{
				$var['select'] = 0;
			}
		}
		$addTwolist = $r->GetAllAds(array('colid'=>6));
		foreach ($addTwolist as &$var){
			if(in_array($var['id'], $adsTwo)){
				$var['select'] = 1;
			}else{
				$var['select'] = 0;
			}
		}
		$this->smarty->assign('onelist',$addOnelist);
		$this->smarty->assign('twolist',$addTwolist);
		$this->forward = "edit";
	}
	
	
	/**
	 * 分页数据显示
	 *
	 * @param num $allcount
	 *        	总条数
	 * @param num $page        	
	 * @param num $pagesize        	
	 * @param num $cursize
	 *        	当前页的数据条数
	 */
	private function inidate($allcount, $page, $pagesize, $cursize) {
		$this->smarty->assign ( 'allcount', $allcount ); // 总数
		$allpage = ceil ( $allcount / $pagesize );
		$this->smarty->assign ( 'allpage', $allpage ); // 总页数
		$pagesize = ($pagesize % 2) ? $pagesize : $pagesize + 1; // 页码取偶数 = 步长
		$pageoffset = ($pagesize - 1) / 2; // 当前页 左右偏移
		$this->smarty->assign ( 'cur_page', $page ); // 当前页
		if ($allcount > $pagesize) {
			// 如果当前页小于等于左偏移
			if ($page <= $pageoffset) {
				$startNum = 1;
				$endNum = $pagesize;
			} else { // 如果当前页大于左偏移
			         // 如果当前页码右偏移超出最大分页数
				if ($page + $pageoffset >= $allcount + 1) {
					$startNum = $allcount - $pagesize + 1;
				} else {
					// 左右偏移都存在时的计算
					$startNum = $page - $pageoffset;
					$endNum = $page + $pageoffset;
				}
			}
		} else {
			$startNum = 1;
			$endNum = $allcount;
		}
		
		$this->smarty->assign ( 'startNum', $startNum ); // 当前从几开始
		$this->smarty->assign ( 'endNum', $endNum ); // 当前到几结束
		$this->smarty->assign ( 'sli', (($pagesize * ($page - 1) + 1)) ); // 当前页的起始第多少条
		$this->smarty->assign ( 'eli', ($pagesize * ($page - 1) + $cursize) ); // 当前页最后一条是第多少条
	}
	
	
	/**
	 * 删除广告
	 */
	public function deleteAction() {
		$ispost = reqnum ( 'ispost', 0 );
		if ($ispost == 1) {
			$id = reqnum('id',0);
			$result = array (
					'result' => 'ERROR'
			);
			if(!id){
				return $result;
			}
			$rule = new Psys_AdsRule();
			$flag = $rule->delStationAds(array('id'=>$id));
			if ($flag)
			{
				$result ['result'] = 'SUCCESS';
			}
			return $result;
		}
	}
	
	public function toggleadsAction(){
		$ispost = reqnum('ispost', 0);
		if ($ispost == 1) {
			$id = reqnum('id',0);
			$flag = reqnum('flag');
			$result = array (
					'result' => 'ERROR'
			);
			if(!$id){
				return $result;
			}
			$flag = empty($flag)?1:0;
			$rule = new Psys_AdsRule();
			$ret = $rule->updateStationAds(array('flag'=>$flag),array('id'=>$id));
			if ($ret)
			{
				$result ['result'] = 'SUCCESS';
			}
			return $result;
		}
	}
}
?>