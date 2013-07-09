<?php
/** 
 * dmc获取手机验证码
 * 
 * PHP version 5 
 * 
 * @category  DUDU-BBS-DMC 
 * @package   tu 
 * @author    liangji   2011-03-10 <liangjig@gmail.com> 
 * @copyright 2013-2014 dudu Inc. All rights reserved. 
 * @version   SVN: $Id: dmc_taxi_green.php,v 1.0 2013-07-07 23:34:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */

if (API_METHOD_GET == $_SERVER['REQUEST_METHOD']) {
    $token = $_COOKIE['token'];
    $did = trim($api_argus[2]);

     //判断非空
    if(trim($token) == ''
		|| trim($did) == ''
	){
		responseApiErrorResult(901, 'para error!');
        exit();
	}
	$sql = 'select token from '.API_TABLE_PRE.'driver_token where did = '.$did;
	$rs = myDoSqlQuery($sql);
	$tokenInfo = pg_fetch_assoc($rs);
	if(!($tokenInfo['token']==$token)){
		responseApiErrorResult(902, 'para error!');
        exit();
	}
	$sql = 'select order_id,pid,ST_AsText(passenger_position)  as point_info,start_point from '.API_TABLE_PRE.'order_normal where did='.$did.' and status=0';
   	$rs = myDoSqlQuery($sql);
   	$row = pg_fetch_assoc($rs);
    if(empty($row['order_id'])){
    	responseApiOkResult(
		   	array(
		   		'status'=>2
		   	)
	   	);
    }else{
    	$pointInfo = postgisToPoint($row['point_info']);
    	$sql = 'select nickname from '.API_TABLE_PRE.'passenger where pid='.$row['pid'];
    	$rs = myDoSqlQuery($sql);
	   	$passengerInfo = pg_fetch_assoc($rs);
	   	responseApiOkResult(
		   	array(
		   		'status' => 1,
		   		'pid'=>$row['pid'],
		   		'passenger_nickname'=>$passengerInfo['nickname'],
		   		'order_id'=>$row['order_id'],
		   		'lng'=>round($pointInfo[1],4),
		   		'lat'=>round($pointInfo[2],4),
		   		'address'=>$row['start_point']
		   	)
	   	);
    }
    
}
?>