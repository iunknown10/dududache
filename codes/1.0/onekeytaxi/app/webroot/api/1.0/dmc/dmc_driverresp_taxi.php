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
 * @version   SVN: $Id: dmc_driverresp_taxi.php,v 1.0 2013-07-07 23:59:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */

if (API_METHOD_POST == $_SERVER['REQUEST_METHOD']) {
    $token = $_COOKIE['token'];
    
    $orderId = safeReqChrStr('order_id');
    $did = safeReqChrStr('did');
    $taxiType = safeReqChrStr('taxi_type');
    $replied = safeReqChrStr('replied');

     //判断非空
    if(trim($token) == ''
		|| trim($orderId) == ''
		|| trim($did) == ''
		|| trim($taxiType) == ''
		|| trim($replied) == ''
	){
		responseApiErrorResult(901, 'para error!');
        exit();
	}
	//检查replied的值
	if(!($replied == 1 || $replied == 2)){
		responseApiErrorResult(901, 'replied value error!');
        exit();
	}
	
	//检查token
	$sql = 'select token from '.API_TABLE_PRE.'driver_token where did = '.$did;
	$rs = myDoSqlQuery($sql);
	$tokenInfo = pg_fetch_assoc($rs);
	if(!($tokenInfo['token']==$token)){
		responseApiErrorResult(902, 'token error!');
        exit();
	}
	
	//打绿车回复
	if($taxiType==DUDU_TAXI_GREEN){
		$taxiType = ORDER_TYPE_GREEN;
		//如果司机去
		if($replied == 1){
			$sql = 'update '.API_TABLE_PRE.'order_normal set status=1,reply_time=now() where order_id='.$orderId;
			myDoSqlQuery($sql);
			
		}elseif ($replied == 2){//如果司机不去
			$sql = 'select pid,status from '.API_TABLE_PRE.'order_normal where order_id='.$orderId;
			$rs = myDoSqlQuery($sql);
			$row = pg_fetch_assoc($rs);
			if($row['status'] == 2){
				responseApiErrorResult(null, 'repeat error!');
		        exit();
			}
			//更新订单表状态
			$sql = 'update '.API_TABLE_PRE.'order_normal set status=2,reply_time=now() where order_id='.$orderId;
			myDoSqlQuery($sql);
			
			
			if($row['pid']){
				//订单评价表增加记录
				$sql = 'insert into '.API_TABLE_PRE.'order_evaluate (order_id,pid,did,cause,taxi_type) values('.$orderId.','.$row['pid'].','.$did.',1,'.ORDER_TYPE_GREEN.')';
				myDoSqlQuery($sql);
			}
			//更新司机订单统计表
			$sql = 'update '.API_TABLE_PRE.'driver_order set broke_num=broke_num+1 where did = '.$did;
        	myDoSqlQuery($sql);
        	//更新乘客订单统计表
			$sql = 'update '.API_TABLE_PRE.'passenger_order set fail_num=fail_num+1 where pid = '.$row['pid'];
        	myDoSqlQuery($sql);
        	
		}
		responseApiOkResult();
	}elseif ($taxiType==DUDU_TAXI_YELLOW){
		$taxiType = ORDER_TYPE_YELLOW;
	}
	
	
    
}
?>