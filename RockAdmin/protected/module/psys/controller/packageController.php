<?php
/**
 * Copyright(c) 2015
 * 日    期:2015年6月26日
 * 作　  者:Daniel
 * E-mail  :Daniel@rockhippo.net
 * 文 件 名:adController.php
 * 创建时间:下午16:58:03
 * 字符编码:UTF-8
 * 脚本语言:PHP
 * 版本信息:$Id: packageController.php 670 2015-06-26 06:59:23Z tony_ren $
 * 修改日期:$LastChangedDate: 2015-06-26 14:59:23 +0800 (周五, 26 六月 2015) $
 * 最后版本:$LastChangedRevision: 670 $
 * 修 改 者:$LastChangedBy: tony_ren $
 * 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/controller/packageController.php $
 * 摘    要: 广告管理
 */

class packageController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * 礼包列表
	 * @see AbstractController::indexAction()
	 */
	public function indexAction() {
		$keyword = reqstr('keyword','');
		if($keyword=='请输入关建词！') $keyword = '';
		
		//$indate = reqstr('indate','');
		//$todate = reqstr('todate','');


		$where = array('keyword'=>$keyword/*,'indate'=>$indate,'todate'=>$todate*/);

		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",20);
		$obj = new Psys_PackageModel();
		$data = $obj -> GetPackageList($where, 'id DESC',$page, $pagesize,"*");
		self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));

		$this->smarty->assign('select',$where);
		$this->smarty->assign('list',$data['allrow']);
		$this->forward = "index";
	}
	/**
	 * 添加礼包
	 */
	public function addAction(){
		$sub = reqstr('btnSave', '');
		$cid = reqnum('cid', 0);
		$am = new Psys_PackageModel();
		if($sub){
			$appid = reqnum('appid', 0);
			$name = reqstr('name', '');
			$appname = reqstr('appname', '');
			$imgurl = reqstr('imgurl', '');
			$stars = reqstr('stars', '');

			$total = reqstr('total', '');
			$sortid = reqnum('sortid', 0);
			$isfree = reqstr('isfree', '');
			$flag = reqstr('flag', '');
			$details = reqstr('details', '');

			$content = reqstr('remark', '');

			$content = str_replace("<br />", "", $content);
			$content = preg_replace("/<(a.*?)>(.*?)<(\/a.*?)>/si","$2",$content); //过滤a标签
			//$content = preg_replace("(<span[^>]*>(.+?)</span>)","<p>$1</p>",$content);
			//$pattern = '/.*<p(.*?)>[.\r\n\s]*?<img [.\r\n\s]*/ie';
			//$content = preg_replace($pattern,'str_replace("$1","","$0")',$content);

			$pattern = '/.*(<p.*?>)[\r\n\s]*?(<img.*?\/>)/i';
			//$content = preg_replace($pattern,"<p>$2</p>$1",$content);
			$content = preg_replace($pattern,"<p>$2</p><p style='text-indent:2em;'>",$content);
			//替换空Ｐ
			$pattern = '/<p[^>]*?>[\s\r\n]*?<\/p>/i';
			$content = preg_replace($pattern,"",$content);
			$ctime = time();

			if(name == ''/* or $content == ''*/ or $ctime == ''){
				echo '<script>alert("内容不全不能提交");window.history.go(-1);"</script>';
				exit;
			}
			$data = array(
				'appid'=>$appid,
				'appname'=>$appname,
				'name'=>$name,
				'imgurl'=>$imgurl,
				'stars'=>$stars,
				'details'=>$details,
				'remark'=>$content,
				'total'=>$total,
				'isfree'=>$isfree,
				'sortid'=>$sortid,
				'ctime'=>$ctime,
				'flag'=>$flag
			);

			$inid = $am->AddOne($data,'rhi_package');
			if($inid){
				$am->UpdateIsPack($appid);
				echo '<script>alert("添加成功！");window.location.href="/package/index"</script>';
				exit;
			}else{
				echo '<script>alert("添加失败！");</script>';
			}
		}
		
		$this->smarty->assign('action','add');
		$this->smarty->assign('datetime',date('Y-m-d H:i:s',time()));
		$this -> forward = 'add';
	}
	//获取APP列表
	public function getApplistAction(){
		$loupan_key = reqstr('loupan_key','');
	//	if(!empty($loupan_key)){
			$am = new Psys_PackageModel();
			$apps = $am->GetApplist($loupan_key);
			echo json_encode($apps['allrow']);
		/*}else{
			echo json_encode(array());
		}*/
		exit;
	}
	/**
	 * 修改礼包
	 */
	public function editAction(){
		$sub = reqstr('btnSave', '');

		if($sub){
			$id = reqnum('id', 0);
			if($id > 0){
				$data = array();
				$appid = reqnum('appid', 0);
				$name = reqstr('name', '');
				$appname = reqstr('appname', '');
				$imgurl = reqstr('imgurl', '');
				$stars = reqstr('stars', '');

				$total = reqstr('total', '');
				$sortid = reqnum('sortid', 0);
				$isfree = reqstr('isfree', '');
				$flag = reqstr('flag', '');
				$details = reqstr('details', '');

				$content = reqstr('remark', '');

				$content = str_replace("<br />", "", $content);
				$content = preg_replace("/<(a.*?)>(.*?)<(\/a.*?)>/si","$2",$content); //过滤a标签
				//$content = preg_replace("(<span[^>]*>(.+?)</span>)","<p>$1</p>",$content);
				//$pattern = '/.*<p(.*?)>[.\r\n\s]*?<img [.\r\n\s]*/ie';
				//$content = preg_replace($pattern,'str_replace("$1","","$0")',$content);
					
				$pattern = '/.*(<p.*?>)[\r\n\s]*?(<img.*?\/>)/i';
				//$content = preg_replace($pattern,"<p>$2</p>$1",$content);
				$content = preg_replace($pattern,"<p>$2</p><p style='text-indent:2em;'>",$content);
				//替换空Ｐ
				$pattern = '/<p[^>]*?>[\s\r\n]*?<\/p>/i';
				$content = preg_replace($pattern,"",$content);
				$ctime = time();

				if(name == '' /*or $content == ''*/ or $ctime == ''){
					echo '<script>alert("内容不全不能提交");window.location.href="/package/add"</script>';
					exit;
				}
				$data = array(
					'appid'=>$appid,
					'name'=>$name,
					'appname'=>$appname,
					'imgurl'=>$imgurl,
					'stars'=>$stars,
					'details'=>$details,
					'remark'=>$content,
					'total'=>$total,
					'isfree'=>$isfree,
					'sortid'=>$sortid,
					'ctime'=>$ctime,
					'flag'=>$flag
				);

				$am = new Psys_PackageModel();

				$inid = $am->editPackage($id, $data);

				if($inid){
					$am->UpdateIsPack($appid);
					echo '<script type="text/javascript">alert("修改成功！");window.location.href="/package/index"</script>';
					exit;
				}else{
					echo '<script type="text/javascript">alert("修改失败！");window.location.href="/package/edit/'.$id.'"</script>';
				}
			}
		}
		$id = reqnum('id', 0);

		if($id > 0){
			$am = new Psys_PackageModel();
			$pack = $am->GetPackageInfo($id);
			$apps = $am->GetApplist();
			foreach ($apps['allrow'] as $v){
				if($pack['appid'] == $v['appid']){
					$pack['appname'] = $v['appname'];
					break;
				}
			}
			$this->smarty->assign('apps',$apps['allrow']);
			$this->smarty->assign('pack',$pack);
		}
		$datetime = !empty($pack['ctime']) ? $pack['ctime'] : time();
		$this->smarty->assign('datetime',date('Y-m-d H:i:s',$datetime));
		$this->smarty->assign('action','edit');
		$this -> forward = 'add';
	}

	/**
	 * 删除新闻
	 */
	public function delAction(){
		$am = new Psys_PackageModel();
		$id = reqnum('id', 0);
		if($id > 0){
			$where = array();
			$where['id'] = $id;
			$pk_arr = array_filter($where);
			$inid = $am->DeleteOne($pk_arr);
			if($inid){
				echo '<script>alert("删除成功！");window.location.href="/news/index"</script>';
				exit;
			}
		}
		echo '<script>alert("删除失败！");</script>';
	}
	//修改新闻状态
	public function isflgAction(){
		$am = new Psys_PackageModel();
		$id = reqnum('id', 0);
		$isflg = reqnum('isflg', 0);
		if($id > 0){
			$isflg = ($isflg == 1)? 0 : 1 ;
			$data = array('flag'=>$isflg);
			$where = array();
			$where['id'] = $id;
			$where = array_filter($where);
			$inid = $am->UpdateOne($data, $where);
			if($inid){
				return array('res'=>1,);
			}
		}
		return array('res'=>0);
	}
	/**
	 * 添加激活码
	 */
	public function valueaddAction() {
		$sub = reqstr('btnSave', '');
		if($sub){
			$obj = new Psys_PackageModel();
			$data = array
			(
			    'packid' => reqnum('packid', 0),
			    'value' => reqstr('pavalue',''),
			    'flag' => reqnum('flag', 1)			    
			);
			$inid = $obj->AddOne($data,'rhi_packvalue');
			if($inid){
				echo '<script>alert("添加成功！");window.location.href="/package/valuelist"</script>';
				exit;
			}else{
				echo '<script>alert("添加失败！");</script>';
			}
		}
		$this->smarty->assign('action','valueadd');
		$this->forward = 'valueadd';
	}
	/**
	 * 激活码列表
	 */
	public function valuelistAction() {
		$pavalue = reqstr('pavalue','');
		if($pavalue == '请输入礼包激活码！') $pavalue = '';
		$username = reqstr('username','');
		if($username == '请输入领取用户名！') $username = '';
		
		$isflag = reqnum('flag', 0);
		$where.="flag = ".$isflag;
		$selec = array();
		$selec['flag'] = $isflag;
		if(!empty($pavalue)){
			$where.=" and (value LIKE '%".$pavalue."%')";
			$selec['value'] = $pavalue;
		}
		if(!empty($username)){
			$where.=" and username = '".$username."'";
			$selec['username'] = $username;
		}
			
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",20);
		$obj = new Psys_PackageModel();
		$data = $obj -> GetList($where, 'id DESC',$page, $pagesize,"*",'rhi_packvalue');
		self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
		$this->smarty->assign('select',$selec);
		$this->smarty->assign('list',$data['allrow']);
		$this->forward = "valuelist";
	}
	/***
	 * 导入数据
	 */
	public  function importAction()
	{
		if (!empty($_FILES ['file_stu']['name']))
		{
			$tmp_file = $_FILES ['file_stu'] ['tmp_name'];
			$file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
			$file_type = $file_types [count ( $file_types ) - 1];

			/*判别是不是.xls文件，判别是不是excel文件*/
			if (strtolower ( $file_type ) != "xls")
			{
				$this->error ( '不是Excel文件，重新上传' );
			}

			/*设置上传路径*/
			define('INSTALL',str_replace('\\','/',dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
			$savePath = INSTALL.'/traindata_appui/imgs/package/Excel/';

			/*以时间来命名上传的文件*/
			$str = date ( 'Ymdhis' );
			$file_name = $str . "." . $file_type;

			/*是否上传成功*/
			if (! copy ( $tmp_file, $savePath . $file_name ))
			{
				$this->error ( '上传失败' );
			}

			$res = $this->read ( $savePath . $file_name );
			/*对生成的数组进行数据库的写入*/
			$errmsg = '';
			$noimpor = '';
			$okmsg = '';
			$obj = new Psys_PackageModel();
			foreach ( $res as $k => $v )
			{
				if ($k != 0)
				{
					$data = array
					(
					    'packid' => $v[0],
					    'value' => $v[1],
						'flag' => 1		    
					);
					$svl = $obj->GetOne(array('value' => $v[1]),'*','rhi_packvalue');
					if(empty($svl)){
						$result = $obj->AddOne($data,'rhi_packvalue');
						if (! $result)
						{
							$str = pring_r($data,true);
							$errmsg.= date ( 'Y-m-d H:i:s' ) . $str . "\r\n";
						}
					}else{
						$noimpor .= $v[1].'：在数据库中已存在'. "\r\n";
					}
				}
			}
			if($noimpor){
				$logpas = ERRLOG_PATH.'package'.DIRECTORY_SEPARATOR.date('Ymd').DIRECTORY_SEPARATOR;
				if(!is_dir($logpas))
				{
					mkdir($logpas,0777,true);
				}
				$log = $logpas.'package_noimport.log';
				$str = $noimpor. "\r\n";
				error_log ( $str, 3, $log );
			}
			if($errmsg){				
				$logpas = ERRLOG_PATH.'package'.DIRECTORY_SEPARATOR.date('Ymd').DIRECTORY_SEPARATOR;
				if(!is_dir($logpas))
				{
					mkdir($logpas,0777,true);
				}
				$log = $logpas.'package_error.log';
				$str = $errmsg. "\r\n";
				error_log ( $str, 3, $log );
			}
			echo '<script>alert("导入成功,自动过滤从复数据！");window.location.href="/package/valuelist"</script>';
			exit;
		}
	}
	//输出错
	private function error($msg){
		echo $msg;exit;
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

	//上传图片
	public function uploadfileAction(){
		//引入图片处理类		
		require_once COMMON_PATH."XThumb.php";
		$imgthumb = new XThumb();
		$istmp = reqnum('istmp', 0);
		//require_once PUBLIC_PATH.'php'.DIRECTORY_SEPARATOR.'JSON.php';
		require_once PUBLIC_PATH.'psys/php/JSON.php';
		define('INSTALL',str_replace('\\','/',dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
		define('NEWS_PATH',INSTALL.'/traindata_appui/imgs/package/');


		//文件保存目录路径
		$save_path = NEWS_PATH;
		//文件保存目录URL
		$save_url = 'http://res.wonaonao.com/imgs/package/';
		//定义允许上传的文件扩展名
		$ext_arr = array(
			'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
			'flash' => array('swf', 'flv'),
			'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
			'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
		);
		//最大文件大小
		if($istmp){
			$max_size = 100000;
		}else{
			$max_size = 1000000;
		}

		//$save_path = realpath($save_path) . '/';
		//PHP上传失败
		if (!empty($_FILES['imgFile']['error'])) {
			switch($_FILES['imgFile']['error']){
				case '1':
					$error = '超过php.ini允许的大小。';
					break;
				case '2':
					$error = '超过表单允许的大小。';
					break;
				case '3':
					$error = '图片只有部分被上传。';
					break;
				case '4':
					$error = '请选择图片。';
					break;
				case '6':
					$error = '找不到临时目录。';
					break;
				case '7':
					$error = '写文件到硬盘出错。';
					break;
				case '8':
					$error = 'File upload stopped by extension。';
					break;
				case '999':
				default:
					$error = '未知错误。';
			}
			alert($error);
		}

		//有上传文件时
		if (empty($_FILES) === false) {
			//原文件名
			$file_name = $_FILES['imgFile']['name'];
			//服务器上临时文件名
			$tmp_name = $_FILES['imgFile']['tmp_name'];
			//文件大小
			$file_size = $_FILES['imgFile']['size'];
			//检查文件名
			if (!$file_name) {
				$this->alert("请选择文件。");
			}
			//检查目录
			if (@is_dir($save_path) === false) {
				$this->alert("上传目录不存在。");
			}
			//检查目录写权限
			if (@is_writable($save_path) === false) {
				$this->alert("上传目录没有写权限。");
			}
					
			//检查是否已上传
			if (@is_uploaded_file($tmp_name) === false) {
				$this->alert("上传失败。");
			}
			if($istmp){
				//获取图片宽度高度
				$img_info = getimagesize($tmp_name);			
				//图片比例限制
				$radio = floor(($img_info[0]/$img_info[1])*10);
				if(!($radio>9 && $radio<11))
				{
					$this->alert("上传文件宽度高度超过限制。");
				}
			}
			//检查文件大小
			if ($file_size > $max_size) {
				$this->alert("上传文件大小超过限制。");
			}
			//检查目录名
			if(empty($_GET['dirid'])){
				$obj = new Psys_PackageModel();
				$ps = $obj->GetMaxPackID();
				if(!empty($ps['max'])) $dir_name = intval($ps['max']+1);
			}else{
				$dir_name = intval($_GET['dirid']);
			}

			/*if (empty($ext_arr[$dir_name])) {
				$this->alert("目录名不正确。");
				}*/
			//获得文件扩展名
			$temp_arr = explode(".", $file_name);
			$file_ext = array_pop($temp_arr);
			$file_ext = trim($file_ext);
			$file_ext = strtolower($file_ext);
			//检查扩展名
			if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
				$this->alert("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $ext_arr[$dir_name]) . "格式。");
			}
			//创建文件夹
			if ($dir_name !== '') {
				$save_path .= $dir_name . "/";
				$save_url .= $dir_name . "/";
				if (!file_exists($save_path)) {
					mkdir($save_path);
				}
			}
			/*$ymd = date("Ymd");
			 $save_path .= $ymd . "/";
			 $save_url .= $ymd . "/";
			 */
			if (!file_exists($save_path)) {
				mkdir($save_path);
			}
			//新文件名
			if($istmp){
				$new_file_name = 'login'. '.' . $file_ext;
			}else{
				$new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
			}

			//移动文件
			$file_path = $save_path . $new_file_name;
			if (move_uploaded_file($tmp_name, $file_path) === false) {
				$this->alert("上传文件失败。");
			}else{
				$imgthumb->imgZoom($file_path,150,150,$file_path);
			}
			@chmod($file_path, 0644);
			
		
			
			$file_url = $save_url . $new_file_name;
			if($istmp){
				/*require_once COMMON_PATH."XThumb.php";
				 $imgtmp = new XThumb();
				 $target = $save_path . 'tmp_'.$new_file_name;
				 $imgtmp->imgZoom($file_path, '180', '120',$target);
				 $file_url = $save_url . 'tmp_'.$new_file_name;
				 */
				$file_url = $new_file_name;
			}
			

			header('Content-type: text/html; charset=UTF-8');
			$json = new Services_JSON();
			echo $json->encode(array('error' => 0, 'url' => $file_url));
			exit;
		}

	}

	//输出错误码
	function alert($msg) {
		require_once PUBLIC_PATH.'psys/php/JSON.php';
		header('Content-type: text/html; charset=UTF-8');
		$json = new Services_JSON();
		echo $json->encode(array('error' => 1, 'message' => $msg));
		exit;
	}
	/**
	* 读取excel $filename 路径文件名 $encode 返回数据的编码 默认为utf8
	*以下基本都不要修改
	*/
	public function read($filename,$encode='utf-8')
	{
		include  PUBLIB_PATH."phpexcel".DIRECTORY_SEPARATOR."PHPExcel.php";
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($filename);
		$objWorksheet = $objPHPExcel->getActiveSheet();
	
		$highestRow = $objWorksheet->getHighestRow();
		$highestColumn = $objWorksheet->getHighestColumn();
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		$excelData = array();
		for ($row = 1; $row <= $highestRow; $row++) {
			for ($col = 0; $col < $highestColumnIndex; $col++) {
				$excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
			}
		}
		return $excelData;
	}
}

?>