<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月14日
* 文 件 名:{PSys_Menu}Rule.php
* 创建时间:13:55
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:daniel (daniel@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:访问统计rule层
*/
class PSys_MenuRule extends PSys_DbAbstractRule{
    /**
    *
    * 继承构造函数
    *
    * @Member public 
    * @author daniel
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
	public function __construct(){
		parent::__construct();
        $this->SetDb('rht_member');
        $this->SetTable("rht_menu");
	}
    
}
