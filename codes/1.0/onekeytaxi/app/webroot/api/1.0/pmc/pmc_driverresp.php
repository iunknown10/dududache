<?php
/** 
 * 5.1.7	PMC获取司机应答
 * 
 * PHP version 5 
 * 
 * @category  DUDU-BBS-PMC 
 * @package   tu 
 * @author    liangji   2011-03-10 <liangjig@gmail.com> 
 * @copyright 2013-2014 dudu Inc. All rights reserved. 
 * @version   SVN: $Id: pmc_driverresp.php,v 1.0 2013-07-08 21:08:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */

if (API_METHOD_GET == $_SERVER['REQUEST_METHOD']) {
	$token = $_COOKIE['token'];
    //判断手机号
    $pid = trim($api_argus[1]);
    $orderType = trim($api_argus[2]);
    $orderId = trim($api_argus[3]);
    //判断都不准为空
	if(trim($pid) == ''
		|| trim($orderType) == ''
		|| trim($orderId) == ''
		|| trim($token) == ''
	){
		responseApiErrorResult(901, 'para error!');
        exit();
	}
	//检查token
	if(!checkToken(DUDU_PASSENGER,$token,$pid)){
		responseApiErrorResult(902, 'token error!');
        exit();
	}
	//green绿车
	if($orderType == DUDU_TAXI_GREEN){
		$sql = 'select did,pid,status from '.API_TABLE_PRE.'order_normal where order_id='.$orderId;
		$rs = myDoSqlQuery($sql);
		$row = pg_fetch_assoc($rs);
		if(0==$row['status']){
			responseApiOkResult(
				array('driver_replied'=>0)
			);
		}else{
			$sql = 'select d.did,d.username,d.nickname,d.car_number,t.taxi_company_name from '.API_TABLE_PRE.'driver d,'.API_TABLE_PRE.'taxi_company t where d.did='.$row['did'].' and d.taxi_company_id = t.taxi_company_id';
			$rs = myDoSqlQuery($sql);
			$driverInfo = pg_fetch_assoc($rs);
			
			if($row['status']==1){
				$diverReplied = 1;
			}elseif ($row['status']==2){
				$diverReplied = 2;
			}else{
				$diverReplied = 9;
			}
			responseApiOkResult(
				array(
					'driver_replied' => $diverReplied,
					'did' => $driverInfo['did'],
					'username' => $driverInfo['username'],
					'nickname' => $driverInfo['nickname'],
					'car_number' => $driverInfo['car_number'],
					'tax_company' => $driverInfo['taxi_company_name']
				)
			);
		}
	}
	
    
}
?>