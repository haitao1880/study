<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月11日                                                 
* 作　  者:peter
* E-mail  :peter@rockhippo.cn
* 文 件 名:userController.php                                                
* 创建时间:下午2:11:39                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                
* 修改日期:$LastChangedDate: 2014-08-27 16:59:08 +0800 (周三, 27 八月 2014) $                                     
* 最后版本:$LastChangedRevision: 3951 $                                 
* 修 改 者:$LastChangedBy: peter $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/controller/memberController.php $                                            
* 摘    要: 会员管理                                                      
*/
include  PUBLIB_PATH."phpexcel".DIRECTORY_SEPARATOR."PHPExcel.php";
class userController extends Psys_AbstractController {
	public function __construct() {
		parent::__construct ();
	}
	/***
	 * 导出数据
	 */
	public  function expAction()
	{
		$appkey = reqstr('appkey','');
		$username = reqstr('username','');
		$moeny = reqstr('moeny','');
		$isreceive = reqstr('isreceive');
		$isgive = '0';
		$indate = reqstr('indate',0);
		$todate = reqstr('todate',0);
		$where = '1=1 and flag=1';
		if(!empty($appkey)){
			$where .= " and appkey='".$appkey."'";
		}
		if(!empty($moeny)){
			$where .= ' and moeny='.$moeny;
		}
		if(!empty($isreceive) || $isreceive === '0'){
			$where .= ' and isreceive='.$isreceive;
		}
		if(!empty($isgive) || $isgive === '0'){
			$where .= ' and isgive='.$isgive;
		}
		if(!empty($username)){
			$where .= " and username='".$username."'";
		}
		if($indate > 0){
			$where .= ' and ctime > '.strtotime($indate);
		}
		if($todate > 0){
			$where .= ' and ctime <= '.strtotime($todate);
		}
		$page = reqnum ( "page", 1 );
		$pagesize = reqnum ( "pagesize", 1000 );
		$m = new Psys_UserModel ();
		$list = $m->GetList ( $where, 'id DESC', $page, $pagesize, "id,username,receiveno,moeny,ctime",'rhi_useraward' );
		global $G_X;
		$filestr = 'award'.date('YmdHis').uniqid().'.csv';
		$filename = PUBLIC_PATH.$G_X['prjstr'].DIRECTORY_SEPARATOR.$filestr; //设置文件名
	
		if(!file_exists($filename)){
			touch($filename);
			chmod($filename, 0777);
		}
		$fp = fopen($filename, 'w');
		$title = array("序号","电话号码",'发放号码','中奖金额','更新时间');
		foreach($title as &$value){
			$value = iconv('utf-8','gb2312',$value);
		}
		fputcsv($fp, $title);
		if($list['allrow']){
			foreach($list['allrow'] as &$row){
				$row['ctime'] = date('Y-m-d H:i:s',$row['ctime']);
				if(fputcsv($fp, $row)){
					$m->UpdateOne(array('isgive'=>1), array('id'=>$row['id']),'rhi_useraward');
				}
			}	
		}
		fclose($fp);
// 		header('Content-Type: application/octet-stream');
// 		header('Content-Disposition: attachment; filename=' . $filestr);
// 		header('Content-Transfer-Encoding: binary');
// 		header('Content-Length: ' . filesize($fileName));
// 		readfile($filename);
 		return array('status'=>1,'filename'=>$filestr);
	}
	
	/***
	 * 抽奖用户列表
	 */
	public function indexAction() {
		$appkey = reqstr('appkey','');
		$username = reqstr('username','');
		$indate = reqstr('indate',0);
		$todate = reqstr('todate',0);
		$where = '1=1';
		if(!empty($appkey)){
			$where .= " and appkey='".$appkey."'";
		}		
		if(!empty($username)){
			$where .= " and username='".$username."'";
		}
		if($todate > 0){
			$where .= ' and ctime > '.strtotime($indate);
		}
		if($indate > 0){
			$where .= ' and ctime <= '.strtotime($todate);
		}
		$page = reqnum ( "page", 1 );
		$pagesize = reqnum ( "pagesize", 10 );
		$m = new Psys_UserModel ();
		$list = $m->GetList ( $where, 'id DESC', $page, $pagesize, "*" );
		foreach ( $list ['allrow'] as $key => &$var ) {
			MsgInfoConst::GetAppKey ( $var ['appkey'], $err );
			$var ['appkey'] = $err ['msg'];
		}
		self::inidate ( $list ['allnum'], $page, $pagesize, count ( $list ['allrow'] ) );
		
		$this->smarty->assign ( 'list', $list ['allrow'] );
		$this->smarty->assign('appkeys',MsgInfoConst::$appkey_arr);  //站点列表
		$this->smarty->assign ( 'psys_base_url', PSYS_BASE_URL );
		$this->forward = "index";
	}
	/***
	 * 中奖用户
	 */
	public function awardAction() {
		
		$appkey = reqstr('appkey','');		
		$username = reqstr('username','');
		$moeny = reqstr('moeny','');
		$isreceive = reqstr('isreceive','');
		$isgive = reqstr('isgive','');
		$indate = reqstr('indate',0);
		$todate = reqstr('todate',0);		
		$where = '1=1 and flag=1';
		if(!empty($appkey)){
			$where .= " and appkey='".$appkey."'";
		}
		if(!empty($moeny)){
			$where .= ' and moeny='.$moeny;
		}
		if(!empty($isreceive) || $isreceive === '0' ){
			$where .= ' and isreceive='.$isreceive;
		}
		if(!empty($isgive) || $isgive === '0'){
			$where .= ' and isgive='.$isgive;
		}
		if(!empty($username)){
			$where .= " and username='".$username."'";
		}		
		if($todate > 0){
			$where .= ' and ctime <= '.strtotime($todate);
		}
		if($indate > 0){
			$where .= ' and ctime > '.strtotime($indate);
		}	
		$page = reqnum ( "page", 1 );
		$pagesize = reqnum ( "pagesize", 10 );
		$m = new Psys_UserModel ();
		$sumaward = $m->getAwardSum($where);
		$sum=0;
		if($sumaward){
			$sum = $sumaward;
		}
		$list = $m->GetList ( $where, 'id DESC', $page, $pagesize, "*",'rhi_useraward' );
		$count=$list ['allnum'];
		
		foreach ( $list ['allrow'] as $key => &$var ) {
			MsgInfoConst::GetAppKey ( $var ['appkey'], $err );
			$var ['appkey'] = $err ['msg'];
		}
		
		$this->smarty->assign('appkeys',MsgInfoConst::$appkey_arr);  //站点列表
		self::inidate ( $list ['allnum'], $page, $pagesize, count ( $list ['allrow'] ) );
	
		$this->smarty->assign ( 'list', $list ['allrow'] );
		$this->smarty->assign ( 'count', $count );
		$this->smarty->assign ( 'sum',$sum );
		$this->smarty->assign ( 'psys_base_url', PSYS_BASE_URL );
		$this->forward = "awarder";
	}
	/***
	 * 用户下载日志
	*/
	public function tasksAction() {
		$appkey = reqstr('appkey','');
		$username = reqstr('username','');
		$taskid=reqstr('taskid','');
		$indate = reqstr('indate',0);
		$todate = reqstr('todate',0);
		$where = '1=1 ';
		if(!empty($appkey)){
			$where .= " and appkey='".$appkey."'";
		}
		if(!empty($taskid)){
			$where .= ' and taskid='.$taskid;
		}		
		if(!empty($username)){
			$where .= " and username='".$username."'";
		}
		if($todate > 0){
			$where .= ' and ctime > '.strtotime($indate);
		}
		if($indate > 0){
			$where .= ' and ctime <= '.strtotime($todate);
		}
		$page = reqnum ( "page", 1 );
		$pagesize = reqnum ( "pagesize", 10 );
		$applist=array('61'=>'开心消消乐','49'=>'消灭星星2015','47'=>'萌宠泡泡龙','68'=>'凤凰视频','69'=>'哪吒看书','70'=>'微看点');
		$m = new Psys_UserModel ();
		$list = $m->GetList ( $where, 'id DESC', $page, $pagesize, "*",'rhi_usertask' );
		foreach ( $list ['allrow'] as $key => &$var ) {
			MsgInfoConst::GetAppKey ( $var ['appkey'], $err );
			$var ['appkey'] = $err ['msg'];
			$var ['taskid']=$applist[$var ['taskid']];
			
		}
		$s_arr = $arr_xip_fw [$appkey];
		self::inidate ( $list ['allnum'], $page, $pagesize, count ( $list ['allrow'] ) );
		$this->smarty->assign('appkeys',MsgInfoConst::$appkey_arr);  //站点列表
		$this->smarty->assign('tasks',$applist);  //站点列表
		$this->smarty->assign ( 'list', $list ['allrow'] );
		$this->smarty->assign ( 'psys_base_url', PSYS_BASE_URL );
		$this->forward = "tasks";
	}
	/**
	 * 分页数据显示
	 * 
	 * @param num $allcount
	 *        	总条数
	 * @param num $page        	
	 * @param num $pagesize        	
	 * @param num $cursize
	 *        	当前页的数据条数
	 */
	private function inidate($allcount, $page, $pagesize, $cursize) {
		$this->smarty->assign ( 'allcount', $allcount ); // 总数
		$allpage = ceil ( $allcount / $pagesize );
		$this->smarty->assign ( 'allpage', $allpage ); // 总页数
		$pagesize = ($pagesize % 2) ? $pagesize : $pagesize + 1; // 页码取偶数 = 步长
		$pageoffset = ($pagesize - 1) / 2; // 当前页 左右偏移
		$this->smarty->assign ( 'cur_page', $page ); // 当前页
		if ($allcount > $pagesize) {
			// 如果当前页小于等于左偏移
			if ($page <= $pageoffset) {
				$startNum = 1;
				$endNum = $pagesize;
			} else { // 如果当前页大于左偏移
			  // 如果当前页码右偏移超出最大分页数
				if ($page + $pageoffset >= $allcount + 1) {
					$startNum = $allcount - $pagesize + 1;
				} else {
					// 左右偏移都存在时的计算
					$startNum = $page - $pageoffset;
					$endNum = $page + $pageoffset;
				}
			}
		} else {
			$startNum = 1;
			$endNum = $allcount;
		}
		
		$this->smarty->assign ( 'startNum', $startNum ); // 当前从几开始
		$this->smarty->assign ( 'endNum', $endNum ); // 当前到几结束
		$this->smarty->assign ( 'sli', (($pagesize * ($page - 1) + 1)) ); // 当前页的起始第多少条
		$this->smarty->assign ( 'eli', ($pagesize * ($page - 1) + $cursize) ); // 当前页最后一条是第多少条
	}
}