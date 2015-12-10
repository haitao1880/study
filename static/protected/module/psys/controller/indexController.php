<?php
/**
* Copyright(c) 2015
* 日    期:2015年05月19日
* 文 件 名:{index}Controller.php
* 创建时间:14:12
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Nick (nick@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要: dashboard基础功能管理
*/
class indexController extends PSys_AbstractController{
    
	public function __construct(){
		parent::__construct();
		$this->smarty->assign("gameActive","active");
        $this->smarty->assign("trainActive","");
        $this->smarty->assign("gameHidden","");
        $this->smarty->assign("trainHidden","hidden");
        $this->smarty->assign("busHidden","hidden");
        $this->smarty->assign("marketHidden","hidden");
        $this->smarty->assign("marketActive","");
        $this->smarty->assign("busActive","");
	}
	/**
     *
     * @do 后台首页
     *
     * @access public 
     * @author Nick
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function indexAction(){
        $this->smarty->assign("active","index/index");
		$this->forward = "index";
	}
	
    /**
    *
    * @do 后台头部
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
	public function headerAction()
	{
		$this->smarty->assign('admin_name',$this->cur_user['nickname']);
		$this->forward = "header";
	}
    
    /**
    *
    * @do 修改密码
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function cpwdAction(){
        
        if(reqstr('ispost')){
            
            $PSys_AccountModel = new PSys_AccountModel();
            
            $where = array();
            $where['username'] = $this->cur_user['username'];
            $where['passwd']   = $PSys_AccountModel->CryptPasswd(reqstr('oldpwd'));
            
            //判断密码是否正确
            $result = $PSys_AccountModel->IsExists($where);
            if($result){
                
                $data = array();
                $data['passwd'] = $PSys_AccountModel->CryptPasswd(reqstr('passwd'));
                
                
                $where = array();
                $where['username'] = $this->cur_user['username'];
                
                $result = $PSys_AccountModel->UpdateOne($data,$where);
                if($result){
                    $this->jump('Set new password is success.','success','/index/main');
                }else{
                    $this->jump('Set new password is fail.','errors','/index/cpwd');
                }
                
            }else{
                
                $this->jump('Your old password is error.','errors','/index/cpwd');
                
            }
            
        }
        
        $this->forward = "cpwd";
        
    }
	
    /**
    *
    * @do system login
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function loginAction(){
    	//当有用户请求登陆时
    	if($this->ispost){
   			$username = reqstr("username",FALSE);
   			$password = reqstr("password",FALSE);

   			if(!$username || !$password) {
                header("Location : /index/login");
   			}

   			//用户model
   			$userModel = new PSys_UserModel();
   			$result = $userModel->loginVerify($username, $password);
   			
   			if($result && $result['status'] == 1){
   				XSession::Set("TA_user", $result);
   				$this->jump("Welcome ".$result['user_nick'], "success", "/index/index",1 );
   				exit;
   			}else{
   				
   				header("Location : /index/login");
   			}   			
    	}
    	$this->forward = "login";    	
    }
    
    /**
    *
    * @do system loginout
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function loginoutAction(){  
    	//清除session
    	session_destroy();
        $this->jump("Loginout successed.", "success", "/index/login",1 );
        exit;
    	
    }
    
    /**
    *
    * @do my data
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function mydataAction(){
        
        GLOBAL $G_X;
        
        $user = XSession::Get("TA_user");
        
        $userModel = new PSys_UserModel();
        
        if($this->ispost){
            $where = array();
            $where['user_id'] = $user['id'];
            
            $data = array();
            $data['nick'] = reqstr("nick","");
            $data['age'] = reqstr("age","");
            $data['phone'] = reqstr("phone","");
            $data['address'] = reqstr("address","");
            $data['photo'] = reqstr("photo","");
            $return = $userModel->UpdateOne($data, $where, "ta_user_information");
            
            if($return){
   				$this->jump("Edit success.", "success", "/index/mydata",1 );
   			}else{
   				$this->jump("Edit failed.", "errors", "/index/mydata",1 );
   			}
            
            exit;
            
        }
        
        $where = array();
        $where['id'] = $user['id'];

        $data = $userModel->GetOne($where,"*","view_user");
        
        $timestamp = time();
        $timestamp_token = md5($G_X['upload']['unique_salt'] . $timestamp);
        
        $this->smarty->assign("timestamp",$timestamp);
        $this->smarty->assign("timestamp_token",$timestamp_token);

        $this->smarty->assign("data",$data);
        $this->forward = "mydata";
        
    }
    
    /**
    *
    * @do work plan
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function workplanAction(){
        
        
        $this->forward = "workplan";
        
    }
    
}
