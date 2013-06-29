<?php 
/*---------------------------------------------------------------------------\
|                   常用调试和性能监测方法                                         |
|-------------------------------------------------------------------------|
|         Copyright (C) 2013, Beijing liangji. All rights reserved  |
|         Version: 1.0                                                                       |
|                                                                                                  | 
|        //调试模式开关，可放在配置文件中定义
|        define('DUDU_DEBUG_FLAG',TRUE);
\---------------------------------------------------------------------------*/
//用于计算页面执行时间的起始时间点,单位:秒，小数点精确到微秒
$g_pageStartMicroTime=microtime(true);

define('DEFAULT_DEBUG_FILENAME',PLUS_SITE_TEMP_PATH.'duduDebug_'.getNowDateStr().'.log');

//简单的输出错误信息到日志文件的方法
function debugSimpleLogger($strlog,$log_file=DEFAULT_DEBUG_FILENAME)
{
    if(defined('DUDU_DEBUG_FLAG'))
    {
        $file=@fopen($log_file,'a');
        if($file)
        {
           fwrite($file,formatTimestampForView(time()).'>> '.$strlog."\n");
           fclose($file);
        }
        /*
        else if($log_file!=DEFAULT_DEBUG_FILENAME)
        {//强制输出到当前目录下
            $log_file='duduTest.log';
            $file=@fopen($log_file,'a');
            if($file)
            {
               fwrite($file,gmdate("M d Y H:i:s", time()).'>> '.$strlog."\n");
               fclose($file);
            }
        }   */
    }
}

//记录SQL执行状态
//包括一次页面执行过程中的sql查询次数和总的用时
//输入参数： 
//    $sql_ok_or_failed :   sql执行成功还是失败，true:成功，false:失败
//    $sql_elapsed_time : 执行耗时，缺省为0，,单位:秒，小数点精确到微秒
//返回： 
//    无
function  debugLogSqlStat($sql_ok_or_failed,$sql_elapsed_time=0)
{
    if(defined('DUDU_DEBUG_FLAG'))
    {
       global $g_intSqlOkCounter,$g_intSqlFailedCounter,$g_intSqlElapsedTime;
       $sql_ok_or_failed?$g_intSqlOkCounter++:$g_intSqlFailedCounter++;
       $g_intSqlElapsedTime+=$sql_elapsed_time;
    }
}

//获得sql状态统计数据
function  debugGetSqlStat()
{
    if(defined('DUDU_DEBUG_FLAG'))
    {
        global $g_intSqlCachedCounter,$g_intSqlOkCounter,$g_intSqlFailedCounter,$g_intSqlElapsedTime;
        return  array(
                'sql_cached_counter'=>$g_intSqlCachedCounter,
                'sql_ok_counter'=>$g_intSqlOkCounter,
                'sql_failed_counter'=>$g_intSqlFailedCounter,
                'sql_elapsed_time'=>$g_intSqlElapsedTime
            );
    }
    else
        return -1;
}

//记录NOSQL执行状态
//包括一次页面执行过程中的nosql查询次数和总的用时
//输入参数： 
//    $nosql_ok_or_failed :   nosql执行成功还是失败，true:成功，false:失败
//    $nosql_elapsed_time : 执行耗时，缺省为0，,单位:秒，小数点精确到微秒
//返回： 
//    无
function  debugLogNoSqlStat($nosql_ok_or_failed,$nosql_elapsed_time=0)
{
    if(defined('DUDU_DEBUG_FLAG'))
    {
       global $g_intNoSqlOkCounter,$g_intNoSqlFailedCounter,$g_intNoSqlElapsedTime;
       $nosql_ok_or_failed?$g_intNoSqlOkCounter++:$g_intNoSqlFailedCounter++;
       $g_intNoSqlElapsedTime+=$nosql_elapsed_time;
    }
}

//获得nosql状态统计数据
function  debugGetNoSqlStat()
{
    if(defined('DUDU_DEBUG_FLAG'))
    {
        global $g_intNoSqlCachedCounter,$g_intNoSqlOkCounter,$g_intNoSqlFailedCounter,$g_intNoSqlElapsedTime;
        return  array(
                'nosql_cached_counter'=>$g_intNoSqlCachedCounter,
                'nosql_ok_counter'=>$g_intNoSqlOkCounter,
                'nosql_failed_counter'=>$g_intNoSqlFailedCounter,
                'nosql_elapsed_time'=>$g_intNoSqlElapsedTime
            );
    }
    else
        return -1;
}

// 记录缓存执行状态
// 2011-7-7 add by xichengyuan
// 输入参数： 
//    $cache_ok_or_failed :   cache执行成功还是失败，true:成功，false:失败
function debugLogCacheStat($cache_ok_or_failed)
{
	if(defined('DUDU_DEBUG_FLAG'))
    {
       global $g_intCacheOkCounter,$g_intCacheFailedCounter;
       $cache_ok_or_failed?$g_intCacheOkCounter++:$g_intCacheFailedCounter++;
    }
}


// 获得缓存使用情况统计数据
// 2011-7-7 modify by xichengyuan
function debugGetCacheStat()
{
    if(defined('DUDU_DEBUG_FLAG'))
    {
        global $g_intCacheOkCounter,$g_intCacheFailedCounter;
        return  array(
                'cache_ok_counter'=>$g_intCacheOkCounter,
                'cache_failed_counter'=>$g_intCacheFailedCounter
            );
    }
    else
        return -1;
}

//得到页面执行耗时,单位:秒，小数点精确到微秒
function  debugGetPageElapsedTime()
{
    global $g_pageStartMicroTime;
    return microtime(true)-$g_pageStartMicroTime;
}

//可用于在页面结束时输出调试信息
//缺省包括页面执行时间
//如果设定了全局变量$INNOV_DEBUG_FLAG为TRUE，可以附加输出sql和cache统计数据
function  debugOutputAtPageEnd()
{
    echo  '<!--DEBUG ',"\n",
          'Request time:'.$_SERVER['REQUEST_TIME'],"\n",
          'End time:'.microtime(true),"\n",
          'Elapsed time:'.debugGetPageElapsedTime().' seconds.',"\n";
    
    if(defined('DUDU_DEBUG_FLAG'))
        echo 'SQL stat: '.print_r(debugGetSqlStat(),true),"\n",
             'NOSQL stat: '.print_r(debugGetNoSqlStat(),true),"\n",
             'Cache stat: '.print_r(debugGetCacheStat(),true);

    echo ' -->',"\n";
}
