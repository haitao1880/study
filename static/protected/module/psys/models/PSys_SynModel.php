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
* 创 建 者:Robin (Robin@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:访问统计模型层
*/
class PSys_SynModel extends PSys_AbstractModel{
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
	
	//获取一条sql详情
	public function getOnesql($where){
		$m = new PSys_SynRule();
		$sql = $m->getsqlDetails($where);
		return $sql;
	}
}