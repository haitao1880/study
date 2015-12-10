<?php
/**
* 电影、音乐等资源的相关数据解析
*/
require './web_record.php';
class Resource extends LogDo
{
	
	public function __construct($stationid)
	{
		parent::__construct($stationid);
	}

	/**
	 * 电影、音乐的点击量、暂停，播放统计
	 * movie/hitnum/{电影id}/{系统} 
	 */
	public function HitsPausePlayNum()
	{
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6)~ /record.php/ && tolower($8)~/movie\/hitnum\/|music\/hitnum\/|movie\/pausenum\/|music\/pausenum\/|movie\/playnum\/|music\/playnum\//) print $8 }\' | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$2"/"$3"/"$4]++}END{for(name in count)print name,count[name]}\' ';
		exec($sh,$hitsnum);
		//movie/hitnum/523/android/1
		foreach ($hitsnum as $n)
		{
			$tep = array();
			list($tep['type'],$tep['action'],$tep['resid'],$tep['sys'],$tep['number']) = explode('/', $n);
			
			if (strtolower($tep['type']) == 'movie') {
				$tep['type'] = 3;
			}elseif(strtolower($tep['type']) == 'music'){
				$tep['type'] = 4;
			}			

			if($tep['sys'] == '-'){
				continue;
			}			
			$tep += array('date'=>$this->DATE,'stationid'=>$this->STATIONID,'from'=>'pm');
			$this->Insert($tep,'rha_movie_music');
			
		}
	}

	/**
	 * 电影音乐的播放时长统计
	 * movie/playtime/{电影id}/{时长}/{系统} 
	 */
	public function PlayTime()
	{
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6)~ /record.php/ && tolower($8)~/movie\/playtime\/|music\/playtime\//) print $8 }\' | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$2"/"$3"/"$5]+= $4}END{for(name in count)print name,count[name]}\' ';
		exec($sh,$playtime);
		//movie/hitnum/366/android/3
		foreach ($playtime as $n)
		{
			$tep = array();
			list($tep['type'],$tep['action'],$tep['resid'],$tep['sys'],$tep['number']) = explode('/', $n);
			
			if (strtolower($tep['type']) == 'movie') {
				$tep['type'] = 3;
			}elseif(strtolower($tep['type']) == 'music'){
				$tep['type'] = 4;
			}			

			if($tep['sys'] == '-'){
				continue;
			}			
			$tep += array('date'=>$this->DATE,'stationid'=>$this->STATIONID,'from'=>'pm');
			$this->Insert($tep,'rha_movie_music');
			
		}
	}


	/**
	 * 插入已经完成的log
	 */
	public function InCheck()
	{
		$int = array(
			'stationid' => 	$this->STATIONID,
			'type'		=>	4,
			'ctime'		=>	date('Y-m-d')	
		);
		$this->Insert($int,'rha_scriptlog');
	}

	/**
	 * 获取已经上传的stationid
	 * @return array
	 */
	public function CheckDo()
	{
		$s = $this->db->query('Select `stationid` From `rha_scriptlog` Where `type` = 4 And `ctime` = CURRENT_DATE()');
		$s->setFetchMode(PDO::FETCH_ASSOC);
		$sid = $s->fetchAll();
		$ss = array();
		foreach ($sid as $v)
		{
			$ss[] = $v['stationid'];
		}
		return $ss;
	}


}



error_reporting(E_ALL^E_NOTICE);
//接收stationid
$stationid = $argv[1];
if (!$stationid) {
	exit;
}
$obj = new Resource($stationid);

$station = $obj->STATION[0];


$check = $obj->CheckDo();

if (!in_array($station['id'], $check)) {
	$obj->setPath($station['logfile'],$station['logname']);
	$obj->setIpFilter($station['logip'], $station['ifconf'], $station['ap'],$station['xuip'],$station['is_alone']);
	$obj->setStationId($station['id']);
	$obj->HitsPausePlayNum();
	$obj->PlayTime();
}