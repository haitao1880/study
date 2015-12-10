<?php 
/**
 * log日志解析
 */
class LogDo
{
	protected $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	// protected $dsn_content = "mysql:host=192.168.28.201;dbname=rht_admin";
	protected $dbuser_content = 'root';
	protected $dbpasswd_content = 'Cahw_1MLLqIt';
	protected $db ;
	
	public $LOG_BASE_PATH = '/home/upload/nginxlog/';
	// public $LOG_BASE_PATH = '/data/store/upload/nginxlog/';
	protected $ALNAME = 'appui_wonaonao_access.log';
	protected $WLNAME = 'm_wonaonao_access.log';
	
	protected $WEB1 ;
	protected $WEB2 ;
	protected $LOG1 ;
	protected $LOG2 ;
	protected $BASE_PATH ;
	protected $PATH ;
	protected $DATE ;
	protected $DATE1 ;
	protected $TEMP_PATH ;
	protected $NEW_PATH ;	
	protected $CAT_FILE_APP ;	//app 日志
	protected $CAT_FILE_WEB ;	//web 认证后日志
	protected $CAT_BEFORFILE_WEB ; //web 认证前日志
	
	public  $STATION ;
	protected $IPFILTER ;//认证后
	protected $BEFORIPFILTER;//认证前
	protected $STATIONID;
	
	protected $tp_index2 ;
	protected $tp_cut ;

	protected $WEB_MAIN =array();
	protected $WEB_DETAIL = array();
	
	
	public function __construct($stationid)
	{	
		$this->DATE = date('Y-m-d',strtotime('-1 day'));
		$this->DATE1 = date('Y_m_d',strtotime('-1 day'));
		// $this->DATE = '2015_09_04';
		$this->db = $this->getdb_content();

		//根据stationid查询出对应的信息
		$p = $this->db->query("Select `id`,`logfile`,`logip`,`ifconf`,`logname`,`ap`,`xuip`,`is_alone`,`is_moreweb` From `rha_station` where id = $stationid");
		$p->setFetchMode(PDO::FETCH_ASSOC);
		$this->STATION = $p->fetchAll();
	}

	/**
	 * 删除$dir下的目录
	 * @param  string $dir
	 */
	public function deldir($dir){
		if (!is_dir($dir)) {
			return;
		}
		$conn = opendir($dir);
		while(($row = readdir($conn)) != false){
			if ($row == '.' || $row == '..' || $row == $this->DATE || $row == 'gonet' || $row == 'qdb' || $row == 'qdd' || $row == 'sf') {
				continue;
			}
			if (is_dir($dir.DIRECTORY_SEPARATOR.$row)) {
				$sh = 'rm -rf '.$dir.DIRECTORY_SEPARATOR.$row;
				passthru($sh);
			}
		}
		closedir($conn);
	}
	/**
	 * 设置通用文件路径
	 * @param string $logfile
	 */
	public function setPath($logfile,$logname,$is_moreweb)
	{
		$this->PATH = $this->LOG_BASE_PATH.$logfile.'/';
		$this->BASE_PATH = $this->PATH.$this->DATE;
		if ($is_moreweb) {
			$this->LOG1 = $this->PATH.$this->DATE.'.'.$logname.'web1.nginxlog.tar.gz';
			$this->LOG2 = $this->PATH.$this->DATE.'.'.$logname.'web2.nginxlog.tar.gz';
			
		}else{
			$this->LOG1 = $this->PATH.$this->DATE.'.'.$logname.'web.nginxlog.tar.gz';
			$this->LOG2 = '';
			
		}
		$this->WEB1 = $this->BASE_PATH . '/web1/www/';
		$this->WEB2 = $this->BASE_PATH . '/web2/www/';
		
	}
	/**
	 * 获取已经上传的stationid
	 * @return array
	 */
	public function CheckDo()
	{
		$s = $this->db->query('Select `stationid` From `rha_scriptlog` Where `type` = 6 And `ctime` = CURRENT_DATE()');
		$s->setFetchMode(PDO::FETCH_ASSOC);
		$sid = $s->fetchAll();
		$ss = array();
		foreach ($sid as $v)
		{
			$ss[] = $v['stationid'];
		}
		return $ss;
	}
	/**
	 * 插入已经完成的log
	 */
	public function InCheck()
	{
		$int = array(
			'stationid' => 	$this->STATIONID,
			'type'		=>	6,
			'ctime'		=>	date('Y-m-d')	
		);
		$this->Insert($int,'rha_scriptlog');
	}
	
	
	public function setStationId($stationid)
	{
		$this->STATIONID = $stationid;
		
	}

	/**
	 * 格式化虚ip
	 * @param  str $xuip 以逗号分割的虚ip
	 */
	protected function getxuip($xuip,$ap){
		if ($ap == '2') {
			$format = "| grep -i 'wlanuserip=";
			$format .= str_replace(array('.',','), array('\.','\|wlanuserip='),$xuip)."'";
		}else{
			$format = '';
		}
		return $format;
	}
	/**
	 * 设置IP过滤规则，且生成打开的文件路径
	 * @param string $ip
	 * @param num $conf
	 */
	public function setIpFilter($ip,$conf,$ap,$xuip,$is_alone)
	{	
		$ip = str_replace(array('.',','),array('\.','\|'),$ip);
		$fil = $conf ? '' : ' -v ';
		if ($is_alone) {
			$this->IPFILTER = ' | grep '.$fil.' "'.$ip.'" ';

			$this->CAT_FILE_APP = ' cat '.$this->WEB1.$this->ALNAME.' '.$this->WEB2.$this->ALNAME.$this->IPFILTER;
			$this->CAT_FILE_WEB = ' cat '.$this->WEB1.$this->WLNAME.' '.$this->WEB2.$this->WLNAME.$this->IPFILTER;
		}else{
			$this->BEFORIPFILTER = $this->getxuip($xuip,$ap);//认证之前
			$this->IPFILTER = ' | grep '.$fil.' "'.$ip.'" ';//认证后

			$this->CAT_FILE_APP = ' cat '.$this->WEB1.$this->ALNAME.' '.$this->WEB2.$this->ALNAME.$this->IPFILTER;
			$this->CAT_FILE_WEB = ' cat '.$this->WEB1.$this->WLNAME.' '.$this->WEB2.$this->WLNAME.$this->IPFILTER;

			//认证前
			$this->CAT_BEFORFILE_WEB = ' cat '.$this->WEB1.$this->WLNAME.' '.$this->WEB2.$this->WLNAME.$this->BEFORIPFILTER;
		}
		
		
		if($ap == 1){
			$this->tp_index2 = 'mac=.*&t';
			$this->tp_cut = '5-17';
		}elseif($ap == 2){
			$this->tp_index2 = 'usermac=.*&s';
			$this->tp_cut = '9-26';
		}
		
		
	
	}
	//日志解压
	function Log($is_moreweb)
	{	
		if (!file_exists($this->LOG1) && !file_exists($this->LOG2)) {
			return ;
		}	
		
		
		if ($is_moreweb) {
			if (file_exists($this->WEB2.$this->WLNAME)) {
				return;
			}
			if (!is_dir($this->BASE_PATH . '/web1') || !$this->BASE_PATH . '/web2') {
				mkdir ($this->BASE_PATH . '/web1' , 0777 , true);
				mkdir ($this->BASE_PATH . '/web2' , 0777 , true);
			}			
			
			$sh1 = 'tar -zxvf '.$this->LOG1.' -C '.$this->BASE_PATH.'/web1';
			$sh2 = 'tar -zxvf '.$this->LOG2.' -C '.$this->BASE_PATH.'/web2';
			passthru($sh1);
			passthru($sh2);
		}else{
			if (file_exists($this->WEB1.$this->WLNAME)) {
				return;
			}

			if (!is_dir($this->BASE_PATH . '/web1') || !$this->BASE_PATH . '/web2') {
				mkdir ($this->BASE_PATH . '/web1/www' , 0777 , true);
				mkdir ($this->BASE_PATH . '/web2/www' , 0777 , true);
			}
			
			$sh1 = 'tar -zxvf '.$this->LOG1.' -C '.$this->BASE_PATH.'/web1/www';
			passthru($sh1);
		}
		
	}
	
	public function clear()
	{
		$this->WEB_MAIN = array();
		$this->WEB_DETAIL = array();
	}
	
	
	/**
	 * 插入数据库
	 * @param array $data 
	 * @param string $tb
	 * @return num : >0 插入成功
	 */
	protected function Insert($data,$tb='rha_count')
	{
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
	
	protected function getdb_content()
	{
		$db = new PDO($this->dsn_content, $this->dbuser_content, $this->dbpasswd_content);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->exec('set names utf8');
	
		return $db;
	}
		
}


error_reporting(E_ALL^E_NOTICE);

//接收stationid
$stationid = $argv[1];
if (!$stationid) {
	exit;
}
$obj = new LogDo($stationid);

$station = $obj->STATION[0];
// var_dump($station['logfile']);
// exit;
$check = $obj->CheckDo();

$filelogs = array();

// foreach ($stations as $k=>$v)
// {	
	// $filelogs[] = $station['logfile'];
 	if (!in_array($station['id'], $check)) {
 		$obj->setPath($station['logfile'],$station['logname'],$station['is_moreweb']);
		$obj->setIpFilter($station['logip'], $station['ifconf'], $station['ap'],$station['xuip'],$station['is_alone']);
		$obj->Log($station['is_moreweb']);
		$obj->setStationId($station['id']);
		
		$obj->clear();
		
	 	$obj->InCheck();
	 	
	 	/*$filelog = array_unique($filelogs);
		foreach($filelog as $v){
			//删除之前的目录
		 	$obj->deldir($obj->LOG_BASE_PATH.$v);
		}*/
 	}
 	
	
	
// }

