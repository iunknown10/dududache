<?php
header('charset=UTF-8');
require_once "../api.inc.php";
$action = safeReqChrStr('action');

if($action == 'get_vercode'){
	$mobilePhone = safeReqChrStr('username');
	if(!empty($mobilePhone)){
		$sql = 'select * from '.API_TABLE_PRE.'driver_vercode where username=\''.$mobilePhone.'\'';
	    $rs = myDoSqlQuery($sql);
	    $result = pg_fetch_assoc($rs);
	    if($result){
	    	echo '司机用户名：'.$mobilePhone.'<br>验证码：'.$result['code'].'<br>有效时间至：'.date('Y-m-d H:i:s',$result['valid_time']);
	    }else {
	    	echo '暂无内容';
	    }
	}
}
?>