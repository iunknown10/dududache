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
	$taxCompany = safeReqChrStr('taxi_company');
	$driverNumber = safeReqChrStr('driver_number');
	$carNumber = safeReqChrStr('car_number');
	$username = safeReqChrStr('username');
	$truename = safeReqChrStr('truename');
	$mobileId = safeReqChrStr('mobile_id');
	$recMobile = safeReqChrStr('rec_mobile');
	$vercode = safeReqChrStr('vercode');
	$cityId = safeReqNumStr('city_id');
	$photoDriving = $_FILES['photo_driving']['tmp_name'];
	$photoTaxi =$_FILES['photo_taxi']['tmp_name'];
	//判断都不准为空
	if(trim($taxCompany) == ''
		|| trim($driverNumber) == ''
		|| trim($carNumber) == ''
		|| trim($username) == ''
		|| trim($truename) == ''
		|| trim($mobileId) == ''
		|| trim($vercode) == ''
		|| trim($cityId) == ''
		|| trim($photoDriving) == ''
		|| trim($photoTaxi) == ''
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
	/*if('M' == $gender){
		$gender = 'T';
	}elseif ('F' == $gender){
		$gender = 'F';
	}else{
		responseApiErrorResult(203, 'gender value error');
        exit();
	}*/
	//去数据库查询验证码
	$sql = 'select code from '.API_TABLE_PRE.'driver_vercode where username = \''.$username.'\' and valid_time>='.time().'';
	$rs = myDoSqlQuery($sql);
	$num = pg_num_rows($rs);
	if(0 ==$num){
		responseApiErrorResult(102, 'vercode error!');
        exit();
	}
	//检查用户是否已经注册
	$sql = 'select did from '.API_TABLE_PRE.'driver where username=\''.$username.'\'';
    $rs = myDoSqlQuery($sql);
    $num = pg_num_rows($rs);
    if(1 == $num){
    	responseApiErrorResult(101, 'username exists!');
        exit();
    }
    $nickname = cut_str($truename,1,0,'').'师傅';
	$realIP = realip();
	//保存图片1
	$photoDrivingName = 'driving_'.$username.'_'.time().'.jpg';
	$photoDrivingPath = API_DMC_PHOTO_PATH.$photoDrivingName;
	move_uploaded_file($photoDriving,$photoDrivingPath);
	//保存图片2
	$photoTaxiName = 'taxi_'.$username.'_'.time().'.jpg';
	$photoTaxiPath = API_DMC_PHOTO_PATH.$photoTaxiName;
	move_uploaded_file($photoTaxi,$photoTaxiPath);
	
	$photoDrivingUrl = '/user_upload/API_DMC/photo/'.$photoDrivingName;
	$photoTaxiUrl = '/user_upload/API_DMC/photo/'.$photoTaxiName;
	$photoInfo['photoDriving'] = $photoDrivingUrl;
	$photoInfo['photoTaxi'] = $photoTaxiUrl;
	$more_info  = json_encode($photoInfo);
	
	//保存出租车公司名
	$sql ='select taxi_company_id from '.API_TABLE_PRE.'taxi_company where taxi_company_name = \''.$taxCompany.'\'';
	$rs = myDoSqlQuery($sql);
    $result = pg_fetch_assoc($rs);
    if($result){
    	$carCompanyId = $result['taxi_company_id'];
    }else{
    	$sql = 'insert into '.API_TABLE_PRE.'taxi_company (taxi_company_name,taxi_company_full_name,city_id) values(\''.mysql_real_escape_string($taxCompany).'\',\''.mysql_real_escape_string($taxCompany).'\','.$cityId.') returning taxi_company_id;';
    	$rs = myDoSqlQuery($sql);
    	$result = pg_fetch_row($rs);
	    $carCompanyId = $result[0];
    }
	
	$sql = 'INSERT INTO '.API_TABLE_PRE.'driver('.
        'username, passwd, email, truename, nickname, mobile_id,  '.
        'car_number, taxi_company_id, driver_number,city_id, rec_username, '.
        'reg_time,reg_ip, last_login_time, last_login_ip, last_login_position, '.
        'status, more_info) VALUES ('.
        '\''.mysql_real_escape_string($username).'\', null, null, \''.mysql_real_escape_string($truename).'\', \''.$nickname.'\', \''.mysql_real_escape_string($mobileId).'\', '.
        '\''.mysql_real_escape_string($carNumber).'\', '.$carCompanyId.',\''.mysql_real_escape_string($driverNumber).'\','.$cityId.',\''.mysql_real_escape_string($recMobile).'\','.
        ' now(), \''.$realIP.'\', now(), \''.$realIP.'\', null, '.
        '1, \''.mysql_real_escape_string($more_info).'\') returning did;';
    $rs = myDoSqlQuery($sql);
    $result = pg_fetch_row($rs);
    $uid = $result[0];
    if($uid){
    	//删除验证码表中记录
    	$sql = 'delete from '.API_TABLE_PRE.'driver_vercode where username=\''.$username.'\'';
    	$rs = myDoSqlQuery($sql);
        responseApiOkResult(array('did'=>$uid));
    }else{
    	responseApiErrorResult(103, 'fail!');
        exit();
    }
}
?>