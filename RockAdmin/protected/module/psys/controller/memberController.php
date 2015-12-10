<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月11日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:memberController.php                                                
* 创建时间:下午2:11:39                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: memberController.php 3951 2014-08-27 08:59:08Z terry $                                                 
* 修改日期:$LastChangedDate: 2014-08-27 16:59:08 +0800 (周三, 27 八月 2014) $                                     
* 最后版本:$LastChangedRevision: 3951 $                                 
* 修 改 者:$LastChangedBy: terry $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/controller/memberController.php $                                            
* 摘    要: 会员管理                                                      
*/

class memberController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 会员列表
	 */
	public function indexAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		
		$nt = new Psys_MemberUserModel();
		$list = $nt -> GetList(array('flag'=>1), 'id DESC',$page, $pagesize,"*");

		self::inidate($list['allnum'],$page,$pagesize,count($list['allrow']));

		$this->smarty->assign('list',$list['allrow']);
		$this->smarty->assign('psys_base_url',PSYS_BASE_URL);
		$this->forward = "index";
	}
	
	/**
	 * 会员查询
	 */
	public function searchAction()
	{	
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$username = trim(reqstr('username',''));
		$email = trim(reqstr('email',''));
		$regtime = reqstr('regtime','');
		$logintime = reqstr('logintime','');

		if(!$username && !$email && !$regtime && !$logintime){
			$this->forward = "search";
			return;
		}
		$url = '';
		if ($username) {
			$url .= '&username='.urlencode($username);
		}
		if ($email) {
			$url .= '&email='.urlencode($email);
		}
		if ($regtime) {
			$url .= '&regtime='.urlencode($regtime);
		}
		if ($logintime) {
			$url .= '&logintime='.urlencode($logintime);
		}
		$m = new Psys_MemberUserModel();
		$list = $m->SearchList($username,$email,$regtime,$logintime,$page,$pagesize);
		
		// var_dump($list);
		// exit;
		self::inidate($list['allnum'],$page,$pagesize,count($list['allrow']));

		$this->smarty->assign('url',$url);
		$this->smarty->assign('list',$list['allrow']);
		$this->smarty->assign('psys_base_url',PSYS_BASE_URL);
		$this->forward = "search";
	}
	
		/**
	 * 会员锁定列表
	 */
	public function blockAction()
	{	
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		
		$nt = new Psys_MemberUserModel();

		$list = $nt -> GetList(array('flag'=>-2), 'id DESC',$page, $pagesize,"*");

		self::inidate($list['allnum'],$page,$pagesize,count($list['allrow']));

		$this->smarty->assign('list',$list['allrow']);
		$this->smarty->assign('psys_base_url',PSYS_BASE_URL);
		$this->forward = "index";
		$this->forward = "block";
	}
		/**
	 * 会员锁定
	 */
	public function lockAction(){
		$id = reqnum('id',0);
		if (!$id) {
			return;
		}
		$nt = new Psys_MemberUserModel();
		$res = $nt->UpdateOne(array('flag'=>-2),array('id'=>$id));
		// if($res){
		// 	echo '<script type="text/javascript">alert(\'锁定成功！\')</script>';
		// }
		header('location:/member/index');

	}
		/**
	 * 会员解锁
	 */
	public function keylockAction(){
		$id = reqnum('id',0);
		if (!$id) {
			return;
		}
		$nt = new Psys_MemberUserModel();
		$res = $nt->UpdateOne(array('flag'=>1),array('id'=>$id));
		header('location:/member/block');

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