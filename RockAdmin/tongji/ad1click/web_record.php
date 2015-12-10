<?php
/**
 * log日志解析
 *
 */
class LogDo
{
	private $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	//private $dsn_content = "mysql:host=192.168.28.201;dbname=rht_admin";
	private $dbuser_content = 'root';
	private $dbpasswd_content = 'Cahw_1MLLqIt';
	private $db ;

	private $LOG_BASE_PATH = '/home/upload/nginxlog/';
	//private $LOG_BASE_PATH = '/data/store/upload/nginxlog/';
	private $PATH ;
	private $DATE ;
	private $DATE1 ;
	private $BASE_PATH ;
	private $WEB ;

	private $WLNAME = 'm_wonaonao_record_**.log';

	private $CAT_FILE_WEB ;
	private $CAT_BEFORFILE_WEB ; //web 认证前日志

	public  $STATION ;

	private $IPFILTER ;
	private $BEFORIPFILTER;//认证前

	private $STATIONID;
	private $TP_MAC ;
	private $MAC_S ;

	private $WEB_MAIN =array();
	private $WEB_DETAIL = array();

	public function __construct($stationid)
	{
		$this->DATE = date('Y-m-d',strtotime('-1 day'));
		$this->DATE1 = date('Y_m_d',strtotime('-1 day'));
		// $this->DATE = '2015_09_04';
		$this->db = $this->getdb_content();
		$p = $this->db->query("Select `id`,`logfile`,`logip`,`ifconf`,`logname`,`ap`,`xuip`,`is_alone` From `rha_station` where id = $stationid");
		$p->setFetchMode(PDO::FETCH_ASSOC);
		$this->STATION = $p->fetchAll();
	}
	/**
	 * 设置通用文件路径
	 * @param string $logfile
	 */
	public function setPath($logfile,$logname)
	{
		$this->PATH = $this->LOG_BASE_PATH.$logfile.'/';
		$this->BASE_PATH = $this->PATH.$this->DATE;
		$this->WEB = $this->BASE_PATH . '/web*/www/';

	}
	/**
	 * 获取已经上传的stationid
	 * @return array
	 */
	public function CheckDo()
	{
		$s = $this->db->query('Select `stationid` From `rha_scriptlog` Where `type` = 7 And `ctime` = CURRENT_DATE()');
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
			'type'		=>	7,
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
	private function getxuip($xuip,$ap){
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
		// $ip = str_replace(',','\|',$ip);
		$ip = str_replace(array('.',','),array('\.','\|'),$ip);
		$fil = $conf ? '' : ' -v ';
		if ($is_alone) {
			$this->IPFILTER = ' | grep '.$fil.' "'.$ip.'" ';

			$this->CAT_FILE_WEB = ' cat '.$this->WEB.$this->WLNAME.$this->IPFILTER;
		}else{
			$this->BEFORIPFILTER = $this->getxuip($xuip,$ap);//认证之前
			$this->IPFILTER = ' | grep '.$fil.' "'.$ip.'" ';//认证后

			$this->CAT_FILE_WEB = ' cat '.$this->WEB.$this->WLNAME.$this->IPFILTER;
			//认证前
			$this->CAT_BEFORFILE_WEB = ' cat '.$this->WEB.$this->WLNAME.$this->BEFORIPFILTER;
		}

		if($ap == 1){
			$this->TP_MAC = 'mac=';
			$this->MAC_S = '4,12';
		}else{
			$this->TP_MAC = 'usermac=';
			$this->MAC_S = '8,27';
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
	private function Insert($data,$tb='')
	{
		$key =  $var = '';
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

	private function getdb_content()
	{
		$db = new PDO($this->dsn_content, $this->dbuser_content, $this->dbpasswd_content);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->exec('set names utf8');

		return $db;
	}

	/**
	 * 广告1、广告2点击统计
	 * index/ads/click/40/ios
	 * index/sindexbanner/click/{广告id}/{系统}
	 * index/sindexnav/click/{广告id}/{系统}
	 */
	public function Ad1ClickStatic(){
		//index/ads/click/40/ios/321
		$sh_pv = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6)~ /record.php/ && tolower($8)~/^index\/ads\/click\/|^index\/welcome\/click\/|^index\/sindexbanner\/click\/|^index\/sindexnav\/click\//) print $8}\' | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$2"/"$3"/"$4"/"$5]++}END{for(name in count) print name,count[name]}\' ';
		exec($sh_pv,$adsclickpv);

		//index/ads/click/40/ios/272
		$sh_uv = $this->CAT_FILE_WEB.'| awk -F "\[\|cut\|\]" \'{if(tolower($6)~ /record.php/ && tolower($8)~/^index\/ads\/click\/|^index\/welcome\/click\/|^index\/sindex\/click\//) print $7,$8}\' | awk \'{if(index($1,"usermac=")) $1=substr($1,index($1,"usermac=")+8,17);else if(index($1,"phone=")) $1=substr($1,index($1,"phone=")+6,11);else $1="";print }\'| cut -d "&" -f 1 | awk \'{count[$1]=$0}END{for(name in count) print count[name]}\' | sed -e "s/ /\//g" | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$3"/"$4"/"$5"/"$6]++}END{for(name in count)print name,count[name]}\' ';
		exec($sh_uv,$adsclickuv);

		foreach ($adsclickpv as $n)
		{
			$tep = array();
			list($tep['action'],$tep['from'],$tep['showtype'],$tep['ad_id'],$tep['sys'],$tep['num']) = explode('/', $n);
			$tep += array('date'=>$this->DATE1,'stationid'=>$this->STATIONID,'type'=>'pv');
			$this->Insert($tep,'rha_ads_info');
		}
		foreach ($adsclickuv as $n1)
		{
			$tep1 = array();
			list($tep1['action'],$tep1['from'],$tep1['showtype'],$tep1['ad_id'],$tep1['sys'],$tep1['num']) = explode('/', $n1);
			$tep1 += array('date'=>$this->DATE1,'stationid'=>$this->STATIONID,'type'=>'uv');
			$this->Insert($tep1,'rha_ads_info');
		}
	}

	/**
	 * 广告1点击后进入统计
	 * /index/indexfocus?adsname=zgrs&from=index
	 * /index/indexfocus?adsname=zgrs&from=welcome&phone=18888888888
	 */
	public function Ad1Info(){
		//[indexfocus/hn/index/2]
		$sh_pv = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6)~/index\/indexfocus/ && tolower($8)~/from=index/ && tolower($7)~/usermac/) print $6,$8,$7}\'| sed -e "s/ prj=pm&act=indexfocus&mod=index&/?/g" | awk \'{$2=substr($2,index($2,"usermac=")+8,17);print $1}\' | sed -e "s/?adsname=/\//g;s/&from=/\//g" | awk -F "/" \'BEGIN{FS=OFS="/"}{count[$2"/"$3"/"$4]++}END{for(name in count)print name,count[name]}\'';

		exec($sh_pv,$ad1intopv);
		foreach($ad1intopv as $pv){
			list($temp_pv['action'],$temp_pv['ad_id'],$temp_pv['from'],$temp_pv['num']) = explode('/',$pv);
			$temp_pv['ad_id'] = $this->GetAdsId($temp_pv['ad_id']);
			$temp_pv += array('date'=>$this->DATE1,'stationid'=>$this->STATIONID,'type'=>'pv');
			//print_r($temp_pv).PHP_EOL;
			$this->Insert($temp_pv,'rha_ads_info');	
		}	
+
		//indexfocus/hn/index/2
		$sh_uv = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6)~/index\/indexfocus/ && tolower($8)~/from=index/ && tolower($7)~/usermac/) print $6,$8,$7}\'| sed -e "s/ prj=pm&act=indexfocus&mod=index&/?/g" | awk \'{$2=substr($2,index($2,"usermac=")+8,17);print $1,$2}\' | awk \'{count[$2]=$1}END{for(name in count)print count[name]}\' | sed -e "s/?adsname=/\//g;s/&from=/\//g" | awk -F "/" \'BEGIN{FS=OFS="/"}{count[$2"/"$3"/"$4]++}END{for(name in count)print name,count[name]}\'';
		exec($sh_uv,$ad1intouv);
		foreach($ad1intouv as $uv){
			list($temp_uv['action'],$temp_uv['ad_id'],$temp_uv['from'],$temp_uv['num']) = explode('/',$uv);
			$temp_uv['ad_id'] = $this->GetAdsId($temp_uv['ad_id']);
			$temp_uv += array('date'=>$this->DATE1,'stationid'=>$this->STATIONID,'type'=>'uv');
			$this->Insert($temp_uv,'rha_ads_info');
		}		

	}

	/**
	 * 广告2点击后进入统计
	 * /index/indexfocus?adsname=zgrs&from=welcome&phone=18888888888
	 */
	public function Ad2Info(){
		//[indexfocus/hn/welcome/1]
		$sh_pv = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6)~/index\/indexfocus/ && tolower($8)~/from=welcome&phone=/) print $6,$8}\' | sed -e "s/ prj=pm&act=indexfocus&mod=index&/?/g" | sed -e "s/?adsname=/\//g;s/&from=/\//g;s/&phone=/\//g" | awk -F "/" \'BEGIN{FS=OFS="/"}{count[$2"/"$3"/"$4]++}END{for(name in count)print name,count[name]}\'';

		exec($sh_pv,$ad2intopv);
		foreach($ad2intopv as $pv){
			list($ad2_pv['action'],$ad2_pv['ad_id'],$ad2_pv['from'],$ad2_pv['num']) = explode('/',$pv);
			$ad2_pv['ad_id'] = $this->GetAdsId($ad2_pv['ad_id']);
			$ad2_pv += array('date'=>$this->DATE1,'stationid'=>$this->STATIONID,'type'=>'pv');
			$this->Insert($ad2_pv,'rha_ads_info');	
		}
		
		//indexfocus/hn/welcome/1
		$sh_uv = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6)~/index\/indexfocus/ && tolower($8)~/from=welcome&phone=/) print $6,$8}\' | sed -e "s/ prj=pm&act=indexfocus&mod=index&/?/g" | sed -e "s/?adsname=/\//g;s/&from=/\//g;s/&phone=/\//g" | awk -F "/" \'{count[$5]=$0}END{for(name in count)print count[name]}\' | awk -F "/" \'BEGIN{FS=OFS="/"}{count[$2"/"$3"/"$4]++}END{for(name in count)print name,count[name]}\'';
		exec($sh_uv,$ad2intouv);
		foreach($ad2intouv as $uv){
			list($ad2_uv['action'],$ad2_uv['ad_id'],$ad2_uv['from'],$ad2_uv['num']) = explode('/',$uv);
			$ad2_uv['ad_id'] = $this->GetAdsId($ad2_uv['ad_id']);
			$ad2_uv += array('date'=>$this->DATE1,'stationid'=>$this->STATIONID,'type'=>'uv');
			$this->Insert($ad2_uv,'rha_ads_info');
		}		
		

	}

	/**
	 * 根据adname获取id
	 *
	 */
	public function GetAdsId($adname){
		$sql = "SELECT id FROM rha_ads WHERE imgurl = '$adname'";
		$res = $this->FetchRow($sql);
		return $res['id'];
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

}


error_reporting(E_ALL^E_NOTICE);
//接收stationid
$stationid = $argv[1];
if (!$stationid) {
	exit;
}
$obj = new LogDo($stationid);
$station = $obj->STATION[0];


$check = $obj->CheckDo();
// foreach ($stations as $k=>$v)
// {
	if (!in_array($station['id'], $check)) {
		$obj->setPath($station['logfile'],$station['logname']);
		$obj->setIpFilter($station['logip'], $station['ifconf'], $station['ap'],$station['xuip'],$station['is_alone']);
		$obj->setStationId($station['id']);

		$obj->clear();
		$obj->Ad1ClickStatic();
		$obj->Ad1Info();
		$obj->Ad2Info();
		$obj->InCheck();
	}


// }
