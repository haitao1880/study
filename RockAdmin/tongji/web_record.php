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

	private $APPNAME;//APP名称
	private $APPTYPE;//app类型
	private $DESCRIPT;//eventrecord中的事件描述
	private $SORTID;

	private $TP_MAC ;
	private $MAC_S ;
	
	private $WEB_MAIN =array();
	private $WEB_DETAIL = array();
	

	public function __construct($stationid)
	{
		$this->DATE = date('Y-m-d',strtotime('-1 day'));
		$this->DATE1 = date('Y_m_d',strtotime('-1 day'));
		// $this->DATE = '2015_11_06';
		$this->db = $this->getdb_content();
		$p = $this->db->query("Select `id`,`logfile`,`logip`,`ifconf`,`logname`,`ap`,`xuip`,`is_alone`,`stationtype` From `rha_station` where id = $stationid");
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
		$s = $this->db->query('Select `stationid` From `rha_scriptlog` Where `type` = 3 And `ctime` = CURRENT_DATE()');
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
			'type'		=>	3,
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
		}elseif($ap == 2){
			$this->TP_MAC = 'usermac=';
			$this->MAC_S = '8,27';
		}elseif($ap == 3){
			$this->TP_MAC = 'usermac=';
			$this->MAC_S = '8,27';
		}
	}

	//入口页uv
	public function Ad_uv($is_alone)
	{	
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /index\/index/ ) print $8,"[|cut|]",$9}\' | awk -F  "\[\|cut\|\]" \'{if(index($1,"'.$this->TP_MAC.'")) $1=substr($1,index($1,"'.$this->TP_MAC.'")+'.$this->MAC_S.');else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh , $ad_uv);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /index\/index/ ) print $8,"[|cut|]",$9}\' | awk -F  "\[\|cut\|\]" \'{if(index($1,"'.$this->TP_MAC.'")) $1=substr($1,index($1,"'.$this->TP_MAC.'")+'.$this->MAC_S.');else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh , $ad_uv);
		}
		
		$result = array();
		
		foreach ($ad_uv as $n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode(' ', $n);
			if($tep['sys'] == '-'){
				break;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'ad','action'=>'visit','detail'=>'uv','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$result[$tep['sys']] = $tep['dtime'];
		}
		
		
		$this->ConArrDETAIL($result,'ad_uv');
		

	}

	//广告页1详情
	public function Ad_Detail($is_alone)
	{	
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($8)  ~ /ad\/show/) print $8}\' | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{count[$3"/"$4]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh,$ad_detail);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($8)  ~ /ad\/show/) print $8}\' | awk \'BEGIN{FS=OFS="/"}{count[$3"/"$4]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh,$ad_detail);
		}
		
		
		foreach ($ad_detail as $n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'ad','action'=>'show','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$data[$tep['detail']][$tep['sys']] = $tep['dtime'];
		}
		if($data){
			$result = array();
			foreach ($data as $m=>$n)
			{
				$result['android'] = $n['android'];
				$result['ios'] = $n['ios'];
				$result['else'] = $n['else'];
				$this->ConArrDETAIL($result,'ad_'.$m);
			}
		}
		
	}
	//注册页uv
	public function Reg_uv($is_alone)
	{	
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB. ' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /index\/register/ ) print $7,"[|cut|]",$9}\' | awk -F  "\[\|cut\|\]" \'{if(index($1,"'.$this->TP_MAC.'")) $1=substr($1,index($1,"'.$this->TP_MAC.'")+'.$this->MAC_S.');else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\'| sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh , $reg_uv);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB. ' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /index\/register/ ) print $7,"[|cut|]",$9}\' | awk -F  "\[\|cut\|\]" \'{if(index($1,"'.$this->TP_MAC.'")) $1=substr($1,index($1,"'.$this->TP_MAC.'")+'.$this->MAC_S.');else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\'| sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh , $reg_uv);
		}
		
		$result = array();
		
		foreach ($reg_uv as $n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode(' ', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'register','action'=>'visit','detail'=>'uv','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$result[$tep['sys']] = $tep['dtime'];
		}	
		$this->ConArrDETAIL($result,'reg_uv');

	}
	//验证码发送
	public function Reg_verify($is_alone)
	{
		//发送成功pv
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($5)  ~ /post/  && tolower($6) ~ /member\/againgetphonecode/ && tolower($10) ~ /status=1/) print $9}\' | awk -F "\[\|cut\|\]" \'{if(tolower($1) ~ /window/) $1="else"; else if(tolower($1) ~ /mac /) $1="ios";else if(tolower($1) ~ /android/) $1="android"; else $1="-"; print}\' | awk \'{count[$1]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh,$verify_success);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($5)  ~ /post/  && tolower($6) ~ /member\/againgetphonecode/ && tolower($10) ~ /status=1/) print $9}\' | awk -F "\[\|cut\|\]" \'{if(tolower($1) ~ /window/) $1="else"; else if(tolower($1) ~ /mac /) $1="ios";else if(tolower($1) ~ /android/) $1="android"; else $1="-"; print}\' | awk \'{count[$1]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh,$verify_success);
		}
		
		$result = array();
		
		foreach ($verify_success as $n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode(' ', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'register','action'=>'verify','detail'=>'success','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$result[$tep['sys']] = $tep['dtime'];
		}
			
		$this->ConArrDETAIL($result,'verify_success');
		//发送失败pv
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($5)  ~ /post/  && tolower($6) ~ /member\/againgetphonecode/ && tolower($10) !~ /status=1/)  print $9}\' | awk -F "\[\|cut\|\]" \'{if(tolower($1) ~ /window/) $1="else"; else if(tolower($1) ~ /mac /) $1="ios";else if(tolower($1) ~ /android/) $1="android"; else $1="-"; print}\' | awk \'{count[$1]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh,$verify_error);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($5)  ~ /post/  && tolower($6) ~ /member\/againgetphonecode/ && tolower($10) !~ /status=1/)  print $9}\' | awk -F "\[\|cut\|\]" \'{if(tolower($1) ~ /window/) $1="else"; else if(tolower($1) ~ /mac /) $1="ios";else if(tolower($1) ~ /android/) $1="android"; else $1="-"; print}\' | awk \'{count[$1]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh,$verify_error);
		}
		
		$result = array();
		
		foreach ($verify_error as $n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode(' ', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'register','action'=>'verify','detail'=>'error','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$result[$tep['sys']] = $tep['dtime'];
		}
				
		$this->ConArrDETAIL($result,'verify_error');
		
	}


	//用户注册登录流水
	public function Loginlog($is_alone)
	{		
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB. ' | awk -F "\[\|cut\|\]" \'{if(tolower($5)  ~ /post/  && tolower($6) ~ /member\/register|member\/checkpasms/ && tolower($10) ~ /success|status=0/)  print $2,"--",$8}\' | awk -F "--" \'{if(index($2,"uName=")) $2=substr($2,index($2,"uName=")+6,11);else if(index($2,"mobile=")) $2=substr($2,index($2,"mobile=")+7,11);else $2="-";print $2,$1}\'|awk \'{count[$1]=$0}END{for(name in count) print count[name]}\'';

			exec($sh,$loginlog);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB. ' | awk -F "\[\|cut\|\]" \'{if(tolower($5)  ~ /post/  && tolower($6) ~ /member\/register|member\/checkpasms/ && tolower($10) ~ /success|status=0/)  print $2,"--",$8}\' | awk -F "--" \'{if(index($2,"uName=")) $2=substr($2,index($2,"uName=")+6,11);else if(index($2,"mobile=")) $2=substr($2,index($2,"mobile=")+7,11);else $2="-";print $2,$1}\'|awk \'{count[$1]=$0}END{for(name in count) print count[name]}\'';
			exec($sh,$loginlog);
		}
		
		$result = array();
		
		foreach ($loginlog as $n)
		{
			$tep = array();
			$loginfo =  explode(' ', $n);
			$tep['username'] = $loginfo[0];
			$tep['logintime'] = strtotime($loginfo[1].' '.$loginfo[2]);
			$tep['stationid'] = $this->STATIONID;

			if($tep['sys'] == '-'){
				continue;
			}			
			$this->Insert($tep,'rha_login_record');
			
		}
			
		
		
	}

	//手机注册
	public function Reg_login($is_alone)
	{
		//注册成功
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB. ' | awk -F "\[\|cut\|\]" \'{if(tolower($5)  ~ /post/  && tolower($6) ~ /member\/register/ && tolower($10) ~ /success|status=0/)  print $8,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"uName=")) $1=substr($1,index($1,"uName=")+6,11);else if(index($1,"mobile=")) $1=substr($1,index($1,"mobile=")+7,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-"; print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';

			exec($sh,$login_success);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB. ' | awk -F "\[\|cut\|\]" \'{if(tolower($5)  ~ /post/  && tolower($6) ~ /member\/register/ && tolower($10) ~ /success|status=0/)  print $8,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"uName=")) $1=substr($1,index($1,"uName=")+6,11);else if(index($1,"mobile=")) $1=substr($1,index($1,"mobile=")+7,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-"; print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh,$login_success);
		}
		
		$result = array();
		
		foreach ($login_success as $n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode(' ', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'register','action'=>'login','detail'=>'success','stationid'=>$this->STATIONID,'from'=>2,'did'=>'mobile');
			$this->Insert($tep);
			$result[$tep['sys']] = $tep['dtime'];
		}
			
		$this->ConArrDETAIL($result,'login_success');
		//注册失败
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB. ' | awk -F "\[\|cut\|\]" \'{if(tolower($5)  ~ /post/  && tolower($6) ~ /member\/register/ && tolower($10) ~ /error/)  print $8,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"uName=")) $1=substr($1,index($1,"uName=")+6,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-"; print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh,$login_error);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB. ' | awk -F "\[\|cut\|\]" \'{if(tolower($5)  ~ /post/  && tolower($6) ~ /member\/register/ && tolower($10) ~ /error/)  print $8,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"uName=")) $1=substr($1,index($1,"uName=")+6,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-"; print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh,$login_error);
		}
		
		$result = array();
		
		foreach ($login_error as $n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode(' ', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'register','action'=>'login','detail'=>'error','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$result[$tep['sys']] = $tep['dtime'];
		}
				
		$this->ConArrDETAIL($result,'login_error');
		
	}


	//首次领取保单成功的注册
	public function Bd_First_Reg_login($is_alone)
	{
		//注册成功
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB. ' | awk -F "\[\|cut\|\]" \'{if(tolower($5)  ~ /post/  && tolower($6) ~ /member\/postbddata/ && tolower($10) ~ /status=1/)  print $8,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"uName=")) $1=substr($1,index($1,"uName=")+6,11);else if(index($1,"mobile=")) $1=substr($1,index($1,"mobile=")+7,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-"; print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';

			exec($sh,$login_success);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB. ' | awk -F "\[\|cut\|\]" \'{if(tolower($5)  ~ /post/  && tolower($6) ~ /member\/postbddata/ && tolower($10) ~ /status=1/)  print $8,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"uName=")) $1=substr($1,index($1,"uName=")+6,11);else if(index($1,"mobile=")) $1=substr($1,index($1,"mobile=")+7,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-"; print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh,$login_success);
		}
		
		$result = array();
		
		foreach ($login_success as $n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode(' ', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'register','action'=>'login','detail'=>'success','stationid'=>$this->STATIONID,'from'=>2,'did'=>'bdfirst');
			$this->Insert($tep);
			$result[$tep['sys']] = $tep['dtime'];
		}
		
	}

	//保单领取不成功，但注册成功的用户
	public function Bd_Not_First_Reg_login($is_alone)
	{
		//注册成功
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB. ' | awk -F "\[\|cut\|\]" \'{if(tolower($5)  ~ /post/  && tolower($6) ~ /member\/postbddata/ && tolower($10) ~ /status=-50|status=-51/)  print $8,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"uName=")) $1=substr($1,index($1,"uName=")+6,11);else if(index($1,"mobile=")) $1=substr($1,index($1,"mobile=")+7,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-"; print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';

			exec($sh,$login_success);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB. ' | awk -F "\[\|cut\|\]" \'{if(tolower($5)  ~ /post/  && tolower($6) ~ /member\/postbddata/ && tolower($10) ~ /status=-50|status=-51/)  print $8,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"uName=")) $1=substr($1,index($1,"uName=")+6,11);else if(index($1,"mobile=")) $1=substr($1,index($1,"mobile=")+7,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-"; print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh,$login_success);
		}
		
		$result = array();
		
		foreach ($login_success as $n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode(' ', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'register','action'=>'login','detail'=>'success','stationid'=>$this->STATIONID,'from'=>2,'did'=>'bdnotfirst');
			$this->Insert($tep);
			$result[$tep['sys']] = $tep['dtime'];
		}
		
	}
	//广告页2uv
	public function Welcome_uv($is_alone,$stationtype,$stationid)
	{	
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($8) ~ /act=welcome&mod=index&phone=/ ) print $8,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"phone=")) $1=substr($1,index($1,"phone=")+6,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
			
			exec($sh,$wel_uv);	
		}else{

			if ($stationtype == '1') {

				if ($stationid == '17') {
					$sh = $this->CAT_BEFORFILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($8) ~ /act=welcome&mod=index&phone=/ ) print $8,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"phone=")) $1=substr($1,index($1,"phone=")+6,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
					exec($sh,$wel_uv);
				}else{
					$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($8) ~ /act=welcome&mod=index&phone=/ ) print $8,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"phone=")) $1=substr($1,index($1,"phone=")+6,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
			
					exec($sh,$wel_uv);
				}
				
			}elseif($stationtype == '2'){
				$sh = $this->CAT_BEFORFILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($8) ~ /act=welcome&mod=index&phone=/ ) print $8,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"phone=")) $1=substr($1,index($1,"phone=")+6,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
				exec($sh,$wel_uv);	

			}
			
		}
		
		$result = array();
		
		foreach ($wel_uv as $n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode(' ', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'welcome','action'=>'visit','detail'=>'uv','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$result[$tep['sys']] = $tep['dtime'];
		}
				
		$this->ConArrDETAIL($result,'welcome_uv');
		
	}
	//广告页2详情
	public function Welcome_Detail($is_alone)
	{	
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($8)  ~ /welcome\/show/) print $8}\' | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{count[$3"/"$4]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh,$wel_detail);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($8)  ~ /welcome\/show/) print $8}\' | cut -d "&" -f 1| awk \'BEGIN{FS=OFS="/"}{count[$3"/"$4]++}END{for(name in count)print name,count[name]}\' ';
			exec($sh,$wel_detail);
		}
		
	
		foreach ($wel_detail as $n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'welcome','action'=>'show','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$data[$tep['detail']][$tep['sys']] = $tep['dtime'];
		}
		if($data){
			$result = array();
			foreach ($data as $m=>$n)
			{
				$result['android'] = $n['android'];
				$result['ios'] = $n['ios'];
				$result['else'] = $n['else'];
				$this->ConArrDETAIL($result,'welcome_'.$m);
			}
		}
	
	}

	//手机注册从welcome到达首页sindex uv
	public function Sindex_uv($stationid)
	{	
		/*if (in_array($stationid,array(3,4,6,7))) {
			$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /index\/sindex/ ) print $8,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"phone=")) $1=substr($1,index($1,"phone=")+6,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
		}else{
			$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /index\/sindex/ ) print $7,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"phone=")) $1=substr($1,index($1,"phone=")+6,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
		}*/
		
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /index\/sindex/ ) print $7,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"phone=")) $1=substr($1,index($1,"phone=")+6,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';

		exec($sh,$sindex_uv);
		$result = array();
		
		foreach ($sindex_uv as $n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode(' ', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'sindex','action'=>'visit','detail'=>'uv','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$result[$tep['sys']] = $tep['dtime'];
		}		
		$this->ConArrDETAIL($result,'sindex_uv');
	}


	//通过其他渠道注册直达首页的用户sindex uv
	public function Other_Sindex_uv($stationid)
	{	
		/*if (in_array($stationid,array(3,4,6,7))) {
			$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /index\/sindex/ ) print $8,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"phone=")) $1=substr($1,index($1,"phone=")+6,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
		}else{
			$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /index\/sindex/ ) print $7,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"phone=")) $1=substr($1,index($1,"phone=")+6,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
		}*/
		
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /index\/sindex/ ) print $8,"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(index($1,"phone=")) $1=substr($1,index($1,"phone=")+6,11);else $1="-";print $1,"[|cut|]",$2}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\' | sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
		
		exec($sh,$sindex_uv);
		$result = array();
		
		foreach ($sindex_uv as $n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode(' ', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'sindex','action'=>'visit','detail'=>'other_uv','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$result[$tep['sys']] = $tep['dtime'];
		}		
		$this->ConArrDETAIL($result,'sindex_uv');
	}
	//四个页面pv
	public function Page_pv()
	{
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($5) ~ /get/ && tolower($6) ~ /station|game|movie|music|app/) print substr($6,0,index($6,"/")),"[|cut|]",$9}\' | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-"; print}\' | awk \'BEGIN{OFS="/"}{count[$1"/"$2]++}END{for(name in count)print name,count[name]}\' ';
		exec($sh,$page_pv);
		
		foreach ($page_pv as $n)
		{
			$tep = array();
			list($tep['model'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			if($tep['sys'] == '-'){
				continue   ;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'action'=>'visit','detail'=>'pv','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$data[$tep['model']][$tep['sys']] = $tep['dtime'];
		}
		if($data){
			$result = array();
			foreach ($data as $m=>$n)
			{
				$result['android'] = $n['android'];
				$result['ios'] = $n['ios'];
				$result['else'] = $n['else'];
				$this->ConArrDETAIL($result,$m.'_pv');
			}
		}
	}
	//our app 弹窗详情
	public function TrainAlert_pv()
	{
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($8) ~ /alertdown\/trainapp/) print $8}\' | awk -F "&" \'{print $1}\' | awk \'BEGIN{FS=OFS="/"}{count[$3"/"$4]++}END{for(name in count)print name,count[name]}\' ';
		exec($sh,$train_alert);
		
		foreach ($train_alert as $n)
		{
			$tep = array();
			list($tep['model'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'action'=>'trainAlert','detail'=>'pv','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$data[$tep['model']][$tep['sys']] = $tep['dtime'];
		}
		if($data){
			$result = array();
			foreach ($data as $m=>$n)
			{
				$result['android'] = $n['android'];
				$result['ios'] = $n['ios'];
				$result['else'] = $n['else'];
				$this->ConArrDETAIL($result,$m.'_alert');
			}
		}
			
	}
	//our app 下载uv详情
	public function TrainDown_uv()
	{
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($8) ~ /downapp\/trainapp/) print $8}\' | awk -F "&" \'{print $1}\' | awk \'BEGIN{FS=OFS="/"}{count[$3"/"$5]++}END{for(name in count)print name,count[name]}\' ';
		exec($sh,$train_down);
		
		foreach ($train_down as $n)
		{
			$tep = array();
			list($tep['model'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'action'=>'trainDown','detail'=>'uv','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$data[$tep['model']][$tep['sys']] = $tep['dtime'];
		}
		if($data){
		$result = array();
			foreach ($data as $m=>$n)
			{
				$result['android'] = $n['android'];
				$result['ios'] = $n['ios'];
				$result['else'] = $n['else'];
				$this->ConArrDETAIL($result,$m.'_down');
			}
		}	
	}
	//第三方 app 弹窗详情
	public function OtherAlert_pv()
	{
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($8) ~ /alertdown/ && tolower($8) ~ /sindex/ && tolower($8) !~ /trainapp/) print $8}\' | awk -F "&" \'{print $1}\' | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$4]++}END{for(name in count)print name,count[name]}\' ';
		exec($sh,$app_alert);
		
		foreach ($app_alert as $n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'sindex','action'=>'alert','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$data[$tep['detail']][$tep['sys']] = $tep['dtime'];
		}
		if($data){
			$result = array();
			foreach ($data as $m=>$n)
			{
				$result['android'] = $n['android'];
				$result['ios'] = $n['ios'];
				$result['else'] = $n['else'];
				$this->ConArrDETAIL($result,$m.'_alert');
			}
		}
	}
	//第三方 app 下载详情
	public function OtherDown_pv()
	{
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($8) ~ /downapp/ && tolower($8) ~ /sindex/ && tolower($8) !~ /trainapp/) print $8}\' | awk -F "&" \'{print $1}\' | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$4]++}END{for(name in count)print name,count[name]}\' ';
		exec($sh,$app_down);
		
		foreach ($app_down as $n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'sindex','action'=>'down','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$data[$tep['detail']][$tep['sys']] = $tep['dtime'];
		}
		if($data){
			$result = array();
			foreach ($data as $m=>$n)
			{
				$result['android'] = $n['android'];
				$result['ios'] = $n['ios'];
				$result['else'] = $n['else'];
				$this->ConArrDETAIL($result,$m.'_down');
			}
		}
	}
	//时长
	public function StayTime()
	{
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($8) ~ /time\/webindexindex|webindexregister|webindexwelcome|webindexsindex|webgameindex|webmovieindex|webmusicindex|webappindex/) print $8}\' | awk -F "&" \'{print $1}\' | cut -d "/" -f 2,3,4 | awk \'{if($1 ~ /\?/) $1=substr($1,0,index($1,"?")) substr($1,index($1,"/")); print  }\' | awk \'BEGIN{FS=OFS="/"}{sum[$1"/"$3]+=$2}END{for(name in sum)print name,sum[name]}\' ';
		exec($sh,$time);

		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($8) ~ /time\/webstation/) print $8}\' | awk -F "&" \'{print $1}\' | cut -d "/" -f 3,4 | awk \'BEGIN{FS=OFS="/"}{sum[$2]+=$1}END{for(name in sum)print name,sum[name]}\' ';
		exec($sh,$station);
		foreach ($station as $v)
		{
			$time[] = 'webstation/'.$v;
		}
		foreach ($time as $n)
		{
			$tep = array();
			list($tep['model'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'action'=>'stay','detail'=>'timecore','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$data[$tep['model']][$tep['sys']] = $tep['dtime'];
		}
		if($data){
			$result = array();
			foreach ($data as $m=>$n)
			{
				$result['android'] = $n['android'];
				$result['ios'] = $n['ios'];
				$result['else'] = $n['else'];
				$this->ConArrDETAIL($result,$m.'_time');
			}
		}
	}
	//app 弹窗详情
	public function App_alert()
	{
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($7) ~ /app/ && tolower($8) ~ /alertdown/ && tolower($8) !~ /trainapp/) print $8}\' | awk -F "&" \'{print $1}\' | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$4]++}END{for(name in count)print name,count[name]}\' ';
		exec($sh,$app_alert);
	
		foreach ($app_alert as $n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'app','action'=>'alert','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$data[$tep['detail']][$tep['sys']] = $tep['dtime'];
		}
	}
	//app 下载详情
	public function App_down()
	{
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($7) ~ /app/ && tolower($8) ~ /downapp/ && tolower($8) !~ /trainapp/) print $8}\' | awk -F "&" \'{print $1}\' | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$4]++}END{for(name in count)print name,count[name]}\' ';
		exec($sh,$app_down);
	
		foreach ($app_down as $n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'app','action'=>'down','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$data[$tep['detail']][$tep['sys']] = $tep['dtime'];
		}
	}
	//app banner详情
	public function App_banner()
	{
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($7) ~ /app/ && tolower($8) ~ /click\/banner/) print $8}\' | awk -F "&" \'{print $1}\' | awk \'BEGIN{FS=OFS="/"}{count[$3"/"$4]++}END{for(name in count)print name,count[name]}\' ';
		exec($sh,$app_banner);
	
		foreach ($app_banner as $n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'app','action'=>'click_banner','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$data[$tep['detail']][$tep['sys']] = $tep['dtime'];
		}
	}
	//game 弹窗详情
	public function Game_alert()
	{
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($7) ~ /game/ && tolower($8) ~ /alertdown/ && tolower($8) !~ /trainapp/) print $8}\'  | awk -F "&" \'{print $1}\' | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$4]++}END{for(name in count)print name,count[name]}\' ';
		exec($sh,$game_alert);
	
		foreach ($game_alert as $n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'game','action'=>'alert','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$data[$tep['detail']][$tep['sys']] = $tep['dtime'];
		}
	}
	//game 下载详情
	public function Game_down()
	{
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($7) ~ /game/ && tolower($8) ~ /downapp/ && tolower($8) !~ /trainapp/) print $8}\' | awk -F "&" \'{print $1}\' | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$4]++}END{for(name in count)print name,count[name]}\' ';
		exec($sh,$game_down);
	
		foreach ($game_down as $n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'game','action'=>'down','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$data[$tep['detail']][$tep['sys']] = $tep['dtime'];
		}
	}
	//game banner详情
	public function Game_banner()
	{
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($7) ~ /game/ && tolower($8) ~ /click\/banner/) print $8}\' | awk -F "&" \'{print $1}\' | awk \'BEGIN{FS=OFS="/"}{count[$3"/"$4]++}END{for(name in count)print name,count[name]}\' ';
		exec($sh,$game_banner);
	
		foreach ($game_banner as $n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			if($tep['sys'] == '-'){
				continue;
			}
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE1,'model'=>'game','action'=>'click_banner','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
			$data[$tep['detail']][$tep['sys']] = $tep['dtime'];
		}
	}
	/**
	 * 操作系统格式化
	 */
	public function sys_strtolower($sys)
	{
		$sys = str_replace('&','',strtolower($sys));
		if($sys == 'andriod'){
			$sys = 'android';
		}
		return $sys;
	}
	/**
	 * 前置流程写入数据库
	 */
	public function IntWebData()
	{
		$int = array(
			'date' => $this->DATE1,
			'stationid' => $this->STATIONID,
			'main' => json_encode($this->WEB_MAIN),
			'detail' => json_encode($this->WEB_DETAIL),
			'counttype' => 2
		);
		$this->Insert($int,'rha_webcount');
	}

	/**
	 * 组合数据 detail
	 */
	private function ConArrDetail(array $data,$name)
	{
		$data['name'] = $name;
		$this->WEB_DETAIL[] = $data;
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
	private function Insert($data,$tb='rha_count_record')
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
	
	private function getdb_content()
	{
		$db = new PDO($this->dsn_content, $this->dbuser_content, $this->dbpasswd_content);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->exec('set names utf8');
	
		return $db;
	}

	/**
	 * 根据appid获取app的类型和名称
	 * @param [type] $appid [description]
	 */
	private function GetAppNameType($appid){
		if (is_numeric($appid)) {
			$p = $this->db->query("Select `appcol`,`appname`,`sortid` From `rha_apps` where id = $appid");
			$p->setFetchMode(PDO::FETCH_ASSOC);
			$info = $p->fetchAll();
			$this->APPTYPE = $info[0]['appcol'];
			$this->APPNAME = $info[0]['appname'];
			$this->SORTID = $info[0]['sortid'];
		}elseif($appid == 'trainapp'){
			$this->APPTYPE = 2;
			$this->APPNAME = '火伴';
			$this->SORTID = 3;
		}else{
			$this->APPTYPE = 0;
			$this->APPNAME = '';
			$this->SORTID = 0;
		}
		
		/*$sql = "select id from rha_eventrecord where date = '$this->DATE' and stationid = $this->STATIONID and type = $this->APPTYPE";
		$pp = $this->db->query($sql);
		$pp->setFetchMode(PDO::FETCH_ASSOC);
		$info = $pp->fetchAll();
		if ($info[0]['id']) {
			$this->DESCRIPT = $info[0]['id'];
		}else{
			$this->DESCRIPT = '';
		}*/
			
	}

	/**
	 * app下载统计详情
	 * downapp/22/gamedetail/andriod/1
	 */
	public function DownAppInfo(){
		$sh = $this->CAT_FILE_WEB.' | awk -F "\[\|cut\|\]" \'{if(tolower($6)~ /record.php/ && tolower($8)~/downapp\//) print $8 }\' | awk -F "&" \'{print $1}\' | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$2"/"$3"/"$4"/"$5]++}END{for(name in count)print name,count[name]}\' ';
		exec($sh,$downapps);
	
		foreach ($downapps as $n)
		{
			$tep = array();
			list($tep['action'],$tep['appid'],$tep['downpage'],$tep['descript'],$tep['sys'],$tep['num']) = explode('/', $n);
			
			if (is_string($tep['appid']) && strtolower($tep['appid']) == 'trainapp') {
				$tep['appid'] = 12;
			}
			//获取app的类型和名称
			$this->GetAppNameType($tep['appid']);

			if($tep['sys'] == '-'){
				continue;
			}			
			$tep += array('appname'=>$this->APPNAME,'type'=>$this->APPTYPE,'date'=>$this->DATE1,'stationid'=>$this->STATIONID,'from'=>'pm','sortid'=>$this->SORTID);
			$this->Insert($tep,'rha_downapp');
			
		}
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
		//web
		$obj->Ad_uv($station['is_alone']);
		$obj->Ad_Detail($station['is_alone']);
		$obj->Reg_uv($station['is_alone']);
		$obj->Reg_verify($station['is_alone']);
		
		$obj->Loginlog($station['is_alone']);
		$obj->Bd_First_Reg_login($station['is_alone']);
		$obj->Bd_Not_First_Reg_login($station['is_alone']);

		$obj->Reg_login($station['is_alone']);
		$obj->Welcome_uv($station['is_alone'],$station['stationtype'],$station['id']);
		$obj->Welcome_Detail($station['is_alone']);
		$obj->Sindex_uv($station['id']);

		$obj->Other_Sindex_uv($station['id']);
		
		$obj->Page_pv();
		// $obj->TrainAlert_pv();
		$obj->TrainDown_uv();
		// $obj->OtherAlert_pv();
		// $obj->OtherDown_pv();
		$obj->StayTime();
		//应用/游戏页banner、弹窗、下载
		// $obj->App_alert();
		// $obj->App_down();
		$obj->App_banner();
		// $obj->Game_alert();
		// $obj->Game_down();
		$obj->Game_banner();

		//app下载详情
		$obj->DownAppInfo();
		
		$obj->IntWebData();
		
		$obj->InCheck();
	}
	
	
// }
