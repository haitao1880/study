<?php
/**
* 元用户活动内容统计  抢红包，抽奖、等
*/
require '/home/upload/nginxlog/newactivity/dolog.php';
class SpreadTrainApp extends LogDo
{	
	protected $today;
	protected $yesterday;
	protected $hour;
	protected $APPNAME;
	public function __construct($stationid)
	{
		parent::__construct($stationid);
		$this->DATE = date('Y-m-d-H',strtotime("-1 hour"));
		// $this->DATE = '2015-08-06-13';
		$this->today = date('Y_m_d',strtotime("-1 hour"));
		$this->yesterday = date('Y_m_d',strtotime("-1 day"));
		$this->hour = date('H',strtotime("-1 hour"));
		// $this->hour = '13';
		$this->WLNAME = 'm_wonaonao_access_'.$this->hour.'.log';

	}

	//日志解压
	public function Log($is_moreweb)
	{	
		if (!file_exists($this->LOG1) && !file_exists($this->LOG2)) {
			return ;
		}
		
		if (file_exists($this->WEB2.$this->WLNAME)) {
			return;
		}
		if (!is_dir($this->BASE_PATH . '/web1/www/')) {
			mkdir ($this->BASE_PATH . '/web1/www/' , 0777 , true);
			mkdir ($this->BASE_PATH . '/web2/www/' , 0777 , true);
		}
		
		if ($is_moreweb) {
			$sh1 = 'tar -zxvf '.$this->LOG1.' -C '.$this->BASE_PATH.'/web1/www';
			$sh2 = 'tar -zxvf '.$this->LOG2.' -C '.$this->BASE_PATH.'/web2/www';
			passthru($sh1);
			passthru($sh2);
		}else{
			$sh1 = 'tar -zxvf '.$this->LOG1.' -C '.$this->BASE_PATH.'/web1/www';
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
		$s = $this->db->query('Select `stationid` From `rha_scriptlog` Where `type` = 10 And `ctime` = CURRENT_DATE() and hour = '.$this->hour);
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
			'type'		=>	10,
			'ctime'		=>	date('Y-m-d',strtotime("-1 hour")),
			'hour'	    =>  $this->hour
		);
		$this->Insert($int,'rha_scriptlog');
	}

	/**
	 * 推广弹窗、海报导航点击、弹窗关闭点击
	 * click/spreadwindow/1888888/ios
	 * click/spreadnav/188888/ios
	 * click/closewindow/15222222222/ios
	 * click/jumptosindex/15222222222/ios
	 * /user/activedetails
	 */
	public function Entrance(){
		$sh_pv = $this->CAT_FILE_WEB.'| awk \'{if(tolower($7)~ /record.php\?click\/spreadwindow|record.php\?click\/spreadnav|record.php\?click\/closewindow|record.php\?click\/jumptosindex/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$4]++}END{for(name in count) print name,count[name]}\'';
		exec($sh_pv,$EntrancePv);

		foreach($EntrancePv as $pv){
			list($tep['action'],$tep['sys'],$tep['num']) = explode('/', $pv);
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'pv');
			$this->Insert($tep,'rha_spread_trainapp');
		}

		$sh_uv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /record.php\?click\/spreadwindow|record.php\?click\/spreadnav|record.php\?click\/closewindow/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | awk -F "/" \'{count[$2"/"$3]=$0}END{for(name in count) print count[name]}\' | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$3"/"$4]++}END{for(name in count) print name,count[name]}\'';
		exec($sh_uv,$EntranceUv);
		foreach($EntranceUv as $uv){
			list($tep_uv['action'],$tep_uv['phone'],$tep_uv['sys'],$tep_uv['num']) = explode('/', $uv);	
			$tep_uv += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'uv');
			$this->Insert($tep_uv,'rha_spread_trainapp');
		}
	}

	/**
	 * 进入的页面统计
	 * 活动主页、花千骨专区、盗墓笔记专区
	 * /user/index/15222222222/spreadwindow
	 * /user/hqg?phone=15222222222&from=[spreadwindow/spreadnav]
	 * /user/dmbj?phone=15222222222&from=[spreadwindow/spreadnav]
	 * /user/activedetails?phone=15222222222&from=[spreadwindow/spreadnav]
	 */
	public function IntoPage()
	{
		// [/userindex/15222222222/spreadwindow]
		$sh_pv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /user\/index\?phone=[0-9]|user\/hqg\?phone=[0-9]|user\/dmbj\?phone=[0-9]|user\/activedetails\?phone=[0-9]/) print $7}\' | sed -e "s/\?phone=/\//g;s/\&from=/\//g;s/user\/index/userindex/;s/user\/hqg/userhqg/;s/user\/dmbj/userdmbj/;s/user\/activedetails/useractivedetails/" | awk -F "/" \'BEGIN{FS=OFS="/"}{count[$2"/"$4]++}END{for(name in count) print name,count[name]}\'';
		exec($sh_pv,$IntoPagePv);

		foreach($IntoPagePv as $pv){
			list($tep['action'],$tep['from'],$tep['num']) = explode('/',$pv);
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'pv');
			$this->Insert($tep,'rha_spread_trainapp');
		}
		
		$sh_uv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /user\/index\?phone=[0-9]|user\/hqg\?phone=[0-9]|user\/dmbj\?phone=[0-9]|user\/activedetails\?phone=[0-9]/) print $7}\' | sed -e "s/\?phone=/\//g;s/\&from=/\//g;s/user\/index/userindex/;s/user\/hqg/userhqg/;s/user\/dmbj/userdmbj/;s/user\/activedetails/useractivedetails/" | awk -F "/" \'{count[$2"/"$3"/"$4]=$0}END{for(name in count) print count[name]}\' | awk -F "/" \'BEGIN{FS=OFS="/"}{count[$2"/"$3"/"$4]++}END{for(name in count) print name,count[name]}\' ';

		exec($sh_uv,$IntoPageUv);
		foreach($IntoPageUv as $uv){
			list($tep1['action'],$tep1['phone'],$tep1['from'],$tep1['num']) = explode('/',$uv);
			$tep1 += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'uv');
			$this->Insert($tep1,'rha_spread_trainapp');
		}	
	}

	/**
	 * 花千骨、盗墓笔记点击、竞技点击
	 * click/hqg/15222222222/[spreadwindow/spreadnav]/ios
	 * click/activedetails/15222222222/[spreadwindow/spreadnav]/ios
	 */
	public function Click_hqg_dmbj()
	{	
		//[clickhqg/15222222222/spreadwindow/ios]
		$sh_pv = $this->CAT_FILE_WEB.'| awk \'{if(tolower($7)~ /record.php\?click\/hqg|record.php\?click\/dmbj|record.php\?click\/activedetails/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | sed -e "s/click\/hqg/clickhqg/;s/click\/dmbj/clickdmbj/;s/click\/activedetails/clickactivedetails/" | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$3"/"$4]++}END{for(name in count) print name,count[name]}\'';
		exec($sh_pv,$Click_hqg_dmbj_pv);

		foreach($Click_hqg_dmbj_pv as $pv){
			list($tep['action'],$tep['from'],$tep['sys'],$tep['num']) = explode('/', $pv);
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'pv');
			$this->Insert($tep,'rha_spread_trainapp');
		}

		$sh_uv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /record.php\?click\/hqg|record.php\?click\/dmbj|record.php\?click\/activedetails/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1  | sed -e "s/click\/hqg/clickhqg/;s/click\/dmbj/clickdmbj/;s/click\/activedetails/clickactivedetails/" | awk -F "/" \'{count[$1"/"$2"/"$3]=$0}END{for(name in count) print count[name]}\' | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$2"/"$3"/"$4]++}END{for(name in count) print name,count[name]}\'';
		exec($sh_uv,$Click_hqg_dmbj_uv);
		foreach($Click_hqg_dmbj_uv as $uv){
			list($tep_uv['action'],$tep_uv['phone'],$tep_uv['from'],$tep_uv['sys'],$tep_uv['num']) = explode('/', $uv);	
			$tep_uv += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'uv');
			$this->Insert($tep_uv,'rha_spread_trainapp');
		}
	}

	/**
	 * 伙伴下载统计
	 * down/spreadapp/userindex/15222222222/spreadwindow/ios
	 */
	public function DownTrainApp()
	{

		$sh_pv = $this->CAT_FILE_WEB.'| awk \'{if(tolower($7)~ /record.php\?down\/spreadapp/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$3"/"$5"/"$6]++}END{for(name in count) print name,count[name]}\'';
		exec($sh_pv,$DownTrainAppPv);

		foreach($DownTrainAppPv as $pv){
			list($tep['action'],$tep['downpage'],$tep['from'],$tep['sys'],$tep['num']) = explode('/', $pv);
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'pv');
			$this->Insert($tep,'rha_spread_trainapp');
		}

		$sh_uv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /record.php\?down\/spreadapp/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | awk -F "/" \'{count[$3"/"$4"/"$5]=$0}END{for(name in count) print count[name]}\' | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$3"/"$4"/"$5"/"$6]++}END{for(name in count) print name,count[name]}\'';
		exec($sh_uv,$DownTrainAppUv);
		foreach($DownTrainAppUv as $uv){
			list($tep_uv['action'],$tep_uv['downpage'],$tep_uv['phone'],$tep_uv['from'],$tep_uv['sys'],$tep_uv['num']) = explode('/', $uv);	
			$tep_uv += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'uv');
			$this->Insert($tep_uv,'rha_spread_trainapp');
		}
	}



}



error_reporting(E_ALL^E_NOTICE);

//接收stationid
$stationid = $argv[1];
if (!$stationid) {
	exit;
}
$obj = new SpreadTrainApp($stationid);

$station = $obj->STATION[0];
$check = $obj->CheckDo();
$filelogs = array();
if (!in_array($station['id'], $check)) {
	//$obj->deldir($obj->LOG_BASE_PATH.$station['logfile']);
	$obj->setPath($station['logfile'],$station['logname'],$station['is_moreweb']);
	$obj->setIpFilter($station['logip'], $station['ifconf'], $station['ap'],$station['xuip'],$station['is_alone']);
	$obj->Log($station['is_moreweb']);
	$obj->setStationId($station['id']);

	$obj->Entrance();
	$obj->IntoPage();
	$obj->Click_hqg_dmbj();
	$obj->DownTrainApp();
	

	$obj->InCheck();
	
}
