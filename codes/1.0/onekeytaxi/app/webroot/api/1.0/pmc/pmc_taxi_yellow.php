<?php
/** 
 * pmc打黄车（约车）
 * 
 * PHP version 5 
 * 
 * @category  DUDU-BBS-PMC 
 * @package   tu 
 * @author    liangji   2011-03-10 <liangjig@gmail.com> 
 * @copyright 2013-2014 dudu Inc. All rights reserved. 
 * @version   SVN: $Id: pmc_taxi_yellow.php,v 1.0 2013-07-27 16:43:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */
if (API_METHOD_POST == $_SERVER['REQUEST_METHOD']) {
	$pid = safeReqChrStr('pid');
	$lng = safeReqChrStr('lng');
	$lat = safeReqChrStr('lat');
	$useTime = safeReqChrStr('use_time');
	$tip = safeReqChrStr('tip');
	$validTime = safeReqChrStr('valid_time');
	$startAddress = safeReqChrStr('start_address');
	$startLng = safeReqChrStr('start_lng');
	$startLat = safeReqChrStr('start_lat');
	$endAddress = safeReqChrStr('end_address');
	$endLng = safeReqChrStr('end_lng');
	$endLat = safeReqChrStr('end_lat');
	
	$voice = $_FILES['voice']['tmp_name'];
	
	$token = $_COOKIE['token'];
	//判断都不准为空
	if(trim($pid) == ''
		|| trim($lng) == ''
		|| trim($lat) == ''
		|| trim($useTime) == ''
		|| trim($validTime) == ''
		|| trim($startAddress) == ''
		|| trim($startLng) == ''
		|| trim($startLat) == ''
		|| trim($endAddress) == ''
		|| trim($endLng) == ''
		|| trim($endLat) == ''
		|| trim($token) == ''
	){
		responseApiErrorResult(901, 'para empty error!');
        exit();
	}
	//判断值的范围
	if(!(is_numeric($useTime) && is_numeric($validTime) && is_numeric($tip) && ($tip>=0 && $tip <=50) && $useTime>time() &&   ($validTime>=5 && $validTime<=60))){
		responseApiErrorResult(901, 'para error!');
        exit();
	}

	//检查token
	if(!checkToken(DUDU_PASSENGER,$token,$pid)){
		responseApiErrorResult(902, 'token verify error!');
        exit();
	}
	$sql = 'select current_order_id from '.API_TABLE_PRE.'order_id_list where order_date=\''.date('Ymd').'\' and order_type='.ORDER_TYPE_YELLOW;
	$rs = myDoSqlQuery($sql);
    $row = pg_fetch_assoc($rs);
    
    if(empty($row['current_order_id'])){
    	$currentOrderId = getFirstOrderIdByType(ORDER_TYPE_YELLOW);
    	$sql = 'insert into '.API_TABLE_PRE.'order_id_list (current_order_id,order_date,order_type) values('.$currentOrderId.',\''.date('Ymd').'\','.ORDER_TYPE_YELLOW.')';
    	
    }else{
    	$currentOrderId = $row['current_order_id']+1;
    	$sql = 'update '.API_TABLE_PRE.'order_id_list set current_order_id='.$currentOrderId.' where order_date=\''.date('Ymd').'\' and order_type='.ORDER_TYPE_YELLOW;
    	
    }
    myDoSqlQuery($sql);
    $currentOrderId = date('Ymd').$currentOrderId.'';
    
    if(!empty($voice)){
	    //语音文件
		$passengerDemandName = 'p_demand_'.$pid.'_'.date('His');
		$passengerDemandPath = API_PMC_VOICE_PATH.'/'.date('Ymd').'/'.$passengerDemandName;
		move_uploaded_file($voice,$passengerDemandPath);
		
		$photoDrivingUrl = '/user_upload/API_PMC/voice/'.date('Ymd').'/'.$passengerDemandName;
    }
	$validTime = time()+$validTime*60;
	$useTime = date('Y-m-d H:i:s',$useTime);
	$validTime = date('Y-m-d H:i:s',$validTime);
	
    $sql = 'INSERT INTO '.API_TABLE_PRE.'order_reserve('.
        'order_id, pid, passenger_position, status, request_time,use_time,valid_time,tip,start_point,start_position,end_point,end_position,voice_url'.
        ') VALUES ('.
        ''.$currentOrderId.', '.$pid.', ST_GeomFromText(\'POINT('.$lng.' '.$lat.')\', '.COORDINATE_SYSTEM.'), 0, now(),\''.$useTime.'\',\''.$validTime.'\','.$tip.', \''.mysql_real_escape_string($startAddress).'\',ST_GeomFromText(\'POINT('.$startLng.' '.$startLat.')\', '.COORDINATE_SYSTEM.'), \''.mysql_real_escape_string($endAddress).'\',ST_GeomFromText(\'POINT('.$endLng.' '.$endLat.')\', '.COORDINATE_SYSTEM.'),\''.mysql_real_escape_string($photoDrivingUrl).'\');';
        $insert_status = myDoSqlQuery($sql);
    if($insert_status){
    	/**
    	 * 用户订单统计表更新
    	 * start
    	 * */
    	$sql = 'update '.API_TABLE_PRE.'passenger_order set all_num=all_num+1 where pid = '.$pid;

    	$rs = myDoSqlQuery($sql);
    	$updateStatus = pg_affected_rows($rs);

    	//如果未更新成功，说明没有记录，则插入一条
    	if(!$updateStatus){
    		$sql ='insert into '.API_TABLE_PRE.'passenger_order (pid,all_num) values('.$pid.',1)';
    		myDoSqlQuery($sql);
    	}
    	/**
    	 * 用户订单统计表更新
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
	
}