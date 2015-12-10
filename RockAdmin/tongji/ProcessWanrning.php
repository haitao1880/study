<?php 

header("Content-type: text/html; charset=utf-8");
/**
 * 数据流程预警
 */
class ProcessWanrning
{
	//触发值
	protected $trigger;
	protected $curprocessdata;

	protected $stationname;
	protected $stationid;
	
	protected $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	protected $dbuser_content = 'root';
	protected $dbpasswd_content = 'Cahw_1MLLqIt';
	protected $db ;
	
	//发送邮件配置
	protected $mailhost = 'mail.rockhippo.cn'; 
	protected $mailuser = "logs@rockhippo.cn";
	protected $mailpassword = "rockhippo";
	 // protected $mailto = "chris@rockhippo.cn";  //发送给运维组
 	protected $mailto = "terry@rockhippo.cn";

	protected $Content;
	
	
	public function __construct($stationid)
	{
		$this->db = $this->getdb_content();
		$p = $this->db->query("Select * From rha_process_trigger where stationid = $stationid");
		$p->setFetchMode(PDO::FETCH_ASSOC);
		$trigger = $p->fetchAll();
		$this->trigger = $trigger[0];

		$this->stationid = $stationid;
		$this->date = date('Y_m_d',strtotime("-1 day"));
		$this->execall();		

	}

	protected function execall()
	{
		$this->GetStationName();
		$this->GetCurProcessData();
		// $this->CmpTrigger();
		$this->CmpProcessData();
		$this->loseWarning();
	}

	//根据stationID获取车站名
	public function GetStationName()
	{
		$p = $this->db->query("Select `stationname` From `rha_station` where id = $this->stationid");
		$p->setFetchMode(PDO::FETCH_ASSOC);
		$stationname = $p->fetchAll();
		$this->stationname = $stationname[0]['stationname'];
	}

	//获取当前时间每个车站的流程数据
	public function GetCurProcessData()
	{	
		//wifi连接
		$date = str_replace('_','-',$this->date);
		$sql_wifi = "SELECT num AS wificonnect FROM rha_aclogview WHERE date = ? and station = ?";
		$wificonnect = $this->FetRow($sql_wifi,array($date,$this->stationid));
		$this->curprocessdata['wificonnect'] = $wificonnect['wificonnect'];

		//广告1
		$sql_ad1 = "SELECT SUM(dtime) AS ad1 FROM rha_count_process WHERE date = ? and stationid = ? AND model = 'ad' AND action = 'visit' AND detail = 'uv'";
		$ad1 = $this->FetRow($sql_ad1,array($this->date,$this->stationid));
		$this->curprocessdata['ad1'] = $ad1['ad1'];

		//注册页
		$sql_regpage = "SELECT SUM(dtime) AS regpage FROM rha_count_process WHERE date = ? and stationid = ? AND model = 'register' AND action = 'visit' AND detail = 'uv'";
		$regpage = $this->FetRow($sql_regpage,array($this->date,$this->stationid));
		$this->curprocessdata['regpage'] = $regpage['regpage'];

		//注册成功
		$sql_regnum = "SELECT SUM(dtime) AS regnum FROM rha_count_process WHERE date = ? and stationid = ? AND model = 'register' AND action = 'login' AND detail = 'success'";
		$regnum = $this->FetRow($sql_regnum,array($this->date,$this->stationid));
		$this->curprocessdata['regnum'] = $regnum['regnum'];

		//广告2
		$sql_ad2 = "SELECT SUM(dtime) AS ad2 FROM rha_count_process WHERE date = ? and stationid = ? AND model = 'welcome' AND action = 'visit' AND detail = 'uv'";
		$ad2 = $this->FetRow($sql_ad2,array($this->date,$this->stationid));
		$this->curprocessdata['ad2'] = $ad2['ad2'];

		//首页
		$sql_sindex = "SELECT SUM(dtime) AS sindex FROM rha_count_process WHERE date = ? and stationid = ? AND model = 'sindex' AND action = 'visit' AND detail = 'uv'";
		$sindex = $this->FetRow($sql_sindex,array($this->date,$this->stationid));
		$this->curprocessdata['sindex'] = $sindex['sindex'];		

		$res = $this->CmpProcessData();
		if (!$res) {
			
			$title = $this->date.$this->stationname.' 流程数据有误';
			$content = $this->date.$this->stationname.' 流程数据有误';
			$this->sendemail($title,$content);
		}
		
	}


	//和触发值进行比较
	public function CmpTrigger()
	{
		if ($this->trigger['wificonnect'] > $this->curprocessdata['wificonnect']) {
			
			$title = $this->date.$this->stationname.' wifi连接数低于触发值 '.$this->trigger['wificonnect'];
			$content = $this->date.$this->stationname.' wifi连接数低于触发值 '.$this->trigger['wificonnect'];
			$this->sendemail($title,$content);
		}

		if ($this->trigger['ad1'] > $this->curprocessdata['ad1']) {
			
			$title = $this->date.$this->stationname.' 广告1人数低于触发值 '.$this->trigger['ad1'];
			$content = $this->date.$this->stationname.' 广告1人数低于触发值 '.$this->trigger['ad1'];
			$this->sendemail($title,$content);
		}

		if ($this->trigger['regpage'] > $this->curprocessdata['regpage']) {
			
			$title = $this->date.$this->stationname.' 注册页人数低于触发值 '.$this->trigger['regpage'];
			$content = $this->date.$this->stationname.' 注册页人数低于触发值 '.$this->trigger['regpage'];
			$this->sendemail($title,$content);
		}

		if ($this->trigger['regnum'] > $this->curprocessdata['regnum']) {
			
			$title = $this->date.$this->stationname.' 注册成功人数低于触发值 '.$this->trigger['regnum'];
			$content = $this->date.$this->stationname.' 注册成功人数低于触发值 '.$this->trigger['regnum'];
			$this->sendemail($title,$content);
		}

		/*if ($this->trigger['ad2'] > $this->curprocessdata['ad2']) {
			
			$title = $this->date.$this->stationname.' 广告2人数低于触发值 '.$this->trigger['ad2'];
			$content = $this->date.$this->stationname.' 广告2人数低于触发值 '.$this->trigger['ad2'];
			$this->sendemail($title,$content);
		}*/

		if ($this->trigger['sindex'] > $this->curprocessdata['sindex']) {
			
			$title = $this->date.$this->stationname.' 首页人数低于触发值 '.$this->trigger['sindex'];
			$content = $this->date.$this->stationname.' 首页人数低于触发值 '.$this->trigger['sindex'];
			$this->sendemail($title,$content);
		}
	}

	//流程数据对比比较	
	public function CmpProcessData()
	{		
		return ($this->curprocessdata['wificonnect'] >= $this->curprocessdata['ad1'] && $this->curprocessdata['ad1'] >= $this->curprocessdata['regpage'] && $this->curprocessdata['regpage'] >= $this->curprocessdata['regnum'] && $this->curprocessdata['regnum'] >= $this->curprocessdata['sindex']);
	}

	//流失率数据报警
	public function loseWarning(){

		//广告1的流失率
		$wifi_ad1 = floor((($this->curprocessdata['ad1'] - $this->curprocessdata['wificonnect'])/$this->curprocessdata['wificonnect'])*100);

		//到达注册页的流失率
		$ad1_regpage = floor((($this->curprocessdata['regpage'] - $this->curprocessdata['ad1'])/$this->curprocessdata['ad1'])*100);

		//注册人数的流失率
		$regpage_regnum = floor((($this->curprocessdata['regnum'] - $this->curprocessdata['regpage'])/$this->curprocessdata['regpage'])*100);

		//到达首页流失率
		$regnum_sindex = floor((($this->curprocessdata['sindex'] - $this->curprocessdata['regnum'])/$this->curprocessdata['regnum'])*100);
		
		if (abs($wifi_ad1) >= 40) {			
			$title = $this->date.$this->stationname.' wifi连接到广告1流失率过高';
			$content = $this->date.$this->stationname.'wifi连接到广告1流失率过高 '.abs($wifi_ad1).'%';
			$this->sendemail($title,$content);
		}

		if (abs($ad1_regpage) >= 20) {
			$title = $this->date.$this->stationname.' 广告1到达注册页流失率过高';
			$content = $this->date.$this->stationname.'广告1到达注册页流失率过高 '.abs($ad1_regpage).'%';
			$this->sendemail($title,$content);
		}

		if (abs($regpage_regnum) >= 40) {
			$title = $this->date.$this->stationname.' 注册页到注册成功人数流失率过高';
			$content = $this->date.$this->stationname.'注册页到注册成功人数流失率过高 '.abs($regpage_regnum).'%';
			$this->sendemail($title,$content);
		}

		if (abs($regnum_sindex) >= 20) {
			$title = $this->date.$this->stationname.' 注册成功到首页人数流失率过高';
			$content = $this->date.$this->stationname.'注册成功到首页人数流失率过高 '.abs($regnum_sindex).'%';
			$this->sendemail($title,$content);
		}
	}



	
	protected function getdb_content()
	{
		$db = new PDO($this->dsn_content, $this->dbuser_content, $this->dbpasswd_content);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->exec('set names utf8');
	
		return $db;
	}

	/**
	 * 执行sql语句
	 * @param string $sql  sql语句
	 * @param array $data 插入的值
	 */
	protected function Execute($sql,$data)
	{
		$dbd = $this->db->prepare($sql);
		return $dbd->execute($data);
	}

	//获取一行数据
	protected function FetRow($sql,$data)
	{	
		try {
            $sth = $this->db->prepare($sql);
			$sth->execute($data);
			$result = $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException  $e) {
            exit('SQL语句：'.$sql.'<br />错误信息：'.$e->getMessage());
        }
		return $result[0];
	}
	
	protected function sendemail($title,$mailbody)
	{
		require("/home/upload/smtp/smtp.php");
	
		$mail = new MySendMail();
		$mail->setServer($this->mailhost, $this->mailuser, $this->mailpassword, 465, true); //到服务器的SSL连接
		//如果不需要到服务器的SSL连接，这样设置服务器：$mail->setServer("smtp.126.com", "XXX@126.com", "XXX");
		$mail->setFrom($this->mailuser);
		
		$mail->setReceiver($this->mailto);
	
	
		$mail->setMail($title, $mailbody);
		$mail->sendMail();
	}
	
	
}
error_reporting(E_ALL^E_NOTICE);
//接收cid
$stationid = $argv[1];
if (!$stationid) {
	exit;
}
$ob = new ProcessWanrning($stationid);

