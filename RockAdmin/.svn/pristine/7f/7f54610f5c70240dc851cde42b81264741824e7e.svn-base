<?php
class Psys_FinModel extends Psys_AbstractModel
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * 拉取订单列表
	 * @param number $page
	 * @param number $pagesize
	 */
	public function GetOrderList()
	{	
		global $G_X;
		$food = $G_X['order_type']['food_type'][0];
		$video = $G_X['order_type']['video_type'][0];
		$music = $G_X['order_type']['music_type'][0];
		$game = $G_X['order_type']['game_type'][0];
		$app = $G_X['order_type']['app_type'][0];
	
		$m = new Psys_FinRule();
	
	
		$data = $m->GetOrderList($food,$video,$music,$game,$app);
		$list = array();
		foreach ($data['foodslist'] as $k=>&$v){
			$v['ctime'] = date('Y-m-d H:i:s',$v['ctime']);
			$v['amount'] = $v['price'] * $v['pnum'];
			$v['type'] = $G_X['order_type']['food_type'][1];
		}
		foreach ($data['videoslist'] as $k=>&$v){
			$v['ctime'] = date('Y-m-d H:i:s',$v['ctime']);
			$v['amount'] = $v['price'] * $v['pnum'];
			$v['type'] = $G_X['order_type']['video_type'][1];
		}
		foreach ($data['musicslist'] as $k=>&$v){
			$v['ctime'] = date('Y-m-d H:i:s',$v['ctime']);
			$v['amount'] = $v['price'] * $v['pnum'];
			$v['type'] = $G_X['order_type']['music_type'][1];
		}
		foreach ($data['gameslist'] as $k=>&$v){
			$v['ctime'] = date('Y-m-d H:i:s',$v['ctime']);
			$v['amount'] = $v['price'] * $v['pnum'];
			$v['type'] = $G_X['order_type']['game_type'][1];
		}
		foreach ($data['appslist'] as $k=>&$v){
			$v['ctime'] = date('Y-m-d H:i:s',$v['ctime']);
			$v['amount'] = $v['price'] * $v['pnum'];
			$v['type'] = $G_X['order_type']['app_type'][1];
		}
		$list = array_merge($data['foodslist'],$data['videoslist'],$data['musicslist'],$data['gameslist'],$data['appslist']);
	
		// var_dump($list);
		// exit;
		return $list;
	}
	
/**
	 * 交易明细查询
	 */
	public function Tralist($producttype,$ifsucc,$order,$page,$pagesize)
	{
		if(!$producttype)
		{
			$producttype = '';
		}
		else 
		{
			$producttype = ' producttype = \'' . $producttype . '\'';
		}
		if($ifsucc == -1)
		{
			$ifsucc = '';
		}
		else if($producttype) 
		{
			$ifsucc = ' and ifsucc = ' . $ifsucc;
		}
		else 
		{
			$ifsucc = ' ifsucc = ' . $ifsucc;
		}
		if(!$order)
		{
			$order = ' ORDER BY ctime DESC,id DESC';
		}
		else
		{
			$order = ' ORDER BY ctime ASC,id ASC';
		}
		
		
		$rule = new Psys_FinRule();
		$list = $rule->Tralist($producttype,$ifsucc,$order,$page,$pagesize);
		return $list;
		
	}
	
	/**
	 * 查询列表
	 */
	public function inquiryList($orderguid,$username,$producttype,$ifsucc,$order,$startTime,$endTime,$page,$pagesize)
	{
		if(!$orderguid)
		{
			$orderquid = '';
		}
		else
		{
			$orderguid = ' orderguid = \'' . $orderguid . '\'';
		}
		if(!$username)
		{
			$username = '';
		}
		else if($orderguid)
		{
			$username = ' AND username = \'' . $username .'\'';
		}
		else 
		{
			$username = ' username = \'' . $username .'\'';
		}
		if(!$producttype)
		{
			$producttype = '';
		}
		else if($orderguid || $username)
		{
			$producttype = ' AND producttype = \'' . $producttype . '\'';
		}
		else 
		{
			$producttype = ' producttype = \'' . $producttype . '\'';
		}
		if($ifsucc != -1)
		{
			if($orderguid || $username || $producttype)
			{
				$ifsucc = ' AND ifsucc = \'' . $ifsucc . '\'';
			}
			else
			{
				$ifsucc = ' ifsucc = \'' . $ifsucc . '\'';
			}
		}
		else 
		{
			$ifsucc = '';
		}
		if($startTime)
		{
			if($orderguid || $username || $producttype || $ifsucc)
			{
				$startTime = ' AND ctime > \'' . $startTime . '\'' . ' AND ctime < \'' . $endTime . '\'';
			}
			else
			{
				$startTime = ' ctime > \'' . $startTime . '\'' . ' AND ctime < \'' . $endTime . '\'';
			}
		}
		else 
		{
			$startTime = '';
		}
		if(!$order)
		{
			$order = ' ORDER BY ctime DESC,id DESC';
		}
		else
		{
			$order = ' ORDER BY ctime ASC,id ASC';
		}
		
		
		$rule = new Psys_FinRule();
		$list = $rule->inquiryList($orderguid,$username,$producttype,$ifsucc,$order,$startTime,$page,$pagesize);
		return $list;
		
	}
	
	
	/**
	 * 
	 * 日期交易
	 */
	public function dateList($day,$order,$ifsucc,$page,$pagesize)
	{
		//echo '当前日期：' . date('Y-m-d');
		//$nextday = date('Y-m-d',mktime(0,0,0,date('m',$day),date('d',$day)+1,date('Y',$day)));
		$nextday = $day+(24*60*60);
		if($day&&$nextday)
		{
			$date = ' ctime > \'' . $day . '\' AND ctime < \'' . $nextday . '\'';
		}
		if($order)
		{
			$order = ' ORDER BY ctime ASC';
		}
		else
		{
			$order = ' ORDER BY ctime DESC';
		}
		if($ifsucc != -1)
		{
			$ifsucc = ' AND ifsucc = ' . $ifsucc;
		}
		else 
		{
			$ifsucc = '';
		}
		$rule = new Psys_FinRule();
		$list = $rule->dateList($date,$order,$ifsucc,$page,$pagesize);
		return $list;
		
		
		
	}
	
	
	
	/**
	 * 
	 * 获取数据库日期范围
	 */
	public function dateRange($year,$month,$day)
	{
		//var_dump($year);exit;
		$rule = new Psys_FinRule();
		//如果$day为空，$month不为空，则为获取当前月在数据库中的天数
		if($day == '' && $month !== '')
		{
			$type = 1;
			$startmonth = strtotime($year . '-' . $month);
			$endmonth = strtotime($year . '-' . ((int)$month+1));
			$list = $rule->getdatelist($startmonth,$endmonth,$type);
		}
		else if($month == '' && $year !== '')
		{
			$type = 2;
			//echo $type;exit;
			$startyear = strtotime($year . '-1-1');
			//echo date('Y-m-d',1388505600);exit;
			$endyear = strtotime(($year+1) . '-1-1');
			//echo date('Y-m-d',1420041600);exit;
			$list = $rule->getdatelist($startyear,$endyear,$type);
			
		}
		else if($year == '') 
		{
			$type = 3;
			$start = '';
			$end = '';
			$list = $rule->getdatelist($start,$end,$type);
		}
		//var_dump($list);exit;
		return $list;
	}
	
	
	
	/**
	 * 获取第三方订单列表
	 * @param $pid:第三方应用ID
	 */
	public function GetThreeOrderList($pid,$ifsucc = false,$stime='',$totime=''){
		$m = new Psys_FinRule();
		$data = $m->GetThreeOrderList($pid,$ifsucc,$stime,$totime);
		return $data;
	}
}