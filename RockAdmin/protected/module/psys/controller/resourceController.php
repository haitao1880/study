<?php
class resourceController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * app或者游戏下载概况页面
	 * @return 
	 */
	public function downappAction(){
		$nt = new Psys_StationModel();
		$stations = $nt->station();
		$this->smarty->assign('stations',$stations);
		$this->forward = 'downapp';
	}

	/**
	 * app或者游戏下载概况数据
	 * @return json
	 */
	public function downappdataAction(){
		$data = reqstr('data');
		if (!$data) {
			return;
		}
		$page = reqnum('page',1);
		$pagesize = 10;
		$data = urldecode($data);
		$info = array();
		parse_str($data,$info);

		$date = (int)$info['date']?(int)$info['date']:7; //默认是查出7天的数据
		$sdate = trim($info['sdate'])?trim($info['sdate']):''; 
		$edate = trim($info['edate'])?trim($info['edate']):'';
		$station = (int)$info['station']?(int)$info['station']:0; // 0 表示查询出所有车站
		$type = (int)$info['type']?(int)$info['type']:1; //默认是游戏

		if (!$sdate || !$edate) {
			$edate = date('Y-m-d');
			$sdate = date('Y-m-d',strtotime("-$date day"));
		}

		//如果时间区间大于30天则按照30天计算
		$date_area = abs(strtotime($edate) - strtotime($sdate)) / 86400;
		
		if ($date_area > 30) {
			$sdate = date('Y-m-d',strtotime("-30 day"));
		}
		$sdate = str_replace('-','_',$sdate);
		$edate = str_replace('-','_',$edate);

		$nt = new Psys_ResourceModel();

		$data = $nt -> DownAppData($sdate,$edate,$station,$type,$page,$pagesize);
		$data_graph = $nt -> DownAppData($sdate,$edate,$station,$type,$page,$pagesize,1);
		
		$paging = $this->paging($data['allnum'],$page,$pagesize,count($data['allrow']));

		$stations = $nt->station();

		$total = 0;
		foreach($data['allrow'] as &$v){			
			if ($station) {
				$v['stationname'] = $this -> getstationname($stations,$v['stationid']);
			}else{
				$v['stationname'] = 'ALL';
			}
			$total += $v['num'];
		}
		
		$datas[0]['type'] = 'pie';
		$datas[0]['name'] = '下载占比';
		foreach($data_graph['allrow'] as $v1){
			$datas[0]['data'][] = array($v1['appname'],(float)round(($v1['num']/$total)*100,2));			
		}
		
		$result['y_cat'] = $datas;
		return array('table'=>$data['allrow'],'paging'=>$paging,'graph'=>$result);

	}

	/**
	 * 下载详情
	 * 
	 */
	public function downappinfoAction(){
		$data = reqstr('data');
		$appid = reqnum('appid',12);
		if (!$appid) {
			return;
		}
		
		$data = urldecode($data);
		$info = array();
		parse_str($data,$info);

		$date = (int)$info['date']?(int)$info['date']:7; //默认是查出7天的数据
		$sdate = trim($info['sdate'])?trim($info['sdate']):''; 
		$edate = trim($info['edate'])?trim($info['edate']):'';
		$station = (int)$info['station']?(int)$info['station']:0; // 0 表示查询出所有车站

		if (!$sdate || !$edate) {
			$edate = date('Y-m-d',strtotime("-1 day"));
			$sdate = date('Y-m-d',strtotime("-$date day"));
		}
		
		$sdate = str_replace('-','_',$sdate);
		$edate = str_replace('-','_',$edate);

		$nt = new Psys_ResourceModel();

		$data = $nt -> DwonAppInfo($appid,$station,$sdate,$edate);
		
		foreach($data['allrow'] as $v){
			$x_date = $result['x_cat'][] = date('m/d',strtotime(str_replace('_','-',$v['date'])));

			$datas[0]['name'] = $v['appname'];

			$eventrecordinfo = $nt -> EventRecordInfo($v['date'],$v['type'],$v['stationid']);

			//返回特殊点的提示信息
			if (!empty($eventrecordinfo)) {
				
				$title = $eventrecordinfo['title'];
				$descript = $eventrecordinfo['descript'];
			}else{			
				$title = '';
				$descript = '';
			}
			$datas[0]['infos']['date'.$x_date]['title'] = $title;
			$datas[0]['infos']['date'.$x_date]['descript'] = $descript;

			if (empty($eventrecordinfo)) {
				$datas[0]['data'][] = (int)$v['num'];
			}else{
				$datas[0]['data'][] = array('y'=>(int)$v['num'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}
			
		}
		
		$result['y_data'] = $datas;

		$stations = $nt->station();
		if ($station) {
				$stationname = $this -> getstationname($stations,$station);
			}else{
				$stationname = ' 所有车站 ';
			}
		$result['title'] = $result['x_cat'][0].'-'.end($result['x_cat']).$stationname.'下载趋势图';
		return $result;
	}


	/**
	 * 根据id获取车站名
	 * @param  int $stationid 车站id
	 * @param  array $stations 车站信息
	 * @return string     车站名
	 */
	private function getstationname($stations,$stationid){
		foreach($stations as $v){
			if ($v['id'] == $stationid) {
				$curstation = $v['stationname'];
			}
		}

		return $curstation;
	}


	/**
	 * 记录事件添加
	 */
	public function eventaddAction(){
		$nt = new Psys_StationModel();
		$stations = $nt->station();
		$this->smarty->assign('stations',$stations);
		$this->forward = 'eventadd';
	}

	/**
	 * 添加记录数据
	 */
	public function eventadddataAction(){
		$data = reqstr('data');		
		$data = urldecode($data);
		$info = array();
		parse_str($data,$info);
		$title = trim($info['title'])?trim($info['title']):'';
		$evetype = trim($info['evetype'])?trim($info['evetype']):'';
		$date =  trim($info['date'])?trim($info['date']):'';
		$stationid = empty($info['stationid'])?'':implode(',',$info['stationid']);
		$type = empty($info['type'])?'':implode(',',$info['type']);
		$descript = trim($info['descript'])?trim($info['descript']):'';
		$isupdate = $info['isupdate'];

		if (!$title) {
			return array('code'=>0,'msg'=>'请输入标题');
		}
		if (!$date) {
			return array('code'=>0,'msg'=>'请选择时间');
		}
		if($evetype=='2'){
			if ($stationid == '') {
				return array('code'=>0,'msg'=>'请选择车站');
			}
		}
		if ($type == '') {
			return array('code'=>0,'msg'=>'请选择类型');
		}
		if (!$descript) {
			return array('code'=>0,'msg'=>'请输入描述');
		}
		$date = str_replace('-','_',$date);
		$adduser = $this->cur_user['realname'];
		$data = array('type'=>$type,'date'=>$date,'stationid'=>$stationid,'descript'=>$descript,'title'=>$title,'adduser'=>$adduser,'evetype'=>$evetype);
		$m = new Psys_StationModel();

		//判断是添加还是更新
		if (!$isupdate) {
			$res = $m->AddOne($data,'rha_eventrecord');
		}else{
			$id = $info['id'];
			$res = $m->UpdateOne($data,array('id'=>$id),'rha_eventrecord');
		}

		if ($res) {
			$mailbody = $date.'日，'.$descript;
			$ee = $this->sendemail($title,$mailbody);
			return array('code'=>1,'msg'=>'操作成功');
		}
		return array('code'=>0,'msg'=>'操作失败');
	}


	//记录列表
	public function eventlistAction(){
		$page = reqnum('page',1);
		$pagesize = 6;
		$m = new Psys_StationModel();
		$data = $m -> GetList(array('is_delete'=>0),'date desc',$page,$pagesize,"id,title,date,descript,adduser",'rha_eventrecord');
		if ($data['allnum']) {
			$data['paging'] = $this->paging($data['allnum'],$page,$pagesize,count($data['allrow']));
		}else{
			$data['paging'] = '';
		}
		
		return $data;
	}


	//删除记录信息
	public function deleventAction(){
		$id = reqnum('id');
		if (!$id) {
			return;
		}
		$m = new Psys_StationModel();
		$data = $m -> UpdateOne(array('is_delete'=>1),array('id'=>$id),'rha_eventrecord');
		if ($data) {
			return array('code'=>1,'msg'=>'删除成功');
		}
		return array('code'=>0,'msg'=>'删除失败');
	}

	/**
	 * 一键上网报警值设定页面
	 */
	public function gonetalarmAction(){
		$nt = new Psys_StationModel();
		$stations = $nt->station();
		$this->smarty->assign('stations',$stations);
		$this->forward = 'gonetalarm';
	}


	/**
	 * 添加一键上网报警数据
	 */
	public function gonetalarmaddAction(){
		$data = reqstr('data');
		
		$data = urldecode($data);
		$info = array();
		parse_str($data,$info);

		$stationid = (int)$info['stationid']?(int)$info['stationid']:0;
		$type = (int)$info['type']?(int)$info['type']:0;
		$stime = trim($info['stime'])?trim($info['stime']):'';
		$etime = trim($info['etime'])?trim($info['etime']):'';
		$trigger = (int)$info['trigger']?(int)$info['trigger']:0;
		$isupdate = $info['isupdate'];

		if (!$stationid) {
			return array('code'=>0,'msg'=>'请选择车站');
		}

		if (!$type) {
			return array('code'=>0,'msg'=>'请选择触发类型');
		}

		if (!$stime) {
			return array('code'=>0,'msg'=>'请选择起始时间');
		}
		if (!$etime) {
			return array('code'=>0,'msg'=>'请选择结束时间');
		}
		
		if (!$trigger) {
			return array('code'=>0,'msg'=>'请输入触发值');
		}
		
		$data = array('stationid'=>$stationid,'type'=>$type,'stime'=>$stime,'etime'=>$etime,'trigger'=>$trigger);
		$m = new Psys_StationModel();

		//判断是添加还是更新
		if (!$isupdate) {
			$res = $m->AddOne($data,'rha_gonet_trigger');
		}else{
			$id = $info['id'];
			$res = $m->UpdateOne($data,array('id'=>$id),'rha_gonet_trigger');
		}

		if ($res) {
			return array('code'=>1,'msg'=>'操作成功');
		}

		return array('code'=>0,'msg'=>'操作失败');
	}


	//一键上网报警列表
	public function gonetalarmlistAction(){
		$page = reqnum('page',1);
		$pagesize = 10;
		$m = new Psys_StationModel();
		$data = $m -> GetList(array('is_delete'=>0),'stationid asc',$page,$pagesize,"*",'rha_gonet_trigger');
		$stations = $m->station();
		if ($data['allnum']) {

			$data['paging'] = $this->paging($data['allnum'],$page,$pagesize,count($data['allrow']));

			foreach($data['allrow'] as &$v){
				$v['stationid'] = $this -> getstationname($stations,$v['stationid']);
				if ($v['type'] == 2) {
					$v['type'] = '一键上网成功次数不足触发';
				}elseif($v['type'] == 1){
					$v['type'] = '一键上网失败次数过多触发';
				}
				$v['stime'] = $v['stime'].'点';
				$v['etime'] = $v['etime'].'点';
			}

		}else{
			$data['paging'] = '';
		}
		
		return $data;
	}

	//一键上网报警日志删除
	public function delgonetalarmAction(){
		$id = reqnum('id');
		if (!$id) {
			return;
		}
		$m = new Psys_StationModel();
		$data = $m -> UpdateOne(array('is_delete'=>1),array('id'=>$id),'rha_gonet_trigger');
		if ($data) {
			return array('code'=>1,'msg'=>'');
		}
		return array('code'=>0,'msg'=>'删除失败');
	}

	/**
	 * 用户抽奖新活动统计页面
	 */
	public function NewUserLotteryPageAction(){
		$nt = new Psys_StationModel();
		$stations = $nt->station();
		$this->smarty->assign('stations',$stations);
		$this->forward = 'newactivity';
	}

	/**
	 * 用户抽奖活动统计页面
	 */
	public function UserLotteryPageAction(){
		$nt = new Psys_StationModel();
		$stations = $nt->station();
		$this->smarty->assign('stations',$stations);
		$this->forward = 'useractivity';
	}

	/**
	 * 用户抽奖活动统计页面
	 */
	public function NewUserLotteryInfoAction(){
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
		//wifi、首页、总下载、活动下载、抽奖人数只统计UV
		$nt = new Psys_ResourceModel();
		$action = array('wifi'=>'wifi','sindex_uv'=>'sindex_uv','totaldown'=>'totaldown',
						'alertwindow'=>'alertwindow','alertdraw'=>'alertdraw',
						'alertclose'=>'alertclose','redpacket'=>'redpacket',
						'activity_window'=>'activity_window',
						'activity_redpacket'=>'activity_redpacket',
						'activdown'=>'activdown','lottery'=>'lottery');
		foreach($action as $k => $v){
			$data_tab[$k] = $nt-> NewUserActivity($sdate,$edate,$station,$hour,$type,$v);
		}

		return $data_tab;
	}

	/**
	 * 用户抽奖活动统计页面
	 */
	public function UserLotteryInfoAction(){
		$data = reqstr('data');
		// if (!$data) {
		// 	return;
		// }
		$page = reqnum('page',1);
		$pagesize = 10;
		$data = urldecode($data);
		$info = array();
		parse_str($data,$info);

		$date = (int)$info['date']?(int)$info['date']:7; //默认是查出7天的数据
		$sdate = trim($info['sdate'])?trim($info['sdate']):''; 
		$edate = trim($info['edate'])?trim($info['edate']):'';
		$station = (int)$info['station']?(int)$info['station']:0; // 0 表示查询出所有车站
		$hour = trim($info['time'])?trim($info['time']):''; 
		$type = trim($info['type'])?trim($info['type']):'pv'; //默认是游戏

		if (!$sdate && !$edate) {
			$edate = date('Y-m-d');
			$sdate = date('Y-m-d',strtotime("-$date day"));
		}
		if(!$sdate || !$edate){
			if ($sdate) {
				$edate = $sdate;
			}else{
				$sdate = $edate;
			}

		}
		
		$sdate = str_replace('-','_',$sdate);
		$edate = str_replace('-','_',$edate);

		$nt = new Psys_ResourceModel();
		$action = array('alertclose'=>'alertclose','alertdraw'=>'alertdraw','redpacket'=>'redpacket','activity'=>'activity','message'=>'message','question'=>'question','lottery'=>'lottery');

		$res_tab = array();
		$res_grap = array();
		$allowstation = array(1,2);
		foreach($action as $k => $v){
			$data_tab[$k] = $nt-> UserActivity($v,$page,$pagesize,$sdate,$edate,$station,$hour,$type);
			
			$data_grap[$k] = $nt-> UserActivity($v,$page,$pagesize,$sdate,$edate,$station,$hour,$type,1);
			if ($data_tab[$k]['allrow']) {
				if (in_array($station,$allowstation)) {
					$res_tab[$k] = $data_tab[$k]['allrow'] * 2;
				}else{
					$res_tab[$k] = $data_tab[$k]['allrow'] * 4;
				}
				
			}else{
				$res_tab[$k] = 0;	
			}

			if ($data_grap[$k]['allrow']) {
				if (in_array($station,$allowstation)) {
					array_push($res_grap,(int)$data_grap[$k]['allrow']*2);	
				}else{
					array_push($res_grap,(int)$data_grap[$k]['allrow']*4);	
				}
				
			}else{
				array_push($res_grap,0);	
			}	
		}

		$result['x_cat'] = array('关闭弹窗','弹窗抽奖','红包点击','活动页面','短信发送','问卷调查','抽奖统计');
		$datas[0]['name'] = '用户抽奖活动流程';
		$datas[0]['data'] = $res_grap;
		$result['table'] = $res_tab;
		$result['y_data'] = $datas;
		return $result;
	}


	/**
	 * 抽奖活动页面app下载
	 */
	public function activitydownappAction(){
		$data = reqstr('data');		
		$data = urldecode($data);
		$info = array();
		parse_str($data,$info);

		$date = (int)$info['date']?(int)$info['date']:7; //默认是查出7天的数据
		$sdate = trim($info['sdate'])?trim($info['sdate']):''; 
		$edate = trim($info['edate'])?trim($info['edate']):'';
		$station = (int)$info['station']?(int)$info['station']:0; // 0 表示查询出所有车站
		$hour = trim($info['time'])?trim($info['time']):'';

		if (!$sdate && !$edate) {
			$edate = date('Y-m-d');
			$sdate = date('Y-m-d',strtotime("-$date day"));
		}

		if(!$sdate || !$edate){
			if ($sdate) {
				$edate = $sdate;
			}else{
				$sdate = $edate;
			}

		}
		
		$sdate = str_replace('-','_',$sdate);
		$edate = str_replace('-','_',$edate);

		$nt = new Psys_ResourceModel();
		$data = $nt -> activitydownapp($sdate,$edate,$station,$hour);
		$total = 0;
		foreach($data as $v1){
			$total += $v1['num'] * 5;
		}

		if ($sdate == $edate) {
			$x_cat = $sdate.'日 ';
		}else{
			$x_cat = $sdate.'日 至 '.$edate.'日 ';
		}
		if ($hour) {
			$x_cat .= $hour.'时';
		}
		$result['x_cat'] = array($x_cat);
		$datass[0]['type'] = 'pie';
		$datass[0]['name'] = '下载占比';
		$i = 0;
		foreach($data as $v){
			$datas[$i]['name'] = $v['appname'];
			$datas[$i]['data'][] = (int)$v['num']*5;
			$i++;
			$datass[0]['data'][] = array($v['appname'],(float)round(($v['num']*5/$total)*100,2));
		}
		
		$result['y_data'] = $datas;
		$result['y_cat'] = $datass;
		return $result;

	}


	/**
	 * 手机品牌、浏览器、系统排行页面
	 */
	public function mobilerankAction(){
		$nt = new Psys_StationModel();
		$stations = $nt->station();
		$this->smarty->assign('stations',$stations);
		$this->forward = 'mobilerank';
	}

	/**
	 * 手机品牌、浏览器、系统排行数据
	 */
	public function mobilerankdataAction(){
		$data = reqstr('data');
		if (!$data) {
			return;
		}
		$page = reqnum('page',1);
		$pagesize = 10;
		$data = urldecode($data);
		$info = array();
		parse_str($data,$info);

		$date = (int)$info['date']?(int)$info['date']:7; //默认是查出7天的数据
		$sdate = trim($info['sdate'])?trim($info['sdate']):''; 
		$edate = trim($info['edate'])?trim($info['edate']):'';
		$station = (int)$info['station']?(int)$info['station']:0; // 0 表示查询出所有车站
		$type = trim($info['type'])?trim($info['type']):'mobile'; //默认是手机

		if (!$sdate || !$edate) {
			$edate = date('Y-m-d');
			$sdate = date('Y-m-d',strtotime("-$date day"));
		}
		
		$sdate = str_replace('-','_',$sdate);
		$edate = str_replace('-','_',$edate);

		$nt = new Psys_ResourceModel();

		$data = $nt -> MobileRankData($sdate,$edate,$station,$type,$page,$pagesize);
		$data_graph = $nt -> MobileRankData($sdate,$edate,$station,$type,$page,$pagesize,1);
		
		$paging = $this->paging($data['allnum'],$page,$pagesize,count($data['allrow']));

		$stations = $nt->station();

		$total = 0;
		foreach($data['allrow'] as &$v){			
			if ($station) {
				$v['stationname'] = $this -> getstationname($stations,$station);
			}else{
				$v['stationname'] = 'ALL';
			}
			$total += $v['num'];
		}
		
		$phonebrand = array('mobile'=>'手机品牌','browser'=>'浏览器','sys'=>'手机系统');
		
		$datas[0]['type'] = 'pie';
		$datas[0]['name'] = $phonebrand[$type].'使用占比';
		foreach($data_graph['allrow'] as $v1){
			$datas[0]['data'][] = array($v1['name'],(float)round(($v1['num']/$total)*100,2));			
		}
		
		$result['y_cat'] = $datas;
		return array('table'=>$data['allrow'],'paging'=>$paging,'graph'=>$result);
	}

}