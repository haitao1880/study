<?php 
/**
 * 采集每个车站各个广告展示的次数（广告展示的pv）
 * 
 */
header("Content-type: text/html; charset=utf-8");
class Showadspv{
	//protected $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	protected $dsn_content = "mysql:host=192.168.28.201;dbname=rht_admin";
	protected $dbuser_content = 'root';
	protected $dbpasswd_content = 'password';
	protected $db ;
	
	public function __construct(){
		$this->db = $this->getdb_content();
		$date = date('Y_m_d',strtotime('-1 day'));
		
		$this->insertdata($date);
		//$this->execsql('2015-01-31','2015-03-24');
	}
	
	/**
	 * 按日期获取数据详情
	 * @param  date $date 当前的日期
	 * @return array     
	 */
	protected function getdetail($date){
		$sql = "SELECT date,detail,stationid FROM rha_webcount WHERE date = '$date' AND counttype = 2";
		return $this->query($sql);
	}
	
	/**
	 * 获取广告详情
	 */
	protected function getadsinfo(){
		$sql = "SELECT * FROM rha_ads";
		$data = $this->query($sql); 
		foreach($data as $v){
			$res[$v['ad1']] = $v['ad_name'];
			$res[$v['ad2']] = $v['id'];
		}

		return $res;
	}

	/**
	 * 分析数据，并写入数据至广告pv详情表
	 */
	protected function insertdata($date){
		

		$detail = $this->getdetail($date);
		$ads = $this->getadsinfo();
		
		
		foreach($detail as &$v){
			$v['detail'] = json_decode($v['detail'],true);
			foreach($v['detail'] as $val){
				//判断是否是广告1

				if (array_key_exists($val['name'],$ads) && stristr($val['name'],'ad')) {
					$ad_code1 = explode('_',$val['name']);
					$data = array('android'=>$val['android'],'ios'=>$val['ios'],'other'=>$val['else'],
								  'show_type'=>1,'ads_id'=>$ads['welcome_'.$ad_code1[1]],'ad_name'=>$ads['ad_'.$ad_code1[1]],
								  'date'=>$date,'stationid'=>$v['stationid']);
	
					$this->insert($data,'rha_ads_pv');
		
				}
				if (array_key_exists($val['name'],$ads) && stristr($val['name'],'welcome')) {
					$ad_code2 = explode('_',$val['name']);
					$data = array('android'=>$val['android'],'ios'=>$val['ios'],'other'=>$val['else'],
								  'show_type'=>2,'ads_id'=>$ads['welcome_'.$ad_code2[1]],'ad_name'=>$ads['ad_'.$ad_code2[1]],
								  'date'=>$date,'stationid'=>$v['stationid']);
					
					$this->insert($data,'rha_ads_pv');
				}
			}
		}

		
	}
	
	/**
	 * 连接数据库
	 * 
	 */
	protected function getdb_content(){
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
	protected function Execute($sql){
		$dbd = $this->db->prepare($sql);
		return $dbd->execute();
	}

	/**
	 * 执行查询语句
	 */
	protected function query($sql){
		$p = $this->db->query($sql);
		$p->setFetchMode(PDO::FETCH_ASSOC);
		return $p->fetchAll();
	}

	/**
	 * 插入广告展示pv详情表
	 */
	protected function insert($data,$tbname){

		$sql = "INSERT INTO $tbname(".implode(',',array_keys($data)).")values('".implode("','",array_values($data))."')";

		return $this->Execute($sql);
	}

	//获取多日数据
	protected function execsql($sdate,$edate){
		$sdate = strtotime($sdate);
		$edate = strtotime($edate);
		while ($sdate <= $edate) {
			
			$date = date('Y_m_d',$sdate);
			$this->insertdata($date);
			$sdate = strtotime('+1 day',$sdate);
		}
	}
}

$ob = new Showadspv();

?>
