<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月4日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:Psys_AbstractController.php                                                
* 创建时间:下午3:07:28                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: Psys_AbstractController.php 626 2014-07-09 09:06:35Z tony_ren $                                                 
* 修改日期:$LastChangedDate: 2014-07-09 17:06:35 +0800 (周三, 09 七月 2014) $                                     
* 最后版本:$LastChangedRevision: 626 $                                 
* 修 改 者:$LastChangedBy: tony_ren $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/controller/Psys_AbstractController.php $                                            
* 摘    要:网站抽象类                                                       
*/
class Psys_AbstractController extends AbstractController
{   

    protected $mailhost = 'mail.rockhippo.cn'; 
    protected $mailuser = "logs@rockhippo.cn";
    protected $mailpassword = "rockhippo";
    protected $mailto = "yunwei@rockhippo.cn"; 
    //protected $mailto = "terry@rockhippo.cn";
	/**
	 * 当前路径
	 * @var array
	 */
	public $cur_path_arr = null;
	public function __construct() 
	{
		parent::__construct("psys");
		$this->smarty->assign("cur_prj_var","psys");
        $db = new Psys_StationRule();
        $totalregnum =$db->TotalRegNum();
        if (!$totalregnum['new']) {
            $totalregnum['new'] = '...';
        }
        $this->smarty->assign("totalregnum",$totalregnum['total']);
        $this->smarty->assign("newregnum",$totalregnum['new']);
	}

    
	/**
     * 分页数据显示
     * @param num $allcount 总条数
     * @param num $page
     * @param num $pagesize
     * @param num $cursize  当前页的数据条数
     */
     public function paging($allcount,$page,$pagesize,$cursize){
        $allpage = ceil($allcount/$pagesize);

        $ppage = $page - 1;
        $npage = $page + 1;
        $fpage = '';
        if ($page>1) {
        	$fpage .='<div class="btn btn-default tooltips btn-group" role="group" aria-label="..." style="margin:0;float: left;" page="'.$ppage.'">上一页</div>';
        }else{
        	$fpage .='<div class="btn btn-default tooltips btn-group disabled" role="group" aria-label="..." style="margin:0;float: left;">上一页</div>';
        }

        $fenye = array();//存放分页导航地址
        $fenye[0] = '<div class="btn btn-default tooltips btn-group active" role="group" aria-label="..." style="margin:0;float: left;" page="'.$page.'">'.$page.'</div>';
       
        //循环分页导航
        
        for($left=$page-1,$right=$page+1;($left>=1||$right<=$allpage) && count($fenye)<3;){
            if($left >= 1){
            	$leftpage = '<div class="btn btn-default tooltips btn-group" role="group" aria-label="..." style="margin:0;float: left;" page="'.$left.'">'.$left.'</div>';
                array_unshift($fenye,$leftpage);
                $left -= 1;
            }

            if($right <= $allpage){
            	$rightpage = '<div class="btn btn-default tooltips btn-group" role="group" aria-label="..." style="margin:0;float: left;" page="'.$right.'">'.$right.'</div>';
                array_push($fenye,$rightpage);
                 $right += 1;
                }
            }
        $fpage .= implode(' ', $fenye);

        if ($page < $allpage) {
        	$fpage .='<div class="btn btn-default tooltips btn-group" role="group" aria-label="..." style="margin:0;float: left;" page="'.$npage.'">下一页</div>';
        }else{
            $fpage .='<div class="btn btn-default tooltips btn-group disabled" role="group" aria-label="..." style="margin:0;float: left;">下一页</div>';
        }
        return $fpage;
    }

    protected function sendemail($title,$mailbody)
    {
        $mail = new XMail();
        return $mail->SendEmail($this->mailto,$title,$mailbody);
    }
}
?>