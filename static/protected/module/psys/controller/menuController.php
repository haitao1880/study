<?php
/**
* Copyright(c) 2014
* 日    期:2014年10月18日
* 文 件 名:{jump}Controller.php
* 创建时间:12:15
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:daniel (daniel@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:仅用于跳转
*/
class menuController extends PSys_AbstractController{
    /**
    *
    * @do 继承构造函数
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
	public function __construct(){
		parent::__construct();
	}
    
    /**
    *
    * @do 跳转
    *
    * @access public 
    * @author dental
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function indexAction(){
    	$m = new PSys_MenuModel();	        
        $all = $m->getMenuTree();
		$this->smarty->assign("all",$all);       
        $this->forward = 'index';        
    }
    
	public function menulistAction(){
		$m = new PSys_MenuModel();		
		$pid = reqnum("pid",0);
        $page = reqnum('page',1);
		$pagesize = 20;
		if($pid==0){
			$where = '';
		}else{
        	$where = array();
        	$where["pid"] = $pid;
		}
    
        $order = " id ASC ";
        $result = $m->GetList($where, $order, $page, $pagesize, "*");
        if($result['allnum']%$pagesize){
            $last = floor($result['allnum']/$pagesize) + 1;
        }else{
           $last = $result['allnum']/$pagesize;
        }
        if($page > 1){
            $this->smarty->assign("pre",$page - 1);
        }else{
            $this->smarty->assign("pre",1);
        } 
        if($page == $last){
            $this->smarty->assign("next",$last);
        }else{
            $this->smarty->assign("next",$page + 1);
        }       
                			 
        $this->smarty->assign("last",$last);
        $this->smarty->assign("tree",$result["allrow"]);
        $this->smarty->assign("total_num",$result["allnum"]);
        $this->forward = "menulist";
	}
	public function ajaxEditAction(){
		$id = reqnum('id', 0);
		$m = new PSys_MenuModel();
		if($_POST['ispost']){
			$data = array();
			$data['action'] = reqstr('action');
			$data['name'] = reqstr('name');
			$data['class'] = reqstr('class');
			$id = reqnum('id',0);
			$data['pid'] = reqnum('pid', 0);
			if($data['action'] and $data['name'] and $id > 0){
				$where['id'] = $id;
				$ps = $m->UpdateOne($data, $where);
				if($ps){
					$ss = $m->getMenuTree(0,true);
					header('location:/menu/index');
					exit;
				}
			}
		}
		if(!empty($id)){
			$last = $m->GetOne(array('id'=>$id));
			$this->smarty->assign("last",$last);
			$all = $m->getMenuTree();
			$this->smarty->assign("all",$all);
		}
		$this->smarty->assign("action",'/menu/ajaxEdit');
		$this->forward = "ajaxEdit";		
	}
	public function ajaxAddAction(){
		$m = new PSys_MenuModel();
		if($_POST['ispost']){
			$data = array();
			$data['action'] = reqstr('action');
			$data['name'] = reqstr('name');
			$data['class'] = reqstr('class');
			$data['pid'] = reqnum('pid', 0);
			if($data['action'] and $data['name']){
				$ps = $m->AddOne($data);
				if($ps){
					$m->getMenuTree(0,true);
					header('location:/menu/index');
					exit;
				}
			}
		}
		$all = $m->getMenuTree();
		$this->smarty->assign("all",$all);
		$this->smarty->assign("action",'/menu/ajaxAdd');
		$this->forward = "ajaxEdit";	
	}
}