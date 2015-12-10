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
* 创 建 者:Nick (nick@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:仅用于跳转
*/
class jumpController extends PSys_AbstractController{
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
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function indexAction(){
        
        $data['message']   = reqstr('message','');
        if($data['message'] == 'privilege') $data['message'] = 'You have no privilege.';
    	$data['type']  = reqstr('type','');
        $data['Url'] = $data['jumpUrl']  = str_replace("|","&",reqstr('url','/index/index'));//若多参数则改为|连接
        $data['waitSecond']  = reqstr('time',3);
        
        $this->smarty->assign('data',$data);
        
        $this->forward = 'index';
        
    }

}