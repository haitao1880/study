<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月21日
* 文 件 名:{pingan}Controller.php
* 创建时间:15:27
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Jerry (Jerry@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要: 综合统计控制器
*/
class codesearchController extends PSys_AbstractController{
    
	public function __construct() {
		parent::__construct();
        $this->smarty->assign("gameActive","");
        $this->smarty->assign("trainActive","active");
        $this->smarty->assign("gameHidden","hidden");
        $this->smarty->assign("trainHidden","");
        $this->smarty->assign("busHidden","hidden");
        $this->smarty->assign("busActive","");
	}
    
	/**
     *
     * @do sql查询
     *
     * @pingan public 
     * @author Jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
    public function indexAction(){      
        $this->smarty->assign("active_f","codesearch");
        $this->smarty->assign("active","codesearch/index");
        $PSys_CodesearchModel = new PSys_CodesearchModel();
        $result = $PSys_CodesearchModel->GetList($where, 'id DESC', 0, 0, "*");
        
        $this->smarty->assign("data",$result["allrow"]);
        $this->forward = "index";
    }

    /**
     *
     * @do sql查询
     *
     * @pingan public 
     * @author Jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
    public function listAction(){      
        $this->smarty->assign("active_f","codesearch");
        $this->smarty->assign("active","codesearch/list");
        $PSys_CodesearchModel = new PSys_CodesearchModel();
        $result = $PSys_CodesearchModel->GetList($where, 'id DESC', 0, 0, "*");
        
        $this->smarty->assign("data",$result["allrow"]);
        $this->forward = "list";
    }

    /**
     *
     * @do ajax插入数据
     *
     * @pingan public 
     * @author Jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
    public function ajaxInsertAction(){
        $code = reqstr("code","");
        $description = reqstr("description","");
        $action = reqstr("action","");   
        $PSys_CodesearchModel = new PSys_CodesearchModel();
        $data = array();
        $rs = $PSys_CodesearchModel->getOne(array('code'=>$code));
        if($rs){
            echo '<tr id="tr_error"><td colspan="4" style="text-align:center; color:red;">编码已经存在!</td></tr>';
            exit;
        }
        $rs = $PSys_CodesearchModel->getOne(array('action'=>$action));
        if($rs){
            echo '<tr id="tr_error"><td colspan="4" style="text-align:center; color:red;">action已经存在!</td></tr>';
            exit;
        }
        $data['code'] = $code;
        $data['description'] = $description;
        $data['action'] = $action;
        $insertid = $PSys_CodesearchModel->addOne($data);
        if($insertid){
            echo '<tr id="tr_'.$insertid.'"><td style="text-align:center;">'.$code.'</td><td style="text-align:left;">'.$description.'</td>
            <td style="text-align:left;">'.$action.'</td><td style="text-align:center;">
                                        <i class="icon-plus"></i>&nbsp;<span style="cursor:pointer" onclick="add('.$insertid.')">添加</span>&nbsp;&nbsp;
                                        <i class="icon-pencil"></i>&nbsp;<span style="cursor:pointer" onclick="edit('.$insertid.')">编辑</span>&nbsp;&nbsp; 
                                        </td>  
                                    </tr>';
        }
        
    }

    /**
     *
     * @do ajax编辑数据
     *
     * @pingan public 
     * @author Jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
    public function ajaxEditAction(){
        $id = reqstr("id","");
        $type = reqstr("type","");
        $PSys_CodesearchModel = new PSys_CodesearchModel();
        $data = array();
        $rs = $PSys_CodesearchModel->getOne(array('id'=>$id));
        if($rs){
            if($type == 1){
                echo '<td><input type="text" style="width:90%" value="'.$rs['code'].'" class="form-control" id="code_'.$rs['id'].'" name="code_'.$rs['id'].'" placeholder="code"></td><td><input type="text" style="width:90%;" value="'.$rs['description'].'" class="form-control" id="description_'.$rs['id'].'" name="description_'.$rs['id'].'" placeholder="description"></td><td><input type="text" style="width:90%;" value="'.$rs['action'].'" class="form-control" id="action_'.$rs['id'].'" name="action_'.$rs['id'].'" placeholder="action"></td><td style="width:90%; text-align:center;"><button type="button" onclick="doEidtSubmit(\''.$rs['id'].'\')" class="btn btn-success">提交</button> <button type="button" class="btn btn-warning" onclick="doEidtCancel(\''.$rs['id'].'\')">取消</button></td>';
            }
            if($type == 2){
                echo '<td style="text-align:center;">'.$rs['code'].'</td><td>'.$rs['description'].'</td><td>'.$rs['action'].'</td><td style="text-align:center;"><i class="icon-plus"></i>&nbsp;<span style="cursor:pointer" onclick="add('.$rs['id'].')">添加</span>&nbsp;&nbsp;<i class="icon-pencil"></i>&nbsp;<span style="cursor:pointer" onclick="edit('.$rs['id'].')">编辑</span>&nbsp;&nbsp;</td>';
            }
        }
    }

    /**
     *
     * @do ajax编辑数据
     *
     * @pingan public 
     * @author Jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
    public function submitEditAction(){
        $id = reqstr("id","");
        $code = reqstr("code","");
        $description = reqstr("description","");
        $action = reqstr("action","");   
        $PSys_CodesearchModel = new PSys_CodesearchModel();
        $data = array();
        $cond1['code'] = $code;
        $cond1['id_<>'] = $id;
        $rs = $PSys_CodesearchModel->getOne($cond1);
        if($rs){
            echo '<td colspan="4" style="text-align:center; color:red;">编码已经存在!</td>';
            exit;
        }
        $cond2['action'] = $action;
        $cond2['id_<>'] = $id;
        $rs = $PSys_CodesearchModel->getOne($cond2);
        if($rs){
            echo '<td colspan="4" style="text-align:center; color:red;">action已经存在!</td>';
            exit;
        }
        $data['code'] = $code;
        $data['description'] = $description;
        $data['action'] = $action;
        $rs = $PSys_CodesearchModel->UpdateOne($data,array('id'=>$id));
        if($rs){
            echo '<td style="text-align:center;">'.$data['code'].'</td><td>'.$data['description'].'</td><td>'.$data['action'].'</td><td style="text-align:center;"><i class="icon-plus"></i>&nbsp;<span style="cursor:pointer" onclick="add('.$id.')">添加</span>&nbsp;&nbsp;<i class="icon-pencil"></i>&nbsp;<span style="cursor:pointer" onclick="edit('.$id.')">编辑</span>&nbsp;&nbsp;</td>'; 
        }else{
            echo '<td style="text-align:center;">'.$data['code'].'</td><td>'.$data['description'].'</td><td>'.$data['action'].'</td><td style="text-align:center;"><i class="icon-plus"></i>&nbsp;<span style="cursor:pointer" onclick="add('.$id.')">添加</span>&nbsp;&nbsp;<i class="icon-pencil"></i>&nbsp;<span style="cursor:pointer" onclick="edit('.$id.')">编辑</span>&nbsp;&nbsp;</td>';
            
        }
    }
}