<?php
/** 
 * dmc查看手机号是否注册
 * 
 * PHP version 5 
 * 
 * @category  DUDU-BBS-DMC 
 * @package   tu 
 * @author    liangji   2011-03-10 <liangjig@gmail.com> 
 * @copyright 2013-2014 dudu Inc. All rights reserved. 
 * @version   SVN: $Id: pcm_vercode.php,v 1.0 2013-09-01 23:21:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */

if (API_METHOD_GET == $_SERVER['REQUEST_METHOD']) {
    //判断手机号
    $username = trim($api_argus[2]);
    if (empty($username) || !checkMobilePhone($username)) {
        responseApiErrorResult(901, 'Invalid driver id!');
        exit();
    }
    //判断现有表中是否存在
    $sql = 'select did from '.API_TABLE_PRE.'driver where username=\''.$username.'\'';
    $rs = myDoSqlQuery($sql);
    $num = pg_num_rows($rs);
    if(1 == $num){
    	responseApiOkResult(array('status'=>1));
    }else{
    	responseApiOkResult(array('status'=>0));
    }
    
    
   
    
}
?>