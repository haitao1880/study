<?php
/**
 * Copyright(c) 2014
 * 日    期:2014年7月29日
 * 作　  者:Daniel
 * E-mail  :Daniel@rockhippo.cn
 * 文 件 名:transactionController.php
 * 字符编码:UTF-8
 * 脚本语言:PHP
 * 摘    要: 游戏充值流水
 */

class transactionController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
		
	}
	/*
	 $productArr=array(
		    '10001' => '飞翔的小鸟',
			'10002' => '风云天下OL',
			'10003' => '功夫熊猫3',
			'10004' => '炸弹糖',
			'10005' => '比武招亲',
			'10006' => '天天向前冲',
			'10007' => '呆鸟也疯狂',
			'10008' => '大富豪捕鱼',
			'10009' => '地铁跑酷罗马版',
			'10010' => '疯狂喷气机',
			'10011' => '中国娃娃餐厅',
			'10012' => '小鸟爆破',
			'10013' => '泡泡大作战',
			'10014' => '索尼克冲刺',
			'10015' => '水果忍者',
			'10016' => '神庙逃亡2',
			'10017' => '神庙逃亡',
			'10018' => '三重镇',
			'10019' => '疾风魔女',
			'10020' => '二战雄鹰',
			'10021' => '天天爱捕鱼',
			'10022' => '挖宝精英',
			'10023' => '果宝三国',
			'10032' => '秦时明月'
		);
	 * */
	/**
	 * 飞翔小鸟
	 */
	public function threeBirdAction()
	{
		$productid = 10001;
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$this->displayThree($productid,$page,$pagesize);
	}
	/**
	 * 风云天下
	 */
	public function theworldAction()
	{
		$productid = 10002;
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$this->displayThree($productid,$page,$pagesize);
	}
	/**
	 * 功夫熊猫
	 */
	public function pandaAction()
	{
		$productid = 10003;
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$this->displayThree($productid,$page,$pagesize);
	}
	/**
	 * 炸弹糖
	 */
	public function bombAction()
	{
		$productid = 10004;
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$this->displayThree($productid,$page,$pagesize);
	}
	/**
	 * 比武招亲
	 */
	public function contestAction()
	{
		$productid = 10005;
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$this->displayThree($productid,$page,$pagesize);
	}
	/**
	 * 天天向前冲
	 */
	public function forwardAction()
	{
		$productid = 10006;
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",10);
		$this->displayThree($productid,$page,$pagesize);
	}
	/**
	 *显示第三方数据 
	 * @param $productid 第三方产品id
	 * @param $page 当前页
	 * @param $pagesize 页大小
	 */
	public function displayThree($productid,$page,$pagesize){
		$offset = ($page-1)*$pagesize;
		$obj = new Psys_FinModel();

		$data = $obj -> GetThreeOrderList($productid);
		$total = count($data);
		$curlist = array_slice($data, $offset,$pagesize);
		$cursize = count($curlist);
		self::inidate($total,$page,$pagesize,$cursize);
		$this->smarty->assign('olist',$curlist);
		$this->forward = 'three';
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