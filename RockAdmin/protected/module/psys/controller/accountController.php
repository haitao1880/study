<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月20日
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:accountController.php
* 创建时间:下午2:30:10
* 字符编码:UTF-8
* 脚本语言:PHP
* 版本信息:$Id: accountController.php 821 2014-07-20 09:50:48Z tony_ren $
* 修改日期:$LastChangedDate: 2014-07-20 17:50:48 +0800 (周日, 20 七月 2014) $
* 最后版本:$LastChangedRevision: 821 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/controller/accountController.php $
* 摘    要: 后台账号管理
*/
class accountController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
	} 


	/**
	 * 列表
	 */
	public function indexAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);

		$nt = new Psys_AdminUserModel();
		$list = $nt -> GetList('', 'id DESC',$page, $pagesize,"*");

		self::inidate($list['allnum'],$page,$pagesize,count($list['allrow']));

		$this->smarty->assign('list',$list['allrow']);
		$this->smarty->assign('psys_base_url',PSYS_BASE_URL);
		$this->forward = "index";
	}

	/**
	 * 添加管理员
	 */
	public function addAction()
	{
		$this->forward = "add";
	}
	//数据更新
	public function updateAction()
	{
		$id = reqnum('id', 0);
		$ispost = reqnum('ispost', 0);
		$model = new Psys_AdminUserModel();
		if($ispost == 1)
		{
			$username = reqstr('username');
			$passwd = reqstr('passwd');
			$realname = reqstr('realname');
			$corp = reqstr('corp');
			$depart = reqstr('depart');
			$flag = reqnum('flag',0);
			$data = array(
					'username' => $username,
					'passwd' => $passwd,
					'realname' => $realname,
					'qxlist' =>'',
					'corp' => $corp,
					'depart' => $depart,
					'flag' => $flag,
			);
			$result = array('result'=>'ERROR');
			if($id == 0)
			{
				if($username == '' || $passwd == '')
				{
					MsgInfoConst::GetMsg(1000, $result);
					return $result;
				}
			}
			else
			{
				if($username == '')
				{
					MsgInfoConst::GetMsg(1000, $result);
					return $result;
				}

			}
			$data['passwd'] = $model->EncrptPasswd($passwd);

			if($id == 0) //新增
			{
				$id = $model->AddOne($data);
				//start 写操作日志
				$log = array(
					'logtype' => 1,
					'guid'    => $_SESSION['Cur_X_User']['id'],
					'ctime'   => time(),
					'cip'     => real_ip(),
				);
				$log['logdetail'] = $_SESSION['Cur_X_User']['username']."于".date("Y-m-d H:i:s")."[添加]管理员帐号".$username;
				$model->admin_syslog($log);
				//end 日志
				$result['result'] = 'SUCCESS';
			}
			else  //更新
			{
				if(empty($passwd)) //密码为空 则保持原密码
				{
					unset($data['passwd']);
				}
				$w = array('id' => $id);
				$model->UpdateOne($data,$w);
				//start 写操作日志
				$log = array(
							 'logtype' => 1,
							 'guid'  => $_SESSION['Cur_X_User']['id'],
							 'ctime' => time(),
							 'cip' => real_ip(),
								);
				$log['logdetail'] = $_SESSION['Cur_X_User']['username']."于".date("Y-m-d H:i:s")."[编辑]管理员帐号".$username;
				$model->admin_syslog($log);
				//end 日志
				$result['result'] = 'SUCCESS';
			}

			return $result;
		}
	}
	//编辑管理员
	public function editAction()
	{
		$id = reqnum('id',0); //获取参数
		if (!$id) {
			echo 'empty id';
			exit;
		}
		$nt = new Psys_AdminUserModel();
		$where = array('id'=>$id);
		$info = $nt -> GetOne($where,"*");
		$this->smarty->assign('info',$info);
		$this->forward = "edit";

    }
	//删除管理员
	public function delAction()
	{
		$nt = new Psys_AdminUserModel();
		if(is_array($_GET['id']))
		{
			$id = reqarray('id',array());
			foreach($id as $v)
			{
				$where = array('id'=>$v);
				$loguser = $nt ->GetOne($where);
				$res = $nt -> DeleteOne($where);
				//start 写操作日志
				$log = array(
							 'logtype' => 1,
							 'guid'  => $_SESSION['Cur_X_User']['id'],
							 'ctime' => time(),
							 'cip' => real_ip(),
								);
				$log['logdetail'] = $_SESSION['Cur_X_User']['username']."于".date("Y-m-d H:i:s")."[删除]管理员帐号".$loguser['username'];
				$nt->admin_syslog($log);
				//end 日志
			}
			header("Location:/account/index");
		}
		elseif(is_numeric($_GET['id']))
		{
			$id  =reqnum('id','0');
			$where = array('id'=>$id);
			$loguser = $nt ->GetOne($where);
			$res = $nt -> DeleteOne($where);
			//start 写操作日志
				$log = array(
							 'logtype' => 1,
							 'guid'  => $_SESSION['Cur_X_User']['id'],
							 'ctime' => time(),
							 'cip' => real_ip(),
								);
				$log['logdetail'] = $_SESSION['Cur_X_User']['username']."于".date("Y-m-d H:i:s")."[删除]管理员帐号".$loguser['username'];
				$nt->admin_syslog($log);
				//end 日志
			header("Location:/account/index");
		}
		else
		{
			echo "<script>alert('id 为空');</script>";
		}

	}

	/**
	 * ajax提交
	 */
	public function loginAction()
	{
		$ispost = reqnum('ispost', 0);
		if($ispost == 1){
			$curcode = reqnum('code');
			$beforcode = XSession::Get("AdminLoginVcode");
			if ($curcode != $beforcode) {
				MsgInfoConst::GetMsg(1006, $result);
	 			return $result;
			}
	 		$registerName = reqstr('username', '');
	 		$password = reqstr('passwd', '');

	 		$result = array('result'=>'ERROR');

	 		if($registerName == '' || $password == '')
	 		{
	 			MsgInfoConst::GetMsg(1000, $result);
	 			return $result;
	 		}
	 		$memModel = new Psys_AdminUserModel();
	 		$userone = $memModel->Login($registerName, $password, $result);

	 		return $result;
		}
 		$this->forward = "login";
	}

	public function logoutAction()
	{
		XSession::Get("Cur_X_User",true);
		session_destroy();

		if($this->isajax)
		{
			return array("result"=>"SUCCESS");
		}else{
			header("Location:/account/login");
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

?>
