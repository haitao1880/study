<?php
/**
 * 车上统计展示
 */
class PSys_BusModel extends PSys_AbstractModel{
  
	public function __construct(){
		parent::__construct();
	} 


	/**
	 * 获取流程数据
	 */
	public function processdata($date,$hour,$cid,$key,$tb){
		$obj = new PSys_BusRule();
		return $obj->processdata($date,$hour,$cid,$key,$tb);
	}

	/**
	 * 获取流程数据
	 */
	public function processdata1($sdate,$edate,$cid,$key,$tb,$isgroup=0){
		$obj = new PSys_BusRule();
		return $obj->processdata1($sdate,$edate,$cid,$key,$tb,$isgroup);
	}


	/**
	 * 获取流程数据
	 */
	public function newprocessdata($date,$stationid,$key,$tb,$stationtype){
		$obj = new PSys_BusRule();
		return $obj->newprocessdata($date,$stationid,$key,$tb,$stationtype);
	}
    
    public function getselfappdata($where,$group,$field,$join,$tb){
        $obj = new PSys_BusRule();
		return $obj->getselfappdata($where,$group,$field,$join,$tb);
    }
    
    
}