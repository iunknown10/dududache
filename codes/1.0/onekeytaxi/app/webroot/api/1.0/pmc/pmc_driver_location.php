<?php
/** 
 * pmc获取司机位置
 * 
 * PHP version 5 
 * 
 * @category  DUDU-BBS-PMC 
 * @package   tu 
 * @author    liangji   2011-03-10 <liangjig@gmail.com> 
 * @copyright 2013-2014 dudu Inc. All rights reserved. 
 * @version   SVN: $Id: pmc_driver_location.php,v 1.0 2013-05-26 10:56:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */

if (API_METHOD_GET == $_SERVER['REQUEST_METHOD']) {
    //判断手机号
    $pid = trim($api_argus[2]);
    $did = trim($api_argus[3]);
    $orderId = trim($api_argus[4]);
    $token = $_COOKIE['token'];

    
    if(trim($pid) == ''
		|| trim($did) == ''
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
    //判断是否有订单ID
    if(empty($orderId)){
    	$sql = 'select ST_AsText(location)  as point_info,alti,speed,direction,accuracy,sate_num,gps_timestamp from  '.API_TABLE_PRE.'driver_position where did='.$did;
	    $rs = myDoSqlQuery($sql);
	    $row = pg_fetch_assoc($rs);
	    if($row){
	    	$pointInfo = postgisToPoint($row['point_info']);
	    	$locationInfo = array(
	    		'lng'       =>  round($pointInfo[1],4),
	    		'lat'       =>  round($pointInfo[2],4),
	    		'alti'      => $row['alti']?$row['alti']:0,
	    		'speed'     => $row['speed']?$row['speed']:0,
	    		'direction' => $row['direction']?$row['direction']:0,
	    		'accuracy'  => $row['accuracy']?$row['accuracy']:0,
	    		'sate_num'  => $row['sate_num']?$row['sate_num']:0,
	    		'timestamp' => $row['gps_timestamp']?$row['gps_timestamp']:0,
	    	);
	    	responseApiOkResult(
				array(
					'location_num' => 1,
					'location_list' => $locationInfo
				)
			);
	    }else{
	    	responseApiOkResult(
				array(
					'location_num' => 0,
					'location_list' => null
				)
			);
	    }
    }else{
    	//验证一下did与order_id
    	$sql = 'select did,pid,status from '.API_TABLE_PRE.'order_normal where status=1 and  order_id='.$orderId;
    	$rs = myDoSqlQuery($sql);
		$row = pg_fetch_assoc($rs);
		if($row['did'] == $did){
			$sql = 'select path_info from '.API_TABLE_PRE.'order_path where order_id='.$orderId;
			$rs = myDoSqlQuery($sql);
			$pointList = array();
			$row = pg_fetch_assoc($rs);
		    $multPointList = explode(':',$row['path_info']);
		    foreach ($multPointList as $key => $val){
		    	if($val){
			    	$single = explode(',',$val);
			    	$pointList[] = array(
			    		'lng'       =>  round($single[0],4),
			    		'lat'       =>  round($single[1],4),
			    		'alti'      => null,
			    		'speed'     => null,
			    		'direction' => null,
			    		'accuracy'  => null,
			    		'sate_num'  => null,
			    		'timestamp' => $single[2]?$single[2]:0,
			    	);
		    	}
		    }
		    responseApiOkResult(
				array(
					'location_num' => count($pointList),
					'location_list' => $pointList
				)
			);
		}else{
			responseApiErrorResult(902, 'driver and order verify error!');
	        exit();
		}
    }
    
}
