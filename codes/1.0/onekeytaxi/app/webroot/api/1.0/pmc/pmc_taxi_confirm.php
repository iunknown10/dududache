<?php
/** 
 * pmc确认打车成功
 * 
 * PHP version 5 
 * 
 * @category  DUDU-BBS-PMC 
 * @package   tu 
 * @author    liangji   2011-03-10 <liangjig@gmail.com> 
 * @copyright 2013-2014 dudu Inc. All rights reserved. 
 * @version   SVN: $Id: pmc_taxi_confirm.php,v 1.0 2013-05-26 10:56:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */

if (API_METHOD_POST == $_SERVER['REQUEST_METHOD']) {
	$did = safeReqChrStr('did');
	$pid = safeReqChrStr('pid');
	$orderId = safeReqChrStr('order_id');
	$taxiType = safeReqChrStr('taxi_type');
	$lat = safeReqChrStr('ride_lat');
	$lng = safeReqChrStr('ride_lng');
	$token = $_COOKIE['token'];
	//判断都不准为空
	if(trim($did) == ''
		|| trim($pid) == ''
		|| trim($lat) == ''
		|| trim($lng) == ''
		|| trim($orderId) == ''
		|| trim($taxiType) == ''
		|| trim($token) == ''
	){
		responseApiErrorResult(901, 'para error!');
        exit();
	}
	//检查token
	if(!checkToken(DUDU_PASSENGER,$token,$pid)){
		responseApiErrorResult(902, 'token verify error!');
        exit();
	}
	
	//我已经上车
	if($taxiType==DUDU_TAXI_GREEN){
		$taxiType = ORDER_TYPE_GREEN;
		//查询当前司机是否有statu为0的订单，即没有答复和订单
		$sql = 'select pid,status from '.API_TABLE_PRE.'order_normal where status=5 and order_id='.$orderId;
		$rs = myDoSqlQuery($sql);
		$row = pg_fetch_assoc($rs);
		//如果没有，则此为重复请求
		if(empty($row['pid'])){
			responseApiErrorResult(null, 'not order error!');
	        exit();
		}
		//判断did与订单号是否相符
		if($pid !=$row['pid']){
			responseApiErrorResult(901, 'para error!');
	        exit();
		}
		$sql = 'update '.API_TABLE_PRE.'order_normal set ride_position=ST_GeomFromText(\'POINT('.$lng.' '.$lat.')\', '.COORDINATE_SYSTEM.'),passenger_rided_time=now(),status=6 where order_id='.$orderId;
		myDoSqlQuery($sql);
		responseApiOkResult();
		
	}elseif($taxiType==DUDU_TAXI_YELLOW){
		$taxiType = DUDU_TAXI_YELLOW;
		//查询当前司机是否有statu为0的订单，即没有答复和订单
		$sql = 'select pid,status from '.API_TABLE_PRE.'order_reserve where status=5 and order_id='.$orderId;
		$rs = myDoSqlQuery($sql);
		$row = pg_fetch_assoc($rs);
		//如果没有，则此为重复请求
		if(empty($row['pid'])){
			responseApiErrorResult(null, 'not order error!');
	        exit();
		}
		//判断did与订单号是否相符
		if($pid !=$row['pid']){
			responseApiErrorResult(901, 'para error!');
	        exit();
		}
		$sql = 'update '.API_TABLE_PRE.'order_reserve set ride_position=ST_GeomFromText(\'POINT('.$lng.' '.$lat.')\', '.COORDINATE_SYSTEM.'),passenger_rided_time=now(),status=6 where order_id='.$orderId;
		myDoSqlQuery($sql);
		responseApiOkResult();
	}
}