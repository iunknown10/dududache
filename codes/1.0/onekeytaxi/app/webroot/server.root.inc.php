<?php 
/*---------------------------------------------------------------------------\
|                    Server  header file                                                       |
|----------------------------------------------------------------------------|
|         Copyright (C) 2010, Beijing ChenHui. All rights reserved        |
|                                                                                                        | 					
\---------------------------------------------------------------------------*/
//本站点域名
define('THIS_SITE_DOMAIN', 
            isset($_SERVER['HTTP_HOST']) ? 
                    $_SERVER['HTTP_HOST'] : 'local.dududache.com'
       ); 

//主要路径设置
define('PLUS_SITE_HTDOCS_PATH', dirname(__FILE__).'/');  
define('PLUS_SITE_LIB_PATH',PLUS_SITE_HTDOCS_PATH.'../../lib/'); 
define('PLUS_SITE_CONFIG_PATH', PLUS_SITE_HTDOCS_PATH.'../../config/'); 
define('PLUS_SITE_TEMP_PATH',PLUS_SITE_HTDOCS_PATH.'../../temp_data/'); 

//装载与该站点域名对应的配置参数
require(PLUS_SITE_CONFIG_PATH.'config.php');

//设置包含zend framework等lib库的路径
set_include_path( 
        get_include_path()
        . PATH_SEPARATOR . PLUS_SITE_LIB_PATH
    );
