<?php
class Psys_CountRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * 获取统计数据，按每天统计出的人次及浏览时长
	 * @param string $model
	 * @param number $page
	 * @param number $pagesize
	 * @return Ambigous <multitype:, boolean, number>
	 */
	public function CountList($model,$page=1,$pagesize=10)
	{
		$offset = ($page-1)*$pagesize;
		$where = " `model`='$model' ";
		$sql = "SELECT
					FROM_UNIXTIME(`createtime`, '%Y-%m-%d') AS ctime,
					SUM(`device` = 1) AS mnum,
					SUM(`device` = 2) AS pcnum,
					SUM(IF(`device` = 1,CONCAT(modifytime - createtime),0)) AS mtime,
					SUM(IF(`device` = 2,CONCAT(modifytime - createtime),0)) AS pctime
				FROM
					rha_column
				WHERE
					 $where
				GROUP BY
					ctime
				ORDER BY ctime DESC ";

		$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";
		$sql .= " LIMIT $offset,$pagesize";
		$allrow = $this->Query($sql);
		$allnum = $this->FetchColOne($c_sql);
		
		return array('allrow'=>$allrow,'allnum'=>$allnum);
	}
	
	public function DetailList($model,$time)
	{
		$sql="SELECT
				sum(action LIKE 'click-play%') AS play,
				sum(action LIKE 'click-pause%') AS pause,
				COUNT(*) AS cnum,
				rht_idc.rhi_videos.vname
			FROM
				rha_useraction
			JOIN rht_idc.rhi_videos ON rht_idc.rhi_videos.id = SUBSTRING_INDEX(action, '-' ,- 1)
			WHERE
				model = '$model' 
			AND (
				action LIKE 'click-play%'
				OR action LIKE 'click-pause%'
				)
			AND FROM_UNIXTIME(createtime, '%Y-%m-%d') = '$time'
			ORDER BY
				cnum DESC
			LIMIT 100";
		
		return $this->Query($sql);		
	}
	//获取所有注册统计
	public function RegallList($page,$pagesize){
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT
					
					SUM(type = 2) AS rnum,
					SUM(
						detail = 'click-register-submit'
					) AS submit,
					SUM(
						 detail = 'click-sendMessage'
					) AS sendsms,
					SUM(
						detail LIKE 'register-fail%'
					) AS fail,
					SUM(
						detail = 'register-success'
					) AS success,
					FROM_UNIXTIME(curtime,'%Y-%m-%d') as day
				FROM
					rha_record
				WHERE
					model = 'member'
				AND action = 'register'

				And FROM_UNIXTIME(curtime,'%Y-%m-%d') > '2014-08-24' 
				GROUP BY day ORDER by day desc";
			$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";

			$sql .= " LIMIT $offset,$pagesize";
			$allrow = $this->Query($sql);
			$allnum = $this->FetchColOne($c_sql);
			
			return array('allrow'=>$allrow,'allnum'=>$allnum);
	}
	//统计当天所有的注册信息
	public function TotalRegList($page,$pagesize,$hours,$date,$ipcno){
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT
					
					SUM(type = 2) AS rnum,
					SUM(detail = 'register-success') AS success,
					SUM(detail LIKE 'register-fail%') AS fail,
					CONCAT(
						ROUND(
							(
								SUM(detail = 'register-success') / SUM(type = 2) * 100
							)
						),
						'%'
					) AS sucrate,
					CONCAT(
						FROM_UNIXTIME(curtime, '%Y-%m-%d %H:00'),
						'-',
						FROM_UNIXTIME(curtime + 3600, '%H:00')
					) AS time
				FROM
					rha_record
				WHERE
					model = 'member'
				AND action = 'register'
				AND FROM_UNIXTIME(curtime, '%Y-%m-%d') = '".$date."'
				GROUP BY
					
					time order by time desc";
			$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";
			//$sql .= " LIMIT $offset,$pagesize";
			$allrow = $this->Query($sql);
			$allnum = $this->FetchColOne($c_sql);

			return array('code'=>1,'allrow'=>$allrow,'allnum'=>$allnum);
	}
	//注册统计
	public function RegList($page,$pagesize,$hours,$date,$ipcno){
		$offset = ($page-1)*$pagesize;
		
		if ($hours == 'hours') {
			
			$sql = "SELECT
						train,
						SUM(type = 2) AS rnum,
						SUM(detail = 'register-success') AS success,
						SUM(detail LIKE 'register-fail%') AS fail,
						CONCAT(
							ROUND(
								(
									SUM(detail = 'register-success') / SUM(type = 2) * 100
								)
							),
							'%'
						) AS sucrate,
						CONCAT(
							FROM_UNIXTIME(curtime, '%Y-%m-%d %H:00'),
							'-',
							FROM_UNIXTIME(curtime + 3600, '%H:00')
						) AS time
					FROM
						rha_record
					WHERE
						model = 'member'
					AND action = 'register'
					AND train = '".$ipcno."'
					AND FROM_UNIXTIME(curtime, '%Y-%m-%d') = '".$date."'
					GROUP BY
						train,
						time order by time desc";
			$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";
			$sql .= " LIMIT $offset,$pagesize";
			$allrow = $this->Query($sql);
			$allnum = $this->FetchColOne($c_sql);

			return array('code'=>1,'allrow'=>$allrow,'allnum'=>$allnum);
		} else {
			$sql = "SELECT
					train,
					SUM(type = 2) AS rnum,
					SUM(
						detail = 'click-register-submit'
					) AS submit,
					SUM(
						 detail = 'click-sendMessage'
					) AS sendsms,
					SUM(
						detail LIKE 'register-fail%'
					) AS fail,
					SUM(
						detail = 'register-success'
					) AS success,
					FROM_UNIXTIME(curtime,'%Y-%m-%d') as day
				FROM
					rha_record
				WHERE
					model = 'member'
				AND action = 'register'
				AND train = '".$ipcno."'

				And FROM_UNIXTIME(curtime,'%Y-%m-%d') > '2014-08-24' 
				GROUP BY day order by day desc";
			$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";

			$sql .= " LIMIT $offset,$pagesize";
			$allrow = $this->Query($sql);
			$allnum = $this->FetchColOne($c_sql);
			
			return array('allrow'=>$allrow,'allnum'=>$allnum);
		}
		
		
	}

	//登录信息统计
	public function LoginDetail($page,$pagesize,$hours,$date,$ipcno){
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT
					train,
					COUNT(*) as loginno,
					CONCAT(
						FROM_UNIXTIME(curtime, '%Y-%m-%d %H:00'),
						'-',
						FROM_UNIXTIME(curtime + 3600, '%H:00')
					) AS time
				FROM
					rha_record
				WHERE
					model = 'index'
				AND action = 'index'
				AND train = '".$ipcno."'
				AND FROM_UNIXTIME(curtime, '%Y-%m-%d') = '".$date."'
				GROUP BY
					train,time";
			$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";

			//$sql .= " LIMIT $offset,$pagesize";
			$allrow = $this->Query($sql);
			$allnum = $this->FetchColOne($c_sql);
			
			return array('allrow'=>$allrow,'allnum'=>$allnum);
	}
	//每日登录信息
	public function TotalLoginDetail($page,$pagesize,$hours,$date,$ipcno){
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT
					
					COUNT(*) as loginno,
					CONCAT(
						FROM_UNIXTIME(curtime, '%Y-%m-%d %H:00'),
						'-',
						FROM_UNIXTIME(curtime + 3600, '%H:00')
					) AS time
				FROM
					rha_record
				WHERE
					model = 'index'
				AND action = 'index'
				AND FROM_UNIXTIME(curtime, '%Y-%m-%d') = '".$date."'
				GROUP BY
					time order by time desc";
			$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";

			//$sql .= " LIMIT $offset,$pagesize";
			$allrow = $this->Query($sql);
			$allnum = $this->FetchColOne($c_sql);
			
			return array('allrow'=>$allrow,'allnum'=>$allnum);
	}
	//音乐统计
	public function TotalMusic($page,$pagesize){
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT
					FROM_UNIXTIME(curtime,'%Y-%m-%d') as time,
					
					SUM(
						type = 4
						AND (
							detail LIKE 'click-play-%'
							OR detail LIKE 'click-pre-%'
							OR detail LIKE 'click-next-%'
						)
					) AS playtotal,
					sum(detail LIKE 'click-play-%') AS play,
					sum(detail LIKE 'click-pause-%') AS pause,
					sum(detail LIKE 'click-next-%') AS next,
					sum(detail LIKE 'click-pre-%') AS pre
				FROM
					rha_record
				WHERE
					FROM_UNIXTIME(curtime,'%Y-%m-%d')>'2014-08-24'
				GROUP BY
					time
				ORDER BY
					time desc";
		$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";

		$sql .= " LIMIT $offset,$pagesize";
		$allrow = $this->Query($sql);
		$allnum = $this->FetchColOne($c_sql);
		return array('allrow'=>$allrow,'allnum'=>$allnum);
		
	}
	//按车厢音乐统计
	public function CountMusic($page,$pagesize,$ipcno){
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT
					FROM_UNIXTIME(curtime,'%Y-%m-%d') as time,
					train,
					SUM(
						type = 4
						AND (
							detail LIKE 'click-play-%'
							OR detail LIKE 'click-pre-%'
							OR detail LIKE 'click-next-%'
						)
					) AS playtotal,
					sum(detail LIKE 'click-play-%') AS play,
					sum(detail LIKE 'click-pause-%') AS pause,
					sum(detail LIKE 'click-next-%') AS next,
					sum(detail LIKE 'click-pre-%') AS pre
				FROM
					rha_record
				WHERE
					train = '".$ipcno."'
				GROUP BY
					time
				ORDER BY
					time desc";
		$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";

		$sql .= " LIMIT $offset,$pagesize";
		$allrow = $this->Query($sql);
		$allnum = $this->FetchColOne($c_sql);
		return array('allrow'=>$allrow,'allnum'=>$allnum);
		
	}

	//音乐播放统计
	public function CountMusicPlay($date,$ipcno){
		$sql = "SELECT
					train,
					count(*) AS c,
					CONCAT(
						FROM_UNIXTIME(curtime, '%Y-%m-%d %H:00'),
						'-',
						FROM_UNIXTIME(curtime + 3600, '%H:00')
					) AS t
				FROM
					rha_record
				WHERE
					model = 'music'
				AND action = 'index'
				AND FROM_UNIXTIME(curtime, '%Y-%m-%d') = '".$date."'
				AND train = '".$ipcno."'
				AND type = 2
				GROUP BY
					train,
					t
				ORDER BY
					train,
					t";
		$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";


		//$sql .= " LIMIT 100";
		$allrow = $this->Query($sql);
		$allnum = $this->FetchColOne($c_sql);

		return array('allrow'=>$allrow,'allnum'=>$allnum);
	}
	//每日音乐播放统计
	public function TotalCountMusicPlay($date){
		$sql = "SELECT
					train,
					count(*) AS c,
					CONCAT(
						FROM_UNIXTIME(curtime, '%Y-%m-%d %H:00'),
						'-',
						FROM_UNIXTIME(curtime + 3600, '%H:00')
					) AS t
				FROM
					rha_record
				WHERE
					model = 'music'
				AND action = 'index'
				AND FROM_UNIXTIME(curtime, '%Y-%m-%d') = '".$date."'
				AND type = 2
				GROUP BY
					train,
					t
				ORDER BY
					train,
					t";
		$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";


		//$sql .= " LIMIT 100";
		$allrow = $this->Query($sql);
		$allnum = $this->FetchColOne($c_sql);

		return array('allrow'=>$allrow,'allnum'=>$allnum);
	}
	//音乐暂停统计
	public function pause($musicid){
		$sql = "SELECT
				SUM(type = 4) AS pause,
				SUBSTRING_INDEX(detail, '-' ,- 1) AS musicid,
				SUBSTRING_INDEX(detail, '-' ,- 1) AS detail
			FROM
				rha_record
			WHERE
				model = 'music'
			AND detail LIKE 'click-pause-%'
			GROUP BY detail
			HAVING musicid in (".$musicid.")";
			
		$pau = $this->Query($sql);
		return $pau;

	}
	public function GetMusicInfo($musicid){
		$this->SetDb('db-rht_idc');

		$sql = "SELECT * FROM rhi_albummusic WHERE id = ?";
		$info = $this->Query($sql,array($musicid));
		return $info[0];
	}
	public function CountAlbumHits($date,$ipcno){
		$sql = "SELECT
					
					train,
					SUM(
						type = 4
						AND (
							detail LIKE 'click-albummusic-%'
						)
					) AS hitstotal,
					a.aname
				FROM
					rha_record as r INNER JOIN rht_train.rht_album as a on SUBSTRING_INDEX(r.detail ,'-',-

				1)=a.id
				WHERE
					FROM_UNIXTIME(curtime,'%Y-%m-%d')='".$date."'
					and r.train = '".$ipcno."'
				GROUP BY
					a.aname
				ORDER BY
					hitstotal DESC";
		$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";

		//$sql .= " LIMIT 100";
		$allrow = array_filter($this->Query($sql));

		$allnum = $this->FetchColOne($c_sql);
		
		return array('allrow'=>$allrow,'allnum'=>$allnum);
		
	}
	//每日榜单点击排行
	public function TotalCountAlbumHits($date){
		$sql = "SELECT
					
					SUM(
						type = 4
						AND (
							detail LIKE 'click-albummusic-%'
						)
					) AS hitstotal,
					a.aname
				FROM
					rha_record as r INNER JOIN rht_train.rht_album as a on SUBSTRING_INDEX(r.detail ,'-',-

				1)=a.id
				WHERE
					FROM_UNIXTIME(curtime,'%Y-%m-%d')='".$date."'
				GROUP BY
					a.aname
				ORDER BY
					hitstotal DESC";
		$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";

		//$sql .= " LIMIT 100";
		$allrow = array_filter($this->Query($sql));

		$allnum = $this->FetchColOne($c_sql);
		
		return array('allrow'=>$allrow,'allnum'=>$allnum);
		
	}
	//获取榜单详情
	public function GetAlbumInfo($albumid){
		$this->SetDb('db-rht_idc');
		$sql = "SELECT * FROM rhi_album WHERE id in (".$albumid.") ";
		$info = $this->Query($sql);
		return $info;
	}
	//按车厢统计首页板块点击次数;
	public function NavHits($page,$pagesize,$ipcno){
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT
				train,
				FROM_UNIXTIME(curtime, '%Y-%m-%d') AS date,
				sum(
					detail = 'click-nav-inquiries'
				) AS inquiries,
				sum(detail = 'click-nav-dzfw') AS dzfw,
				sum(detail = 'click-nav-foods') AS foods,
				sum(detail = 'click-nav-luggage') AS luggage,
				sum(detail = 'click-nav-movie') AS movie,
				sum(detail = 'click-nav-game') AS game,
				sum(detail = 'click-nav-app') AS app,
				sum(detail = 'click-nav-music') AS music
			FROM
				rha_record
			WHERE
				FROM_UNIXTIME(curtime, '%Y-%m-%d') > '2014-08-24'
			AND train = '".$ipcno."'
			GROUP BY
				date
			ORDER BY date desc";
		$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";

		$sql .= " LIMIT $offset,$pagesize";
		$allrow = $this->Query($sql);
		$allnum = $this->FetchColOne($c_sql);
		return array('allrow'=>$allrow,'allnum'=>$allnum);
	}
	//统计首页板块点击次数;
	public function TotalNavHits($page,$pagesize){
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT
				FROM_UNIXTIME(curtime, '%Y-%m-%d') AS date,
				sum(
					detail = 'click-nav-inquiries'
				) AS inquiries,
				sum(detail = 'click-nav-dzfw') AS dzfw,
				sum(detail = 'click-nav-foods') AS foods,
				sum(detail = 'click-nav-luggage') AS luggage,
				sum(detail = 'click-nav-movie') AS movie,
				sum(detail = 'click-nav-game') AS game,
				sum(detail = 'click-nav-app') AS app,
				sum(detail = 'click-nav-music') AS music
			FROM
				rha_record
			WHERE
				FROM_UNIXTIME(curtime, '%Y-%m-%d') > '2014-08-24'
			GROUP BY
				date
			ORDER BY date desc";
		$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";

		$sql .= " LIMIT $offset,$pagesize";
		$allrow = $this->Query($sql);
		$allnum = $this->FetchColOne($c_sql);
		return array('allrow'=>$allrow,'allnum'=>$allnum);
	}
	//按日期统计点击次数
	public function navhitno($time,$ipcno){
		$sql = "SELECT
				train,
				concat(FROM_UNIXTIME(curtime, '%Y-%m-%d %H:00'),'-',FROM_UNIXTIME(curtime+3600, '%H:00')) AS date,
				sum(
					detail = 'click-nav-inquiries'
				) AS inquiries,
				sum(detail = 'click-nav-dzfw') AS dzfw,
				sum(detail = 'click-nav-foods') AS foods,
				sum(detail = 'click-nav-luggage') AS luggage,
				sum(detail = 'click-nav-movie') AS movie,
				sum(detail = 'click-nav-game') AS game,
				sum(detail = 'click-nav-app') AS app,
				sum(detail = 'click-nav-music') AS music
			FROM
				rha_record
			WHERE
				FROM_UNIXTIME(curtime, '%Y-%m-%d') = '".$time."'
			AND train = '".$ipcno."'
			GROUP BY
				date
			ORDER BY date desc";
		$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";
		$allrow = $this->Query($sql);
		$allnum = $this->FetchColOne($c_sql);
		return array('allrow'=>$allrow,'allnum'=>$allnum);
	}
	//按日期统计点击次数详情
	public function TotalNavHit($time){
		$sql = "SELECT
				train,
				concat(FROM_UNIXTIME(curtime, '%Y-%m-%d %H:00'),'-',FROM_UNIXTIME(curtime+3600, '%H:00')) AS date,
				sum(
					detail = 'click-nav-inquiries'
				) AS inquiries,
				sum(detail = 'click-nav-dzfw') AS dzfw,
				sum(detail = 'click-nav-foods') AS foods,
				sum(detail = 'click-nav-luggage') AS luggage,
				sum(detail = 'click-nav-movie') AS movie,
				sum(detail = 'click-nav-game') AS game,
				sum(detail = 'click-nav-app') AS app,
				sum(detail = 'click-nav-music') AS music
			FROM
				rha_record
			WHERE
				FROM_UNIXTIME(curtime, '%Y-%m-%d') = '".$time."'
			GROUP BY
				date
			ORDER BY null";
		$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";
		$allrow = $this->Query($sql);
		$allnum = $this->FetchColOne($c_sql);
		return array('allrow'=>$allrow,'allnum'=>$allnum);
	}
	/**
	 * 页面点击数统计
	 * @param num $page
	 * @param num $pagesize
	 * @param string $ipcno
	 * @param string $time
	 * @param string $where
	 * @return multitype:string Ambigous <multitype:, boolean, number>
	 */
	public function PageHits($page,$pagesize,$ipcno,$time,$where = '')
	{
		$offset = ($page-1)*$pagesize;
		if ($ipcno) {
			$where .= " And train = '$ipcno' ";
		}
		if ($time) {
			$where .= " And FROM_UNIXTIME(curtime, '%Y-%m-%d') = '$time' ";
		}
		$ser = $time ? " CONCAT(FROM_UNIXTIME(curtime, '%Y-%m-%d %H:00'),'-',FROM_UNIXTIME(curtime + 3600, '%H:00'))" : " FROM_UNIXTIME(curtime, '%Y-%m-%d') ";
		
		$sql = "SELECT
				$ser AS time,
				SUM(model = 'movie') AS `movie`,
				SUM(model = 'music') AS `music`,
				SUM(model = 'game') AS `game`,
				SUM(model = 'app') AS `app`,
				train
			FROM
				rha_record
			WHERE
				type = 2 AND action = 'index' $where
			GROUP BY
				time";
		
		
		$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";
		if (!$time) {
			$sql .= " Order by time desc Limit $offset,$pagesize";
		}
		$allrow = $this->Query($sql);
		$allnum = $this->FetchColOne($c_sql);
		return array('allrow'=>$allrow,'allnum'=>$allnum);
		
	}

	//每天总的应用下载量
	public function AllAppDowns($page,$pagesize){
			$offset = ($page-1)*$pagesize;
			$sql = "SELECT
						sum(model = 'app' or model='music' or model='movie' or model='index') AS appnum,
						sum(model = 'game') AS gamenum,
						FROM_UNIXTIME(curtime, '%Y-%m-%d') AS date
					FROM
						rha_record
					WHERE
						detail LIKE 'click-downfile-%'
					OR detail LIKE 'downfile-%'
					OR detail LIKE 'down-trainapp-%'
					GROUP BY
						date
					ORDER BY
						date DESC";
		$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";
		$sql .= " LIMIT $offset,$pagesize";
		$allrow = $this->Query($sql);
		$allnum = $this->FetchColOne($c_sql);
		return array('allrow'=>$allrow,'allnum'=>$allnum);
		
	}

	//每天每台服务器的应用下载量
	public function IpcAppDown($page,$pagesize,$ipcno){
			$offset = ($page-1)*$pagesize;
			$sql = "SELECT
						train,
						SUBSTRING_INDEX(detail, '-' ,- 1) as id,
						sum(model = 'app' or model='music' or model='movie' or model='index') AS appnum,
						sum(model = 'game') AS gamenum,
						
							FROM_UNIXTIME(curtime, '%Y-%m-%d')
						 AS date
					FROM
						rha_record LEFT JOIN rht_train.rht_apps AS app ON SUBSTRING_INDEX(detail, '-' ,- 1) = app.id
					WHERE
						 train = '".$ipcno."'
					AND (
						detail LIKE 'click-downfile-%'
						OR detail LIKE 'downfile-%'
						OR detail LIKE 'down-trainapp-%'
					)

					GROUP BY
						date
					ORDER BY
						date DESC";
		$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";
		$sql .= " LIMIT $offset,$pagesize";
		$allrow = $this->Query($sql);
		$allnum = $this->FetchColOne($c_sql);
		return array('allrow'=>$allrow,'allnum'=>$allnum);
		
	}
	
	//按服务器统计应用下载量
	public function DetailAppDown($date,$train,$id){
		$sql = "SELECT
					train,
					app.appname,
					SUM(model='app' or model='game' OR detail LIKE 'down-trainapp-%') AS downnum,
					SUBSTRING_INDEX(detail, '-' ,- 1) AS id,
					CONCAT(FROM_UNIXTIME(curtime, '%Y-%m-%d %H:00'),'-',FROM_UNIXTIME(curtime+3600, '%H:00')) AS date
				FROM
					rha_record
				LEFT JOIN rht_train.rht_apps AS app ON SUBSTRING_INDEX(detail, '-' ,- 1) = app.id
				WHERE
				train='".$train."'
				AND
					FROM_UNIXTIME(curtime,'%Y-%m-%d')='".$date."'
				AND(
					detail LIKE 'click-downfile-%'
				OR detail LIKE 'downfile-%'
				OR detail LIKE 'down-trainapp-%')
				GROUP BY
					app.appname
				ORDER BY
					downnum DESC";
		$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";
		$allrow = $this->Query($sql);
		$allnum = $this->FetchColOne($c_sql);
		return array('allrow'=>$allrow,'allnum'=>$allnum);
	}
	//获取总应用下载详情
	public function TotalAppDetail($date){
			$sql = "SELECT
						sum(model = 'app' or model='music' or model='movie' or model='index') AS appnum,
						sum(model = 'game') AS gamenum,
						CONCAT(FROM_UNIXTIME(curtime, '%Y-%m-%d %H:00'),'-',FROM_UNIXTIME(curtime+3600, '%H:00')) AS date
					FROM
						rha_record
					WHERE
					FROM_UNIXTIME(curtime, '%Y-%m-%d')='".$date."'
					AND(
						detail LIKE 'click-downfile-%'
					OR detail LIKE 'downfile-%'
					OR detail LIKE 'down-trainapp-%')
					GROUP BY
						date
					ORDER BY
						date DESC";
		$c_sql = "SELECT COUNT(*) as allnum FROM ($sql) as t";
		$allrow = $this->Query($sql);
		$allnum = $this->FetchColOne($c_sql);
		return array('allrow'=>$allrow,'allnum'=>$allnum);
	}
	/**
	 * 独立访客
	 * @param unknown $page
	 * @param unknown $pagesize
	 * @param unknown $ipcno
	 * @return string|multitype:string Ambigous <multitype:, boolean, number>
	 */
	public function UnVstor($page,$pagesize,$ipcno)
	{
		$offset = ($page-1)*$pagesize;
		
		$crtemp_tb = function ($model)use($offset,$pagesize,$ipcno){
			$where = $ipcno ? " AND train = '$ipcno' " : '';
			$temp_sql = "SELECT
						COUNT(DISTINCT ip) AS $model,
						FROM_UNIXTIME(curtime, '%Y-%m-%d') AS time
						FROM
							rha_record
						WHERE
							model = '$model' $where
						GROUP BY
							time
						ORDER BY time desc
						Limit $offset,$pagesize";
			return $temp_sql;
		};
		
		$sql = "SELECT t2.music,t3.app,t4.game,t1.* FROM
					(
						".$crtemp_tb('movie')."
					) AS t1,
					(
						".$crtemp_tb('music')."
					) AS t2,
					(
						".$crtemp_tb('app')."
					) AS t3,
					(
						".$crtemp_tb('game')."
					) AS t4
				WHERE
					t1.time = t2.time
				AND t1.time = t3.time
				AND t1.time = t4.time
				GROUP BY t1.time desc ";
		$c_sql = "SELECT COUNT(DISTINCT FROM_UNIXTIME(curtime,'%Y-%m-%d')) as allnum FROM  rha_record ";
		$allrow = $this->Query($sql);
		$allnum = $this->FetchColOne($c_sql);
		return array('allrow'=>$allrow,'allnum'=>$allnum);
	}
	/**
	 * 电影统计
	 * @param unknown $page
	 * @param unknown $pagesize
	 * @param unknown $ipcno
	 * @return multitype:unknown
	 */
	public function MovieCountList($page,$pagesize,$ipcno)
	{
		$offset = ($page-1)*$pagesize;
		$where = $ipcno ? " AND train = '$ipcno' " : '';
		$sql = "SELECT
				SUM(detail LIKE 'click-play-%') AS play,
				SUM(detail LIKE 'click-pause-%') AS pause,
				SUM(detail LIKE 'buffer-%') AS buffer,
				SUM(detail LIKE 'click-set-%') AS `set`,
				SUM(detail LIKE 'click-playbar%') AS `bar`,
				FROM_UNIXTIME(curtime, '%Y-%m-%d') AS time
			FROM
				rha_record
			WHERE
				model = 'movie'
			$where
			AND type = 4
			GROUP BY
				time
			ORDER BY
				time DESC
			Limit $offset,$pagesize";
		$c_sql = "SELECT
				COUNT(DISTINCT FROM_UNIXTIME(curtime,'%Y-%m-%d')) as allnum
			FROM
				rha_record
			WHERE
				model = 'movie'
			$where
			AND type = 4";
		$allrow = $this->Query($sql);
		$allnum = $this->FetchColOne($c_sql);
		return array('allrow'=>$allrow,'allnum'=>$allnum);
	}
	
	public function MovieDay($page,$pagesize,$ipcno,$time,$where = '')
	{
		$where .= $time ? " AND FROM_UNIXTIME(curtime, '%Y-%m-%d') = '$time'" : '';
		$where .= $ipcno ? " AND train = '$ipcno' " : '';
		$sql = "SELECT
				rht_train.rht_videos.vname,
				sum(detail LIKE 'click-play-%') AS play,
				sum(detail LIKE 'click-pause-%') AS pause
			FROM
				rha_record
			JOIN rht_train.rht_videos ON SUBSTRING_INDEX(detail, '-' ,- 1) = rht_train.rht_videos.id
			WHERE
				model = 'movie'
			AND type = 4
			$where
			GROUP BY
				rht_train.rht_videos.vname
			HAVING
				play > 0
			ORDER BY
				play DESC 
			Limit 100";
		$data = $this->Query($sql);
		return $data;
	}
	
}
	
