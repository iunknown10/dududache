<?php 
/*---------------------------------------------------------------------------\
|                   API相关宏定义                                                                  |
|----------------------------------------------------------------------------|
|         Copyright (C) 2010, Beijing liangji. All rights reserved           |
|         Version: 1.0                                                                                 |
|                                                                                                             | 
\---------------------------------------------------------------------------*/
define('API_ACCEPT_TYPE_XML','xml');
define('API_ACCEPT_TYPE_JSON','json');

define('API_METHOD_GET','GET');
define('API_METHOD_PUT','PUT');
define('API_METHOD_POST','POST');
define('API_METHOD_DELETE','DELETE');

define('API_RESULT_OK','OK');
define('API_RESULT_ERROR','ERR');
define('API_RESULT_STATUS','resp_status');
define('API_RESULT_DATA','resp_data');
define('API_RESULT_ERROR_CODE','error_code');
define('API_RESULT_ERROR_DESC','error_desc');
define('API_RESULT_DEBUG_DATA','resp_debug');
define('API_RESULT_DEBUG_ELAPSED_TIME','elapsed_time');
define('API_RESULT_DEBUG_SQL_STAT','sql_stat');
define('API_RESULT_DEBUG_CACHE_STAT','cache_stat');

define('API_LOG_PATH',PLUS_SITE_TEMP_PATH.'/API_log/');
define('API_DMC_PHOTO_PATH',PLUS_SITE_TEMP_PATH.'/upload/API_DMC/photo/');

