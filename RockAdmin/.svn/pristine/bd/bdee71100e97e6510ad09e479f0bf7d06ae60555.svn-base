<?php
class Psys_CardModel extends Psys_AbstractModel
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * 充值卡列表
	 * @param array $where
	 * @param number $page
	 * @param number $pagesize
	 * @param string $field
	 * @param string $order
	 * @param string $tb
	 * @return array("allrow"=>array(),"allnum"=>0);:
	 */
	public function CardList(array $where,$page=1,$pagesize=10,$field='*',$order='corn desc',$tb='dpt_serialnumbers')
	{
		return $this->GetList($where,$order,$page,$pagesize,$field,$tb);
	}
	
	public function Sale($id)
	{	

		return $this->UpdateOne(array('used'=>1), array('id'=>$id),'dpt_serialnumbers');
	}
}