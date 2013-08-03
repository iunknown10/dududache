<?php
/** 
 * dmc5.1.13	DMC更改出租车状态
 * 
 * PHP version 5 
 * 
 * @category  DUDU-BBS-DMC 
 * @package   tu 
 * @author    liangji   2011-03-10 <liangjig@gmail.com> 
 * @copyright 2013-2014 dudu Inc. All rights reserved. 
 * @version   SVN: $Id: dmc_taxi_changestatus.php,v 1.0 2013-06-06 22:45:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */

if (API_METHOD_POST == $_SERVER['REQUEST_METHOD']) {
    $token = $_COOKIE['token'];
    $did = safeReqChrStr('did');
    $status = safeReqChrStr('status');
	
    
    //判断非空
    if(trim($token) == ''
		|| trim($did) == ''
		|| trim(!isset($status))
	){
		responseApiErrorResult(901, 'para error!');
        exit();
	}
    //检查token
	if(!checkToken(DUDU_DRIVER,$token,$did)){
		responseApiErrorResult(902, 'token verify error!');
        exit();
	}
	if($status==0 || $status==1 || $status==2 || $status==9){
		$sql = 'update '.API_TABLE_PRE.'driver_status set status='.$status.' where did = '.$did;
		$updateStatus = myDoSqlQuery($sql);
		if($updateStatus){
			responseApiOkResult();
		}else{
			responseApiErrorResult(null, 'update error!');
	        exit();
		}
	}
    
}
?>