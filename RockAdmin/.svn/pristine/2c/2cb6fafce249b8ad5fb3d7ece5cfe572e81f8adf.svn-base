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

class ipcController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 设备列表
	 */
	public function indexAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$obj = new Psys_IpcModel();
		$data = $obj -> GetList('', 'id DESC',$page, $pagesize,"*");
		self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
		
		$this->smarty->assign('list',$data['allrow']);
		$this->forward = "index";
	}
	
	/**
	 * 设备增加
	 */
	public function addAction()
	{
		$isadd = 0;
		if ($_POST) {
			//获取修改人信息
			$adminuser = XSession::Get('Cur_X_User') ;
			$_POST['cadminid'] = $adminuser['id'];
			$_POST['cadmin'] = $adminuser['realname'];
			$_POST['ctime'] = time();
			$obj = new Psys_IpcModel();
			$r = $obj->AddOne($_POST);
			$isadd = $r >0 ? 1 : -1;
		}
		$this->smarty->assign('isadd',$isadd);
		$this->forward = "add";
	}
	/**
	 * 设备修改
	 */
	public function editAction()
	{
		$ipcno = reqstr('id','');
		
		if (!$ipcno) {
			return ;
		}
		$isadd = 0;
		if($_POST)
		{
			$obj = new Psys_IpcModel();
			$rt = $obj->UpdateOne($_POST, array('ipcno'=>$ipcno));
			if ($rt > 0) {
				$isadd = 1;
			}else{
				$isadd = -2;
			}
			$r = $_POST;
		}else{
			$obj = new Psys_IpcModel();
			$r = $obj->GetOne(array('ipcno'=>$ipcno));

		}
		foreach ($r as $key => $var)
		{
			$this->smarty->assign($key,$var);
		}
		$this->smarty->assign('isadd',$isadd);
		$this->forward = "edit";
	}
	
	/**
	 * 设备状态
	 */
	public function statusAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		
		$obj = new Psys_IpcModel();
		$data= $obj-> GetList('', 'id DESC',$page, $pagesize,"*",'rha_ipcstatus');
		
		self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
		
		$this->smarty->assign('list',$data['allrow']);
		$this->forward = "status";
	}
	
	public function logAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		
		$obj = new Psys_IpcModel();
		$data= $obj-> GetList('', 'id DESC',$page, $pagesize,"*",'rha_ipclog');
		
		self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
		
		$this->smarty->assign('list',$data['allrow']);
		$this->forward = "log";
	}
	
	public function logaddAction()
	{
		$logdetail = reqstr('logdetail','');
		$ipcno = reqstr('ipcno','');
		if (!$logdetail) {
			return array('code'=>-1);
		}
		if (!$ipcno) {
			return array('code'=>-2);
		}
		
		$data = array(
			'logdetail'=>$logdetail,
			'ctime'=>time(),
			'cip'=>real_ip(),
			'user'=>$this->cur_user['realname'],
			'ipcno'=>$ipcno
		);
		$m = new Psys_IpcModel();
		$res = $m->AddOne($data,'rha_ipclog');
		if ($res > 0) {
			$data['id'] = $res;
			$data['ctime'] = date('Y-m-d H:i:S',$data['ctime']);
			return array('code'=>1,'data'=>$data);
		}else{
			return array('code'=>0);
		}
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
}