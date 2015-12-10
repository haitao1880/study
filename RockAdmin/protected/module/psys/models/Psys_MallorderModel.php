<?php
/**
 * 摘    要:积分商城订单
 */

class Psys_MallorderModel extends Psys_AbstractModel
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function UpdateOrderFlag($orderid,$flag){
		$data = array();
		$data['flag'] = $flag;
		$data['utime'] = $_SERVER['REQUEST_TIME'];
		$where = array('orderid'=>$orderid);
		$num = $this->UpdateOne($data, $where);
		$result = array();
		if($num){
			$result['result'] = 'SUCCESS';
			$result['msg'] = '更新成功';
		}else{
			$result['result'] = 'ERROR';
			$result['msg'] = '更新成功';
		}		
		return $result;
	}
}