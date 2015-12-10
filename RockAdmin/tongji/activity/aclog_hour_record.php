<?php 
/**
 * 新aclog日志解析
 * @author Terry
 */
class AcLog
{
	protected $PATH ;
	protected $TEMP_PATH;
	protected $NEW_PATH;
	protected $DATA ;
	public  $STATIONS ;//记录当前站点的ac日志解析的次数
	protected $ALONG = array();
	
	protected $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	//protected $dsn_content = "mysql:host=192.168.28.201;dbname=rht_admin";
	protected $dbuser_content = 'root';
	protected $dbpasswd_content = 'password';
	protected $db ;
	protected $date;
	protected $day;
	protected $hour;

	protected $acrecord_table = 'rha_aclog_record';

	public function __construct()
	{			
		$this->hour = date('H',strtotime("-1 hour"));
		$this->DATA = date('Y-m-d-H',strtotime("-1 hour"));
		$this->date = date('Y-m-d',strtotime("-1 hour"));
		$this->day = date('d',strtotime('-1 hour'));

		/*$this->hour = $hour;
		$this->DATA = $data;
		$this->date = $date;
		$this->day = $day;
*/
		$this->acrecord_table = $this->acrecord_table.'_'.date('Y_m');

		$this->db = $this->getdb_content();
		$p = $this->db->query('Select `id` as `num`,`acfile` as `name`,`ap`,`acip`,`is_alone` From `rha_station` ');
		$p->setFetchMode(PDO::FETCH_ASSOC);
		$this->STATIONS = $p->fetchAll();

		if ($this->date == date('Y-m').'-01' && $this->hour == '00') {
			$this->CreatTb();
		}
		
	}
	
	public function setPath($name)
	{
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
				
				if ($num == 1) {
					$sh1 = 'cat '.$this->PATH.' | grep "active\|roaming to\|leave" | sed -e "s/\..*Client(/ /g;s/).*://g;s/AP.*(\|)\|in .*(\|to .*(//Ig;s/\.#.*\| success\.#015/ '.$num.' '.$this->hour.' /g;s/ /\x09/g;s/T/\t/">> '.$this->TEMP_PATH;
				}else{
					if ($this->day >= 10) {
						$sh1 = 'cat '.$this->PATH.' | grep "active\|leave" | sed -e "s/notify.*:/ /g;s/  //g" | awk \'{$1="'.$this->date.'";print $1,$3,$11,$12,$14}\'|sed -e "s/Client(//g;s/)\|(//g;s/\.#.*\| success\.#015/ '.$num.' '.$this->hour.'/g" | sed -e "s/ /\t/g" >> '.$this->TEMP_PATH;
					}else{
						$sh1 = 'cat '.$this->PATH.' | grep "active\|leave" | sed -e "s/notify.*:/ /g;s/  //g" | awk \'{$1="'.$this->date.'";print $1,$2,$9,$10,$12,$14}\'|sed -e "s/Client(//g;s/)\|(//g;s/\.#.*\| success\.#015/ '.$num.' '.$this->hour.'/g" | sed -e "s/ /\t/g" >> '.$this->TEMP_PATH;
					}
				}
												
				passthru($sh1);
			}elseif($ap == 2){
				//判断是否是独立站点
				if ($is_alone) {
					$sh1 = 'cat '.$this->PATH.' | grep  "access\|leave" | sed -e "s/Local.*STA//g;s/WTP.*IP\://g;s/wlan.*//g;s/,.*/ '.$num.' '.$this->hour.' /g;s/\x09//g;s/ /\x09/g;s/\+.*STA//g;s/T/\t /" >> '.$this->TEMP_PATH;

					// if (in_array($num,array(15,17))) {			

					// 	$sh1 = 'cat '.$this->PATH.' | grep  "access\|leave" | sed -e "s/Local.*STA//g;s/WTP.*IP\://g;s/wlan.*//g;s/,.*/ '.$num.' '.$this->hour.' /g;s/\x09//g;s/ /\x09/g;s/\+.*STA//g;s/T/\t /" >> '.$this->TEMP_PATH;
					// }else{
					// 	$sh1 = 'sed -e "s/Local.*STA//g;s/WTP.*IP\://g;s/wlan.*//g;s/,.*/ '.$num.' '.$this->hour.'/g;s/\x09//g;s/ /\x09/g" '.$this->PATH.'| grep "access\|leave" >> ' .$this->TEMP_PATH;
					// }
					
				}else{
					$acips = str_replace(array('.',','),array('\.','\|'),$acip);
					//$acips = str_replace(',','\|',$acip);
					
					if (!stristr($name, 'qdjy')) {
						$sh1 = 'cat '.$this->PATH.' | grep  "access\|leave" | grep "'.$acips.'" | sed -e "s/Local.*STA//g;s/WTP.*IP\://g;s/wlan.*//g;s/,.*/ '.$num.' '.$this->hour.'/g;s/\x09//g;s/ /\x09/g" >> '.$this->TEMP_PATH;
					}else{
						$sh1 = 'cat '.$this->PATH.' | grep  "access\|leave" | grep "'.$acips.'" | sed -e "s/Local.*STA//g;s/WTP.*IP\://g;s/wlan.*//g;s/,.*/ '.$num.' '.$this->hour.'/g;s/\x09//g;s/ /\x09/g;s/T/\t/;s/\+.*STA//g" >> '.$this->TEMP_PATH;
					}					
					
					
					// if (!in_array($num,array(11,12,13,14,18,19))) {
					// 	$sh1 = 'cat '.$this->PATH.' | grep  "access\|leave" | grep "'.$acips.'" | sed -e "s/Local.*STA//g;s/WTP.*IP\://g;s/wlan.*//g;s/,.*/ '.$num.' '.$this->hour.'/g;s/\x09//g;s/ /\x09/g" >> '.$this->TEMP_PATH;
					// }else{
					// 	$sh1 = 'cat '.$this->PATH.' | grep  "access\|leave" | grep "'.$acips.'" | sed -e "s/Local.*STA//g;s/WTP.*IP\://g;s/wlan.*//g;s/,.*/ '.$num.' '.$this->hour.'/g;s/\x09//g;s/ /\x09/g;s/T/\t/;s/\+.*STA//g" >> '.$this->TEMP_PATH;
					// }
					

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

					$sh1 = 'cat '.$this->PATH.' | grep associated | sed -e "s/\..*Message:(/ /g;s/).*vap(/ /g;s/\/.*STA(/ /g;s/).*/ '.$num.' /g;s/T/ /" | awk \'{print $1,$2,$5,$3,$4,$6,'.$this->hour.'}\' | sed -e "s/\s/\x09/g">> ' .$this->TEMP_PATH;
				}
				passthru($sh1);
			}
			
			//数据入库	
			$sh3 = 'mysql -uroot -ppassword  rht_admin --local-infile=1 -e \'load data local infile "'.$this->TEMP_PATH.'" into table '.$this->acrecord_table.'(date,time,client,action,ap,stationid,hour)\'';
			passthru($sh3);	

			//计算pv[pv应该排除离开时的连接]
			$sh_pv = 'cat '.$this->TEMP_PATH.' | sed -e "s/\t/ /g" | grep -v leave | awk \'BEGIN{OFS="/"}{count[$6"/"$7]++}END{for(name in count) print name,count[name]}\'';
			exec($sh_pv,$hour_pv);
			foreach($hour_pv as $pv){
				list($temp_pv['stationid'],$temp_pv['hour'],$temp_pv['num']) = explode('/',$pv);
				$temp_pv += array('date'=>$this->date,'type'=>'pv');
				$this->Insert($temp_pv,'rha_aclog_hour');
			}

			//新增用户
			$sh_mac = 'cat '.$this->TEMP_PATH.' | sed -e "s/\t/ /g" | awk \'{count[$3]=$0}END{for(name in count) print count[name]}\' | cut -d " " -f 3';
			exec($sh_mac,$umacs);

			//先删掉其他天的数据
			$sql_other = "DELETE FROM rha_isexists_umac WHERE date != ? ";
			$this->Execute($sql_other,array($this->date));
			$newuser = 0;
			//print_r($umacs);
			foreach($umacs as $mac){

				//判断当前用户是否是新增用户
				$sql = "SELECT count(client) as num FROM rha_isexists_umac WHERE client = '$mac' AND stationid = $num";
				$res = $this->FetchRow($sql);
				if (!$res['num']) {
					$newuser++;
					$datamac = array('date'=>$this->date,'hour'=>$this->hour,
						'stationid'=>$num,'client'=>$mac );
					$this->Insert($datamac,'rha_isexists_umac');
				}
			}
			//echo $newuser;
			//uv
			$sh_uv = 'cat '.$this->TEMP_PATH.' | sed -e "s/\t/ /g" | awk \'{count[$3]=$0}END{for(name in count) print count[name]}\'| awk \'BEGIN{OFS="/"}{count[$6"/"$7]++}END{for(name in count) print name,count[name]}\'';
			exec($sh_uv,$hour_uv);
			foreach($hour_uv as $uv){
				list($temp_uv['stationid'],$temp_uv['hour'],$temp_uv['num']) = explode('/',$uv);
				$temp_uv += array('date'=>$this->date,'new_uv'=>$newuser,'type'=>'uv');
				$this->Insert($temp_uv,'rha_aclog_hour');
			}


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
	 * 额外部分
	 */
	public function Ex_Qufu()
	{
		$sql = "Update rha_aclog Set station = 5 Where station = 4 and ap like '222.43.236%' ;";
		$this->db->query($sql);
	}
	
	protected function getdb_content()
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

	/**
	 * 插入数据库
	 * @param array $data 
	 * @param string $tb
	 * @return num : >0 插入成功
	 */
	protected function Insert($data,$tb='rha_count')
	{	
		$key = '';
		$var = '';
		foreach ($data as $kt=>$vt)
		{
			$key.= "`$kt`,";
			$var.= "?,";
		}
		$key = rtrim($key,',');
		$var = rtrim($var,',');
		$in_sql = "Insert Into `$tb`($key) Value ($var);";
		$sth = $this->db->prepare($in_sql);
		$sth->execute(array_values($data));
		return $this->db->lastInsertId();
		
	}

	/**
	 * 返回一行数据
	 * @param 
	 */
	protected function FetchRow($sql)
    {	
		$p = $this->db->query($sql);
		$p->setFetchMode(PDO::FETCH_ASSOC);
		$result = $p->fetchAll();
        return $result[0];
    }

    protected function Execute($sql,$data)
	{
		$dbd = $this->db->prepare($sql);
		return $dbd->execute($data);
	}

	//创建表
	protected function CreatTb(){
		$sql = "CREATE TABLE $this->acrecord_table (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `date` char(10) DEFAULT NULL,
				  `hour` char(2) DEFAULT NULL,
				  `time` varchar(10) DEFAULT NULL,
				  `client` varchar(64) DEFAULT NULL,
				  `action` varchar(8) DEFAULT NULL,
				  `ap` varchar(20) DEFAULT NULL,
				  `stationid` tinyint(4) DEFAULT '1' COMMENT '1,青岛南；2，青岛北',
				  PRIMARY KEY (`id`),
				  KEY `index_date` (`date`),
				  KEY `client` (`client`) USING BTREE
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";	
		try {
		    $this->db->exec($sql);

		} catch (PDOException $e) {
		    die("DB ERROR: ". $e->getMessage());
		}
		
		
	}
}


function execsql($sdate,$edate){
	$hours = array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23');
	$sdate = strtotime($sdate);
	$edate = strtotime($edate);
	while ($sdate <= $edate) {
		$date = date('Y-m-d',$sdate);
		foreach($hours as $hour){
			$data = $date.'-'.$hour;
			$day = date('d',strtotime($date));
			$obj = new AcLog($hour,$data,$date,$day);
			$stations = $obj->STATIONS;

			foreach ($stations as $key=>$var)
			{
				$obj -> setPath($var['name']);
				$stationnum = $obj -> samestationnum($var['name']);
				$obj -> ac($var['num'],$var['ap'],$var['is_alone'],$var['acip'],$stationnum,$var['name']);

				
			}
		}		
		$sdate = strtotime('+1 day',$sdate);
	}
}

//execsql('2015-08-13','2015-08-14');
$obj = new AcLog();
$stations = $obj->STATIONS;

foreach ($stations as $key=>$var)
{
	$obj -> setPath($var['name']);
	$stationnum = $obj -> samestationnum($var['name']);
	$obj -> ac($var['num'],$var['ap'],$var['is_alone'],$var['acip'],$stationnum,$var['name']);

	
}



echo 'success';

