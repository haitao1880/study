<?php
/**
 * Copyright(c) 2013
 * 日    期:2013年11月20日
 * 文 件 名:AbstractController.php
 * 创建时间:下午5:23:19
 * 字符编码:UTF-8
 * 版本信息:$Id: AbstractController.php 10 2014-06-13 07:03:39Z tony_ren $
 * 修改日期:$LastChangedDate: 2014-06-13 15:03:39 +0800 (周五, 13 六月 2014) $
 * 最后版本:$LastChangedRevision: 10 $
 * 修 改 者:$LastChangedBy: tony_ren $
 * 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/protected/publib/abstract/AbstractController.php $
 * 摘    要:显示控制-抽象类
 */

require_once(PUBLIB_PATH.'smarty'.DIRECTORY_SEPARATOR.'Smarty.class.php');

class AbstractController
{
	public $smarty;
	/**
	 * 当前模板名
	 * @var string
	 */
	public $forward;
	public $view_dir = "";//"admin".DIRECTORY_SEPARATOR
	/**
	 * 当前用户登陆信息
	 * @var array
	 */
	public $cur_user = null;
	public $config = array();
	/**
	 * 当前用户ID
	 * @var number
	 */
	public $cur_userid = "0";
	/**
	 * 错误号
	 * @var number
	 */
	public $errcode = 0;//
	public $error = null;//错误信息，主要在AJAX用
	/**
	 * 是否AJAX访问,请求的参数中含有ajax=1时就是AJAX请求
	 * @var boolean
	 */
	public $isajax = false;//
	
	public $ispost = false;
	
	
	/**
	 * 当前语言
	 * @var string en,de
	 */
	public $culture = 'en';

	public function __construct($prjname='puc')
	{
		global $G_X;

		$prjname = $prjname == '' ? $G_X['prjstr'] : $prjname;
		
		if(!in_array($prjname,array_keys($G_X['allow_project'])))
		{
			header("Location:".WEB_URL."html/noproject.html");
			return;
		}
		if(reqnum("ajax",0) == 1)
		{
			$this->isajax = true;
		}
		if(!empty($_POST)){
			$this->ispost = true;
		}
		$this->smarty = new Smarty();
		
		$this->SetUserInfo();
		$this->SetTemplate($prjname);
	}

	/**
	 * 设置用户信息
	 */
	protected function SetUserInfo()
	{
		$this->cur_user = XSession::Get("Cur_X_User");
		$this->cur_userid = $this->cur_user['id'];
		$this->culture	= isset($this->cur_user['culture']) ? $this->cur_user['culture'] : $this->culture;
		$this->smarty->assign("Cur_X_User",$this->cur_user);
	}

	/**
	 * 设置模板信息
	 */
	public function SetTemplate($prjname)
	{
		global $G_X;

		$upperprjname = strtoupper($prjname);    
		
		$cur_views_path = constant($upperprjname."_VIEWS_PATH").$this->culture.DIRECTORY_SEPARATOR;
		$cur_viewsc_path = constant($upperprjname."_VIEWSC_PATH").$this->culture.DIRECTORY_SEPARATOR;
		$this->smarty->template_dir = $cur_views_path;
		$this->smarty->compile_dir = $cur_viewsc_path;
		$this->smarty->left_delimiter = "{{";
		$this->smarty->right_delimiter = "}}";
		
		$this->config = $G_X;
		
		$staticcss = sprintf('%s/style/%s/%s',$G_X['imgdomain']['css'],$this->culture,$G_X['template']);
		$staticjs  = sprintf('%s/style/%s/%s',$G_X['imgdomain']['js'],$this->culture,$G_X['template']);
		$staticimg = sprintf('%s/style/%s/%s',$G_X['imgdomain']['img'],$this->culture,$G_X['template']);

		$this->smarty->assign("pava_js",$staticjs);
		$this->smarty->assign("pava_img",$staticimg);
		$this->smarty->assign("pava_css",$staticcss);
		
		$this->smarty->assign("pava_base_url",PAVA_BASE_URL);
		$this->smarty->assign("pava_views_path",PAVA_VIEWS_PATH);

		$this->smarty->assign("psys_base_url",PSYS_BASE_URL);
		$this->smarty->assign("psys_views_path",PSYS_VIEWS_PATH);

		$this->smarty->assign("cur_prj_va",$prjname);
		
	}
	
	/**
	 * 设置区域信息
	 * @param string $culture 区域：en,de
	 */
	protected function SetCulture($culture)
	{
		if(empty($culture))$culture = $this->culture;
		
		setlocale(LC_ALL, 'en_US.utf8', 'en_US.UTF8', 'en_US.utf-8', 'en_US.UTF-8');
		setlocale(LC_COLLATE, $culture.'.utf8', $culture.'.UTF8', $culture.'.utf-8', $culture.'.UTF-8');
		setlocale(LC_CTYPE, $culture.'.utf8', $culture.'.UTF8', $culture.'.utf-8', $culture.'.UTF-8');
		setlocale(LC_MONETARY, $culture.'.utf8', $culture.'.UTF8', $culture.'.utf-8', $culture.'.UTF-8');
		setlocale(LC_TIME, $culture.'.utf8', $culture.'.UTF8', $culture.'.utf-8', $culture.'.UTF-8');	

		ini_set('date.timezone', 'Etc/GMT');//Asia/Shanghai
		ini_set('date.default_latitude', 31.5167);
		ini_set('date.default_longitude', 121.4500);
	}

	public function indexAction()
	{
		$this->forward = "index";
	}

}
?>