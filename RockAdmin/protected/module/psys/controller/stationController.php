<?php
class stationController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	//车站列表
	public function stlistAction()
	{
		$model = new Psys_StationModel();
		$list = $model->getlist('', 'id ASC','', '',"*","rha_station");
		$this->smarty->assign('list',$list['allrow']);
		$this->forward = "stlist";
	}
	//添加车站
	public function addAction()
	{
		$this->forward = "add";
	}
	//处理添加车站
	public function doAddAction()
	{
		$station = reqstr('station');
		$acfile = reqstr('acfile');
		$logfile = reqstr('logfile');
		$logip = reqstr('logip');
		$ifconf = reqstr('ifconf');
		$data = array(
				'stationname' => $station,
				'acfile' => $acfile,
				'logfile' => $logfile,
				'logip' => $logip,
				'ifconf' => $ifconf,
		);
		$model = new Psys_StationModel();

		$re=$model->AddOne($data,"rha_station");
		
		$this->smarty->assign('ck',$re?1:0);    
		$this->forward = "opration";	
	}
	//车站修改
	public function editAction()
	{
		$id = reqnum('id',0);
		$model = new Psys_StationModel();
		$where = array('id'=>$id);
		$list = $model->GetOne($where,"*","rha_station");
		$this->smarty->assign('list',$list);
		
		$this->forward = "edit";
	}
	//处理车站修改
	public function doEditAction()
	{
		$id = reqnum('id',0);
		$station = reqstr('station');
		$acfile = reqstr('acfile');
		$logfile = reqstr('logfile');
		$logip = reqstr('logip');
		$ifconf = reqstr('ifconf');
		$data = array(
				'stationname' => $station,
				'acfile' => $acfile,
				'logfile' => $logfile,
				'logip' => $logip,
				'ifconf' => $ifconf,
		);
		$model = new Psys_StationModel();
		$where = array('id' => $id);
		$re = $model->UpdateOne($data,$where,"rha_station");
		
		$this->smarty->assign('ck',$re?1:0);
		$this->forward = "opration";

	}
	//车站删除
	public function delAction()
	{
		$id = reqnum('id',0);
		$this->forward = "del";
		$model = new Psys_StationModel();
		$where = array('id' => $id);
		$re = $model->DeleteOne($where,"rha_station");
		
		$this->smarty->assign('ck',$re?1:0);
		$this->forward = "opration";
	}
	//查询记录总数
	public function totalrows()
	{
		$model = new Psys_StationModel();
		return $model->totalrows();
	}
	//新流程查询记录总数
	public function newtotalrows()
	{
		$model = new Psys_StationModel();
		return $model->newtotalrows();
	}
	//注册统计
	public function regAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$stationid = reqnum('stationid',1);

		$model = new Psys_StationModel();
		$stations = $model->station();
		$data = $model->regCount($page,$pagesize,$stationid);
		foreach ($data as &$v)
		{
			$v += array('date'=>'0','getphonecode'=>'0','submitreg'=>'0','regsuccessios'=>'0','regsuccessandroid'=>'0','regsuccesswp'=>'0','regsuccesselse'=>'0');
		}
		$allnum = $this->totalrows();
		self::inidate($allnum,$page,$pagesize,count($data));

		$this->smarty->assign('stations',$stations);

		$this->smarty->assign('stationid',$stationid);
		$this->smarty->assign('data',$data);
		$this->forward = 'reg';
	}
	//导航点击统计
	public function navhitAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$stationid = reqnum('stationid',1);

		$model = new Psys_StationModel();
		$stations = $model->station();
		$data = $model->navhitCount($page,$pagesize,$stationid);

		foreach ($data as &$v)
		{	
			$sum1 = 0;
			$v += array('date'=>'0','inquiries'=>'0','station'=>'0','luggage'=>'0','movie'=>'0','music'=>'0','app'=>'0','game'=>'0');
			foreach($v as $key=>$value)
			{
				if($key != 'date'){
					$sum1 += $value;
				}
			}
			$v['sum'] = $sum1;	
		}

		$allnum = $this->totalrows();
		self::inidate($allnum,$page,$pagesize,count($data));

		$this->smarty->assign('stations',$stations);
		$this->smarty->assign('stationid',$stationid);
		$this->smarty->assign('data',$data);
		$this->forward = 'navhit';
	}
	//banner点击统计
	public function pagehitAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$stationid = reqnum('stationid',1);
		
		$model = new Psys_StationModel();
		$stations = $model->station();
		$data = $model->pagehitCount($page,$pagesize,$stationid);
		foreach ($data as &$v)
		{
			$v += array('date'=>'0','index'=>'0','music'=>'0','movie'=>'0','app'=>'0','game'=>'0');
		}
		$allnum = $this->totalrows();
		self::inidate($allnum,$page,$pagesize,count($data));

		$this->smarty->assign('stations',$stations);
		$this->smarty->assign('stationid',$stationid);
		$this->smarty->assign('data',$data);
		$this->forward = 'pagehit';
	}
	//首页-banner详情
	public function BannerIndexAction()
	{
		$stationid = reqnum('stationid', 1);
		$date = reqstr('date','');	
		//if(!$date){
		//	return array('code'=>0);
		//}
		$nt = new Psys_StationModel();
		$list = $nt -> BannerIndex($stationid,$date);
		return array('code'=>1,'allrow'=>$list);
	}
	//影视统计
	public function movieAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$stationid = reqnum('stationid',1);
	
		$model = new Psys_StationModel();
		$stations = $model->station();
		$data = $model->movieCount($page,$pagesize,$stationid);		
		foreach ($data['allrow'] as &$v)
		{
			$v += array('date'=>'0','columns'=>'0','play'=>'0','pause'=>'0','view'=>'0','buffer'=>'0','playbar'=>'0','playall'=>'0');
		}
		$allnum = $this->totalrows();
		self::inidate($allnum,$page,$pagesize,count($data['allrow']));

		$this->smarty->assign('stations',$stations);
		$this->smarty->assign('stationid',$stationid);
		$this->smarty->assign('data',$data['allrow']);
		$this->forward = 'movie';
	}
	//影视播放详情
	public function movieDetailAction()
	{
		$date = reqstr('date','');
		$stationid = reqnum('stationid', 1);
		if(!$date){
			return array('code'=>0);
		}
		$nt = new Psys_StationModel();
		$list = $nt -> moviePlay($date,$stationid);

		return array('code'=>1,'allrow'=>$list);	
	}
	//音乐统计
	public function musicAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$stationid = reqnum('stationid',1);
	
		$model = new Psys_StationModel();
		$stations = $model->station();
		$data = $model->musicCount($page,$pagesize,$stationid);
		foreach ($data as &$v)
		{
			$v += array('date'=>'0','addalbum'=>'0','play'=>'0','pause'=>'0','like'=>'0','addsong'=>'0','addsinger'=>'0','singer'=>'0');
		}
		$allnum = $this->totalrows();
		self::inidate($allnum,$page,$pagesize,count($data));

		$this->smarty->assign('stations',$stations);
		$this->smarty->assign('stationid',$stationid);
		$this->smarty->assign('data',$data);
		$this->forward = 'music';
	}
	//音乐播放详情
	public function musicDetailAction()
	{
		$date = reqstr('date','');
		$stationid = reqnum('stationid', 1);
		if(!$date){
			return array('code'=>0);
		}
		$nt = new Psys_StationModel();
		$list = $nt -> musicPlay($date,$stationid);

		return array('code'=>1,'allrow'=>$list);	
	}
	//App统计
	public function appAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$stationid = reqnum('stationid',1);
	
		$model = new Psys_StationModel();
		$stations = $model->station();
		$data = $model->appCount($page,$pagesize,$stationid);
		foreach ($data as &$v)
		{
			$v += array('date'=>'0','click'=>'0','down'=>'0','downfinish'=>'0','update'=>'0');
		}
		$allnum = $this->totalrows();
		self::inidate($allnum,$page,$pagesize,count($data));
		
		$this->smarty->assign('stations',$stations);
		$this->smarty->assign('stationid',$stationid);
		$this->smarty->assign('data',$data);
		$this->forward = 'app';
	}
	//下载详情
	public function downapkAction()
	{
		$date = reqstr('date','');
		$stationid = reqnum('stationid', 1);
		if(!$date){
			return array('code'=>0);
		}
		$nt = new Psys_StationModel();
		$list = $nt -> downapkCount($date,$stationid);

		return array('code'=>1,'allrow'=>$list);	
	}
	
	//订票统计
	public function orderAction()
	{
		$condition = array(
				'order_time' => reqstr('order_time',''),
				'order_number' => reqstr('order_number',''),
				'order_state' => reqstr('order_state',''),
				//'train_number' => reqstr('train_number',''),
				//'seat_name' => reqstr('seat_name',''),
		);
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
	
		$model = new Psys_StationModel();
		$data = $model->orderCount($page,$pagesize,$condition);
		self::inidate($data['rows'][0]['rows'],$page,$pagesize,count($data));
		$this->smarty->assign('order_time',$condition['order_time']);
		$this->smarty->assign('order_number',$condition['order_number']);
		$this->smarty->assign('order_state',$condition['order_state']);
		//$this->smarty->assign('train_number',$condition['train_number']);
		//$this->smarty->assign('seat_name',$condition['seat_name']);
		$this->smarty->assign('data',$data);
		$this->forward = 'order';
	}
	
	//web-游戏/应用-统计
	public function webAppAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",15);
		$stationid = reqnum('stationid',1);
		$select = reqnum('select',1);
	
		$model = new Psys_StationModel();
		$stations = $model->station();
		$data = $model->webAppCount($stationid,$select,$page,$pagesize);

		self::inidate($data['count'][0]['count'],$page,$pagesize,count($data));
		
		$this->smarty->assign('stations',$stations);
		$this->smarty->assign('stationid',$stationid);
		$this->smarty->assign('select',$select);
		$this->smarty->assign('data',$data['allrows']);
		$this->forward = 'webApp';
	}
	//web-游戏/应用-统计详情
	public function webAppDetailAction()
	{
		$date = str_replace('-', '_',reqstr('date',''));
		$stationid = reqnum('stationid', 1);
		$select = reqnum('select',1);
		if(!$date){
			return array('code'=>0);
		}
		$nt = new Psys_StationModel();
		$list = $nt -> webAppDetail($date,$stationid,$select);
		$ret = array('code'=>1,'allrow'=>$list);
		//var_dump($list);
		//echo json_encode($list['app']);
		return $ret;
	}

	//车站wifi
	public function aclogAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$stationid = reqnum('stationid', 1);
		$nt = new Psys_StationModel();
		$stations = $nt->station();
		$list = $nt -> AcList($page, $pagesize,$stationid);

// 		self::inidate($list['allnum'],$page,$pagesize,count($list['allrow']));
		
		$this->smarty->assign('stations',$stations);
		$this->smarty->assign('list',$list['allrow']);
		$this->smarty->assign('stationid',$stationid);
		$this->smarty->assign('psys_base_url',PSYS_BASE_URL);
		$this->forward = "aclog";
	}
	//独立访客
	public function uvisitorAction()
	{
		$date = reqstr('date','');
		$obj = new Psys_StationModel();
		$data = $obj->uVisitor($date);
		$this->smarty->assign('totallist',$data);
		$this->forward = 'uv';
	}
	//榜单点击
	public function albumhitAction()
	{
		$date = reqstr('date','');
		$obj = new Psys_StationModel();
		$data = $obj->albumHit($date);
		return $data;
	}
	
	//伙伴每日数据
	public function everydayAction()
	{
		
		$date = reqstr('date',date('Y_m_d',strtotime('-1 day')));
		$stationid = reqnum('stationid',1);
		$obj = new Psys_StationModel();
		$stations = $obj->station();
		$picDir = $obj->picDir($stationid);
		$data = $obj->everyday($date,$stationid);
		$link = $data['link'] ? $data['link'] : '0';
		$list = $data['detail'];
		foreach ($list as &$v)
		{
			$v += array('register'=>'0','index'=>'0','visit'=>'0','alert'=>'0','down'=>'0','first'=>'0','undown'=>'0','convert'=>'0');
		}
		$this->smarty->assign('stations',$stations);
		$this->smarty->assign('picDir',$picDir);
		$this->smarty->assign('date',$date);
		$this->smarty->assign('stationid',$stationid);
		$this->smarty->assign('link',$link);
		$this->smarty->assign('list',$list);
		$this->forward = 'stateTable';
	}
	
	
	public function webcountAction()
	{
		$date = reqstr('date',date('Y_m_d',strtotime('-1 day')));
		$stationid = reqnum('stationid', 1);
		$obj = new Psys_StationModel();
		$stations = $obj->station();
		$data = $obj->webCount($date,$stationid);
		
		$this->smarty->assign('stations',$stations);
		$this->smarty->assign('date',$date);
		$this->smarty->assign('stationid',$stationid);
		$this->smarty->assign('totallist',$data);
		$this->forward = 'list';
	}
	//新流程
	public function webCountNewAction()
	{
		$page = reqnum("page",'1');
		$pagesize = reqnum("pagesize",'15');
		$stationid = reqnum('stationid', 1);
		
		$model = new Psys_StationModel();
		$stations = $model->station();
		$data = $model->webCountNew($stationid,$page,$pagesize);
		
		$allnum = $this->newtotalrows();
		self::inidate($allnum,$page,$pagesize,count($data));

		$this->smarty->assign('stations',$stations);
		$this->smarty->assign('stationid',$stationid);
		$this->smarty->assign('totallist',$data);
		$this->forward = 'webCount';
	}

	/**
	 * 流程导出功能
	 */
	public function webcountexportAction(){	
			
		$stationid = reqnum('stationid', 1);

		$model = new Psys_StationModel();
		$stationname = $model -> GetOne(array('id'=>$stationid),'stationname','rha_station');
		$data = $model->webcountexport($stationid,$page,$pagesize);

		$databody = array();
		foreach($data as $k=>$v){
			$databody[$k][] = $v['date'];
			$databody[$k][] = $v['link'];
			$databody[$k][] = $v['ad'];
			$databody[$k][] = $v['reg'];
			$databody[$k][] = $v['login'];
			$databody[$k][] = $v['sindex'];
		}
		
		$header = array('日期','WIFI连接','广告页1/uv','注册页/uv','注册成功数/uv','首页/uv');
		require COMMON_PATH.'XExportExcel.php';
        $excel = new Excel();
        $excel->addHeader($header);
        $excel->addBody($databody);
        $excel->downLoad($stationname['stationname']);
		
	}
	//新流程详情
	public function webCountNDAction()
	{	
		$date = reqstr('date','');
		$date = date('Y_m_d',strtotime($date));
		$stationid = reqnum('stationid');
		if(!$date){
			return array('code'=>0);
		}
		$model = new Psys_StationModel();

		$list = $model -> GetOne(array('date'=>$date,'stationid'=>$stationid,'counttype'=>2),'detail','rha_webcount');

		$dt = str_replace('else', 'Else', $list['detail']);

		$dt = str_replace(' ', '', $dt);
		
		$dt = str_ireplace(
					array(
							'ad_uv',
			
							'ad_dm',
							'ad_qd',
							'ad_aqy',
							'ad_pkq',
							'ad_dtcq',
			
							'reg_uv',
			
							'verify_success',
							'verify_error',
			
							'login_success',
							'login_error',
			
							'welcome_uv',
							
							'welcome_dm',
							'welcome_qd',
							'welcome_aqy',
							'welcome_pkq',
			
							'sindex_uv',
			
							'station_pv',
							'game_pv',
							'movie_pv',
							'music_pv',
			
							'game_alert',
							'movie_alert',
							'music_alert',
							'sindex_alert',
			
							'game_down',
							'movie_down',
							'music_down',
							'sindex_down',
			
							'dm_alert',
							'qd_alert',
							'aqy_alert',
							'pkq_alert',
			
							'dm_down',
							'qd_down',
							'aqy_down',
							'pkq_down',
			
							'webindexindex_time',
							'webindexregister_time',
							'webindexwelcome_time',
							'webindexsindex_time',
			
							'webstation_time',
							'webgameindex_time',
							'webmovieindex_time',
							'webmusicindex_time'
					),
					array(
							'广告页1/uv',
			
							'广告1-多米/pv',
							'广告1-起点/pv',
							'广告1-爱奇艺/pv',
							'广告1-皮卡丘/pv',
							'广告1-刀塔传奇/pv',
			
							'注册页/uv',
			
							'验证码发送成功/pv',
							'验证码发送失败/pv',
							
							'登录成功(新注册)/uv',
							'验证码错误/uv',
			
							'广告页2/uv',
							
							'广告2-多米/pv',
							'广告2-起点/pv',
							'广告2-爱奇艺/pv',
							'广告2-皮卡丘/pv',
			
							'sindex页/uv',
			
							'车站页/pv',
							'游戏页/pv',
							'电影页/pv',
							'音乐页/pv',
			
							'train下载弹窗-游戏页/pv',
							'train下载弹窗-电影页/pv',
							'train下载弹窗-音乐页/pv',
							'train下载弹窗-sindex页/pv',
			
							'train下载-游戏页uv',
							'train下载-电影页uv',
							'train下载-音乐页uv',
							'train下载-sindex页/uv',
			
							'第三方App下载弹窗-多米/pv',
							'第三方App下载弹窗-起点/pv',
							'第三方App下载弹窗-爱奇/pv',
							'第三方App下载弹窗-皮卡丘/pv',
			
							'第三方App下载-多米/pv',
							'第三方App下载-起点/pv',
							'第三方App下载-爱奇艺/pv',
							'第三方App下载-皮卡丘/pv',
			
							'广告页1时长/秒',
							'注册页时长/秒',
							'广告页2时长/秒',
							'sindex页时长',
			
							'车站页时长/秒',
							'游戏页时长/秒',
							'电影页时长/秒',
							'音乐页时长/秒'
					),
					$dt
			);
		$data = json_decode($dt,true);
		
		$arr = array('android'=>0,'ios'=>0,'Else'=>0);
		foreach ($data as $m=>&$n)
		{
			$n += $arr;
		}
		return array('code'=>1,'allrow'=>$data);
	}
	
	//流程统计汇总
	public function webCountAllAction()
	{
		$page = reqnum("page",'1');
		$pagesize = reqnum("pagesize",'15');
		
		$model = new Psys_StationModel();
		$stations = $model->station();
		$data = $model->webCountAll($page,$pagesize);
		
		$allnum = $this->newtotalrows();
		self::inidate($allnum,$page,$pagesize,count($data));
		
		$this->smarty->assign('totallist',$data);
	
		$this->forward = 'webCountAll';
	}
	
	public function apkAction()
	{
		$date = reqstr('date','');
		$obj = new Psys_StationModel();
		$data = $obj->downCount($date);
	
		$this->smarty->assign('totallist',$data);
		$this->forward = 'apk';
	}
	
	//每日分时段
	function actimeAction()
	{
		$date = reqstr('date','');
		$stationid = reqnum('stationid', 1);
		if (!$date) {
			return array('code'=>0);
		}
		$nt = new Psys_StationModel();
		$list = $nt -> AcTime($date,$stationid);
		
		return array('code'=>1,'allrow'=>$list);
		
	}
	
	function aplogAction()
	{
		$date = reqstr('date','');
		$stationid = reqnum('stationid', 1);
		if (!$date) {
			return array('code'=>0);
		}
		$nt = new Psys_StationModel();
		$list = $nt -> ApLog($date,$stationid);
	
		return array('code'=>1,'allrow'=>$list);
	
	}
	function apdetailAction()
	{
		$date = reqstr('date','');
		$stationid = reqnum('stationid', 1);
		if (!$date) {
			return array('code'=>0);
		}
		$nt = new Psys_StationModel();
		$list = $nt -> ApDetail($date,$stationid);
	
		return array('code'=>1,'allrow'=>$list);
	
	}
	/**
	 * 分页数据显示
	 * @param num $allcount 总条数
	 * @param num $page
	 * @param num $pagesize
	 * @param num $cursize  当前页的数据条数
	 */
	private function inidate($allcount,$page,$pagesize,$cursize){
		$this->smarty->assign('allcount',$allcount);   //总数
		$allpage = ceil($allcount/$pagesize);
		$this->smarty->assign('allpage',$allpage);  //总页数
		$pagesize = ($pagesize%2)?$pagesize:$pagesize+1; //页码取偶数 = 步长
		$pageoffset =($pagesize-1)/2; //当前页 左右偏移
		$this->smarty->assign('cur_page',$page); //当前页
		if($allcount>$pagesize)
		{
			//如果当前页小于等于左偏移
			if($page<=$pageoffset)
			{
				$startNum=1;
				$endNum = $pagesize;
			}
			else
			{//如果当前页大于左偏移
				//如果当前页码右偏移超出最大分页数
				if($page+$pageoffset>=$allcount+1)
				{
					$startNum = $allcount-$pagesize+1;
				}
				else
				{
					//左右偏移都存在时的计算
					$startNum = $page-$pageoffset;
					$endNum = $page+$pageoffset;
				}
			}
		}
		else
		{
			$startNum=1;
			$endNum = $allcount;
		}
	
		$this->smarty->assign('startNum',$startNum);  //当前从几开始
		$this->smarty->assign('endNum',$endNum);  //当前到几结束
		$this->smarty->assign('sli',(($pagesize*($page-1)+1)));  //当前页的起始第多少条
		$this->smarty->assign('eli',($pagesize*($page-1)+$cursize));  //当前页最后一条是第多少条
	}

	/**
	 * 监控日志
	 */
	public function checklogAction(){
		$station = reqnum('station',1);
		$state = reqnum('state',0);
		$cdate = reqstr('cdate');
		$nt = new Psys_StationModel();
		$stations = $nt->station();
		$this->smarty->assign('stations',$stations); 
		$this->smarty->assign('station',$station); 
		$this->smarty->assign('state',$state);
		$this->smarty->assign('cdate',$cdate);
		$this->forward = 'checklog';
	}

	/**
	 * 监控日志详情
	 */
	public function checkloginfoAction(){
		$page = reqnum('page',1);
		$pagesize = reqnum('rp',5);
		$m = new Psys_StationModel();
		$data1 = $m->CheckLogList();
		$error = $data1['error'];
		$success = $data1['success'];

		//过滤
		foreach($error as &$v){
			foreach($success as $k1 => &$v1){
				
				if ($v === $v1) {
					unset($success[$k1]);
					//$v['state'] = 'error';
				}
			}
		}

		foreach($error as &$v){
			$v['state'] = '<img src="/style/default/images/error.png"/>';
		}

		foreach($success as &$v){
			$v['state'] = '<img src="/style/default/images/success.png"/>';
		}
		
		$merge = array_merge($error,$success);
		
		array_multisort($merge,SORT_DESC);
		$offset = ($page-1)*$pagesize;
		$data['total'] = count($merge);
		$result = array_slice($merge, $offset,$pagesize);
		$data['page'] = $page;
		$data['rows'] = $result;

		//拼接返回的数据格式
		foreach($data['rows'] as &$v){
			$v['cell'] = array_values($v);
			unset($v['station'],$v['scdate'],$v['state']);
		}
		return $data;
	}


	public function getchecklogerrorAction(){
		$states = array('正常','未上传','未解压','未解析','不存在');
		$cdate = reqstr('cdate');
		$station = reqstr('station');
		if (!$cdate || !$station) {
			return array('code'=>0,'msg'=>'Parameter is empty');
		}
		$page = reqnum('page',1);
		$pagesize = reqnum('rp',100);
		$where = array('cdate'=>$cdate,'station'=>$station,'state_!='=>0);
		$m = new Psys_StationModel();
		$data = $m->GetList($where,'',$page,$pagesize,'*','rha_checklog');

		foreach($data['allrow'] as &$v){
			if ($v['logtype'] !=2) {
				$filenem_o = explode('_',$v['filename']);
				$v['web'] = $filenem_o[0];
				$v['filename'] = substr($v['filename'],5);
			}else{
				$v['web'] = 'AC';
			}
			$v['state'] = $states[$v['state']];
		}

		$title = $station.$cdate.'日错误日志详情';
		$this->smarty->assign('data',$data['allrow']);
		$this->smarty->assign('title',$title);
		$this->forward = 'logerrorinfo';
	}

	/**
	 * 数据统计
	 * @return json
	 */
	public function paitingdataAction(){
		set_time_limit(120);
		$stations = array(1=>'青岛南',2=>'青岛北',3=>'济南');
		$stationid = reqnum('station',1);
		$calsstype = reqnum('classtype',0);
		$sdate = reqstr('sdate',date('Y-m-d',strtotime('-1 day')));
		$edate = reqstr('edate');
		$nt = new Psys_StationModel();
		switch ($calsstype) {
			case 0:
				//总图

				$edate = reqstr('edate',date('Y-m-d',strtotime('-1 day')));
				$sdate = reqstr('sdate',date('Y-m-d',strtotime($edate)-3600*24*6));
				
				$data['connect'] = $this->connect($stationid,$sdate,$edate);
				$data['reg'] = $this->regnum($stationid,$sdate,$edate);
				$data['down'] = $this->down($stationid,$sdate,$edate);
				$data['rate'] = $this->rate($stationid,$sdate,$edate);
				$data['rose'] = $this->rose($stationid,$sdate,$edate);
				$data['prose'] = $this->prose($stationid,$sdate,$edate);
				$data['prevconnect'] = $this->PrevWeekTotal($stationid,$sdate,$edate);
				$data['curconnect'] = $this->CurWeekTotal($stationid,$sdate,$edate);
				$data['prevweekrose'] = $this->PrevWeekRose($stationid,$sdate,$edate);
				$data['curweekrose'] = $this->CurWeekRose($stationid,$sdate,$edate);
				return $data;
				break;
			
			case 1: //用户连接走势
				return $this->connect($stationid,$sdate,$edate);
				break;

			case 2:
				//用户注册走势
				return $this->regnum($stationid,$sdate,$edate);
				break;

			case 3:
				//用户下载走势
				return $this->down($stationid,$sdate,$edate);
				break;

			case 4:
				//转化率走势
				return $this->rate($stationid,$sdate,$edate);
				break;

			case 5:
				//每日Ios流程步骤流失率
				return $this->rose($stationid,$sdate,$edate);
				break;

			case 6:
				//每日流程数据概况
				return $this->prose($stationid,$sdate,$edate);
				break;

			case 7:
				//上周用户连接数，注册数、下载数
				return $this->PrevWeekTotal($stationid,$sdate,$edate);
				break;

			case 8:
				//本周用户连接数，注册数、下载数
				return $this->CurWeekTotal($stationid,$sdate,$edate);
				break;
		}

	}

	public function paitingAction(){
		$nt = new Psys_StationModel();
		$stations = $nt->station();
		$this->smarty->assign('stations',$stations); 
		$this->forward = 'paiting';
	}

	//用户连接统计
	private function connect($stationid,$sdate,$edate){
		$nt = new Psys_StationModel();
		$data = $nt -> UserConnect($stationid,$sdate,$edate);

		$nts = new Psys_ResourceModel();
		$datas[0]['name'] ='连接走势';
		foreach($data as $v){
			$x_date = $x_cat[] = date('m/d',strtotime($v['date']));

			$eventrecordinfo = $nts -> EventRecordInfo(str_replace('-','_',$v['date']),0,$stationid);
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

			//$y_data[] = (int)$v['num'];
		}
		$res['y_data'] = $datas;
		$res['x_cat'] = $x_cat;
		return $res;
	}

	//用户注册统计
	private function regnum($stationid,$sdate,$edate){
		$nt = new Psys_StationModel();
		$data = $nt -> UserReg($stationid,$sdate,$edate);

		$nts = new Psys_ResourceModel();
		$datas[0]['name'] ='android';
		foreach($data['android'] as $v){

			$x_date = $x_cat[] = substr(str_replace('_','/',$v['date']),5);

			$eventrecordinfo = $nts -> EventRecordInfo($v['date'],0,$stationid);
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
				$datas[0]['data'][] = (int)$v['reg'];
			}else{
				$datas[0]['data'][] = array('y'=>(int)$v['reg'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}
			
		}
		
		$datas[1]['name'] ='ios';
		foreach($data['ios'] as $v1){

			$x_date = substr(str_replace('_','/',$v1['date']),5);

			$eventrecordinfo = $nts -> EventRecordInfo($v1['date'],0,$stationid);
			//返回特殊点的提示信息
			if (!empty($eventrecordinfo)) {
				
				$title = $eventrecordinfo['title'];
				$descript = $eventrecordinfo['descript'];
			}else{			
				$title = '';
				$descript = '';
			}
			$datas[1]['infos']['date'.$x_date]['title'] = $title;
			$datas[1]['infos']['date'.$x_date]['descript'] = $descript;

			if (empty($eventrecordinfo)) {
				$datas[1]['data'][] = (int)$v1['reg'];
			}else{
				$datas[1]['data'][] = array('y'=>(int)$v1['reg'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}

			//$y_ios[] = (int)$v1['reg'];
		}

		$datas[2]['name'] ='total';
		foreach($data['total'] as $v2){
			$x_date = substr(str_replace('_','/',$v2['date']),5);

			$eventrecordinfo = $nts -> EventRecordInfo($v2['date'],0,$stationid);
			//返回特殊点的提示信息
			if (!empty($eventrecordinfo)) {
				
				$title = $eventrecordinfo['title'];
				$descript = $eventrecordinfo['descript'];
			}else{			
				$title = '';
				$descript = '';
			}
			$datas[2]['infos']['date'.$x_date]['title'] = $title;
			$datas[2]['infos']['date'.$x_date]['descript'] = $descript;

			if (empty($eventrecordinfo)) {
				$datas[2]['data'][] = (int)$v2['reg'];
			}else{
				$datas[2]['data'][] = array('y'=>(int)$v2['reg'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}

			//$y_total[] = (int)$v2['reg'];
		}

		$res['y_data'] = $datas;
		$res['x_cat'] = $x_cat;

		return $res;
	}

	//用户下载统计
	private function down($stationid,$sdate,$edate){
		$nt = new Psys_StationModel();
		$data = $nt -> UserDown($stationid,$sdate,$edate);

		$nts = new Psys_ResourceModel();
		$datas[0]['name'] ='android';
		foreach($data['android'] as $v){
			$x_date = $x_cat[] = substr(str_replace('_','/',$v['date']),5);

			$eventrecordinfo = $nts -> EventRecordInfo($v['date'],0,$stationid);
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
				$datas[0]['data'][] = (int)$v['down'];
			}else{
				$datas[0]['data'][] = array('y'=>(int)$v['down'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}
		}
		
		$datas[1]['name'] ='ios';
		foreach($data['ios'] as $v1){
			$x_date = substr(str_replace('_','/',$v1['date']),5);

			$eventrecordinfo = $nts -> EventRecordInfo($v1['date'],0,$stationid);
			//返回特殊点的提示信息
			if (!empty($eventrecordinfo)) {
				
				$title = $eventrecordinfo['title'];
				$descript = $eventrecordinfo['descript'];
			}else{			
				$title = '';
				$descript = '';
			}
			$datas[1]['infos']['date'.$x_date]['title'] = $title;
			$datas[1]['infos']['date'.$x_date]['descript'] = $descript;

			if (empty($eventrecordinfo)) {
				$datas[1]['data'][] = (int)$v1['down'];
			}else{
				$datas[1]['data'][] = array('y'=>(int)$v1['down'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}
		}

		$datas[2]['name'] ='total';
		foreach($data['total'] as $v2){
			$x_date = substr(str_replace('_','/',$v2['date']),5);

			$eventrecordinfo = $nts -> EventRecordInfo($v2['date'],0,$stationid);
			//返回特殊点的提示信息
			if (!empty($eventrecordinfo)) {
				
				$title = $eventrecordinfo['title'];
				$descript = $eventrecordinfo['descript'];
			}else{			
				$title = '';
				$descript = '';
			}
			$datas[2]['infos']['date'.$x_date]['title'] = $title;
			$datas[2]['infos']['date'.$x_date]['descript'] = $descript;

			if (empty($eventrecordinfo)) {
				$datas[2]['data'][] = (int)$v2['down'];
			}else{
				$datas[2]['data'][] = array('y'=>(int)$v2['down'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}

		}

		$res['y_data'] = $datas;
		$res['x_cat'] = $x_cat;

		return $res;
		
	}

	//转化率统计
	private function rate($stationid,$sdate,$edate){
		$nt = new Psys_StationModel();
		$data = $nt -> UserRate($stationid,$sdate,$edate);

		$nts = new Psys_ResourceModel();
		$datas[0]['name'] ='android';
		foreach($data['android'] as $v){

			$x_date = $x_cat[] = substr(str_replace('_','/',$v['date']),5);
			$eventrecordinfo = $nts -> EventRecordInfo($v['date'],0,$stationid);
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
				$datas[0]['data'][] = (int)$v['rate'];
			}else{
				$datas[0]['data'][] = array('y'=>(int)$v['rate'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}
		}
		
		
		$datas[1]['name'] ='ios';
		foreach($data['ios'] as $v1){
			$x_date = substr(str_replace('_','/',$v1['date']),5);

			$eventrecordinfo = $nts -> EventRecordInfo($v1['date'],0,$stationid);
			//返回特殊点的提示信息
			if (!empty($eventrecordinfo)) {
				
				$title = $eventrecordinfo['title'];
				$descript = $eventrecordinfo['descript'];
			}else{			
				$title = '';
				$descript = '';
			}
			$datas[1]['infos']['date'.$x_date]['title'] = $title;
			$datas[1]['infos']['date'.$x_date]['descript'] = $descript;

			if (empty($eventrecordinfo)) {
				$datas[1]['data'][] = (int)$v1['rate'];
			}else{
				$datas[1]['data'][] = array('y'=>(int)$v1['rate'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}
		}
		$res['y_data'] = $datas;
		$res['x_cat'] = $x_cat;
		return $res;
	}

	//每日流失率统计
	private function rose($stationid,$sdate,$edate){
		$nt = new Psys_StationModel();
		$data = $nt -> UserRose($stationid,$sdate,$edate);

		//添加计算连接数到广告1的流失率
		$data_ = $nt -> DailyProcess($stationid,$sdate,$edate);
		//$x_cat = array('用户连接数','广告页1','注册页','注册数','广告页2','首页','下载数');
		$con = array();
		foreach($data_ as $v){
			$con[] = (int)$v;
		}
		//连接数到广告1的流失率
		$con_ad1 = round(($con[0]-$con[1])/$con[0],2)*100;

		$x_cat = array('连接数-广告1','广告1-注册页','注册页-注册数','注册数-广告2','广告2-首页','首页-下载数');
		$y_android = array();
		$y_ios = array();
		$y_total = array();
		foreach($data['android'] as $v){
			$y_android[] = (int)$v;
		}
		
		foreach($data['ios'] as $v1){
			$y_ios[] = (int)$v1;
		}

		foreach($data['total'] as $v2){
			$y_total[] = (int)$v2;
		}
		

		array_unshift($y_android,0);
		array_unshift($y_ios,0);
		array_unshift($y_total,$con_ad1);
		
		$res['y_data'] = array(
			array('name'=>'andriod','data'=>$y_android),
			array('name'=>'ios','data'=>$y_ios),
			array('name'=>'总体','data'=>$y_total)
		);
		$res['x_cat'] = $x_cat;
		$res['date'] = $edate;
		return $res;
	}

	//每日流程统计
	private function prose($stationid,$sdate,$edate){
		/*$nt = new Psys_StationModel();
		$data = $nt -> DailyProcess($stationid,$sdate,$edate);*/
		$data = $this->prose7day($stationid,$edate);
		$x_cat = array('用户连接数','广告页1','注册页','注册数','广告页2','首页','下载数');
		$y_data = array();

		$i = 0;
		foreach($data as $key => $val){
			foreach($val as $v){
				$y_data[$key][] = (int)$v;

			}
			
			$res['y_data'][$i]['name'] = $key;
			$res['y_data'][$i]['data'] = $y_data[$key];
			if($i>0){
				$res['y_data'][$i]['visible'] = false;
			}
			$i++;
		}

		$datea = date('m/d',strtotime($res['y_data'][6]['name'])).'-'.date('m/d',strtotime($res['y_data'][0]['name']));
		$res['x_cat'] = $x_cat;
		$res['date'] = $datea;
		return $res;
	}

	//统计近7天的流程数据
	protected function prose7day($stationid,$edate){
		$nt = new Psys_StationModel();
		if (!$edate) {
			$edate = date('Y-m-d',strtotime('-1 day'));
		}
		//根据当前日期获取最近7天的时间
		
		for ($i=0; $i < 7; $i++) { 
			$day['day'.$i] = date('Y-m-d',strtotime($edate)-3600*24*$i);
		}
		foreach($day as $k => $v){
			$data[$v] = $nt -> DailyProcess($stationid,'',$v);
		}

		return $data;
	}

	//上周用户连接数，注册数、下载数情况
	private function PrevWeekTotal($stationid,$sdate,$edate){
		$nt = new Psys_StationModel();
		$data = $nt -> PrevWeekTotal($stationid,$sdate,$edate);
		
		$nts = new Psys_ResourceModel();
		$datas[0]['name'] ='上周用户连接数';
		foreach($data['connect'] as $k => $v){
			$x_date = $x_cat[] = substr(str_replace('-','/',$k),5);
			$eventrecordinfo = $nts -> EventRecordInfo(str_replace('-','_',$k),0,$stationid);
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
				$datas[0]['data'][] = (int)$v;
			}else{
				$datas[0]['data'][] = array('y'=>(int)$v,
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}	
		}

		$datas[1]['name'] ='上周用户注册数';
		foreach($data['reg'] as $k1 => $v1){

			$x_date = substr(str_replace('_','/',$k1),5);
			$eventrecordinfo = $nts -> EventRecordInfo(str_replace('-','_',$k1),0,$stationid);
			//返回特殊点的提示信息
			if (!empty($eventrecordinfo)) {
				
				$title = $eventrecordinfo['title'];
				$descript = $eventrecordinfo['descript'];
			}else{			
				$title = '';
				$descript = '';
			}
			$datas[1]['infos']['date'.$x_date]['title'] = $title;
			$datas[1]['infos']['date'.$x_date]['descript'] = $descript;

			if (empty($eventrecordinfo)) {
				$datas[1]['data'][] = (int)$v1;
			}else{
				$datas[1]['data'][] = array('y'=>(int)$v1,
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}	
		}

		$datas[2]['name'] ='上周用户下载数';
		foreach($data['down'] as $k2 => $v2){
			$x_date = substr(str_replace('_','/',$k2),5);
			$eventrecordinfo = $nts -> EventRecordInfo(str_replace('-','_',$k2),0,$stationid);
			//返回特殊点的提示信息
			if (!empty($eventrecordinfo)) {
				
				$title = $eventrecordinfo['title'];
				$descript = $eventrecordinfo['descript'];
			}else{			
				$title = '';
				$descript = '';
			}
			$datas[2]['infos']['date'.$x_date]['title'] = $title;
			$datas[2]['infos']['date'.$x_date]['descript'] = $descript;

			if (empty($eventrecordinfo)) {
				$datas[2]['data'][] = (int)$v2;
			}else{
				$datas[2]['data'][] = array('y'=>(int)$v2,
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}
		}

		$res['y_data'] = $datas;
		$res['x_cat'] = $x_cat;
		return $res;
	}

	//本周用户连接数，注册数、下载数情况
	private function CurWeekTotal($stationid,$sdate,$edate){
		$nt = new Psys_StationModel();
		$data = $nt -> CurWeekTotal($stationid,$sdate,$edate);
		$nts = new Psys_ResourceModel();
		$datas[0]['name'] ='本周用户连接数';
		foreach($data['connect'] as $k => $v){
			$x_date = $x_cat[] = substr(str_replace('-','/',$k),5);
			$eventrecordinfo = $nts -> EventRecordInfo(str_replace('-','_',$k),0,$stationid);
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
				$datas[0]['data'][] = (int)$v;
			}else{
				$datas[0]['data'][] = array('y'=>(int)$v,
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}	
		}

		$datas[1]['name'] ='本周用户注册数';
		foreach($data['reg'] as $k1 => $v1){

			$x_date = substr(str_replace('_','/',$k1),5);
			$eventrecordinfo = $nts -> EventRecordInfo(str_replace('-','_',$k1),0,$stationid);
			//返回特殊点的提示信息
			if (!empty($eventrecordinfo)) {
				
				$title = $eventrecordinfo['title'];
				$descript = $eventrecordinfo['descript'];
			}else{			
				$title = '';
				$descript = '';
			}
			$datas[1]['infos']['date'.$x_date]['title'] = $title;
			$datas[1]['infos']['date'.$x_date]['descript'] = $descript;

			if (empty($eventrecordinfo)) {
				$datas[1]['data'][] = (int)$v1;
			}else{
				$datas[1]['data'][] = array('y'=>(int)$v1,
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}	
		}

		$datas[2]['name'] ='本周用户下载数';
		foreach($data['down'] as $k2 => $v2){
			$x_date = substr(str_replace('_','/',$k2),5);
			$eventrecordinfo = $nts -> EventRecordInfo(str_replace('-','_',$k2),0,$stationid);
			//返回特殊点的提示信息
			if (!empty($eventrecordinfo)) {
				
				$title = $eventrecordinfo['title'];
				$descript = $eventrecordinfo['descript'];
			}else{			
				$title = '';
				$descript = '';
			}
			$datas[2]['infos']['date'.$x_date]['title'] = $title;
			$datas[2]['infos']['date'.$x_date]['descript'] = $descript;

			if (empty($eventrecordinfo)) {
				$datas[2]['data'][] = (int)$v2;
			}else{
				$datas[2]['data'][] = array('y'=>(int)$v2,
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}
		}

		$res['y_data'] = $datas;
		$res['x_cat'] = $x_cat;
		return $res;
	}

	//上周注册、下载流失率
	private function PrevWeekRose($stationid,$sdate,$edate){
		$nt = new Psys_StationModel();
		$data = $nt -> PrevWeekRose($stationid,$sdate,$edate);

		$nts = new Psys_ResourceModel();

		$datas[0]['name'] ='上周注册流失率';
		foreach($data['regrose'] as $k => $v){
			$x_date = $x_cat[] = substr(str_replace('_','/',$k),5);
			$eventrecordinfo = $nts -> EventRecordInfo(str_replace('-','_',$k),0,$stationid);
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
				$datas[0]['data'][] = (int)$v;
			}else{
				$datas[0]['data'][] = array('y'=>(int)$v,
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}	
		}

		$datas[1]['name'] ='上周下载流失率';
		foreach($data['downrose'] as $k1 => $v1){
			$x_date = substr(str_replace('_','/',$k1),5);
			$eventrecordinfo = $nts -> EventRecordInfo(str_replace('-','_',$k1),0,$stationid);
			//返回特殊点的提示信息
			if (!empty($eventrecordinfo)) {
				
				$title = $eventrecordinfo['title'];
				$descript = $eventrecordinfo['descript'];
			}else{			
				$title = '';
				$descript = '';
			}
			$datas[1]['infos']['date'.$x_date]['title'] = $title;
			$datas[1]['infos']['date'.$x_date]['descript'] = $descript;

			if (empty($eventrecordinfo)) {
				$datas[1]['data'][] = (int)$v1;
			}else{
				$datas[1]['data'][] = array('y'=>(int)$v1,
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}	
		}

		$res['y_data'] = $datas;
		$res['x_cat'] = $x_cat;
		return $res;
	}

	//本周注册、下载流失率
	private function CurWeekRose($stationid,$sdate,$edate){
		$nt = new Psys_StationModel();
		$data = $nt -> CurWeekRose($stationid,$sdate,$edate);

		$nts = new Psys_ResourceModel();

		$datas[0]['name'] ='本周注册流失率';
		foreach($data['regrose'] as $k => $v){
			$x_date = $x_cat[] = substr(str_replace('_','/',$k),5);
			$eventrecordinfo = $nts -> EventRecordInfo(str_replace('-','_',$k),0,$stationid);
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
				$datas[0]['data'][] = (int)$v;
			}else{
				$datas[0]['data'][] = array('y'=>(int)$v,
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}	
		}

		$datas[1]['name'] ='本周下载流失率';
		foreach($data['downrose'] as $k1 => $v1){
			$x_date = substr(str_replace('_','/',$k1),5);
			$eventrecordinfo = $nts -> EventRecordInfo(str_replace('-','_',$k1),0,$stationid);
			//返回特殊点的提示信息
			if (!empty($eventrecordinfo)) {
				
				$title = $eventrecordinfo['title'];
				$descript = $eventrecordinfo['descript'];
			}else{			
				$title = '';
				$descript = '';
			}
			$datas[1]['infos']['date'.$x_date]['title'] = $title;
			$datas[1]['infos']['date'.$x_date]['descript'] = $descript;

			if (empty($eventrecordinfo)) {
				$datas[1]['data'][] = (int)$v1;
			}else{
				$datas[1]['data'][] = array('y'=>(int)$v1,
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}	
		}

		$res['y_data'] = $datas;
		$res['x_cat'] = $x_cat;
		return $res;
	}

	//每日所有车站流程汇总页面
  	public function totalstationAction(){
  		$date = reqstr('date');
  		if (!$date) {
  			$date = date('Y-m-d',strtotime('-1 day'));
  		}

		$this->smarty->assign('date',$date);
  		$this->forward = 'totalstationproce';
  	}

  	//每日所有车站流程汇总数据
  	public function stationinfoAction(){
  		$date = reqstr('date');
  		$page = reqnum('page',1);
		$pagesize = reqnum('rp',50);
		$isgrahp = reqnum('isgrahp');
  		if (!$date) {
  			$date = date('Y-m-d',strtotime('-1 day'));
  		}

  		$nt = new Psys_StationModel();

  		//获取全部车站信息
		$allstation = $nt -> StationInfo($date);
		if ($allstation['keyval']['ad1']) {
			array_unshift($allstation['keyval'],'ALL');
		}
		
		//每个车站信息
		$singerstation = $nt -> StationInfo($date,false);
		$stations = $nt->station();

		foreach($singerstation as &$v){
			$v['station'] = $this -> getstationname($stations,$v['station']);
		}
		array_unshift($singerstation, $allstation['keyval']);		
		
		//拼接数据格式
		$offset = ($page-1)*$pagesize;
		$data['total'] = count($singerstation);
		$result = array_slice($singerstation, $offset,$pagesize);
		$data['page'] = $page;
		$data['rows'] = $result;

		//拼接返回的数据格式
		$graph = array();
		foreach($data['rows'] as &$v){
			$graph[] = $v['cell'] = array_values($v);
			array_splice($v,0,7);
		}
		
		//统计图数据格式
		if ($isgrahp) {
			$res['x_cat'] = array('连接数','广告1','注册页','注册数','首页数');
			$i = 0;
			foreach($graph as $k=>$v){

				//拼接分类
				$datas[$k]['name']= $v[0];
				if ($i>0) {
					$datas[$k]['visible'] = false;
				}
				//拼接数据
				array_shift($v);
				foreach($v as $v1){
					$datas[$k]['data'][]=(int)$v1;					
						
				}
				array_pop($datas[$k]['data']);
				$res['y_data'] = $datas;
				$i++;
			}
			return $res;
		}

		return $data;
  	}

  	//旧的前置流程统计
  	public function oldwebcountAction(){

  		$date = reqstr('date','2015-01-30');
  		$allowdate = strtotime('2015-01-31');

  		if (strtotime($date) >= $allowdate) {
  			echo '旧流程只能统计2015-01-31之前的数据 <a href="/station/oldwebcount" >点击此处返回</a>';
  			return;
  		}
  		if ($date) {
  			$cdate = date('Y-m-d',strtotime($date));
  		}else{
  			$cdate = date('Y-m-d',strtotime('-1 day'));
  		}

  		//获取车站
		$stationid = reqnum('station', 3);
		$obj = new Psys_StationModel();
		$stations = $obj->station();
		foreach($stations as $v){
			if ($v['id'] == $stationid) {
				$curstation = $v['stationname'];
			}
		}

		$data = $obj->OldWebCount($cdate,$stationid);
		var_dump($data);
		exit;
		$this->smarty->assign('curstation',$curstation);
		$this->smarty->assign('date',$date);
		$this->smarty->assign('totallist',$data);
		$this->forward = 'oldwebcount';
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
	 * wifi每日连接情况
	 * 
	 */
	public function wifidailyAction(){
		$date = reqstr('date');
  		if (!$date) {
  			$date = date('Y-m-d',strtotime('-1 day'));
  		}

		$this->smarty->assign('date',$date);
  		$this->forward = 'wifidaily';
	}

	/**
	 * wifi每日连接详情数据ajax
	 */
	public function wifidailyinfoAction(){
		$date = reqstr('date');
  		$page = reqnum('page',1);
		$pagesize = reqnum('rp',10);

		//接收排序
		$sortname = reqstr('sortname','total');
		$sortorder = reqstr('sortorder','desc');

		$isgrahp = reqnum('isgrahp');
  		if (!$date) {
  			$date = date('Y-m-d',strtotime('-1 day'));
  		}

  		$nt = new Psys_StationModel();
  		$data = $nt -> WifiDailyInfo($date,$sortname,$sortorder,$page,$pagesize);
  		
  		if ($data['allnum'] <= 1) {
  			return 0;
  		}
  		//拼接返回的表格数据格式
  		$res['total'] = $data['allnum'];
		$res['page'] = $page;
		$res['rows'] = $data['allrow'];

		$stations = $nt->station();

		//把车站id替换成车站名
		foreach($res['rows'] as &$v){
			if (is_numeric($v['station'])) {
				$v['station'] = $this -> getstationname($stations,$v['station']);
			}
		}

		$graph = array();
		foreach($res['rows'] as &$v){
			$graph[] = $v['cell'] = array_values($v);
			array_splice($v, 0,5);
		}
	
		//拼接统计图数据格式
		if ($isgrahp) {
			$result['x_cat'] = array('总在线人数','平均在线人数(h)','高峰时段在线人数(h)','最高在线人数(h)');

			foreach($graph as $k=>$v){

				//拼接分类
				$datas[$k]['name']= $v[0];

				//拼接数据
				array_shift($v);
				foreach($v as $v1){
					$datas[$k]['data'][]=(int)$v1;	
				}
				$result['y_data'] = $datas;
			}
			return $result;
		}


	
		return $res;
	}


	/**
	 * wifi 7日连接情况
	 *  
	 */
	public function wifiweekAction(){
		
  		$this->forward = 'wifiweek';
	}

	/**
	 * wifi每7日连接详情数据ajax
	 */
	public function wifiweekinfoAction(){
  		$page = reqnum('page',1);
		$pagesize = reqnum('rp',100);

		//接收排序
		$sortname = reqstr('sortname','date');
		$sortorder = reqstr('sortorder','asc');

		$isgrahp = reqnum('isgrahp');

  		$nt = new Psys_StationModel();
  		$data = $nt -> WifiWeekInfo($sortname,$sortorder,$page,$pagesize);



  		if ($data['allnum'] <= 0) {
  			return 0;
  		}
  		//拼接返回的表格数据格式
  		$res['total'] = $data['allnum'];
		$res['page'] = $page;
		$res['rows'] = $data['allrow'];
		
		$graph = array();
		foreach($res['rows'] as &$v){
			$graph[] = $v['cell'] = array_values($v);
			array_splice($v, 0,5);
		}
		
		//拼接统计图数据格式
		$nts = new Psys_ResourceModel();
		$ydata = array();
		if ($isgrahp) {
			foreach($graph as $k=>$v){
				$cat = explode('-',$v[0]);
				$x_date = $result['x_cat'][] = substr($cat[0],5).'-'.substr($cat[1],5);
				$sdate = str_replace('/','_',$cat[0]);
				$edate = str_replace('/','_',$cat[1]);

				$dates = array($sdate,$edate);
				$eventrecordinfo = $nts -> EventRecordInfo($dates ,0,1,true);
				//返回特殊点的提示信息
				$title = '';
				$descript = '';
				if (!empty($eventrecordinfo)) {
					
					$flag = 1;
					foreach($eventrecordinfo as $info){
						$infodate = str_replace('_','-',$info['date']);	
						$infodates = date('m/d',strtotime($infodate));						
						$title .= '<br /><b>'.$info['title'].'</b><br />'.$info['descript'].'<br />';
						$descript = '<br />';	
					}

				}else{
					$flag = 0;
				}
				$infos['date'.$x_date]['title'] = $title;
				$infos['date'.$x_date]['descript'] = $descript;

				//拼接数据
				array_shift($v);
				if (!$flag) {
					// $datas[0]['data'][] = (int)$v['num'];
					$totalconn[] = (int)$v[0];
					/*$avgdaily[] = (int)$v[1];
					$avghour[] = (int)$v[2];
					$avgtop[] = (int)$v[3];*/
				}else{				
					/*$datas[0]['data'][] = array('y'=>(int)$v['num'],
												'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
												'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色*/

					$totalconn[] = array('y'=>(int)$v[0],
												'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
												'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));
				}
				/*$totalconn[] = (int)$v[0];
				$avgdaily[] = (int)$v[1];
				$avghour[] = (int)$v[2];
				$avgtop[] = (int)$v[3];*/
				$result['y_data'] = array(
						array('name'=>'总连接数','data'=>$totalconn,'infos'=>$infos),
						/*array('name'=>'日均','data'=>$avgdaily),
						array('name'=>'平均每小时(/h)','data'=>$avghour),
						array('name'=>'高峰时段(/h)','data'=>$avgtop)*/
					);
			}
			return $result;
		}


	
		return $res;
	}

	/**
	 * 广告1与广告2展示页面
	 */
	public function adshowpvAction(){
		$date = reqstr('date');
  		if (!$date) {
  			$date = date('Y-m-d',strtotime('-1 day'));
  		}

		$this->smarty->assign('date',$date);
		$this->forward='adshowpv';
	}

	/**
	 * 广告1与广告2展示的pv详情
	 */
	public function adshowpvinfoAction(){
		$page = reqnum('page',1);
		$pagesize = reqnum('rp',100);
		$date = reqstr('date')?date('Y_m_d',strtotime(reqstr('date'))):date('Y_m_d',strtotime('-1 day'));
		//接收排序
		$sortname = reqstr('sortname','total');
		$sortorder = reqstr('sortorder','asc');
		$nt = new Psys_StationModel();
		$data = $nt -> AdShowPvInfo($page,$pagesize,$date,$sortname,$sortorder);

		if ($data['allnum'] <= 0) {
  			return 0;
  		}
  		//拼接返回的表格数据格式
  		$res['total'] = $data['allnum'];
		$res['page'] = $page;
		$res['rows'] = $data['allrow'];
		
		$graph = array();
		foreach($res['rows'] as &$v){
			if ($v['show_type'] == '1') {
				$v['show_type'] = '广告1';
			}elseif($v['show_type'] == '2'){
				$v['show_type'] = '广告2';
			}
			$graph[] = $v['cell'] = array_values($v);
			array_splice($v, 0,7);
		}
		return $res;

	}

	/**
	 * 广告PV统计图
	 */
	public function graphadpvAction(){
		$date = reqstr('date')?date('Y_m_d',strtotime(reqstr('date'))):date('Y_m_d',strtotime('-1 day'));
		$nt = new Psys_StationModel();
		$data['ad1'] = $nt->GraphAdPv($date,1);
		$data['ad2'] = $nt->GraphAdPv($date,2);
		return $data;
	}

	/**
	 * 首页游戏广告流程统计
	 */
	public function sindexgameadAction(){
		$nt = new Psys_StationModel();
		$stations = $nt->station();

		$date = reqstr('date');
  		if (!$date) {
  			$date = date('Y-m-d',strtotime('-1 day'));
  		}
  		$stationid = reqnum('stationid',1);

		$this->smarty->assign('date',$date);
		$this->smarty->assign('stationid',$stationid);
		$this->smarty->assign('stations',$stations); 
		$this->forward='sindexgamead';
	}


	public function tablegameadAction(){
		$page = reqnum('page',1);
		$pagesize = reqnum('rp',100);
		$date = reqstr('date')?date('Y_m_d',strtotime(reqstr('date'))):date('Y_m_d',strtotime('-1 day'));
		//接收排序
		$sortname = reqstr('sindexnum','sindexnum');
		$sortorder = reqstr('sortorder','desc');
		$stationid = reqstr('stationid',1);
		$nt = new Psys_StationModel();
		$seleced = "date,stationid,appid,sindexnum,detailnum,downnum,sys";
		$data = $nt -> GetList(array('date'=>$date,'stationid'=>$stationid),$sortname.' '.$sortorder,$page,$pagesize,$seleced,'rha_game_ads_process');
		/*var_dump($data);
		exit;*/

		if ($data['allnum'] <= 0) {
  			return 0;
  		}
  		//拼接返回的表格数据格式
  		$res['total'] = $data['allnum'];
		$res['page'] = $page;
		$res['rows'] = $data['allrow'];
		
		$stations = $nt->station();
		foreach($res['rows'] as &$v){
			$v['stationid'] = $this -> getstationname($stations,$v['stationid']);
			$v['sindexnum'] = $v['sindexnum']?$v['sindexnum']:0;
			$v['detailnum'] = $v['detailnum']?$v['detailnum']:0;
			$v['downnum'] = $v['downnum']?$v['downnum']:0;
			$v['appid'] = $nt->GetAppName($v['appid']);
			$v['cell'] = array_values($v);
			
			array_splice($v, 0,5);
		}
		return $res;
	}


	/**
	 * 首页游戏广告流程统计图
	 */
	public function graphgameadAction(){
		$date = reqstr('date')?date('Y_m_d',strtotime(reqstr('date'))):date('Y_m_d',strtotime('-1 day'));
		$stationid = reqstr('stationid',1);

		$nt = new Psys_StationModel();
		$seleced = "appid,sindexnum,detailnum,downnum";
		$data_an = $nt -> GetList(array('date'=>$date,'stationid'=>$stationid,'sys'=>'android'),'sindexnum desc',$page,$pagesize,$seleced,'rha_game_ads_process');

		$data_ios = $nt -> GetList(array('date'=>$date,'stationid'=>$stationid,'sys'=>'ios'),'sindexnum desc',$page,$pagesize,$seleced,'rha_game_ads_process');
		/*var_dump($data);
		exit;*/

		if ($data_an['allnum'] <= 0 && $data_ios['allnum']) {
  			return 0;
  		}

  		$result['x_cat'] = array('首页点击数量','进入详情页数量','下载数量');
  		$i = 0;
  		foreach($data_an['allrow'] as $k=>$v){
  			//$result['x_cat'][$i] = $v['ad_name'];
			//拼接分类
			$datas_an[$i]['name']= $nt->GetAppName($v['appid']);			
			$datas_an[$i]['data'][]=(int)$v['sindexnum'];
			$datas_an[$i]['data'][]=(int)$v['detailnum'];
			$datas_an[$i]['data'][]=(int)$v['downnum'];
			$i++;
		}

		$j = 0;
  		foreach($data_ios['allrow'] as $k=>$v){
  			//$result['x_cat'][$j] = $v['ad_name'];
			//拼接分类
			$datas_ios[$j]['name']= $nt->GetAppName($v['appid']);			
			$datas_ios[$j]['data'][]=(int)$v['sindexnum'];
			$datas_ios[$j]['data'][]=(int)$v['detailnum'];
			$datas_ios[$j]['data'][]=(int)$v['downnum'];
			$j++;
		}
		$result['android']['y_data'] = $datas_an;
		$result['ios']['y_data'] = $datas_ios;
		return $result;

	}

	/**
	 * 首页栏目导航pv统计页面
	 * @return 
	 */
	public function navigatorAction(){
		$nt = new Psys_StationModel();
		$stations = $nt->station();

		$sdate = reqstr('sdate');
		$edate = reqstr('edate');
  		if (!$edate) {
  			$edate = date('Y-m-d',strtotime('-1 day'));
  		}

  		if (!$sdate) {
  			$sdate = date('Y-m-d',strtotime('-7 day'));
  			
  		}
  		$stationid = reqnum('stationid',1);
		$this->smarty->assign('sdate',$sdate);
		$this->smarty->assign('edate',$edate);
		$this->smarty->assign('stationid',$stationid);
		$this->smarty->assign('stations',$stations); 
		$this->forward = 'navigator';

	}

	/**
	 * 首页栏目导航pv数据
	 * @return 
	 */
	public function tablenavigatorAction(){
		$sdate = reqstr('sdate');
		$edate = reqstr('edate');
		$page = reqnum('page',1);
		$pagesize = reqnum('rp',100);
		$sortname = reqstr('sortname','date');
		$sortorder = reqstr('sortorder','asc');
		$stationid = reqstr('stationid',1);
  		if (!$edate) {
  			$edate = date('Y_m_d',strtotime('-1 day'));
  		}else{
  			$edate = date('Y_m_d',strtotime($edate));
  		}

  		if (!$sdate) {
  			$sdate = date('Y_m_d',strtotime('-7 day'));
  		}else{
  			$sdate = date('Y_m_d',strtotime($sdate));
  		}
  		
  		$nt = new Psys_StationModel();
		$stations = $nt->station();
		$data = $nt -> NavigatorPv($sdate,$edate,$stationid,$page,$pagesize,$sortname,$sortorder);

		if ($data['allnum'] <= 0) {
  			return 0;
  		}
  		//拼接返回的表格数据格式
  		$res['total'] = $data['allnum'];
		$res['page'] = $page;
		$res['rows'] = $data['allrow'];
		
		$stations = $nt->station();
		foreach($res['rows'] as &$v){
			$v['stationid'] = $this -> getstationname($stations,$v['stationid']);
			$v['cell'] = array_values($v);
			
			array_splice($v, 0,6);
		}
		return $res;
	}

	/**
	 * 首页栏目导航pv统计图
	 * @return 
	 */
	public function graphnavigatorAction(){

		$sdate = reqstr('sdate');
		$edate = reqstr('edate');
		$stationid = reqstr('stationid',1);
  		if (!$edate) {
  			$edate = date('Y_m_d',strtotime('-1 day'));
  		}else{
  			$edate = date('Y_m_d',strtotime($edate));
  		}

  		if (!$sdate) {
  			$sdate = date('Y_m_d',strtotime('-7 day'));
  		}else{
  			$sdate = date('Y_m_d',strtotime($sdate));
  		}

		$nt = new Psys_StationModel();
		$data = $nt -> NavigatorPv($sdate,$edate,$stationid);

		if ($data['allnum'] = 0) {
  			return 0;
  		}
  		
  		$nts = new Psys_ResourceModel();
  		$datas[0]['name'] = '车站服务PV';
  		$datas[1]['name'] = '音乐点击PV';
  		$datas[2]['name'] = '游戏点击PV';
  		$datas[3]['name'] = '电影点击PV';
  		$datas[4]['name'] = '应用点击PV';
  		foreach($data['allrow'] as $k=>$v){
			$x_date = $result['x_cat'][] = $v['date'];
			$eventrecordinfo = $nts -> EventRecordInfo($v['date'],0,$stationid);
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
			$datas[1]['infos']['date'.$x_date]['title'] = $title;
			$datas[1]['infos']['date'.$x_date]['descript'] = $descript;
			$datas[2]['infos']['date'.$x_date]['title'] = $title;
			$datas[2]['infos']['date'.$x_date]['descript'] = $descript;
			$datas[3]['infos']['date'.$x_date]['title'] = $title;
			$datas[3]['infos']['date'.$x_date]['descript'] = $descript;
			$datas[4]['infos']['date'.$x_date]['title'] = $title;
			$datas[4]['infos']['date'.$x_date]['descript'] = $descript;


			if (empty($eventrecordinfo)) {
				$datas[0]['data'][] = (int)$v['stationpv'];
				$datas[1]['data'][] = (int)$v['musicpv'];
				$datas[2]['data'][] = (int)$v['gamepv'];
				$datas[3]['data'][] = (int)$v['moviepv'];
				$datas[4]['data'][] = (int)$v['apppv'];
			}else{
				$datas[0]['data'][] = array('y'=>(int)$v['stationpv'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
				$datas[1]['data'][] = array('y'=>(int)$v['musicpv'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
				$datas[2]['data'][] = array('y'=>(int)$v['gamepv'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
				$datas[3]['data'][] = array('y'=>(int)$v['moviepv'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
				$datas[4]['data'][] = array('y'=>(int)$v['apppv'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}	

			$datas[1]['visible'] = false;
			$datas[2]['visible'] = false;
			$datas[3]['visible'] = false;
			$datas[4]['visible'] = false;
			
		}
		
		$result['y_data'] = $datas;
		
		return $result;
	}

	/**
	 * wifi连接详情页面
	 */
	public function wifiinfoAction(){
		$nt = new Psys_StationModel();
		$stations = $nt->station();
		$this->smarty->assign('stations',$stations);
		$this->forward = 'wifiinfo';
	}

	/**
	 * wifi链接详情数据
	 */
	public function wifidataAction(){
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

		// $station = (int)$info['station']?(int)$info['station']:0; // 0 表示查询出所有车站

		$station = empty($info['station'])?0:implode(',',$info['station']);
		// echo $station;

		if (!$sdate || !$edate) {
			$edate = date('Y-m-d');
			$sdate = date('Y-m-d',strtotime("-$date day"));
		}

		if ($sdate < '2015-02-01') {
			$sdate = '2015-02-01';
		}

		//如果时间区间大于30天则按照30天计算
		/*$date_area = abs(strtotime($edate) - strtotime($sdate)) / 86400;
		if ($date_area > 30) {
			$sdate = date('Y-m-d',strtotime("-30 day"));
		}*/
		$nt = new Psys_StationModel();
		$tabledata = $nt->wifidata($sdate,$edate,$station,$page,$pagesize);
		//分页
		$paging = $this->paging($tabledata['allnum'],$page,$pagesize,count($tabledata['allrow']));

		//wifi连接统计图
		$graphdata = $nt->wifidata($sdate,$edate,$station,$page,$pagesize,1);
		// print_r($graphdata);

		//注册统计图
		$regdata = $nt->RegData($sdate,$edate,$station);
		// print_r($regdata);

		$stations = $nt->station();
		if ($station) {
			$stationname = $this -> getstationname($stations,$station);
		}else{
			$stationname = '所有车站';
		}
		$datas[0]['name'] = 'wifi连接数';
		$datas[1]['name'] = '注册数';
		//拼接统计图数据格式
		$nts = new Psys_ResourceModel();
		$dates = array(str_replace('-','_',$sdate),str_replace('-','_',$edate));
		// $flag = 0;
  		foreach($graphdata['allrow'] as $k=>$v){

  			//生成补位判断数据
  			$graph_date[] = 'reg'.str_replace('-','_',$v['date']);
			$x_date = $result['x_cat'][] = date('m/d',strtotime($v['date']));
			$flags = 'a'.str_replace('-','_',$v['date']);

			if ($station) {
				$eventrecordinfo = $nts -> EventRecordInfo(str_replace('-','_',$v['date']),0,$station);
				//返回特殊点的提示信息
				if (!empty($eventrecordinfo)) {
					$title = '';
					$descript = '';
					if (is_numeric($station)) {
						$title = $eventrecordinfo['title'];
						$descript = $eventrecordinfo['descript'];
					}else{

						foreach($eventrecordinfo as $info){
							$infodate = str_replace('_','-',$info['date']);	
							$infodates = date('m/d',strtotime($infodate));
							if ($infodates == $x_date) {

								//去掉重复的标题和描述
								if (!stristr($title,$info['title'])) {
									$title .= '<br /><b>'.$info['title'].'</b><br />'.$info['descript'].'<br />';
									$descript .= '<br />';
								}
									
								$flag['a'.$info['date']] = 1;

							}else{
								$flag['a'.$info['date']] = 0;
							}						
							
						}
					}
					
				}else{			
					$title = '';
					$descript = '';
				}
			}else{
				$eventrecordinfo = $nts -> EventRecordInfo($dates ,0,$station,true);
				//返回特殊点的提示信息
				if (!empty($eventrecordinfo)) {
					$title = '';
					$descript = '';
					foreach($eventrecordinfo as $info){
						$infodate = str_replace('_','-',$info['date']);	
						$infodates = date('m/d',strtotime($infodate));
						if ($infodates == $x_date) {
							$title .= $info['title'];
							$descript .= $info['descript'];
							$flag['a'.$info['date']] = 1;

						}else{
							$flag['a'.$info['date']] = 0;
						}						
						
					}

				}
			}
			
			
			$datas[0]['infos']['date'.$x_date]['title'] = $title;
			$datas[0]['infos']['date'.$x_date]['descript'] = $descript;
			if ($station) {
				if (is_numeric($station)) {
					if (empty($eventrecordinfo)) {
						$datas[0]['data'][] = (int)$v['num'];
					}else{				
						$datas[0]['data'][] = array('y'=>(int)$v['num'],
													'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
													'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
					}
				}else{
					if (!$flag[$flags]) {
						$datas[0]['data'][] = (int)$v['num'];
					}else{				
						$datas[0]['data'][] = array('y'=>(int)$v['num'],
													'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
													'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
					}
				}
					
			}else{
				if (!$flag[$flags]) {
					$datas[0]['data'][] = (int)$v['num'];
				}else{				
					$datas[0]['data'][] = array('y'=>(int)$v['num'],
												'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
												'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
				}
			}
			
		}

		//整理注册数据
		foreach($regdata as $reg){
			$register['reg'.$reg['date']]['num'] = $reg['num'];
			$register['reg'.$reg['date']]['stationid'] = $station;
		}

		foreach($graph_date as $g_date){
			if (!isset($register[$g_date]['num']) || !$register[$g_date]['num']){
				$register[$g_date]['num'] = 0;
				$register[$g_date]['stationid'] = $station;
			}
		}

		ksort($register);
		foreach($register as $k1=>$v1){

			$tmpdate = str_replace(array('reg','_'),array('','-'),$k1);
			$x_date = date('m/d',strtotime($tmpdate));
			$da = str_replace('reg','',$k1);
			$flags = 'a'.$da;

			if ($v1['stationid']) {
				$eventrecordinfo = $nts -> EventRecordInfo($da,0,$v1['stationid']);
				//返回特殊点的提示信息			

				if (!empty($eventrecordinfo)) {
					$title = '';
					$descript = '';
					if (is_numeric($station)) {
						$title = $eventrecordinfo['title'];
						$descript = $eventrecordinfo['descript'];
					}else{

						foreach($eventrecordinfo as $info){
							$infodate = str_replace('_','-',$info['date']);	
							$infodates = date('m/d',strtotime($infodate));
							if ($infodates == $x_date) {

								//去掉重复的标题和描述
								if (!stristr($title,$info['title'])) {
									$title .= '<br /><b>'.$info['title'].'</b><br />'.$info['descript'].'<br />';
									$descript .= '<br />';
								}
									
								$flag['a'.$info['date']] = 1;

							}else{
								$flag['a'.$info['date']] = 0;
							}						
							
						}
					}
					
				}else{			
					$title = '';
					$descript = '';
				}
			}else{
				$eventrecordinfo = $nts -> EventRecordInfo($dates ,0,$v1['stationid'],true);
				//返回特殊点的提示信息
				if (!empty($eventrecordinfo)) {
					$title = '';
					$descript = '';
					foreach($eventrecordinfo as $info){
						$infodate = str_replace('_','-',$info['date']);	
						$infodates = date('m/d',strtotime($infodate));
						if ($infodates == $x_date) {
							$title .= $info['title'];
							$descript .= $info['descript'];
							$flag['a'.$info['date']] = 1;

						}else{
							$flag['a'.$info['date']] = 0;
						}						
						
					}

				}
			}
			
			
			$datas[1]['infos']['date'.$x_date]['title'] = $title;
			$datas[1]['infos']['date'.$x_date]['descript'] = $descript;
			if ($station) {
				if (is_numeric($station)) {
					if (empty($eventrecordinfo)) {
						$datas[1]['data'][] = (int)$v1['num'];
					}else{				
						$datas[1]['data'][] = array('y'=>(int)$v1['num'],
													'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
													'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
					}
				}else{
					if (!$flag[$flags]) {
						$datas[1]['data'][] = (int)$v1['num'];
					}else{				
						$datas[1]['data'][] = array('y'=>(int)$v1['num'],
													'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
													'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
					}
				}
					
			}else{
				if (!$flag[$flags]) {
					$datas[1]['data'][] = (int)$v1['num'];
				}else{				
					$datas[1]['data'][] = array('y'=>(int)$v1['num'],
												'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
												'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
				}
			}
			
		}
		$result['y_data'] = $datas;
		$isbaifenbi = reqnum('baifenbi',0);
		if ($isbaifenbi) {
			$baifenbis[0]['name'] = '注册连接百分比';
			foreach($datas[0]['data'] as $k1 => $connect1){
				foreach($datas[1]['data'] as $k2=>$reg1){
					if ($k1 == $k2) {
						if (is_numeric($reg1) && is_numeric($connect1)) {
							$b = ($reg1/$connect1)*100;
						}else{
							$b = ($reg1['y']/$connect1['y'])*100;
						}					
						
						$baifenbis[0]['data'][] = (float)round($b,2);
					}
				}
			}
					
			$result['baifenbi'] = $baifenbis;
		}		
		
		return array('table'=>$tabledata['allrow'],'graph'=>$result,'paging'=>$paging);		
		

	}

	/**
	 * wifi链接详情与ap访问详情
	 */
	public function WifiApDetailAction(){
		$date = reqstr('date',date('Y-m-d',strtotime('-1 day')));
		$station = reqnum('station',0);//如果是0则表示所有车站
		$nt = new Psys_StationModel();

		$stations = $nt->station();
		if ($station) {
			$stationname = $this -> getstationname($stations,$station);
		}else{
			$stationname = '所有车站';
		}
		//查出wifi当日每小时的连接人数
		/*-----------------------------------------------------------------------------*/
		$data_w = $nt->WifiHourNum($date,$station);
  		foreach($data_w as $k=>$v){
			$wifinum['x_cat'][] = $v['hour'].'点';
			$datas[0]['data'][] = (int)$v['wifinum'];
			
		}
		$datas[0]['name'] = $stationname;
		$wifinum['y_data'] = $datas;
		/*-----------------------------------------------------------------------------*/
		//当日每个ap的访问次数
		$data_ap = $nt->ApQueryNum($date,$station);	
		foreach($data_ap as $k=>$v){
			$apnum['x_cat'][] = $v['apkey'];
			$apnums[0]['data'][] = (int)$v['apnum'];
			
		}
		$apnums[0]['name'] = $stationname;
		$apnum['y_data'] = $apnums;

		return array('wifinum'=>$wifinum,'apnum'=>$apnum);
	}

	/**
	 * 页面时长统计
	 */
	public function staytimeAction(){
		$nt = new Psys_StationModel();
		$stations = $nt->station();
		$this->smarty->assign('stations',$stations);
		$this->forward = 'staytime';
	}

	/**
	 * 页面停留时间详情
	 */
	public function staytimeinfoAction(){
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

		if (!$sdate || !$edate) {
			$edate = date('Y_m_d');
			$sdate = date('Y_m_d',strtotime("-$date day"));
		}

		//如果时间区间大于30天则按照30天计算
		$date_area = abs(strtotime($edate) - strtotime($sdate)) / 86400;
		
		if ($date_area > 30) {
			$sdate = date('Y_m_d',strtotime("-30 day"));
		}
		$sdate = str_replace('-','_',$sdate);
		$edate = str_replace('-','_',$edate);

		$nt = new Psys_StationModel();
		$data = $nt -> StayTimeInfo($sdate,$edate,$station,$page,$pagesize);
		
		//分页
		$paging = $this->paging($data['allnum'],$page,$pagesize,count($data['allrow']));

		$nts = new Psys_ResourceModel();
		//所有页面
		$datas[0]['name'] = '所有页面';
		$datas[1]['name'] = '广告1';
		$datas[1]['visible'] = false;
		$datas[2]['name'] = '注册页面';
		$datas[2]['visible'] = false;
		$datas[3]['name'] = '广告2';
		$datas[3]['visible'] = false;
		$datas[4]['name'] = '首页';
		$datas[4]['visible'] = false;
		$datas[5]['name'] = '车站服务';
		$datas[5]['visible'] = false;
		$datas[6]['name'] = '电影';
		$datas[6]['visible'] = false;
		$datas[7]['name'] = '音乐';
		$datas[7]['visible'] = false;
		$datas[8]['name'] = '游戏';
		$datas[8]['visible'] = false;
		$datas[9]['name'] = '应用';
		$datas[9]['visible'] = false;		
		foreach($data['allrow'] as &$v){
			//表格展示数据
			$v['date'] = str_replace('_','-',$v['date']);
			$v['total'] = (float)round(($v['indexindex'] + $v['indexregister'] + $v['indexwelcome'] + $v['indexsindex'] + $v['stationindex'] + $v['movieindex'] + $v['musicindex'] + $v['gameindex'] + $v['appindex'])/3600,2);
			$v['indexindex'] = (float)round($v['indexindex']/3600,2);
			$v['indexregister'] = (float)round($v['indexregister']/3600,2);
			$v['indexwelcome'] = (float)round($v['indexwelcome']/3600,2);
			$v['indexsindex'] = (float)round($v['indexsindex']/3600,2);
			$v['stationindex'] = (float)round($v['stationindex']/3600,2);
			$v['movieindex'] = (float)round($v['movieindex']/3600,2);
			$v['musicindex'] = (float)round($v['musicindex']/3600,2);
			$v['gameindex'] = (float)round($v['gameindex']/3600,2);
			$v['appindex'] = (float)round($v['appindex']/3600,2);

			/*------------------------------统计图数据-------------------------------*/			
			$x_date = $result['x_cat'][] = date('m/d',strtotime($v['date']));
			$eventrecordinfo = $nts -> EventRecordInfo(str_replace('-','_',$v['date']),0,$station);
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
			$datas[1]['infos']['date'.$x_date]['title'] = $title;
			$datas[1]['infos']['date'.$x_date]['descript'] = $descript;
			$datas[2]['infos']['date'.$x_date]['title'] = $title;
			$datas[2]['infos']['date'.$x_date]['descript'] = $descript;
			$datas[3]['infos']['date'.$x_date]['title'] = $title;
			$datas[3]['infos']['date'.$x_date]['descript'] = $descript;
			$datas[4]['infos']['date'.$x_date]['title'] = $title;
			$datas[4]['infos']['date'.$x_date]['descript'] = $descript;
			$datas[5]['infos']['date'.$x_date]['title'] = $title;
			$datas[5]['infos']['date'.$x_date]['descript'] = $descript;
			$datas[6]['infos']['date'.$x_date]['title'] = $title;
			$datas[6]['infos']['date'.$x_date]['descript'] = $descript;
			$datas[7]['infos']['date'.$x_date]['title'] = $title;
			$datas[7]['infos']['date'.$x_date]['descript'] = $descript;
			$datas[8]['infos']['date'.$x_date]['title'] = $title;
			$datas[8]['infos']['date'.$x_date]['descript'] = $descript;
			$datas[9]['infos']['date'.$x_date]['title'] = $title;
			$datas[9]['infos']['date'.$x_date]['descript'] = $descript;

			if (empty($eventrecordinfo)) {
				$datas[0]['data'][] = (int)$v['total'];
				$datas[1]['data'][] = (int)$v['indexindex'];
				$datas[2]['data'][] = (int)$v['indexregister'];
				$datas[3]['data'][] = (int)$v['indexwelcome'];
				$datas[4]['data'][] = (int)$v['indexsindex'];
				$datas[5]['data'][] = (int)$v['stationindex'];
				$datas[6]['data'][] = (int)$v['movieindex'];
				$datas[7]['data'][] = (int)$v['musicindex'];
				$datas[8]['data'][] = (int)$v['gameindex'];
				$datas[9]['data'][] = (int)$v['appindex'];
			}else{
				$datas[0]['data'][] = array('y'=>(int)$v['total'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
				$datas[1]['data'][] = array('y'=>(int)$v['indexindex'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
				$datas[2]['data'][] = array('y'=>(int)$v['indexregister'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
				$datas[3]['data'][] = array('y'=>(int)$v['indexwelcome'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
				$datas[4]['data'][] = array('y'=>(int)$v['indexsindex'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
				$datas[5]['data'][] = array('y'=>(int)$v['stationindex'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
				$datas[6]['data'][] = array('y'=>(int)$v['movieindex'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
				$datas[7]['data'][] = array('y'=>(int)$v['musicindex'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
				$datas[8]['data'][] = array('y'=>(int)$v['gameindex'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
				$datas[9]['data'][] = array('y'=>(int)$v['appindex'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}	

		}
		$result['y_cat'] = $datas;

		$stations = $nt->station();
		if ($station) {
			$stationname = $this -> getstationname($stations,$station);
		}else{
			$stationname = '所有车站';
		}
		
		return array('table'=>$data['allrow'],'graph'=>$result,'paging'=>$paging,'station'=>$stationname);

	}

	/**
	 * 广告1与广告2展示页面
	 */
	public function showadpvAction(){
		$nt = new Psys_StationModel();
		$stations = $nt->station();
		$this->smarty->assign('stations',$stations);
		$this->forward='showadpv';
	}

	/**
	 * 广告展示详情
	 */
	public function showadpvinfoAction(){
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
		$adtype = trim($info['adtype'])?trim($info['adtype']):'ad'; //默认是广告1
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

		$nt = new Psys_StationModel();
		$data = $nt -> ShowAdPvInfo($sdate,$edate,$station,$adtype,$page,$pagesize);
		$graphdata = $nt -> ShowAdPvInfo($sdate,$edate,$station,$adtype,$page,$pagesize,1);
		$paging = $this->paging($data['allnum'],$page,$pagesize,count($data['allrow']));

		/*------------------------------数据格式---------------------------------------------*/	
		foreach($data['allrow'] as $k => &$v){
			$adname = $nt -> GetAdName($v['detail']);
			$v['adname'] = $adname['adname'];
		}
		
		$nts = new Psys_ResourceModel();
		foreach($graphdata['allrow'] as $v1){
			$adinfo = $nt -> GetAdName($v1['detail']);
			$x_date = $x_cat[] = date('m/d',strtotime(str_replace('_','-',$v1['date'])));

			$eventrecordinfo = $nts -> EventRecordInfo($v1['date'],5,$station);
			//返回特殊点的提示信息
			if (!empty($eventrecordinfo)) {
				
				$title = $eventrecordinfo['title'];
				$descript = $eventrecordinfo['descript'];
			}else{			
				$title = '';
				$descript = '';
			}
			$datas[$adinfo['id']]['infos']['date'.$x_date]['title'] = $title;
			$datas[$adinfo['id']]['infos']['date'.$x_date]['descript'] = $descript;

			if (empty($eventrecordinfo)) {
				$datas[$adinfo['id']]['data'][] = (int)$v1['num'];
			}else{
				$datas[$adinfo['id']]['data'][] = array('y'=>(int)$v1['num'],
											'marker'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000',//设置当前点的颜色
											'states'=>array('hover'=>array('fillColor'=>'#FF0000','lineColor'=>'#FF0000'))));//设置当前点鼠标经过的颜色
			}
			$datas[$adinfo['id']]['name'] = $adinfo['adname'];
			
		}
		
		/*------------------------------chart数据格式需要索引从0开始---------------------------------------------*/	
		$j=0;
		foreach(array_unique($x_cat) as $v_x){
			$result['x_cat'][$j] = $v_x;
			$j++;
		}
		
		$i = 0;
		foreach($datas as $val){
			
			$datass[$i] = $val;
			if ($i > 0) {
				$datass[$i]['visible'] = false;
			}
			$i++;
		}
	
		$result['y_cat'] = $datass;
		$stations = $nt->station();
		if ($station) {
			$stationname = $this -> getstationname($stations,$station);
		}else{
			$stationname = '所有车站';
		}
	
		return array('table'=>$data['allrow'],'graph'=>$result,'paging'=>$paging,'station'=>$stationname);
		
	}

	/**
	 * 每7天注册人数趋势图页面
	 */
	public function registerweekAction(){
		$nt = new Psys_StationModel();
		$stations = $nt->station();
		$this->smarty->assign('stations',$stations);
		$this->forward = 'registerweek';
	}

	/**
	 * 每7天注册人数趋势图数据
	 */
	public function registerweekdataAction(){
		$data = reqstr('data');
		if (!$data) {
			return;
		}
		
		$data = urldecode($data);
		$info = array();
		parse_str($data,$info);

		$date = (int)$info['date']?(int)$info['date']:7; //默认是查出7天的数据
		$sdate = trim($info['sdate'])?trim($info['sdate']):''; 
		$edate = trim($info['edate'])?trim($info['edate']):'';
		$station = (int)$info['station']?(int)$info['station']:0; // 0 表示查询出所有车站

		$nt = new Psys_StationModel();
		$res = $nt->RegisterWeekData($sdate,$edate,$station);

		$stations = $nt->station();
		if ($station) {
			$stationname = $this -> getstationname($stations,$station);
		}else{
			$stationname = '所有车站';
		}
		$datas[0]['name'] = $stationname;

		//拼接统计图数据格式
  		foreach($res as $k=>$v){
  			$datearea = explode('/', str_replace('_','-',$v['datearea']));
			$result['x_cat'][] = date('m/d',strtotime($datearea[0])).'-'.date('m/d',strtotime($datearea[1]));			
			$datas[0]['data'][] = (int)$v['num'];
		}
		$result['y_data'] = $datas;
		return $result;

	}


	/**
	 * 用户活跃数据
	 */
	public function useractiveAction(){
		$nt = new Psys_StationModel();
		$stations = $nt->station();
		$this->smarty->assign('stations',$stations);
		$this->forward = 'useractive';
	}

	/**
	 * 用户活跃数据
	 */
	public function useractiveinfoAction(){
		$data = reqstr('data');
		
		
		$data = urldecode($data);
		$info = array();
		parse_str($data,$info);

		$station = empty($info['station'])?1:implode(',',$info['station']);
		// $where['stationid_IN'] = $station;
		$nt = new Psys_StationModel();
		$all_month = $nt -> UserActiveInfo($station);

		/*$all_month = $nt -> GetList($where,'',1,100000,'cur_month,one_month,two_month,three_month,two_befor_month,three_befor_month,cur_month_regnum,cur_month_active_user','rha_active_user');*/
		
		$datas[0]['name'] = '上月活跃用户';
		$datas[1]['name'] = '前两月活跃用户';
		$datas[2]['name'] = '前三月活跃用户';
		$datas[3]['name'] = '上上月活跃用户';
		$datas[4]['name'] = '上上上月活跃用户';
		$datas[5]['name'] = '本月活跃用户';		
		$datas[6]['name'] = '本月注册用户';
		foreach($all_month as $v){
			$result['x_cat'][] = $v['cur_month'];
			$datas[0]['data'][] = (int)$v['one_month'];
			$datas[1]['data'][] = (int)$v['two_month'];			
			$datas[2]['data'][] = (int)$v['three_month'];
			$datas[3]['data'][] = (int)$v['two_befor_month'];
			$datas[4]['data'][] = (int)$v['three_befor_month'];
			$datas[5]['data'][] = (int)$v['cur_month_active_user'];
			$datas[6]['data'][] = (int)$v['cur_month_regnum'];
		}


		$datas[1]['visible'] = false;
		$datas[2]['visible'] = false;
		$datas[3]['visible'] = false;
		$datas[4]['visible'] = false;
		$datas[5]['visible'] = false;
		$datas[6]['visible'] = false;

		$result['y_data'] = $datas;
		return $result;
		
		
	}

	/**
	 * 所选时间段范围内的每7天WiFi连接数与注册数
	 */
	public function wifidataweekAction(){
		$data = reqstr('data');
		if (!$data) {
			return;
		}
			
		$data = urldecode($data);
		$info = array();
		parse_str($data,$info);

		$date = (int)$info['date']?(int)$info['date']:7; //默认是查出7天的数据
		$sdate = trim($info['sdate'])?trim($info['sdate']):''; 
		$edate = trim($info['edate'])?trim($info['edate']):'';

		//默认是青岛南
		$station = empty($info['station'])?1:implode(',',$info['station']);

		if (!$sdate || !$edate) {
			$edate = date('Y-m-d');
			$sdate = date('Y-m-d',strtotime("-$date day"));
		}

		if ($sdate < '2015-02-01') {
			$sdate = '2015-02-01';
		}
		$isbaifenbi = reqnum('baifenbi',0);			

		$dates = $this->cutdate($sdate,$edate);
		$nt = new Psys_StationModel();
		foreach($dates as $v_date){
			$res[] = $nt -> wifidataweek($station,$v_date['sdate'],$v_date['edate']);
		}

		$datas[0]['name'] = 'wifi连接数';
		$datas[1]['name'] = '注册数';
		
		foreach($res as $v){
			$result['x_cat'][] = $v['datearea'];
			$datas[0]['data'][] = (int)$v['wifi'];
			$datas[1]['data'][] = (int)$v['reg'];
			if ($isbaifenbi) {
				$baifenbis[0]['name'] = '周注册连接百分比';	
				$b = ($v['reg']/$v['wifi'])*100;
				$baifenbis[0]['data'][] = (float)round($b,2);		
			}			
			
		}

		$result['y_data'] = $datas;

		if ($isbaifenbi) {
			$result['baifenbi'] = $baifenbis;
		}

		return $result;
	}

	/**
	 * 时间段切分
	 * @param  [type] $sdate 起始时间
	 * @param  [type] $edate 结束时间
	 * @return array  返回切割后的时间数组
	 */
	public function cutdate($sdate,$edate){
		$dates = array();//用来存放分割后每7天的日期
		$sdate = strtotime($sdate);
		$edate = strtotime($edate);
		$i = 0;
		while($sdate < $edate){
			$dates[$i]['edate']= date('Y-m-d',$edate);
			$edate = strtotime('-6 day',$edate);
			if ($edate > $sdate) {
				$dates[$i]['sdate'] = date('Y-m-d',$edate);
			}else{
				$dates[$i]['sdate'] = date('Y-m-d',$sdate);
			}
			$edate = strtotime('-1 day',$edate);
			$i++;
		}

		return array_reverse($dates);
	}

}
