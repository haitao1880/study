<?php
class cardController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function indexAction()
	{
		$cd = new Psys_CardModel();
		$c_list = $cd -> CardList(array(),0,0,'DISTINCT(corn)','corn asc');
		$this->smarty->assign('c_list',$c_list['allrow']);
		$this->forward = 'index';
	}
	
	/**
	 * 卡号列表
	 * @return array("allrow"=>array(),"allnum"=>0);:
	 */
	public function cardlistAction()
	{
		if(!$_SESSION['Cur_X_User']){
			return array('Code'=>-100);
		}
		$page = reqstr('page',1);
		$pagesize = reqstr('pagesize',10);
		$where['used'] = 0;
		$corn = reqstr('corn','');
		if ($corn) {
			$where['corn'] = $corn ;
		}
		$cd = new Psys_CardModel();
		$cardlist = $cd -> CardList($where,$page,$pagesize,'*','corn asc');
		$cardlist['Code'] = 1;
		$cardlist['pagesize'] = $pagesize;
		

		return $cardlist;
	}
	
	/**
	 * 出售充值卡
	 * @return multitype:number string |multitype:number string boolean
	 */
	public function saleAction()
	{
		if(!$_SESSION['Cur_X_User']){
			return array('Code'=>-100);
		}
		$id = reqstr('cardNo','');
		$rs = array('Code'=>-1,'result'=>'');
		
	
		if (!$id) {
			$rs['result'] = 'id为空！';
			return $rs;
		}
		
		$cd = new Psys_CardModel();
		$k = $cd->Sale($id);
		
		if ($k > 0) {
			$rs['Code'] = 0;
			$rs['result'] = true;
		}else{
			$rs['result'] = '售卖失败';
		}
		
		return $rs;
	}
}