<?php
/** 
 * pmc打绿车
 * 
 * PHP version 5 
 * 
 * @category  DUDU-BBS-PMC 
 * @package   tu 
 * @author    liangji   2011-03-10 <liangjig@gmail.com> 
 * @copyright 2013-2014 dudu Inc. All rights reserved. 
 * @version   SVN: $Id: pmc_taxi_green.php,v 1.0 2013-07-06 15:36:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */
if (API_METHOD_POST == $_SERVER['REQUEST_METHOD']) {
	$did = safeReqChrStr('did');
	$pid = safeReqChrStr('pid');
	$lat = safeReqChrStr('lat');
	$lng = safeReqChrStr('lng');
	$address = safeReqChrStr('address');
	//判断都不准为空
	if(trim($did) == ''
		|| trim($pid) == ''
		|| trim($lat) == ''
		|| trim($lng) == ''
		|| trim($address) == ''
	){
		responseApiErrorResult(201, 'para error!');
        exit();
	}
	$sql = 'select status from '.API_TABLE_PRE.'driver_status where did='.$did;
	$rs = myDoSqlQuery($sql);
    $row = pg_fetch_assoc($rs);
    if(ORDER_TYPE_GREEN == $row['status']){
    	$sql = 'select current_order_id from '.API_TABLE_PRE.'order_id_list where order_date=\''.date('Ymd').'\' and order_type='.ORDER_TYPE_GREEN;
    	$rs = myDoSqlQuery($sql);
	    $row = pg_fetch_assoc($rs);
	    
	    if(empty($row['current_order_id'])){
	    	$currentOrderId = getFirstOrderIdByType(ORDER_TYPE_GREEN);
	    	$sql = 'insert into '.API_TABLE_PRE.'order_id_list (current_order_id,order_date,order_type) values('.$currentOrderId.',\''.date('Ymd').'\','.ORDER_TYPE_GREEN.')';
	    	
	    }else{
	    	$currentOrderId = $row['current_order_id']+1;
	    	$sql = 'update '.API_TABLE_PRE.'order_id_list set current_order_id='.$currentOrderId.' where order_date=\''.date('Ymd').'\' and order_type='.ORDER_TYPE_GREEN;
	    	
	    }
	    myDoSqlQuery($sql);
	    $currentOrderId = date('Ymd').$currentOrderId.'';
	    
	    $sql = 'INSERT INTO '.API_TABLE_PRE.'order_normal('.
	        'order_id, pid, did, passenger_position, driver_position, status, request_time, start_point'.
	        ') VALUES ('.
	        ''.$currentOrderId.', '.$pid.', '.$did.',ST_GeomFromText(\'POINT('.$lng.' '.$lat.')\', '.COORDINATE_SYSTEM.'), null, 0, now(), \''.$address.'\');';
	    $insert_status = myDoSqlQuery($sql);
        if($insert_status){
        	/**
        	 * 用户订单统计表更新
        	 * start
        	 * */
        	$sql = 'update '.API_TABLE_PRE.'passenger_order set all_num=all_num+1 where pid = '.$pid;

        	$rs = myDoSqlQuery($sql);
        	$update_status = pg_affected_rows($rs);

        	//如果未更新成功，说明没有记录，则插入一条
        	if(!$update_status){
        		$sql ='insert into '.API_TABLE_PRE.'passenger_order (pid,all_num) values('.$pid.',1)';
        		myDoSqlQuery($sql);
        	}
        	/**
        	 * 用户订单统计表更新
        	 * end
        	 * */
        	
        	
        	/**
        	 * 司机订单统计表更新
        	 * start
        	 * */
        	$sql = 'update '.API_TABLE_PRE.'driver_order set all_num=all_num+1 where did = '.$did;
        	$rs = myDoSqlQuery($sql);
        	$update_status = pg_affected_rows($rs);
        	//如果未更新成功，说明没有记录，则插入一条
        	if(!$update_status){
        		$sql ='insert into '.API_TABLE_PRE.'driver_order (did,all_num) values('.$did.',1)';
        		myDoSqlQuery($sql);
        	}
        	/**
        	 * 司机订单统计表更新
        	 * end
        	 * */
        	responseApiOkResult(
				array(
					'status' => 1,
					'order_id' => $currentOrderId
				)
			);
        }else{
        	responseApiErrorResult(null,'add error');
        }
    }else{
    	responseApiOkResult(
			array(
				'status' => 2,
				'order_id' => null
			)
		);
    }
}