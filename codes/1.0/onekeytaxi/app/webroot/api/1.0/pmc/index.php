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





$module = trim($api_argus[0]);
switch ($module){
	case 'register':
		require_once ('pmc_register.php');
		break;
	case 'vercode':
		require_once ('pmc_vercode.php');
		break;
	case 'token':
		require_once ('pmc_token.php');
		break;
	case 'taxilist':
		require_once ('pmc_taxilist.php');
		break;
	case 'driverdetail':
		require_once ('pmc_driverdetail.php');
		break;
	case 'taxi':
		$action = trim($api_argus[1]);
		if('green' == $action){
			require_once ('pmc_taxi_green.php');
		}else if('confirm' == $action){
			require_once ('pmc_taxi_confirm.php');
		}else if('yellow' == $action){
			require_once ('pmc_taxi_yellow.php');
		}else{
			header('HTTP/1.0 404 Not Found');
			exit;
		}
		break;
	case 'driverresp':
		require_once ('pmc_driverresp.php');
		break;
	case 'driver':
		$action = trim($api_argus[1]);
		if('location' == $action){
			require_once ('pmc_driver_location.php');
		}else if('evaluate' == $action){
			require_once ('pmc_driver_evaluate.php');
		}else{
			header('HTTP/1.0 404 Not Found');
			exit;
		}
		break;
	case 'compliant':
		require_once ('pmc_compliant.php');
		break;
	default:
		header('HTTP/1.0 404 Not Found');
		exit;
}
