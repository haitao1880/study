<?php

class regionController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 列表
	 */
	public function indexAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",100);
		
		$nt = new PSys_RegionModel();
		$list = $nt -> GetList('region_type =  2', ' region_id ASC',$page, $pagesize,"*");

		self::inidate($list['allnum'],$page,$pagesize,count($list['allrow']));

		$this->smarty->assign('list',$list['allrow']);
		$this->smarty->assign('psys_base_url',PSYS_BASE_URL);
		$this->forward = "index";
	}
	//更新数据库中的拼音转化的 只做测试用
	public function testupdateDataAction()
	{
		$model = new PSys_RegionModel();
		$list = $model -> GetList('region_type > 0  and region_type < 3', ' region_id ASC',0, 0,"*");

		require_once COMMON_PATH."Xpinyin.php";
		$py = new Xpinyin();
		foreach($list['allrow'] as $lv)
		{
			
			$pinyin = $py->Pinyin($lv['region_name']); //全拼
			$logogram = $py->getInitials($lv['region_name']); //简写
			$initials = substr($logogram,0,1); //首字母
			$data =array(
					'initials' => $initials,
					'pinyin'  =>$pinyin,
					'logogram' => $logogram,
						);
			$w = array('region_id' => $lv['region_id']);
			$model->UpdateOne($data,$w);
		}
		 echo "更新完毕";
		
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