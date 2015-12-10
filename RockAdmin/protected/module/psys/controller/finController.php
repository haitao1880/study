<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月29日                                                 
* 作　  者:Neil
* E-mail  :neil@rockhippo.cn
* 文 件 名:finController.php                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 摘    要: 财务管理                                                      
*/

class finController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function orderlistAction()
	{	
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$offset = ($page-1)*$pagesize;

		$obj = new Psys_FinModel();
		
		$data = $obj -> GetOrderList();
		$total = count($data);
		$curlist = array_slice($data, $offset,$pagesize);
		$cursize = count($curlist);
		self::inidate($total,$page,$pagesize,$cursize);
		
		$this->smarty->assign('olist',$curlist);
		$this->forward = 'orderlist';
	}
	
	
	
	/**
	 * 交易明细列表
	 */
	public function trasictionAction()
	{
		global $G_X;
		$page = reqnum('page',1);
		$pagesize = reqnum('pagesize',10);
		//$orderguid = reqstr('orderguid','');		//订单号查询
		//$username = reqstr('username','');			//按用户名查询
		$producttyp = reqnum('producttype',0);		//按产品类型查询
		$order = reqnum('order',0);					//按排序查询
		$ifsucc = reqnum('ifsucc',-1);				//按交易成功与否查询

		$url = '';
		
		if ($producttyp) {
			$url .= '&producttype='.$producttyp;
		}
		
		if ($order) {
			$url .= '&order='.$order;
		}
		
		if ($ifsucc != -1)
		{
			$url .= '&ifsucc=' . $ifsucc;
		}
		
		$model = new Psys_FinModel();
		$list = $model->Tralist($producttyp,$ifsucc,$order,$page,$pagesize);
		//var_dump($list);exit;
		//资源列表
		$resModel = new Psys_ResModel();
		
		foreach ($list['allrow'] as &$value)
		{
			$value['ctime'] = date('Y-m-d H:i:s',$value['ctime']);	//下单时间格式转换
			$value['cip'] = long2ip($value['cip']);					//下单地址格式转换
			if($value['utime'] != '' || $value['uip'] != '')
			{
				$value['utime'] = date('Y-m-d H:i:s',$value['utime']);
				$value['uip'] = long2ip($value['uip']);
			}
			
			switch($value['producttype'])
			{
				case 1:							//视频
					$value['producttype'] = $G_X['order_type']['video_type'][1];
					$where = array('id'=>$value['productid']);
					$result = $resModel->GetOneVideo($where);
					$value['productid'] = $result['vname'];
					break;
				case 2:							//音乐
					$value['producttype'] = $G_X['order_type']['music_type'][1];
					$where = array('id'=>$value['productid']);
					$result = $resModel->GetOne($where,'mname','rhi_music');
					$value['productid'] = $result['mname'];
					break;
				case 3:							//游戏
					$value['producttype'] = $G_X['order_type']['game_type'][1];
					$where = array('id'=>$value['productid']);
					$result = $resModel->GetOneGame($where);
					$value['productid'] = $result['appname'];
					break;
				case 4:							//应用
					$value['producttype'] = $G_X['order_type']['app_type'][1];
					$where = array('id'=>$value['productid']);
					$result = $resModel->GetOne($where,'appname','rhi_apps');
					$value['productid'] = $result['appname'];
					break;
				case 10:						//美食
					$value['producttype'] = $G_X['order_type']['food_type'][1];
					$where = array('id'=>$value['productid']);
					$result = $resModel->GetOne($where,'fname','rhi_foodish');
					$value['productid'] = $result['fname'];
					break;
					
			}
			//exit;
			/*
			foreach ($G_X['order_type'] as $type)
			{
				if(in_array($value['producttype'],$type))
				{
					$value['producttype'] = $type[1];
				}
			}
			*/
		}
		
		self::inidate($list['allnum'],$page,$pagesize,count($list['allrow']));
		//$this->smarty->assign('orderguid',$orderguid);
		//$this->smarty->assign('username',$username);
		$this->smarty->assign('producttyp',$producttyp);
		$this->smarty->assign('order',$order);
		$this->smarty->assign('ifsucc',$ifsucc);
		//$this->smarty->assign('startTime',$startTime);
		//$this->smarty->assign('endTime',$endTime);
		$this->smarty->assign('producttype',$G_X['order_type']);
		$this->smarty->assign('url',$url);
		$this->smarty->assign('list',$list['allrow']);
		$this->forward = 'trasiction';
	}
	
	/**
	 * 交易查询
	 */
	public function tradeInquiryAction()
	{
		global $G_X;
		$this->smarty->assign('producttype',$G_X['order_type']);
		$this->forward = 'tradeInquiry';
	}
	
	/**
	 * 交易查询列表
	 */
	public function inquiryListAction()
	{
		global $G_X;
		$page = reqnum('page',1);
		$pagesize = reqnum('pagesize',10);
		$orderguid = reqstr('orderguid','');
		$username = reqstr('username','');
		$producttype = reqnum('producttype','');
		$order = reqnum('order',0);	//默认为倒序排序
		$ifsucc = reqnum('ifsucc',-1);	//默认为全部
		$startTime = reqstr('startTime','');
		$endTime = reqstr('endTime',date('Y-m-d H:i:s'));
		
		//判断起始时间是否有值
		if($startTime !== '')
		{
			$startTime = strtotime($startTime);
			$endTime = strtotime($endTime);
		}
		
		$url = '';
		//如果订单号不为空，重组URL
		if ($orderguid)
		{
			$url .= '&orderguid=' . $orderguid;
		}
		//如果用户名不为空，重组URL
		if ($username) {
			$url .= '&username='.$username;
		}
		//如果产品类型不为空，重组URL
		if ($producttype) {
			$url .= '&producttype='.$producttype;
		}
		//如果排序不为空，重组URL
		if ($order) {
			$url .= '&order='.$order;
		}
		//如果起始时间不为空，重组URL
		if ($startTime)
		{
			$url .= '&startTime=' . date('Y-m-d H:i:s',$startTime) . '&endTime=' . date('Y-m-d H:i:s',$endTime);
		}
		//如果状态信息不为空，则查询全部
		if ($ifsucc != -1)
		{
			$url .= '&ifsucc=' . $ifsucc;
		}
		
		$model = new Psys_FinModel();
		$list = $model->inquiryList($orderguid,$username,$producttype,$ifsucc,$order,$startTime,$endTime,$page,$pagesize);
		//var_dump($list);exit;
		//资源列表
		$resModel = new Psys_ResModel();
		
		foreach ($list['allrow'] as &$value)
		{
			$value['ctime'] = date('Y-m-d H:i:s',$value['ctime']);	//下单时间格式转换
			$value['cip'] = long2ip($value['cip']);					//下单地址格式转换
			if($value['utime'] != '' || $value['uip'] != '')
			{
				$value['utime'] = date('Y-m-d H:i:s',$value['utime']);
				$value['uip'] = long2ip($value['uip']);
			}
			
			switch($value['producttype'])
			{
				case 1:							//视频
					$value['producttype'] = $G_X['order_type']['video_type'][1];
					$where = array('id'=>$value['productid']);
					$result = $resModel->GetOneVideo($where);
					$value['productid'] = $result['vname'];
					break;
				case 2:							//音乐
					$value['producttype'] = $G_X['order_type']['music_type'][1];
					$where = array('id'=>$value['productid']);
					$result = $resModel->GetOne($where,'mname','rhi_music');
					$value['productid'] = $result['mname'];
					break;
				case 3:							//游戏
					$value['producttype'] = $G_X['order_type']['game_type'][1];
					$where = array('id'=>$value['productid']);
					$result = $resModel->GetOneGame($where);
					$value['productid'] = $result['appname'];
					break;
				case 4:							//应用
					$value['producttype'] = $G_X['order_type']['app_type'][1];
					$where = array('id'=>$value['productid']);
					$result = $resModel->GetOne($where,'appname','rhi_apps');
					$value['productid'] = $result['appname'];
					break;
				case 10:						//美食
					$value['producttype'] = $G_X['order_type']['food_type'][1];
					$where = array('id'=>$value['productid']);
					$result = $resModel->GetOne($where,'fname','rhi_foodish');
					$value['productid'] = $result['fname'];
					break;
					
			}
			
		}
		self::inidate($list['allnum'],$page,$pagesize,count($list['allrow']));
			//$this->smarty->assign('orderguid',$orderguid);
			//$this->smarty->assign('username',$username);
			//$this->smarty->assign('producttyp',$producttyp);
			//$this->smarty->assign('order',$order);
			//$this->smarty->assign('ifsucc',$ifsucc);
			//$this->smarty->assign('startTime',$startTime);
			//$this->smarty->assign('endTime',$endTime);
			//$this->smarty->assign('producttype',$G_X['order_type']);
		$this->smarty->assign('url',$url);
		$this->smarty->assign('list',$list['allrow']);
		$this->forward = 'inquiryList';
	
	}
	
	/**
	 * 日期交易
	 */
	public function dateListAction()
	{
		//var_dump($_POST);exit;
		global $G_X;
		$page = reqnum('page',1);
		$pagesize = reqnum('pagesize',10);
		$order = reqnum('order',0);
		$ifsucc = reqnum('ifsucc',-1);
		//获取用户输入
		$year = reqnum('year',0);
		$month = reqnum('month',0);
		$day = reqnum('day',0);		
		
		if(!$year || !$month || !$day)
		{
			$date = '';	
		}
		else 
		{
			$date = $year . '-' . $month . '-' . $day;
		}
		if($date == '')
		{
			$date = date('Y-m-d');
		}
		$date = strtotime($date);
		//url拼接
		$url = '';
		$url .= '&date=' . $date;
		$url .= '&order=' . $order;
		$url .= '&ifsucc=' . $ifsucc;
		
		$model = new Psys_FinModel();
		$list = $model->dateList($date,$order,$ifsucc,$page,$pagesize);		
		//资源列表
		$resModel = new Psys_ResModel();
		
		foreach ($list['allrow'] as &$value)
		{
			$value['ctime'] = date('Y-m-d H:i:s',$value['ctime']);	//下单时间格式转换
			$value['cip'] = long2ip($value['cip']);					//下单地址格式转换
			if($value['utime'] != '' || $value['uip'] != '')
			{
				$value['utime'] = date('Y-m-d H:i:s',$value['utime']);
				$value['uip'] = long2ip($value['uip']);
			}
			
			switch($value['producttype'])
			{
				case 1:							//视频
					$value['producttype'] = $G_X['order_type']['video_type'][1];
					$where = array('id'=>$value['productid']);
					$result = $resModel->GetOneVideo($where);
					$value['productid'] = $result['vname'];
					break;
				case 2:							//音乐
					$value['producttype'] = $G_X['order_type']['music_type'][1];
					$where = array('id'=>$value['productid']);
					$result = $resModel->GetOne($where,'mname','rhi_music');
					$value['productid'] = $result['mname'];
					break;
				case 3:							//游戏
					$value['producttype'] = $G_X['order_type']['game_type'][1];
					$where = array('id'=>$value['productid']);
					$result = $resModel->GetOneGame($where);
					$value['productid'] = $result['appname'];
					break;
				case 4:							//应用
					$value['producttype'] = $G_X['order_type']['app_type'][1];
					$where = array('id'=>$value['productid']);
					$result = $resModel->GetOne($where,'appname','rhi_apps');
					$value['productid'] = $result['appname'];
					break;
				case 10:						//美食
					$value['producttype'] = $G_X['order_type']['food_type'][1];
					$where = array('id'=>$value['productid']);
					$result = $resModel->GetOne($where,'fname','rhi_foodish');
					$value['productid'] = $result['fname'];
					break;
					
			}
			
		}
		
		self::inidate($list['allnum'],$page,$pagesize,count($list['allrow']));
		$this->smarty->assign('year',$year);
		$this->smarty->assign('month',$month);
		$this->smarty->assign('day',$day);
		$this->smarty->assign('order',$order);
		$this->smarty->assign('ifsucc',$ifsucc);
		
		
		$this->smarty->assign('url',$url);
		$this->smarty->assign('allnum',$list['allnum']);
		$this->smarty->assign('list',$list['allrow']);		
		$this->forward = 'dateList';
	}
	
	
	//日期下拉列表框
	public function checkboxAction()
	{
		//return 123;
		$year = reqnum('year','');
		$month = reqnum('month','');
		$day = reqnum('day','');
		
		$model = new Psys_FinModel();		
		$dateArray = $model->dateRange($year,$month,$day);
		//var_dump($dateArray);exit;
		return $dateArray;
		
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
}

?>