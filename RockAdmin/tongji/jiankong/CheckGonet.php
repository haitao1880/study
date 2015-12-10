<?php
/**
 * 每个一小时一键上网统计与分析
 */
require '/home/upload/nginxlog/gonet/Bassclass.php';
class CheckGonet extends Bassclass
{

	protected $mailto = "terry@rockhippo.cn";
	//protected $dsn_content = "mysql:host=192.168.28.201;dbname=rht_admin";
	protected $content;
	protected $ins;

	//日志所在目录
	protected $filedir;
	//压缩日志文件名
	protected $filename;
	protected $prevfilename;
	protected $gonetdir;
	protected $prevgonetdir;
	protected $date;
	protected $stationid;
	protected $hour;
	protected $prevhour;

	//解析后的三种数据
	protected $success;
	protected $fail;
	protected $uv;

	//警报值
	protected $warning_success = 0;
	protected $warning_fail = 0;
	protected $warning_uv = 0;

	protected $is_alarm=0;

	public function __construct($stationid)
	{			
		parent::__construct($stationid);
		$this->date = date('Y_m_d',strtotime("-1 hour"));
		$this->stationid = $stationid;
		$this->excutefunction();
	}

	protected function excutefunction()
	{
		$this->SetFileName();
		$this->SetDir();
		$this->Gonet();
		$this->GetAlarm();
		$this->warning();
		$this->gonetlog();
		$this->deletefile();
	}

	/**
	 * 设置文件名
	 */
	protected function SetFileName()
	{
		$this->hour = date('H',strtotime("-1 hour"));
		$this->filename = 'yjww_'.$this->hour.'.log';
		$this->prevhour = date('H',strtotime("-2 hour"));
		$this->prevfilename = 'yjww_'.$this->prevhour.'.log';
	}


	/**
	 * 设置文件路径
	 */
	protected function SetDir()
	{	
		$gonetdir = $this->loginfo[0]['gonetdir'];
		$this->gonetdir = '/home/upload/nginxlog/'.$gonetdir.'/gonet/'.$this->filename;
		$this->prevgonetdir = '/home/upload/nginxlog/'.$gonetdir.'/gonet/'.$this->prevfilename;
	}


	/**
	 * 一键上网数据解析
	 */
	protected function Gonet()
	{	
		if (!file_exists($this->gonetdir)) {
			$this->title = $this->date."日".$this->hour."时，一键上网日志未上传";
			$content = $this->date."日".$this->hour."时，".$this->loginfo[0]['stationname'].",一键上网日志未上传";
			$this->sendemail($content);
			return;
		}
		$sh_success = 'cat '.$this->gonetdir.' | grep -P "\d{11}] => 0|\d{11}] => 2" | wc -l';
		$sh_fail = 'cat '.$this->gonetdir.' | grep -P "\d{11}" | grep -Pv "\d{11}] => 0|\d{11}] => 2" | wc -l';
		$sh_uv = 'cat '.$this->gonetdir.' | grep -Po "\d{11}" | sort -u | wc -l';
		exec($sh_success,$success);
		exec($sh_fail,$fail);
		exec($sh_uv,$uv);
		$this->success = $success[0];
		$this->fail = $fail[0];
		$this->uv = $uv[0];
	}

	/**
	 * 获取报警值
	 */
	protected function GetAlarm()
	{
		$sql_warning_success = "SELECT `trigger` FROM rha_gonet_trigger where stationid = $this->stationid AND stime <= $this->hour AND etime >= $this->hour AND type = 2 AND is_delete = 0";

		$sql_warning_fail = "SELECT `trigger` FROM rha_gonet_trigger where stationid = $this->stationid AND stime <= $this->hour AND etime >= $this->hour AND type = 1 AND is_delete = 0";

		$sql_warning_uv = "SELECT `trigger` FROM rha_gonet_trigger where stationid = $this->stationid AND stime <= $this->hour AND etime >= $this->hour AND type = 3 AND is_delete = 0";

		$warning_success = $this->FetchRow($sql_warning_success)['trigger'];
		$warning_fail = $this->FetchRow($sql_warning_fail)['trigger'];
		$warning_uv = $this->FetchRow($sql_warning_uv)['trigger'];

		if ($warning_success) {
			$this->warning_success = $warning_success;
		}
		if ($warning_fail) {
			$this->warning_fail = $warning_fail;
		}
		if ($warning_uv) {
			$this->warning_uv = $warning_uv;
		}
	
	}

	/**
	 * 警报
	 */
	protected function warning()
	{
		if ($this->success && $this->warning_success && $this->success < $this->warning_success) 
		{
			$this->title = $this->date."日".$this->hour."时，一键上网故障";
			$this->content = $this->date."日".$this->hour."时，".$this->loginfo[0]['stationname'].",一键上网成功次数:".$this->success.",小于设定的触发值 ".$this->warning_success;
			$this->is_alarm = 1;

		}elseif($this->fail && $this->warning_fail && $this->fail > $this->warning_fail)
		{
			$this->title = $this->date."日".$this->hour."时，一键上网故障";
			$this->content = $this->date."日".$this->hour."时，".$this->loginfo[0]['stationname'].",一键上网失败次数:".$this->fail.",大于设定的触发值 ".$this->warning_fail;
			$this->is_alarm = 1;
		}elseif($this->uv && $this->warning_uv && $this->uv < $this->warning_uv)
		{
			$this->title = $this->date."日".$this->hour."时，一键上网故障";
			$this->content = $this->date."日".$this->hour."时，".$this->loginfo[0]['stationname'].",一键上网人数:".$this->uv.",小于设定的触发值 ".$this->warning_uv;
			$this->is_alarm = 1;
		}
		if ($this->title && $this->content) {
			$this->sendemail($this->content);
		}
		
	}

	/**
	 * 一键上网数据入库
	 * @return 
	 */
	protected function gonetlog()
	{
		$success_data = array('date'=>$this->date,'stationid'=>$this->stationid,'hour'=>$this->hour,'gonetnum'=>$this->success,'triggernum'=>$this->warning_success,'is_alarm'=>$this->is_alarm,'type'=>2);
		$this->Insert($success_data,'rha_gonet_alarm');

		$fail_data = array('date'=>$this->date,'stationid'=>$this->stationid,'hour'=>$this->hour,'gonetnum'=>$this->fail,'triggernum'=>$this->warning_fail,'is_alarm'=>$this->is_alarm,'type'=>1);
		$this->Insert($fail_data,'rha_gonet_alarm');

		$uv_data = array('date'=>$this->date,'stationid'=>$this->stationid,'hour'=>$this->hour,'gonetnum'=>$this->uv,'triggernum'=>$this->warning_uv,'is_alarm'=>$this->is_alarm,'type'=>3);		
		$this->Insert($uv_data,'rha_gonet_alarm');
	}

	protected function deletefile()
	{	
		unlink($this->prevgonetdir);
	}

}



error_reporting(E_ALL^E_NOTICE);
//接收stationid
$stationid = $argv[1];
if (!$stationid) {
	exit;
}
$obj = new CheckGonet($stationid);