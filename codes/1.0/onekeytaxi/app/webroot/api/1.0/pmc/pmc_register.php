<?php
/** 
 * pmc注册
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
if (API_METHOD_POST == $_SERVER['REQUEST_METHOD']) {
	$username = safeReqChrStr('username');
	$nickname = safeReqChrStr('nickname');
	$gender = safeReqChrStr('gender');
	$vercode = safeReqChrStr('vercode');
	$mobileId = safeReqChrStr('mobile_id');
	//判断都不准为空
	if(trim($username) == ''
		|| trim($nickname) == ''
		|| trim($gender) == ''
		|| trim($vercode) == ''
		|| trim($mobileId) == ''
	){
		responseApiErrorResult(201, 'para error!');
        exit();
	}
	//判断手机号是否正确
	if (!checkMobilePhone($username)) {
        responseApiErrorResult(202, 'Invalid phone number!');
        exit();
    }
	//判断性别字段值是否正确
	if('M' == $gender){
		$gender = 'T';
	}elseif ('F' == $gender){
		$gender = 'F';
	}else{
		responseApiErrorResult(203, 'gender value error');
        exit();
	}
	//去数据库查询验证码
	$sql = 'select code from '.API_TABLE_PRE.'passenger_vercode where username = \''.$username.'\' and valid_time>='.time().'';
	$rs = myDoSqlQuery($sql);
	$num = pg_num_rows($rs);
	if(0 ==$num){
		responseApiErrorResult(102, 'vercode error!');
        exit();
	}
	//检查用户是否已经注册
	$sql = 'select pid from '.API_TABLE_PRE.'passenger where username=\''.$username.'\'';
    $rs = myDoSqlQuery($sql);
    $num = pg_num_rows($rs);
    if(1 == $num){
    	responseApiErrorResult(101, 'username exists!');
        exit();
    }
	$realIP = realip();
	$sql = 'INSERT INTO '.API_TABLE_PRE.'passenger('.
        'username, passwd, email, nickname, gender, mobile_id, reg_time, '.
        'reg_ip, last_login_time, last_login_ip, last_login_position, '.
        'status, more_info) VALUES ('.
        '\''.mysql_real_escape_string($username).'\', null, null, \''.mysql_real_escape_string($nickname).'\', \''.$gender.'\', \''.mysql_real_escape_string($mobileId).'\', now(), '.
        '\''.$realIP.'\', now(), \''.$realIP.'\', null, '.
        '1, null) returning pid;';
    $rs = myDoSqlQuery($sql);
    $result = pg_fetch_row($rs);
    $uid = $result[0];
    if($uid){
    	//删除验证码表中记录
    	$sql = 'delete from '.API_TABLE_PRE.'passenger_vercode where username=\''.$username.'\'';
    	$rs = myDoSqlQuery($sql);
        responseApiOkResult(array('pid'=>$uid));
    }else{
    	responseApiErrorResult(103, 'fail!');
        exit();
    }
}
?>