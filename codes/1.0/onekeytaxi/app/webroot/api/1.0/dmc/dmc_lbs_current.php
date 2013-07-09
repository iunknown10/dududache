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
    $sql = 'select token from '.API_TABLE_PRE.'driver_token where did = '.$did;
	$rs = myDoSqlQuery($sql);
	$tokenInfo = pg_fetch_assoc($rs);
	if(!($tokenInfo['token']==$token)){
		responseApiErrorResult(902, 'para error!');
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
        .'accuracy,sate_num,gps_timestamp, address ) VALUES ('.
        ''.$did.',\''.mysql_real_escape_string($username).'\',ST_GeomFromText(\'POINT('.$lng.' '.$lat.')\', '.COORDINATE_SYSTEM.'), \''.mysql_real_escape_string($alti).'\', \''.mysql_real_escape_string($speed).'\', \''.mysql_real_escape_string($direction).'\','
        .' \''.intval($accuracy).'\',\''.intval($sateNum).'\',\''.intval($gpsTime).'\',\''.mysql_real_escape_string($address).'\');';
        $rs = myDoSqlQuery($sql);
    if(0 == $rs){
    	responseApiErrorResult(null, 'Failed to update position !');
    }else{
		responseApiOkResult();
    }
    
}
?>