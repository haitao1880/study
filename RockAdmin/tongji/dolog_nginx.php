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
		$this->DATE = date('Y_m_d',strtotime('-1 day'));
		// $this->DATE = '2015_08_01';
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
		$s = $this->db->query('Select `stationid` From `rha_scriptlog` Where `type` = 2 And `ctime` = CURRENT_DATE()');
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
			'type'		=>	2,
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
			$this->tp_index2 = 'usermac=.*&';
			$this->tp_cut = '9-26';
		}elseif($ap == 3){
			$this->tp_index2 = 'usermac=.*&';
			$this->tp_cut = '9-26';
		}
		
		
	
	}
	//日志解压
	function Log($is_moreweb)
	{	
		if (file_exists($this->WEB1.$this->WLNAME)) {
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
	//解析日志
	
	//入口页uv
	public function Ad_uv($is_alone)
	{	
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.'| grep " 200 " | grep -i "get /?" | grep -i "android" | grep -i -v "window" | grep -P -o "'.$this->tp_index2.'" | cut -c '.$this->tp_cut.' | sort | uniq | grep "&" -c';

			exec($sh,$ad_a);
			$sh = $this->CAT_FILE_WEB.'| grep " 200 " | grep -i "get /?" | grep -i "mac " | grep -i -v "window" | grep -P -o "'.$this->tp_index2.'" | cut -c '.$this->tp_cut.' | sort | uniq | grep "&" -c';
			exec($sh,$ad_i);
			$sh = $this->CAT_FILE_WEB.'| grep " 200 " | grep -i "get /?" | grep -i "window" | grep -P -o "'.$this->tp_index2.'" | cut -c '.$this->tp_cut.' | sort | uniq | grep "&" -c';
			exec($sh,$ad_e);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB.'| grep " 200 " | grep -i "get /?" | grep -i "android" | grep -i -v "window" | grep -P -o "'.$this->tp_index2.'" | cut -c '.$this->tp_cut.' | sort | uniq | grep "&" -c';

			exec($sh,$ad_a);
			$sh = $this->CAT_BEFORFILE_WEB.'| grep " 200 " | grep -i "get /?" | grep -i "mac " | grep -i -v "window" | grep -P -o "'.$this->tp_index2.'" | cut -c '.$this->tp_cut.' | sort | uniq | grep "&" -c';
			exec($sh,$ad_i);
			$sh = $this->CAT_BEFORFILE_WEB.'| grep " 200 " | grep -i "get /?" | grep -i "window" | grep -P -o "'.$this->tp_index2.'" | cut -c '.$this->tp_cut.' | sort | uniq | grep "&" -c';
			exec($sh,$ad_e);
		}
		

		$result = array('android'=>$ad_a[0],'ios'=>$ad_i[0],'else'=>$ad_e[0]);
		foreach ($result as $k=>$v)
		{
			$tep = array('date'=>$this->DATE,'model'=>'ad','action'=>'visit','detail'=>'uv','dtime'=>$v,'sys'=>$k,'stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
		}
		
		$this->ConArrDETAIL($result,'ad_uv');
	}
	//广告详情
	public function Ad_Detail($is_alone)
	{	
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "ad/show/" | cut -d " " -f 7 |cut -d "?" -f 2 | cut -d "/" -f 3,4 | cut -d "&" -f 1 | awk \'BEGIN{OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
		
			exec($sh,$ad_show);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "ad/show/" | cut -d " " -f 7 |cut -d "?" -f 2 | cut -d "/" -f 3,4 | cut -d "&" -f 1 | awk \'BEGIN{OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
			exec($sh,$ad_show);
		}
		
		
		foreach ($ad_show as $v)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $v);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE,'model'=>'ad','action'=>'show','stationid'=>$this->STATIONID,'from'=>2);
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
		$ref = 'm.wonaonao.com:81/?\|m.wonaonao.com/?';
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.'| grep " 200 " | grep -i "get /index/register" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | grep -P -o "'.$this->tp_index2.'" | cut -c '.$this->tp_cut.' | sort | uniq | grep "&" -c';
			exec($sh,$reg_a);
			$sh = $this->CAT_FILE_WEB.'| grep " 200 " | grep -i "get /index/register" | grep -i "'.$ref.'" | grep -i "mac " | grep -i -v "window" | grep -P -o "'.$this->tp_index2.'" | cut -c '.$this->tp_cut.' | sort | uniq | grep "&" -c';
			exec($sh,$reg_i);
			$sh = $this->CAT_FILE_WEB.'| grep " 200 " | grep -i "get /index/register" | grep -i "'.$ref.'" | grep -i "window" | grep -P -o "'.$this->tp_index2.'" | cut -c '.$this->tp_cut.' | sort | uniq | grep "&" -c';
			exec($sh,$reg_e);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB.'| grep " 200 " | grep -i "get /index/register" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | grep -P -o "'.$this->tp_index2.'" | cut -c '.$this->tp_cut.' | sort | uniq | grep "&" -c';
			exec($sh,$reg_a);
			$sh = $this->CAT_BEFORFILE_WEB.'| grep " 200 " | grep -i "get /index/register" | grep -i "'.$ref.'" | grep -i "mac " | grep -i -v "window" | grep -P -o "'.$this->tp_index2.'" | cut -c '.$this->tp_cut.' | sort | uniq | grep "&" -c';
			exec($sh,$reg_i);
			$sh = $this->CAT_BEFORFILE_WEB.'| grep " 200 " | grep -i "get /index/register" | grep -i "'.$ref.'" | grep -i "window" | grep -P -o "'.$this->tp_index2.'" | cut -c '.$this->tp_cut.' | sort | uniq | grep "&" -c';
			exec($sh,$reg_e);
		}
		
		
	
		$result = array('android'=>$reg_a[0],'ios'=>$reg_i[0],'else'=>$reg_e[0]);
		foreach ($result as $k=>$v)
		{
			$tep = array('date'=>$this->DATE,'model'=>'register','action'=>'visit','detail'=>'uv','dtime'=>$v,'sys'=>$k,'stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
		}
		
		$this->ConArrDETAIL($result,'reg_uv');
	}
	//验证码
	public function Reg_verify($is_alone)
	{
		$ref = 'm.wonaonao.com:81/index/register\|m.wonaonao.com/index/register';
		//发送次数
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "post /member/againgetphonecode" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
			exec($sh,$verify_a);
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "post /member/againgetphonecode" | grep -i "'.$ref.'" | grep -i "mac " | grep -i -v "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
			exec($sh,$verify_i);
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "post /member/againgetphonecode" | grep -i "'.$ref.'" | grep -i "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
			exec($sh,$verify_e);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "post /member/againgetphonecode" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
			exec($sh,$verify_a);
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "post /member/againgetphonecode" | grep -i "'.$ref.'" | grep -i "mac " | grep -i -v "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
			exec($sh,$verify_i);
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "post /member/againgetphonecode" | grep -i "'.$ref.'" | grep -i "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
			exec($sh,$verify_e);
		}
		
		
		$result = array('android'=>$verify_a[0],'ios'=>$verify_i[0],'else'=>$verify_e[0]);
		foreach ($result as $k=>$v)
		{
			$tep = array('date'=>$this->DATE,'model'=>'register','action'=>'verify','detail'=>'sum','dtime'=>$v,'sys'=>$k,'stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
		}
		
		$this->ConArrDETAIL($result,'verify_sum');
		//发送成功
		
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "post /member/againgetphonecode" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | cut -d " " -f 10 | grep "43" | cut -d "4" -f 1 | sort | uniq -c ';
			exec($sh,$success_a);
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "post /member/againgetphonecode" | grep -i "'.$ref.'" | grep -i "mac " | grep -i -v "window" | cut -d " " -f 10 | grep "43" | cut -d "4" -f 1 | sort | uniq -c ';
			exec($sh,$success_i);
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "post /member/againgetphonecode" | grep -i "'.$ref.'" | grep -i "window" | cut -d " " -f 10 | grep "43" | cut -d "4" -f 1 | sort | uniq -c ';
			exec($sh,$success_e);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "post /member/againgetphonecode" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | cut -d " " -f 10 | grep "43" | cut -d "4" -f 1 | sort | uniq -c ';
			exec($sh,$success_a);
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "post /member/againgetphonecode" | grep -i "'.$ref.'" | grep -i "mac " | grep -i -v "window" | cut -d " " -f 10 | grep "43" | cut -d "4" -f 1 | sort | uniq -c ';
			exec($sh,$success_i);
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "post /member/againgetphonecode" | grep -i "'.$ref.'" | grep -i "window" | cut -d " " -f 10 | grep "43" | cut -d "4" -f 1 | sort | uniq -c ';
			exec($sh,$success_e);
		}
		
		
		$result = array('android'=>$success_a[0],'ios'=>$success_i[0],'else'=>$success_e[0]);
		foreach ($result as $k=>$v)
		{
			$tep = array('date'=>$this->DATE,'model'=>'register','action'=>'verify','detail'=>'success','dtime'=>$v,'sys'=>$k,'stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
		}
		
		$this->ConArrDETAIL($result,'verify_success');
	}
	//登录
	public function Reg_login($is_alone)
	{
		$ref = 'm.wonaonao.com:81/index/register\|m.wonaonao.com/index/register';
		//登录次数
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
			exec($sh,$login_a);
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "mac " | grep -i -v "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
			exec($sh,$login_i);
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
			exec($sh,$login_e);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
			exec($sh,$login_a);
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "mac " | grep -i -v "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
			exec($sh,$login_i);
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
			exec($sh,$login_e);
		}
		
	
		$result = array('android'=>$login_a[0],'ios'=>$login_i[0],'else'=>$login_e[0]);
		foreach ($result as $k=>$v)
		{
			$tep = array('date'=>$this->DATE,'model'=>'register','action'=>'login','detail'=>'sum','dtime'=>$v,'sys'=>$k,'stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
		}
		
		$this->ConArrDETAIL($result,'login_sum');
		//登录成功
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.' | grep " 200 65" | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | cut -d " " -f 10 | cut -d "6" -f 1 | sort | uniq -c ';
			exec($sh,$success_a);
			$sh = $this->CAT_FILE_WEB.' | grep " 200 65" | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "mac " | grep -i -v "window" | cut -d " " -f 10  | cut -d "6" -f 1 | sort | uniq -c ';
			exec($sh,$success_i);
			$sh = $this->CAT_FILE_WEB.' | grep " 200 65" | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "window" | cut -d " " -f 10 | cut -d "6" -f 1 | sort | uniq -c ';
			exec($sh,$success_e);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 65" | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | cut -d " " -f 10 | cut -d "6" -f 1 | sort | uniq -c ';
			exec($sh,$success_a);
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 65" | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "mac " | grep -i -v "window" | cut -d " " -f 10  | cut -d "6" -f 1 | sort | uniq -c ';
			exec($sh,$success_i);
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 65" | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "window" | cut -d " " -f 10 | cut -d "6" -f 1 | sort | uniq -c ';
			exec($sh,$success_e);
		}
		
	
		$result = array('android'=>$success_a[0],'ios'=>$success_i[0],'else'=>$success_e[0]);
		foreach ($result as $k=>$v)
		{
			$tep = array('date'=>$this->DATE,'model'=>'register','action'=>'login','detail'=>'success','dtime'=>$v,'sys'=>$k,'stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
		}
		
		$this->ConArrDETAIL($result,'login_success');
		//验证码错误
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | cut -d " " -f 10 | grep "122" | cut -d "1" -f 1 | sort | uniq -c ';
			exec($sh,$error_a);
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "mac " | grep -i -v "window" | cut -d " " -f 10 | grep "122" | cut -d "1" -f 1 | sort | uniq -c ';
			exec($sh,$error_i);
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "window" | cut -d " " -f 10 | grep "122" | cut -d "1" -f 1 | sort | uniq -c ';
			exec($sh,$error_e);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | cut -d " " -f 10 | grep "122" | cut -d "1" -f 1 | sort | uniq -c ';
			exec($sh,$error_a);
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "mac " | grep -i -v "window" | cut -d " " -f 10 | grep "122" | cut -d "1" -f 1 | sort | uniq -c ';
			exec($sh,$error_i);
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "window" | cut -d " " -f 10 | grep "122" | cut -d "1" -f 1 | sort | uniq -c ';
			exec($sh,$error_e);
		}
		
		
		$result = array('android'=>$error_a[0],'ios'=>$error_i[0],'else'=>$error_e[0]);
		foreach ($result as $k=>$v)
		{
			$tep = array('date'=>$this->DATE,'model'=>'register','action'=>'login','detail'=>'error','dtime'=>$v,'sys'=>$k,'stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
		}
		
		$this->ConArrDETAIL($result,'login_error');
		//已登录
		
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | cut -d " " -f 10 | grep "97" | cut -d "9" -f 1 | sort | uniq -c ';
			exec($sh,$db_a);
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "mac " | grep -i -v "window" | cut -d " " -f 10 | grep "97" | cut -d "9" -f 1 | sort | uniq -c ';
			exec($sh,$db_i);
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "window" | cut -d " " -f 10 | grep "97" | cut -d "9" -f 1 | sort | uniq -c ';
			exec($sh,$db_e);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | cut -d " " -f 10 | grep "97" | cut -d "9" -f 1 | sort | uniq -c ';
			exec($sh,$db_a);
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "mac " | grep -i -v "window" | cut -d " " -f 10 | grep "97" | cut -d "9" -f 1 | sort | uniq -c ';
			exec($sh,$db_i);
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "post /member/register" | grep -i "'.$ref.'" | grep -i "window" | cut -d " " -f 10 | grep "97" | cut -d "9" -f 1 | sort | uniq -c ';
			exec($sh,$db_e);
		}
		
		
		$result = array('android'=>$db_a[0],'ios'=>$db_i[0],'else'=>$db_e[0]);
		foreach ($result as $k=>$v)
		{
			$tep = array('date'=>$this->DATE,'model'=>'register','action'=>'login','detail'=>'db','dtime'=>$v,'sys'=>$k,'stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
		}
		
		$this->ConArrDETAIL($result,'login_db');
	}
	//欢迎页uv
	public function Welcome_uv($is_alone)
	{
		$ref = 'm.wonaonao.com:81/index/register\|m.wonaonao.com/index/register';

		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.'| grep " 200 " | grep -i "get /index/welcome" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | grep -P -o "welcome\?phone=[0-9]{11}" | cut -c 15-26 | sort | uniq | wc -l';
			exec($sh,$wel_a);
			$sh = $this->CAT_FILE_WEB.'| grep " 200 " | grep -i "get /index/welcome" | grep -i "'.$ref.'" | grep -i "mac " | grep -i -v "window" | grep -P -o "welcome\?phone=[0-9]{11}" | cut -c 15-26 | sort | uniq | wc -l';
			exec($sh,$wel_i);
			$sh = $this->CAT_FILE_WEB.'| grep " 200 " | grep -i "get /index/welcome" | grep -i "'.$ref.'" | grep -i "window" | grep -P -o "welcome\?phone=[0-9]{11}" | cut -c 15-26 | sort | uniq | wc -l';
			exec($sh,$wel_e);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB.'| grep " 200 " | grep -i "get /index/welcome" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | grep -P -o "welcome\?phone=[0-9]{11}" | cut -c 15-26 | sort | uniq | wc -l';
			exec($sh,$wel_a);
			$sh = $this->CAT_BEFORFILE_WEB.'| grep " 200 " | grep -i "get /index/welcome" | grep -i "'.$ref.'" | grep -i "mac " | grep -i -v "window" | grep -P -o "welcome\?phone=[0-9]{11}" | cut -c 15-26 | sort | uniq | wc -l';
			exec($sh,$wel_i);
			$sh = $this->CAT_BEFORFILE_WEB.'| grep " 200 " | grep -i "get /index/welcome" | grep -i "'.$ref.'" | grep -i "window" | grep -P -o "welcome\?phone=[0-9]{11}" | cut -c 15-26 | sort | uniq | wc -l';
			exec($sh,$wel_e);
		}
		
		
	
		$result = array('android'=>$wel_a[0],'ios'=>$wel_i[0],'else'=>$wel_e[0]);
		foreach ($result as $k=>$v)
		{
			$tep = array('date'=>$this->DATE,'model'=>'welcome','action'=>'visit','detail'=>'uv','dtime'=>$v,'sys'=>$k,'stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
		}
		
		$this->ConArrDETAIL($result,'welcome_uv');
	}
	//欢迎页 广告详情
	public function Welcome_Detail($is_alone)
	{	
		if ($is_alone) {
			$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "welcome/show/" | cut -d " " -f 7 |cut -d "?" -f 2 | cut -d "/" -f 3,4 | cut -d "&" -f 1 | awk \'BEGIN{OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
			exec($sh,$wel_show);
		}else{
			$sh = $this->CAT_BEFORFILE_WEB.' | grep " 200 " | grep -i "welcome/show/" | cut -d " " -f 7 |cut -d "?" -f 2 | cut -d "/" -f 3,4 | cut -d "&" -f 1 | awk \'BEGIN{OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
			exec($sh,$wel_show);
		}
		
	
		foreach ($wel_show as $v)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $v);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE,'model'=>'welcome','action'=>'show','stationid'=>$this->STATIONID,'from'=>2);
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
	//sindex uv
	public function Sindex_uv()
	{
		$ref = 'm.wonaonao.com:81/index/welcome\|m.wonaonao.com/index/welcome';
		
		$sh = $this->CAT_FILE_WEB.'| grep " 200 " | grep -i "get /index/sindex" | grep -i "'.$ref.'" | grep -i "android" | grep -i -v "window" | grep -P -o "welcome\?phone=[0-9].{11}" | cut -c 15-25 | sort | uniq | wc -l';
		exec($sh,$sindex_a);
		$sh = $this->CAT_FILE_WEB.'| grep " 200 " | grep -i "get /index/sindex" | grep -i "'.$ref.'" | grep -i "mac "  | grep -i -v "window" | grep -P -o "welcome\?phone=[0-9].{11}" | cut -c 15-25 | sort | uniq | wc -l';
		exec($sh,$sindex_i);
		$sh = $this->CAT_FILE_WEB.'| grep " 200 " | grep -i "get /index/sindex" | grep -i "'.$ref.'" | grep -i "window" | grep -P -o "welcome\?phone=[0-9].{11}" | cut -c 15-25 | sort | uniq | wc -l';
		exec($sh,$sindex_e);
	
		$result = array('android'=>$sindex_a[0],'ios'=>$sindex_i[0],'else'=>$sindex_e[0]);
		foreach ($result as $k=>$v)
		{
			$tep = array('date'=>$this->DATE,'model'=>'sindex','action'=>'visit','detail'=>'uv','dtime'=>$v,'sys'=>$k,'stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
		}
		
		$this->ConArrDETAIL($result,'sindex_uv');
	}
	//四个页面pv
	public function Page_pv()
	{
		//station
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "get /station/index " | grep -i "android" | grep -i -v "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
		exec($sh,$station_a);
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "get /station/index " | grep -i "mac " | grep -i -v "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
		exec($sh,$station_i);
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "get /station/index " | grep -i "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
		exec($sh,$station_e);
		$result = array('android'=>$station_a[0],'ios'=>$station_i[0],'else'=>$station_e[0]);
		foreach ($result as $k=>$v)
		{
			$tep = array('date'=>$this->DATE,'model'=>'station','action'=>'visit','detail'=>'pv','dtime'=>$v,'sys'=>$k,'stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
		}
		
		$this->ConArrDETAIL($result,'station_pv');
		//game
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "get /game/index " | grep -i "android" | grep -i -v "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
		exec($sh,$game_a);
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "get /game/index " | grep -i "mac " | grep -i -v "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
		exec($sh,$game_i);
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "get /game/index " | grep -i "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
		exec($sh,$game_e);
		$result = array('android'=>$game_a[0],'ios'=>$game_i[0],'else'=>$game_e[0]);
		foreach ($result as $k=>$v)
		{
			$tep = array('date'=>$this->DATE,'model'=>'game','action'=>'visit','detail'=>'pv','dtime'=>$v,'sys'=>$k,'stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
		}
		
		$this->ConArrDETAIL($result,'game_pv');
		//movie
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "get /movie/index " | grep -i "android" | grep -i -v "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
		exec($sh,$movie_a);
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "get /movie/index " | grep -i "mac " | grep -i -v "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
		exec($sh,$movie_i);
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "get /movie/index " | grep -i "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
		exec($sh,$movie_e);
		$result = array('android'=>$movie_a[0],'ios'=>$movie_i[0],'else'=>$movie_e[0]);
		foreach ($result as $k=>$v)
		{
			$tep = array('date'=>$this->DATE,'model'=>'movie','action'=>'visit','detail'=>'pv','dtime'=>$v,'sys'=>$k,'stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
		}
		
		$this->ConArrDETAIL($result,'movie_pv');
		//music
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "get /music/index " | grep -i "android" | grep -i -v "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
		exec($sh,$music_a);
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "get /music/index " | grep -i "mac " | grep -i -v "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
		exec($sh,$music_i);
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "get /music/index " | grep -i "window" | cut -d " " -f 7 | cut -d "/" -f 1 | sort | uniq -c ';
		exec($sh,$music_e);
		$result = array('android'=>$music_a[0],'ios'=>$music_i[0],'else'=>$music_e[0]);
		foreach ($result as $k=>$v)
		{
			$tep = array('date'=>$this->DATE,'model'=>'music','action'=>'visit','detail'=>'pv','dtime'=>$v,'sys'=>$k,'stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
		}

		$this->ConArrDETAIL($result,'music_pv');
	}
	//our app 弹窗详情
	public function TrainAlert_pv()
	{
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "alertdown/trainapp/" | cut -d " " -f 7 |cut -d "?" -f 2 | cut -d "/" -f 3,4 | cut -d "&" -f 1 | awk \'BEGIN{OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$train_alert);
		
		foreach ($train_alert as $v)
		{
			$tep = array();
			list($tep['model'],$tep['sys'],$tep['dtime']) = explode('/', $v);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE,'action'=>'trainAlert','detail'=>'pv','stationid'=>$this->STATIONID,'from'=>2);
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
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "downapp/trainapp/" | cut -d " " -f 7 |cut -d "?" -f 2 | cut -d "/" -f 3,5 | cut -d "&" -f 1 | awk \'BEGIN{OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$train_down);
		
		foreach ($train_down as $v)
		{
			$tep = array();
			list($tep['model'],$tep['sys'],$tep['dtime']) = explode('/', $v);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE,'action'=>'trainDown','detail'=>'uv','stationid'=>$this->STATIONID,'from'=>2);
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
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "alertdown/" | grep -i "/sindex" | cut -d " " -f 7 |cut -d "?" -f 2 | cut -d "/" -f 2,4 | cut -d "&" -f 1 | awk \'BEGIN{OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';

		exec($sh,$app_alert);
		
		foreach ($app_alert as $v)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $v);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE,'model'=>'sindex','action'=>'alert','stationid'=>$this->STATIONID,'from'=>2);
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
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep -i "downapp/" | grep -i "/sindex" | cut -d " " -f 7 |cut -d "?" -f 2 | cut -d "/" -f 2,4 | cut -d "&" -f 1 | awk \'BEGIN{OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$app_down);
		
		foreach ($app_down as $v)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $v);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE,'model'=>'sindex','action'=>'down','stationid'=>$this->STATIONID,'from'=>2);
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
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep "record.php?time" |cut -d " " -f 7 | grep -i "time/webindexindex\|time/webindexregister\|time/webindexwelcome\|time/webindexsindex\|time/webgameindex\|time/webmovieindex\|time/webmusicindex\|time/webappindex" | cut -d "/" -f 3,4,5 | cut -d "&" -f 1 | awk \'{if($1 ~ /\?/) $1=substr($1,0,index($1,"?")) substr($1,index($1,"/")); print  }\' | awk \'BEGIN{FS=OFS="/"}{sum[$1"/"$3]+=$2}END{for(name in sum)print name,sum[name]}\' ';

		exec($sh,$time);
		$sh = $this->CAT_FILE_WEB.' | grep " 200 " | grep "record.php?time" |cut -d " " -f 7 | grep -i "time/webstation" | cut -d "/" -f 4,5 | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{sum[$2]+=$1}END{for(name in sum)print name,sum[name]}\' ';

		exec($sh,$station);
		foreach ($station as $v)
		{
			$time[] = 'webstation/'.$v;
		}
		foreach ($time as $v)
		{
			$tep = array();
			list($tep['model'],$tep['sys'],$tep['dtime']) = explode('/', $v);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE,'action'=>'stay','detail'=>'timecore','stationid'=>$this->STATIONID,'from'=>2);
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
	/**
	 * Music&Movie的补充
	 * @param int $stationid 车站id
	 */
	public function Add()
	{
		$sh = $this->CAT_FILE_APP.'| grep " 200 " | grep -i "record.php?movie/columns\|record.php?movie/playall\|record.php?movie/view\|record.php?movie/buffer\|record.php?music/like" | cut -d " " -f 7 | cut -d "/" -f 2,3,4,5 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$2"/"$3"/"$4]++}END{for(name in count)print name,count[name]}\'';

		exec($sh,$arr);
	
		//$arr  array(0=>'movie/view/515/Android/1',0=>'movie/buffer/370/R831S/4')
		foreach ($arr as $m=>$n)
		{
			$tep = array();
			list($tep['model'],$tep['detail'],$tep['did'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('from'=>1,'stationid'=>$this->STATIONID,'date'=>$this->DATE,'action'=>'click');
			$this->Insert($tep);
		}
		
	}
	/**
	 * Music_autoplay
	 * @param int $stationid 车站id
	 */
	public function AutoPlay()
	{
		$sh = $this->CAT_FILE_APP.' | grep -i "record.php?music/play" | cut -d " " -f 7 | cut -d "/" -f 2,3,4,5 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$2"/"$3"/"$4]++}END{for(name in count)print name,count[name]}\'';

		exec($sh,$arr);
		//$arr  array(0=>'music/play/25772629/ios/1',0=>'music/play/26829325/Android/1')
		foreach ($arr as $m=>$n)
		{
			$tep = array();
			list($tep['model'],$tep['action'],$tep['did'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('from'=>1,'stationid'=>$this->STATIONID,'date'=>$this->DATE);
			$this->Insert($tep);
		}
	
	}
	/**
	 * 下载提示
	 * @param int $stationid 车站id
	 */
	public function Alert()
	{
		$sh = $this->CAT_FILE_WEB.'| grep " 200 " | grep -i "get /record.php?" | grep "/alert" | cut -d " " -f 7 | cut -d "/" -f 2,3,4 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$2"/"$3]++}END{for(name in count)print name,count[name]}\'';

		exec($sh,$arr);
		//$arr  array(0=>'movie/alert/ios/1000',1=>'music/alert/android/2000'...)
		foreach ($arr as $m=>$n)
		{
			$tep = array();
			list($tep['model'],$tep['action'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('from'=>2,'stationid'=>$this->STATIONID,'date'=>$this->DATE);
			$this->Insert($tep);
		}
	
	}
	/**
	 * 下载数量
	 * @param int $stationid 车站id
	 */
	public function Down()
	{
		$sh = $this->CAT_FILE_WEB.'| grep " 200 " | grep -i "get /record.php?downapp" | cut -d " " -f 7 | cut -d "/" -f 2,3,4 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$2"/"$3]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr);
		//$arr  array(0=>'downapp/welcome/ios/1000',1=>'downapp/movie/android/2000'...)
		foreach ($arr as $m=>$n)
		{
			$tep = array();
			list($tep['action'],$tep['model'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('from'=>2,'stationid'=>$this->STATIONID,'date'=>$this->DATE);
			$this->Insert($tep);
		}
		
	}
	/**
	 * 首次打开
	 * @param int $stationid 车站id
	 */
	public function Open()
	{
		$sh =  $this->CAT_FILE_APP."| grep '200' | grep -i 'record.php?open' |cut -d ' ' -f 7| cut -d '/' -f 2,3,4 | cut -d '?' -f 2 | awk 'BEGIN{FS=OFS=\"/\"}{count[$1\"/\"$2\"/\"$3]++}END{for(name in count)print name,count[name]}'";
		exec($sh,$arr);
		
		foreach ($arr as $m=>$n)
		{
			$tep = array();
			list($tep['detail'],$tep['action'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			if (!$tep['action'] || !in_array($tep['action'], array('first','pv','uv'))) continue;
			$tep += array('from'=>1,'stationid'=>$this->STATIONID,'date'=>$this->DATE,'model'=>'open');
			$this->Insert($tep);
		}
		
	}
	/**
	 * 八个导航点击
	 * @param int $stationid 车站id
	 */
	public function Nav()
	{
		//pv
		$sh = $this->CAT_FILE_APP.'| grep " 200 " | grep -i "index/click" | grep -v "uv" | cut -d " " -f 7 | cut -d "/" -f 4,5 | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$2]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr);
		//$arr	array(0=>'inquiries/Android/101',1=>'movie/Android/158',...)
		foreach ($arr as $m=>$n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('from'=>1,'stationid'=>$this->STATIONID,'date'=>$this->DATE,'model'=>'nav','action'=>'pv_click');
			$this->Insert($tep);
		}
		//uv
		$sh_uv = $this->CAT_FILE_APP.'| grep " 200 " | grep -i "index/click/uv" | cut -d " " -f 7 | cut -d "/" -f 5,6 | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$2]++}END{for(name in count)print name,count[name]}\'';
		exec($sh_uv,$arruv);
		//$arr	array(0=>'movie/ios/14',1=>'app/Android/39',...)
		foreach ($arruv as $mv=>$n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('from'=>1,'stationid'=>$this->STATIONID,'date'=>$this->DATE,'model'=>'nav','action'=>'uv_click');
			$this->Insert($tep);
		}
	}
	/**
	 * banner点击
	 * @param int $stationid 车站id
	 */
	public function Banner()
	{
		//pv	
		$sh = $this->CAT_FILE_APP.'| grep " 200 " | grep -i "banner/click" | grep -v "uv" | cut -d " " -f 7| cut -d "/" -f 2,3,5,6 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$2"/"$3"/"$4]++}END{for(name in count)print name,count[name]}\'';

		exec($sh,$arr);
		//$arr	array(0=>'movie/banner/4/Android/1',game/banner/31/Android/3,...)
		foreach ($arr as $m=>$n)
		{
			$tep = array();
			list($tep['detail'],$tep['model'],$tep['did'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('from'=>1,'stationid'=>$this->STATIONID,'date'=>$this->DATE,'action'=>'pv_click');
			$this->Insert($tep);
		}

	}
	/**
	 * Movie&Music点击
	 * @param int $stationid 车站id
	 */
	public function MClick()
	{	
		$sh = $this->CAT_FILE_APP.'| grep " 200 " | grep -i "record.php?movie/click\|record.php?music/click" | cut -d " " -f 7 | cut -d "/" -f 2,3,4,5,6 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$2"/"$3"/"$4"/"$5]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr);
		//$arr  array(0=>'music/click/pause/26754183/Android/4',1=>'movie/click/playbar/453/ios/1'...)
		foreach ($arr as $m=>$n)
		{
			$tep = array();
			list($tep['model'],$tep['action'],$tep['detail'],$tep['did'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('from'=>1,'stationid'=>$this->STATIONID,'date'=>$this->DATE);
			$this->Insert($tep);
		}
	}
	/**
	 * App&Game点击
	 * @param int $stationid 车站id
	 */
	public function App()
	{
		//pv	
		$sh = $this->CAT_FILE_APP.'| grep " 200 " | grep -i "record.php?game\|record.php?app" | grep -v "uv"| cut -d " " -f 7  | cut -d "/" -f 2,3,4,5 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$2"/"$3"/"$4]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr);
		//$arr  array(0=>'app/downfinish/36/Android/1',1=>'game/down/49/Android/2'...)
		foreach ($arr as $m=>$n)
		{
			$tep = array();
			list($tep['model'],$tep['action'],$tep['did'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('from'=>1,'stationid'=>$this->STATIONID,'date'=>$this->DATE,'detail'=>'pv');
			$this->Insert($tep);
		}
		//uv		
		$sh = $this->CAT_FILE_APP.'| grep " 200 " | grep -i "record.php?game/click/uv\|record.php?game/down/uv\|record.php?app/click/uv\|record.php?app/down/uv" | cut -d " " -f 7  | cut -d "/" -f 2,3,5,6 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1"/"$2"/"$3"/"$4]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr);
		//$arr  array(0=>'app/click/38/Android/38',1=>'game/down/13/Android/6'...)
		foreach ($arr as $m=>$n)
		{
			$tep = array();
			list($tep['model'],$tep['action'],$tep['did'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('from'=>1,'stationid'=>$this->STATIONID,'date'=>$this->DATE,'detail'=>'uv');
			$this->Insert($tep);
		}
	}
	/**
	 * 时长统计
	 * @param int $stationid 站点ID
	 */
	public function ConTime()
	{
		$sh = $this->CAT_FILE_APP.'| grep " 200 " | grep "record.php?time" | grep -v "time/movie/play"  | cut -d " " -f 7 | cut -d "/" -f 3,4,5 | awk \'BEGIN{FS=OFS="/"}{sum[$1"/"$3]+=$2}END{for(name in sum)print name,sum[name]}\' ';
		exec($sh,$arr);
		//$sh_web = $this->CAT_FILE_WEB.'| grep " 200 " | grep "record.php?time" | cut -d " " -f 7 | cut -d "/" -f 3,4,5 | awk \'BEGIN{FS=OFS="/"}{sum[$1"/"$3]+=$2}END{for(name in sum)print name,sum[name]}\' ';
		//exec($sh_web,$arr_web);
		
		//$arr  array(0=>'link/ios/2',1=>'movie/Android/4516'...)
		
		$insrt = function($data,$from)
		{
			foreach ($data as $m=>$n)
			{
				$tep = array();
				list($tep['model'],$tep['sys'],$tep['dtime']) = explode('/', $n);
				$tep['sys'] = $this->sys_strtolower($tep['sys']);
				$tep += array('detail'=>'timerecord','from'=>$from,'stationid'=>$this->STATIONID,'date'=>$this->DATE);
				$this->Insert($tep);
			}
		};
		
		$insrt($arr,1);
		//$insrt($arr_web,2);
		
	}
	/**
	 * 车站服务
	 * @param int $stationid 站点ID
	 */
	public function StationSer()
	{
		$sh = $this->CAT_FILE_APP.' | grep " 200 " | grep -i "get /station/" | cut -d " " -f 7 | cut -d "/" -f 3 | cut -d "?" -f 1| awk \'BEGIN{OFS="/"}{count[$0]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr);
		foreach ($arr as $m=>$n)
		{
			$tep = array();
			list($tep['action'],$tep['dtime']) = explode('/', $n);
			$tep += array('model'=>'station','detail'=>'pv','date'=>$this->DATE,'stationid'=>$this->STATIONID,'from'=>1);
			$this->Insert($tep);
		}
	}


	/**
	 * 统计首页游戏广告点击次数
	 */
	public function sindex_ad_click(){
		$sh = $this->CAT_FILE_WEB.' | awk -F " " \'{if(tolower($7)~/sindex\/click/) print $7}\' | cut -d "&" -f 1 | awk \'BEGIN{FS=OFS="/"}{count[$4"/"$5"/"$6]++}END{for(name in count) print name,count[name]}\'';
		
		exec($sh,$app_down);

		foreach ($app_down as $v)
		{
			$tep = array();
			list($tep['action'],$tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $v);
			$tep['action'] = 'game_click';
			$tep['sys'] = $this->sys_strtolower($tep['sys']);
			$tep += array('date'=>$this->DATE,'model'=>'sindex','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
		}
		
	}

	/**
	 * 统计点击首页游戏广告进入游戏详情的次数
	 */
	public function sindex_game_detail(){
		$sh = $this->CAT_FILE_WEB.' | awk -F " " \'{if(tolower($7)~/game\/detail/ && tolower($7)~/from=sindex/) print $7}\' | awk -F "?" \'{$2=substr($2,4);print $1,"/",$2}\' | awk -F "&" \'{$3=substr($3,5);print $1,"/",$3}\' | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$3"/"$4"/"$5]++}END{for(name in count) print name,count[name]}\' | sed -e "s/ //g"';
		exec($sh,$sindex_game_detail);

		foreach ($sindex_game_detail as $v)
		{
			
			$datas = explode('/', $v);
			$tep['action'] = $datas[0].'_'.$datas[1];
			$tep['detail'] = $datas[2];
			$tep['sys'] = $this->sys_strtolower($datas[3]);
			$tep['dtime'] = $datas[4];
			
			$tep += array('date'=>$this->DATE,'model'=>'sindex','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
		}
	}


	/**
	 * 统计首页广告过来的点击游戏下载的次数
	 */
	public function sindex_game_down(){
		$sh = $this->CAT_FILE_WEB.'| awk -F " " \'{if(tolower($7)~/downapp/ && tolower($7)~/sindex/ && tolower($7)~/game/) print $7}\' | awk -F "?" \'{print $2}\' | awk \'BEGIN{FS=OFS="/"}{count[$3"/"$1"/"$2"/"$4]++}END{for(name in count) print name,count[name]}\'';
		exec($sh,$sindex_game_detail);

		foreach ($sindex_game_detail as $v)
		{
			
			$datas = explode('/', $v);
			$tep['action'] = 'game_down';
			$tep['detail'] = $datas[2];
			$tep['sys'] = $this->sys_strtolower($datas[3]);
			$tep['dtime'] = $datas[4];
			
			$tep += array('date'=>$this->DATE,'model'=>'sindex','stationid'=>$this->STATIONID,'from'=>2);
			$this->Insert($tep);
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
			'date' => $this->DATE,
			'stationid' => $this->STATIONID,
			'main' => json_encode($this->WEB_MAIN),
			'detail' => json_encode($this->WEB_DETAIL),
			'counttype' => 1,
		);
		$this->Insert($int,'rha_webcount');
	}

	/**
	 * 组合数据 detail
	 */
	protected function ConArrDetail(array $data,$name)
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
		//$obj->Log($station['is_moreweb']);
		$obj->setStationId($station['id']);
		
		$obj->clear();
		//web
		$obj->Ad_uv($station['is_alone']);
		$obj->Ad_Detail($station['is_alone']);
		$obj->Reg_uv($station['is_alone']);
		$obj->Reg_verify($station['is_alone']);
		$obj->Reg_login($station['is_alone']);
		$obj->Welcome_uv($station['is_alone']);
		$obj->Welcome_Detail($station['is_alone']);
		$obj->Sindex_uv();
		$obj->Page_pv();
		$obj->TrainAlert_pv();
		$obj->TrainDown_uv();
		$obj->OtherAlert_pv();
		$obj->OtherDown_pv();

		//首页游戏广告流程统计
		$obj->sindex_ad_click();
		$obj->sindex_game_detail();
		$obj->sindex_game_down();

		$obj->StayTime();

		$obj->IntWebData();

		//app
		$obj->Open();
		$obj->Nav();
		$obj->Banner();
		$obj->MClick();
		$obj->Add();
		$obj->AutoPlay();
		$obj->App();
		$obj->ConTime();
		$obj->StationSer();
		
	 	$obj->InCheck();
	 	
	 	/*$filelog = array_unique($filelogs);
		foreach($filelog as $v){
			//删除之前的目录
		 	$obj->deldir($obj->LOG_BASE_PATH.$v);
		}*/
 	}
 	
	
	
// }

