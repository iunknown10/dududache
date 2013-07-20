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
 * @version   SVN: $Id: dmc_driver_arrvied.php,v 1.0 2013-07-09 15:15:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */

if (API_METHOD_POST == $_SERVER['REQUEST_METHOD']) {
	
    $token = $_COOKIE['token'];
    
    $orderId = safeReqChrStr('order_id');
    $did = safeReqChrStr('did');
    $taxiType = safeReqChrStr('taxi_type');
    $lng = safeReqChrStr('lng');
    $lat = safeReqChrStr('lat');
    $address = safeReqChrStr('address');
    
    //判断非空
    if(trim($token) == ''
		|| trim($orderId) == ''
		|| trim($did) == ''
		|| trim($taxiType) == ''
		|| trim($lng) == ''
		|| trim($lat) == ''
		|| trim($address) == ''
	){
		responseApiErrorResult(901, 'para error!');
        exit();
	}
    //检查token
	if(!checkToken(DUDU_DRIVER,$token,$did)){
		responseApiErrorResult(902, 'token verify error!');
        exit();
	}
	//打绿车回复
	if($taxiType==DUDU_TAXI_GREEN){
		$taxiType = ORDER_TYPE_GREEN;
		//查询当前司机是否有statu为0的订单，即没有答复和订单
		$sql = 'select did,status from '.API_TABLE_PRE.'order_normal where status=1 and  order_id='.$orderId;
		$rs = myDoSqlQuery($sql);
		$row = pg_fetch_assoc($rs);
		//如果没有，则此为重复请求
		if(empty($row['did'])){
			responseApiErrorResult(null, 'repeat error!');
	        exit();
		}
		//判断did与订单号是否相符
		if($did !=$row['did']){
			responseApiErrorResult(901, 'para error!');
	        exit();
		}
		$sql = 'update '.API_TABLE_PRE.'order_normal set driver_position=ST_GeomFromText(\'POINT('.$lng.' '.$lat.')\', '.COORDINATE_SYSTEM.'),driver_arrived_time=now(),status=5 where order_id='.$orderId;
		myDoSqlQuery($sql);
		responseApiOkResult();
	}
    
}
?>