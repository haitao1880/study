<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月08日
* 文 件 名:{PSys_Menu}Model.php
* 创建时间:13:46
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Daniel (daniel@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:访问统计模型层
*/
class PSys_MenuModel extends PSys_AbstractModel{
    /**
    *
    * 继承构造函数
    *
    * @access public 
    * @author daniel
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
	public function __construct(){
		parent::__construct();
	}
	
	//根据pid获取无限级分类
	public function getMenuTree($pid = 0,$cach = false){
		$tree = array();
		$treemenukey = 'treemenukey';
		$cache = XMemCache::GetInstance();
		$tree = $cache->Get($treemenukey);
		if(empty($tree) or $cach){
		    $tree = $this->getMeTree($pid);
			$cache->Set($treemenukey,$tree);
		}		
		return $tree;
	}
    public function getMeTree($pid = 0){
    	$treearr = array();
		$rows = $this->GetList(array('pid'=>$pid),'', '', '','*');
		if(!empty($rows)){
			foreach ($rows["allrow"] as &$v){
				$row = $this->getMeTree($v['id']);
				if(!empty($row)){
					$v['submenu'] = $row;
				}
			}		
			$treearr = $rows["allrow"];
		}				
		return $treearr;
    }  
}