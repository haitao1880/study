<?php 

header("Content-type: text/html; charset=utf-8");
/**
 * 每日日志监测
 */
class LogInspection
{
	
	private $DATA;
	
	private $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	private $dbuser_content = 'root';
	private $dbpasswd_content = 'password';
	private $db ;
	
	//发送邮件配置
	private $mailhost = 'mail.rockhippo.cn'; 
	private $mailuser = "logs@rockhippo.cn";
	private $mailpassword = "rockhippo";
	 private $mailto = "chris@rockhippo.cn";  //发送给运维组
 	//private $mailto = "terry@rockhippo.cn";

	private $Content;
	
	
	public function __construct()
	{
		$this->db = $this->getdb_content();
		$p = $this->db->query('Select `stationname`,`acfile`,`logfile`,`logname`,`is_moreweb` From `rha_station` ');
		$p->setFetchMode(PDO::FETCH_ASSOC);
		$this->DATA = $p->fetchAll();
		$this->CheckAction();
	}
	
	
	public function CheckAction()
	{
		$n_logdate = date('Y_m_d',strtotime('-1 day'));
		$a_logdate = date('Y-m-d');
		$access_date = date('Y-m-d',strtotime('-1 day'));
		$this->CheckAcLog($a_logdate);
		$this->CheckAccessLog($n_logdate,$access_date);
		
		if (!empty($this->Content)) {
			$this->sendemail($this->Content);
		}
	}

	/**
	 * AC日志监控
	 * @param string $a_logdate 文件中的日期格式
	 */
	private function CheckAcLog($a_logdate){
		foreach ($this->DATA as $key=>$var)
		{	
			
			$a_file = '/home/upload/aclog/'.$var['acfile'].'/aclog'.$a_logdate.'.txt';
			$a_file_o = '/home/upload/aclog/'.$var['acfile'].'/aclog'.$a_logdate.'_old.txt';
			$filename_ac = 'aclog'.$a_logdate.'.txt';

			//如果成功直接跳过
			if (file_exists($a_file_o)) {
				$sql_ac = 'insert into rha_checklog(station,logtype,filename,state,cdate) values (?,?,?,?,?)';
				$this->Execute($sql_ac,array($var["stationname"],2,$filename_ac,0,$a_logdate));
				continue;
			}

			if (!file_exists($a_file)) {
				$sql_ac = 'insert into rha_checklog(station,logtype,filename,state,cdate) values (?,?,?,?,?)';
				$this->Content .= $a_file.'<br>';
				$this->Content .= $var['stationname'].'aclog未上传！<br>';
				$this->Execute($sql_ac,array($var["stationname"],2,$filename_ac,1,$a_logdate));
				continue;
			}
			if(!file_exists($a_file_o)){
				$sql_ac = 'insert into rha_checklog(station,logtype,filename,state,cdate) values (?,?,?,?,?)';
				$this->Execute($sql_ac,array($var["stationname"],2,$filename_ac,3,$a_logdate));
				continue;
			}

			
		}
	}

	/**
	 * 访问日志监控
	 * @param string $n_logdate 文件中的日期格式
	 */
	private function CheckAccessLog($n_logdate,$access_date){
		foreach ($this->DATA as $key=>$var)
		{	
			//压缩文件名
			if ($var['is_moreweb']) {
				$z_file1 = $n_logdate.'.'.$var['logname'].'web1.nginxlog.tar.gz';
				$z_file2 = $n_logdate.'.'.$var['logname'].'web2.nginxlog.tar.gz';
			}else{
				$z_file1 = $n_logdate.'.'.$var['logname'].'web.nginxlog.tar.gz';
				$z_file2 = $n_logdate.'.'.$var['logname'].'web.nginxlog.tar.gz';
			}
			

			$n_file1 = '/home/upload/nginxlog/'.$var['logfile'].'/'.$z_file1;
			$n_file2 = '/home/upload/nginxlog/'.$var['logfile'].'/'.$z_file2;

			//判断压缩包是否上传,若没有上传则写入数据库
			if (!file_exists($n_file1)) {
				$sql = 'insert into rha_checklog(station,logtype,filename,state,cdate) values (?,?,?,?,?)';
				$this->Execute($sql,array($var["stationname"],1,$z_file1,1,$access_date));
			}

			if (!file_exists($n_file2)) {
				$sql = 'insert into rha_checklog(station,logtype,filename,state,cdate) values (?,?,?,?,?)';
				$this->Execute($sql,array($var["stationname"],1,$z_file2,1,$access_date));
			}
			
			if (!file_exists($n_file1) || !file_exists($n_file2)) {
				$this->Content .= $n_file1.'&nbsp;'.$n_file2.'<br>';
				$this->Content .= $var['stationname'].'nginxlog未上传！<br>';
				continue;
			}
			if ($var['is_moreweb']) {
				$access_appui1 = '/home/upload/nginxlog/'.$var['logfile'].'/'.$n_logdate.'/web1/www/appui_wonaonao_access.log';
				$access_appui2 = '/home/upload/nginxlog/'.$var['logfile'].'/'.$n_logdate.'/web2/www/appui_wonaonao_access.log';

				$access_m1 = '/home/upload/nginxlog/'.$var['logfile'].'/'.$n_logdate.'/web1/www/m_wonaonao_access.log';
				$access_m2 = '/home/upload/nginxlog/'.$var['logfile'].'/'.$n_logdate.'/web2/www/m_wonaonao_access.log';
			}else{
				$access_appui1 = '/home/upload/nginxlog/'.$var['logfile'].'/'.$n_logdate.'/web1/www/m_wonaonao_access.log';
				$access_appui2 = '/home/upload/nginxlog/'.$var['logfile'].'/'.$n_logdate.'/web1/www/m_wonaonao_access.log';

				$access_m1 = '/home/upload/nginxlog/'.$var['logfile'].'/'.$n_logdate.'/web1/www/m_wonaonao_access.log';
				$access_m2 = '/home/upload/nginxlog/'.$var['logfile'].'/'.$n_logdate.'/web1/www/m_wonaonao_access.log';
			}
			

			//判断web1是否解压
			if (!file_exists($access_appui1)) {
				$sql_x = 'insert into rha_checklog(station,logtype,filename,state,cdate) values (?,?,?,?,?)';
				$this->Execute($sql_x,array($var["stationname"],3,$z_file1,2,$access_date));
			}

			if(!file_exists($access_appui2)){
				$sql_x = 'insert into rha_checklog(station,logtype,filename,state,cdate) values (?,?,?,?,?)';
				$this->Execute($sql_x,array($var["stationname"],3,$z_file2,2,$access_date));
			}

			if (!file_exists($access_appui1) || !file_exists($access_appui2)) {
				$this->Content = '';
				$this->Content .= $n_file1.'&nbsp;'.$n_file2.'<br>';
				$this->Content .= $var['stationname'].'nginxlog未被解压！<br>';
				continue;
			}

			//存放所有要监控的日志文件
			$access_web1 = array($access_appui1,$access_m1);
			$access_web2 = array($access_appui2,$access_m2);

			$hours = array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23');
			foreach ($hours as $v1){
				$access_hour1 = '/home/upload/nginxlog/'.$var['logfile'].'/'.$n_logdate.'/web1/www/m_wonaonao_record_'.$v1.'.log';
				$access_hour2 = '/home/upload/nginxlog/'.$var['logfile'].'/'.$n_logdate.'/web2/www/m_wonaonao_record_'.$v1.'.log';
				array_push($access_web1, $access_hour1);
				array_push($access_web2, $access_hour2);
			}

			//循环判断web1要监控的日志文件是否存在
			foreach($access_web1 as $web1){
				$filename = 'web1_'.basename($web1);
				if (file_exists($web1)) {
					$sql_web1 = 'insert into rha_checklog(station,logtype,filename,state,cdate) values (?,?,?,?,?)';
					$this->Execute($sql_web1,array($var["stationname"],3,$filename,0,$access_date));
				}else{
					$sql_web1 = 'insert into rha_checklog(station,logtype,filename,state,cdate) values (?,?,?,?,?)';
					$this->Execute($sql_web1,array($var["stationname"],3,$filename,4,$access_date));
				}
				
			}


			//循环判断web2要监控的日志文件是否存在
			foreach($access_web2 as $web2){
				$filename = 'web2_'.basename($web2);
				if (file_exists($web2)) {
					$sql_web2 = 'insert into rha_checklog(station,logtype,filename,state,cdate) values (?,?,?,?,?)';
					$this->Execute($sql_web2,array($var["stationname"],3,$filename,0,$access_date));
				}else{
					$sql_web2 = 'insert into rha_checklog(station,logtype,filename,state,cdate) values (?,?,?,?,?)';
					$this->Execute($sql_web2,array($var["stationname"],3,$filename,4,$access_date));
				}
			}
			
		}
	}
	
	private function getdb_content()
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
	private function Execute($sql,$data)
	{
		$dbd = $this->db->prepare($sql);
		return $dbd->execute($data);
	}
	
	private function sendemail($mailbody)
	{
		require("/etc/openvpn/smtp.php");
	
		$mail = new MySendMail();
		$mail->setServer($this->mailhost, $this->mailuser, $this->mailpassword, 465, true); //到服务器的SSL连接
		//如果不需要到服务器的SSL连接，这样设置服务器：$mail->setServer("smtp.126.com", "XXX@126.com", "XXX");
		$mail->setFrom($this->mailuser);
		
		$mail->setReceiver($this->mailto);
	
	
		$mail->setMail("日志发送监测", $mailbody);
		$mail->sendMail();
	}
	
	
}


$ob = new LogInspection();

?>
