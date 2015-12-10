<?php 
/**
 * 新aclog日志解析
 * @author Terry
 */
class AcLog
{
	private $PATH ;
	private $TEMP_PATH;
	private $NEW_PATH;
	private $DATA ;
	public  $STATIONS ;//记录当前站点的ac日志解析的次数
	private $ALONG = array();
	
	private $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	//private $dsn_content = "mysql:host=192.168.28.201;dbname=rht_admin";
	private $dbuser_content = 'root';
	private $dbpasswd_content = 'Cahw_1MLLqIt';
	private $db ;
	private $date;
	protected $day;
	public function __construct()
	{	
		$this->DATA = date('Y-m-d',strtotime('-1 day'));
		// $this->DATA = '2015-11-26';
		
		$this->date = date('Y-m-d',strtotime('-1 day'));
		$this->day = date('d',strtotime('-1 day'));

		$this->db = $this->getdb_content();
		$p = $this->db->query('Select `id` as `num`,`acfile` as `name`,`ap`,`acip`,`is_alone` From `rha_station` ');
		$p->setFetchMode(PDO::FETCH_ASSOC);
		$this->STATIONS = $p->fetchAll();
	}
	
	public function setPath($name,$num)
	{	
		/*if (in_array($num,array(16,17))) {
			$this->DATA = date('Y-m-d',strtotime('-1 day'));
		}else{
			$this->DATA = date('Y-m-d');
		}*/
		$path = '/home/upload/aclog/'.$name.'/';
		//$path = '/data/store/upload/aclog/'.$name.'/';
		$this->PATH = $path.'aclog'.$this->DATA.'.txt';
		$this->TEMP_PATH = $path.'temp'.$this->DATA.'.txt';
		$this->NEW_PATH = $path.'aclog'.$this->DATA.'_old.txt';
	}
	
	/**
	 * ac日志解析
	 * @param  int $num        车站id
	 * @param  int $ap         服务器ap 锐捷为1 傲天为2
	 * @param  int $is_alone   是否是独立站点jixiaclog，如，济南西，曲阜，枣庄，泰安，滕州是公用的济南西aclog
	 * @param  string $acip    车站的外网ip 用于区分站点
	 * @param  int $stationnum 共用ac日志的站点数量
	 */
	public function ac($num,$ap,$is_alone,$acip,$stationnum,$name)
	{
		if (!file_exists($this->PATH)) {
			return ;
		}
		try {
			
			if($ap == 1){
				
				/*if ($num == 1) {
					$sh1 = 'cat '.$this->PATH.' | grep "active\|roaming to\|leave" | sed -e "s/\..*Client(/ /g;s/).*://g;s/AP.*(\|)\|in .*(\|to .*(//Ig;s/\.#.*\| success\.#015/ '.$num.'/g;s/ /\x09/g;s/T/\t/" >> '.$this->TEMP_PATH;
				}else{
					if ($this->day >= 10) {
						$sh1 = 'cat '.$this->PATH.' | grep "active\|roaming to\|leave" | sed -e "s/notify.*:/ /g;s/  //g" | awk \'{$1="'.$this->date.'";print $1,$3,$11,$12,$14}\'|sed -e "s/Client(//g;s/)\|(//g;s/\.#.*\| success\.#015/ '.$num.'/g" | sed -e "s/ /\t/g" >> '.$this->TEMP_PATH;
					}else{
						$sh1 = 'cat '.$this->PATH.' | grep "active\|roaming to\|leave" | sed -e "s/notify.*:/ /g;s/  //g" | awk \'{$1="'.$this->date.'";print $1,$2,$9,$10,$12,$14}\'|sed -e "s/Client(//g;s/)\|(//g;s/\.#.*\| success\.#015/ '.$num.'/g" | sed -e "s/ /\t/g" >> '.$this->TEMP_PATH;
					}
				}*/
				$sh1 = 'cat '.$this->PATH.' | grep "active\|roaming to\|leave" | sed -e "s/\..*Client(/ /g;s/).*://g;s/AP.*(\|)\|in .*(\|to .*(//Ig;s/\.#.*\| success\.#015/ '.$num.'/g;s/ /\x09/g;s/T/\t/" >> '.$this->TEMP_PATH;				
								
				passthru($sh1);
				
			}elseif($ap == 2){
				//判断是否是独立站点
				if ($is_alone) {
					
					if ($num == '16') {
						$sh1 = 'cat '.$this->PATH.' | grep associated | sed -e "s/\..*Message:(/ /g;s/).*vap(/ /g;s/\/.*STA(/ /g;s/).*/ '.$num.'/g;s/T/ /" | awk \'{print $1,$2,$5,$3,$4,$6}\' | sed -e "s/\s/\x09/g">> ' .$this->TEMP_PATH;
					}else{
						$sh1 = 'cat '.$this->PATH.' | grep  "access\|leave" | sed -e "s/Local.*STA//g;s/WTP.*IP\://g;s/wlan.*//g;s/,.*/ '.$num.'/g;s/\x09//g;s/ /\x09/g;s/ /\x09/g;s/T/\t/;s/\+.*STA//g" >> '.$this->TEMP_PATH;
					}				
					
				}else{

					if ($num == '17') {
						$sh1 = 'cat '.$this->PATH.' | grep  "access\|leave" | sed -e "s/Local.*STA//g;s/WTP.*IP\://g;s/wlan.*//g;s/,.*/ '.$num.'/g;s/\x09//g;s/ /\x09/g;s/ /\x09/g;s/T/\t/;s/\+.*STA//g" >> '.$this->TEMP_PATH;
					}else{
						$acips = str_replace(array('.',','),array('\.','\|'),$acip);
						$sh1 = 'cat '.$this->PATH.' | grep  "access\|leave" | grep "'.$acips.'" | sed -e "s/Local.*STA//g;s/WTP.*IP\://g;s/wlan.*//g;s/,.*/ '.$num.'/g;s/\x09//g;s/ /\x09/g;s/ /\x09/g;s/T/\t/;s/\+.*STA//g" >> '.$this->TEMP_PATH;
					}

					//记录当前站点的ac日志解析的次数
					if (!isset($this->ALONG[$name]) || !$this->ALONG[$name]) {
						$this->ALONG[$name] = 1;
					}else{
						$this->ALONG[$name]++;
					}
					
				}
				
				passthru($sh1);
			}elseif($ap == 3){//环创
				//判断是否是独立站点
				if ($is_alone) {
					$sh1 = 'cat '.$this->PATH.' | grep associated | sed -e "s/\..*Message:(/ /g;s/).*vap(/ /g;s/\/.*STA(/ /g;s/).*/ '.$num.'/g;s/T/ /" | awk \'{print $1,$2,$5,$3,$4,$6}\' | sed -e "s/\s/\x09/g">> ' .$this->TEMP_PATH;
				}
				passthru($sh1);
			}

			/*$sh3 = 'mysql -h192.168.28.201 -uroot -pCahw_1MLLqIt  rht_admin --local-infile=1 -e \'load data local infile "'.$this->TEMP_PATH.'" into table rha_aclog(date,time,client,action,ap,station)\'';*/
			$sh3 = 'mysql -uroot -pCahw_1MLLqIt  rht_admin --local-infile=1 -e \'load data local infile "'.$this->TEMP_PATH.'" into table rha_aclog(date,time,client,action,ap,station)\'';
			passthru($sh3);
		
		}catch (Exception $e) {   
			print $e->getMessage();  
			
			exit();   
		}
		if ($is_alone) {
			$sh4 = 'mv ' . $this->PATH .' '. $this->NEW_PATH;
			passthru($sh4);
		}else{
			if ($this->ALONG[$name] == $stationnum) {
				$sh4 = 'mv ' . $this->PATH .' '. $this->NEW_PATH;
				passthru($sh4);
			}
		}
		
		
		unlink($this->TEMP_PATH);
	}
	
	/**
	 * 根据AP区分出肇东
	 */
	public function CutZd()
	{
		$sql = "UPDATE rha_aclog SET station = 24 WHERE date='$this->date' AND station='16' AND ap in('129','130','131','132','133','134','135','136','137')";
		$this->db->query($sql);
	}

	/**
	 * 根据AP区分出安达
	 */
	public function CutAd()
	{
		$sql = "UPDATE rha_aclog SET station = 23 WHERE date='$this->date' AND station='16' AND ap in('138','139','140','141','142','143','144','145','146','147','148','149','150')";
		$this->db->query($sql);
	}

	/**
	 * 根据APMAC区分出高密
	 */
	public function CutGm()
	{
		$sql = "UPDATE rha_aclog SET station = 30 WHERE date='$this->date' AND station='2' AND ap in('5869.6c44.2bce','5869.6c44.2bee','5869.6c44.326e','5869.6c44.336a','5869.6c44.33a6','5869.6c44.3456')";
		$this->db->query($sql);
	}
	
	private function getdb_content()
	{
		$db = new PDO($this->dsn_content, $this->dbuser_content, $this->dbpasswd_content);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->exec('set names utf8');	
		return $db;
	}

	/**
	 * 根据ac日志目录，获取相同目录下的日志包含的车站数量
	 * @param  str $acfile 当前车站的ac日志目录
	 */
	public function samestationnum($acfile){
		$sql = "select count(*) FROM rha_station WHERE acfile='$acfile'";
		$rs = $this->db->query($sql);
		return $rs->fetchColumn();
	}
}

$obj = new AcLog();
$stations = $obj->STATIONS;

foreach ($stations as $key=>$var)
{
	$obj -> setPath($var['name'],$var['num']);
	$stationnum = $obj -> samestationnum($var['name']);
	$obj -> ac($var['num'],$var['ap'],$var['is_alone'],$var['acip'],$stationnum,$var['name']);

	
}

$obj->CutZd();
$obj->CutAd();
$obj->CutGm();

echo 'success';

