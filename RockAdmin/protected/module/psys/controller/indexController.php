<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月4日
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:indexController.php
* 创建时间:下午3:03:54
* 字符编码:UTF-8
* 脚本语言:PHP
* 版本信息:$Id: indexController.php 639 2014-07-10 06:06:57Z tony_ren $
* 修改日期:$LastChangedDate: 2014-07-10 14:06:57 +0800 (周四, 10 七月 2014) $
* 最后版本:$LastChangedRevision: 639 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/controller/indexController.php $
* 摘    要:网站首页
*/

class indexController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
	}

	//后台首页
	public function indexAction()
	{	
		$this->smarty->assign('admin_name',$this->cur_user['realname']);
		$this->smarty->assign('username',$this->cur_user['username']);
		$this->forward = "index";
	}
	//头部
	public function headerAction()
	{
		$this->smarty->assign('admin_name',$this->cur_user['realname']);
		$this->forward = "header";
	}

	public function menuAction()
	{
		$curmenu = reqstr('m','account');

		$this->smarty->assign('curmenu',$curmenu);

		$this->forward = "menu";
	}

	function mainAction()
	{
		$this->forward = "main";
	}


}
?>
