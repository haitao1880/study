<?php
class Psys_CountModel extends Psys_AbstractModel
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function CountList($model,$page=1,$pagesize=10)
	{
		return $this->DbRule->CountList($model,$page,$pagesize);
	}
	
	public function DetailList($model,$time)
	{
		return $this->DbRule->DetailList($model,$time,$hours);
	}
	public function RegList($page,$pagesize,$hours,$date,$ipcno)
	{
		return $this->DbRule->RegList($page,$pagesize,$hours,$date,$ipcno);
	}
	//统计当天所有的注册信息
	public function TotalRegList($page,$pagesize,$hours,$date,$ipcno)
	{
		return $this->DbRule->TotalRegList($page,$pagesize,$hours,$date,$ipcno);
	}
	//获取所有的注册统计
	public function RegallList($page,$pagesize)
	{
		return $this->DbRule->RegallList($page,$pagesize);
	}

	//按车厢获取音乐统计
	public function CountMusic($page,$pagesize,$ipcno){
		return $this->DbRule->CountMusic($page,$pagesize,$ipcno);
	}
	//获取音乐统计
	public function TotalMusic($page,$pagesize){
		return $this->DbRule->TotalMusic($page,$pagesize);
	}
	//歌曲点击统计
	public function CountMusicPlay($date,$ipcno){
		$list = $this->DbRule->CountMusicPlay($date,$ipcno);
		if (!empty($list['allrow'])) {
			$list['code'] = 1;
		}
		return $list;
	}
	//每日歌曲点击统计
	public function TotalCountMusicPlay($date){
		$list = $this->DbRule->TotalCountMusicPlay($date);
		if (!empty($list['allrow'])) {
			$list['code'] = 1;
		}
		return $list;
	}
	//获取歌曲详情
	public function GetMusicInfo($musicid){
		return $this->DbRule->GetMusicInfo($musicid);
	}

	//每台服务器每日榜单点击排行
	public function CountAlbumHits($date,$ipcno){

		$list = $this->DbRule->CountAlbumHits($date,$ipcno);
		if (!empty($list['allrow'])) {
			$list['code'] = 1;
		}
		return $list;

	}
	//榜单点击排行
	public function TotalCountAlbumHits($date){

		$list = $this->DbRule->TotalCountAlbumHits($date);
		if (!empty($list['allrow'])) {
			$list['code'] = 1;
		}
		return $list;

	}
	//获取榜单详情
	public function GetAlbumInfo($musicid){
		return $this->DbRule->GetAlbumInfo($musicid);
	}

	public function LoginDetail($page,$pagesize,$hours,$date,$ipcno){
		$data = $this->DbRule->LoginDetail($page,$pagesize,$hours,$date,$ipcno);
		if (!empty($data)) {
			$data['code'] =1;
		}
		return $data;
	}
	//每日登录信息
	public function TotalLoginDetail($page,$pagesize,$hours,$date,$ipcno){
		$data = $this->DbRule->TotalLoginDetail($page,$pagesize,$hours,$date,$ipcno);
		if (!empty($data)) {
			$data['code'] =1;
		}
		return $data;
	}
	//按车厢统计首页板块点击次数;
	public function NavHits($page,$pagesize,$ipcno){
		return $this->DbRule->NavHits($page,$pagesize,$ipcno);
	}
	//按日期统计点击次数
	public function navhitno($time,$ipcno){
		$res = $this->DbRule->navhitno($time,$ipcno);
		if (!empty($res['allrow'])) {
			$res['code'] = 1;
		}
		return $res;
	}
	//按日期统计点击次数详情
	public function TotalNavHit($time){
		$res = $this->DbRule->TotalNavHit($time);
		if (!empty($res['allrow'])) {
			$res['code'] = 1;
		}
		return $res;
	}
	//统计首页板块点击次数;
	public function TotalNavHits($page,$pagesize){
		return $this->DbRule->TotalNavHits($page,$pagesize);
	}
	//每天总的应用下载量
	public function AllAppDowns($page,$pagesize){
		return $this->DbRule->AllAppDowns($page,$pagesize);
	}
	//每台服务器每天的应用下载量
	public function IpcAppDown($page,$pagesize,$ipcno){
		$res = $this->DbRule->IpcAppDown($page,$pagesize,$ipcno);
		
		return $res;
	}
	//按服务器统计应用下载量
	public function DetailAppDown($date,$train,$id){
		$res = $this->DbRule->DetailAppDown($date,$train,$id);
		if (!empty($res['allrow'])) {
			$res['code'] = 1;
			foreach ($res['allrow'] as $k=>&$v) {
				if (!is_numeric($v['id'])) {
					$v['appname'] = '摇滚河马平台';
				}
			}
		}
		return $res;
	}
	//页面点击
	public function PageHits($page,$pagesize,$ipcno,$time)
	{
		return $this->DbRule->PageHits($page,$pagesize,$ipcno,$time);
	}
	//获取总应用下载详情
	public function TotalAppDetail($date){
		$res = $this->DbRule->TotalAppDetail($date);
		if (!empty($res['allrow'])) {
			$res['code'] = 1;
		}
		return $res;
	}	
	
	//独立访客 uv
	public function UnVstor($page,$pagesize,$ipcno)
	{
		return $this->DbRule->UnVstor($page,$pagesize,$ipcno);
	}
	//电影统计
	public function MovieCountList($page,$pagesize,$ipcno)
	{
		return $this->DbRule->MovieCountList($page,$pagesize,$ipcno);
	}
	//电影统计(日)
	public function MovieDay($page,$pagesize,$ipcno,$time)
	{
		return $this->DbRule->MovieDay($page,$pagesize,$ipcno,$time);
	}
}