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
    $pid = trim($api_argus[1]);
    if (empty($pid)) {
        responseApiErrorResult(901, 'Invalid passenger id!');
        exit();
    }
    //判断现有表中是否存在
    $sql = 'select pid from '.API_TABLE_PRE.'passenger_token where pid='.$pid;
    $rs = myDoSqlQuery($sql);
    $num = pg_num_rows($rs);
    if(1 == $num){
    	$sql = 'delete from '.API_TABLE_PRE.'passenger_token where pid='.$pid;
    	$rs = myDoSqlQuery($sql);
    }
    $rand = mt_rand();
    $sql = 'select mobile_id,username from '.API_TABLE_PRE.'passenger where pid='.$pid;
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
    $sql = 'INSERT INTO '.API_TABLE_PRE.'passenger_token('.
        'pid, token) VALUES ('.
        '\''.intval($pid).'\', \''.$token.'\');';
    $rs = myDoSqlQuery($sql);
    if(0 == $rs){
    	responseApiErrorResult(null, 'fail');
        exit();
    }
    responseApiOkResult(array('random'=>$rand));
   
    
}
?>