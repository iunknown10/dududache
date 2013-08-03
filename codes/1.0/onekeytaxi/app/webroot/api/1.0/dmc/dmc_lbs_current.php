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
 * @version   SVN: $Id: dmc_lbs_current.php,v 1.0 2013-06-06 22:45:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */

if (API_METHOD_POST == $_SERVER['REQUEST_METHOD']) {
	
    $token = $_COOKIE['token'];
    
    $did = safeReqChrStr('did');
    $lat = safeReqChrStr('lat');
    $lng = safeReqChrStr('lng');
    $alti = safeReqChrStr('alti');
    $speed = safeReqChrStr('speed');
    $direction = safeReqChrStr('direction');
    $accuracy = safeReqChrStr('accuracy');
    $sateNum = safeReqChrStr('sate_num');
    $gpsTime = safeReqChrStr('gps_timestamp');
    $address = safeReqChrStr('address');
    
    //判断非空
    if(trim($token) == ''
		|| trim($did) == ''
		|| trim($lat) == ''
		|| trim($lng) == ''
	){
		responseApiErrorResult(901, 'para error!');
        exit();
	}
    //检查token
	if(!checkToken(DUDU_DRIVER,$token,$did)){
		responseApiErrorResult(902, 'token verify error!');
        exit();
	}
	//判断现有表中是否存在
    $sql = 'select username from '.API_TABLE_PRE.'driver_position where did='.$did;
    $rs = myDoSqlQuery($sql);
    $num = pg_num_rows($rs);
    if($num){
    	$sql = 'delete from '.API_TABLE_PRE.'driver_position where did='.$did;
    	$rs = myDoSqlQuery($sql);
    }
    
    
	$sql = 'INSERT INTO '.API_TABLE_PRE.'driver_position('.
        'did, username, location, alti, speed, direction,'
        .'accuracy,sate_num,gps_timestamp, address ,update_time) VALUES ('.
        ''.$did.',\''.mysql_real_escape_string($username).'\',ST_GeomFromText(\'POINT('.$lng.' '.$lat.')\', '.COORDINATE_SYSTEM.'), \''.mysql_real_escape_string($alti).'\', \''.mysql_real_escape_string($speed).'\', \''.mysql_real_escape_string($direction).'\','
        .' \''.intval($accuracy).'\',\''.intval($sateNum).'\','.intval($gpsTime).',\''.mysql_real_escape_string($address).'\','.time().');';
        $rs = myDoSqlQuery($sql);
        $insertStatus = pg_affected_rows($rs);
        var_dump($insertStatus);
    if($insertStatus){
    	//查看是否有订单
    	$sql = 'select order_id from '.API_TABLE_PRE.'order_normal where status=1 and did='.$did;
    	 $rs = myDoSqlQuery($sql);
    	 $orderInfo = pg_fetch_assoc($rs);
    	 //更新订单路径表
    	 if(!empty($orderInfo['order_id'])){
    	 	$tmpCurrentPosition = '\''.$lng.','.$lat.','.time().'\'';
    	 	$sql = 'update '.API_TABLE_PRE.'order_path set path_info = CONCAT(path_info,\':\','.$tmpCurrentPosition.') where order_id='.$orderInfo['order_id'];
    	 	$rs = myDoSqlQuery($sql);
        	$updateStatus = pg_affected_rows($rs);
        	if(!$updateStatus){
        		$sql = 'insert into '.API_TABLE_PRE.'order_path (order_id,path_info) values ('.$orderInfo['order_id'].','.$tmpCurrentPosition.')';
        		$rs = myDoSqlQuery($sql);
        	}
    	 }
    	responseApiOkResult();
    }else{
		responseApiErrorResult(null, 'Failed to update position !');
    }
    
}
?>