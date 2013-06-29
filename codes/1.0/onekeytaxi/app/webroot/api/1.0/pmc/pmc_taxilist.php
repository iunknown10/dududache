<?php
/** 
 * pmc获取周边出租车
 * 
 * PHP version 5 
 * 
 * @category  DUDU-BBS-PMC 
 * @package   tu 
 * @author    liangji   2011-03-10 <liangjig@gmail.com> 
 * @copyright 2013-2014 dudu Inc. All rights reserved. 
 * @version   SVN: $Id: pmc_taxilist.php,v 1.0 2013-05-26 10:56:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */

if (API_METHOD_GET == $_SERVER['REQUEST_METHOD']) {
    //判断手机号
    $pid = trim($api_argus[1]);
    $userLocationLng = trim($api_argus[2]);
    $userLocationLat = trim($api_argus[3]);
    $distince = trim($api_argus[4]);
    if (empty($pid) && empty($userLocationLng) && empty($userLocationLat)) {
        responseApiErrorResult(901, 'para empty error!');
        exit();
    }
    $distince = $distince? $distince:20000;
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
    //判断现有表中是否存在
    $sql = 'select username from '.API_TABLE_PRE.'passenger_position where pid='.$pid;
    $rs = myDoSqlQuery($sql);
    $num = pg_num_rows($rs);
    if($num){
    	$sql = 'delete from '.API_TABLE_PRE.'passenger_position where pid='.$pid;
    	$rs = myDoSqlQuery($sql);
    }
    //更新用户当前位置
    $sql = 'insert into  '.API_TABLE_PRE.'passenger_position (pid,location) VALUES ('.$pid.',ST_GeomFromText(\'POINT('.$userLocationLng.' '.$userLocationLat.')\','.COORDINATE_SYSTEM.'));';
    $rs = myDoSqlQuery($sql);
    if(0 == $rs){
    	responseApiErrorResult(null, 'Failed to update position !');
    }else{
		$sql = 'SELECT d.did,d.username,ST_AsText(d.location)  as point_info,ST_Distance(ST_Transform(p.location,'.COORDINATE_DISTINCE_SYSTEM.'),ST_Transform(d.location,'.COORDINATE_DISTINCE_SYSTEM.')) as distance from dudu_passenger_position p,dudu_driver_position d where p.pid='.$pid.' and ST_DWithin(ST_Transform(p.location,'.COORDINATE_DISTINCE_SYSTEM.'), ST_Transform(d.location,'.COORDINATE_DISTINCE_SYSTEM.'),'.$distince.') order by distance;';
		
		$rs = myDoSqlQuery($sql);
		$taxiList = array();
	    while ($row = pg_fetch_assoc($rs)) {
	    	$pointInfo = postgisToPoint($row['point_info']);
	    	$taxiInfo = get_taxi_info($row['did']);
		    $taxiList[] = array(
				    	'did'=> $row['did'],
				    	'nickname'=> $taxiInfo['nickname'],
				    	'car_number'=> $taxiInfo['car_number'],
				    	'taxi_company'=> $taxiInfo['taxi_company_name'],
				    	'taxi_lng'=> round($pointInfo[1],4),
				    	'taxi_lat'=> round($pointInfo[2],4),
				    	);
		}
		responseApiOkResult(
			array(
				'taxi_num' => count($taxiList),
				'taxi_list' => $taxiList
			)
		);
    }
}
function get_taxi_info($did){
	$sql = 'select d.nickname,d.car_number,t.taxi_company_name from dudu_driver d,dudu_taxi_company t where did='.$did.' and d.taxi_company_id=t.taxi_company_id;';
	$rs = myDoSqlQuery($sql);
	if(0 == $rs){
		return false;
	}else{
		$row = pg_fetch_assoc($rs);
		return $row;
	}
}