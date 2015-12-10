<?php
//sim卡状态
class simController extends Psys_AbstractController
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
		$pagesize = reqnum("pagesize",10);
		
		$nt = new PSys_SimModel();
		$list = $nt -> GetList('', 'id DESC',$page, $pagesize,"*");

		self::inidate($list['allnum'],$page,$pagesize,count($list['allrow']));

		$this->smarty->assign('list',$list['allrow']);
		$this->smarty->assign('psys_base_url',PSYS_BASE_URL);
		$this->forward = "index";
	}
	
	/**
	 * 添加
	 */
	public function addAction()
	{
		$isadd = 0;
		if(array_filter($_POST)){
			if(count($_POST)<5){
				$isadd = -1;  //参数错误
			}else{
				$_POST['ctime'] = time();
				$k = $_POST['frequency'] == 4 ? ($_POST['frequency']*$_POST['frequency']+2*$_POST['frequency'])/2 : ($_POST['frequency']*$_POST['frequency']+$_POST['frequency'])/2;
				$_POST['renewaldate'] = $_POST['activation']?date('Y-m-d',(strtotime("+$k month" , strtotime($_POST['activation'])))):'';
				$m = new Psys_SimModel();
				
				$ck = $m -> GetOne(array('cardnum'=>$_POST['cardnum']),'id');
				if ($ck>0) {
					$isadd == -3; //重复数据
				}
				else{
					$r = $m -> AddOne($_POST);
					if($r > 0){
						$isadd = 1; //成功
					}else{
						$isadd = -2; //失败
					}
				}
				
			}
		}
		$this->smarty->assign('isadd',$isadd);
		$this->forward = "add";
	}
	
	public function delAction()
	{
		$id = reqnum('id','');
		if (!$id) {
			return ;
		}
		$m = new Psys_SimModel();
		$m->DeleteOne(array('id'=>$id));
		header('Location: /sim/index');
	}
	
	
	public function editAction()
	{
		
		$id = reqnum('id','');
		if (!$id) {
			return ;
		}
		$m = new Psys_SimModel();
		if ($_POST) {
			
			if ($_POST['activation'] && !$_POST['renewaldate']) {
				$k = $_POST['frequency'] == 4 ? ($_POST['frequency']*$_POST['frequency']+2*$_POST['frequency'])/2 : ($_POST['frequency']*$_POST['frequency']+$_POST['frequency'])/2;
				$_POST['renewaldate'] = $_POST['activation']?date('Y-m-d',(strtotime("+$k month" , strtotime($_POST['activation'])))):'';
				
			}
			
			$r = $m->UpdateOne($_POST, array('id'=>$id));
			if ($r>0) {
				echo "<script>alert('成功');</script>";
				echo "<meta http-equiv='Refresh' content='0;URL=/sim/index'>"; 
				$this->forward = 'add';
				return ;
			}
			
			echo "<script>alert('失败');</script>";
		}
		
		$data = $m->GetOne(array('id'=>$id));
		
		foreach ($data as $key=>$var){
			$this->smarty->assign($key,$var);
		}
		$this->forward = "edit";
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