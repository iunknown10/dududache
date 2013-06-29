<?php
/** 
 * index
 * 
 * PHP version 5 
 * 
 * @category  DUDU-BBS-PMC 
 * @package   tu 
 * @author    liangji   2011-03-10 <liangjig@gmail.com> 
 * @copyright 2013-2014 dudu Inc. All rights reserved. 
 * @version   SVN: $Id: pcm_vercode.php,v 1.0 2013-05-18 00:21:11 
 * beijing Exp $ 
 * @link      http://www.dududache.com/
 */
//error_reporting(E_ALL);
require_once "../api.inc.php";

$gApiRequestAcceptType = trim($_SERVER['HTTP_ACCEPT']);

$gApiRequestAcceptType = 'json';
//debugSimpleLogger('Request HTTP_ACCEPT='.$gApiRequestAcceptType);



$api_argus_str = $_REQUEST['api_argus'];
//echo $api_argus_str;


if (0 == strlen($api_argus_str)) {
    header('HTTP/1.0 404 Not Found');
    exit();
}

$api_argus = explode('/', $api_argus_str);
//print_r( $api_argus);




$module = trim($api_argus[0]);
switch ($module){
	case 'register':
		require_once ('dmc_register.php');
		break;
	case 'vercode':
		require_once ('dmc_vercode.php');
		break;
	case 'token':
		require_once ('dmc_token.php');
		break;
	case 'openedcity':
		require_once ('dmc_openedcity.php');
		break;
	case 'lbs':
		$action = trim($api_argus[1]);
		if('current' == $action){
			require_once ('dmc_lbs_current.php');
		}
		break;
	default:
		header('HTTP/1.0 404 Not Found');
		exit;
}
