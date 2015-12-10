<?php 
/**
 * 基础类
 */
header("Content-type: text/html; charset=utf-8");
ini_set('date.timezone', 'Asia/Shanghai');
ini_set('date.default_latitude', 31.5167);
ini_set('date.default_longitude', 121.4500);
class Bassclass
{
	
	protected $loginfo;
	protected $title;
	
	//数据库配置
	protected $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	protected $dbuser_content = 'root';
	protected $dbpasswd_content = 'password';
	protected $db ;
	
	//发送邮件配置
	protected $mailhost = 'mail.rockhippo.cn'; 
	protected $mailuser = "logs@rockhippo.cn";
	protected $mailpassword = "rockhippo";
	protected $mailto = "yunwei@rockhippo.cn";  //发送给运维组
	protected $Content;
	
	public function __construct($stationid)
	{	
		$this->db = $this->getdb_content();
		$p = $this->db->query("Select `gonetdir`,`stationname` From `rha_station` where id = $stationid");
		$p->setFetchMode(PDO::FETCH_ASSOC);
		$this->loginfo = $p->fetchAll();
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

	/**
	 * 插入数据库
	 * @param array $data 
	 * @param string $tb
	 * @return num : >0 插入成功
	 */
	protected function Insert($data,$tb='')
	{
		foreach ($data as $kt=>$vt)
		{
			$key.= "`$kt`,";
			$var.= "?,";
		}
		$key = rtrim($key,',');
		$var = rtrim($var,',');
		$in_sql = "Insert Into $tb($key) Value ($var);";		
		$sth = $this->db->prepare($in_sql);
		/*print_r(array_values($data));
		exit;*/
		$sth->execute(array_values($data));
		return $this->db->lastInsertId();
		
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
	
	
	protected function sendemail($mailbody)
	{
		require("/etc/openvpn/smtp.php");
	
		$mail = new MySendMail();
		$mail->setServer($this->mailhost, $this->mailuser, $this->mailpassword, 465, true); //到服务器的SSL连接
		//如果不需要到服务器的SSL连接，这样设置服务器：$mail->setServer("smtp.126.com", "XXX@126.com", "XXX");
		$mail->setFrom($this->mailuser);
		
		$mail->setReceiver($this->mailto);
	
		$mail->setMail($this->title, $mailbody);
		$mail->sendMail();
	}

	
	
}


