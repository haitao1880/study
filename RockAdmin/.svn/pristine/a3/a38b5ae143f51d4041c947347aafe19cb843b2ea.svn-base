<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月7日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:XBootStrap.php                                                
* 创建时间:下午1:23:15                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: XBootStrap.php 664 2014-07-11 04:04:20Z tony_ren $                                                 
* 修改日期:$LastChangedDate: 2014-07-11 12:04:20 +0800 (周五, 11 七月 2014) $                                     
* 最后版本:$LastChangedRevision: 664 $                                 
* 修 改 者:$LastChangedBy: tony_ren $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/publib/boot/XBootStrap.php $                                            
* 摘    要:系统启动类                                                       
*/
class XBootStrap
{
	public function run($prj='')
	{
		$mod = '';
		$act = '';
		UrlParse($prj,$mod, $act);
		$isnologinerr = false;
		$prj = strtolower($prj);
		$mod = strtolower($mod);
		$act = strtolower($act);
		
		global $G_X;
		
		$noyz = @$G_X['allow_project'][$prj]['nologin'];
		$noyz = isset($noyz[$mod]) ? $noyz[$mod] : array();
		$noyz = is_array($noyz) ? $noyz : array();
		
		$actstr = $act == '' ? 'index':$act;
		$actstr .= "Action";
	
		//echo $G_X['modstr']."<br />";
		//$session=XSession::Get("Cur_X_User");
		
		//开发阶段取消验证
		//if(!in_array($act, $noyz))
		if(!in_array($act, $noyz))
		{
			//进行权限校验
			$session = XSession::Get("TA_user") || XSession::Get("Cur_X_User");			
						
			if($session == null)
			{
				if($prj == 'psys')
				{
					$mod = "account";
					$act = "login";
				}else{
					if(empty($prj))$prj = "pc";
					$mod = "index";
					$act = "index";
					$isnologinerr = true;
				}			
			}elseif(isset($session['qxlist']) && !ckAccess($session['qxlist'], $prj, $mod, $act,$G_X['all_privilege']['url'])){		
			    
				//print_r($session['qxlist']);
				//exit;
				if(substr($actstr,0,4)=="ajax"||req("ajax")){
					exit(json_encode(array('result'=>'ERROR','msg'=>'您没有权限','msgcode'=>2004)));
				}
				header("Location:/html/noaccess.html");
				return;
			}
		}
		
		$G_X['modstr'] = $mod;
		$G_X['actstr'] = $act;
		$G_X['prjstr'] = $prj;
		$actstr=$act."Action";
		$modstr = $mod."Controller";
		$file = constant(strtoupper($prj)."_PATH")."controller".DIRECTORY_SEPARATOR.$modstr.".php";
		
		if(file_exists($file))
		{
			require_once $file;
		}else{
		    header('HTTP/1.1 404 Not Found');
		    //header("status: 404 Not Found");
		    exit();
			//print_r($G_X);
			//exit("文件不存在---->".$file);
		}
		
		$modobj = new $modstr();
		
		if($modobj->isajax){
			//判断访问为ajax请求
			try {
				if($isnologinerr)
				{
				    $return = array('result'=>'ERROR','msg'=>'您没有登录','msgcode'=>2005);
				}else{
				    $return=$modobj->$actstr();
				}
			} catch (Exception $e) {
				$return['result']	= 'ERROR';//SUCCESS表示成功
				$return['msg'] 		= $e->getMessage();
			}
			header('Content-type: application/json');
			exit (json_encode($return));
		}else{
		    try {
			    $modobj->$actstr();
		    }catch (Exception $e) {
			    exit($e->getMessage());
			}
		}
				
		if(in_array($modobj->forward, array("msg","msg_nologin","ajaxmsg")))
		{
			$html = 'shared'.DIRECTORY_SEPARATOR.$modobj->forward.".html";
		}else{
		    $html = $mod.DIRECTORY_SEPARATOR.$modobj->forward.".html";			
		}
		
		$modobj->smarty->display($html);
	}
}

?>