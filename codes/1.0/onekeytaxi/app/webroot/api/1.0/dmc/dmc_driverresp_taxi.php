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
    $lng = safeReqChrStr('lng');
    $lat = safeReqChrStr('lat');

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
	if(!checkToken(DUDU_DRIVER,$token,$did)){
		responseApiErrorResult(902, 'token verify error!');
        exit();
	}
	
	//打绿车回复
	if($taxiType==DUDU_TAXI_GREEN){
		$taxiType = ORDER_TYPE_GREEN;
		//查询当前司机是否有statu为0的订单，即没有答复和订单
		$sql = 'select did,pid,status from '.API_TABLE_PRE.'order_normal where status=0 and  order_id='.$orderId;
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
			
		//如果司机去
		if($replied == 1){
			$sql = 'update '.API_TABLE_PRE.'order_normal set status=1,reply_time=now() where order_id='.$orderId;
			myDoSqlQuery($sql);
			
		}elseif ($replied == 2){//如果司机不去
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
		responseApiOkResult(array('status'=>1));
	}elseif ($taxiType==DUDU_TAXI_YELLOW){
		//打黄车司机必须上传应单时经纬度
		//判断非空
	    if(trim($lng) == ''
			|| trim($lat) == ''
		){
			responseApiErrorResult(901, 'para error!');
	        exit();
		}
		$taxiType = ORDER_TYPE_YELLOW;
		//查询当前司机是否有statu为0的订单，即没有答复和订单
		$sql = 'select pid,status from '.API_TABLE_PRE.'order_reserve where status=0 and  order_id='.$orderId;
		$rs = myDoSqlQuery($sql);
		$row = pg_fetch_assoc($rs);
		//如果没有，则已经被别人应单
		if(empty($row['pid'])){
			responseApiOkResult(array('status'=>2));
	        exit();
		}
		//更新订单中司机信息
		$sql = 'update '.API_TABLE_PRE.'order_reserve set did='.$did.',driver_position=ST_GeomFromText(\'POINT('.$lng.' '.$lat.')\', '.COORDINATE_SYSTEM.'),reply_time=now(),status=1 where order_id='.$orderId;
		$updateStatus = myDoSqlQuery($sql);
		if($updateStatus){
			
			//更新司机订单统计表
			$sql = 'update '.API_TABLE_PRE.'driver_order set all_num=all_num+1 where did = '.$did;
        	myDoSqlQuery($sql);
        	
        	
        	//接口返回
			responseApiOkResult(array('status'=>1));
		}else{
			responseApiErrorResult(null,'add error');
		}
		
	}
	
	
    
}
?>