<?php
class Psys_PagerecordRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
	}
       /** 获取最小值  **/
        function get_max_date(){
           static $maxdate="";
            if(!$maxdate){
               $sql="select max(date) as date from rha_pagerecord;";
               $back= $this->Query($sql);
               if(empty($back[0]["date"])){$maxdate= date("Y-m-d",time());}else{$maxdate= $back[0]["date"];  }
            } 
            return $maxdate;
        }
        /** 获取最大值  **/
       function get_min_date(){
           static $mindate='';
           if(!$mindate){
               $sql="select min(date) as date from rha_pagerecord;";
               $back= $this->Query($sql);
               if(empty($back[0]["date"])){$mindate= date("Y-m-d",time());}else{$mindate= $back[0]["date"];  }
           }
          return    $mindate;
        }
        
      function list_query($sql){
        return   $this->Query($sql);
      }  
}