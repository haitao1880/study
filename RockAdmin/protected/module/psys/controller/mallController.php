<?php
/**
 * //商城管理
 * @author Administrator
 *  */
class mallController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 *商品添加 
	 **/
	public function goodsaddAction()
	{
		$this->forward = 'goodsedit';
	}
	
	/**
	 *商品编辑   
	 **/
	public function goodseditAction()
	{
		$id = reqnum('id', 0);
		if($id)
		{
			$where = array('id'=>$id);
			$field = '*';
			$model = new Psys_GoodsModel();
			$goods = $model->GetOne($where,$field);
			$this->smarty->assign('goods',$goods);
			$this->forward = 'goodsedit';
		}
	}
	
	/**
	 * 商品更新
	 * @return multitype:string  
	 * */
	public function goodsupdateAction()
	{
		$ispost = reqnum('ispost',0);
		if($ispost)
		{
			
			$goods_id = reqnum('goods_id',0);
			$name = reqstr('name','');
			$category = reqstr('category','');
			$price = reqnum('price',0);
			$discount = reqstr('discount','');
			$desc = reqstr('desc','');
			$flag = reqnum('flag',0);
			$imgs = reqstr('imgs','');
			$starttime = reqstr('starttime','');
			$endtime = reqstr('endtime','');
			$num = reqnum('num',0);
			if($starttime){
				$starttime = date('Y/m/d',strtotime($starttime));
			}
			if($endtime){
				$endtime = date('Y/m/d',strtotime($endtime));
			}
			$data = array(
					'name'=>$name,
					'category'=>$category,
					'price'=>floatval($price),
					'discount'=>floatval($discount),
					'desc'=>$desc,
					'flag'=>$flag,
					'imgs'=>$imgs,
					'starttime'=>$starttime,
					'endtime'=>$endtime,
					'num'=>$num
			);
			$result = array (
					'result' => 'SUCCESS'
			);
			$model = new Psys_GoodsModel();
			if($goods_id == 0)
			{	
				$data['ctime'] = time();
				$data['utime'] = time();
				$insert_R = $model->AddOne($data);
				if(!$insert_R)
				{
					$result = array('result'=>'ERROR');
				}
			}
			else
			{	
				$where = array('id'=>$goods_id);
				$data['utime'] = time();
				$update_R = $model->UpdateOne($data, $where);
				if(!$update_R)
				{
					$result = array('result'=>'ERROR');
				}
			}
		}
		return $result;
	}
	
	/**
	 * 商品列表   
	 **/
	public function goodslistAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",20);
	
		$model = new Psys_GoodsModel();
		$where = array();
		$orderby = '';
		$field = '*';
		$goods_list = $model->GetList($where,$orderby,$page,$pagesize,$field);
		if(!empty($goods_list['allrow']))
		{
			foreach($goods_list['allrow'] as  &$goods)
			{
				//获取城市信息
			/* 	$goods['ctime'] = date('Y-m-d H:i:s',$goods['ctime']);
				$goods['utime'] = date('Y-m-d H:i:s',$goods['utime']);
				$imgsUrl = explode(';', $goods['imgs']);
				foreach($imgsUrl as &$value){
					$value = 'http://admin.wonaonao.com/imgs/goods/'.$value;
				} */
				$goods['imgs'] = GOODS_PATH.'goods_'.$goods['id'].'.png';
			}
		}
		self::inidate ( $goods_list['allnum'], $page, $pagesize,count($goods_list['allrow']));
		$this->smarty->assign('goods_list',$goods_list['allrow']);
		$this->forward = 'goodslist';
	}
	
	/**
	 * 商品状态切换
	 */
	public function goodstoggleAction()
	{
		$id = reqnum('id',0);
		$result = array('result'=>'SUCCESS','msg'=>'');
		if($id)
		{
			$where = array('id'=>$id);
			$field = 'flag';
			$model = new Psys_GoodsModel();
			$goods = $model->GetOne($where,$field);
			$update_data = array('flag'=>(int)!$goods['flag']);
			$update_R = $model->UpdateOne($update_data, $where);
			if(!$update_R)
			{
				$result = array('result'=>'ERROR','msg'=>'状态修改失败');
			}
		}
		else
		{
			$result = array('result'=>'ERROR','msg'=>'系统出错');
		}
		return $result;
	}
	
	
	/**
	 * 优惠券是否兑换切换
	 */
	public function ordertoggleAction()
	{
		$id = reqstr('id',0);
		$result = array('result'=>'SUCCESS','msg'=>'');
		if($id)
		{
			$where = array('orderid'=>$id);
			$field = 'ifuse';
			$model = new Psys_MallorderModel();
			$order = $model->GetOne($where,$field);
			$update_data = array('ifuse'=>(int)!$order['ifuse'],'utime'=>time());
			$update_R = $model->UpdateOne($update_data, $where);
			if(!$update_R)
			{
				$result = array('result'=>'ERROR','msg'=>'状态修改失败');
			}
		}
		else
		{
			$result = array('result'=>'ERROR','msg'=>'系统出错');
		}
		return $result;
	}
	/**
	 * 商品删除
	 * @return multitype:string  
	 * */
	public function goodsdelAction()
	{
		$id = reqnum('id',0);
		$result = array('result'=>'SUCCESS','msg'=>'删除成功！');
		if($id)
		{
			$where = array('id'=>$id);
			$model = new Psys_GoodsModel();
			$update_R = $model->DeleteOne($where);	
			if(!$update_R)
			{
				$result = array('result'=>'ERROR','msg'=>'状态修改失败');
			}
		}
		else
		{
			$result = array('result'=>'ERROR','msg'=>'系统出错');
		}
		return $result;
	}
	/**
	 *订单列表   
	 **/
	public function orderlistAction(){
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",20);
		
		$orderid = reqstr('orderid','');
		$username = reqstr('username','');
		
		
		$model = new Psys_MallorderModel();
		$where = array();
		if(!empty($orderid)){
			$where['orderid'] = $orderid;
			$this->smarty->assign('orderid',$orderid);
		}
		if(!empty($username)){
			$where['username'] = $username;
			$this->smarty->assign('username',$username);
		}
		$orderby = ' id desc ';
		$field = '*';
		$order_list = $model->GetList($where,$orderby,$page,$pagesize,$field);
		if(!empty($order_list['allrow']))
		{
			$rule = new Psys_MallorderRule();
			foreach($order_list['allrow'] as  &$order)
			{
				//获取城市信息
				$order['ctime'] = date('Y-m-d H:i:s',$order['ctime']);
				$order['utime'] = date('Y-m-d H:i:s',$order['utime']);
				$order['name'] = $rule->GetGoodsName(array('goodsid'=>$order['goodsid']));
			}
		}
		self::inidate ( $order_list['allnum'], $page, $pagesize,count($order_list['allrow']));
		$this->smarty->assign('order_list',$order_list['allrow']);
		$this->forward = 'orderlist';	
	}
	
	/**
	 *确认订单
	 **/
	public function confirmorderAction(){
		$orderid = reqstr('orderid','');
		$result = array('result'=>'SUCCESS','msg'=>'');
		
		if(empty($orderid)){
			$result['result'] = 'ERROR';
			$result['msg'] = '参数orderid不能为空';
			return $result;
		}
		$flag = 1;
		$model = new Psys_MallorderModel();
		$ret = $model->UpdateOrderFlag($orderid,$flag);
		return $ret;
	}
	
	/**
	 *取消订单
	 **/
	public function concelorderAction(){
		$orderid = reqstr('orderid','');
		$result = array('result'=>'SUCCESS','msg'=>'');
	
		if(empty($orderid)){
			$result['result'] = 'ERROR';
			$result['msg'] = '参数orderid不能为空';
			return $result;
		}
		$flag = 3;
		$model = new Psys_MallorderModel();
		$ret = $model->UpdateOrderFlag($orderid,$flag);
		return $ret;
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
	
	/**
	 * 图片上传
	 */
	public function uploadAction()
	{
		$files = array();
		foreach($_FILES['file'] as $k=>$v)
		{
			foreach($v as $key=>$val)
			{
				$files[$key][$k] = $val;
			}
		}
		//上传错误提示
		$errorMsg = array(
				'0'=>'文件上传成功',
				'1'=>'文件超出了服务器配置大小',
				'2'=>'文件超出了表单配置大小',
				'3'=>'仅部分文件上传',
				'4'=>'没有找到上传文件,请选择文件',
				'5'=>'上传文件大小为零',
				'6'=>'未找到临时文件夹',
				'7'=>'临时文件夹写入失败',
				'8'=>'服务器文件上传扩展未开启',
				'9'=>'上传图片规格不符合要求',
				'10'=>'存放文件夹建立失败',
				'11'=>'上传文件移动失败',
				'12'=>'上传图片不符合要求580*560'
		);
		$dir = GOODS_Upload_PATH;
		$id = reqnum('id', 0);
		if(!$id){
			$model = new Psys_GoodsRule();
			$lastId = $model->GetLastId();
			$id = $lastId+1;
		}
		
		$arr = array();
		//json数据返回
		$returnJson = '';
	
		//上传详情
		$msg = '';
		//成功与否标志
		$flag = true;
		foreach($files as $k=>$file)
		{
			$img_info = getimagesize($file['tmp_name']);
			if( ($img_info[0] < 576)||($img_info[1]< 206 )){
				$flag = false;
				$msg = $errorMsg[12];
			}else{
				if($file['error'] == 0)
				{
					if(!is_dir($dir))
					{
						if(!mkdir($dir,0777,true))
						{
							$flag = false;
							$msg = $msg ? $msg . ',' . $file['name'] . $errorMsg[10] : $file['name'] . $errorMsg[10];
						}
					}
					else
					{
						$ext = pathinfo($file['name'],PATHINFO_EXTENSION);
						$filename = 'goods_'.$id.'.png';
						$savePath = $dir . $filename;
						$arr['img_name'] = isset($arr['img_name']) ? $arr['img_name'] . ';' .$filename : $filename;
						if(!move_uploaded_file($file['tmp_name'], $savePath))
						{
							$flag = false;
							$msg = $msg ? $msg . ',' . $file['name'] . $errorMsg[11] : $file['name'] . $errorMsg[11];
						}
					}
				}
				else
				{
					$flag = false;
					$msg = $msg ? $msg . ',' . $file['name'] . $errorMsg[$file['error']] : $file['name'] . $errorMsg[$file['error']];
					$arr['result'] = 'error';
					$arr['msg'] = $errorMsg[$file['error']];
				}
			}
			
		}
		if($flag)
		{
			$arr['result'] = 'success';
			$arr['msg'] = $errorMsg[0];
		}
		else
		{
			$arr['error'] = 'error';
			$arr['msg'] = $msg;
		}
		//json返回
		$returnJson = json_encode($arr);
		die("<script type='text/javascript'>window.parent.callbackFunction('".$returnJson."');</script>");
	}
	
	public  function expAction()
	{
		$orderid = reqstr('orderid','');
		$username = reqstr('username','');
		$id = reqstr('id','');
		$where = '1=1 and flag=1';
		if(!empty($orderid)){
			$where .= " and orderid='".$orderid."'";
		}
		if(!empty($username)){
			$where .= ' and username='.$username;
		}
		if(!empty($id)){
			$id = trim($id,',');
			$where .= " and id in (".$id.")";
		}
		$page = reqnum ( "page", 1 );
		$pagesize = reqnum ( "pagesize", 1000 );
		$m = new Psys_UserModel ();
		$list = $m->GetList ( $where, '', $page, $pagesize, "id,username,mark,goodsid,num,ctime",'rhi_mallorder' );
		global $G_X;
		$filestr = 'charge'.date('YmdHis').uniqid().'.csv';
		$filename = PUBLIC_PATH.$G_X['prjstr'].DIRECTORY_SEPARATOR.$filestr; //设置文件名
	
		if(!file_exists($filename)){
			touch($filename);
			chmod($filename, 0777);
		}
		$fp = fopen($filename, 'w');
		$title = array("序号","电话号码",'发放号码','商品id','数目','兑换时间');
		foreach($title as &$value){
			$value = iconv('utf-8','gb2312',$value);
		}
		fputcsv($fp, $title);
		if($list['allrow']){
			foreach($list['allrow'] as &$row){
				$row['ctime'] = date('Y-m-d H:i:s',$row['ctime']);
				if(fputcsv($fp, $row)){
					$m->UpdateOne(array('ifuse'=>1), array('id'=>$row['id']),'rhi_mallorder');
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
	
}