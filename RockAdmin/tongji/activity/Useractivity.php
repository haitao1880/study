<?php
/**
* 元用户活动内容统计  抢红包，抽奖、等
*/
require '/home/upload/nginxlog/newactivity/dolog.php';
class Useractivity extends LogDo
{	
	protected $today;
	protected $yesterday;
	protected $hour;
	protected $APPNAME;
	public function __construct($stationid)
	{
		parent::__construct($stationid);
		$this->DATE = date('Y-m-d-H',strtotime("-1 hour"));
		//$this->DATE = '2015-06-15-13';
		$this->today = date('Y_m_d',strtotime("-1 hour"));
		$this->yesterday = date('Y_m_d',strtotime("-1 day"));
		$this->hour = date('H',strtotime("-1 hour"));
		//$this->hour = '13';
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
		$s = $this->db->query('Select `stationid` From `rha_scriptlog` Where `type` = 8 And `ctime` = CURRENT_DATE() and hour = '.$this->hour);
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
			'type'		=>	8,
			'ctime'		=>	date('Y-m-d',strtotime("-1 hour")),
			'hour'	    =>  $this->hour
		);
		$this->Insert($int,'rha_scriptlog');
	}
	
	//sindex uv
	public function Sindex_uv()
	{		
		$sh = $this->CAT_FILE_WEB.'| awk \'{if(tolower($7)~/index\/sindex/ && tolower($11)~/welcome\?phone=[0-9]/) print $11}\' | grep -Po "\d{11}" | sort -u | wc -l';
		exec($sh,$sindex_uv);
		
		$tep = array('action'=>'sindex_uv','num'=>$sindex_uv[0],'date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'uv','sys'=>'');
		$this->Insert($tep,'rha_user_activitys');
		
	}


	/**
	 * 弹出窗口pv/uv [sindex/alertwindow/1888888888/ios]
	 */
	public function alertwindow(){
		$sh_pv = $this->CAT_FILE_WEB.'| awk \'{if(tolower($7)~ /record.php\?sindex\/alertwindow/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$4]++}END{for(name in count) print name,count[name]}\'';
		exec($sh_pv,$alertwindowPv);
		foreach($alertwindowPv as $pv){
			$tep = array();
			list($tep['action'],$tep['sys'],$tep['num']) = explode('/', $pv);
					
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'pv');
			$this->Insert($tep,'rha_user_activitys');
		}

		$sh_uv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /record.php\?sindex\/alertwindow/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | sort -t/ -n -k3 -u | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$3"/"$4]++}END{for(name in count) print name,count[name]}\'';
		exec($sh_uv,$alertwindowUv);
		foreach($alertwindowUv as $uv){
			$tep = array();
			list($tep['action'],$tep['phone'],$tep['sys'],$tep['num']) = explode('/', $uv);
					
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'uv');
			$this->Insert($tep,'rha_user_activitys');
		}
	}

	/**
	 * 红包点击pv/uv [click/redpacket/15888888888/ios]
	 */
	public function ClickRedPacket(){
		$sh_pv = $this->CAT_FILE_WEB.'| awk \'{if(tolower($7)~ /record.php\?click\/redpacket/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$4]++}END{for(name in count) print name,count[name]}\'';
		exec($sh_pv,$ClickRedPacketPv);
		foreach($ClickRedPacketPv as $pv){
			$tep = array();
			list($tep['action'],$tep['sys'],$tep['num']) = explode('/', $pv);
					
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'pv');
			$this->Insert($tep,'rha_user_activitys');
		}

		$sh_uv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /record.php\?click\/redpacket/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | sort -t/ -n -k3 -u | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$3"/"$4]++}END{for(name in count) print name,count[name]}\'';
		exec($sh_uv,$ClickRedPacketUv);
		foreach($ClickRedPacketUv as $uv){
			$tep = array();
			list($tep['action'],$tep['phone'],$tep['sys'],$tep['num']) = explode('/', $uv);
					
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'uv');
			$this->Insert($tep,'rha_user_activitys');
		}
	}

	/**
	 * 弹窗关闭统计pv/uv [click/alertclose/18888888888/ios]
	 */
	public function AlertClose(){
		$sh_pv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /record.php\?click\/alertclose/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | wc -l';
		exec($sh_pv,$AlertClosePv);
		
		if ($AlertClosePv[0]) {
			$data = array('action'=>'alertclose','num'=>$AlertClosePv[0],'date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'pv');
			$this->Insert($data,'rha_user_activitys');
		}


		$sh_uv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /record.php\?click\/alertclose/) print $7}\' | cut -d "?" -f 2| grep -Po "\d{11}"|sort -u';

		exec($sh_uv,$AlertCloseUv);
		foreach($AlertCloseUv as $uv){
			$data1 = array('action'=>'alertclose','date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'uv','phone'=>$uv);
			$this->Insert($data1,'rha_user_activitys');
		}
		
	}


	/**
	 * 弹窗抽奖统计pv[click/alertdraw/18888888888/ios]
	 */
	public function AlertDraw(){
		$sh_pv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /record.php\?click\/alertdraw/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | wc -l';
		exec($sh_pv,$AlertDrawPv);
		
		if ($AlertDrawPv[0]) {
			$data = array('action'=>'alertdraw','num'=>$AlertDrawPv[0],'date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'pv');
			$this->Insert($data,'rha_user_activitys');
		}


		$sh_uv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /record.php\?click\/alertdraw/) print $7}\' | cut -d "?" -f 2| grep -Po "\d{11}"|sort -u ';

		exec($sh_uv,$AlertDrawUv);
		foreach($AlertDrawUv as $uv){
			$data1 = array('action'=>'alertdraw','date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'uv','phone'=>$uv);
			$this->Insert($data1,'rha_user_activitys');
		}
		
	}

	/**
	 * 进入活动页面pv/uv [/user/activity?phone=18888888888]
	 * pv[activity/redpacket/9]
	 * uv[activity/15883346740/redpacket/1]
	 */
	public function ActivityPage(){
		$sh_pv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /user\/activity\?phone=[0-9]/) print $7}\' | sed -e "s/\?phone=/\//g;s/\&/\//g" | awk -F "/" \'BEGIN{FS=OFS="/"}{count[$3"/"$5]++}END{for(name in count) print name,count[name]}\'';
		exec($sh_pv,$ActivityPagePv);

		foreach($ActivityPagePv as $pv){
			list($tep['action'],$tep['from'],$tep['num']) = explode('/',$pv);
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'pv','phone'=>'');
			$this->Insert($tep,'rha_user_activitys');
		}
		
		$sh_uv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /user\/activity\?phone=[0-9]/) print $7}\' | sed -e "s/\?phone=/\//g;s/\&/\//g" | sort -t/ -k3 -u | awk -F "/" \'BEGIN{FS=OFS="/"}{count[$3"/"$4"/"$5]++}END{for(name in count) print name,count[name]}\' ';

		exec($sh_uv,$ActivityPageUv);
		foreach($ActivityPageUv as $uv){
			list($tep1['action'],$tep1['phone'],$tep1['from'],$tep1['num']) = explode('/',$uv);
			$tep1 += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'uv');
			$this->Insert($tep1,'rha_user_activitys');
		}
		
	}

	/**
	 * app下载统计详情
	 * downapp/123/useractivity/15288888888/ios/999
	 */
	public function DownAppInfo(){

		$sh = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /downapp\/[0123456789]+\//) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$2"/"$3"/"$4"/"$5]++}END{for(name in count) print name,count[name]}\' ';
		exec($sh,$downapps);
	
		foreach ($downapps as $n)
		{
			$tep = array();
			list($tep['action'],$tep['appid'],$tep['downpage'],$tep['phone'],$tep['sys'],$tep['num']) = explode('/', $n);
			
			if (is_string($tep['appid']) && strtolower($tep['appid']) == 'trainapp') {
				$tep['appid'] = 12;
			}
			//获取app的类型和名称
			$this->GetAppNameType($tep['appid']);
					
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'appname'=>$this->APPNAME);
			$this->Insert($tep,'rha_activity_downapps');
			
		}
	}



	/**
	 * 短信发送pv/uv [send/message/15888888888/ios]
	 */
	public function SendMessage(){
		$sh_pv = $this->CAT_FILE_WEB.'| awk \'{if(tolower($7)~ /record.php\?send\/message/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$4]++}END{for(name in count) print name,count[name]}\' ';
		exec($sh_pv,$SendMessagePv);
		foreach($SendMessagePv as $pv){
			$tep = array();
			list($tep['action'],$tep['sys'],$tep['num']) = explode('/', $pv);
					
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'pv');
			$this->Insert($tep,'rha_user_activitys');
		}

		$sh_uv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /record.php\?send\/message/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | sort -t/ -n -k3 -u | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$3"/"$4]++}END{for(name in count) print name,count[name]}\' ';
		exec($sh_uv,$SendMessageUv);
		foreach($SendMessageUv as $uv){
			$tep = array();
			list($tep['action'],$tep['phone'],$tep['sys'],$tep['num']) = explode('/', $uv);
					
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'uv');
			$this->Insert($tep,'rha_user_activitys');
		}
	}


	/**
	 * 问卷调查pv/uv [user/question/18888888888/ios/{message/web}]
	 */
	public function UserQuestion(){
		$sh_pv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /record.php\?user\/question/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$4"/"$5]++}END{for(name in count) print name,count[name]}\'';
		exec($sh_pv,$UserQuestionPv);
		foreach($UserQuestionPv as $pv){
			$tep = array();
			list($tep['action'],$tep['from'],$tep['sys'],$tep['num']) = explode('/', $pv);
					
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'pv');
			$this->Insert($tep,'rha_user_activitys');
		}

		$sh_uv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /record.php\?user\/question/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | sort -t/ -n -k3 -u | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$3"/"$4"/"$5]++}END{for(name in count) print name,count[name]}\' ';
		exec($sh_uv,$UserQuestionUv);
		foreach($UserQuestionUv as $uv){
			$tep = array();
			list($tep['action'],$tep['phone'],$tep['from'],$tep['sys'],$tep['num']) = explode('/', $uv);
					
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'uv');
			$this->Insert($tep,'rha_user_activitys');
		}
	}

	/**
	 * 参与抽奖pv/uv [click/lottery/15888888888/ios]
	 */
	public function Lottery(){
		$sh_pv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /record.php\?click\/lottery/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$4]++}END{for(name in count) print name,count[name]}\' ';
		exec($sh_pv,$LotteryPv);
		foreach($LotteryPv as $pv){
			$tep = array();
			list($tep['action'],$tep['sys'],$tep['num']) = explode('/', $pv);
					
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'pv');
			$this->Insert($tep,'rha_user_activitys');
		}

		$sh_uv = $this->CAT_FILE_WEB.' | awk \'{if(tolower($7)~ /record.php\?click\/lottery/) print $7}\' | cut -d "?" -f 2 | cut -d "&" -f 1 | sort -t/ -n -k3 -u | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$3"/"$4]++}END{for(name in count) print name,count[name]}\' ';
		exec($sh_uv,$LotteryUv);
		foreach($LotteryUv as $uv){
			$tep = array();
			list($tep['action'],$tep['phone'],$tep['sys'],$tep['num']) = explode('/', $uv);
					
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'numtype'=>'uv');
			$this->Insert($tep,'rha_user_activitys');
		}
	}


	/**
	 * 根据appid获取app的类型和名称
	 * @param [type] $appid [description]
	 */
	protected function GetAppNameType($appid){
		if (is_numeric($appid)) {
			$p = $this->db->query("Select `appname` From `rha_apps` where id = $appid");
			$p->setFetchMode(PDO::FETCH_ASSOC);
			$info = $p->fetchAll();
			$this->APPNAME = $info[0]['appname'];
		}
			
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

	/**
	 * 各大手机品牌的用户使用量
	 * [iphone/12753]
	 */
	public function MobileBrand(){
		$sh = $this->CAT_FILE_WEB.' |grep -i "usermac" | awk \'{if(index($0,"usermac=")) $1=substr($0,index($0,"usermac=")+8,17);a=index($0,$12);print $1,substr($0,a)}\' | awk \'{a[$1]=$0}END{for(i in a)print a[i]}\'| grep -io "iphone\|motorola\|NOKIA\|BlackBerry\|Microsoft\|Nexus\|SAMSUNG\|SAMSUNG\|Sony\|ZTE\|Coolpad\|Lenovo\|oppo\|gionee\|meizu\|MI\|HM\|vivo\|huawei\|k-touch\|htc" | awk \'BEGIN{FS=OFS="/"}{count[tolower($1)]++}END{for(name in count)print name,count[name]}\' ';
		exec($sh,$MobileBrand);

		foreach($MobileBrand as $v){
			list($tep['name'],$tep['num']) = explode('/', $v);
					
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'type'=>'mobile');
			$this->Insert($tep,'rha_mobile_ranklist');
		}
		
	}

	/**
	 * 各大浏览器的使用量
	 * [iphone/12753]
	 */
	public function BrowserUse(){
		$sh = $this->CAT_FILE_WEB.' | grep -i "usermac" | awk \'{if(index($0,"usermac=")) $1=substr($0,index($0,"usermac=")+8,17);a=index($0,$12);print $1,substr($0,a)}\' | awk \'{a[$1]=$0}END{for(i in a)print a[i]}\'| grep -io "MQQBrowser\|UCBrowser\|Opera\|baidubrowser\|baiduboxapp\|SogouMobileBrowser\|360browser\|Chrome\|hao123\|LBBROWSER\|Mb2345Browser\|MiuiBrowser" | awk \'BEGIN{FS=OFS="/"}{count[tolower($1)]++}END{for(name in count)print name,count[name]}\'  ';
		exec($sh,$BrowserUse);

		foreach($BrowserUse as $v){
			list($tep['name'],$tep['num']) = explode('/', $v);
					
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'type'=>'browser');
			$this->Insert($tep,'rha_mobile_ranklist');
		}

		//苹果safari
		$sh_ios = $this->CAT_FILE_WEB.' | grep -i "usermac" | awk \'{if(index($0,"usermac=")) $1=substr($0,index($0,"usermac=")+8,17);a=index($0,$12);print $1,substr($0,a)}\' | awk \'{a[$1]=$0}END{for(i in a)print a[i]}\'|grep -i "mac" |grep -io "safari" | awk \'BEGIN{FS=OFS="/"}{count[tolower($1)]++}END{for(name in count)print name,count[name]}\'  ';
		exec($sh_ios,$BrowserUse_ios);
		list($ios['name'],$ios['num']) = explode('/', $BrowserUse_ios[0]);
		$ios += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'type'=>'browser');
			$this->Insert($ios,'rha_mobile_ranklist');
		
	}

	/**
	 * 安卓、苹果手机系统使用人数
	 * [android/19460]
	 */
	public function SysUse(){
		$sh = $this->CAT_FILE_WEB.' | grep -i "usermac" | awk \'{if(index($0,"usermac=")) $1=substr($0,index($0,"usermac=")+8,17);a=index($0,$12);print $1,substr($0,a)}\' | awk \'{a[$1]=$0}END{for(i in a)print a[i]}\' | grep -i -v "window"  | grep -io "android\|mac" | awk \'BEGIN{FS=OFS="/"}{count[tolower($1)]++}END{for(name in count)print name,count[name]}\'  ';
		exec($sh,$SysUse);

		foreach($SysUse as $v){
			list($tep['name'],$tep['num']) = explode('/', $v);
					
			$tep += array('date'=>$this->today,'stationid'=>$this->STATIONID,'hour'=>$this->hour,'type'=>'sys');
			$this->Insert($tep,'rha_mobile_ranklist');
		}
		
	}
	

}



error_reporting(E_ALL^E_NOTICE);

//接收stationid
$stationid = $argv[1];
if (!$stationid) {
	exit;
}
$obj = new Useractivity($stationid);

$station = $obj->STATION[0];
$check = $obj->CheckDo();
$filelogs = array();
if (!in_array($station['id'], $check)) {
	$obj->deldir($obj->LOG_BASE_PATH.$station['logfile']);
	$obj->setPath($station['logfile'],$station['logname'],$station['is_moreweb']);
	$obj->setIpFilter($station['logip'], $station['ifconf'], $station['ap'],$station['xuip'],$station['is_alone']);
	$obj->Log($station['is_moreweb']);
	$obj->setStationId($station['id']);

	$obj->Sindex_uv();
	$obj->alertwindow();
	$obj->ClickRedPacket();
	$obj->ActivityPage();
	$obj->DownAppInfo();
	$obj->SendMessage();
	$obj->UserQuestion();
	$obj->Lottery();
	$obj->AlertClose();
	$obj->AlertDraw();
	/*$obj->MobileBrand();
	$obj->BrowserUse();
	$obj->SysUse();*/


	$obj->InCheck();
	
}
