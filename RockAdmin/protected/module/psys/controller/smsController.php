<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月10日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:ipcController.php                                                
* 创建时间:下午3:02:53                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: ipcController.php 4042 2014-09-01 09:21:47Z neil $                                                 
* 修改日期:$LastChangedDate: 2014-09-01 17:21:47 +0800 (周一, 2014-09-01) $                                     
* 最后版本:$LastChangedRevision: 4042 $                                 
* 修 改 者:$LastChangedBy: neil $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/controller/ipcController.php $                                            
* 摘    要: 车上服务器管理                                                      
*/

class smsController extends Psys_AbstractController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 短信列表
	 */
	public function indexAction()
	{
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",20);
		$obj = new Psys_SmsModel();
		$data = $obj -> GetList('', 'id DESC',$page, $pagesize,"*");
		
		self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
		
		$this->smarty->assign('list',$data['allrow']);
		$this->forward = "index";
	}
	//获取Appkey
	public function getAppkeys(){
	 	return array(
	 		'qdn'=>'青岛南','qdb'=>'青岛北','jn'=>'济南','jnx'=>'济南西','zb'=>'淄博','wf'=>'潍坊','qf'=>'曲阜',
			'ta'=>'泰安','tz'=>'滕州','zz'=>'枣庄','yt'=>'烟台','qdb-b'=>'青岛汽车北站','sf-b'=>'青岛汽车总站','qdd-b'=>'青岛汽车东站','pd-b'=>'青岛平度站','jm-b'=>'青岛即墨站','lxjs-b'=>'青岛莱西姜山站','hd-b'=>'青岛黄岛站','jn-b'=>'青岛脐南站','dqd'=>'大庆东站','qqhe'=>'齐齐哈尔南站'
	 	);
	}
	/**
	 * 充值
	 */
	public function addAction()
	{
		$obj = new Psys_SmsModel();
		if ($_POST) {
			//获取修改人信息
			$adminuser = XSession::Get('Cur_X_User') ;
			$data['nume'] = (int)$_POST['nume'];
			$data['type'] = (int)$_POST['type'];
			$data['user'] = $adminuser['realname'];
			$data['ctime'] = time();
			$r = $obj->AddOne($data);
			if($r){
				header('location:/sms/index');
			}
		}			
		$this->forward = "add";
	}
	/**
	 * 充值
	 */
	public function setAction()
	{
		$obj = new Psys_SmsModel();
		$path = CONF_PATH."config_sms.ini";
		if ($_POST) {
			$email = !empty($_POST['email']) ? trim($_POST['email']) : '';
			$data['msms'] = array('type'=>1,'max'=>(int)$_POST['type1'],'email'=>$email);
			$data['mdsms'] = array('type'=>2,'max'=>(int)$_POST['type2'],'email'=>$email);
			$data['busmdsms'] = array('type'=>3,'max'=>(int)$_POST['type3'],'email'=>$email);
			
			if(!empty($data)){				
				$this->write_ini_file($data, $path, true);
				header('location:/sms/set');
			}
		}		
		$dc = $this->readini($path);
		$this->smarty->assign('smsc',$dc);  //当前从几开始	
		$this->forward = "set";
	}
	/**
	 * 获取短信发送量
	 */
	public function getmsmmumAction()
	{
		$path = CONF_PATH."config_sms.ini";
		$obj = new Psys_SmsModel();
		//获取统计充值短信量
		$cou = $obj->getSmsStatistics();
			
		$etime = time();
		$dc = $this->readini($path);
		$content = '';
		$address = '';
		foreach ($dc as $v){
			$stime = $cou[$v['type']]['etime'];
			$stime = !empty($stime) ? $stime : 0;
			$num = $cou[$v['type']]['numes'];
			$data = $obj -> addSmsStatistics($stime,$etime,$num,$v['type']);
			if($data['numes'] <= $v['max']){				
				if($v['type']==1){
					$content .= '电信剩余短信量不足'.$v['max'].'请尽快充值！！ ';
				}elseif($v['type']==2){
					$content .= ' 漫道(铁路)剩余短信量不足'.$v['max'].'请尽快充值！！';				
				}else{
					$content .= ' 漫道(交运)剩余短信量不足'.$v['max'].'请尽快充值！！';				
				}
				$address = $v['email'];				
			}
		}
		if(!empty($_POST['ajax'])){
			$data = $obj ->getSmsStatistics();
			$ed = array('result'=> 'SUCCESS','data'=>$data);
			echo json_encode($ed);
			exit;
		}
		if(!empty($content) and !empty($address)){
			//发送邮件
			$msg = $this->SendMail('短信余量报警',$content,$address);
		}
		exit;
	}
	public function getTimeNumAction(){
		$stime = reqstr('stime',0);
		$etime = reqstr('etime',0);
		$type = reqstr('type',0);
		$username = reqstr('username','');
		$code = reqstr('code','');
		$appk = reqstr('appk','');
		$obj = new Psys_SmsModel();
		if($stime){
			$stime = strtotime($stime);
		}else{
			//$stime = strtotime("-1 day");
			$stime = '';
		}

		if($etime){
			$etime = strtotime($etime);
		}else{
			$etime = '';
		}
		$smscot = $obj ->getSmsStatistics();
		$postime = '&';
		if(!empty($stime) and !empty($etime)){
			$postime .= 'stime='.date('Y-m-d',$stime).'&etime='.date('Y-m-d',$etime);
		}
		$postime .= 'type='.$type;
		
		if(!empty($appk)) $postime .= '&appk='.$appk;
		if(!empty($username)) $postime .= '&username='.$username;
		if(!empty($code)) $postime .= '&code='.$code;
		
		$page = reqnum("page",1);
		$pagesize = reqnum("pagesize",20);
		
		$data = $obj -> getSmsIdclogList($stime,$etime,$type,$username,$appk,$code,$page,$pagesize);
		self::inidate($data['allnum'],$page,$pagesize,count($data['allrow']));
		
		$appkeys = $this->getAppkeys();
		foreach ($data['allrow'] as &$v){
			if(!empty($appkeys[$v['appkey']])){
				$v['appkey'] = $appkeys[$v['appkey']];
			}else{
				$v['appkey'] = $v['appkey'];
			}
		}
		
		$this->smarty->assign('appkeys',$appkeys);  //站点列表
		$this->smarty->assign('smscot',$smscot);  //当前从几开始
		$this->smarty->assign('stime',$stime);
		$this->smarty->assign('etime',$etime);
		$this->smarty->assign('appk',$appk);
		$this->smarty->assign('username',$username);
		$this->smarty->assign('type',$type);
		$this->smarty->assign('postime',$postime);
		$this->smarty->assign('list',$data['allrow']);
		$this->forward = "smsnum";
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
		$pageoffset =($pagesize-10-1)/2; //当前页 左右偏移
		$this->smarty->assign('cur_page',$page); //当前页
		if($allcount>($pagesize))
		{
			//如果当前页小于等于左偏移
			if($page<=$pageoffset)
			{
				$startNum=1;
				$endNum = 10;
			}
			else
			{//如果当前页大于左偏移
				//如果当前页码右偏移超出最大分页数
				if($page+$pageoffset>=$allcount)
				{
					$startNum = $allcount-$pagesize;
				}
				else
				{
					//左右偏移都存在时的计算
					$startNum = $page-$pageoffset;
					$endNum = $page+$pageoffset-1;
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
	//写入ini文件
	private function write_ini_file($assoc_arr, $path, $has_sections=FALSE)
	{
		$content = "";
		if ($has_sections)
		{
			foreach ($assoc_arr as $key=>$elem)
			{
				$content .= "[".$key."]\n";
				foreach ($elem as $key2=>$elem2)
				{
					if(is_array($elem2))
					{
						for($i=0;$i<count($elem2);$i++)
						{
							$content .= $key2."[] = \"".$elem2[$i]."\"\n";
						}
					}
					else if($elem2=="") $content .= $key2." = \n";
					else $content .= $key2." = \"".$elem2."\"\n";
				}
			}
		}else{
			foreach ($assoc_arr as $key=>$elem)
			{
				if(is_array($elem))
				{
					for($i=0;$i<count($elem);$i++)
					{
						$content .= $key2."[] = \"".$elem[$i]."\"\n";
					}
				}else if($elem=="") $content .= $key2." = \n";
				else $content .= $key2." = \"".$elem."\"\n";
			}
		}
		if (!$handle = fopen($path, 'w'))
		{
			return false;
		}
		if (!fwrite($handle, $content))
		{
			return false;
		}
		fclose($handle);
		return true;
	}
	//读取ini文件
	private function readini($name)
	{
		if (file_exists($name))
		{
			$data = parse_ini_file($name,true);
			if ($data)
			{
				return $data;
			}
		}
		else
		{
			return false;
		}
	}
	//发邮件
	function SendMail($subject,$ctext,$mailadd){
		$msg = array();
		//require(PUBLIB_PATH."mail".DIRECTORY_SEPARATOR."class.phpmailer.php"); 
		
		$e_mail = explode(',',$mailadd);
		$is_send = 1;
		$m_error = array();
		foreach ($e_mail as $v){
			$mail = new PHPMailer(); //建立邮件发送类
			$mail->IsSMTP(); // 使用SMTP方式发送
			$mail->CharSet = "utf-8";
			$mail->Host = "mail.rockhippo.cn"; // 您的企业邮局域名
			$mail->SMTPAuth = false; // 启用SMTP验证功能
			$mail->Port=25;
			$mail->From = "seepsms@rockhippo.cn"; //邮件发送者email地址
			$mail->FromName = "短信余量报警";
			$mail_name = explode('@',$v);
			
			$mail->AddAddress("$v", $mail_name[0]);//收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
			//$mail->AddReplyTo("", "");
	
			//$mail->AddAttachment("/var/tmp/file.tar.gz"); // 添加附件
			$mail->IsHTML(true); // set email format to HTML //是否使用HTML格式
	
			$mail->Subject = $subject; //邮件标题
			$mail->Body = $ctext; //邮件内容
			//$mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //附加信息，可以省略
	
			if(!$mail->Send())
			{
				$is_send = 0;
				$m_error[] = $mail->ErrorInfo;
			}
		}
		$msg = array('code'=>$is_send,'msg'=>$m_error);
		return $msg;
	}
}