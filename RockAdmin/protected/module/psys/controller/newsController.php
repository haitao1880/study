<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月10日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:ipcController.php                                                
* 创建时间:下午3:02:53                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: ipcController.php 4042 2014-09-01 09:21:47Z neil $                                                 
* 修改日期:$LastChangedDate: 2014-09-01 17:21:47 +0800 (周一, 2014-09-01) $                                     
* 最后版本:$LastChangedRevision: 4042 $                                 
* 修 改 者:$LastChangedBy: neil $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/controller/ipcController.php $                                            
* 摘    要: 车上服务器管理                                                      
*/
class newsController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 新闻列表
	 */
	public function indexAction()
	{
		
		$keyword = reqstr('keyword','');
		if($keyword=='请输入关建词！') $keyword = '';
		$news_type = reqstr('news_type','');
		$indate = reqstr('indate','');
		$todate = reqstr('todate','');
		
		$cache = XMemCache::GetInstance ();
		$key = 'selectkeyword';
		
		$where = array('keyword'=>$keyword,'news_type'=>$news_type,'indate'=>$indate,'todate'=>$todate);
		if(!empty($keyword) or $news_type  or isset($news_type) or $indate or $todate){
			$cache->Set($key,$where,600);
		}
		$select = $cache->Get($key);
		
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",20);
		$obj = new Psys_NewsModel();
		$data = $obj -> GetNewsList($select, 'id DESC',$page, $pagesize,"*");
		self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
		
		$types = $this->getNewsType();
		foreach ($data['allrow'] as &$v){
			$v['newstype'] = $types[$v['newstype']];
		}
		$this->smarty->assign('select',$select);
		$this->smarty->assign('list',$data['allrow']);
		$this->smarty->assign('newstype',$types);
		$this->forward = "index";
	}
	//新闻分类
	public function getNewsType(){
		$type = array('1'=>'今日头条','2'=>'财经','3'=>'娱乐','4'=>'体育','5'=>'科技','6'=>'社会','7'=>'军事');
		return $type;
	}
	/**
	 * 添加新闻
	 */
	public function addAction(){
		$sub = reqstr('btnSave', '');
		$cid = reqnum('cid', 0);
		$am = new Psys_NewsModel();
		if($sub){
			$key = reqstr('key','');
			$ntitle = reqstr('title', '');
			$imgurl = reqstr('imgurl', '');
			$author = reqstr('author', '');
			$content = reqstr('ndetail', '');
			
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
			
				
			$ctime = reqstr('ctime', '');
			if(!empty($ctime)){
				$utime = strtotime($ctime);
				$time = date('Y-m-d',$utime);
				$stime = date('H:i:s',$utime);
				if($stime=='00:00:00'){
					$stime = date('H:i:s',time());
				}
				$ctime = strtotime($time.' '.$stime);
			}else{
				$ctime = time();
			}
			$nfrom = reqstr('nfrom', '');
			$type = reqnum('news_type', 0);
			$sort = reqnum('sort', 0);
			$flag = reqnum('flag',0);
			if($ntitle == '' or $content == '' or $ctime == ''){
				echo '<script>alert("内容不全不能提交");window.location.href="/news/add?cid='.$type.'"</script>';
				exit;
			}
			$data = array(
				'appkey'=>$key,
				'title'=>$ntitle,
				'imgurl'=>$imgurl,
				'author'=>$author,
				'content'=>$content,
				'nfrom'=>$nfrom,
				'newstype'=>$type,
				'sort'=>$sort,
				'ctime'=>$ctime,
				'flag'=>$flag
			);			
			
			$inid = $am->AddOne($data,'rhi_appnews');
			if($inid){
				echo '<script>alert("添加成功！");window.location.href="/news/index"</script>';
				exit;
			}else{
				echo '<script>alert("添加失败！");</script>';
			}
		}
		$types = $this->getNewsType();
     	$this->smarty->assign('new_type',$types);
		$this->smarty->assign('action','add');
		$this->smarty->assign('datetime',date('Y-m-d H:i:s',time()));
		$this->smarty->assign('cid',$cid);
		$this -> forward = 'add';
	}
	/**
	 * 删除新闻
	 */
	public function delAction(){
		$am = new Psys_NewsModel();
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
		$am = new Psys_NewsModel();
		$id = reqnum('id', 0);	
		$isflg = reqnum('isflg', 0);
		if($id > 0){
			$isflg = ($isflg == 1)? 0 : 1 ;
			$data = array('flag'=>$isflg,'servicer'=>'');
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
	public function displayAction(){
		$id = reqnum('id', 0);
		if(!empty($id)){
			$am = new Psys_NewsModel();
			$new = $am->GetOne(array('id'=>$id));
			$this->smarty->assign('newsinfo',$new);
			$this->forward='newsinfo';
		}
	}
	/**
	 * 修改新闻
	 */
	public function editAction(){
		$sub = reqstr('btnSave', '');
		
		if($sub){
			$id = reqnum('news_id', 0);
	        if($id > 0){
				$data = array();
				$key = reqstr('key','');
				$ntitle = reqstr('title', '');
				$imgurl = reqstr('imgurl', '');
				$author = reqstr('author', '');
				
				$content = reqstr('ndetail', '');
				$content = str_replace("<br />", "", $content);
				$content = preg_replace("/<(a.*?)>(.*?)<(\/a.*?)>/si","$2",$content); //过滤a标签
				
				$pattern = '/.*(<p.*?>)[\r\n\s]*?(<img.*?\/>)/i';
				//$content = preg_replace($pattern,"<p>$2</p>$1",$content);
				$content = preg_replace($pattern,"<p>$2</p><p style='text-indent:2em;'>",$content);
				//替换空Ｐ
				$pattern = '/<p[^>]*?>[\s\r\n]*?<\/p>/i';				
				$content = preg_replace($pattern,"",$content);
							
		        $ctime = reqstr('ctime', '');
				if(!empty($ctime)){
					$utime = strtotime($ctime);
					$time = date('Y-m-d',$utime);
					$stime = date('H:i:s',$utime);
					if($stime=='00:00:00'){
						$stime = date('H:i:s',time());
					}
					$ctime = strtotime($time.' '.$stime);
				}else{
					$ctime = time();
				}
			
				$nfrom = reqstr('nfrom', '');
				$type = reqnum('news_type', 0);
				$sort = reqnum('sort', 0);
				$flag = reqnum('flag',0);
		        if($ntitle == '' or $content == '' or $ctime == ''){
					echo '<script>alert("内容不全不能提交");window.location.href="/news/edit?id='.$id.'"</script>';
					exit;
				}
				$data = array(
					'appkey'=>$key,
					'title'=>$ntitle,
					'imgurl'=>$imgurl,
					'author'=>$author,
					'content'=>$content,
					'nfrom'=>$nfrom,
					'newstype'=>$type,
					'sort'=>$sort,
					'ctime'=>$ctime,
					'flag'=>$flag,
					'servicer'=>''
				);					
				
				$am = new Psys_NewsModel();
				$inid = $am->editNews($id, $data);
								
				if($inid){
					echo '<script type="text/javascript">alert("修改成功！");window.location.href="/news/index?cid='.$type.'"</script>';
					exit;
				}else{
					echo '<script type="text/javascript">alert("修改失败！");window.location.href="/news/edit/'.$id.'"</script>';
				}
	        }
		}
		$id = reqnum('id', 0);
	
		if($id > 0){
			$am = new Psys_NewsModel();
			$newInfo = $am->GetNewInfo($id);
			$type = $this->getNewsType();
     		$this->smarty->assign('new_type',$type);
      
			$this->smarty->assign('new',$newInfo);
		}
		$datetime = !empty($newInfo['ctime']) ? $newInfo['ctime'] : time();
		$this->smarty->assign('datetime',date('Y-m-d H:i:s',$datetime));
		$this->smarty->assign('action','edit');
		$this -> forward = 'add';
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
		$istmp = reqnum('istmp', 0);
		//require_once PUBLIC_PATH.'php'.DIRECTORY_SEPARATOR.'JSON.php';
		require_once PUBLIC_PATH.'psys/php/JSON.php';
		define('INSTALL',str_replace('\\','/',dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
		define('NEWS_PATH',INSTALL.'/traindata_appui/imgs/news/');
		
		
		//文件保存目录路径
		$save_path = NEWS_PATH;
		//文件保存目录URL
		$save_url = 'http://news.wonaonao.com/imgs/news/';
		//定义允许上传的文件扩展名
		$ext_arr = array(
			'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
			'flash' => array('swf', 'flv'),
			'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
			'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
		);
		
		//最大文件大小
		$max_size = 1000000;
		
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
			//检查文件大小
			if ($file_size > $max_size) {
				$this->alert("上传文件大小超过限制。");
			}
			//检查目录名
			$dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
			if (empty($ext_arr[$dir_name])) {
				$this->alert("目录名不正确。");
			}
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
			$ymd = date("Ymd");
			$save_path .= $ymd . "/";
			$save_url .= $ymd . "/";
			if (!file_exists($save_path)) {
				mkdir($save_path);
			}
			//新文件名
			$new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
			//移动文件
			$file_path = $save_path . $new_file_name;
			if (move_uploaded_file($tmp_name, $file_path) === false) {
				$this->alert("上传文件失败。");
			}
			@chmod($file_path, 0644);
			$file_url = $save_url . $new_file_name;
			if($istmp){
				require_once COMMON_PATH."XThumb.php";
				$imgtmp = new XThumb();
				$target = $save_path . 'tmp_'.$new_file_name;
				$imgtmp->imgZoom($file_path, '180', '120',$target);
				$file_url = $save_url . 'tmp_'.$new_file_name;
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
	 * 
	 * 新闻实时同步
	 */
	public function syncAction()
	{
		$configPath = dirname(dirname(dirname(dirname(__FILE__)))).'/configs/server_config.php';
		require $configPath;
		$this->smarty->assign('lists',$SERVER_LIST);
		$this->smarty->assign('num',$syncNum);
		$this->forward = 'sync';
	}
	
	/**
	 * 新闻数据同步
	 */
	public function synclAction()
	{
		$configPath = dirname(dirname(dirname(dirname(__FILE__)))).'/configs/server_config.php';
		require $configPath;
		//从配置文件中获取允许同步IP
		$ip = array("171.221.199.56","112.193.220.232");
		//获取当前IP
		$curIp = real_ip();
		if(in_array($curIp,$ip))
		{
			//同步rhi_idc-rht_train
			global $G_X;
			$model = new Psys_NewsModel();
			$servicer = $G_X['appkey'];
			$data = $model->getSyncList($servicer);
			foreach($data as $news)
			{
				unset($news['servicer']);
				$where = array('id'=>$news['id']);
				$field = 'id';
				$one = $model->GetSyncOne($where,$field);
				if($one)
				{
					$updateR = $model->UpdateSyncOne($news, $where);
					if($updateR !== false)
					{
						$field = "servicer";
						$result = $model->GetOne($where,$field);
						$server = $result['servicer'];
						$server = $server ? $server . ',' . $servicer : $servicer;
						$updateData = array('servicer'=>$server);
						$model->UpdateOne($updateData, $where);
					}
				}
				else
				{
					$insertR = $model->AddSyncOne($news);
					if($insertR !== false)
					{
						$field = "servicer";
						$result = $model->GetOne($where,$field);
						$server = $result['servicer'];
						$server = $server ? $server . ',' . $servicer : $servicer;
						$updateData = array('servicer'=>$server);
						$model->UpdateOne($updateData, $where);
					}
				}
			}
			
			$host = reqstr('host','');
			$data = array();
			$url = $host . '/news_and_ads.php?act=news';
			$return = http_post_array($url, $data);
			return $return['msg'];
		}
		else
		{
			return array('result'=>'ERROR','msg'=>'不可允许IP同步！');
		}	
	}
	
	
	public function newsnumaction()
	{
		$appkey = reqstr('apk','');
		$model = new Psys_NewsModel();
		$data = $model->newsNum($appkey);
		return $data;
	}
}