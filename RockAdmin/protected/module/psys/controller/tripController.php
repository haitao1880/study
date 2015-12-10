<?php
//线路管理
class tripController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 列表
	 */
	public function vlistAction()
	{
		$keyword = reqstr('trainno','');
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",20);
		$where = '';
		$nt = new Psys_TripModel();
		if(!empty($keyword)){
			$where = " trainno LIKE '%,$keyword,%'";
		}
		$list = $nt -> getTripList($where, $page, $pagesize);
		self::inidate($list['allnum'],$page,$pagesize,count($list['allrow']));

		$this->smarty->assign('list',$list['allrow']);
		$this->smarty->assign('trainno',$keyword);
		$this->smarty->assign('psys_base_url',PSYS_BASE_URL);
		$this->forward = "vlist";
	}
	
	/**
	 * 添加
	 */
	public function vaddAction()
	{
		$ispost = reqnum('ispost', 0);
		$id = reqnum('id', 0);
		$nt = new Psys_TripModel();
		if($ispost){
			$trainno = reqstr('trainno');
			if(empty($trainno)){
				echo '<script>alert("列车信息不能为空,反回列表！"); window.history.go(-1);</script>';
				exit;
			}
			$coimg = $this->uploadvImg();
			$data = array(
				'trainno' => reqstr('trainno'),
			    'traintype' => reqstr('traintype'),
			    'bstation' => reqstr('bstation'),
			    'estation' => reqstr('estation', 0),
			    'btime' => reqstr('btime', 0),
			    'etime' => reqstr('etime'),
			    'runtime' => reqstr('runtime'),
			    'price' => reqstr('price'),
			   //'mileage' => reqstr('mileage'),
			    'flag' => reqnum('flag', 0),
				//'bdate' => reqstr('bdate'),
				//'bfreq' => reqstr('bfreq'),
				'stationlist' => reqstr('stationlist'),
				'coimg' => $coimg,
				'coordinates' => reqstr('coordinates'),
			);
			$trip = $nt->AddOne($data);
			if($trip){
				echo '<script>alert("修改成功,反回列表！");window.location.href="/trip/vlist"</script>';
				exit;
			}
		}
		$this->smarty->assign('action','vadd');
		$this->smarty->assign('isadd',$isadd);
		$this->forward = "vadd";
	}
	
	
	public function veditAction()
	{
		$ispost = reqnum('ispost', 0);
		$id = reqnum('id', 0);
		$nt = new Psys_TripModel();
		if($ispost){
			$trainno = reqstr('trainno');
			if(empty($trainno)){
				echo '<script>alert("列车信息不能为空,反回列表！"); window.history.go(-1);</script>';
				exit;
			}
			
			$coimg = $this->uploadvImg();
			$data = array(
				'trainno' => reqstr('trainno'),
			    'traintype' => reqstr('traintype'),
			    'bstation' => reqstr('bstation'),
			    'estation' => reqstr('estation', 0),
			    'btime' => reqstr('btime', 0),
			    'etime' => reqstr('etime'),
			    'runtime' => reqstr('runtime'),
			    'price' => reqstr('price'),
			    //'mileage' => reqstr('mileage'),
			    'flag' => reqnum('flag', 0),
				//'bdate' => reqstr('bdate'),
				//'bfreq' => reqstr('bfreq'),
				'stationlist' => reqstr('stationlist'),
				'coordinates' => reqstr('coordinates'),
			);
			
			if(!empty($coimg)){
				$data['coimg']= $coimg;
			}
			$where = array('id'=>$id);
			$trip = $nt->UpdateOne($data,$where);
			
			if($trip){
				echo '<script>alert("添加成功,反回列表！");window.location.href="/trip/vlist"</script>';
				exit;
			}
		}else{		
			if($id > 0){
				$where = array('id'=>$id);
				$trips = $nt->GetOne($where,"*");
				$this->smarty->assign('trip',$trips);
			}
		}
		$this->smarty->assign('action','vedit');
		//$this->smarty->assign('isadd',$isadd);
		$this->forward = "vadd";
	}
	/**
	 * 图片上传
	 */
	public function uploadvImg(){
		$dir = TRIPS_PATH;
		if (!is_dir($dir)) {
			if(!mkdir($dir,0777,true)){
				return '';
			}
		}
		$filename = '';
		$vpath = isset($_FILES['vpath'])?$_FILES['vpath']:'';
		if($vpath && ($vpath['error']==0)){
			
			$ext = strrchr($vpath['name'],'.');
			$filename = date('Ymdhis',time()). rand(100,999).$ext;			
			$dir1 = $dir.$filename;
			if(!move_uploaded_file($vpath['tmp_name'], $dir1)){
				return '';
			}
		}
		return $filename;
	}
	
	//城市添加
	public function cityAddAction(){
	$ispost = reqnum('ispost', 0);
		$nt = new Psys_TripModel();
		if($ispost){
			$cityname = reqstr('cityname');
			if(empty($cityname)){
				echo '<script>alert("城市名称不能为空,反回列表！"); window.history.go(-1);</script>';
				exit;
			}
			$data = array(
				'cityname' => reqstr('cityname'),
			    'pinyin' => reqstr('pinyin'),
			    'szm' => reqstr('szm'),		
			    'longitude' => reqstr('longitude'),
			    'latitude' => reqstr('latitude'),
			    'flag' => reqnum('flag', 0)
			);
			$citys = $nt->AddOne($data,'rht_trainstation');
			if($citys){
				echo '<script>alert("添加成功,反回列表！");window.location.href="/trip/cityList"</script>';
				exit;
			}
		}
		$this->smarty->assign('action','cityAdd');
		$this->forward = "tadd";
	}
	//城市修改
	public function cityEditAction(){
		$ispost = reqnum('ispost', 0);
		$id = reqnum('id', 0);
		$nt = new Psys_TripModel();
		if($ispost){
			$cityname = reqstr('cityname');
			if(empty($cityname)){
				echo '<script>alert("修改成功,反回列表！"); window.history.go(-1);</script>';
				exit;
			}
			$data = array(
				'cityname' => reqstr('cityname'),
			    'pinyin' => reqstr('pinyin'),
			    'szm' => reqstr('szm'),
			    'longitude' => reqstr('longitude'),
			    'latitude' => reqstr('latitude'),
			    'flag' => reqnum('flag', 0)
			);
			$where = array('id'=>$id);
			$citys = $nt->UpdateOne($data,$where,'rht_trainstation');
			if($citys){
				echo '<script>alert("修改成功,反回列表！");window.location.href="/trip/cityList"</script>';
				exit;
			}
		}else{		
			if($id > 0){
				$where = array('id'=>$id);
				$citys = $nt->GetOne($where,"*",'rht_trainstation');
				$this->smarty->assign('citys',$citys);
			}
		}
		$this->smarty->assign('action','cityEdit');
		$this->forward = "tadd";
	}
	//城市列表
	public function cityListAction(){
		$citykey = reqstr('citykey','');
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",20);
		
		$nt = new Psys_TripModel();
		$list = $nt->getCityList("szm LIKE '$citykey%'",$page,$pagesize);
		self::inidate($list['allnum'],$page,$pagesize,count($list['allrow']));

		$this->smarty->assign('list',$list['allrow']);
		$this->smarty->assign('psys_base_url',PSYS_BASE_URL);
		$this->forward = "citylist";
	}
	
	public function updateLngAction(){
		$Code = -1;
		$nt = new Psys_TripModel();
		$data = array('longitude' => reqstr('lng'),'latitude' => reqstr('lat'));
		$where = array('id'=>reqnum('id'));
		$num = $nt ->updateLng($data,$where);
		
		if($num){
			$Code = 1;
		}
		return array('Code'=>$Code);
	}
	
	
	/**
	 * 城市简介站点列表
	 */
	public function citydetaillistAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",20);
		$station_name = reqstr('station_name','');		
		$model = new Psys_TripModel();
		$where = array('flag'=>1);
		if($station_name)
		{
			$where['cityname'] = $station_name;
			$this->smarty->assign('station_name',$station_name);
		}
		$orderby = '';
		$field = 'id,cityname,flag';
		$tbname = 'rht_trainstation';
		$station_list = $model->GetList($where,$orderby,$page,$pagesize,$field,$tbname);
		if(!empty($station_list['allrow']))
		{
			foreach($station_list['allrow'] as &$station)
			{
				//获取城市信息
				$id = $station['id'];
				$where = array('trainid_like'=>'%,'.$id.',%');
				$field = '*';
				$tbname = 'rht_cityinfo';
				$city = $model->GetOne($where,$field,$tbname);
				if($city)
				{
					$station['city'] = $city;
				}
			}
		}
		self::inidate ( $station_list['allnum'], $page, $pagesize,count($station_list['allrow']));
		//var_dump($station_list);exit;
		$this->smarty->assign('station_list',$station_list['allrow']);
		$this->forward = 'citydetaillist';		
	}
	
	
	
	
	/**
	 * 城市简介添加
	 * Enter description here ...
	 */
	public function cityDetailAction()
	{
		$id = reqnum('id', '');
			
		$model = new Psys_TripModel();
		$where_city = array('trainid_like'=>"%,$id,%");
		$field_city = '*';
		$tbname_city = 'rht_cityinfo';
		$data_city = $model->GetOne($where_city,$field_city,$tbname_city);
		$where_station = array('id'=>$id);
		$field_station = 'id,cityname';
		$tbname_station = 'rht_trainstation';
		$data_station = $model->GetOne($where_station,$field_station,$tbname_station);
		$data_station['city'] = $data_city;
		
		$this->smarty->assign('data_station',$data_station);
		$this->forward = 'citydetail';	
	}
	
	/**
	 * 根据用户输入进行城市搜索
	 */
	public function citysearchAction()
	{
		$name = reqstr('name','');
		$where = array('name'=>$name);
		$field = '*';
		$tbname = 'rht_cityinfo';
		$model = new Psys_TripModel();
		$city = $model->GetOne($where,$field,$tbname);
		if($city)
		{
			return array('result'=>'SUCCESS','city'=>$city);
		}
		else 
		{
			return array('result'=>'ERROR');
		}
	}
	/**
	 * 城市信息更新
	 */
	public function citydetailupdateAction()
	{
		$ispost = reqnum('ispost',0);		
		if($ispost)
		{
			$city_id = reqnum('city_id',0);
			$station_id = reqnum('station_id',0);
			$name = reqstr('name','');
			$headpath = reqstr('headpath','');
			$imgs = reqstr('imgs','');
			$cityinfo = reqstr('cityinfo','');
			$besttime = reqstr('besttime','');
			$mapimg = reqstr('mapimg','');
			$remark = reqstr('remark','');
			$state = reqnum('state','');
			
			$model = new Psys_TripModel();
			$data = array('name'=>$name,'headpath'=>$headpath,'imgs'=>$imgs,'cityinfo'=>$cityinfo,'besttime'=>$besttime,'mapimg'=>$mapimg,'remark'=>$remark,'flag'=>$state);
			
			$result = array (
					'result' => 'SUCCESS' 
			);
			
			//为保证站点对应城市的唯一，保证trainid的唯一
			$where_del = array('trainid_like'=>"%,$station_id,%");
			$field_del = 'id,trainid';
			$tbname_del = 'rht_cityinfo';
			$data_city = $model->GetOne($where_del,$field_del,$tbname_del);
			if($data_city)
			{
				$model->UpdateOne(array('trainid'=>str_replace(",$station_id,", ',', $data_city['trainid'])),array('id'=>$data_city['id']),$tbname_del);
			}
					
			if($city_id == 0)
			{	//城市简介新增
				$data['trainid'] = ",$station_id,";
				$data['ctime'] = time();
				$insert_R = $model->AddOne($data,'rht_cityinfo');
				if(!$insert_R)
				{
					$result = array('result'=>'ERROR');
				}
			}
			else
			{	//如果城市id不为空，则添加stationid	
				$where = array('id'=>$city_id);
				$field = 'trainid';
				$tbname = 'rht_cityinfo';
				$trainid_arr = $model->GetOne($where,$field,$tbname);
				$data['trainid'] = $trainid_arr['trainid'] . "$station_id,";
				$update_R = $model->UpdateOne($data, $where,$tbname);
				if(!$update_R)
				{
					$result = array('result'=>'ERROR');
				}
			}			
		}
		return $result;
	}
	
	/**
	 * 旅游景点添加
	 * Enter description here ...
	 */
	public function spotaddAction()
	{		
		$this->forward = 'spotedit';
	}
	
	/**
	 * 站点---景点列表
	 */
	public function spotslistAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",20);
		$spot_name = reqstr("spot_name",'');
	
		$model = new Psys_TripModel();
		$where = array();
		if($spot_name)
		{
			$where['name'] = $spot_name;
			$this->smarty->assign('spot_name',$spot_name);
		}
		$orderby = '';
		$field = '*';
		$tbname = 'rht_tspot';
		$spot_list = $model->GetList($where,$orderby,$page,$pagesize,$field,$tbname);
		if(!empty($spot_list['allrow']))
		{
			foreach($spot_list['allrow'] as &$spot)
			{
				//获取城市信息
				$id = $spot['cityid'];
				
				$where_city = array('id'=>$id);
				$field_city = 'id,name';
				$tbname_city = 'rht_cityinfo';
				$city = $model->GetOne($where_city,$field_city,$tbname_city);
				
				if($city)
				{
					$spot['city'] = $city;
				}
			}
		}
		self::inidate ( $spot_list['allnum'], $page, $pagesize,count($spot_list['allrow']));
		$this->smarty->assign('spot_list',$spot_list['allrow']);
		$this->forward = 'spotslist';
	}
	
	/**
	 * 旅游景点添加/更新
	 */
	public function spotupdateAction()
	{
		$ispost = reqnum('ispost',0);		
		if($ispost)
		{
			$city_id = reqnum('city_id',0);
			$spot_id = reqnum('spot_id',0);
			$name = reqstr('name','');
			$headimg = reqstr('headimg','');
			$imgs = reqstr('imgs','');
			$star = reqstr('star',0);
			$label = str_replace(array('，',',','；','、'),';',reqstr('label',''));
			$adress = reqstr('adress','');
			$hot = reqstr('hot',0);
			$bustime = reqstr('bustime','');
			$ticket = reqnum('ticket',0);
			$remark = str_replace(array('，',',','；','、'),';',reqstr('remark',''));
			$flag = reqnum('flag',0);
			
			$model = new Psys_TripModel();
			$data = array(
				'cityid'=>$city_id,
				'name'=>$name,
				'headimg'=>$headimg,
				'imgs'=>$imgs,
				'star'=>$star,
				'label'=>$label,
				'adress'=>$adress,
				'hot'=>$hot,
				'bustime'=>$bustime,
				'ticket'=>$ticket,
				'remark'=>$remark,
				'flag'=>$flag
			);
			$result = array (
					'result' => 'SUCCESS' 
			);
					
			if($spot_id == 0)
			{	//城市简介新增
				$data['ctime'] = time();
				$insert_R = $model->AddOne($data,'rht_tspot');
				if(!$insert_R)
				{
					$result = array('result'=>'ERROR');
				}
			}
			else
			{	//如果城市id不为空，则添加stationid	
				$where = array('id'=>$spot_id);
				$tbname = 'rht_tspot';
				$update_R = $model->UpdateOne($data, $where,$tbname);
				if(!$update_R)
				{
					$result = array('result'=>'ERROR');
				}
			}			
		}
		return $result;
	}
	
	/**
	 * 城市景点编辑 
	 * 
	 */
	public function spoteditAction()
	{
		$id = reqnum('id', 0);
		if($id)
		{
			$where = array('id'=>$id);
			$field = '*';
			$tbname = 'rht_tspot';
			$model = new Psys_TripModel();
			$spot = $model->GetOne($where,$field,$tbname);
			$where_city = array('id'=>$spot['cityid']);
			$field_city = 'id,name';
			$tbname_city = 'rht_cityinfo';
			$city = $model->GetOne($where_city,$field_city,$tbname_city);
			$spot['cityname'] = $city['name'];
			$this->smarty->assign('spot',$spot);
			$this->forward = 'spotedit';		
		}		
	}
	
	/**
	 * 旅游景点状态切换
	 */
	public function spottoggleAction()
	{
		$id = reqnum('id',0);
		$result = array('result'=>'SUCCESS','msg'=>'');
		if($id)
		{
			$where = array('id'=>$id);
			$field = 'flag';
			$tbname = 'rht_tspot';
			$model = new Psys_TripModel();
			$spot = $model->GetOne($where,$field,$tbname);
			$update_data = array('flag'=>(int)!$spot['flag']);
			$update_R = $model->UpdateOne($update_data, $where,$tbname);
			if(!$update_R)
			{
				$result = array('result'=>'ERROR','msg'=>'状态修改失败');
			}
		}
		else
		{
			$result = array('result'=>'ERROR','msg'=>'系统出错');
		}
		return $result;
	}
	
	
	
	/**
	 * 美食添加
	 */
	public function foodaddAction()
	{
		$this->forward = 'foodedit';
	}
	
	/**
	 * 美食列表
	 */
	public function foodlistAction()
	{
		$model = new Psys_TripModel();
		$where = array();
		$food_name = reqstr('food_name','');
		if($food_name)
		{
			$where['name'] = $food_name;
			$this->smarty->assign('food_name',$food_name);
		}
		$orderby = '';
		$page = reqnum('page',1);
		$pagesize = reqnum('pagesize',20);
		$field = '*';
		$tbname = 'rht_tfood';		
		$food_list = $model->GetList($where,$orderby,$page,$pagesize,$field,$tbname);
		if(!empty($food_list['allrow']))
		{
			foreach($food_list['allrow'] as &$food)
			{
				$where_city = array('id'=>$food['cityid']);
				$field_city = 'id,name';
				$tbname_city = 'rht_cityinfo';
				$city = $model->GetOne($where_city,$field_city,$tbname_city);
				$food['cityname'] = $city['name'];
			}
		}
		//var_dump($food_list);
		self::inidate ( $food_list['allnum'], $page, $pagesize,count($food_list['allrow']));
		$this->smarty->assign('food_list',$food_list['allrow']);
		$this->forward = 'foodlist';
	}	
	
	/**
	 * 美食编辑
	 */
	public function foodeditAction()
	{
		$id = reqnum('id', '');
		if($id)
		{
			$where = array('id'=>$id);
			$field = '*';
			$tbname = 'rht_tfood';
			$model = new Psys_TripModel();
			$food = $model->GetOne($where,$field,$tbname);
			$where_city = array('id'=>$food['cityid']);
			$field_city = 'id,name';
			$tbname_city = 'rht_cityinfo';
			$city = $model->GetOne($where_city,$field_city,$tbname_city);
			$food['cityname'] = $city['name'];
			$this->smarty->assign('food',$food);
			$this->forward = 'foodedit';		
		}		
	}
	
	/**
	 * 美食信息更新
	 */
	public function foodupdateAction()
	{
		$ispost = reqnum('ispost',0);		
		if($ispost)
		{
			$city_id = reqnum('city_id',0);
			$food_id = reqnum('food_id',0);
			$name = reqstr('name','');
			$headimg = reqstr('headimg','');
			$imgs = reqstr('imgs','');
			$star = reqstr('star',0);
			$label = str_replace(array('，',',','；','、'),';',reqstr('label',''));
			$adress = reqstr('adress','');
			$hot = reqstr('hot',0);
			$bustime = reqstr('bustime','');
			$share = reqnum('share',0);
			$tel = reqstr('tel',0);
			$feature = str_replace(array('，',',','；','、'),';',reqstr('feature',''));
			$remark = reqstr('remark','');
			$flag = reqnum('flag',0);
			
			$model = new Psys_TripModel();
			$data = array(
				'cityid'=>$city_id,
				'name'=>$name,
				'headimg'=>$headimg,
				'imgs'=>$imgs,
				'star'=>$star,
				'label'=>$label,
				'adress'=>$adress,
				'hot'=>$hot,
				'bustime'=>$bustime,
				'share'=>$share,
				'tel'=>$tel,
				'feature'=>$feature,
				'remark'=>$remark,
				'flag'=>$flag
			);
			$result = array (
					'result' => 'SUCCESS' 
			);
					
			if(!$food_id)
			{	//城市简介新增
				$data['ctime'] = time();
				$insert_R = $model->AddOne($data,'rht_tfood');
				if(!$insert_R)
				{
					$result = array('result'=>'ERROR');
				}
			}
			else
			{	//如果城市id不为空，则添加stationid	
				$where = array('id'=>$food_id);
				$tbname = 'rht_tfood';
				$update_R = $model->UpdateOne($data, $where,$tbname);
				if(!$update_R)
				{
					$result = array('result'=>'ERROR');
				}
			}			
		}
		return $result;
	}
	
	/**
	 * 旅游景点状态切换
	 */
	public function foodtoggleAction()
	{
		$id = reqnum('id',0);
		$result = array('result'=>'SUCCESS','msg'=>'');
		if($id)
		{
			$where = array('id'=>$id);
			$field = 'flag';
			$tbname = 'rht_tfood';
			$model = new Psys_TripModel();
			$food = $model->GetOne($where,$field,$tbname);
			$update_data = array('flag'=>(int)!$food['flag']);
			$update_R = $model->UpdateOne($update_data, $where,$tbname);
			if(!$update_R)
			{
				$result = array('result'=>'ERROR','msg'=>'状态修改失败');
			}
		}
		else
		{
			$result = array('result'=>'ERROR','msg'=>'系统出错');
		}
		return $result;
	}
	
	
	/**
	 * 预览图片获取
	 */
	public function imgviewAction()
	{
		$id = reqnum('id', 0);
		$type = reqnum('type',0);
		if($id)
		{
			$where = array('id'=>$id);
			switch($type)
			{
				case 1:
					$field = "headpath,imgs,mapimg";
					$tbname = 'rht_cityinfo';
					$path = 'city';
					break;
				case 2:
					$field = 'headimg,imgs';
					$tbname = 'rht_tspot';
					$path = 'spot';
					break;
				case 3:
					$field = 'headimg,imgs';
					$tbname = 'rht_tfood';
					$path = 'food';
					break;
			}
			$model = new Psys_TripModel();
			$data = $model->GetOne($where,$field,$tbname);
			$return = array('result'=>'SUCCESS','msg'=>array());
			if($data)
			{
				$imgs = array();
				$key = 0;
				foreach($data as $value)
				{
					$values = explode(';', $value);
					foreach($values as $var)
					{
						$imgs[$key]['path'] = 'http://res.wonaonao.com/imgs/trip/' . $path . '/' . $var;
						$imgs[$key]['name'] = $var;
						$key++;
					}
				}
				$return['msg'] = $imgs;
			}
			else
			{
				$return['result'] = 'ERROR';
			}
		}
		return $return;
	}
	
	
	/**
	 * 图片上传
	 */
	public function uploadAction()
	{
		$files = array();
		foreach($_FILES['file'] as $k=>$v)
		{	
			foreach($v as $key=>$val)
			{
				$files[$key][$k] = $val;
			}
		}
		//上传错误提示
		$errorMsg = array(
			'0'=>'文件上传成功',
			'1'=>'文件超出了服务器配置大小',
			'2'=>'文件超出了表单配置大小',
			'3'=>'仅部分文件上传',
			'4'=>'没有找到上传文件,请选择文件',
			'5'=>'上传文件大小为零',
			'6'=>'未找到临时文件夹',
			'7'=>'临时文件夹写入失败',
			'8'=>'服务器文件上传扩展未开启',
			'9'=>'上传图片规格不符合要求',
			'10'=>'存放文件夹建立失败',
			'11'=>'上传文件移动失败'
		);
		switch($_POST['type'])
		{
			case 1:
				$dir = TRIPS_PATH . 'city/';
				break;
			case 2:
				$dir = TRIPS_PATH . 'spot/';
				break;
			case 3:
				$dir = TRIPS_PATH . 'food/';
				break;
			default:
				$dir = ADDS_PATH;
				break;
		}	
		
		//返回数据
		$arr = array();
		//json数据返回
		$returnJson = '';
		
		//上传详情
		$msg = '';
		//成功与否标志
		$flag = true;	
		foreach($files as $k=>$file)
		{
			//$img_info = getimagesize($file['tmp_name']);
			
			$arr['img_x'] = $img_info[0];
			$arr['img_y'] = $img_info[1];
			$arr['whe'] = $_POST['whe'];					
			if($file['error'] == 0)
			{
				if(!is_dir($dir))
				{
					if(!mkdir($dir,0777,true))
					{
						$flag = false;
						$msg = $msg ? $msg . ',' . $file['name'] . $errorMsg[10] : $file['name'] . $errorMsg[10];
					}
				}
				else 
				{
					$ext = pathinfo($file['name'],PATHINFO_EXTENSION);
					$filename = 'trip_' . date('YmdHis') . '_' . rand(10000,99999) . '.' . $ext;
					$savePath = $dir . $filename;
					$arr['img_name'] = isset($arr['img_name']) ? $arr['img_name'] . ';' .$filename : $filename;
					if(!move_uploaded_file($file['tmp_name'], $savePath))
					{
						$flag = false;
						$msg = $msg ? $msg . ',' . $file['name'] . $errorMsg[11] : $file['name'] . $errorMsg[11];			
					}
				}
			}
			else 
			{
				$flag = false;
				$msg = $msg ? $msg . ',' . $file['name'] . $errorMsg[$file['error']] : $file['name'] . $errorMsg[$file['error']];
				$arr['result'] = 'error';
				$arr['msg'] = $errorMsg[$file['error']];
			}
		}
		if($flag)
		{
			$arr['result'] = 'success';
			$arr['msg'] = $errorMsg[0];
		}
		else
		{
			$arr['error'] = 'error';
			$arr['msg'] = $msg;
		}
		//json返回
		$returnJson = json_encode($arr);
		die("<script type='text/javascript'>window.parent.callbackFunction('".$returnJson."');</script>");
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
	
	//车站列表
	public function stlistAction()
	{
		$model = new Psys_TripModel();
		$model->SetDb("db-rht_train");
		
		
		$keyword = reqstr('trainno','');
		if($keyword == '请输入车次！'){
			$keyword = '';
		}
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",40);		
		$where = '';
		if(!empty($keyword)){
			$keyword = strtoupper($keyword);
			$where = " sno = '$keyword'";
		}
		$list = $model -> GetList($where, 'id ASC',$page, $pagesize,"*","rht_station");		
		self::inidate($list['allnum'],$page,$pagesize,count($list['allrow']));
		
		$this->smarty->assign('list',$list['allrow']);
		$this->forward = "stlist";
	}
		
	//车站添加/修改
	public function stationeditAction()
	{
		$model = new Psys_TripModel();
		$model->SetDb("db-rht_train");
				
		$ispost = reqnum('ispost', 0);
		if($ispost){
			$id = reqnum('id',0);
			
			$sno = reqstr('sno');
			$fz = reqstr('fz');
			$dz = reqstr('dz');
			$stime = reqstr('stime');
			$dtime = reqstr('dtime');
			$dzxh = reqstr('dzxh');
			$isdz = reqstr('isdz');
			$flag = reqstr('flag');
			$appkey = reqstr('appkey');
			
			$data = array(
					'sno' => $sno,
					'fz' => $fz,
					'dz' => $dz,
					'stime' => $stime,
					'dtime' => $dtime,
					'dzxh' => $dzxh,
					'isdz' => $isdz,
					'flag' => $flag,
					'appkey' => $appkey,
			);
			if($id > 0){
				$where = array('id' => $id);			
				$re = $model->UpdateOne($data,$where,"rht_station");
				$msg = '修改';
			}else{
				$re = $model->AddOne($data,"rht_station");
				$msg = '添加';
			}
			if($re){
				echo '<script>alert("'.$msg.'成功,反回列表！");window.location.href="/trip/stlist?trainno='.$sno.'"</script>';
				exit;
			}
			
		}
		$id = reqnum('id',0);
		$station = array();
		if($id > 0){
			$where = array('id'=>$id);
			$station = $model->GetOne($where,"*","rht_station");
			$this->smarty->assign('action','edit');
		}else{
			$this->smarty->assign('action','add');
		}
		$this->smarty->assign('station',$station);
		$this->forward = "add";
		
	}
}

?>