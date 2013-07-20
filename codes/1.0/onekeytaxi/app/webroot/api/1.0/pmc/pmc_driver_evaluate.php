<?php
/** 
 * pmc评价司机
 * 
 * PHP version 5 
 * 
 * @category  DUDU-BBS-PMC 
 * @package   tu 
 * @author    liangji   2011-03-10 <liangjig@gmail.com> 
 * @copyright 2013-2014 dudu Inc. All rights reserved. 
 * @version   SVN: $Id: pmc_driver_evaluate.php,v 1.0 2013-07-06 15:36:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */
if (API_METHOD_POST == $_SERVER['REQUEST_METHOD']) {
	$did = safeReqChrStr('did');
	$pid = safeReqChrStr('pid');
	$orderId = safeReqChrStr('order_id');
	$cause = safeReqChrStr('cause');
	$taxiType = safeReqChrStr('taxi_type');
	$content = safeReqChrStr('content');
	$token = $_COOKIE['token'];
	//判断都不准为空
	if(trim($did) == ''
		|| trim($pid) == ''
		|| trim($orderId) == ''
		|| trim($cause) == ''
		|| trim($cause) == ''
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
	//绿车
	if(DUDU_TAXI_GREEN == $taxiType){
		//如果司机同意了，但未来接乘客
		if(2 == $cause){
			$sql = 'select pid,did from '.API_TABLE_PRE.'order_normal where status=1 and  order_id='.$orderId;
			$rs = myDoSqlQuery($sql);
		    $row = pg_fetch_assoc($rs);
		   if($row['pid'] == $pid && $row['did'] == $did){
		    	//订单评价表增加记录
				$sql = 'insert into '.API_TABLE_PRE.'order_evaluate (order_id,pid,did,cause,taxi_type) values('.$orderId.','.$pid.','.$did.',2,'.ORDER_TYPE_GREEN.')';
				myDoSqlQuery($sql);
				//更新司机订单统计表
				$sql = 'update '.API_TABLE_PRE.'driver_order set success_num=success_num+1 where did = '.$did;
	        	myDoSqlQuery($sql);
	        	//更新乘客订单统计表
				$sql = 'update '.API_TABLE_PRE.'passenger_order set success_num=success_num+1 where pid = '.$pid;
	        	myDoSqlQuery($sql);
		    }else{
		    	responseApiErrorResult(902, 'did verify error!');
		        exit();
		    }
		}elseif(3 == $cause){//好评
			$sql = 'select pid,did from '.API_TABLE_PRE.'order_normal where status=7 and  order_id='.$orderId;
			$rs = myDoSqlQuery($sql);
		    $row = pg_fetch_assoc($rs);
		    if($row['pid'] == $pid && $row['did'] == $did){
		    	if(!empty($content)){
		    		$moreInfo = json_encode(array('content'=>$content));
		    		//订单评价表增加记录
					$sql = 'insert into '.API_TABLE_PRE.'order_evaluate (order_id,pid,did,cause,taxi_type,more_info) values('.$orderId.','.$pid.','.$did.','.$cause.','.ORDER_TYPE_GREEN.',\''.$moreInfo.'\')';
		    	}else{
		    		//订单评价表增加记录
					$sql = 'insert into '.API_TABLE_PRE.'order_evaluate (order_id,pid,did,cause,taxi_type) values('.$orderId.','.$pid.','.$did.','.$cause.','.ORDER_TYPE_GREEN.')';
		    	}
		    	
				myDoSqlQuery($sql);
				//更新司机订单统计表
				$sql = 'update '.API_TABLE_PRE.'driver_order set success_num=success_num+1 where did = '.$did;
	        	myDoSqlQuery($sql);
	        	//更新乘客订单统计表
				$sql = 'update '.API_TABLE_PRE.'passenger_order set success_num=success_num+1 where pid = '.$pid;
	        	myDoSqlQuery($sql);
		    }else{
		    	responseApiErrorResult(902, 'did verify error!');
		        exit();
		    }
		}elseif(4 == $cause || 5 == $cause){//中评、差评
			$sql = 'select pid,did from '.API_TABLE_PRE.'order_normal where status=7 and  order_id='.$orderId;
			$rs = myDoSqlQuery($sql);
		    $row = pg_fetch_assoc($rs);
		    if($row['pid'] == $pid && $row['did'] == $did){
		    	if(!empty($content)){
		    		$moreInfo = json_encode(array('content'=>$content));
		    		//订单评价表增加记录
					$sql = 'insert into '.API_TABLE_PRE.'order_evaluate (order_id,pid,did,cause,taxi_type,more_info) values('.$orderId.','.$pid.','.$did.','.$cause.','.ORDER_TYPE_GREEN.',\''.$moreInfo.'\')';
		    	}else{
		    		responseApiErrorResult(902, 'content not empty!');
			        exit();
		    	}
				myDoSqlQuery($sql);
				//更新司机订单统计表
				$sql = 'update '.API_TABLE_PRE.'driver_order set success_num=success_num+1 where did = '.$did;
	        	myDoSqlQuery($sql);
	        	//更新乘客订单统计表
				$sql = 'update '.API_TABLE_PRE.'passenger_order set success_num=success_num+1 where pid = '.$pid;
	        	myDoSqlQuery($sql);
		    }else{
		    	responseApiErrorResult(902, 'did verify error!');
		        exit();
		    }
		}else{
			responseApiErrorResult(null, 'error!');
		        exit();
		}
		responseApiOkResult();
	}
	
}