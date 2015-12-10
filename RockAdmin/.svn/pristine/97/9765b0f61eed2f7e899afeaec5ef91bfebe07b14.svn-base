<?php
/**                                  
* 摘    要: 设备管理                                                      
*/

class Psys_SmsModel extends Psys_AbstractModel
{
	public function __construct()
	{
		parent::__construct();
	}
   //添加查寻数据到数据库
	public function addSmsStatistics($stime,$etime,$num,$type){	
		$return = array();	
		//获取时间内短信充值量
		$rule = new Psys_SmsRule();
		$pnum = $rule->getSmsRechargeNum($stime,$etime,$type);
		
		//获取发送短信量		
		$idcnum = $rule->getSmsIdclogNum($stime,$etime,$type);
		$this->SetDb("db");
		$numes = $num + $pnum - $idcnum;
		//如果时间内短信发生变化须记录
		if($pnum > 0 or $idcnum > 0){
			$data = array('pnum'=>$pnum,'cnum'=>$idcnum,'numes'=>$numes,'stime'=>$stime,'etime'=>$etime,'type'=>$type);
			$id = $this->AddOne($data,'rha_smsstatistics');
			$return['id'] = $id;
		}
		$return['numes'] = $numes;
		return $return;
	}
	//获取统计充值短信量
	public function getSmsStatistics(){
		$data = array();
		$type = array(1,2,3);
		foreach ($type as $v){
			$info = $this -> GetList('type='.$v, 'etime DESC',1, 1,"*",'rha_smsstatistics');
			$data[$v] = $info['allrow'][0];
		}
		return $data;
	}
//获取时间内短信发送量
	public function getSmsIdclogList($stime,$etime,$type = 1,$username,$appk,$code,$page,$pagesize)
	{
		$this->SetDb("db-rht_idc");
		$where = '1=1';
		if($type > 0){
			$where .= ' and type='.$type;
		}
		if($stime > 0){
			$where .= ' and ctime > '.$stime;
		}
		if($etime > 0){
			$where .= ' and ctime <= '.$etime;
		}
		if(!empty($username)){
			$where .= ' and username = '.$username;
		}
		if(!empty($appk)){
			$where .= ' and appkey = "'.$appk.'"';
		}
		if(!empty($code)){
			$where .= ' and code = "'.$code.'"';
		}
		//echo $where;exit;
		$list = $this -> GetList($where, 'ctime DESC',$page, $pagesize,"*",'rhi_smsclog');
		return $list;
	}
}

?>