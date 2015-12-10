<?php
class pointsController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function pointslistAction(){
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",20);

		$username = reqstr('username','');
		
		$model = new Psys_PointsModel();
		$where = array();
		if(!empty($username)){
			$where['username'] = username;
		}
		$orderby = '';
		$field = '*';
		$points_list = $model->GetList($where,$orderby,$page,$pagesize,$field);
		if(!empty($points_list['allrow']))
		{
			foreach($points_list['allrow'] as  &$points)
			{
				//获取城市信息
				$points['ctime'] = date('Y-m-d H:i:s',$points['ctime']);		
			}
		}
		self::inidate ( $points_list['allnum'], $page, $pagesize,count($points_list['allrow']));
		$this->smarty->assign('points_list',$points_list['allrow']);
		$this->forward = 'pointslist';	
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