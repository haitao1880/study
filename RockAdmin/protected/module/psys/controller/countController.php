<?php
/**
 * Copyright(c) 2014
 * 日    期:2014年7月4日
 * 作　  者:Neil
 * E-mail  :neil@rockhippo.cn
 * 文 件 名:countController.php
 * 创建时间:
 * 字符编码:UTF-8
 * 脚本语言:PHP
 * 摘    要:统计数据
 */
class countController extends Psys_AbstractController
{
	private $weekarray=array("日","一","二","三","四","五","六");  
	
	public function __construct()
	{
		parent::__construct();
	}
	//电影统计
	public function videoinAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$ipcno = reqstr("ipcno",'');
		
		$ipclist = $this->gettrain();
		$obj = new Psys_CountModel();
		$data = $obj->MovieCountList($page, $pagesize, $ipcno);
		
		self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
		$this->smarty->assign('clist',$data['allrow']);
		$this->smarty->assign('ipcno',$ipclist['allrow']);
		$this->smarty->assign('counttitle','电影统计 ');
		$this->smarty->assign('trainno',$ipcno);
		$this->forward = 'movie';
	}
	
	
	public function showmovieAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$ipcno = reqstr("ipcno",'');
		$time = reqstr("time",'');
		$ipclist = $this->gettrain();
		$obj = new Psys_CountModel();
		$data = $obj->MovieDay($page, $pagesize, $ipcno,$time);
		return array('allrow'=>$data,'code'=>1);
	}
	
	public function countregAction(){
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$date = reqstr("time",'');
		$hours = reqstr("ishours",'');
		$ipcno = reqstr("ipcno",'');
		$obj = new Psys_CountModel();
		$ipclist = $this->gettrain();
		if ($ipcno) {
			$data = $obj->RegList($page,$pagesize,$hours,$date,$ipcno);
			self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
			$this->smarty->assign('clist',$data['allrow']);
			$ipc = '&ipcno='.$ipcno;
		
		}else{
			$data = $obj->RegallList($page,$pagesize);
			self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
			$this->smarty->assign('totallist',$data['allrow']);
			$ipc = '';
		}
		$this->smarty->assign('counttitle','注册统计');
		$this->smarty->assign('ipcno',$ipclist['allrow'] );
		$this->smarty->assign('ipc',$ipc );
		$this->forward = 'countreg';
	}
	//获取服务器编号
	protected function gettrain(){
		$obj = new Psys_CountRule();
		$obj->SetTable('rha_ipc');
		return $obj->GetList(array(),'ipcno','id asc',0,0);
	}
	//音乐统计
	public function countmusicAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$ipcno = reqstr("ipcno",'');
		$obj = new Psys_CountModel();
		$ipclist = $this->gettrain();
		if ($ipcno) {
			$data = $obj->CountMusic($page,$pagesize,$ipcno);
			self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
			$this->smarty->assign('clist',$data['allrow']);
			$ipc = '&ipcno='.$ipcno;
		}else{
			$data = $obj->TotalMusic($page,$pagesize);
			self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
			$this->smarty->assign('totallist',$data['allrow']);
			$ipc = '';
		}
		
		$this->smarty->assign('counttitle','音乐统计');
		$this->smarty->assign('ipc',$ipc);
		$this->smarty->assign('ipcno',$ipclist['allrow']);
		$this->forward = 'countmusic';
	}
	
	public function newsinAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$obj = new Psys_CountModel();
		$data = $obj->CountList('news');
		
		self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
		$this->smarty->assign('clist',$data['allrow']);
		$this->smarty->assign('counttitle','新闻统计');
		$this->forward = 'index';
	}
	//应用统计详情
	public function detailappdownAction(){
		$date = reqstr('time','');
		$train = reqstr('ipcno','');
		$id = reqstr('id','');
		if (!$train || !$date) {
			return;
		}
		$obj = new Psys_CountModel();
		$data = $obj->DetailAppDown($date,$train,$id);
		return $data;
	}
	//应用统计
	public function appinAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$ipcno = reqstr("ipcno",'');
		$ipclist = $this->gettrain();
		$obj = new Psys_CountModel();
		if ($ipcno) {
			//按服务器获取统计

			$data = $obj->IpcAppDown($page,$pagesize,$ipcno);
			self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
			$this->smarty->assign('clist',$data['allrow']);
			$ipc = '&ipcno='.$ipcno;

		
		}else{
			//获取全部统计
			$data = $obj->AllAppDowns($page,$pagesize);
			self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
			$this->smarty->assign('totallist',$data['allrow']);
			$ipc = '';
		}
		$this->smarty->assign('ipc',$ipc);
		$this->smarty->assign('ipcno',$ipclist['allrow']);
		$this->smarty->assign('counttitle','应用统计');
		$this->forward = 'countapp';
	}
	
	public function gameinAction()
	{
		$this->smarty->assign('counttitle','游戏统计');
		$this->forward = 'index';
	}
	
	public function detailAction()
	{
		$model = reqstr('model','');
		$time = reqstr('time','');
		
		$obj = new Psys_CountModel();
		return array('code'=>1,'result'=>$obj->DetailList($model, $time));
	}

	public function regdetailAction()
	{
		$hours = reqstr("ishours",'');
		$date = reqstr("time",'');
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$ipcno = reqstr("ipcno",'');
		$obj = new Psys_CountModel();
		$data = $obj->RegList($page,$pagesize,$hours,$date,$ipcno);
		// foreach ($data['allrow'] as $key => &$value) {
		// 	$value['day'] = substr($value['day'], 10).' 点';
		// }
		return $data;
	}

	public function totalregdetailAction()
	{
		$hours = reqstr("ishours",'');
		$date = reqstr("time",'');
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$ipcno = reqstr("ipcno",'');
		$obj = new Psys_CountModel();
		$data = $obj->TotalRegList($page,$pagesize,$hours,$date,$ipcno);
		// foreach ($data['allrow'] as $key => &$value) {
		// 	$value['day'] = substr($value['day'], 10).' 点';
		// }
		return $data;
	}

	//每节车厢登录信息
	public function logindetailAction()
	{
		$hours = reqstr("ishours",'');
		$date = reqstr("time",'');
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$ipcno = reqstr("ipcno",'');
		$obj = new Psys_CountModel();
		$data = $obj->LoginDetail($page,$pagesize,$hours,$date,$ipcno);
		
		return $data;
	}
	//每日登录信息
	public function totallogindetailAction()
	{
		$hours = reqstr("ishours",'');
		$date = reqstr("time",'');
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$ipcno = reqstr("ipcno",'');
		$obj = new Psys_CountModel();
		$data = $obj->TotalLoginDetail($page,$pagesize,$hours,$date,$ipcno);
		
		return $data;
	}
	/**
	 * 分页数据显示
	 * @param num $allcount 总条数
	 * @param num $page
	 * @param num $pagesize
	 * @param num $cursize  当前页的数据条数
	 */
	private function inidate($allcount,$page,$pagesize,$cursize){
		$this->smarty->assign('allcount',$allcount);   //总数
		$allpage = ceil($allcount/$pagesize);
		$this->smarty->assign('allpage',$allpage);  //总页数
		$pagesize = ($pagesize%2)?$pagesize:$pagesize+1; //页码取偶数 = 步长
		$pageoffset =($pagesize-1)/2; //当前页 左右偏移
		$this->smarty->assign('cur_page',$page); //当前页
		if($allcount>$pagesize)
		{
			//如果当前页小于等于左偏移
			if($page<=$pageoffset)
			{
				$startNum=1;
				$endNum = $pagesize;
			}
			else
			{//如果当前页大于左偏移
				//如果当前页码右偏移超出最大分页数
				if($page+$pageoffset>=$allcount+1)
				{
					$startNum = $allcount-$pagesize+1;
				}
				else
				{
					//左右偏移都存在时的计算
					$startNum = $page-$pageoffset;
					$endNum = $page+$pageoffset;
				}
			}
		}
		else
		{
			$startNum=1;
			$endNum = $allcount;
		}
	
		$this->smarty->assign('startNum',$startNum);  //当前从几开始
		$this->smarty->assign('endNum',$endNum);  //当前到几结束
		$this->smarty->assign('sli',(($pagesize*($page-1)+1)));  //当前页的起始第多少条
		$this->smarty->assign('eli',($pagesize*($page-1)+$cursize));  //当前页最后一条是第多少条
	}

	//音乐点击量排行
	public function musichitsAction()
	{		
		$date = reqstr("time",'');
		$ipcno = reqstr("ipcno",'');
		if (!$date || !$ipcno ) {
			return;
		}

		$obj = new Psys_CountModel();
					
		$res = $obj->CountMusicPlay($date,$ipcno);
		// foreach ($data['allrow'] as $key => &$value) {
		// 	$value['day'] = substr($value['day'], 10).' 点';
		// }
		return $res;	
				
	}
	//全天音乐点击量排行
	public function totalmusichitsAction()
	{		
		$date = reqstr("time",'');
		if (!$date) {
			return;
		}

		$obj = new Psys_CountModel();
					
		$res = $obj->TotalCountMusicPlay($date);
		return $res;	
				
	}
	//每台服务器每日榜单点击排行
	public function albumhitsAction(){
		$date = reqstr("time",'');
		$ipcno = reqstr("ipcno",'');
		if (!$date || !$ipcno ) {
			return;
		}
		$obj = new Psys_CountModel();

		$res= $obj->CountAlbumHits($date,$ipcno);
		return $res;	
		
	}
	//每日榜单点击排行
	public function totalalbumhitsAction(){
		$date = reqstr("time",'');
		if (!$date) {
			return;
		}
		$obj = new Psys_CountModel();

		$res= $obj->TotalCountAlbumHits($date);
		return $res;	
		
	}
	//首页模块点击统计
	public function modelhitsAction(){
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$ipcno = reqstr("ipcno",'');
		$ipclist = $this->gettrain();
		$obj = new Psys_CountModel();
		if ($ipcno) {
			$data = $obj->NavHits($page,$pagesize,$ipcno);
			self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
			$this->smarty->assign('clist',$data['allrow']);
			$ipc = '&ipcno='.$ipcno;
		
		}else{
			$data = $obj->TotalNavHits($page,$pagesize);
			self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
			$this->smarty->assign('totallist',$data['allrow']);
			$ipc = '';
		}
		
		$this->smarty->assign('ipcno',$ipclist['allrow']);
		$this->smarty->assign('ipc',$ipc);
		$this->smarty->assign('counttitle','首页模块点击次数统计 ');
		$this->forward = 'modelhits';
	}

	//按日期统计点击次数
	public function navhitnoAction(){
		$ipcno = reqstr("ipcno",'');
		$time = reqstr("time",'');
		if (!$ipcno || !$time) {
			return;
		}
		$obj = new Psys_CountModel();
		return $obj->navhitno($time,$ipcno);
	}
	//按日期统计点击次数详情
	public function totalnavhitAction(){
		$time = reqstr("time",'');
		if (!$time) {
			return;
		}
		$obj = new Psys_CountModel();
		return $obj->TotalNavHit($time);
	}
	//页面点击
	public function clickAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$ipcno = reqstr("ipcno",'');
		$time = reqstr("time",'');
		
		$ipclist = $this->gettrain();
		$obj = new Psys_CountModel();
		$data = $obj->PageHits($page,$pagesize,$ipcno,$time);
		
		if ($time) {
			return $data;
		}
		
		
		self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
		$this->smarty->assign('clist',$data['allrow']);
		$this->smarty->assign('ipcno',$ipclist['allrow']);
		$this->smarty->assign('counttitle','页面点击统计 ');
		$this->smarty->assign('trainno',$ipcno);
		$this->forward = 'click';
	}
	
	//独立访客
	public function uvisitorAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$ipcno = reqstr("ipcno",'');

		$ipclist = $this->gettrain();
		$obj = new Psys_CountModel();
		$data = $obj->UnVstor($page, $pagesize, $ipcno);
		
		self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
		$this->smarty->assign('clist',$data['allrow']);
		$this->smarty->assign('ipcno',$ipclist['allrow']);
		$this->smarty->assign('counttitle','独立访客统计 ');
		$this->smarty->assign('trainno',$ipcno);
		$this->forward = 'uvisitor';
	}

	//获取总应用下载详情
	public function totalappdetailAction(){
		$date = reqstr('time','');

		if (!$date) {
			return;
		}
		$obj = new Psys_CountModel();
		return $obj->TotalAppDetail($date);
	}
}