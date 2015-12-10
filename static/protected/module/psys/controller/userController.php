<?php
/**
* Copyright(c) 2015
* 日    期:2015年05月23日
* 文 件 名:{menu}Controller.php
* 创建时间:10:52
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Nick (nick@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要: 用户管理(后台专用)
*/
class userController extends PSys_AbstractController{
    
	public function __construct() {
		parent::__construct();
	}
    
	/**
     *
     * @do 用户dashboard 列表
     *
     * @access public 
     * @author Nick
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function indexAction(){	
	   
        $PSys_UserModel = new PSys_UserModel();
        
        if($this->ispost){
         
            //排序保存
            $sort = reqarray("sort");
            $PSys_UserModel->saveSortOption($sort);
            
            $this->jump('Save sort option success.','success','/menu/index');
            exit;
            
        }
        
        $where = array();
        $order = "id DESC";
        $page  = reqnum("p", 1);
        $pagesize = reqnum("n", 14);
        $field = "*";
        $table = "view_user";
        
        //用户列表
        $userlist = $PSys_UserModel->getList($where, $order, $page, $pagesize, $field, $table);
        $userlist['pageView'] = $this->pageView($userlist['allnum'], $param, $page, $pagesize);
        
        $this->smarty->assign("userlist",$userlist);
        $this->smarty->assign("active","user/index");
		$this->forward = "index";
        
	}
    
    /**
     *
     * @do 添加新用户
     *
     * @access public 
     * @author Nick
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function addAction(){
	   
        GLOBAL $G_X;
        
        $admins = XSession::Get("TA_user");
	   
        $PSys_UserModel = new PSys_UserModel();
	   
        if($this->ispost){
            
            $data['username']       = reqstr("username","");
            $data['password']       = reqstr("password","");
            $data['email']          = reqstr("email","");
            $data['role_id']        = reqnum("role_id",0);
            
            $data['nick']      = reqstr("nick","");
            $data['phone']      = reqstr("phone","");
            $data['address']      = reqstr("address","");
            $data['photo']      = reqstr("photo","");
            $data['age']      = reqstr("age",0);
            $data['sex']      = reqnum("sex",1);
        
            $data['createtime'] = time();
            $data['creater_id'] = $admins['id'];
            
            if(!$data['username'] || !$data['password'] || !$data['email'] || !$data['role_id']){
                $this->jump('The user information is error.','errors','/user/add');
                exit;
            }
            
            $data['password'] = md5($G_X['passAddTo'] . $data['password']);
            
            $return = $PSys_UserModel->MAddUser($data);
            
            if($return['output'] == 1){
                $this->operateLogs(1);
                $this->jump('Create the user successed.','success','/user/index');
            }else{
                $this->operateLogs(0);
                $this->jump('Create the user failed.','errors','/user/index');
            }
            
            exit;
            
        }
        
        $PSys_RoleModel = new PSys_RoleModel();
        
        $where = array();
        $order = "role_id ASC";
        
        $role = $PSys_RoleModel->GetList($where, $order, 1, 100, "role_id,rolename");
        
        $timestamp = time();
        $timestamp_token = md5($G_X['upload']['unique_salt'] . $timestamp);
        
        $this->smarty->assign("timestamp",$timestamp);
        $this->smarty->assign("timestamp_token",$timestamp_token);
        
        $this->smarty->assign("role",$role);
        $this->smarty->assign("active","user/index");
		$this->forward = "add";
	}
    
    /**
     *
     * @do 编辑用户
     *
     * @access public 
     * @author Nick
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function editAction(){
	   
        GLOBAL $G_X;
	   
        $PSys_UserModel = new PSys_UserModel();
	   
        if($this->ispost){
            
            $data['id']       = reqnum("id",0);
            
            $data['password']       = reqstr("password","");
            $data['email']          = reqstr("email","");
            $data['role_id']        = reqnum("role_id",0);
            
            $data['nick']      = reqstr("nick","");
            $data['phone']      = reqstr("phone","");
            $data['address']      = reqstr("address","");
            $data['photo']      = reqstr("photo","");
            $data['age']      = reqstr("age",0);
            $data['sex']      = reqnum("sex",1);
            
            if(!$data['email'] || !$data['role_id']){
                $this->jump('The user information is error.','errors','/user/edit?id='.$data['id']);
                exit;
            }
            
            //是否有修改密码
            if($data['password']){
                $data['password'] = md5($G_X['passAddTo'] . $data['password']);
            }
            
            $return = $PSys_UserModel->MEditUser($data);
  
            if($return['output'] == 1){
                $this->operateLogs(1);
                $this->jump('Edit the user successed.','success','/user/index');
            }else{
                $this->operateLogs(0);
                $this->jump('Edit the user failed.','errors','/user/index');
            }
            
            exit;
            
        }
        
        $where = array();
        $where['id'] = reqnum("id",0);
        
        if($where['id'] == 0){
            $this->jump('The user id is error.','errors','/user/index');
            exit;
        }
        
        $data = $PSys_UserModel->GetOne($where, "*", "view_user");
        
        $PSys_RoleModel = new PSys_RoleModel();
        
        $where = array();
        $order = "role_id ASC";
        
        $role = $PSys_RoleModel->GetList($where, $order, 1, 100, "role_id,rolename");
        
        $timestamp = time();
        $timestamp_token = md5($G_X['upload']['unique_salt'] . $timestamp);
        
        $this->smarty->assign("timestamp",$timestamp);
        $this->smarty->assign("timestamp_token",$timestamp_token);
        
        $this->smarty->assign("role",$role);
        $this->smarty->assign("data",$data);
        $this->smarty->assign("active","user/index");
		$this->forward = "edit";
	}
    
    /**
    *
    * @do 删除菜单
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function deleteAction(){
        
        $id = reqnum("id",0);
        
        if($id == 0){
            $this->jump('The user id is error.','errors','/user/index');
            exit;
        }

        if($id == 1){
            $this->jump('Admin can not be delete.','errors','/user/index');
            exit;
        }
        
        //删除
        $PSys_UserModel = new PSys_UserModel();

        $return = $PSys_UserModel->DeleteUser($id);
        
        if($return){
            $this->operateLogs(1);
            $this->jump('Delete success.','success','/user/index');
        }else{
            $this->operateLogs(0);
            $this->jump('Delete failed.','errors','/user/index');
        }
        
        exit;
        
    }
	
}