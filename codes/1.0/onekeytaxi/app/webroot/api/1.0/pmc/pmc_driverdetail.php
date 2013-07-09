<?php
/** 
 * pmc获取司机详情
 * 
 * PHP version 5 
 * 
 * @category  DUDU-BBS-PMC 
 * @package   tu 
 * @author    liangji   2011-03-10 <liangjig@gmail.com> 
 * @copyright 2013-2014 dudu Inc. All rights reserved. 
 * @version   SVN: $Id: pcm_vercode.php,v 1.0 2013-05-18 00:21:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */

if (API_METHOD_GET == $_SERVER['REQUEST_METHOD']) {
    //判断手机号
    $pid = trim($api_argus[1]);
    $did = trim($api_argus[2]);
    if(empty($pid) || empty($did)){
    	responseApiErrorResult(901, 'Invalid passenger id!');
        exit();
    }
    $token = $_COOKIE['token'];
    if(empty($token)){
    	responseApiErrorResult(901, 'token empty error!');
        exit();
    }
    //检查token
    $sql = 'select token from '.API_TABLE_PRE.'passenger_token where pid='.$pid;
    $rs = myDoSqlQuery($sql);
    $result = pg_fetch_assoc($rs);
    if($result['token'] != $token){
    	responseApiErrorResult(902, 'token verify error!');
        exit();
    }
    
    //查询司机昵称及公司
    $sql = 'select d.did,d.nickname,d.car_number,t.taxi_company_name from '.API_TABLE_PRE.'driver d,'.API_TABLE_PRE.'taxi_company t where d.did='.$did.' and d.taxi_company_id=t.taxi_company_id';
    $rs = myDoSqlQuery($sql);
    if(0 == $rs){
    	responseApiErrorResult(null, 'Failed to get !');
    }
    $driverInfo = pg_fetch_assoc($rs);
    $sql = 'select all_num,success_num,broke_num from '.API_TABLE_PRE.'driver_order where did='.$did;
    $rs = myDoSqlQuery($sql);
    $driverOrderInfo = pg_fetch_assoc($rs);
    
    $driverDetails = array(
    	'did' => $did,
    	'nickname' => $driverInfo['nickname'],
    	'car_number' => $driverInfo['car_number'],
    	'tax_company' => $driverInfo['taxi_company_name'],
    	'order_all_num' => $driverOrderInfo['all_num'] ? $driverOrderInfo['all_num'] : 0,
    	'order_ok_num' => $driverOrderInfo['success_num'] ? $driverOrderInfo['success_num'] : 0,
    	'order_refuse_num' => $driverOrderInfo['broke_num'] ? $driverOrderInfo['broke_num'] : 0,
    );
    
	responseApiOkResult($driverDetails);

    
    
}
?>