<?php
class Memcached_Config {
        //持续连接
        const PERSISTENT = 1;
        //连接超时，秒级
        const TIMEOUT = 1;
        //重试时间间隔，秒级，勿改
        const RETRY_INTERVAL = 0;
        //初始状态，勿改
        const INIT_STATUS = TRUE;

        //memcached配置，两个机房的配置都写进去，通过M_CURRENT_CONF来指定使用哪个机房的配置
        //MemCached_Wrapper类不能同时使用两个机房的配置
        public static $arrAllMemCacheServer;
        public static $arrMemCacheServer;

}

MemCached_Config :: $arrAllMemCacheServer['dudu']['resource'] = array 
        (
                array
                (
                        'host'     =>   Memcached_Server_Config::$servers_config['resource']['ip'],
                        'port'     =>   Memcached_Server_Config::$servers_config['resource']['port'],
                        'weight'   =>   1,
                ),
        );

$strConf = defined("M_CURRENT_CONF") ? M_CURRENT_CONF : 'dudu';
MemCached_Config :: $arrMemCacheServer = MemCached_Config :: $arrAllMemCacheServer[$strConf];

?>
