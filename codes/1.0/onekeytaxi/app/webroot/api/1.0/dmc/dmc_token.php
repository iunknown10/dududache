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
    $did = trim($api_argus[1]);
    if (empty($did)) {
        responseApiErrorResult(901, 'Invalid driver id!');
        exit();
    }
    //判断现有表中是否存在
    $sql = 'select did from '.API_TABLE_PRE.'driver_token where did='.$did;
    $rs = myDoSqlQuery($sql);
    $num = pg_num_rows($rs);
    if(1 == $num){
    	$sql = 'delete from '.API_TABLE_PRE.'driver_token where did='.$did;
    	$rs = myDoSqlQuery($sql);
    }
    $rand = mt_rand();
    $sql = 'select mobile_id,username from '.API_TABLE_PRE.'driver where did='.$did;
    $rs = myDoSqlQuery($sql);
    $result = pg_fetch_assoc($rs);
    if(!$result){
    	responseApiErrorResult(null, 'other error!');
        exit();
    }
    $token = generateToken($result['mobile_id'],$result['username'],$rand);
    if(empty($token)){
    	responseApiErrorResult(901, 'token empty');
        exit();
    }
    $sql = 'INSERT INTO '.API_TABLE_PRE.'driver_token('.
        'did, token) VALUES ('.
        '\''.intval($did).'\', \''.$token.'\');';
    $rs = myDoSqlQuery($sql);
    if(0 == $rs){
    	responseApiErrorResult(null, 'fail');
        exit();
    }
    responseApiOkResult(array('random'=>$rand));
   
    
}
?>