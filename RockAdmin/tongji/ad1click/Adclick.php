<?php
/**
* 元用户活动内容统计  抢红包，抽奖、等
*/
require '/home/upload/nginxlog/adclick/web_record.php';
class Adclick extends LogDo
{	
	protected $LOG1;
	protected $LOG2;
	protected $hour;
	protected $APPNAME;
	public function __construct($stationid)
	{
		parent::__construct($stationid);
		$this->DATE = '2015_07_01';

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
		$this->WEB = $this->BASE_PATH . '/web*/www/';
		
	}

	//日志解压
	function Log($is_moreweb)
	{	
		if (file_exists($this->WEB2.$this->WLNAME)) {
			return ;
		}
		
		mkdir ($this->BASE_PATH . '/web1' , 0777 , true);
		mkdir ($this->BASE_PATH . '/web2' , 0777 , true);
		if ($is_moreweb) {
			$sh1 = 'tar -zxvf '.$this->LOG1.' -C '.$this->BASE_PATH.'/web1';
			$sh2 = 'tar -zxvf '.$this->LOG2.' -C '.$this->BASE_PATH.'/web2';
			passthru($sh1);
			passthru($sh2);
		}else{
			$sh1 = 'tar -zxvf '.$this->LOG1.' -C '.$this->BASE_PATH.'/web1';
			passthru($sh1);
		}
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
			if ($row == '.' || $row == '..' || $row == 'gonet' || $row == 'qdb' || $row == 'qdd' || $row == 'sf' || $row == $this->yesterday || $row == $this->DATE) {
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
	 * 获取已经上传的stationid
	 * @return array
	 */
	public function CheckDo()
	{	
		$s = $this->db->query('Select `stationid` From `rha_scriptlog` Where `type` = 6 And `ctime` = CURRENT_DATE() ');
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

	/**
	 * 广告1点击统计
	 */
	public function Ad1ClickStatic(){	
		//index/click/40/ios/254	
		$sh_pv = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(
		tolower($6)~ /record.php/ && tolower($8)~/index\/ads\/click\//) print $8}\' | cut -d "&" -f 1 | awk
		 \'BEGIN{FS=OFS="/"}{count[$1"/"$3"/"$4"/"$5]++}END{for(name in count) print name,count[name]}\' ';
		exec($sh_pv,$adsclickpv);

		$sh_uv = $this->CAT_FILE_WEB.'| awk -F "\[\|cut\|\]" \'{if(tolower($6)~ /record.php/ && tolower($8)~/
		index\/ads\/click\//) print $7,$8}\' | awk \'{if(index($1,"usermac=")) $1=substr($1,
		index($1,"usermac=")+8,17);else if(index($1,"phone=")) $1=substr($1,index($1,"phone=")+6,11);print
		 }\'| cut -d "&" -f 1 | sort -k1 -u | sed -e "s/ /\//g" | awk 
		\'BEGIN{FS=OFS="/"}{count[$2"/"$4"/"$5"/"$6]++}END{for(name in count)print name,count[name]}\' ';
		exec($sh_uv,$adsclickuv);
	
		foreach ($downappspv as $n)
		{
			$tep = array();
			list($tep['action'],$tep['showtype'],$tep['ad_id'],$tep['sys'],$tep['num']) = explode('/', $n);
			$tep += array('date'=>$this->DATE,'stationid'=>$this->STATIONID,'from'=>'pm','type'=>'pv');
			$this->Insert($tep,'rha_ads_info');
		}
		foreach ($adsclickuv as $n1)
		{
			$tep = array();
			list($tep['action'],$tep['showtype'],$tep['ad_id'],$tep['sys'],$tep['num']) = explode('/', $n1);		
			$tep += array('date'=>$this->DATE,'stationid'=>$this->STATIONID,'from'=>'pm','type'=>'uv');
			$this->Insert($tep,'rha_ads_info');
		}
	}

	/**
	 * 广告1点击后进入统计
	 */
	public function Ad1Into(){
		//[index/indexfocus/586]
		$sh_pv = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6)~/index\/indexfocus/ && tolower($7)~/usermac/) print $6,$7}\' | awk \'{$2=substr($2,index($2,"usermac=")+8,17);print $1}\' | wc -l ';
		exec($sh_pv,$ad1intopv);

		if ($ad1intopv[0]) {
			$temppv = array('action'=>'indexfocus','ad_id'=>40,'num'=>$ad1intopv[0],'date'=>$this->DATE,'stationid'=>$this->STATIONID,'from'=>'pm','type'=>'pv');
			$this->Insert($temppv,'rha_ads_info');
		}
		

		$sh_uv = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6)~ /index\/indexfocus/ && tolower($7)~/usermac/) print $6,$7}\' | awk \'{$2=substr($2,index($2,"usermac=")+8,17);print $2}\'|sort -u | wc -l ';
		exec($sh_uv,$ad1intouv);

		if ($ad1intouv[0]) {
			$tempuv = array('action'=>'indexfocus','ad_id'=>40,'num'=>$ad1intopv[0],'date'=>$this->DATE,'stationid'=>$this->STATIONID,'from'=>'pm','type'=>'uv');
			$this->Insert($tempuv,'rha_ads_info');
		}
	}
	

	

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	

}



error_reporting(E_ALL^E_NOTICE);

//接收stationid
$stationid = $argv[1];
if (!$stationid) {
	exit;
}
$obj = new Adclick($stationid);

$station = $obj->STATION[0];
$check = $obj->CheckDo();
$filelogs = array();
if (!in_array($station['id'], $check)) {
	//$obj->deldir($obj->LOG_BASE_PATH.$station['logfile']);
	$obj->setPath($station['logfile'],$station['logname'],$station['is_moreweb']);
	$obj->setIpFilter($station['logip'], $station['ifconf'], $station['ap'],$station['xuip'],$station['is_alone']);
	$obj->Log($station['is_moreweb']);
	$obj->setStationId($station['id']);
	$obj->Ad1ClickStatic();
	$obj->Ad1Into();
	$obj->InCheck();	
	
}