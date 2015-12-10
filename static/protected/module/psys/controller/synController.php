<?php
/**
* Copyright(c) 2014
* 日    期:2014年10月18日
* 文 件 名:synController.php
* 创建时间:12:15
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Robin (Robin@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:仅用于跳转
*/
class synController extends PSys_AbstractController{
    /**
    *
    * @do 继承构造函数
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
	public function __construct(){
		parent::__construct();
	} 
    /**
    *
    * @do 跳转
    *
    * @access public 
    * @author dental
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
	//所有数据同步
	public function allsynAction(){
		 $this->forward = 'allsyn'; 
	}
	//站点列表
	public function citelistAction(){
		$m = new PSys_SynModel();		
		$pid = reqnum("pid",0);
        $page = reqnum('page',1);
		$pagesize = 20;
		if($pid==0){
			$where = '';
		}else{
        	$where = array();
        	$where["pid"] = $pid;
		}
    
        $order = " id ASC ";
        $result = $m->GetList($where, $order, $page, $pagesize, "*");
        if($result['allnum']%$pagesize){
            $last = floor($result['allnum']/$pagesize) + 1;
        }else{
           $last = $result['allnum']/$pagesize;
        }
        if($page > 1){
            $this->smarty->assign("pre",$page - 1);
        }else{
            $this->smarty->assign("pre",1);
        } 
        if($page == $last){
            $this->smarty->assign("next",$last);
        }else{
            $this->smarty->assign("next",$page + 1);
        }       
                			 
        $this->smarty->assign("last",$last);
        $this->smarty->assign("tree",$result["allrow"]);
        $this->smarty->assign("total_num",$result["allnum"]);
        $this->forward = "citelist";
	}
	//站点修改
	public function citeEditAction(){
		$id = reqnum('id', 0);
		$m = new PSys_SynModel();
		if($_POST['ispost']){
			$data = array();
			$data['citeip'] = reqstr('citeip');
			$data['citename'] = reqstr('citename');
			$data['password'] = reqstr('password');
			$data['name'] = reqstr('name');
			$data['port'] = reqnum('port');
			if($data['citename'] and $id > 0){
				$where['id'] = $id;
				$ps = $m->UpdateOne($data, $where);
				if($ps){
					header('location:/syn/allsyn');
					exit;
				}
			}
		}
		if(!empty($id)){
			$last = $m->GetOne(array('id'=>$id));
			$this->smarty->assign("last",$last);
		}
		$this->smarty->assign("action",'/syn/citeEdit');
		$this->forward = "citeEdit";		
	}
	//站点添加
	public function citeAddAction(){
		$m = new PSys_SynModel();
		if($_POST['ispost']){
			$data = array();
			$data['citeip'] = reqstr('citeip');
			$data['citename'] = reqstr('citename');
			$data['password'] = reqstr('password');
			$data['name'] = reqstr('name');
			$data['port'] = reqnum('port');
			if($data['citename']){
				$ps = $m->AddOne($data);
				if($ps){
					header('location:/syn/allsyn');
					exit;
				}
			}
		}
		$this->smarty->assign("action",'/syn/citeAdd');
		$this->forward = "citeEdit";	
	}
	
	//站点同步选择
	public function citeChoiceAction(){
		$m = new PSys_SynModel();
		$result = $m->GetList($where, $order, $page, $pagesize, "*");
		$this->smarty->assign("last",$last);
        $this->smarty->assign("tree",$result["allrow"]);
        $this->smarty->assign("total_num",$result["allnum"]);
        $this->smarty->assign("action",'/syn/citeSyn');
        $this->forward = "citeChoice";
	}
	
	//站点同步
	public function citeSynAction(){
		if($_POST['ispost']){
			$cites = reqarray('citeId');
			$error = $this->DBsynAction($cites,'');
			if($error['error']==true){
				echo "<script>alert('同步完成')</script>";
			}else{
				echo "<script>alert('同步有错误--".$error['ercontent']."')</script>";
			}
			echo "<script>window.location.href='/syn/allsyn'</script>";
			exit;
		}
	}
	
	//sql首页
	public function sqlIndexAction(){
		$m = new PSys_SynRule();
		$tbNames = $m->getDBname();
		$this->smarty->assign("tbNames",$tbNames);
		$this->forward = "sqlIndex";
	}
	
	//sql列表
	public function sqlListAction(){
		$m = new PSys_SynRule();
		$sqltype = reqstr('sqltype', 0);
		$updatetype = reqstr('updatetype', 0);
		$page = reqnum('page',1);
		$pagesize = 20;
		$where = array();
		if($sqltype){
			$where["sqltype"] = $sqltype;
		}
		if($updatetype){
			$where["updatetype"] = $updatetype;
		}
        $order = " id ASC ";
        $result = $m->GetsqlList($where);
        if($result['allnum']%$pagesize){
            $last = floor($result['allnum']/$pagesize) + 1;
        }else{
           $last = $result['allnum']/$pagesize;
        }
        if($page > 1){
            $this->smarty->assign("pre",$page - 1);
        }else{
            $this->smarty->assign("pre",1);
        } 
        if($page == $last){
            $this->smarty->assign("next",$last);
        }else{
            $this->smarty->assign("next",$page + 1);
        }       
        foreach($result['allrow'] as $key=>$val){
        	$sitesName = array();
        	$result['allrow'][$key]['sqlcontent'] = mb_substr($val['sqlcontent'],0,50)."……";
        	$sites = explode(',',$val['siteid']);
        	foreach($sites as $ke=>$val){
        		$where = array('id'=>$val);
        		$name = $m->GetsiteName($where);
        		$sitesName[] .= $name[0]['citename'];
        	}
        	$result['allrow'][$key]['siteid'] = $sitesName;
        }
        $this->smarty->assign("last",$last);
        $this->smarty->assign("tree",$result["allrow"]);
        $this->smarty->assign("total_num",$result["allnum"]);
        $this->forward = "sqlList";
	}
	
	//sql信息详情
	public function sqlDetailsAction(){
		$id = reqnum('id',0);
		$m = new PSys_SynModel();
		$where = array('id'=>$id);
		$sql = $m->getOnesql($where);
		$cites = $m->GetList('', $order, $page, $pagesize, "id,citename");
		$trueCitesId = explode(',',$sql['siteid']);
		$trueCites = array();
		$falseCites = array();
		foreach($trueCitesId as $id){
			foreach($cites['allrow'] as $key=>$cite){
				if($cite['id'] == $id){
					$trueCites[] = $cite;
				}
			}
		}
		foreach($cites['allrow'] as $key=>$cite){
			foreach($trueCitesId as $id){
				if($id == $cite['id']){
					unset($cites['allrow'][$key]);
				}
			}
		}
		$this->smarty->assign('sql',$sql);
		$this->smarty->assign("action",'/syn/oneSqlSyn');
		$this->smarty->assign("trueCites",$trueCites);
		$this->smarty->assign("falseCites",$cites['allrow']);
		$this->forward = "sqlDetails";
	}
	
	//单条SQL语句同步
	public function oneSqlSynAction(){
		if($_POST['ispost'] && $_POST['cid']){
			$id = reqnum('id',0);
			$cid = reqarray('cid');
			$error = $this->DBonesynAction($cid,$id);
			if($error['error']==true){
				echo "<script>alert('同步完成')</script>";
			}else{
				echo "<script>alert('同步有错误--".$error['ercontent']."')</script>";
			}
			echo "<script>window.location.href='/syn/sqlIndex'</script>";
			exit;
		}
		header('location:/syn/sqlIndex');
		exit;
	}
	
	//表同步选择
	public function tableChoiceAction(){
		$m = new PSys_SynRule();
		$tbNames = $m->getDBname();
		$n = new PSys_SynModel();
		$result = $n->GetList($where, $order, $page, $pagesize, "*");
        $this->smarty->assign("cites",$result["allrow"]);
		$this->smarty->assign("tbNames",$tbNames);
		$this->smarty->assign("action",'/syn/tableSyn');
		$this->forward = "tableChoice";
	}
	
	//表同步
	public function tableSynAction(){
		if($_POST['ispost']){
			$cites = reqarray('citeId');
			$tableName = reqarray('tableName');
			$error = $this->DBsynAction($cites,$tableName);
			if($error['error']==true){
				echo "<script>alert('同步完成')</script>";
			}else{
				echo "<script>alert('同步有错误--".$error['ercontent']."')</script>";
			}
			echo "<script>window.location.href='/syn/sqlIndex'</script>";
			exit;
		}
	}
	
	//数据库连接方法
	/*@cuteid array
	 *@databaseName array
	 */
	public function DBsynAction($citeid,$databaseName=''){
		$m = new PSys_SynModel();
		$d = new PSys_SynRule();
		$error = array();
		$error['error'] = true;
		foreach($citeid as $id){
			$cites = array();
			$where = array('id'=>$id);
			$cites = $m->GetOne($where,'*');
			if(!$databaseName){
				$sqls = $d->getcitesql($id);
				foreach($sqls as $key=>$sql){
					$db_config["hostname"] = $cites['citeip']; //服务器地址
			   		$db_config["username"] = $cites['name']; //数据库用户名
			    	$db_config["password"] = $cites['password']; //数据库密码
			    	$db_config["database"] = $sql['database']; //数据库名称
			    	$db_config["charset"] = "utf8";//数据库编码
			    	$db_config["pconnect"] = 0;//开启持久连接
			    	$db_config["log"] = 0;//开启日志
			    	$db_config["logfilepath"] = './';//开启日志
					$db = new PSys_dbsynRule($db_config);
					$result = $db->query($sql['sqlcontent']);
					if(!$result){
						$sqlwhere = array('id'=>$sql['id']);
						$data = array('error'=>$sql['error'].','.$id);
						$d->Update($data,$sqlwhere);
						$error['error'] = false;
						$error['ercontent'].= "SQL语句同步失败编号:".$sql['id']."。";
					}else{
						$sqlwhere = array('id'=>$sql['id']);
						$data = array('siteid'=>$sql['siteid'].','.$id);
						$d->Update($data,$sqlwhere);
					}
				}
			}else{
				foreach($databaseName as $dataName){
					$sqls = $d->getcitesql($id,$dataName);
					foreach($sqls as $key=>$sql){
						$db_config["hostname"] = $cites['citeip']; //服务器地址
				   		$db_config["username"] = $cites['name']; //数据库用户名
				    	$db_config["password"] = $cites['password']; //数据库密码
				    	$db_config["database"] = $sql['database']; //数据库名称
				    	$db_config["charset"] = "utf8";//数据库编码
				    	$db_config["pconnect"] = 0;//开启持久连接
				    	$db_config["log"] = 0;//开启日志
				    	$db_config["logfilepath"] = './';//开启日志
						$db = new PSys_dbsynRule($db_config);
						$result = $db->query($sql['sqlcontent']);
						if(!$result){
							$sqlwhere = array('id'=>$sql['id']);
							$data = array('error'=>$sql['error'].','.$id);
							$d->Update($data,$sqlwhere);
							$error['error'] = false;
							$error['ercontent'].= "SQL语句同步失败编号:".$sql['id']."。";
						}else{
							$sqlwhere = array('id'=>$sql['id']);
							$data = array('siteid'=>$sql['siteid'].','.$id);
							$d->Update($data,$sqlwhere);
						}
					}
				}
			}
		}
		return $error;
	}
	
	//数据库连接方法
	/*@cuteid array
	 *@databaseName array
	 */
	public function DBonesynAction($citeid,$sqlid){
		$m = new PSys_SynModel();
		$d = new PSys_SynRule();
		$error = array();
		$error['error'] = true;
		foreach($citeid as $id){
			$cites = array();
			$where = array('id'=>$id);
			$cites = $m->GetOne($where,'*');
			$swhere = array('id'=>$sqlid);
			$sql = $d->GetOnesql('*',$swhere);
			$db_config["hostname"] = $cites['citeip']; //服务器地址
			$db_config["username"] = $cites['name']; //数据库用户名
			$db_config["password"] = $cites['password']; //数据库密码
			$db_config["database"] = $sql['database']; //数据库名称
			$db_config["charset"] = "utf8";//数据库编码
			$db_config["pconnect"] = 0;//开启持久连接
			$db_config["log"] = 0;//开启日志
			$db_config["logfilepath"] = './';//开启日志
			$db = new PSys_dbsynRule($db_config);
			$result = $db->query($sql['sqlcontent']);
			if(!$result){
				$sqlwhere = array('id'=>$sqlid);
				$data = array('error'=>$sql['error'].','.$id);
				$d->Update($data,$sqlwhere);
				$error['error'] = false;
				$error['ercontent'].= "SQL语句同步失败编号:".$sql['id']."。";
			}else{
				$sqlwhere = array('id'=>$sqlid);
				$data = array('siteid'=>$sql['siteid'].','.$id);
				$d->Update($data,$sqlwhere);
			}
		}
		return $error;
	}
}