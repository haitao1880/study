<?php
/**
 * 车上统计展示
 */
class PSys_TrainstationRule extends PSys_DbAbstractRule{
    
	public function __construct(){
		parent::__construct();
		$this->SetDb('rha_admin');
	} 

	/**
     * 伙伴分类下载详情
     */
	public function DownTrainappInfo($sdate,$edate,$stationids){ 
		
		$sdate = str_replace('-','_',$sdate);
		$edate = str_replace('-','_',$edate);
		$sql = "SELECT date,stationid,SUM(dtime) as moviedown FROM rha_count_process WHERE (date BETWEEN ? AND ? ) AND stationid IN($stationids) AND  model = 'movie' AND action = 'trainDown' AND detail = 'uv' GROUP BY date,stationid";

		$movie = $this->Query($sql,array($sdate,$edate));

		$sql_activity = "SELECT date,stationid,COUNT(DISTINCT phone) as activity FROM rha_spread_trainapp WHERE (date BETWEEN ? AND ? ) AND stationid IN($stationids) and action='spreadapp' and numtype='uv' GROUP BY date,stationid";
		$activity = $this->Query($sql_activity,array($sdate,$edate));

		$i = 0;
		$j = 0;
		$res = array();
		$res1 = array();
		if (empty($activity)) {
			foreach($movie as &$mvv){
				$mvv['date'] = str_replace('_','-',$mvv['date']);
				$mvv['stationid'] = $mvv['stationid'];
				$mvv['total'] = $mvv['moviedown'];
				$mvv['movie'] = $mvv['moviedown'];
				$mvv['activity'] = 0;
				unset($mvv['movie']);
			}			
			return $movie;

		}	
		foreach($movie as $mv){
			foreach($activity as $at){
				if ($mv['date'] == $at['date'] && $mv['stationid'] == $at['stationid']) {
					$res[$i]['date'] = str_replace('_','-',$mv['date']);
					$res[$i]['stationid'] = $mv['stationid'];
					$res[$i]['total'] = $mv['moviedown'] + $at['activity'];
					$res[$i]['movie'] = $mv['moviedown'];
					$res[$i]['activity'] = $at['activity'];
					$i++;
					
				}else{					
					$res1[$j]['date'] = str_replace('_','-',$mv['date']);
					$res1[$j]['stationid'] = $mv['stationid'];
					$res1[$j]['total'] = $mv['moviedown'] + 0;
					$res1[$j]['movie'] = $mv['moviedown'];
					$res1[$j]['activity'] = 0;			
					$j++;
								
				}				

			}

		}
		//判断是多站点还是单站点
		$stationnum = count(explode(',',$stationids));
		
		foreach($res1 as $k=>&$v){
			foreach($res as $v1){
				if ($stationnum < 2) {
					if ($v['date'] == $v1['date']) {
						unset($res1[$k]);
					}
				}else{
					if ($v['date'] == $v1['date'] && $v['stationid'] == $v1['stationid']) {
						unset($res1[$k]);
					}
				}
				
			}			
		}		
		
		$temp = array();
		foreach($res1 as $v){
			if (!in_array($v,$temp)) {
				$temp[] = $v;
			}

		}

		$resdata = array_merge($res,$temp);
		$sort = array(  
		        'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
		        'field'     => 'date',       //排序字段  
		);  
		//数组排序
		$arrSort = array();  
		foreach($resdata AS $uniqid => $row){  
		    foreach($row AS $key=>$value){  
		        $arrSort[$key][$uniqid] = $value;  
		    }  
		}
		if($sort['direction']){  
		    array_multisort($arrSort[$sort['field']], constant($sort['direction']), $resdata);  
		}		

		return $resdata;
	}
    
}
