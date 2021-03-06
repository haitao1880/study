<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月11日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:resController.php                                                
* 创建时间:下午2:18:51                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: resController.php 4022 2014-09-01 07:02:34Z terry $                                                 
* 修改日期:$LastChangedDate: 2014-09-01 15:02:34 +0800 (周一, 01 九月 2014) $                                     
* 最后版本:$LastChangedRevision: 4022 $                                 
* 修 改 者:$LastChangedBy: terry $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/controller/resController.php $                                            
* 摘    要: 资源列表                                                      
*/
class resController extends PSys_AbstractController {
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
	 * 资源状态
	 * 显示当前总共有：视频多少，音乐多少，游戏多少
	 */
	public function indexAction() {
		$this->forward = "index";
	}
	
	/**
	 * 音乐添加修改
	 */
	public function maddAction() {
		$model = new PSys_ResModel();
		$where = array();
		$alist = $model -> GetList($where, 'id DESC','', '',"*",'rhi_album');
		
		$this->smarty->assign('alist',$alist['allrow']);
		$this->smarty->assign("active_menu","res");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "madd";
	}
		//编辑专辑
	public function meditAction()
	{
		$id = reqnum('id',0); //获取参数
		if (!$id) {
			echo 'empty id';
			exit;
		}
		$model = new PSys_ResModel();
		$where = array('id'=>$id);
		$info = $model -> GetOne($where,"*",'rhi_albummusic');
		
		$alist = $model -> GetList('', 'id DESC','', '',"*",'rhi_album');
		
		$this->smarty->assign('alist',$alist['allrow']);
		$this->smarty->assign('info',$info);
		$this->smarty->assign("active_menu","res");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "medit";
		
    }
	
	
	//音乐更新
	public function updatemusicAction()
	{
		$id = reqnum('id', 0);
		$ispost = reqnum('ispost', 0);
		$model = new PSys_ResModel();
		if($ispost == 1)
		{
			$albumid = reqstr('albumid');
			$musicid = reqstr('musicid');
			$mname = reqstr('mname');
			$singer = reqstr('singer');
			$sortid = reqstr('sortid');
			$flag = reqnum('flag',0);
			$hits = reqstr('hits');
			$price = reqstr('price');
			$mpath = reqstr('mpath');
			
			$data = array(
					'albumid' => $albumid,
					'musicid' => $musicid,
					'mname' => $mname,
					'singer' => $singer,
					'sortid' =>$sortid,
					'flag' => $flag,
					'hits' => $hits,
					'price' => $price,
					'mpath' => $mpath,
			);
			$result = array('result'=>'ERROR');
			if($id == 0)
			{
				if($mname == '')
				{
					MsgInfoConst::GetMsg(1051, $result);
					return $result;
				}
			}
			else
			{
				if($mname == '')
				{
					MsgInfoConst::GetMsg(1051, $result);
					return $result;
				}
			
			}	
			
			
			if($id == 0) //新增
			{
				$data['ctime'] = time();
				$model->addMusic($data);
				//$id = $model->AddOne($data,'rhi_albummusic');
				//$model->Record($data,$id,'db-rht_sync','albummusic','rhs_downsync');
				//start 写操作日志
				$log = array(
							 'logtype' => 71,
							 'guid'  => $_SESSION['Cur_X_User']['id'],
							 'ctime' => time(),
							 'cip' => real_ip(),
								);
				$log['logdetail'] = $_SESSION['Cur_X_User']['username']."于".date("Y-m-d H:i:s")."[添加]音乐".$mname;			
				$model->admin_syslog($log); 
				//end 日志
				$result['result'] = 'SUCCESS';
			}
			else  //更新
			{
				$w = array('id' => $id);
				$model->updateMusic($data,$w);
				//$res = $model->UpdateOne($data,$w,'rhi_albummusic');
				//$model->Record($data,$res,'db-rht_sync','albummusic','rhs_downsync');
				//start 写操作日志
				$log = array(
							 'logtype' => 71,
							 'guid'  => $_SESSION['Cur_X_User']['id'],
							 'ctime' => time(),
							 'cip' => real_ip(),
								);
				$log['logdetail'] = $_SESSION['Cur_X_User']['username']."于".date("Y-m-d H:i:s")."[编辑]音乐".$mname;			
				$model->admin_syslog($log); 
				//end 日志
				$result['result'] = 'SUCCESS';
			}
			
			return $result;
		}	
	}
	//删除音乐
	public function mdelAction()
	{
		$model = new PSys_ResModel();
		if(is_array($_GET['id']))
		{
			$id = reqarray('id',array());
			foreach($id as $v)
			{
				$where = array('id'=>$v);
				$info = $model ->GetOne($where,'*','rhi_albummusic');
				
				/*
				//物理文件删除
				$filename = $info['mpath'];
				$musicPath = dirname(dirname(VIDEO_PATH)) . '/' . 'files' . '/' . 'music' . '/' . $filename;
				unlink($musicPath);
				*/
								
				$model -> DeleteOneMusic($where);
				//start 写操作日志
				$log = array(
							 'logtype' => 1,
							 'guid'  => $_SESSION['Cur_X_User']['id'],
							 'ctime' => time(),
							 'cip' => real_ip(),
								);
				$log['logdetail'] = $_SESSION['Cur_X_User']['username']."于".date("Y-m-d H:i:s")."[删除]音乐".$info['mname'];			
				$model->admin_syslog($log); 
				//end 日志
			}
			header("Location:/res/mlist");
		}
		elseif(is_numeric($_GET['id']))
		{
			$id  =reqnum('id','0');
			$where = array('id'=>$id);
			$info = $model ->GetOne($where,'*','rhi_albummusic');
			$model -> DeleteOneMusic($where);
			//start 写操作日志
				$log = array(
							 'logtype' => 1,
							 'guid'  => $_SESSION['Cur_X_User']['id'],
							 'ctime' => time(),
							 'cip' => real_ip(),
								);
				$log['logdetail'] = $_SESSION['Cur_X_User']['username']."于".date("Y-m-d H:i:s")."[删除]音乐".$info['mname'];			
				$model->admin_syslog($log); 
				//end 日志
			header("Location:/res/mlist");
		}
		else
		{
			echo "<script>alert('id 为空');</script>";
		}
		
	}
	/**
	 * 音乐列表
	 */
	public function mlistAction() {
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$where = array();
		$nt = new PSys_ResModel();
		
		$list = $nt -> GetList($where,'id DESC',$page, $pagesize,"*",'rhi_albummusic');
		//$sql ="select m.*,a.aname from rhi_albummusic as m left join  rhi_album as a on m.albumid = a.id order by m.id desc ";
		//$list = $nt -> Query($sql);
		//echo "<pre>";print_r($list);exit; 
		self::inidate($list['allnum'],$page,$pagesize,count($list['allrow']));
		foreach($list['allrow'] as $key=>$val){
			$where = array('id'=>$val['albumid']);
			$alist = $nt -> GetList($where, 'id DESC','', '',"*",'rhi_album');
			$list['allrow'][$key]['albumid'] = $alist['allrow'][0]['aname'];
		}
		$this->smarty->assign('list',$list['allrow']);
		$this->smarty->assign('psys_base_url',PSYS_BASE_URL);
		$this->smarty->assign("active_menu","res");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "mlist";
	}
	//专辑列表
	public function albumlistAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$where = array();
		$nt = new PSys_ResModel();
		$list = $nt -> GetList($where,'id DESC',$page, $pagesize,"*",'rhi_album');

		self::inidate($list['allnum'],$page,$pagesize,count($list['allrow']));

		$this->smarty->assign('list',$list['allrow']);
		$this->smarty->assign('psys_base_url',PSYS_BASE_URL);
		$this->smarty->assign("active_menu","res");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "album_list";
	}
	//添加专辑
	public function albumaddAction() 
	{
		$this->smarty->assign("active_menu","res");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "album_add";
	}
		//专辑数据更新
	public function updatealbumAction()
	{
		$id = reqnum('id', 0);
		$ispost = reqnum('ispost', 0);
		$model = new PSys_ResModel();
		if($ispost == 1)
		{
			$aname = reqstr('aname');
			$smallpic = reqstr('smallpic');
			$bigpic = reqstr('bigpic');
			$hits = reqstr('hits');
			$flag = reqnum('flag',0);
			$parternid = reqstr('parternid');
			$partner = reqstr('partner');
			$sortid = reqstr('sortid');
			
			$data = array(
					'aname' => $aname,
					'smallpic' => $smallpic,
					'bigpic' => $bigpic,
					'hits' =>$hits,
					'flag' => $flag,
					'parternid' => $parternid,
					'partner' => $partner,
			);
			$result = array('result'=>'ERROR');
			if($id == 0)
			{
				if($aname == '')
				{
					MsgInfoConst::GetMsg(1050, $result);
					return $result;
				}
			}
			else
			{
				if($aname == '')
				{
					MsgInfoConst::GetMsg(1050, $result);
					return $result;
				}
			
			}	
			
			
			if($id == 0) //新增
			{
				$data['ctime'] = time();
				$model->AddAlbum($data);
				//$id = $model->AddOne($data,'rhi_album');
				//$model->Record($data,$id,'db-rht_sync','album','rhs_downsync');
				//start 写操作日志
				$log = array(
							 'logtype' => 71,
							 'guid'  => $_SESSION['Cur_X_User']['id'],
							 'ctime' => time(),
							 'cip' => real_ip(),
								);
				$log['logdetail'] = $_SESSION['Cur_X_User']['username']."于".date("Y-m-d H:i:s")."[添加]音乐专辑".$aname;			
				$model->admin_syslog($log); 
				//end 日志
				$result['result'] = 'SUCCESS';
			}
			else  //更新
			{
				$data['utime'] = time();
				$w = array('id' => $id);
				
				//在更新前删除原始图片物理文件
				$oldData = $model->GetOne($w,'smallpic,bigpic','rhi_album');	//取得专辑信息
				//判断是否更新
				$smallpic = $oldData['smallpic'];
				$bigpic = $oldData['bigpic'];
				//该专辑物理文件存放地址
				$smallDir = ALBUM_PATH . '/' . 'small_pic' . '/' . $smallpic;
				$bigDir = ALBUM_PATH . '/' . 'big_pic' . '/' . $bigpic;
				if(file_exists($smallDir) && $smallpic != $data['smallpic'])
				{
					unlink($smallDir);
				}
				if(file_exists($bigDir) && $bigpic != $data['bigpic'])
				{
					unlink($bigDir);
				}		
				//-------------原始物理文件删除结束
				$model->UpdateAlbum($data,$w);
				//$id = $model->UpdateOne($data,$w,'rhi_album');
				//$model->Record($data,$id,'db-rht_sync','album','rhs_downsync');
				//start 写操作日志
				$log = array(
							 'logtype' => 71,
							 'guid'  => $_SESSION['Cur_X_User']['id'],
							 'ctime' => time(),
							 'cip' => real_ip(),
								);
				$log['logdetail'] = $_SESSION['Cur_X_User']['username']."于".date("Y-m-d H:i:s")."[编辑]音乐专辑".$aname;			
				$model->admin_syslog($log); 
				//end 日志
				$result['result'] = 'SUCCESS';
			}
			
			return $result;
		}	
	}
	//编辑专辑
	public function albumeditAction()
	{
		$id = reqnum('id',0); //获取参数
		if (!$id) {
			echo 'empty id';
			exit;
		}
		$nt = new PSys_ResModel();
		$where = array('id'=>$id);
		$info = $nt -> GetOne($where,"*",'rhi_album');
		$this->smarty->assign('info',$info);
		$this->smarty->assign("active_menu","res");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "album_edit";
		
    }
	
	//删除专辑
	public function albumdelAction()
	{
		$nt = new PSys_ResModel();

		if(is_numeric($_GET['id']))
		{
			$id  =reqnum('id','0');
			$where = array('id'=>$id);
			$info = $nt ->GetOne($where,'*','rhi_album');//取专辑信息
			
			//		------------物理文件删除
			//构建删除物理地址条件
			$smallpic = $info['smallpic'];
			$bigpic = $info['bigpic'];
			//该专辑物理文件存放地址
			$smallDir = ALBUM_PATH . '/' . 'small_pic' . '/' . $smallpic;
			$bigDir = ALBUM_PATH . '/' . 'big_pic' . '/' . $bigpic;
			if(file_exists($smallDir) && $smallpic != $data['smallpic'])
			{
				unlink($smallDir);
			}
			if(file_exists($bigDir) && $bigpic != $data['bigpic'])
			{
				unlink($bigDir);
			}
						
			
			//取专辑下所有歌曲并日志
			$w = array('albumid' => $info['id'] );
			$allmusic = $nt->Getlist($w, 'id DESC',0, 0,"*",'rhi_albummusic');
			foreach($allmusic['allrow'] as $vs)
			{
				//删除当前专辑下的音乐
				//start 写操作日志
				$log = array(
							 'logtype' => 71,
							 'guid'  => $_SESSION['Cur_X_User']['id'],
							 'ctime' => time(),
							 'cip' => real_ip(),
								);
				$log['logdetail'] = $_SESSION['Cur_X_User']['username']."于".date("Y-m-d H:i:s")."[删除]音乐".$vs['mname'];			
				$nt->admin_syslog($log); 
				//end 日志
				
			}
			
			//$res = $nt -> DeleteOne($where,'rhi_album');
			$res = $nt->deleteOneAlbum($where);
			//start 写操作日志
			$log = array(
						 'logtype' => 1,
						 'guid'  => $_SESSION['Cur_X_User']['id'],
						 'ctime' => time(),
						 'cip' => real_ip(),
							);
			$log['logdetail'] = $_SESSION['Cur_X_User']['username']."于".date("Y-m-d H:i:s")."[删除]音乐专辑".$info['aname'];			
			$nt->admin_syslog($log); 
			//end 日志
			header("Location:/res/albumlist");
		}
		else
		{
			echo "<script>alert('id 为空');</script>";
		}
		
	}
	/**
	 * 游戏列表
	 */
	public function glistAction() {
		$page = reqnum ( "page", 1 );
		$pagesize = reqnum ( "pagesize", 20 );
		$typeid = reqstr ( 'type', '1' );
		$m = new PSys_ResRule ();
		$list = $m->GameList ( $typeid, $page, $pagesize, '*' );
		foreach ( $list ['allrow'] as $key => &$var ) {
			$var ['imgurl'] = '/imgs/Games/' . $var ['imgurl'];
		}
		self::inidate ( $list ['allnum'], $page, $pagesize, count ( $list ['allrow'] ) );
		$this->smarty->assign ( 'list', $list ['allrow'] );
		$this->smarty->assign ( 'psys_base_url', PSYS_BASE_URL );
		$this->smarty->assign ('page',$page);
		$this->smarty->assign("active_menu","res");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "glist";
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
	 * 游戏添加
	 */
	public function gaddAction() {
		$this->forward = "gedit";
	}
	/**
	 * 转到游戏修改页面
	 */
	public function geditAction() {
		$id = reqnum ( 'id', 0 ); // 获取参数
		if (! $id) {
			echo 'empty id';
			exit ();
		}
		$m = new PSys_ResModel ();
		$where = array (
				'id' => $id 
		);
		$info = $m->GetOneGame ( $where );
		$m = new PSys_ResRule ();
		//var_dump($info);exit;
		$list = $m->GamePPTList($info['appid'], 1, 5, '*' );
		$imgurls = '';
		
		foreach($list['allrow'] as $imgs)
		{
			$imgurls .= $imgurls ? ';' . $imgs['imgurl'] : $imgs['imgurl'];
		}
		$this->smarty->assign('imgurls',$imgurls);
		$this->smarty->assign ( 'info', $info );
		$this->smarty->assign("active_menu","res");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "gedit";
	}
	/**
	 * 更新游戏信息
	 */
	public function gupdateAction() {
		$id = reqnum ( 'id', 0 );
		$ispost = reqnum ( 'ispost', 0 );
		$m = new Psys_ResModel ();
		if ($ispost == 1) {
			$appname = reqstr ( 'appname' );
			$appcol = reqstr ( 'appcol' );
			$appid = reqstr('appid','');
			$appid = $appid ? $appid : $this->getmaxappidAction($appcol);
			$price = reqstr ( 'price',0);
			$downcount = reqstr ( 'downcount',0);
			$logourl = reqstr ( 'logourl' );
			$appurl = reqstr ( 'appurl' );
			$ver = reqstr ( 'ver', 0 );
			$vernum = reqstr ( 'vernum' );
			$filesize = reqstr ( 'filesize' );
			$apppackage = reqstr ( 'package' );			
			$apptype = reqstr ( 'apptype' );
			$lang = reqstr ( 'lang' );
			$iftj = reqstr ( 'iftj' );
			$flag = reqstr ( 'flag' );
			$adesc = reqstr ( 'adesc' );
			$adetail = reqstr ( 'adetail' );
			
			$typeinfo = reqstr ( 'typeinfo' );
			$develop = reqstr ( 'develop' );
			
			$sig = reqstr('sig');
			$pptfilenames = reqstr('pptfilenames');
			$pptiosnames = reqstr('pptios');
			$sortid = reqnum('sortid', 0);
			//return array('result'=>$pptfilenames);
			//转ppt文件名连接成的字符串为数组
			if(!empty($pptfilenames))
			{
				$ppt = explode(';',$pptfilenames);
			}
			else 
			{
				$ppt = array();
			}
			
			if(!empty($pptiosnames))
			{
				$pptios = explode(';',$pptiosnames);
			}
			else 
			{
				$pptios = array();
			}
			//$ext = strrchr($logourl,'.');
			$ext = '.png';
			$data = array (
					//'appguid' => strtoupper ( md5 ( uniqid ( rand (), true ) ) ),
					'appname' => $appname,
					'appid' => $appid,
					'price' => $price,
					'downcount' => $downcount,
					'sortid' => $sortid,
					'imgurl' => 'logo' . $ext,
					'appurl' => $appurl,
					'ver' => $ver,
					'vernum' => $vernum,
					'filesize' => $filesize,
					'package' => $apppackage,
					'appcol' => $appcol,
					'apptype' => $apptype,
					'lang' => $lang,
					'iftj' => $iftj,
					'flag' => $flag,
					'adesc' => $adesc,
					'adetail' => $adetail,
					'signature' => $sig,
					'ctime' => time (),
					'utime' => time(),
					'typeinfo' => $typeinfo,
					'develop' => $develop,
			);
			
			$result = array (
					'result' => 'ERROR' 
			);
			if ($logourl == '') {
				MsgInfoConst::GetMsg ( 1041, $result );
				return $result;
			}
			/*
			 * // 判断APP是否存在 $where = array ( 'appid' => $appid ); $isexit = $m->GetOneGame ( $where ); if ($isexit || count ( $isexit ) > 0) { MsgInfoConst::GetMsg ( 1043, $result ); return $result; }
			 */
			if ($id == 0) 			// 新增
			{
				$m->AddGame ( $data );				
				if($apptype == 2)
				{
					$ppt = $pptios;
				}
				if(!empty($ppt))
				{
					for($i=0; $i < count ( $ppt ); $i ++) {
						// APP对应PPT
						//$ext = '.png';
						//$ext = strrchr($ppt[$i],'.');
							
						
						$appimg = array (
								'appid' => $appid,
								'imgurl' => $ppt[$i],
								'ctime' => time () 
						);
						if(empty($appimg['imgurl'])){
							break;
						}
						$res = $m->AddGamePPT ( $appimg );
						$m->Record($appimg,$res,'db-rht_sync','appimg','rhs_downsync');
					}
				}
				// start 写操作日志
				$log = array (
						'logtype' => 71,
						'guid' => $_SESSION ['Cur_X_User'] ['id'],
						'ctime' => time (),
						'cip' => real_ip () 
				);
				$log ['logdetail'] = $_SESSION ['Cur_X_User'] ['username'] . "于" . date ( "Y-m-d H:i:s" ) . "[添加]APP" . $appname;
				$m->admin_syslog ( $log );
				$result ['result'] = 'SUCCESS';
			} 
			else 			// 更新
			{
				
				$w = array (
						'id' => $id 
				);
				$data ['utime'] = time ();
				
				//------------------------物理文件更新开始
				//获取更新前数据信息
				$oldData = $m->GetOneGame($w,'imgurl,appurl');
				if($data['appcol'] == 1)
				{
					$dir = GAME_PATH;
				}
				else 
				{
					$dir = APP_PATH;
				}
				$imgurlPath = $dir . '/' . $data['appid'] . '/' . $oldData['imgurl'];
				$appurlPath = $dir . '/' . $data['appid'] . '/' .$oldData['appurl'];
				if(file_exists($imgurlPath) && $data['imgurl'] != $oldData['imgurl'])
				{
					unlink($imgurlPath);
				}
				if(file_exists($appurlPath) && $data['appurl'] != $oldData['appurl'])
				{
					unlink($appurlPath);
				}
				//------------------------物理文件更新结束
				
				$m->UpdateGame ( $data, $w );
				// 先删除PPT表里等于APPID的PPT
				$w = array (
						'appid' => $appid 
				);
				$m->DelOneGamePPT ( $w );
				for($i=0; $i < count ( $ppt ); $i ++) {
					// APP对应PPT
					//$ext = strrchr($ppt[$i],'.');
					//$ext = '.png';
					$appimg = array (
							'appid' => $appid,
							'imgurl' => $ppt[$i],
							'ctime' => time () 
					);
					if(empty($appimg['imgurl'])){
						break;
					}
					$res = $m->AddGamePPT ( $appimg );
					$m->Record($appimg,$res,'db-rht_sync','appimg','rhs_downsync');
				}
				// start 写操作日志
				$log = array (
						'logtype' => 71,
						'guid' => $_SESSION ['Cur_X_User'] ['id'],
						'ctime' => time (),
						'cip' => real_ip () 
				);
				$log ['logdetail'] = $_SESSION ['Cur_X_User'] ['username'] . "于" . date ( "Y-m-d H:i:s" ) . "[编辑]APP信息" . $appname;
				$m->admin_syslog ( $log );
				// end 日志
				$result ['result'] = 'SUCCESS';
			}
			
			return $result;
		}
	}
	
	
	/**
	 * 删除应用
	 */
	public function gdeleteAction() {
		$m = new PSys_ResModel ();
		if(isset($_GET['page']))
		{
			$page = reqnum('page',1);
		}
		else 
		{
			$page = 1;
		}
		if(isset($_GET['id']))
		{
			if ( is_array ( $_GET ['id'] )) {
				$id = reqarray ( 'id', array () );
				foreach ( $id as $v ) {
					$where = array (
							'id' => $v 
					);
					$data = $m->GetOneGame ( $where );
					
					//-------------物理文件删除开始
					//物理文件删除判断
					if($data['appcol'] == 1)
					{
						$pathType = GAME_PATH;
					}
					else 
					{
						$pathType = APP_PATH;
					}
					$path = $pathType . '/' . $data['appid'];
					if(is_dir($path))
					{
						deldir($path);
					}
					//----------------------------------物理文件删除结束
					$affectedNum = $m->DelOneGame ( $where );
					// 删除等于当前等于APPID的PPT
					$w = array (
							'appid' => $data ['appid'] 
					);
					$m->DelOneGamePPT ( $w );
					// start 写操作日志
					$log = array (
							'logtype' => 71,
							'guid' => $_SESSION ['Cur_X_User'] ['id'],
							'ctime' => time (),
							'cip' => real_ip () 
					);
					$log ['logdetail'] = $_SESSION ['Cur_X_User'] ['username'] . "于" . date ( "Y-m-d H:i:s" ) . "[删除]游戏" . $data ['appname'];
					// $m=new PSys_SyslogModel();
					// $m->admin_syslog($log);
					$m->admin_syslog ( $log );
					// end 日志
				}
				header ( "Location:/res/glist?page=$page" );
			} elseif (is_numeric ( $_GET ['id'] )) {
				$id = reqnum ( 'id', '0' );
				$where = array (
						'id' => $id 
				);
				$data = $m->GetOneGame ( $where );
				
				//--------------------------------物理文件删除开始
				//物理文件删除判断
				if($data['appcol'] == 1)
				{
					$pathType = GAME_PATH;	
				}
				else 
				{
					$pathType = APP_PATH;
				}
				$pathAppid = $data['appid'];
				//$list = $m->GetList(array('appid'=>$data['appid']),'','','','*','rhi_apps');
				$path = $pathType . '/' . $data['appid'];
				
				if(is_dir($path))
				{
					deldir($path);
				}
				
				//---------------------------------物理文件删除结束
				
				
				$m->DelOneGame ( $where );
				// 删除等于当前等于APPID的PPT
				$w = array (
						'appid' => $data ['appid'] 
				);
				$m->DelOneGamePPT ( $w );
				// start 写操作日志
				$log = array (
						'logtype' => 71,
						'guid' => $_SESSION ['Cur_X_User'] ['id'],
						'ctime' => time (),
						'cip' => real_ip () 
				);
				$log ['logdetail'] = $_SESSION ['Cur_X_User'] ['username'] . "于" . date ( "Y-m-d H:i:s" ) . "[删除]游戏" . $data ['appname'];
				$m->admin_syslog ( $log );
				// end 日志
				header ( "Location:/res/glist?page=$page" );
			} else {
				echo "<script>alert('为选择数据，id为空。');</script>";
				header('HTTP/1.1 204 no content');
			}
		}
		else 
		{
			echo "<script>alert('为选择数据，id为空。');</script>";	
			header('HTTP/1.1 204 no content');
		}
	}
	
	/**
	 * 视频列表
	 */
	public function vlistAction() {
		$page = reqnum ( "page", 1 );
		$pagesize = reqnum ( "pagesize", 10 );
		$m = new PSys_ResRule ();
		$list = $m->VideoList ( $page, $pagesize, '*' );
		self::inidate ( $list ['allnum'], $page, $pagesize, count ( $list ['allrow'] ) );
		$this->smarty->assign ( 'list', $list ['allrow'] );
		$this->smarty->assign ( 'psys_base_url', PSYS_BASE_URL );
		$this->smarty->assign("active_menu","res");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "vlist";
	}
	
	/**
	 * 视频添加
	 */
	public function vaddAction() {
		$this->smarty->assign("active_menu","res");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "vedit";
	}
	/**
	 * 转到视频修改页面
	 */
	public function veditAction() {
		$id = reqnum ( 'id', 0 ); // 获取参数
		if (! $id) {
			return array (
					'status' => - 1 
			);
		}
		$m = new PSys_ResModel ();
		$where = array (
				'id' => $id 
		);
		$info = $m->GetOneVideo ( $where );
		$types = explode(',', $info['type']);
		$colid_1 = 0;
		$colid_2 = 0;
		foreach($types as $key=>$val){
			if($val=='0'){
				$colid_1 = 1;
			}
			if($val=='1'){
				$colid_2 = 1;
			}
		}
		$this->smarty->assign ( 'info', $info );
		$this->smarty->assign ( 'colid_1', $colid_1 );
		$this->smarty->assign ( 'colid_2', $colid_2 );
		$this->smarty->assign("active_menu","res");
		$this->smarty->assign("active","pageview/index");
		$this->forward = "vedit";
	}
	/**
	 * 更新视频位信息
	 */
	public function vupdateAction() {
		$id = reqnum ( 'id', 0 );
		$ispost = reqnum ( 'ispost', 0 );
		$m = new PSys_ResModel ();
		if ($ispost == 1) {
			
			$vname = reqstr ( 'vname' );
			$cast = reqstr ( 'cast' );
			$direcotr = reqstr ( 'direcotr' );
			$vpath = reqstr ( 'vpath' );
			$vimg = reqstr ( 'vimg' );
			$vimg_2 = reqstr ( 'vimg_2' );
			$sortid = reqstr ( 'sortid' );
			$vyear = reqstr ( 'vyear' );
			$area = reqstr ( 'area' );
			$iftj = reqstr ( 'iftj' );
			$flag = reqstr ( 'flag' );
			$vdesc = reqstr ( 'vdesc' );
			$vdetail = reqstr ( 'vdetail' );
			$types = reqarray ( 'type' );
			$colstr = ',';
			$type = reqarray ( 'colstr', array () );
			asort($type);
			if ($type && count ( $type ) > 0) {
				foreach ( $type as $v ) {
					$colstr .= $v . ',';
				}
			}
			$vtype ='';
			asort($types);
			if ($types && count ( $types ) > 0) {
				foreach ( $types as $v ) {
					$vtype.= $v . ',';
				}
			}
			
			$data = array (
					'vname' => $vname,
					'cast' => $cast,
					'direcotr' => $direcotr,
					'vpath' => $vpath,
					'vimg' => $vimg,
					'vimg_2' => $vimg_2,
					'type' => $vtype,
					'sortid' => $sortid,
					'area' => $area,
					'colid' => $colstr,
					'vyear' => $vyear,
					'iftj' => $iftj,
					'flag' => $flag,
					'vdesc' => $vdesc,
					'vdetail' => $vdetail,
					'ctime' => time () 
			);
			$colstr = '';
			$col = explode(",",$data['colid']);
			foreach($col as $key=>$val){
			if($val == '31'){
					$colstr.= '爱情.';
				}
			if($val == '32'){
					$colstr.= '战争.';
				}
			if($val == '33'){
					$colstr.= '喜剧.';
				}
			if($val == '34'){
					$colstr.= '科幻.';
				}
			if($val == '35'){
					$colstr.= '恐怖.';
				}
			if($val == '36'){
					$colstr.= '动作.';
				}
			if($val == '37'){
					$colstr.= '动画.';
				}
			if($val == '38'){
					$colstr.= '灾难.';
				}
			if($val == '39'){
					$colstr.= '剧情.';
				}
			if($val == '40'){
					$colstr.= '传记.';
				}
			if($val == '41'){
					$colstr.= '惊悚.';
				}
			if($val == '42'){
					$colstr.= '犯罪.';
				}
			}
			$data['colstr'] = $colstr;
			$result = array (
					'result' => 'ERROR' 
			);
			/*
			 * // 判断电影是否存在 $where = array ( 'vname' => $vname ); $isexit = $m->GetOneVideo ( $where ); if ($isexit) { MsgInfoConst::GetMsg ( 1043, $result ); return $result; }
			 */
			if ($id == 0) 			// 新增
			{
				$t = $m->AddVideo ( $data );
				// start 写操作日志
				$log = array (
						'logtype' => 71,
						'guid' => $_SESSION ['Cur_X_User'] ['id'],
						'ctime' => time (),
						'cip' => real_ip () 
				);
				$log ['logdetail'] = $_SESSION ['Cur_X_User'] ['username'] . "于" . date ( "Y-m-d H:i:s" ) . "[添加]电影" . $vname;
				$m->admin_syslog ( $log );
				// end 日志
				$result ['result'] = 'SUCCESS';
			} else 			// 更新
			{
				
				$w = array (
						'id' => $id 
				);
				$data ['utime'] = time ();
				
				/*
				//--------------物理文件删除
				//获取数据信息
				$oldData = $m->GetOne($w,'vimg,ivpath,avpath','rhi_video');
				$imgDir = VIDEO_PATH;
				$tDir = dirname(dirname($imgDir)) . '/' . 'files';
				$imgPath = $imgDir . $oldData['vimg'];
				$vDir = $tDir . '/' . 'movies';
				$ivPath = $vDir . '/' . $oldData['ivpath'];
				$avpath = $vDir . '/ . $oldData['avpath'];
				if(file_exists($ivPath) && $data['ivpath'] != $oldData['ivpath'])
				{
					unlink($ivPath);
				}
				if(file_exists($avpath) && $data['avpath'] != $oldData['avpath'])
				{
					unlink($avpath);
				}
				*/				
				
				$m->UpdateVideo ( $data, $w );
				// start 写操作日志
				$log = array (
						'logtype' => 71,
						'guid' => $_SESSION ['Cur_X_User'] ['id'],
						'ctime' => time (),
						'cip' => real_ip () 
				);
				$log ['logdetail'] = $_SESSION ['Cur_X_User'] ['username'] . "于" . date ( "Y-m-d H:i:s" ) . "[编辑]电影" . $vname;
				//$m->admin_syslog ( $log );
				// end 日志
				$result ['result'] = 'SUCCESS';
			}
			
			return $result;
		}
	}
	
	/**
	 * 删除电影
	 */
	public function vdeleteAction() {
		$m = new PSys_ResModel ();
		if (is_array ( $_GET ['id'] )) {
			$id = reqarray ( 'id', array () );
			foreach ( $id as $v ) {
				$where = array (
						'id' => $v 
				);
				$data = $m->GetOneVideo ( $where );
				
				
				//在放开注释是得修改表名
				$imgName = $data['vimg'];
				$avName = $data['avpath'];
				$ivName = $data['ivpath'];
				//组建路径
				$imgPath = VIDEO_PATH . $imgName;
				//删除物理图片文件
				if(file_exists($imgPath))
				{
					unlink($imgPath);
				}
				//删除视频文件
				$vPath = dirname(dirname(VIDEO_PATH)) . '/' . 'files' . '/';
				if(file_exists($vPath . $avName))
				{
					unlink($vPath . $avName);
				}
				if(file_exists($vPath . $ivName))
				{
					unlink($vPath . $ivName);
				}							
				
				$m->DelOneVideo ( $where );
				// start 写操作日志
				$log = array (
						'logtype' => 71,
						'guid' => $_SESSION ['Cur_X_User'] ['id'],
						'ctime' => time (),
						'cip' => real_ip () 
				);
				$log ['logdetail'] = $_SESSION ['Cur_X_User'] ['username'] . "于" . date ( "Y-m-d H:i:s" ) . "[删除]电影" . $data ['vname'];
				// $m=new PSys_SyslogModel();
				// $m->admin_syslog($log);
				$m->admin_syslog ( $log );
				// end 日志
			}
			header ( "Location:/res/vlist" );
		} elseif (is_numeric ( $_GET ['id'] )) {
			$id = reqnum ( 'id', '0' );
			$where = array (
					'id' => $id 
			);
			$data = $m->GetOneVideo ( $where );
			$m->DelOneVideo ( $where );
			// start 写操作日志
			$log = array (
					'logtype' => 71,
					'guid' => $_SESSION ['Cur_X_User'] ['id'],
					'ctime' => time (),
					'cip' => real_ip () 
			);
			$log ['logdetail'] = $_SESSION ['Cur_X_User'] ['username'] . "于" . date ( "Y-m-d H:i:s" ) . "[删除]电影" . $data ['vname'];
			$m->admin_syslog ( $log );
			// end 日志
			header ( "Location:/res/vlist" );
		} else {
			echo "<script>alert('id 为空');window.location.href='/res/vlist';</script>";
		}
	}
	/**
	 * 根据appcol获取最大的appid
	 */
	public function getmaxappidAction($appcol){
		//$appcol = reqnum('appcol',0);
		if(!$appcol){
			return array('status'=>'empty appcol') ;
		}
		$m = new PSys_ResModel();
		$appid = $m->GetAppid($appcol);
		if($appid){
			$appid = $appid + 1;
			return $appid;
		}
		
	}
	/**
	 * 安装包文件和对应图片的上传
	 */
public function uploadfileAction(){	
		//$stateArray = array();
		//读取memcache缓存信息
		//$memcache = XMemCache::GetInstance();
		//$status = $memcache->Get('Cur_X_User');
		//if(empty($_SESSION['Cur_X_User']['id']))
		//{
		//	die('请先登录');	
		//}
		//上传错误提示
		$errorMsg = array(
			'0'=>'文件上传成功',
			'1'=>'文件超出了服务器配置大小',
			'2'=>'文件超出了表单配置大小',
			'3'=>'仅部分文件上传',
			'4'=>'没有找到上传文件',
			'5'=>'上传文件大小为零',
			'6'=>'未找到临时文件夹',
			'7'=>'临时文件夹写入失败',
			'8'=>'服务器文件上传扩展未开启',
			'9'=>'上传图片规格不符合要求',
			'10'=>'存放文件夹建立失败',
			'11'=>'上传文件移动失败'
		);
		//引入图片处理类		
		require_once COMMON_PATH."XThumb.php";
		$imgthumb = new XThumb();
		$num = reqnum ( "num", 0 );
		$appcol = reqstr('appcol',1);
		$appid = reqstr('appid','');
		$appid = $appid ? $appid : $this->getmaxappidAction($appcol);
		//返回数据
		$arr = array();
		//json数据返回
		$returnJson = '';
		$error = true;
		//上传文件目录设置
		if($appcol == 2){
			$root = APP_PATH;
		}elseif ($appcol == 1){
			$root = GAME_PATH;
		}
		define('ROOT',$root);
		$flag = isset($_POST['flag']) ? $_POST['flag'] : '';
		if(!$flag)
		{
			return;
		}
		switch($flag)
		{
			//logo上传
			case 'logo':
				$logo = isset($_FILES['file']) ? $_FILES['file'] : '';
				if($logo['error'] == 0)
				{
					$dir = ROOT.$appid;
					$img_info = getimagesize($logo['tmp_name']);
					//图片比例限制
					$radio = floor(($img_info[0]/$img_info[1])*10);
					if(!($radio>9 && $radio<11))
					{
						$error = false;
						$msg = $msg ? $msg . ',' . $logo['tmp_name'] . $errorMsg[9] : $logo['tmp_name'] . $errorMsg[9];
					}else{	
						if (!is_dir($dir))
						{
							if(!mkdir($dir,0777,true))
							{
								$error = false;
								$msg = $msg ? $msg . ',' . $logo['tmp_name'] . $errorMsg[10] : $logo['tmp_name'] . $errorMsg[10];
							}else{
								//获取后缀名
								//$ext = strrchr($logo['name'],'.');
								$ext = '.png';
								$filename = 'logo'.$ext;
								//文件名及地址组装
								$path = $dir.'/'.$filename;
								$arr['img_name'] = str_replace(ROOT,'/',$path);
								if(!move_uploaded_file($logo['tmp_name'], $path))
								{
									$error = false;
									$msg = $msg ? $msg . ',' . $logo['tmp_name'] . $errorMsg[11] : $logo['tmp_name'] . $errorMsg[11];
								}
								else 
								{
									$imgthumb->imgZoom($path,150,150,$path);
								}	
							}
						}else{
								//获取后缀名
								//$ext = strrchr($logo['name'],'.');
								$ext = '.png';
								$filename = 'logo'.$ext;
								//文件名及地址组装
								$path = $dir.'/'.$filename;
								$arr['img_name'] = $filename;
								if(!move_uploaded_file($logo['tmp_name'], $path))
								{
									$error = false;
									$msg = $msg ? $msg . ',' . $logo['tmp_name'] . $errorMsg[11] : $logo['tmp_name'] . $errorMsg[11];
								}
								else 
								{
									$imgthumb->imgZoom($path,150,150,$path);
								}	
						}
					}
				}
				else if($logo['error'] > 0)							//-------------logo文件上传出错
				{
					$error = false;
					$msg = $msg ? $msg . ',' . $logo['tmp_name'] . $errorMsg[$file['error']] : $logo['tmp_name'] . $errorMsg[$file['error']];
					$arr['result'] = 'error';
					$arr['msg'] = $errorMsg[$logo['error']];
				}
				break;
			//安装文件上传
			case 'installfile':
				//安装文件
				$installfile = isset($_FILES['file']) ? $_FILES['file'] : '';
				$arr['size'] = ceil($installfile['size']/1000000);
				if($installfile['error'] == 0)
				{
					$dir = ROOT.$appid;
					if (!is_dir($dir))
					{
						if(!mkdir($dir,0777,true))
						{
							$error = false;
							$msg = $errorMsg[10];
						}
					}
					$filename = $installfile['name'];
					$file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
					$appname = $appid.'.'.$file_ext;
					$path = $dir.'/'.$appname;
					$arr['img_name'] = $appname;
					if(!move_uploaded_file($installfile['tmp_name'], $path))
					{
						$error = false;
						$msg = $msg ? $msg . ',' . $installfile['tmp_name'] . $errorMsg[11] : $installfile['tmp_name'] . $errorMsg[11];
					}
				}
				else if($installfile['error'] > 0)					//-----------文件上传出错
				{
					$error = false;
					$msg = $msg ? $msg . ',' . $installfile['tmp_name'] . $errorMsg[$installfile['error']] : $installfile['tmp_name'] . $errorMsg[$installfile['error']];
					$arr['result'] = 'error';
					$arr['msg'] = $errorMsg[$installfile['tmp_name']];
				}
				break;
			//安卓封面
			case 'fmandroid':
				$error = true;
				$fmandroid = isset($_FILES['file']) ? $_FILES['file'] : '';
				if($fmandroid['error'] == 0)
				{
					$dir = ROOT . $appid;
					$dir = $dir.'/'.'android'.'/'.'ads'.'/';
					echo $dir;
					$img_info = getimagesize($fmandroid['tmp_name']);
					//计算长宽必烈
					$radio = floor(($img_info[0]/$img_info[1])*100);
					if(!($radio>185 && $radio<195))
					{
						$error = false;
						$msg = $msg ? $msg . ',' . $fmandroid['tmp_name'] . $errorMsg[9] : $fmandroid['tmp_name'] . $errorMsg[9];
					}else{
						if(!is_dir($dir))
						{
							if(!mkdir($dir,0777,true))
							{
								$error = false;
								$msg = $msg ? $msg . ',' . $fmandroid['tmp_name'] . $errorMsg[10] : $fmandroid['tmp_name'] . $errorMsg[10];
							}else{
								//获取扩展名
								$filename = $fmandroid['name'];
								$ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
			
								$filename = 'adshdpi';
								$fname =  $filename .'.'. $ext;
								$path = $dir .$fname;
								$arr['img_name'] = $fname;
								if(!move_uploaded_file($fmandroid['tmp_name'], $path))
								{
									$error = false;
									$msg = $msg ? $msg . ',' . $fmandroid['tmp_name'] . $errorMsg[11] : $fmandroid['tmp_name'] . $errorMsg[11];
								}
								else 
								{
									$imgthumb->imgZoom($path,480,255,$dir.'adshdpi'.$ext);
									$imgthumb->imgZoom($path,540,288,$dir.'adsmdpi'.$ext);
									$imgthumb->imgZoom($path,720,380,$dir.'adsxhdpi'.$ext);
									$imgthumb->imgZoom($path,1080,570,$dir.'adsxxhdpi'.$ext);
								}
							}
						}else{
							//获取扩展名
							$filename = $fmandroid['name'];
							$ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
		
							$filename = 'adshdpi';
							$fname =  $filename .'.'. $ext;
							$path = $dir .$fname;
							$arr['img_name'] = $fname;
							if(!move_uploaded_file($fmandroid['tmp_name'], $path))
							{
								$error = false;
								$msg = $msg ? $msg . ',' . $fmandroid['tmp_name'] . $errorMsg[11] : $fmandroid['tmp_name'] . $errorMsg[11];
							}
							else 
							{
								$imgthumb->imgZoom($path,480,255,$dir.'adshdpi'.$ext);
								$imgthumb->imgZoom($path,540,288,$dir.'adsmdpi'.$ext);
								$imgthumb->imgZoom($path,720,380,$dir.'adsxhdpi'.$ext);
								$imgthumb->imgZoom($path,1080,570,$dir.'adsxxhdpi'.$ext);
							}
						}
					}
				}
				else if($fmandroid['error'] > 0)						//文件上传出错
				{
					$error = false;
					$msg = $msg ? $msg . ',' . $fmandroid['tmp_name'] . $errorMsg[$fmandroid['error']] : $fmandroid['tmp_name'] . $errorMsg[$fmandroid['error']];
					$arr['result'] = 'error';
					$arr['msg'] = $errorMsg[$fmandroid['tmp_name']];
				}
				break;
			//ppt安卓
			case 'pptandroid':
				$error = true;
				$files = array();
				$arr['img_name'] = '';
				foreach($_FILES['file'] as $k=>$v)
				{
					foreach($v as $key=>$val)
					{
						$files[$key][$k] = $val;
					}
				}
				
				foreach($files as $key=>$file){
					if($file['error'] == 0)
					{
						//获取图片信息
						$img_info = getimagesize($file['tmp_name']);
						$radio = floor(($img_info[0]/$img_info[1])*100);
						if(!($radio>55 && $radio<61))
						{
							$error = false;
							$msg = $msg ? $msg . ',' . $file['tmp_name'] . $errorMsg[9] : $file['tmp_name'] . $errorMsg[9];
						}
						else
						{
							$dir = ROOT . $appid . '/';
							if (!is_dir($dir))
							{
								if(!mkdir($dir,0777,true))
								{
									$error = false;
									$msg = $msg ? $msg . ',' . $file['tmp_name'] . $errorMsg[10] : $file['tmp_name'] . $errorMsg[10];
								}
							}
										
							$filename = $file['name'];
							$ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
							$rand = rand(100, 999);
							$filename = $appid.'_'.date("YmdHis") . $rand .".".$ext;
							$path = $dir .$filename;
							$newImg = $filename;
							$arr['img_name'] = $newImg.';'.$arr['img_name'];
							//创建需要的文件目录
							$dir0 = $dir . 'android/' . 'hdpi/';
							$dir1 = $dir . 'android/' . 'mdpi/';
							$dir2 = $dir . 'android/' . 'xhdpi/';
							$dir3 = $dir . 'android/' . 'xxhdpi/';
							
							
							//echo $path;exit;
						    if(!move_uploaded_file($file['tmp_name'], $path))
							{
								$error = false;
								$msg = $msg ? $msg . ',' . $file['tmp_name'] . $errorMsg[11] : $file['tmp_name'] . $errorMsg[11];
							}
							else 
							{
								if(!is_dir($dir0))
								{
									if(!mkdir($dir0,0777,true))
									{
										$error = false;
										$msg = $msg ? $msg . ',' . $file['tmp_name'] . $errorMsg[10] : $file['tmp_name'] . $errorMsg[10];
									}
								}
								if (!is_dir($dir1))
								{
									if(!mkdir($dir1,0777,true))
									{
										$error = false;
										$msg = $msg ? $msg . ',' . $file['tmp_name'] . $errorMsg[10] : $file['tmp_name'] . $errorMsg[10];
									}
								}
								if (!is_dir($dir2))
								{
									if(!mkdir($dir2,0777,true))
									{
										$error = false;
										$msg = $msg ? $msg . ',' . $file['tmp_name'] . $errorMsg[10] : $file['tmp_name'] . $errorMsg[10];
									}										
								}
								if (!is_dir($dir3))
								{
									if(!mkdir($dir3,0777,true))
									{
										$error = false;
										$msg = $msg ? $msg . ',' . $file['tmp_name'] . $errorMsg[10] : $file['tmp_name'] . $errorMsg[10];
									}										
								}
								$imgthumb->imgZoom($path,336,560,$dir0.$filename);
								$imgthumb->imgZoom($path,378,672,$dir1.$filename);							
								$imgthumb->imgZoom($path,504,896,$dir2.$filename);								
								$imgthumb->imgZoom($path,756,1344,$dir3.$filename);
							}
						}
					}
					else if($file['error'] > 0)
					{
						$error = false;
						$msg = $msg ? $msg . ',' . $file['tmp_name'] . $errorMsg[$file['error']] : $file['tmp_name'] . $errorMsg[$file['error']];
						$arr['result'] = 'error';
						$arr['msg'] = $errorMsg[$file['tmp_name']];
					}
				}
				break;
			case 'fmios':
				//ios封面图片缩放及上传
				$error = true;
				$fmios = isset($_FILES['file']) ? $_FILES['file'] : '';
				if($fmios['error'] == 0)
				{
					$dir = ROOT . $appid;
					$dir = $dir.'/'.'ios'.'/'.'ads'.'/';
					$img_info = getimagesize($fmios['tmp_name']);
					
					//图片长宽必烈
					$radio = floor(($img_info[0]/$img_info[1])*100);
					if(!($radio>55 && $radio<65))
					{
						$error = false;
						$msg = $msg ? $msg . ',' . $fmios['tmp_name'] . $errorMsg[9] : $fmios['tmp_name'] . $errorMsg[9];
					}else{
						if(!is_dir($dir))
						{
							if(!mkdir($dir,0777,true))
							{
								$error = false;
								$msg = $msg ? $msg . ',' . $fmios['tmp_name'] . $errorMsg[10] : $fmios['tmp_name'] . $errorMsg[10];
							}else{
								//获取扩展名
								//$ext = strrchr($fmios['name'],'.');
								$ext = '.png';
								$filename = 'adsios';
								$path = $dir . $filename . $ext;
								$arr['img_name'] = $filename.$ext;
								if(!move_uploaded_file($fmios['tmp_name'], $path))
								{
									$error = false;
									$msg = $msg ? $msg . ',' . $fmios['tmp_name'] . $errorMsg[11] : $fmios['tmp_name'] . $errorMsg[11];
								}
								else 
								{
									$imgthumb->imgZoom($path,960,576,$dir.$filename.$ext);
								}
							}
						}else{
						//获取扩展名
								//$ext = strrchr($fmios['name'],'.');
								$ext = '.png';
								$filename = 'adsios';
								$path = $dir . $filename . $ext;
								$arr['img_name'] = $filename.$ext;
								if(!move_uploaded_file($fmios['tmp_name'], $path))
								{
									$error = false;
									$msg = $msg ? $msg . ',' . $fmios['tmp_name'] . $errorMsg[11] : $fmios['tmp_name'] . $errorMsg[11];
								}
								else 
								{
									$imgthumb->imgZoom($path,960,576,$dir.$filename.$ext);
								}	
						}
					}
				}
				else if($fmios['error'] > 0)
				{
					$error = false;
					$msg = $msg ? $msg . ',' . $fmios['tmp_name'] . $errorMsg[$fmios['error']] : $fmios['tmp_name'] . $errorMsg[$fmios['error']];
					$arr['result'] = 'error';
					$arr['msg'] = $errorMsg[$fmios['tmp_name']];
				}
				break;
			//ios  PPT1
			case 'pptios1':
				$error = true;
				$files = array();
				$arr['img_name'] = '';
				foreach($_FILES['file'] as $k=>$v)
				{
					foreach($v as $key=>$val)
					{
						$files[$key][$k] = $val;
					}
				}
				foreach($files as $key=>$file){
					if($file['error'] == 0)
					{
						//获取图片信息
						$img_info = getimagesize($file['tmp_name']);
						$radio = floor(($img_info[0]/$img_info[1])*100);
						if(!($radio>56 && $radio<60))
						{
							$error = false;
							$msg = $msg ? $msg . ',' . $file['tmp_name'] . $errorMsg[9] : $file['tmp_name'] . $errorMsg[9];
							//$this->showMsg("pptitips1",$img_info['name'] . $errorMsg[9]);
							
						}
						else
						{
							$dir = ROOT . $appid . '/' . 'ios' . '/'. 'shdpi' .'/';
							//echo $dir;exit;
							if (!is_dir($dir))
							{
								if(!mkdir($dir,0777,true))
								{
									$error = false;
									$msg = $msg ? $msg . ',' . $file['tmp_name'] . $errorMsg[10] : $file['tmp_name'] . $errorMsg[10];
								}
							}
							$filename =substr($file['name'], 0,strpos($file['name'], '.'));
							$ext = '.png';
							$path = $dir .$filename.$ext;
							$newImg = $filename.$ext;
							$arr['img_name'] = $newImg.';'.$arr['img_name'];
						    if(!move_uploaded_file($file['tmp_name'], $path))
							{
								$error = false;
								$msg = $msg ? $msg . ',' . $file['tmp_name'] . $errorMsg[11] : $file['tmp_name'] . $errorMsg[11];
							}
							else 
							{
								$imgthumb->imgZoom($path,780,1344,$dir.$filename.$ext);
							}
						}	
					}
					else if($file['error'] > 0)
					{
						$error = false;
						$msg = $msg ? $msg . ',' . $file['tmp_name'] . $errorMsg[$file['error']] : $file['tmp_name'] . $errorMsg[$file['error']];
						$arr['result'] = 'error';
						$arr['msg'] = $errorMsg[$file['tmp_name']];
					}					
				}
				break;
			case 'pptios2':
				$error = true;
				$files = array();
				$arr['img_name'] = '';
				foreach($_FILES['file'] as $k=>$v)
				{
					foreach($v as $key=>$val)
					{
						$files[$key][$k] = $val;
					}
				}	
				foreach($files as $key=>$file){
					if($file['error'] == 0)
					{
						//获取图片信息
						$img_info = getimagesize($file['tmp_name']);
						$radio = floor(($img_info[0]/$img_info[1])*100);
						if(!($radio>70 && $radio<75))								
						{
							$error = false;
							$msg = $msg ? $msg . ',' . $file['tmp_name'] . $errorMsg[9] : $file['tmp_name'] . $errorMsg[9];
						}
						else
						{
							$dir = ROOT . $appid . '/' . 'ios' . '/'. 'bhdpi' .'/';
							if (!is_dir($dir))
							{
								if(!mkdir($dir,0777,true))
								{
									$error = false;
									$msg = $msg ? $msg . ',' . $file['tmp_name'] . $errorMsg[10] : $file['tmp_name'] . $errorMsg[10];
								}
							}
							$filename =substr($file['name'], 0,strpos($file['name'], '.'));;
							//$ext = strrchr($file['name'],'.');
							$ext = '.png';
							$path = $dir .$filename.$ext;
							$newImg = $filename.$ext;
							$arr['img_name'] = $newImg.';'.$arr['img_name'];	
						    if(!move_uploaded_file($file['tmp_name'], $path))
							{
								$error = false;
								$msg = $msg ? $msg . ',' . $file['tmp_name'] . $errorMsg[11] : $file['tmp_name'] . $errorMsg[11];
							}
							else 
							{
								$imgthumb->imgZoom($path,780,1080,$dir.$filename.$ext);
							}
						}	
					}
					else if($file['error'] > 0)
					{
						
						$error = false;
						$msg = $msg ? $msg . ',' . $file['tmp_name'] . $errorMsg[$file['error']] : $file['tmp_name'] . $errorMsg[$file['error']];
						$arr['result'] = 'error';
						$arr['msg'] = $errorMsg[$file['tmp_name']];
					}
				}
			break;
		}
		if($error)
		{
			$arr['result'] = 'success';
			$arr['msg'] = $errorMsg['0'];
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
	 * 专辑图片上传
	 */
	public function upalbumAction(){
		$dir = ALBUM_PATH;
		if (!is_dir($dir)) {
			if(!mkdir($dir,0777,true)){
				return;
			}
		}
		$vpath = isset($_FILES['small'])?$_FILES['small']:'';
		$vimg = isset($_FILES['big'])?$_FILES['big']:'';
		if($vpath && ($vpath['error']==0)){
				$dir1 = $dir. '/' .'small_pic';
				if(!is_dir($dir1))
				{
					if(!mkdir($dir1,0777,true))
					{
						return;
					}
				}
				if(!move_uploaded_file($vpath['tmp_name'], $dir1 . '/' . $vpath['name'])){
					return;
				}
			}
		if($vimg && ($vimg['error']==0)){
				$dir2 = $dir. '/' .'big_pic';
				if(!is_dir($dir2))
				{
					if(!mkdir($dir2,0777,true))
					{
						return;
					}
				}
				if(!move_uploaded_file($vimg['tmp_name'], $dir2 . '/' . $vimg['name'])){
					return;
				}
			}
		echo 'SUCCESS';
		exit;
	}
	/**
	 * 同步更新
	 * Enter description here ...
	 */
	
	
	public function syncupdateAction()
	{
		$ispost = reqnum('ispost', 0);
		if($ispost)
		{
			$id = reqnum('id',0);
			$m = new PSys_ResModel();
			$return = $m->syncDb($id,false);
			return $return;			
		}
	}
	
	/**
	 * 同步删除
	 * Enter description here ...
	 */
	
	public function syncdelAction()
	{
		$ispost = reqnum('ispost', 0);
		if($ispost)
		{
			$id = reqnum('id',0);
			$m = new PSys_ResModel();
			$return = $m->syncDb($id,true);
			return $return;			
		}
	}
	
	/***
	 * 导入数据
	 */
	public  function importAction()
	{
		/*设置上传路径*/
		define('INSTALL',str_replace('\\','/',dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
		$savePath = INSTALL.'/www/traindata_appui/imgs/video/Excel/';
		$action = !empty($_GET['action']) ? trim($_GET['action']) : '';
		$file = !empty($_GET['file']) ? trim($_GET['file']) : '';
		if($action == 'del'){
		  $myfile = $savePath . $file;
		  if (file_exists($myfile)) {
		   		if(unlink($myfile)){
		   			echo '<script type="text/javascript">alert("删除成功;返回列表");window.location.href="/res/import";</script>';
		   			exit;
		   		}
		   		echo '<script type="text/javascript">alert("删除失败;返回列表");window.location.href="/res/import";</script>';
		   		exit;    
		  }else{
		  		echo '<script type="text/javascript">alert("文件不存在;返回列表");window.location.href="/res/import";</script>';
		   		exit;
		  }
		}elseif (!empty($_FILES ['file_stu']['name']) or $action=='import'){
			if($action == 'import' and !empty($file)){
				$file_name = $file;
			}elseif(!empty($_FILES ['file_stu']['name'])){
				$tmp_file = $_FILES ['file_stu'] ['tmp_name'];
				$file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
				$file_type = $file_types [count ( $file_types ) - 1];
	
				/*判别是不是.xls文件，判别是不是excel文件*/
				if (strtolower ( $file_type ) != "xls")
				{
					echo '<script type="text/javascript">alert("不是Excel文件，重新上传");window.location.href="/res/import";</script>';
		   			exit;
				}
	
				if(!is_dir($savePath))
				{
					mkdir($savePath,0777,true);
				}
				/*以时间来命名上传的文件*/
				$str = date ( 'Ymdhis' );
				$file_name = $str . "." . $file_type;
				/*是否上传成功*/
				if (!move_uploaded_file($tmp_file, $savePath.$file_name))
				{
					echo '<script type="text/javascript">alert("上传失败，重新上传");window.location.href="/res/import";</script>';
					exit;
				}
				
			}
			$res = $this->read ( $savePath . $file_name );
			
			/*对生成的数组进行数据库的写入*/
			$errmsg = '';
			$noimpor = '';
			$okmsg = '';
			$m = new PSys_ResModel ();
			foreach ( $res as $k => $v )
			{
					if ($k >1)
					{
						$data = array (
							'vname' => $v['1'],				//电影名称
							'cast' => $v['6'],				//中文类型
							'cast' => $va['10'],			//主演
							'direcotr' => $v['11'],			//导演
							'vpath' => $v['2'],				//电影片源
							'vimg' => $v['3'],				//火伴封面
							'sortid' => $v['14'],			//排序,越大越靠前
							'area' => $v['9'],				//地区
							'colid' => $v['7'],				//电影类型id
							'vyear' => $v['8'],				//所属年代
							'iftj' => $v['12'],				//1推荐,0不推荐
							'flag' => $v['13'],				//1显示，0隐藏
							'vdesc' => $v['4'],				//摘要
							'vdetail' => $v['5'],			//视频详情	
							'ctime' => time (),				//创建时间
							'vimg_2' => $v['19'],			//火车站封面
							'type' => $v['20']				//放映位置
						);
					$svl = $m->GetOne(array('vname' => $v[1]),'*','rhi_videos');
					if(empty($svl)){
						$m->AddVideo ( $data );
						echo '<script>alert("导入成功,自动过滤从复数据！");window.location.href="/res/vlist"</script>';
						exit;
					}else{
						echo '<script type="text/javascript">alert("'.$v['1'].'在数据库已存在，请重新上传!");window.location.href="/res/import";</script>';
						exit;
					}
				}
			}
			if($noimpor){
				$logpas = ERRLOG_PATH.'video'.DIRECTORY_SEPARATOR.date('Ymd').DIRECTORY_SEPARATOR;
				if(!is_dir($logpas))
				{
					mkdir($logpas,0777,true);
				}
				$log = $logpas.'video_noimport.log';
				$str = $noimpor. "\r\n";
				error_log ( $str, 3, $log );
			}
			if($errmsg){				
				$logpas = ERRLOG_PATH.'video'.DIRECTORY_SEPARATOR.date('Ymd').DIRECTORY_SEPARATOR;
				if(!is_dir($logpas))
				{
					mkdir($logpas,0777,true);
				}
				$log = $logpas.'video_error.log';
				$str = $errmsg. "\r\n";
				error_log ( $str, 3, $log );
			}
			echo '<script>alert("导入成功,自动过滤从复数据！");window.location.href="/res/vlist"</script>';
			exit;
		}else{
			//开始运行
			$files = $this->listDir($savePath);
			$this->smarty->assign('files',$files);
			$this->smarty->assign("active_menu","res");
			$this->smarty->assign("active","pageview/index");
			$this->forward = "import";
		}
	}
	//文件遍历
	function listDir($dir)
	{
		$files = array();
	    if(is_dir($dir))
	    {
	        if ($dh = opendir($dir))
	        {
	            while (($file = readdir($dh)) !== false)
	            {
					if($file!="." && $file!="..")
	                {
	                    $files[] = $file;
	                }
	            }
	            closedir($dh);
	        }
	    }
	    return $files;
	}	
	/**
	* 读取excel $filename 路径文件名 $encode 返回数据的编码 默认为utf8
	*以下基本都不要修改
	*/
	public function read($filename,$encode='utf-8')
	{
		include_once PUBLIB_PATH."phpexcel".DIRECTORY_SEPARATOR."PHPExcel.php";
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

/**
	 * 上传四张全屏广告图片  */
	function uploadalbumAction(){
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
		$dir = ALBUM_PATH;
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
			$name = 'alb_'.uniqid().'.'.$fileType;
			
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
						if($arr['img_x']>='1024' || $arr['img_y']>='680'){
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
		}
		else
		{
			$arr['result'] = 'error';
			$arr['msg'] = $msg;
			$arr['num'] = $num;
		}
		//json返回
		$returnJson = json_encode($arr);
		echo "<script type='text/javascript'>window.parent.callbackFunction('".$returnJson."');</script>";
	}
	
}	

?>