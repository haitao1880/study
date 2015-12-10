<?php
/**
 * 交运车上电影、音乐、应用详细信息统计
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
		$this->db = $this->GetDB();
		$this->tb = $this->SetTb();
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


/***************************************************************************
* 影片信息统计
*/
class Jiaoyun_Movie extends DatabeseAction
{	
	public function __construct()
	{	
		parent::__construct();		
		$this->MovieExcute();		
	}

	/**
	 * 海报点击
	 */
	protected function MoviePosterNum(){
		$sql = "SELECT $this->date as date,0 as movie_cat_id,car_id as carid,pid as movie_id,count(*) as num,1 as type FROM $this->tb WHERE FROM_UNIXTIME(create_time,'%Y-%m-%d')='$this->date' AND type='502' GROUP BY carid,movie_id ORDER BY NULL";

		$tb = $this->SetTb('movie');
		$sql_insert = "INSERT INTO $tb(date,movie_cat_id,carid,movie_id,num,type) $sql";
		$res = $this->db->executesql($sql_insert);
	}

	/**
	 * 播放数
	 */
	protected function MoviePlayNum(){
		$sql = "SELECT $this->date as date,0 as movie_cat_id,car_id as carid,pid as movie_id,count(*) as num,2 as type FROM $this->tb WHERE FROM_UNIXTIME(create_time,'%Y-%m-%d')='$this->date' AND type='503' GROUP BY carid,movie_id ORDER BY NULL";

		$tb = $this->SetTb('movie');
		$sql_insert = "INSERT INTO $tb(date,movie_cat_id,carid,movie_id,num,type) $sql";
		$res = $this->db->executesql($sql_insert);
		
	}

	/**
	 * 播放时长
	 */
	protected function MoviePlayTime(){
		$tb = $this->SetTb('playtime');
		$sql = "SELECT $this->date as date,0 as movie_cat_id,car_id as carid,pid as movie_id,sum(playtime) as num,3 as type FROM $tb WHERE FROM_UNIXTIME(create_time,'%Y-%m-%d')='$this->date' AND type='2' GROUP BY carid,movie_id ORDER BY NULL";

		$tb = $this->SetTb('movie');
		$sql_insert = "INSERT INTO $tb(date,movie_cat_id,carid,movie_id,num,type) $sql";
		$res = $this->db->executesql($sql_insert);
	}	

	/**
	 * 执行
	 */
	protected function MovieExcute(){
		$this->MoviePosterNum();
		$this->MoviePlayNum();
		$this->MoviePlayTime();
	}
}


/***************************************************************************
* 歌曲信息统计
*/
class Jiaoyun_Music extends DatabeseAction
{	

	public function __construct()
	{	
		parent::__construct();		
		$this->MusicExcute();		
	}

	/**
	 * 海报点击
	 */
	protected function MusicPosterNum(){
		$sql = "SELECT $this->date as date,0 as music_cat_id,car_id as carid,pid as music_id,count(*) as num,1 as type FROM $this->tb WHERE FROM_UNIXTIME(create_time,'%Y-%m-%d')='$this->date' AND type='506' GROUP BY carid,music_id ORDER BY NULL";

		$tb = $this->SetTb('music');
		$sql_insert = "INSERT INTO $tb(date,music_cat_id,carid,movie_id,num,type) $sql";
		$res = $this->db->executesql($sql_insert);
	}

	/**
	 * 播放数
	 */
	protected function MusicPlayNum(){
		$sql = "SELECT $this->date as date,0 as music_cat_id,car_id as carid,pid as music_id,count(*) as num,2 as type FROM $this->tb WHERE FROM_UNIXTIME(create_time,'%Y-%m-%d')='$this->date' AND type='507' GROUP BY carid,music_id ORDER BY NULL";

		$tb = $this->SetTb('music');
		$sql_insert = "INSERT INTO $tb(date,music_cat_id,carid,movie_id,num,type) $sql";
		$res = $this->db->executesql($sql_insert);
		
	}

	/**
	 * 播放时长
	 */
	protected function MusicPlayTime(){
		$tb = $this->SetTb('playtime');
		$sql = "SELECT $this->date as date,0 as music_cat_id,car_id as carid,pid as music_id,sum(playtime) as num,3 as type FROM $tb WHERE FROM_UNIXTIME(create_time,'%Y-%m-%d')='$this->date' AND type='1' GROUP BY carid,music_id ORDER BY NULL";

		$tb = $this->SetTb('music');
		$sql_insert = "INSERT INTO $tb(date,music_cat_id,carid,movie_id,num,type) $sql";
		$res = $this->db->executesql($sql_insert);
	}	

	/**
	 * 执行
	 */
	protected function MusicExcute(){
		$this->MusicPosterNum();
		$this->MusicPlayNum();
		$this->MusicPlayTime();
	}
}


/**
 * 应用下载统计
 */
class Jiaoyun_DownApp extends DatabeseAction
{	

	public function __construct()
	{	
		parent::__construct();		
		$this->AppDownNum();		
	}

	//应用下载统计
	protected function 	AppDownNum(){
		$sql = "SELECT $this->date as date,car_id as carid,pid as app_id,count(*) as down_num FROM $this->tb WHERE FROM_UNIXTIME(create_time,'%Y-%m-%d')='$this->date' AND type='401' GROUP BY carid,app_id ORDER BY NULL";

		$tb = $this->SetTb('app_down');
		$sql_insert = "INSERT INTO $tb(date,carid,app_id,down_num) $sql";
		$res = $this->db->executesql($sql_insert);
	}
	
}


new Jiaoyun_Movie();
new Jiaoyun_Music();
new Jiaoyun_DownApp();
