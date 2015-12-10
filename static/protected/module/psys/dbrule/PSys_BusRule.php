<?php
/**
 * 车上统计展示
 */
class PSys_BusRule extends PSys_DbAbstractRule{
    
	public function __construct(){
		parent::__construct();
        $this->SetDb('bus_db');
	} 

	/**
	 * 获取流程数据
	 */
	public function processdata($date,$hour,$cid,$key,$tb){
		$parms = array($date);

		$sql = "SELECT count(DISTINCT usermac) AS $key FROM $tb where date='$date' ";
		if ($hour) {
			$sql .= " and hour='$hour'";			
		}

		if ($cid) {
			$sql .= " and carid in ($cid)";			
		}
		// echo $sql;
		$data = $this->Query($sql);			
		return (int)$data[0][$key];

		/*$parms = array($date);

		$sql = "SELECT sum(wifi_num) as wifi_num,sum(ad1_num) as ad1_num,sum(reg_page_num) as reg_page_num,sum(send_code_num) as send_code_num,sum(reg_success_num) as reg_success_num,sum(ad2_num) as ad2_num,sum(sindex_num) as sindex_num,sum(down_trainapp_num) as down_trainapp_num from rb_process where date='$date' ";
		if ($hour) {
			$sql .= " and hour='$hour'";			
		}

		if ($cid) {
			$sql .= " and cid in ($cid)";			
		}
		$data = $this->Query($sql);			
		return $data;*/
		
	}


	/**
	 * 获取流程数据1
	 */
	public function processdata1($sdate,$edate,$cid,$key,$tb,$isgroup=0){
	   
		if (!$isgroup) {
		  
            if($sdate && !$edate){
                $sql = "SELECT count(DISTINCT usermac) AS $key FROM $tb where date = '$sdate' AND carid IN ($cid)";		
            }elseif(!$sdate && $edate){
                $sql = "SELECT count(DISTINCT usermac) AS $key FROM $tb where date = '$edate' AND carid IN ($cid)";		
            }elseif($sdate && $edate){
                $sql = "SELECT count(DISTINCT usermac) AS $key FROM $tb where date BETWEEN '$sdata' AND $edate' AND carid IN ($cid)";
            }
          
		}else{
		  
            if($sdate && !$edate){
                $sql = "SELECT count(DISTINCT usermac) AS $key FROM $tb where date = '$sdate' AND carid = '$cid'";		
            }elseif(!$sdate && $edate){
                $sql = "SELECT count(DISTINCT usermac) AS $key FROM $tb where date = '$edate' AND carid = '$cid'";		
            }elseif($sdate && $edate){
                $sql = "SELECT count(DISTINCT usermac) AS $key FROM $tb where date BETWEEN '$sdate' AND '$edate' AND carid = '$cid'";
            }
			// // echo $sql.PHP_EOL;
			// $data = $this->Query($sql);	
			// // print_r($data);		
			// return (int)$data[$key];	
		}
        
        
		$data = $this->Query($sql);			
		return (int)$data[0][$key];	
	}



	/**
	 * 获取流程数据
	 */
	public function newprocessdata($date,$stationid,$key,$tb,$stationtype){
		$CurDb = array_keys($tb);
		$this->SetDb($CurDb[0]);
		$CurTb = array_values($tb);

		switch ($key) {
			case 'wifi_num':
				if ($stationtype != 3) {
					$sql = "SELECT SUM(num) as $key FROM $CurTb[0] WHERE date='$date' AND station in ($stationid)";
				}else{
					$sql = "SELECT count(DISTINCT usermac) as $key FROM $CurTb[0] WHERE date='$date' AND carid in ($stationid)";
				}
				
				break;

			case 'ad1_num':
				if ($stationtype != 3) {
					$date = str_replace('-','_',$date);
					$sql = "SELECT SUM(dtime) as $key FROM $CurTb[0] WHERE date='$date' AND model = 'ad' AND action = 'visit' AND detail = 'uv' AND stationid in ($stationid)";
				}else{
					$sql = "SELECT count(DISTINCT usermac) as $key FROM $CurTb[0] WHERE date='$date' AND carid in ($stationid)";

				}
				
				break;

			case 'send_code_num':
				$date = str_replace('-','',$date);
				if ($stationtype != 3) {
					$sql = "SELECT count(DISTINCT mobile) as $key FROM $CurTb[0] WHERE cday='$date' AND type='204' and station_id in ($stationid)";
				}else{
					$sql = "SELECT count(DISTINCT mobile) as $key FROM $CurTb[0] WHERE cday='$date' AND type='204' and car_id in ($stationid)";
				}				
								
				
				break;
			case 'baodan_reg':
				$date = str_replace('-','',$date);
				if ($stationtype != 3) {
					$sql = "SELECT count(DISTINCT mobile) as $key FROM $CurTb[0] WHERE cday='$date' AND type='205' AND pid='0' and station_id in ($stationid)";				# code...
				}else{
					$sql = "SELECT count(DISTINCT mobile) as $key FROM $CurTb[0] WHERE cday='$date' AND type='205' AND pid='0' and car_id in ($stationid)";
				}			
				
				break;
			case 'phone_reg':
				$date = str_replace('-','',$date);
				if ($stationtype != 3) {
					$sql = "SELECT count(DISTINCT mobile) as $key FROM $CurTb[0] WHERE cday='$date' AND (type='202' OR (type='205' AND pid='1')) AND station_id in ($stationid)";				# code...
				}else{
					$sql = "SELECT count(DISTINCT mobile) as $key FROM $CurTb[0] WHERE cday='$date' AND (type='202' OR (type='205' AND pid='1')) and car_id in ($stationid)";
				}			
				
				break;

			case 'baodan_stay_time':
				$date = str_replace('-','',$date);
				if ($stationtype != 3) {
					$sql = "SELECT SUM(stay_time) as $key FROM $CurTb[0] WHERE cday='$date' AND type='205' and station_id in ($stationid)";			
				}else{
					$sql = "SELECT SUM(stay_time) as $key FROM $CurTb[0] WHERE cday='$date' AND type='205' and car_id in ($stationid)";
				}			
				
				break;	

			case 'phone_stay_time':
				$date = str_replace('-','',$date);
				if ($stationtype != 3) {
					$sql = "SELECT SUM(stay_time) as $key FROM $CurTb[0] WHERE cday='$date' AND type='202' and station_id in ($stationid)";	
				}else{
					$sql = "SELECT SUM(stay_time) as $key FROM $CurTb[0] WHERE cday='$date' AND type='202' and car_id in ($stationid)";
				}			
				
				break;			
			
		}
		// echo $sql.PHP_EOL;
		$data = $this->Query($sql);		
		return (int)$data[0][$key];
		
	}
    
    public function getselfappdata($where,$group,$field,$join = '',$tb = "rb_log"){
        
        $sql = "SELECT {$field} FROM {$tb} {$join} WHERE {$where} {$group} ";
        //echo $sql.'<br/>';
        $data = $this->Query($sql);		
		return $data;
        
    }
    
}
