<?php
class Psys_FinRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
		$this->SetDb('db-rht_idc');
		$this->_dbprefix = 'rhi_';
	}
	/**
	 * 拉取订单列表
	 * @param number $page
	 * @param number $pagesize
	 */
	public function GetOrderList($food,$video,$music,$game,$app){

		$sqlf = 'select i.ctime,f.id as productid,f.fname as name,i.price,i.pnum ';
		$sqlf .='from '.$this->GetPreFix().'orderinfo as i left join '.$this->GetPreFix().'foodish as f on i.productid=f.id ';
		$sqlf .= 'where i.status=1 and i.producttype=? ';
		$data['foodslist'] = $this->Query($sqlf,array($food));

		$sqlv = 'select i.ctime,v.id as productid,v.vname as name,i.price,i.pnum ';
		$sqlv .='from '.$this->GetPreFix().'orderinfo as i left join '.$this->GetPreFix().'videos as v on i.productid=v.id ';
		$sqlv .= 'where i.producttype=? ';
		$data['videoslist'] = $this->Query($sqlv,array($video));

		$sqlm = 'select i.ctime,m.id as productid,m.mname as name,i.price,i.pnum ';
		$sqlm .='from '.$this->GetPreFix().'orderinfo as i left join '.$this->GetPreFix().'music as m on i.productid=m.id ';
		$sqlm .= 'where i.producttype=? ';
		$data['musicslist'] = $this->Query($sqlm,array($music));

		$sqlg = 'select i.ctime,a.appid as productid,a.appname as name,i.price,i.pnum ';
		$sqlg .='from '.$this->GetPreFix().'orderinfo as i left join '.$this->GetPreFix().'apps as a on i.productid=a.id ';
		$sqlg .= 'where i.producttype=? ';
		$data['gameslist'] = $this->Query($sqlg,array($game));

		$sqla = 'select i.ctime,a.appid as productid,a.appname as name,i.price,i.pnum ';
		$sqla .='from '.$this->GetPreFix().'orderinfo as i left join '.$this->GetPreFix().'apps as a on i.productid=a.id ';
		$sqla .= 'where i.producttype=? ';
		$data['appslist'] = $this->Query($sqla,array($app));

		return $data;
	}
	
	
	//交易明细查询
	public function Tralist($producttype,$ifsucc,$order,$page,$pagesize)
	{
		$this->SetTable('rhi_order');
		$offset = ($page-1)*$pagesize;
		$sql = 'select * from ' . $this->_table;
		
		if(!($producttype !== '' || $ifsucc !== ''))
		{
			$sql = $sql . $order;
		}
		else 
		{
			$sql = $sql . ' where ' . $producttype . $ifsucc . $order;
		}
		$sqlend = $sql . ' limit ' . $offset . ',' . $pagesize;
		
		
		$c_sql = 'select count(*) as allnum from ('.$sql.') as t';
		//echo $c_sql;exit;
		$list = $this->Query($sqlend);
		$allnum = $this->FetchColOne($c_sql);
		

		return array('allrow'=>$list,'allnum'=>$allnum);
	}
	
	//交易查询列表
	public function inquiryList($orderguid,$username,$producttype,$ifsucc,$order,$startTime,$page,$pagesize)
	{
		$this->SetTable('rhi_order');
		$offset = ($page-1)*$pagesize;
		$sql = 'select * from ' . $this->_table;
		
		if(!($producttype !== '' || $ifsucc !== '' || $orderguid !== '' || $username !== '' || $startTime !== ''))
		{
			$sql = $sql . $order;
		}
		else 
		{
			$sql = $sql . ' where ' . $orderguid . $username . $producttype . $ifsucc . $startTime . $order;
		}
		$sqlend = $sql . ' limit ' . $offset . ',' . $pagesize;
		
		
		$c_sql = 'select count(*) as allnum from ('.$sql.') as t';
		//echo $c_sql;exit;
		$list = $this->Query($sqlend);
		$allnum = $this->FetchColOne($c_sql);
		

		return array('allrow'=>$list,'allnum'=>$allnum);
	}
	
	/**
	 * 日期交易
	 */
	public function dateList($date,$order,$ifsucc,$page,$pagesize)
	{
		$this->SetTable('rhi_order');
		$offset = ($page-1)*$pagesize;
		$sql = 'select * from ' . $this->_table;
		
		$sql .=  ' where ' . $date . $ifsucc;
		$sqlend = $sql . ' limit ' . $offset . ',' . $pagesize;
		$c_sql = 'select count(*) as allnum from (' . $sql . ') as t';
		$list = $this->Query($sqlend);
		$allnum = $this->FetchColOne($c_sql);
		
		return array('allrow'=>$list,'allnum'=>$allnum);
	}
	
	
	
	public function getdatelist($start,$end,$type)
	{
		$this->SetTable('rhi_order');
		//$sql = "select FROM_UNIXTIME( ctime, '%Y-%m-%d' ) as day,count(*) as num from " . $this->_table . ' where ctime > \'' . $start . '\' AND ctime < \'' . $end . '\' group by day';
		if($type == 3)
		{
			$sql = "select FROM_UNIXTIME(ctime,'%Y') as year,count(*) as num FROM " .$this->_table . ' GROUP BY year';
			//echo $sql;exit;
		}
		else if($type == 2)
		{
			$sql = "select FROM_UNIXTIME( ctime, '%m' ) as month,count(*) as num from " . $this->_table . ' where ctime > \'' . $start . '\' AND ctime < \'' . $end . '\' group by month';
		}
		else
		{
			$sql = "select FROM_UNIXTIME( ctime, '%d' ) as day,count(*) as num from " . $this->_table . ' where ctime > \'' . $start . '\' AND ctime < \'' . $end . '\' group by day';
		}
		//echo $sql;exit;
		$list = $this->Query($sql);
		//var_dump($list);exit;
		return $list;
	}
	
	
	
	
	/**
	 * 获取第三方订单列表
	 * @param $pid:第三方应用ID
	 */
	public  function GetThreeOrderList($pid,$ifsucc,$stime='',$totime=''){
		$sqlf = 'select i.ctime,i.productid, i.producttype as type,f.fname as name,i.price,i.pnum,i.totalprice,i.ifsucc ';
		$sqlf .='from '.$this->GetPreFix().'order as i left join '.$this->GetPreFix().'foodish as f on i.productid=f.id ';
		$sqlf .= 'where i.productid=? ';
		if(!empty($stime)){
			$sqlf .=' and i.ctime >='.$stime;
		}
		if(!empty($totime)){
			$sqlf .=' and i.ctime <='.$totime;
		}
		if($ifsucc == true){
			$sqlf .=' and i.ifsucc=1';
		}
		$data = $this->Query($sqlf,array($pid));
		return $data;
	}
}