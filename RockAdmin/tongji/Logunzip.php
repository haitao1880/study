<?php
/**
 * 日志解压脚本
 */

require '/home/upload/nginxlog/dologunzip.php';
class Logunzip extends LogDo
{

	public function __construct($stationid){
		parent::__construct($stationid);
	}

	//日志解压
	public function Log($is_moreweb)
	{	
		if (!file_exists($this->LOG1) && !file_exists($this->LOG2)) {
			return ;
		}	
		
		
		if ($is_moreweb) {
			if (file_exists($this->WEB2.$this->WLNAME)) {
				return;
			}
			if (!is_dir($this->BASE_PATH . '/web1/www') || !$this->BASE_PATH . '/web2/www') {
				mkdir ($this->BASE_PATH . '/web1/www' , 0777 , true);
				mkdir ($this->BASE_PATH . '/web2/www' , 0777 , true);
			}			
			
			$sh1 = 'tar -zxvf '.$this->LOG1.' -C '.$this->BASE_PATH.'/web1/www';
			$sh2 = 'tar -zxvf '.$this->LOG2.' -C '.$this->BASE_PATH.'/web2/www';
			passthru($sh1);
			passthru($sh2);
		}else{
			if (file_exists($this->WEB1.$this->WLNAME)) {
				return;
			}

			if (!is_dir($this->BASE_PATH . '/web1/www') || !$this->BASE_PATH . '/web2/www') {
				mkdir ($this->BASE_PATH . '/web1/www' , 0777 , true);
				mkdir ($this->BASE_PATH . '/web2/www' , 0777 , true);
			}
			
			$sh1 = 'tar -zxvf '.$this->LOG1.' -C '.$this->BASE_PATH.'/web1/www';
			passthru($sh1);
		}
		
	}
}


error_reporting(E_ALL^E_NOTICE);

//接收stationid
$stationid = $argv[1];
if (!$stationid) {
	exit;
}
$obj = new Logunzip($stationid);
$station = $obj->STATION[0];
$obj->setPath($station['logfile'],$station['logname'],$station['is_moreweb']);
$obj->setIpFilter($station['logip'], $station['ifconf'], $station['ap'],$station['xuip'],$station['is_alone']);
$obj->Log($station['is_moreweb']);
	
	
