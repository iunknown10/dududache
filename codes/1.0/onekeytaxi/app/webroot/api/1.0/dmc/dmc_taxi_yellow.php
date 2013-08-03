<?php
/** 
 * dmc获取约车请求（黄车）
 * 
 * PHP version 5 
 * 
 * @category  DUDU-BBS-DMC 
 * @package   tu 
 * @author    liangji   2011-03-10 <liangjig@gmail.com> 
 * @copyright 2013-2014 dudu Inc. All rights reserved. 
 * @version   SVN: $Id: dmc_taxi_yellow.php,v 1.0 2013-07-30 23:09:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */

if (API_METHOD_GET == $_SERVER['REQUEST_METHOD']) {
    $token = $_COOKIE['token'];
    
    $did = trim($api_argus[2]);
    $lng = trim($api_argus[3]);
    $lat = trim($api_argus[4]);

     //判断非空
    if(trim($token) == ''
		|| trim($did) == ''
		|| trim($lng) == ''
		|| trim($lat) == ''
	){
		responseApiErrorResult(901, 'para error!');
        exit();
	}
	//检查token
	if(!checkToken(DUDU_DRIVER,$token,$did)){
		responseApiErrorResult(902, 'token verify error!');
        exit();
	}
	
	$sql = 'select order_id,pid,use_time,start_point,ST_AsText(start_position)  as start_point_info,end_point,ST_AsText(end_position)  as end_point_info,tip,voice_url,'.
		'ST_Distance(ST_Transform(start_position,'.COORDINATE_DISTINCE_SYSTEM.'),ST_Transform(ST_SetSRID(ST_Point('.$lng.','.$lat.'),'.COORDINATE_SYSTEM.'),'.COORDINATE_DISTINCE_SYSTEM.')) as distance '. 
		'from '.API_TABLE_PRE.'order_reserve where status=0 and ST_DWithin(ST_Transform(start_position,'.COORDINATE_DISTINCE_SYSTEM.'), ST_Transform(ST_SetSRID(ST_Point('.$lng.','.$lat.'),'.COORDINATE_SYSTEM.'),'.COORDINATE_DISTINCE_SYSTEM.'),'.DUDU_YELLOW_DISTANCE.')'.
		'order by distance';
   	$rs = myDoSqlQuery($sql);
   	$num = 0;
   	while ($row = pg_fetch_assoc($rs)) {
   		$startPointInfo = postgisToPoint($row['start_point_info']);
   		$endPointInfo = postgisToPoint($row['end_point_info']);
   		
   		$orderList[] = array(
   			'order_id'=>$row['order_id'],
   			'pid'=>$row['pid'],
   			'leave_time'=>$row['use_time'],
   			'tip'=>$row['tip'],
   			'start_address'=>$row['start_point'],
   			'start_lng'=>round($startPointInfo[1],4),
   			'start_lat'=>round($startPointInfo[2],4),
   			'end_address'=>$row['end_point'],
   			'end_lng'=>round($endPointInfo[1],4),
   			'end_lat'=>round($endPointInfo[2],4),
   			'distance'=> round($row['distance']),
   			'voice'=>$row['voice_url']?PLUS_SITE_ROOT_URL.$row['voice_url']:null,
   		);
   		$num++;
   	}
	
   	responseApiOkResult(
	   	array(
	   		'order_num' => $num,
	   		'order_list'=>$orderList,
	   	)
   	);

    
}
?>