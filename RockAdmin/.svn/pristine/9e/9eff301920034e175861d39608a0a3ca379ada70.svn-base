<?php
class activityController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 伙伴推广页面
	 */
	public function spreadtrainappAction(){
		$nt = new Psys_StationModel();
		$stations = $nt->station();
		$this->smarty->assign('stations',$stations);
		$this->forward = "spreadtrainapp";
	}

	/**
	 * 伙伴推广数据
	 */
	public function spreadtrainappinfoAction(){
			$data = reqstr('data');
		// if (!$data) {
		// 	return;
		// }
		$data = urldecode($data);
		$info = array();
		parse_str($data,$info);

		//$date = (int)$info['date']?(int)$info['date']:7; //默认是查出7天的数据
		$sdate = trim($info['sdate'])?trim($info['sdate']):''; 
		$edate = trim($info['edate'])?trim($info['edate']):'';
		$station = (int)$info['station']?(int)$info['station']:0; // 0 表示查询出所有车站
		$hour = trim($info['time'])?trim($info['time']):''; 
		$from = trim($info['entrance'])?trim($info['entrance']):'all'; 
		$type = trim($info['type'])?trim($info['type']):'pv'; //默认是游戏

		if (!$sdate && !$edate) {
			$edate = $sdate = date('Y-m-d');
		}
		if(!$sdate || !$edate){
			if ($sdate) {
				$edate = $sdate;
			}else{
				$sdate = $edate;
			}

		}
		//
		$nt = new Psys_ActivityModel();
		$action = array('entrance'=>$from,
						'userindex'=>'userindex',
						'clickhqg'=>'clickhqg',
						'userhqg'=>'userhqg',
						'clickdmbj'=>'clickdmbj',
						'userdmbj'=>'userdmbj',
						'clickactivedetails'=>'clickactivedetails',
						'useractivedetails'=>'useractivedetails',
						'useractivedetailsdown'=>'useractivedetailsdown',
						'userhqgdown'=>'userhqgdown',
						'userdmbjdown'=>'userdmbjdown',
						'opentrainapp'=>'opentrainapp');
		foreach($action as $k => $v){
			$data_tab[$k] = $nt-> NewUserActivity($sdate,$edate,$station,$hour,$type,$v,$from);
		}

		return $data_tab;
	}



}