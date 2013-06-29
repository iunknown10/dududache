<?php
/** 
 * pmc获取已经开通城市列表
 * 
 * PHP version 5 
 * 
 * @category  DUDU-BBS-PMC 
 * @package   tu 
 * @author    liangji   2011-03-10 <liangjig@gmail.com> 
 * @copyright 2013-2014 dudu Inc. All rights reserved. 
 * @version   SVN: $Id: pcm_openedcity.php,v 1.0 2013-06-28 00:21:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */

if (API_METHOD_GET == $_SERVER['REQUEST_METHOD']) {
    //判断现有表中是否存在
    $sql = 'select city_id,city_name from '.API_TABLE_PRE.'city where status=1';
    $rs = myDoSqlQuery($sql);
    if(!$rs){
    	responseApiErrorResult(103, 'fail');
        exit();
    }
    $cityList = array();
    while ($row = pg_fetch_assoc($rs)) {
	    $cityList[] = array(
			    	'city_id'=> $row['city_id'],
			    	'city_name'=> $row['city_name'],
			    	);
	}
    responseApiOkResult(array('city_num'=>$cityList));
    
}
?>