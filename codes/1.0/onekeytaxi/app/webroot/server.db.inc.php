<?php
/*---------------------------------------------------------------------------\
|                    DB config header file                                   |
|----------------------------------------------------------------------------|
|         Copyright (C) 2010, Beijing Liangji. All rights reserved           |
|                                                                            | 					
\---------------------------------------------------------------------------*/
require_once('Zend/Db.php'); 

// Database parameters
$gArrayDbConfig = array(
       'type' => 'PDO_MYSQL',
       'db'   => array(
           'host' 		=> $g_MySqlDbHost,
           'username' 	=> $g_MySqlDbUser,
           'password' 	=> $g_MySqlDbPwd,
           'dbname'  	=> $g_MySqlDbName
        )
); 

// Make a global database adapter and tell Zend_Db_Table to use it
$gObjDb = Zend_Db::factory($gArrayDbConfig['type'], $gArrayDbConfig['db']);
Zend_Db_Table::setDefaultAdapter($gObjDb);
$gObjDb->query('set names utf8');
?>
