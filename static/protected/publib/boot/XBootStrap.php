<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:XBootStrap.php
* 创建时间:下午5:35:35
* 字符编码:UTF-8
* 版本信息:$Id: XBootStrap.php 10 2014-06-13 07:03:39Z tony_ren $
* 修改日期:$LastChangedDate: 2014-06-13 15:03:39 +0800 (周五, 13 六月 2014) $
* 最后版本:$LastChangedRevision: 10 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/protected/publib/boot/XBootStrap.php $
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
		
		global $G_X;		
				
		$noyz = @$G_X['allow_project'][$prj]['nologin'][$mod];
		if(!is_array($noyz))$noyz = array();
		
		$actstr = $act == '' ? 'index':$act;
		$actstr .= "Action";
	
		//echo $G_X['modstr']."<br />";
		$session=XSession::Get("TA_user");		
		
		//开发阶段取消验证
		//if(!in_array($act, $noyz))
		if(!in_array($act, $noyz))
		{
			
			//进行权限校验
			$session = XSession::Get("TA_user");
            if($session){
                $app_array = $session['app_array'];
                $session['app_array'] = $app_array == 'All' ? 'All' : eval("return {$app_array};");
                if($session['app_array'] != 'All'){
                    $session['app_array'] = array_merge($session['app_array'],$G_X["allow_project"][$prj]["filter"]);
                }
			}
            
            
			if($session == null)
			{
				if($mod == 'admin')
				{
					header("Location:".PUC_BASE_URL."admin/login");
					return;
				}
				
				if($prj == 'psys'){
					header("Location:".PSYS_BASE_URL."index/login");
					return;
				}
				
				if(empty($prj))$prj = "pweb";
				$mod = "index";
				$act = "index";
				$isnologinerr = true;				
			}elseif(!ckAccess($session['app_array'], $prj, $mod, $act,$G_X['all_privilege']['url'])){		
				if(substr($actstr,0,4)=="ajax"||req("ajax")){
					exit(json_encode(array('result'=>'ERROR','msg'=>MsgInfoConst::GetMsg(2004,'en'),'msgcode'=>2004)));
				}
			    echo "<script>window.location.href='/jump/index?type=errors&message=privilege';</script>";
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
				    $return = array('result'=>'ERROR','msg'=>MsgInfoConst::GetMsg(2005,$modobj->culture),'msgcode'=>2005);
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
			$modobj->smarty->display($modobj->forward.".html");
		}else{
			$html=$mod.DIRECTORY_SEPARATOR.$modobj->forward.".html";
			$modobj->smarty->display($html);
		}
	}
}

?>