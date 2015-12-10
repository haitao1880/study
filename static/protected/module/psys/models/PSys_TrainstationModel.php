<?php
/**
 * 火车站统计model模块
 */
class PSys_TrainstationModel extends PSys_AbstractModel{
  	
  	protected $obj;
	public function __construct(){
		parent::__construct();
		$this->obj = new PSys_TrainstationRule();
	} 


	/**
     * 伙伴分类下载详情
     */
	public function DownTrainappInfo($sdate,$edate,$stationids){ 
		$res = $this->obj->DownTrainappInfo($sdate,$edate,$stationids);
		foreach($res as &$v){
			$v['stationid'] = $this->GetStationName($v['stationid']);
		}
		return $res;
	}

	/**
	 * 根据车站id获取车站名
	 */
	public function GetStationName($stationid){
		$res = $this->GetOne(array('id'=>$stationid),'stationname','rha_station');
		return $res['stationname'];
	}


}