<?php
require_once dirname(__FILE__) . "/../../server.root.inc.php";

//require_once "6yun/6yun_define.php";
require_once "common/db_func.php";
require_once "API/api_common_func.php";
//
//require_once "6yun/db_func.php";
//require_once "6yun/Ucs/ucs.resource.php";

$db_link = @pg_connect("host=$g_PostgresqlDbHost port=$g_PostgresqlDbPort dbname=$g_PostgresqlDbName user=$g_PostgresqlDbUser password=$g_PostgresqlDbPwd") or
 die("Can not connect to the postgres server!");
@pg_query("Set Names 'UTF8'");

createFolder(API_LOG_PATH,0755);
createFolder(API_DMC_PHOTO_PATH,0755);
