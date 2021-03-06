<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月15日                                                 
* 作　  者:peter
* E-mail  :perter@rockhippo.net
* 文 件 名:adsController.php                                                
* 创建时间:下午2:29:03                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: adController.php 670 2014-07-11 06:59:23Z tony_ren $                                                 
* 修改日期:$LastChangedDate: 2014-07-11 14:59:23 +0800 (周五, 11 七月 2014) $                                     
* 最后版本:$LastChangedRevision: 670 $                                 
* 修 改 者:$LastChangedBy: tony_ren $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/controller/adsController.php $                                            
* 摘    要: 广告管理                                                      
*/
require_once COMMON_PATH."XThumb.php";
class adsController extends PSys_AbstractController {
	public function __construct() {
		parent::__construct ();
		$this->smarty->assign("gameActive","");
        $this->smarty->assign("trainActive","");
        $this->smarty->assign("gameHidden","hidden");
        $this->smarty->assign("trainHidden","hidden");
        $this->smarty->assign("busHidden","hidden");
        $this->smarty->assign("marketHidden","");
        $this->smarty->assign("marketActive","active");
        $this->smarty->assign("busActive","");
	}
	
	/**
	 * 广告列表
	 */
	public function indexAction() {
		$page = reqnum ( "page", 1 );
		$pagesize = reqnum ( "pagesize", 10 );
		$m = new PSys_AdsModel ();
		$list = $m->GetList ( '', 'id DESC', $page, $pagesize, "*" );
		foreach ( $list ['allrow'] as $key => &$var ) {
			
			switch ($var ['colid']) {
				case 0 :
					$var ['colid'] = '主页';
					break;
				case 1 :
					$var ['colid'] = '电影';
					break;
				case 2 :
					$var ['colid'] = '游戏';
					break;
				case 3 :
					$var ['colid'] = '音乐';
					break;
				default :
					$var ['colid'] = '主页';
			}
		}
		self::inidate ( $list ['allnum'], $page, $pagesize, count ( $list ['allrow'] ) );
		$this->smarty->assign ( 'list', $list ['allrow'] );
		$this->smarty->assign ( 'psys_base_url', PSYS_BASE_URL );
		$this->smarty->assign("active_menu","ads");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "index";
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
	 * 添加广告
	 */
	public function addAction() {
		$this->smarty->assign("active_menu","ads");
		$this->smarty->assign("active","ads/add");
		$this->forward = "edit";
	}
	/**
	 * 转到广告修改页面
	 */
	public function editAction() {
		$id = reqnum ( 'id', 0 ); // 获取参数
		if (! $id) {
			echo 'empty id';
			exit ();
		}
		$nt = new PSys_AdsModel ();
		$where = array (
				'id' => $id 
		);
		$info = $nt->GetOne ( $where, "*" );
		$info['validity'] = $info['validity'] ? date('Y-m-d',$info['validity']) : '';
		$adstypes = explode(',',$info['adstype']);
		$ads_pm = '0';
		$ads_an = '0';
		$ads_ios = '0';
		foreach($adstypes as $key=>$val){
			if($val=='0'){
				$ads_pm = '1';
			}
			if($val=='1'){
				$ads_an = '1';
			}
			if($val=='2'){
				$ads_ios = '1';
			}
		}
		$this->smarty->assign ('ads_pm',$ads_pm);
		$this->smarty->assign ('ads_an',$ads_an);
		$this->smarty->assign ('ads_ios',$ads_ios);
		$this->smarty->assign ( 'info', $info );
		$this->smarty->assign("active_menu","ads");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "edit";
	}
	/**
	 * 更新广告位信息
	 */
	public function updateAction() {
		$id = reqnum ( 'id', 0 );
		$ispost = reqnum ( 'ispost', 0 );
		$model = new PSys_AdsModel ();
		if ($ispost == 1) {
			//广告名
			$adname = reqstr ( 'adname' );
			//显示位置
			$colid = reqnum ( 'colid' );
			//显示系统
			$adstype = reqstr('adstype','');
			//是否车站
			$isstation = reqnum('isstation',0);
			//状态
			$flag = reqnum ( 'flag', 0 );
			//图片路径
			$imgurl = reqstr ('imgurl','');
			//访问路径
			$actionurl = reqstr ( 'actionurl','' );
			//排序
			$orderby = reqnum('orderby',0);
			//链接ID
			$tjappid = reqnum ( 'tjappid', 0 );
			//有效期
			$validity = strtotime(reqstr('validity',0));
			//广告描述
			$addesc = reqstr ( 'content','' );
			
			//根据ID获取app类型
			$appmodel = new PSys_ResModel();
			$where = array('id'=>$tjappid);
			$app_data = $appmodel->GetOneGame($where);
			$tjapptype = $app_data ? $app_data['apptype'] : 0;
			//添加时间
							
			$data = array (
					'adname' => $adname,
					'colid' => $colid,
					'adstype' => $adstype,
					'station' =>$isstation,
					'flag' => $flag,
					'imgurl' => $imgurl,
					'actionurl' => $actionurl,
					'orderby' => $orderby,
					'tjappid' => $tjappid,
					'validity'=>$validity,
					'addesc' => $addesc,
					'colstr' => $colstr,
					'ctime' => time (),		
					'tjapptype'=> $tjapptype	
			);
			$result = array (
					'result' => 'ERROR' 
			);
			if ($imgurl == '') {
				MsgInfoConst::GetMsg ( 1041, $result );
				return $result;
			}
			if ($id == 0) 			// 新增
			{
				$returnid = $model->AddOne ( $data );
				//$m = new PSys_ResModel ();
				//$m->Record($data,$returnid,'db-rht_sync','ads','rhs_downsync');
				// start 写操作日志
				$log = array (
						'logtype' => 72,
						'guid' => $_SESSION ['Cur_X_User'] ['id'],
						'ctime' => time (),
						'cip' => real_ip () 
				);
				$log ['logdetail'] = $_SESSION ['Cur_X_User'] ['username'] . "于" . date ( "Y-m-d H:i:s" ) . "[添加]广告位" . $adname;
				//$model->admin_syslog ( $log );
				// end 日志
				$result ['result'] = 'SUCCESS';
			} else 			// 更新
			{
				
				$w = array (
						'id' => $id 
				);
				$returnid = $model->UpdateOne ( $data, $w );
				//$m = new PSys_ResModel ();
				//$m->Record($data,$returnid,'db-rht_sync','ads','rhs_downsync');
				// start 写操作日志
				$log = array (
						'logtype' => 72,
						'guid' => $_SESSION ['Cur_X_User'] ['id'],
						'ctime' => time (),
						'cip' => real_ip () 
				);
				$log ['logdetail'] = $_SESSION ['Cur_X_User'] ['username'] . "于" . date ( "Y-m-d H:i:s" ) . "[编辑]广告位" . $adname;
				//$model->admin_syslog ( $log );
				// end 日志
				$result ['result'] = 'SUCCESS';
			}
			if($result['result'] == 'SUCCESS')
			{
				$file = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/temp.txt';
				$fp = fopen($file, 'w');
				fwrite($fp, '1');
				fclose($fp);			
			}
			return $result;
		}
	}
	
	/**
	 * 删除广告
	 */
	public function deleteAction() {
		$m = new PSys_AdsModel ();
		if (is_array ( $_GET ['id'] )) {
			$id = reqarray ( 'id', array () );
			foreach ( $id as $v ) {
				$where = array (
						'id' => $v 
				);
				$data = $m->GetOne ( $where );
				$res = $m->DeleteOne ( $where );
				// start 写操作日志
				$log = array (
						'logtype' => 72,
						'guid' => $_SESSION ['Cur_X_User'] ['id'],
						'ctime' => time (),
						'cip' => real_ip () 
				);
				$log ['logdetail'] = $_SESSION ['Cur_X_User'] ['username'] . "于" . date ( "Y-m-d H:i:s" ) . "[删除]广告位" . $data ['adname'];
				// $m=new PSys_SyslogModel();
				// $m->admin_syslog($log);
				//$m->admin_syslog ( $log );
				// end 日志
			}
			echo "<script>alert('删除成功');window.location.href='/ads/index'</script>";
		} elseif (is_numeric ( $_GET ['id'] )) {
			$id = reqnum ( 'id', '0' );
			$where = array (
					'id' => $id 
			);
			$data = $m->GetOne ( $where );
			$res = $m->DeleteOne ( $where );
			// start 写操作日志
			$log = array (
					'logtype' => 72,
					'guid' => $_SESSION ['Cur_X_User'] ['id'],
					'ctime' => time (),
					'cip' => real_ip () 
			);
			$log ['logdetail'] = $_SESSION ['Cur_X_User'] ['username'] . "于" . date ( "Y-m-d H:i:s" ) . "[删除]广告位" . $data ['adname'];
			//$m->admin_syslog ( $log );
			// end 日志
			echo "<script>alert('删除成功');window.location.href='/ads/index'</script>";
		} else {
			echo "<script>alert('id 为空');window.location.href='/ads/index'</script>";
		}
	}
	/**
	 * 广告图片上传
	 */
	function uploadaddsAction(){
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
		$dir = ADDS_PATH;
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
			$img_info = getimagesize($file['tmp_name']);
			
			$arr['img_x'] = $img_info[0];
			$arr['img_y'] = $img_info[1];
			$arr['img_name'] = $file['name'];
					
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
					$savePath = $dir . $file['name'];
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
	
	
	public function imgcutAction()
	{
		$dir = ADDS_PATH;
		$filename = reqstr('file','');
		$type = reqstr('type','');
		$filePath = $dir . $filename;
		//获取原始文件后缀
		$file_ext = pathinfo($filePath,PATHINFO_EXTENSION);
		//获取原始文件名
		$file_name = pathinfo($filePath,PATHINFO_FILENAME);
		if($file_ext == 'jpg')
		{
			$file_ext = 'jpeg';
		}
		//创建画布函数重组
		$d_func = 'imagecreatefrom' . $file_ext;
		$res = $d_func($filePath);
		
		$dst_x = reqnum('x',0);
		$dst_y = reqnum('y', 0);
		$dst_w = reqnum('w',0);
		$dst_h = reqnum('h',0);		
		$dst = imagecreatetruecolor($dst_w, $dst_h);
		
		imagecopyresampled($dst,$res,0,0,$dst_x,$dst_y,$dst_w,$dst_h,$dst_w,$dst_h);
		
		//图片保存函数重组
		$s_func = 'image' . $file_ext;
		$flag = true;
		if($file_ext == 'jpeg')
		{
			$flag = $s_func($dst,$filePath,100);
		}
		else
		{
			$flag = $s_func($dst,$filePath);
		}
		
		if($flag)
		{
			//图片缩略处理
			$XImg = new XThumb();
			if($type)
			{
				switch($type)
				{
					case 1:
						$XImg->imgZoom($filePath, 234, 168, $dir.$file_name.'-android-1'.$file_ext);
						$XImg->imgZoom($filePath, 264, 197, $dir.$file_name.'-android-2'.$file_ext);
						$XImg->imgZoom($filePath, 352, 260, $dir.$file_name.'-android-3'.$file_ext);
						$XImg->imgZoom($filePath, 528, 390, $dir.$file_name.'-android-4'.$file_ext);
						break;
					case 2:
						$XImg->imgZoom($filePath, 471, 390, $dir.$file_name.'-ios-1'.$file_ext);
						break;
					case 3:
						$XImg->imgZoom($filePath, 471, 258, $dir.$file_name.'-ios-2'.$file_ext);
						break;
					case 4:
						$XImg->imgZoom($filePath, 480, 252, $dir.$file_name.'-android-1'.$file_ext);
						$XImg->imgZoom($filePath, 540, 288, $dir.$file_name.'-android-2'.$file_ext);
						$XImg->imgZoom($filePath, 720, 380, $dir.$file_name.'-android-3'.$file_ext);
						$XImg->imgZoom($filePath, 1080, 570, $dir.$file_name.'-android-4'.$file_ext);
						break;
					case 5:
						$XImg->imgZoom($filePath, 960, 576, $dir.$file_name.'-ios'.$file_ext);
						break;
				}
			}
			echo "<script type='text/javascript'>window.parent.cutAfter('".$filename."');</script>";
			//echo "<script type='text/javascript'>window.parent.cutAfter('1.png');</script>";
		}
		imagedestroy($dst);
		imagedestroy($res);
	}
	
	/**
	 * 
	 * 广告实时同步
	 */
	public function syncAction()
	{
		$configPath = dirname(dirname(dirname(dirname(__FILE__)))).'/configs/server_config.php';
		require $configPath;
		$this->smarty->assign('lists',$SERVER_LIST);
		$this->forward = 'sync';
	}
	
	/**
	 * 广告数据同步
	 */
	public function synclAction()
	{
		//从配置文件中获取允许同步IP
		$ip = array("171.221.199.56","112.193.220.232");
		//获取当前IP
		$curIp = real_ip();
		if(in_array($curIp,$ip))
		{
			$file = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/temp.txt';
			$str = file_get_contents($file);
			//0同步1不同步
			if(!$str)
			{
				//同步rhi_idc-rht_train
				//229 rhi_idc==>rht_train
				$this->idcToTrain();
				//各服务器同步
				$host = reqstr('host','');
				//$host = 'm.wonaonao.com';
				$url = $host . '/news_and_ads.php?act=adsdb';
				$data = array();
				$return = http_post_array($url,$data);
				if($return['msgcode'] == 200)
				{
					return $return['msg'];
				}
				else
				{
					return array('result'=>'ERROR','msg'=>'请求失败,错误代码：' . $return['msgcode']);
				}
			}
			else
			{
				return array('result'=>'ERROR','msg'=>'资源未同步完成，请5分钟后再进行同步数据库！');
			}
		}
		else
		{
			return array('result'=>'ERROR','msg'=>'不可允许IP同步！');
		}
	}
	
	/**
	 * 229服务器IDC数据库到train数据库同步
	 * Enter description here ...
	 */
	private function idcToTrain()
	{
		global $G_X;
		$model = new PSys_AdsModel();
		$servicer = $G_X['appkey'];
		$data = $model->getSyncList($servicer);
		foreach($data as $news)
		{
			unset($news['appkeys']);
			$where = array('id'=>$news['id']);
			$field = 'id';
			$one = $model->GetOneAds($where,$field);
			if($one)
			{
				$updateR = $model->UpdateOneAds($news, $where);
				if($updateR !== false)
				{
					$field = "appkeys";
					$result = $model->GetOne($where,$field);
					$server = $result['appkeys'];
					$server = $server ? $server . ',' . $servicer : $servicer;
					$updateData = array('appkeys'=>$server);
					$model->UpdateOne($updateData, $where);
				}
			}
			else
			{
				$insertR = $model->AddOneAds($news);
				if($insertR !== false)
				{
					$field = "appkeys";
					$result = $model->GetOne($where,$field);
					$server = $result['appkeys'];
					$server = $server ? $server . ',' . $servicer : $servicer;
					$updateData = array('appkeys'=>$server);
					$model->UpdateOne($updateData, $where);
				}
			}
		}
	}
	
	/**
	 * 全屏广告管理  */
	public function fulladsindexAction(){
		$page = reqnum ( "page", 1 );
		$pagesize = reqnum ( "pagesize", 10 );
		$m = new PSys_AdsModel ();
		$list = $m->GetList ( ' (find_in_set(5,colid) or find_in_set(6,colid))', 'id DESC', $page, $pagesize, "id,adname,subpage,imgurl,actionurl,colid,flag,adsname" );
	
		foreach ( $list ['allrow'] as $key => &$var ) {
			$var['adsone'] = 0;
			$var['adstwo'] = 0;
			if($var['colid']){
				$colids = explode(',', $var['colid']);
				if(in_array(5, $colids)){
					$var['adsone'] = 1;
				}
				if(in_array(6, $colids)){
					$var['adstwo'] = 1;
				}
			}
		}
		self::inidate ( $list ['allnum'], $page, $pagesize, count ( $list ['allrow'] ) );
		
		$this->smarty->assign ( 'list', $list ['allrow'] );
		$this->smarty->assign ( 'psys_base_url', PSYS_BASE_URL );
		$this->smarty->assign("active_menu","ads");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "fulladsindex";
	}
	
	/**
	 * 添加全屏广告
	 */
	public function fulladsaddAction() {
		$this->smarty->assign("active_menu","ads");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "fulladsadd";
	}
	
	/**
	 * 上传四张全屏广告图片  */
	function uploadfulladsAction(){
		$num = reqnum ( "num", 0 );
		$files = array();
		foreach($_FILES['file'] as $k=>$v)
		{
			foreach($v as $key=>$val)
			{
				$files[$key][$k] = $val;
			}
		}
		//图片长宽，大小限制
		$file_x = '640';
		$file_y = '284';
		$file_maxsize = '128000';
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
				'11'=>'上传文件移动失败',
				'12'=>'文件格式错误'
		);
		$dir = ADDS_PATH;
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
			//文件后缀名
			$fileType = strtoupper(substr(strrchr($file['name'], '.'), 1)); 
			$img_info = getimagesize($file['tmp_name']);
			//上传文件名	
			$name = 'ads'.time().'.'.$fileType;
			
			$arr['img_name'] = $name;
			$arr['img_x'] = $img_info[0];
			$arr['img_y'] = $img_info[1];
			if($file['error'] == 0)
			{
				if(!is_dir($dir))
				{
					if(!mkdir($dir,0777,true))
					{
						$flag = false;
						$msg = $msg ? $msg . ',' . $file['name'] . $errorMsg[10] : $file['name'] . $errorMsg[10];
					}
				}else{
					if($fileType!='JPEG' && $fileType!='TIFF' && $fileType!='BMP'&& $fileType!='GIF' && $fileType!='PNG' &&$fileType!='JPG'){
						$flag = false;
						$msg = $msg ? $msg . ',' . $file['name'] . $errorMsg[11] : $file['name'] . $errorMsg[11];
					}else{
						if($arr['img_x']>='641' || $arr['img_y']>='285'){
							$flag = false;
							$msg = $msg ? $msg . ',' . $file['name'] . $errorMsg[9] : $file['name'] . $errorMsg[9];
						}else{
						$savePath = $dir . $name;
							if(!move_uploaded_file($file['tmp_name'], $savePath))
							{
								$flag = false;
								$msg = $msg ? $msg . ',' . $file['name'] . $errorMsg[11] : $file['name'] . $errorMsg[11];
							}
						}				
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
			$arr['msg'] = $errorMsg[$file['error']];
			$arr['num'] = $num;
		}else
		{
			$arr['result'] = 'error';
			$arr['msg'] = $msg;
			$arr['num'] = $num;
		}
		//json返回
		$returnJson = json_encode($arr);
		die("<script type='text/javascript'>window.parent.callbackFunction('".$returnJson."');</script>");
	}
	
	/**
	 * 更新广告位信息
	 */
	public function updatefulladsAction() {
		$id = reqnum ( 'id', 0 );
		$ispost = reqnum ( 'ispost', 0 );
		$model = new PSys_AdsModel ();
		if ($ispost == 1) {
			//广告名
			$adname = reqstr ( 'adname' );
			//缩写名
			$adsname = reqstr ( 'adsname' );
			//显示位置
			$colid = reqstr ( 'colid' );
			//图片路径
			$imgurl = reqstr ('imgurl','');
			//访问路径
			$actionurl = reqstr ( 'actionurl');
			
			$subpage = reqnum('subpage');
			$flag = reqnum('flag');
			$data = array (
					'adname' => $adname,
					'adsname' => $adsname,
					'colid' => $colid,
					'flag' => $flag,
					'imgurl' => $imgurl,
					'actionurl' => $actionurl,
					'subpage' => $subpage,
					'ctime' => time (),
					'station' => '1'
			);
			$result = array (
					'result' => 'ERROR'
			);
			if ($imgurl == '') {
				MsgInfoConst::GetMsg ( 1041, $result );
				return $result;
			}
				
			if ($id == 0) 			// 新增
			{
				$nt = new PSys_AdsModel ();
				$where = array (
				'adname' => $adname
				);
				$info = $nt->GetOne ( $where, "id,adname,subpage,imgurl,actionurl,colid,flag,adsname" );
				if($info){
					$result = array(
						'result'=>'ADNAME'
					);
					return $result;
				}
				$returnid = $model->AddOne ( $data );
				// start 写操作日志
				$log = array (
						'logtype' => 72,
						'guid' => $_SESSION ['Cur_X_User'] ['id'],
						'ctime' => time (),
						'cip' => real_ip ()
				);
				$log ['logdetail'] = $_SESSION ['Cur_X_User'] ['username'] . "于" . date ( "Y-m-d H:i:s" ) . "[添加]广告位" . $adname;
				//$model->admin_syslog ( $log );
				// end 日志
				$result ['result'] = 'SUCCESS';
			} else 			// 更新
			{
	
				$w = array (
						'id' => $id
				);
				$returnid = $model->UpdateOne ( $data, $w );
				// start 写操作日志
				$log = array (
						'logtype' => 72,
						'guid' => $_SESSION ['Cur_X_User'] ['id'],
						'ctime' => time (),
						'cip' => real_ip ()
				);
				$log ['logdetail'] = $_SESSION ['Cur_X_User'] ['username'] . "于" . date ( "Y-m-d H:i:s" ) . "[编辑]广告位" . $adname;
				//$model->admin_syslog ( $log );
				// end 日志
				$result ['result'] = 'SUCCESS';
			}
			if($result['result'] == 'SUCCESS')
			{
				$file = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/temp.txt';
				$fp = fopen($file, 'w');
				fwrite($fp, '1');
				fclose($fp);
			}
			return $result;
		}
	}
	
	/**
	 * 转到广告修改页面
	 */
	public function fulladseditAction() {
		$id = reqnum ( 'id', 0 ); // 获取参数
		if (! $id) {
			echo 'empty id';
			exit ();
		}
		$nt = new PSys_AdsModel ();
		$where = array (
				'id' => $id
		);
		$info = $nt->GetOne ( $where, "id,adname,subpage,imgurl,actionurl,colid,flag,adsname" );
		if($info){
			$info['adsone'] = 0;
			$info['adstwo'] = 1;
			$colids = explode(',', $info['colid']);
			if(in_array(5,$colids)){
				$info['adsone'] = 1;
			}else{
				$info['adsone'] = 0;
			}
			if(in_array(6,$colids)){
				$info['adstwo'] = 1;
			}else{
				$info['adstwo'] = 0;
			}
		}
		$urlArray = explode(",", $info['imgurl']);
		$info['imgurl_1'] = $urlArray[0];
		$info['imgurl_2'] = $urlArray[1];
		$info['imgurl_3'] = $urlArray[2];
		$info['imgurl_4'] = $urlArray[3];
		$this->smarty->assign ( 'info', $info);
		$this->smarty->assign("active_menu","ads");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "fulladsadd";
	}
	
	
	/**
	 * 切换广告状态
	 * @return multitype:string  */
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
			$w = array (
					'id' => $id
			);
			$model = new PSys_AdsModel ();
			$returnid = $model->UpdateOne ( array('flag'=>$flag), $w );
			if ($returnid)
			{
				$result ['result'] = 'SUCCESS';
			}
			return $result;
		}
	}
	
	/**
	 * 删除广告
	 */
	public function deletefulladsAction() {
		$ispost = reqnum('ispost', 0);
		if ($ispost == 1) {
			$id = reqnum('id',0);
			$result = array (
					'result' => 'ERROR'
			);
			if(!$id){
				return $result;
			}
			$w = array (
					'id' => $id
			);
			//判断是否广告正在被车站使用
			$m = new PSys_AdsRule();
			$stationads = $m->getonecity($where,'adsone');
			foreach($stationads as $key=>$val){
				$match = '/'.$id.'/';
				if(preg_match($match, $val['adsone'])){
					$result = array (
					'result' => 'ERROR2',
					);
					return $result;
				}
				if(preg_match($match, $val['adstwo'])){
					$result = array (
					'result' => 'ERROR2',
					);
					return $result;
				}
			}
			$model = new PSys_AdsModel ();
			$data = $model->GetOne ( $where );
			$res = $model->DeleteOne ( $w );
			if ($res)
			{
				$log = array (
						'logtype' => 72,
						'guid' => $_SESSION ['Cur_X_User'] ['id'],
						'ctime' => time (),
						'cip' => real_ip ()
				);
				$log ['logdetail'] = $_SESSION ['Cur_X_User'] ['username'] . "于" . date ( "Y-m-d H:i:s" ) . "[删除]广告位" . $data ['adname'];
				// $m=new PSys_SyslogModel();
				// $m->admin_syslog($log);
				//$model->admin_syslog ( $log );
				$result ['result'] = 'SUCCESS';
			}
			return $result;
		}
	}
	//广告分配
	public function adsallotAction(){
		$m = new PSys_AdsModel ();
		$addOnelist = $m->GetList ( ' (find_in_set(5,colid))', 'id DESC', $page, $pagesize, "id,adname" );
		$addTwolist = $m->GetList ( ' (find_in_set(6,colid))', 'id DESC', $page, $pagesize, "id,adname" );
		$c = new PSys_AdsRule();
		$citys = $c->getcitys();
 		$this->smarty->assign ( 'addOnelist', $addOnelist[allrow]);
 		$this->smarty->assign ( 'addTwolist', $addTwolist[allrow]);
 		$this->smarty->assign ( 'citys' , $citys[allrow]);
 		$this->smarty->assign("active_menu","ads");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "adsallot";
	}
	
	//广告分配处理
	public function updateadsAction(){
		$adsid = reqnum ( 'adsid', 0 );
		$adsType = reqnum ( 'adsType', 0 );
		$cityid = $_POST['cityid'];
		$ispost = reqnum ( 'ispost', 0 );
		if($ispost==0){
			$this->forward = "adsallot";
			return;
		}
		$m = new PSys_AdsRule();
		if($adsType=='5'){
			//删除所有车站这个广告
			$where = array();
			$stationads = $m->getonecity('adsone');
			foreach($stationads as $key=>$val){
				$stationOne = $val['adsone'];
				$naArr = explode(',', $stationOne);
				for($i=0;$i<count($naArr);$i++){
					if($naArr[$i]==$adsid){
						unset($naArr[$i]);
					}
				}
				$naArr = implode(',', $naArr);
				$data = array('adsone'=>$naArr);
				$whereCid = array('id'=>$val['id']);
				$m->UpdateCityOne($data, $whereCid);
			}
			//添加所选车站所选广告
			foreach($cityid as $key=>$val){
				$adone = '';
				$where = array('id'=>$val);
				$city = $m->getonecity('*');
				$cityone = $city[0]['adsone'];
				$nameArr = explode(',', $cityone);
				for($i=0;$i<count($nameArr);$i++){
					if($nameArr[$i]==$adsid){
						$adone = 'have';
					}
				}
				if($adone==''){
					array_push($nameArr,$adsid);
					if($nameArr[0]==''){
						unset($nameArr[0]);
					}
					$cityone = implode(',', $nameArr);
					$data = array('adsone'=>$cityone);
					$m->UpdateCityOne($data, $where);
				}

			}
		}
		if($adsType=='6'){
			//删除所有车站这个广告
			$where = array();
			$stationads = $m->getonecity($where,'adstwo');
			foreach($stationads as $key=>$val){
				$stationTwo = $val['adstwo'];
				$naArr = explode(',', $stationTwo);
				for($i=0;$i<count($naArr);$i++){
					if($naArr[$i]==$adsid){
						unset($naArr[$i]);
					}
				}
				$naArr = implode(',', $naArr);
				$data = array('adsTwo'=>$naArr);
				$whereCid = array('id'=>$val['id']);
				$m->UpdateCityOne($data, $whereCid);
			}
			
			//添加所选车站所选广告
			foreach($cityid as $key=>$val){
				$adtwo = '';
				$where = array('id'=>$val);
				$city = $m->getonecity($where,'*');
				$citytwo = $city[0]['adstwo'];
				$nameArr = explode(',', $citytwo);
				for($i=0;$i<count($nameArr);$i++){
					if($nameArr[$i]==$adsid){
						$adtwo = 'have';
					}
				}
				if($adtwo==''){
					array_push($nameArr,$adsid);
					if($nameArr[0]==''){
						unset($nameArr[0]);
					}
					$cityone = implode(',', $nameArr);
					$data = array('adstwo'=>$cityone);
					$m->UpdateCityOne($data, $where);
				}
			}
		}
		echo "<script>window.location.href='/fullads/index';alert('修改成功');</script>";
	}
	
	//广告选择城市状态
	public function changecityAction(){
		$adsid = $_GET['adsid'];
		$adstype = $_GET['adsType'];
		$m = new PSys_AdsRule();
		$citys = $m->getcitys();
		$citysId = array();
		foreach($citys[allrow] as $c){
			if($adstype=='5'){
				$m->SetDb('db-rht_idc');
				$m->SetTable("rhi_stationads");
				if($adstype=='5'){
					$sql = 'SELECT id FROM rhi_stationads WHERE id='.$c['id'].' and adsone LIKE "%'.$adsid.'%"';
					$data = $m->Query($sql);
					if($data){
						array_push($citysId, $data[0]['id']);
					}
				}
			}
			if($adstype=='6'){
				$m->SetDb('db-rht_idc');
				$m->SetTable("rhi_stationads");
				if($adstype=='6'){
					$sql = 'SELECT id FROM rhi_stationads WHERE id='.$c['id'].' and adstwo LIKE "%'.$adsid.'%"';
					$data = $m->Query($sql);
					if($data){
						array_push($citysId, $data[0]['id']);
					}
				}
			}
		}
		$citysId = implode(',', $citysId);
		print_r($citysId);
	}
}
?>