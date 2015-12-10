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
	public function __construct()
	{	
		//$this->DATA = '2015-06-21';
		$this->hour = date('H',strtotime("-1 hour"));
		$this->DATA = date('Y-m-d-H',strtotime("-1 hour"));
		$this->date = date('Y-m-d',strtotime("-1 hour"));
		$this->day = date('d',strtotime('-1 hour'));

		$this->db = $this->getdb_content();
		$p = $this->db->query('Select `id` as `num`,`acfile` as `name`,`ap`,`acip`,`is_alone` From `rha_station` ');
		$p->setFetchMode(PDO::FETCH_ASSOC);
		$this->STATIONS = $p->fetchAll();
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
				if ($this->day >= '10') {
					$sh1 = 'cat '.$this->PATH.' | grep "active\|roaming to\|leave" | sed -e "s/notify.*:/ /g;s/  //g" | awk \'{print $11}\'|sed -e "s/Client(//g;s/)\|(/ '.$num.'/g" | awk \'{count[$1]=$0}END{for(name in count) print count[name]}\' | awk \'BEGIN{OFS="/"}{num[$2]++}END{for(name in num) print name,num[name]}\'';	

					//pv
					$sh2 = 'cat '.$this->PATH.' | grep "active\|roaming to" | sed -e "s/notify.*:/ /g;s/  //g" | awk \'{print $11}\'|sed -e "s/Client(//g;s/)\|(/ '.$num.'/g" | awk \'BEGIN{OFS="/"}{num[$2]++}END{for(name in num) print name,num[name]}\'';	
				}else{
					$sh1 = 'cat '.$this->PATH.' | grep "active\|roaming to\|leave" | sed -e "s/notify.*:/ /g;s/  //g" | awk \'{print $9}\'|sed -e "s/Client(//g;s/)\|(/ '.$num.'/g" | awk \'{count[$1]=$0}END{for(name in count) print count[name]}\' | awk \'BEGIN{OFS="/"}{num[$2]++}END{for(name in num) print name,num[name]}\'';
					//pv
					$sh2 = 'cat '.$this->PATH.' | grep "active\|roaming to" | sed -e "s/notify.*:/ /g;s/  //g" | awk \'{print $9}\'|sed -e "s/Client(//g;s/)\|(/ '.$num.'/g" | awk \'BEGIN{OFS="/"}{num[$2]++}END{for(name in num) print name,num[name]}\'';		
				}				
							
				exec($sh1,$hourconnect);
				exec($sh2,$hourconnect_pv);
				foreach($hourconnect as $v){
					list($temp['stationid'],$temp['num']) = explode('/',$v);
					$temp += array('date'=>$this->date,'hour'=>$this->hour,'type'=>'uv');
					$this->Insert($temp,'rha_aclog_hour');
				}
				foreach($hourconnect_pv as $pv){
					list($temp_pv['stationid'],$temp_pv['num']) = explode('/',$pv);
					$temp_pv += array('date'=>$this->date,'hour'=>$this->hour,'type'=>'pv');
					$this->Insert($temp_pv,'rha_aclog_hour');
				}
				
			}elseif($ap == 2){
				//判断是否是独立站点
				if ($is_alone) {
					$sh1 = 'sed -e "s/.*Local.*STA//g;s/WTP.*/ '.$num.'/g" '.$this->PATH.'| grep "access\|leave" | awk \'{count[$1]=$0}END{for(name in count) print count[name]}\' | awk \'BEGIN{OFS="/"}{num[$3]++}END{for(name in num) print name,num[name]}\'';
					//pv
					$sh2 = 'sed -e "s/.*Local.*STA//g;s/WTP.*/ '.$num.'/g" '.$this->PATH.'| grep "access" | awk \'BEGIN{OFS="/"}{num[$3]++}END{for(name in num) print name,num[name]}\'';
				}else{
					$acips = str_replace(array('.',','),array('\.','\|'),$acip);
					$sh1 = 'cat '.$this->PATH.' | grep  "access\|leave" | grep "'.$acips.'" | sed -e "s/.*Local.*STA//g;s/WTP.*/ '.$num.'/g" | awk \'{count[$1]=$0}END{for(name in count) print count[name]}\' | awk \'BEGIN{OFS="/"}{num[$3]++}END{for(name in num) print name,num[name]}\'';
					//pv
					$sh2 = 'cat '.$this->PATH.' | grep  "access" | grep "'.$acips.'" | sed -e "s/.*Local.*STA//g;s/WTP.*/ '.$num.'/g" | awk \'BEGIN{OFS="/"}{num[$3]++}END{for(name in num) print name,num[name]}\'';
					//记录当前站点的ac日志解析的次数
					if (!isset($this->ALONG[$name]) || !$this->ALONG[$name]) {
						$this->ALONG[$name] = 1;
					}else{
						$this->ALONG[$name]++;
					}
					
				}
				
				exec($sh1,$hourconnects);
				exec($sh2,$hourconnects_pv);
				foreach($hourconnects as $v1){
					list($temp2['stationid'],$temp2['num']) = explode('/',$v1);
					$temp2 += array('date'=>$this->date,'hour'=>$this->hour,'type'=>'uv');
					$this->Insert($temp2,'rha_aclog_hour');
				}
				foreach($hourconnects_pv as $pv1){
					list($temp_pv2['stationid'],$temp_pv2['num']) = explode('/',$pv1);
					$temp_pv2 += array('date'=>$this->date,'hour'=>$this->hour,'type'=>'pv');
					$this->Insert($temp_pv2,'rha_aclog_hour');
				}
				
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
}

$obj = new AcLog();
$stations = $obj->STATIONS;

foreach ($stations as $key=>$var)
{
	$obj -> setPath($var['name']);
	$stationnum = $obj -> samestationnum($var['name']);
	$obj -> ac($var['num'],$var['ap'],$var['is_alone'],$var['acip'],$stationnum,$var['name']);

	
}

//$obj->Ex_Qufu();

echo 'success';

