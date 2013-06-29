<?php 
/*---------------------------------------------------------------------------\
|                    Server config  file                                                        |
|----------------------------------------------------------------------------|
|         Copyright (C) 2009, Beijing ChenHui. All rights reserved           |
|                                                                                                             |
\---------------------------------------------------------------------------*/
//error_reporting(E_ERROR);
//调试模式开关
define('DUDU_DEBUG_FLAG',TRUE);

//HTTP传输协议设置,http://或https
define('HTTP_TRANS_PROTOCOL','http://');

//服务器所在时区，缺省为北京时间:8.0
define('SERVER_TIME_ZONE', 8);

//短信验证码几分钟内有效
define('SMS_VERCODE_VALID', 30);

//API数据库表前缀
define('API_TABLE_PRE', 'dudu_');

define('COORDINATE_SYSTEM', 4326);
define('COORDINATE_DISTINCE_SYSTEM', 26986);


//相关站点域名设置
define('PLUS_SITE_ROOT_URL', HTTP_TRANS_PROTOCOL.THIS_SITE_DOMAIN.'/');



//lib库的位置
define('DUDU_LIB_PATH',
PLUS_SITE_HTDOCS_PATH . '/../lib/' );


//MYSQL数据库连接参数
$g_PostgresqlDbHost = "127.0.0.1";                                  // 数据库主机名
$g_PostgresqlDbPort = 5432;
$g_PostgresqlDbUser = "dudu";                                       // 数据库用户名
$g_PostgresqlDbPwd = "dudu";                                // 数据库密码
$g_PostgresqlDbName = 'dudu';											//数据库名





