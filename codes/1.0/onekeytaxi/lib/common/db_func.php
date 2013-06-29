<?php
/** 
 * db_func
 * 
 * PHP version 5 
 * 
 * @category  UTS-UCS 
 * @package   common/ 
 * @author    liangji   2011-02-11 <liangji@gmail.com> 
 * @copyright 2010-2011 ChinaTsp Inc. All rights reserved. 
 * @license   http://uts.chinatsp.com/developer/licence ChinaTsp Licence 
 * @version   SVN: $Id: db_func.php,v 0.2.0 2013-05-18 01:22:47 
 * beijing Exp $ 
 * @link      http://www.dududache.com/ 
 */

//require_once 'common/cache_func.php';

/**
 * myDoSqlQuery
 * 
 * 自行封装的执行sql命令的函数
 * 可以记录调试信息，自动对耗时长的查询操作进行缓存
 * 
 * @param string $sql_str 待执行的sql命令字符串.
 * 
 * @return result.
 */
function myDoSqlQuery ($sql_str)
{
    if (defined('DUDU_DEBUG_FLAG')) {
        $tmp_start_time = microtime(true);
        $rs = pg_query($sql_str);
        debugLogSqlStat($rs != false, microtime(true) - $tmp_start_time);
    } else {
        $rs = pg_query($sql_str);
    }
    return $rs;
}

/**
 * myDoSqlQueryAndGenResult
 * 
 * 自行封装的执行sql命令的函数
 * 可以记录调试信息，进行缓存，并返回结果数组
 * 
 * @param string $sql_str 待执行的sql命令字符串.
 * @param string $cached  是否缓存.
 * 
 * @return array.
 */
function myDoSqlQueryAndGenResult ($sql_str, $cached = false)
{
    if ($cached) {
        $key = MD5($sql_str);
        $cachedDataStr = cacheGetSingleValue($key);
        debugSimpleLogger(
            'myDoSqlQueryAndGenResult(): $key=' . $key . ';$sql_str=' . $sql_str .
            ';$cachedDataStr=' . $cachedDataStr
        );
        if ($cachedDataStr != null) {
            global $g_intSqlCachedCounter;
            $g_intSqlCachedCounter ++;
            return json_decode($cachedDataStr, true);
        }
    }
    
    $rs = myDoSqlQuery($sql_str);
    if (false == $rs) {
        return null;
    }
    
    $array_result = array();
    while ($row = mysql_fetch_assoc($rs)) {
        $array_result[] = $row;
    }
    
    if ($cached) {
        cacheSetSingleValue(
            $key, 
            json_encode($array_result), 
            DEFAULT_UPDATE_CACHE_INTERVAL
        );
    }
    return $array_result;
}
