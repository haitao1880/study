<?php
/**
 * 交运车上电影、音乐、应用栏目统计
 */
header("Content-type: text/html; charset=utf-8");
ini_set('date.timezone', 'Asia/Shanghai');
ini_set('date.default_latitude', 31.5167);
ini_set('date.default_longitude', 121.4500);

$jiaoyundir = dirname(__file__).DIRECTORY_SEPARATOR;
require_once $jiaoyundir.'Jiaoyun_Database.php';
/**
 * 数据库
 */
class DatabeseAction{
	protected $db;//数据库对象
	protected $tb;
	protected $date;
	public function __construct(){
		$this->GetDB();
		$this->date = date('Y-m-d',strtotime("-1 day"));
	}

	protected function GetDB(){
		$this->db = Jiaoyun_Database::GetIns();
		$this->tb = $this->SetTb();
	}

	protected function SetTb($dbname="log_app")
	{
		return Jiaoyun_Database::$prefix.$dbname;
	}

}



/***********************************************
 *
 * 主要栏目统计类
 */
class Jiaoyun_main_plate extends DatabeseAction{
	protected $carid;
	protected $data;//存放入库数据

	public function __construct($carid)
	{	
		parent::__construct();		
		$this->carid = $carid;
		$this->ExcuteBigCat();		
	}

	/**
	 * 电影主页访问数
	 */
	protected function MovieIndexNum()
	{
		$sql = "SELECT COUNT(*) as movie_index FROM $this->tb WHERE FROM_UNIXTIME(create_time,'%Y-%m-%d')='$this->date' and car_id='$this->carid' AND type=0 AND current_page='/movie/index'";

		$res = $this->db->FetRow($sql);
		if ($res['movie_index']) {
			$this->data['movie_index'] = $res['movie_index'];
		}
	}

	/**
	 * 音乐主页访问数
	 */
	protected function MusicIndexNum(){
		$sql = "SELECT COUNT(*) as music_index FROM $this->tb WHERE FROM_UNIXTIME(create_time,'%Y-%m-%d')='$this->date' and car_id='$this->carid' AND type=0 AND current_page='/music/index'";

		$res = $this->db->FetRow($sql);
		if ($res['music_index']) {
			$this->data['music_index'] = $res['music_index'];
		}
	}

	/**
	 * 应用主页
	 */
	protected function AppIndexNum(){
		$sql = "SELECT COUNT(*) as app_index FROM $this->tb WHERE FROM_UNIXTIME(create_time,'%Y-%m-%d')='$this->date' and car_id='$this->carid' AND type=0 AND current_page='/app/index'";

		$res = $this->db->FetRow($sql);
		if ($res['app_index']) {
			$this->data['app_index'] = $res['app_index'];
		}
	}

	protected function InToTable(){
		if (empty($this->data)) {
			return;
		}		
		$tb = $this->SetTb('main_plate');
		
		$this->data['date'] = $this->date;
		$this->data['carid'] = $this->carid;
		$this->db->Insert($this->data,$tb);
	}

	protected function ExcuteBigCat()
	{
		$this->MovieIndexNum();
		$this->MusicIndexNum();
		$this->AppIndexNum();
		$this->InToTable();
	}	
}





/***************************************************************************
* 电影栏目统计
*/
class Jiaoyun_MovieCat extends DatabeseAction
{
	protected $carid;
	protected $data;//存放入库数据
	protected $cats;//存放入库数据

	public function __construct($carid)
	{	
		parent::__construct();		
		$this->carid = $carid;
		$this->CountMovieCat();		
	}

	/**
	 * 获取要统计的电影类别
	 */
	protected function MovieFunny(){
		$sql = "SELECT type FROM rb_movie_music_cat WHERE class=1";
		$this->cats = $this->db->FetAll($sql);
	}

	/**
	 * 统计电影每个类别的数据
	 */
	protected function CountMovieCat(){
		$this->MovieFunny();
		foreach($this->cats as $type){
			$sql = "SELECT count(*) as visit_num FROM $this->tb WHERE FROM_UNIXTIME(create_time,'%Y-%m-%d')='$this->date' and car_id='$this->carid' AND type= '$type'";
			$res = $this->db->FetRow($sql);
			if ($res['visit_num']) {
				$this->data['visit_num'] = $res['visit_num'];
				$this->data['movie_cat_id'] = $type;
				$this->InToTable();
			}

		}
		
	}

	/**
	 * 数据入库
	 */
	protected function InToTable(){
		if (empty($this->data)) {
			return;
		}		
		$tb = $this->SetTb('movie_cat');		
		$this->data['date'] = $this->date;
		$this->data['carid'] = $this->carid;
		$this->db->Insert($this->data,$tb);
	}
}


error_reporting(E_ALL^E_NOTICE);

//接收cid
$cid = $argv[1];
if (!$cid) {
	exit;
}

new Jiaoyun_main_plate($cid);
new Jiaoyun_MovieCat($cid);