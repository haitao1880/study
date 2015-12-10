<?php
/**
* Copyright(c) 2015
* 日    期:2015年04月17日
* 文 件 名:{PSys_Abstract}Controller.php
* 创建时间:11:10
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Nick (nick@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:系统管理抽象类
*/
class PSys_AbstractController extends AbstractController
{
	/**
	 * 当前路径
	 * @var array
	 */
	public $cur_path_arr = null;
	public function __construct() 
	{       	   	   
		parent::__construct("psys");
		$this->smarty->assign("cur_prj_var","psys");
                
	}
	
	public function SetTemplate($prjname)
	{
		parent::SetTemplate($prjname);
		global $G_X;
		$this->getMenu();
		
		$staticcss = sprintf("%s/style/%s/%s/css/",'',$this->culture,$G_X['template']);
		$staticjs  = sprintf("%s/style/%s/%s/js/",'',$this->culture,$G_X['template']);
		$staticimg = sprintf('%s/style/%s/%s/img/','',$this->culture,$G_X['template']);
		$staticimages = sprintf('%s/style/%s/%s/images/','',$this->culture,$G_X['template']);
        $staticxeditor = sprintf('%s/style/%s/%s/xeditor/','',$this->culture,$G_X['template']);
        $staticdatepicker = sprintf('%s/style/%s/%s/datePicker/','',$this->culture,$G_X['template']);
        $statictinybox = sprintf('%s/style/%s/%s/tinybox/','',$this->culture,$G_X['template']);
        $staticupload = sprintf('%s/style/%s/%s/upload/','',$this->culture,$G_X['template']);
        $staticchart = sprintf('%s/style/%s/%s/visualize/','',$this->culture,$G_X['template']);
	
		$this->smarty->assign("psys_js",$staticjs);
		$this->smarty->assign("psys_img",$staticimg);
		$this->smarty->assign("psys_images",$staticimages);
		$this->smarty->assign("psys_css",$staticcss);
        $this->smarty->assign("psys_xeditor",$staticxeditor);
        $this->smarty->assign("psys_datepicker",$staticdatepicker);
        $this->smarty->assign("psys_tinybox",$statictinybox);
        $this->smarty->assign("psys_upload",$staticupload);
        $this->smarty->assign("psys_chart",$staticchart);
        
	}
    
	public function getMenu(){
		global $G_X;
		$ac_menu = $G_X['modstr'].'/'.$G_X['actstr'];		
		if($G_X['modstr']=='ads' or $G_X['modstr']=='fullads') $G_X['modstr']='ads';
		$m = new  PSys_MenuModel();
		$ps = $m->getMenuTree(0);
		$ismenu = 'game';
		foreach ($ps as $k => &$v){
			foreach ($v['submenu'] as $uk => &$vs){
				if($vs['action'] == $G_X['modstr']){					
					$ismenu = $v['action'];
					$active_id = $vs['id'];
					break;
				}
			}
		}
		$this->smarty->assign("psys_menu",$ps);
		$this->smarty->assign("ismenu",$ismenu);
		$this->smarty->assign("active_menu",$G_X['modstr']);
		$this->smarty->assign("active_id",$active_id);
		$this->smarty->assign("ac_menu",$ac_menu);
	}
    
    /**
    *
    * 提交跳转页面
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param $str string 提示性语句
    * @param $time int 跳转时间
    * @param $url sting 跳转地址
    * @param $type string 是否成功   success or error
    * @return -
    *
    */
    public function jump(string $str, $type, $url, $time = 3){
        
        header("Location:/jump/index?message=".$str."&type=".$type."&url=".$url."&time=".$time);
    }
    
    /**
     *
     * @do 翻页公用
     *
     * @access public
     * @author Nick
     * @copyright rockhippo
     * @param $totalPageSize int 总数
     * @param $param array 参数
     * @param $nowPage int 当前页数
     * @param $pageSize int 每页多少条
     * @return pageview
     *
     */
    public function pageView($totalPageSize, $param, $nowPage = 1, $pageSize = 10){
    	
    	//参数字符串
    	$paramStr = "&n=".$pageSize;
    	
		foreach($param as $key=>$val){
			$paramStr .= "&".$key."=".$val;
		}
    	    	
    	$pageHtml = "<div class='pagination'><ul><li><a href='?p=1".$paramStr."'>First Page</a></li><li><a href='?p=%s".$paramStr."'>Pre Page</a></li>%s<li><a href='?p=%s".$paramStr."'>Next Page</a></li> <li><a href='?p=%s".$paramStr."'>End Page</a></li></ul></div>";
    	
    	//总页数
    	$totalPage = ceil($totalPageSize/$pageSize);
    	
    	//第一页
    	$firstPage = 1;
    	//最后一页
    	$endPage   = $totalPage;
    	
    	//上一页
    	$prevPage  = ($nowPage - 1) <= 0 ? 1 : $nowPage - 1;
    	//下一页
    	$nextPage  = ($nowPage + 1) >= $totalPage ? $totalPage : ($nowPage + 1);

    	//数字分页
    	$numPage = "";
    	
    	
    	
    	if($totalPage <= 5){

    		for($i=1;$i<=$totalPage;$i++){
    			if($i == $nowPage){
    				$numPage .= "<li class='active'><a href='javascript:void(0);'>$i</a></li>";
    			}else{
    				$numPage .= "<li><a href='?p=".$i.$paramStr."'>$i</a></li>";
    			}
    		}
    		
    	}else{
    		if($nowPage <= 2){
    			for($i=1;$i<=2;$i++){
    				if($i == $nowPage){
    					$numPage .= "<li class='active'><a href='javascript:void(0);'>{$i}</a></li>";
    				}else{
    					$numPage .= "<li><a href='?p=".$i.$paramStr."'>{$i}</a></li>";
    				}
    			}
    			if($nowPage == 2){
    				$numPage .= "<li><a href='?p=3".$paramStr."'>3</a></li>";
    			}
    			$numPage .= "...";
    			$numPage .= "<li><a href='?p=".($totalPage-1).$paramStr."'>".($totalPage-1)."</a></li>";
    			$numPage .= "<li><a href='?p=".$totalPage.$paramStr."'>$totalPage</a></li>";
    		}else if(($totalPage - $nowPage) <= 1){
    			$numPage .= "<li><a href='?p=1".$paramStr."'>1</a></li>";
    			$numPage .= "<li><a href='?p=2".$paramStr."'>2</a></li>";
    			$numPage .= "...";
    			
    			if($nowPage == ($totalPage - 1)){
    				$numPage .= "<li><a href='?p=".($totalPage - 2).$paramStr."'>".($totalPage - 2)."</a></li>";
    			}
    			
    			for($i=$totalPage-1;$i<=$totalPage;$i++){
    				if($i == $nowPage){
    					$numPage .= "<li class='active'><a href='javascript:void(0);'>{$i}</a></li>";
    				}else{
    					$numPage .= "<li><a href='?p=".$i.$paramStr."'>{$i}</a></li>";
    				}
    			}
    		}else{    			
    			$numPage .= "<li><a href='?p=1".$paramStr."'>1</a></li>";
    			if($nowPage - 1 != 2){
    				$numPage .= "..";
    			}
    			$numPage .= "<li><a href='?p=".($nowPage-1).$paramStr."'>".($nowPage-1)."</a></li>";
    			$numPage .= "<li class='active'><a href='?p=".$nowPage.$paramStr."'>$nowPage</a></li>";
    			$numPage .= "<li><a href='?p=".($nowPage+1).$paramStr."'>".($nowPage+1)."</a></li>";
    			if(($nowPage + 1) != ($totalPage - 1)){
    				$numPage .= "..";
    			}
    			$numPage .= "<li><a href='?p=".$totalPage.$paramStr."'>$totalPage</a></li>";
    			
    		}
    	}
    	
    	return sprintf($pageHtml,$prevPage,$numPage,$nextPage,$endPage);
    	
    }
    
    /**
    *
    * @do 操作记录
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function operateLogs($status){        
        GLOBAL $G_X;        
        //操作人
        $user = XSession::Get("TA_user");        
        $data = array();
        $data['userid'] = $user['id'];
        $data['username'] = $user['username'];
        $data['operate'] = $G_X['modstr'] . "-" . $G_X['actstr'];
        $data['values'] = var_export($_REQUEST,true);
        $data['logtime'] = time();
        $data['status'] = $status;
        
        $PSys_LogsModel = new PSys_LogsModel();
        $PSys_LogsModel->AddOne($data);
        
    }

	

}
