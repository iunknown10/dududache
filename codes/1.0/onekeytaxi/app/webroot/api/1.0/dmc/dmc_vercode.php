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
 * @version   SVN: $Id: pcm_vercode.php,v 1.0 2013-06-06 22:45:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */

if (API_METHOD_GET == $_SERVER['REQUEST_METHOD']) {
    //判断手机号
    $mobilePhone = trim($api_argus[1]);
    if (!checkMobilePhone($mobilePhone)) {
        responseApiErrorResult(null, 'Invalid phone number!');
        exit();
    }
    //判断现有表中是否存在
    $sql = 'select username from '.API_TABLE_PRE.'driver_vercode where username=\''.$mobilePhone.'\'';
    $rs = myDoSqlQuery($sql);
    $num = pg_num_rows($rs);
    if($num){
    	$sql = 'delete from '.API_TABLE_PRE.'driver_vercode where username=\''.$mobilePhone.'\'';
    	$rs = myDoSqlQuery($sql);
    }
    $tmpRand = rand(1000,9999);
    $validTime = time()+60*SMS_VERCODE_VALID;//验证码5分钟内有效
    $sql = 'INSERT INTO '.API_TABLE_PRE.'driver_vercode(' .
   			' username, code, valid_time)VALUES ('.
    		'\''.mysql_real_escape_string($mobilePhone).'\', '.$tmpRand.', \''.$validTime.'\');';
   	$rs = myDoSqlQuery($sql);
    if(0 == $rs){
    	responseApiErrorResult(null, 'Failed to get !');
    }else{
    	if(sendSms($mobilePhone,$tmpRand)){
    		responseApiOkResult();
    	}
    }
    
}
?>