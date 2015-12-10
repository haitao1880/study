<?php
$tongji_dsn = 'mysql:host=localhost;port=3306;dbname=rht_tongji';
$tongji_conn = new PDO($tongji_dsn,'root','password');
$idc_dsn = 'mysql:host=localhost;port=3306;dbname=rht_idc';
$idc_conn = new PDO($idc_dsn,'root','password');
$predate = date('Y-m-d',strtotime('-1 days'));
$tempPredate = date('Ymd',strtotime('-1 days'));
$currentdate = date('Y-m-d');
$premiketime = strtotime($predate);
$curmiketime = strtotime($currentdate)-1;
$sql = '
    SELECT start.total_start,submit.total_submit FROM
	(SELECT count(id) AS total_start FROM rhi_answer WHERE cday = "'.$tempPredate.'" AND type = 1 ) AS start,
	(SELECT count(DISTINCT mobile) AS total_submit FROM rhi_answer WHERE cday = "'.$tempPredate.'" AND type = 2 ) AS submit';
$rs = $idc_conn->query($sql);
$rs->setFetchMode(PDO::FETCH_ASSOC);
$row = $rs->fetch();
$total_start = $row['total_start'];
$total_submit = $row['total_submit'];
$sql_1 = 'SELECT answer,count(id) AS total_submit FROM rhi_answer WHERE cday = "'.$tempPredate.'" AND type = 2 AND ques_id = 1 GROUP BY answer';
$rs = $idc_conn->query($sql_1);
$rs->setFetchMode(PDO::FETCH_ASSOC);
$row_1 = $rs->fetchAll();
$answer_1_A = $answer_1_B = 0; 
foreach($row_1 as $k=>$v){
    if($v['answer'] == 'A'){$answer_1_A = $v['total_submit'];}
    if($v['answer'] == 'B'){$answer_1_B = $v['total_submit'];}
}
$answer_1 = $answer_1_A.'-'.$answer_1_B;

$sql_2 = 'SELECT answer,count(id) AS total_submit FROM rhi_answer WHERE cday = "'.$tempPredate.'" AND type = 2 AND ques_id = 2 GROUP BY answer';
$rs = $idc_conn->query($sql_2);
$rs->setFetchMode(PDO::FETCH_ASSOC);
$row_2 = $rs->fetchAll();
$answer_2_A = $answer_2_B = $answer_2_C = $answer_2_D = 0; 
foreach($row_2 as $k=>$v){
    if($v['answer'] == 'A'){$answer_2_A = $v['total_submit'];}
    if($v['answer'] == 'B'){$answer_2_B = $v['total_submit'];}
    if($v['answer'] == 'C'){$answer_2_C = $v['total_submit'];}
    if($v['answer'] == 'D'){$answer_2_D = $v['total_submit'];}
}
$answer_2 = $answer_2_A.'-'.$answer_2_B.'-'.$answer_2_C.'-'.$answer_2_D;

$sql_3 = 'SELECT answer,count(id) AS total_submit FROM rhi_answer WHERE cday = "'.$tempPredate.'" AND type = 2 AND ques_id = 3 GROUP BY answer';
$rs = $idc_conn->query($sql_3);
$rs->setFetchMode(PDO::FETCH_ASSOC);
$row_3 = $rs->fetchAll();
$answer_3_A = $answer_3_B = $answer_3_C = 0; 
foreach($row_3 as $k=>$v){
    if($v['answer'] == 'A'){$answer_3_A = $v['total_submit'];}
    if($v['answer'] == 'B'){$answer_3_B = $v['total_submit'];}
    if($v['answer'] == 'C'){$answer_3_C = $v['total_submit'];}
}
$answer_3 = $answer_3_A.'-'.$answer_3_B.'-'.$answer_3_C;

$sql_4 = 'SELECT answer,count(id) AS total_submit FROM rhi_answer WHERE cday = "'.$tempPredate.'" AND type = 2 AND ques_id = 4 GROUP BY answer';
$rs = $idc_conn->query($sql_4);
$rs->setFetchMode(PDO::FETCH_ASSOC);
$row_4 = $rs->fetchAll();
$answer_4_A = $answer_4_B = $answer_4_C = $answer_4_D = 0; 
foreach($row_4 as $k=>$v){
    if($v['answer'] == 'A'){$answer_4_A = $v['total_submit'];}
    if($v['answer'] == 'B'){$answer_4_B = $v['total_submit'];}
    if($v['answer'] == 'C'){$answer_4_C = $v['total_submit'];}
    if($v['answer'] == 'D'){$answer_4_D = $v['total_submit'];}
}
$answer_4 = $answer_4_A.'-'.$answer_4_B.'-'.$answer_4_C.'-'.$answer_4_D;

$sql_5 = 'SELECT answer,count(id) AS total_submit FROM rhi_answer WHERE cday = "'.$tempPredate.'" AND type = 2 AND ques_id = 5 GROUP BY answer';
$rs = $idc_conn->query($sql_5);
$rs->setFetchMode(PDO::FETCH_ASSOC);
$row_5 = $rs->fetchAll();
$answer_5_A = $answer_5_B = $answer_5_C = $answer_5_D = 0; 
foreach($row_5 as $k=>$v){
    if($v['answer'] == 'A'){$answer_5_A = $v['total_submit'];}
    if($v['answer'] == 'B'){$answer_5_B = $v['total_submit'];}
    if($v['answer'] == 'C'){$answer_5_C = $v['total_submit'];}
    if($v['answer'] == 'D'){$answer_5_D = $v['total_submit'];}
}
$answer_5 = $answer_5_A.'-'.$answer_5_B.'-'.$answer_5_C.'-'.$answer_5_D;

$sql_6 = 'SELECT answer,count(id) AS total_submit FROM rhi_answer WHERE cday = "'.$tempPredate.'" AND type = 2 AND ques_id = 6 GROUP BY answer';
$rs = $idc_conn->query($sql_6);
$rs->setFetchMode(PDO::FETCH_ASSOC);
$row_6 = $rs->fetchAll();
$answer_6_A = $answer_6_B = $answer_6_C = $answer_6_D = 0; 
foreach($row_6 as $k=>$v){
    if($v['answer'] == 'A'){$answer_6_A = $v['total_submit'];}
    if($v['answer'] == 'B'){$answer_6_B = $v['total_submit'];}
    if($v['answer'] == 'C'){$answer_6_C = $v['total_submit'];}
    if($v['answer'] == 'D'){$answer_6_D = $v['total_submit'];}
}
$answer_6 = $answer_6_A.'-'.$answer_6_B.'-'.$answer_6_C.'-'.$answer_6_D;

$sql_7 = 'SELECT answer,count(id) AS total_submit FROM rhi_answer WHERE cday = "'.$tempPredate.'" AND type = 2 AND ques_id = 7 GROUP BY answer';
$rs = $idc_conn->query($sql_7);
$rs->setFetchMode(PDO::FETCH_ASSOC);
$row_7 = $rs->fetchAll();
$answer_7_A = $answer_7_B = $answer_7_C = 0; 
foreach($row_7 as $k=>$v){
    if($v['answer'] == 'A'){$answer_7_A = $v['total_submit'];}
    if($v['answer'] == 'B'){$answer_7_B = $v['total_submit'];}
    if($v['answer'] == 'C'){$answer_7_C = $v['total_submit'];}
}
$answer_7 = $answer_7_A.'-'.$answer_7_B.'-'.$answer_7_C;

$sql_8 = 'SELECT answer,count(id) AS total_submit FROM rhi_answer WHERE cday = "'.$tempPredate.'" AND type = 2 AND ques_id = 8 GROUP BY answer';
$rs = $idc_conn->query($sql_8);
$rs->setFetchMode(PDO::FETCH_ASSOC);
$row_8 = $rs->fetchAll();
$answer_8_A = $answer_8_B = $answer_8_C = $answer_8_D = $answer_8_E = 0; 
foreach($row_8 as $k=>$v){
    if($v['answer'] == 'A'){$answer_8_A = $v['total_submit'];}
    if($v['answer'] == 'B'){$answer_8_B = $v['total_submit'];}
    if($v['answer'] == 'C'){$answer_8_C = $v['total_submit'];}
    if($v['answer'] == 'D'){$answer_8_D = $v['total_submit'];}
    if($v['answer'] == 'E'){$answer_8_E = $v['total_submit'];}
}
$answer_8 = $answer_8_A.'-'.$answer_8_B.'-'.$answer_8_C.'-'.$answer_8_D.'-'.$answer_8_E;

$sql_9 = 'SELECT answer,count(id) AS total_submit FROM rhi_answer WHERE cday = "'.$tempPredate.'" AND type = 2 AND ques_id = 9 GROUP BY answer';
$rs = $idc_conn->query($sql_9);
$rs->setFetchMode(PDO::FETCH_ASSOC);
$row_9 = $rs->fetchAll();
$answer_9_A = $answer_9_B = 0; 
foreach($row_9 as $k=>$v){
    if($v['answer'] == 'A'){$answer_9_A = $v['total_submit'];}
    if($v['answer'] == 'B'){$answer_9_B = $v['total_submit'];}
}
$answer_9 = $answer_9_A.'-'.$answer_9_B;

$sql_10 = 'SELECT answer,count(id) AS total_submit FROM rhi_answer WHERE cday = "'.$tempPredate.'" AND type = 2 AND ques_id = 10 GROUP BY answer';
$rs = $idc_conn->query($sql_10);
$rs->setFetchMode(PDO::FETCH_ASSOC);
$row_10 = $rs->fetchAll();
$answer_10_A = $answer_10_B = 0; 
foreach($row_10 as $k=>$v){
    if($v['answer'] == 'A'){$answer_10_A = $v['total_submit'];}
    if($v['answer'] == 'B'){$answer_10_B = $v['total_submit'];}
}
$answer_10 = $answer_10_A.'-'.$answer_10_B;

$sql_11 = 'SELECT answer,count(id) AS total_submit FROM rhi_answer WHERE cday = "'.$tempPredate.'" AND type = 2 AND ques_id = 11 GROUP BY answer';
$rs = $idc_conn->query($sql_11);
$rs->setFetchMode(PDO::FETCH_ASSOC);
$row_11 = $rs->fetchAll();
$answer_11_A = $answer_11_B = $answer_11_C = $answer_11_D = 0; 
foreach($row_11 as $k=>$v){
    if($v['answer'] == 'A'){$answer_11_A = $v['total_submit'];}
    if($v['answer'] == 'B'){$answer_11_B = $v['total_submit'];}
    if($v['answer'] == 'C'){$answer_11_C = $v['total_submit'];}
    if($v['answer'] == 'D'){$answer_11_D = $v['total_submit'];}
}
$answer_11 = $answer_11_A.'-'.$answer_11_B.'-'.$answer_11_C.'-'.$answer_11_D;

$dailyAnsSql = 'SELECT id FROM rhc_answer_daily WHERE day = "'.$predate.'"';
$dailyAnsRs = $tongji_conn->query($dailyAnsSql);
$dailyAnsRs->setFetchMode(PDO::FETCH_ASSOC);
$dailyAnsRow = $dailyAnsRs->fetch();
if($dailyAnsRow){
    $updateSql = "UPDATE rhc_answer_daily SET start_times = ?,submit_times = ?,answer_1 = ?,answer_2 = ?,answer_3 = ?,answer_4 = ?,answer_5 = ?,answer_6 = ?,answer_7 = ?,answer_8 = ?,answer_9 = ?,answer_10 = ?,answer_11 = ? WHERE day = ?";
	$bind = array();
	$bind = array($total_start,$total_submit,$answer_1,$answer_2,$answer_3,$answer_4,$answer_5,$answer_6,$answer_7,$answer_8,$answer_9,$answer_10,$answer_11,$predate);
	$stmt = $tongji_conn->prepare($updateSql);
	$stmt->execute($bind);
}else{
    $insertSql = "INSERT INTO rhc_answer_daily(start_times,submit_times,answer_1,answer_2,answer_3,answer_4,answer_5,answer_6,answer_7,answer_8,answer_9,answer_10,answer_11,day) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $bind = array();
	$tongji_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    $tongji_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bind = array($total_start,$total_submit,$answer_1,$answer_2,$answer_3,$answer_4,$answer_5,$answer_6,$answer_7,$answer_8,$answer_9,$answer_10,$answer_11,$predate);
    $stmt = $tongji_conn->prepare($insertSql);
	$stmt->execute($bind); 
}
$stmt = NULL;


 ?>
