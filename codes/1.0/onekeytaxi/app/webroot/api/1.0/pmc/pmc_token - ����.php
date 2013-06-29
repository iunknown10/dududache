<?php
/** 
 * pmc获取token随机数
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
    $mobilePhone = trim($api_argus[1]);
    if (!checkMobilePhone($mobilePhone)) {
        responseApiErrorResult(202, 'Invalid phone number!');
        exit();
    }
    //判断现有表中是否存在
    $sql = 'select username from '.API_TABLE_PRE.'passenger_token where username=\''.$mobilePhone.'\'';
    $rs = myDoSqlQuery($sql);
    $num = pg_num_rows($rs);
    if(1 == $num){
    	$sql = 'delete from '.API_TABLE_PRE.'passenger_token where username=\''.$mobilePhone.'\'';
    	$rs = myDoSqlQuery($sql);
    }
    $rand = mt_rand();
    $sql = 'select mobile_id from '.API_TABLE_PRE.'passenger where username=\''.$mobilePhone.'\'';
    $rs = myDoSqlQuery($sql);
    $result = pg_fetch_row($rs);
    if(!$result[0]){
    	responseApiErrorResult(103, 'other error!');
        exit();
    }
    $token = generateToken($result[0],$mobilePhone,$rand);
    if(empty($token)){
    	responseApiErrorResult(103, 'fail');
        exit();
    }
    $sql = 'INSERT INTO '.API_TABLE_PRE.'passenger_token('.
        'username, token) VALUES ('.
        '\''.mysql_real_escape_string($mobilePhone).'\', \''.$token.'\');';
    $rs = myDoSqlQuery($sql);
    if(0 == $rs){
    	responseApiErrorResult(103, 'fail');
        exit();
    }
    responseApiOkResult(array('random'=>$rand));
   
    
}
?>