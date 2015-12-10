<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月7日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:AbstractController.php                                                
* 创建时间:下午1:53:43                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id$                                                 
* 修改日期:$LastChangedDate$                                     
* 最后版本:$LastChangedRevision$                                 
* 修 改 者:$LastChangedBy$                                      
* 版本地址:$HeadURL$                                            
* 摘    要: 显示控制-抽象类                                                      
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
	public $isajax = false;
	
	public function __construct($prjname='psys')
	{
		global $G_X;

		$prjname = $prjname == '' ? $G_X['prjstr'] : $prjname;
		
		if(!in_array($prjname,array_keys($G_X['allow_project'])))
		{
			header("Location:/html/noproject.html");
			return;
		}
		if(reqnum("ajax",0) == 1)
		{
			$this->isajax = true;
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
		$this->cur_userid = isset($this->cur_user['id']) ? $this->cur_user['id'] : 0;
		$this->smarty->assign("cur_userid",$this->cur_userid);
		$this->smarty->assign("cur_x_user",$this->cur_user);
	}

	/**
	 * 设置模板信息
	 */
	public function SetTemplate($prjname)
	{
		global $G_X;

		$upperprjname = strtoupper($prjname); 
		
		$this->OptimizeSmarty(constant($upperprjname."_DEBUG"));
		
		$cur_views_path = constant($upperprjname."_VIEWS_PATH").DIRECTORY_SEPARATOR;
		$cur_viewsc_path = constant($upperprjname."_VIEWSC_PATH").DIRECTORY_SEPARATOR;
		$this->smarty->template_dir = $cur_views_path;
		$this->smarty->compile_dir = $cur_viewsc_path;
		
		$this->config = $G_X;
		
		$staticcss = sprintf('%s/style/%s/css/','',$G_X['template']);
		$staticjs  = sprintf('%s/style/%s/js/','',$G_X['template']);
		$staticimg = sprintf('%s/style/%s/images/','',$G_X['template']);

		$this->smarty->assign($prjname."_js",$staticjs);
		$this->smarty->assign($prjname."_img",$staticimg);
		$this->smarty->assign($prjname."_css",$staticcss);
		
		$this->smarty->assign($prjname."_base_url",constant($upperprjname."_BASE_URL"));
		$this->smarty->assign($prjname."_views_path",constant($upperprjname."_VIEWS_PATH"));

		$this->smarty->assign("cur_prj_va",$prjname);
	}
	
	/**
	 * 优化smarty模板，注意smarty_fix_include，如果要看到效果，最好清空一下views_c目录
	 * @param boolean $debug
	 */
	private function OptimizeSmarty($debug)
	{
		if($debug)
		{
			$this->smarty->force_compile = true;
		}else{
			$this->smarty->compile_check = false;
			$this->smarty->register_prefilter('smarty_fix_include');
		}		
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