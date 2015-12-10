<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月14日
* 文 件 名:{PSys_Member}Model.php
* 创建时间:13:46
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:jerry (jerry@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:访问统计模型层
*/
class PSys_MemberModel extends PSys_AbstractModel{
    /**
    *
    * 继承构造函数
    *
    * @Member public 
    * @author jerry
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
    * 获取时间段内留存率数据
    *
    * @Member public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
	public function getRate($start){
        $PSys_MemberRule = new PSys_MemberRule();
        $tempday = date('Y-m-d',$start);
        $sday = $start;
        $eday = $start + 86400-1;
        $sday1 = $start + 86400*1;
        $eday1 = $start + 86400*2-1;
        $sday3 = $start + 86400*3;
        $eday3 = $start + 86400*4-1;
        $sday7 = $start + 86400*7;
        $eday7 = $start + 86400*8-1;
        $sday30 = $start + 86400*30;
        $eday30 = $start + 86400*31-1;
        $sql = "SELECT client FROM rhc_client WHERE cday = '".$tempday."'";
        $new_user = $num1 = $num3 = $num7 = $num30 = 0;
        $newArr = array();
        $rtArr = $PSys_MemberRule->rateQuery($sql);
        foreach($rtArr as $v){
            $tempSql1 = "SELECT id FROM rhc_game_platform WHERE client = '".strtoupper($v['client'])."' AND type = 1 AND create_time BETWEEN $sday1 AND $eday1 limit 1";
            $tempSql3 = "SELECT id FROM rhc_game_platform WHERE client = '".strtoupper($v['client'])."' AND type = 1 AND create_time BETWEEN $sday3 AND $eday3 limit 1";
            $tempSql7 = "SELECT id FROM rhc_game_platform WHERE client = '".strtoupper($v['client'])."' AND type = 1 AND create_time BETWEEN $sday7 AND $eday7 limit 1";
            $tempSql30 = "SELECT id FROM rhc_game_platform WHERE client = '".strtoupper($v['client'])."' AND type = 1 AND create_time BETWEEN $sday30 AND $eday30 limit 1";
            $tempRs1 = $PSys_MemberRule->rateQuery($tempSql1); if($tempRs1){$num1++;}
            $tempRs3 = $PSys_MemberRule->rateQuery($tempSql3); if($tempRs3){$num3++;}
            $tempRs7 = $PSys_MemberRule->rateQuery($tempSql7); if($tempRs7){$num7++;}
            $tempRs30 = $PSys_MemberRule->rateQuery($tempSql30); if($tempRs30){$num30++;}
        }
        $rtArr['new_user'] = count($rtArr);
        $rtArr['num1'] = $num1;
        $rtArr['num3'] = $num3;
        $rtArr['num7'] = $num7;
        $rtArr['num30'] = $num30;
        return $rtArr;
	}
    
    /**
    *
    * 获取时间段内流失率数据
    *
    * @Member public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
	public function getLossrate($miketime){
        $PSys_MemberRule = new PSys_MemberRule();
        $sweek1 = $miketime - 7*86400;
        $eweek1 = $miketime-1;
        $sweek2 = $miketime - 14*86400;
        $eweek2 = $miketime - 7*86400-1;
        $smonth1 = $miketime - 30*86400;
        $emonth1 = $miketime-1;
        $smonth2 = $miketime - 60*86400;
        $emonth2 = $miketime - 30*86400-1;
        $weeksql1 = "SELECT DISTINCT client FROM rhc_game_platform WHERE create_time BETWEEN $sweek1 AND $eweek1 AND type = 1";
        $weeksql2 = "SELECT DISTINCT client FROM rhc_game_platform WHERE create_time BETWEEN $sweek2 AND $eweek2 AND type = 1";
       
        $monthsql1 = "SELECT DISTINCT client FROM rhc_game_platform WHERE create_time BETWEEN $smonth1 AND $emonth1 AND type = 1";
        $monthsql2 = "SELECT DISTINCT client FROM rhc_game_platform WHERE create_time BETWEEN $smonth2 AND $emonth2 AND type = 1";
 
        $rtArr = $week1 = $week2 = $month1 = $month2 = array();
        $wkRs1 = $PSys_MemberRule->rateQuery($weeksql1);
        $wkRs2 = $PSys_MemberRule->rateQuery($weeksql2);
        $monRs1 = $PSys_MemberRule->rateQuery($monthsql1);
        $monRs2 = $PSys_MemberRule->rateQuery($monthsql2);
        foreach($wkRs1 as $k=>$v){$week1[$k] = strtoupper($v['client']);}
        foreach($wkRs2 as $k=>$v){$week2[$k] = strtoupper($v['client']);}
        foreach($monRs1 as $k=>$v){$month1[$k] = strtoupper($v['client']);}
        foreach($monRs2 as $k=>$v){$month2[$k] = strtoupper($v['client']);}
        $rtnWeek = $rtnMonth = 0;
        foreach($week2 as $v){
            if(in_array($v,$week1)){
                $rtnWeek++;
            }
        }
        foreach($month2 as $v){
            if(in_array($v,$month1)){
                $rtnMonth++;
            }
        }
        $rtArr['weeknums'] = count($week2);
        $rtArr['weekloss'] = count($week2)-$rtnWeek;
        $rtArr['wklossrate'] = $rtArr['weeknums'] ? round($rtArr['weekloss']/$rtArr['weeknums']*100,2):0;
        $rtArr['monthnums'] = count($month2);
        $rtArr['monthloss'] = count($month2)-$rtnMonth;
        $rtArr['monlossrate'] = $rtArr['monthnums'] ? round($rtArr['monthloss']/$rtArr['monthnums']*100,2):0;
        return $rtArr;
	}
    
}