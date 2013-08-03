<?php
/** 
 * dmc5.1.8	DMC确认乘客上车
 * 
 * PHP version 5 
 * 
 * @category  DUDU-BBS-DMC 
 * @package   tu 
 * @author    liangji   2011-03-10 <liangjig@gmail.com> 
 * @copyright 2013-2014 dudu Inc. All rights reserved. 
 * @version   SVN: $Id: dmc_taxi_confirm.php,v 1.0 2013-06-06 22:45:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */

if (API_METHOD_POST == $_SERVER['REQUEST_METHOD']) {
    $token = $_COOKIE['token'];
    
    $orderId = safeReqChrStr('order_id');
    $did = safeReqChrStr('did');
    $taxiType = safeReqChrStr('taxi_type');
	
    
    //判断非空
    if(trim($token) == ''
		|| trim($orderId) == ''
		|| trim($did) == ''
		|| trim($taxiType) == ''
	){
		responseApiErrorResult(901, 'para error!');
        exit();
	}
    //检查token
	if(!checkToken(DUDU_DRIVER,$token,$did)){
		responseApiErrorResult(902, 'token verify error!');
        exit();
	}
	//我已经上车
	if($taxiType==DUDU_TAXI_GREEN){
		$taxiType = ORDER_TYPE_GREEN;
		//查询当前司机是否有statu为0的订单，即没有答复和订单
		$sql = 'select pid,did,status from '.API_TABLE_PRE.'order_normal where status=6 and order_id='.$orderId;
		$rs = myDoSqlQuery($sql);
		$row = pg_fetch_assoc($rs);
		//如果没有，则此为重复请求
		if(empty($row['did'])){
			responseApiErrorResult(null, 'not order error!');
	        exit();
		}
		//判断did与订单号是否相符
		if($did !=$row['did']){
			responseApiErrorResult(901, 'para error!');
	        exit();
		}
		$sql = 'update '.API_TABLE_PRE.'order_normal set status=7 where order_id='.$orderId;
		myDoSqlQuery($sql);
		//更新司机订单统计表
		$sql = 'update '.API_TABLE_PRE.'driver_order set success_num=success_num+1 where did = '.$did;
    	myDoSqlQuery($sql);
    	//更新乘客订单统计表
		$sql = 'update '.API_TABLE_PRE.'passenger_order set success_num=success_num+1 where pid = '.$row['pid'];
    	myDoSqlQuery($sql);
		responseApiOkResult();
		
	}elseif($taxiType == DUDU_TAXI_YELLOW){
//		$taxiType = ORDER_TYPE_YELLOW;
		//查询当前司机是否有statu为0的订单，即没有答复和订单
		$sql = 'select pid,did,status from '.API_TABLE_PRE.'order_reserve where status=6 and order_id='.$orderId;
		$rs = myDoSqlQuery($sql);
		$row = pg_fetch_assoc($rs);
		//如果没有，则此为重复请求
		if(empty($row['did'])){
			responseApiErrorResult(null, 'not order error!');
	        exit();
		}
		//判断did与订单号是否相符
		if($did !=$row['did']){
			responseApiErrorResult(901, 'para error!');
	        exit();
		}
		$sql = 'update '.API_TABLE_PRE.'order_reserve set status=7 where order_id='.$orderId;
		myDoSqlQuery($sql);
		
		//更新司机订单统计表
		$sql = 'update '.API_TABLE_PRE.'driver_order set success_num=success_num+1 where did = '.$did;
    	myDoSqlQuery($sql);
    	//更新乘客订单统计表
		$sql = 'update '.API_TABLE_PRE.'passenger_order set success_num=success_num+1 where pid = '.$row['pid'];
    	myDoSqlQuery($sql);
    	
		responseApiOkResult();
	}
    
}
?>