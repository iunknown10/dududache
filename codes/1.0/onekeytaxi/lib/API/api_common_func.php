<?php 
/*---------------------------------------------------------------------------\
|                   API相关常用方法                                            |
|----------------------------------------------------------------------------|
|         Copyright (C) 2013-05-18, Beijing liangji. All rights reserved     |
|         Version: 1.0                                                       |
|                                                                            | 
\---------------------------------------------------------------------------*/

require_once 'common/common_define.php';
require_once 'common/common_func.php';
require_once 'common/MyCurl.php';
require_once "common/xml_func.php";
require_once "API/api_common_define.php";

//用于API处理程序返回标准的出错信息
function responseApiErrorResult($error_code,$error_desc)
{
    $arrayResult=array();
    $arrayResult[API_RESULT_STATUS]=API_RESULT_ERROR;
    $arrayResult[API_RESULT_ERROR_CODE]=$error_code;
    $arrayResult[API_RESULT_ERROR_DESC]=$error_desc;
    
    //Added by chenhui,2011-01-24
    debugSimpleLogger(
                'responseApiErrorResult(): ('
                .$error_code.')'.$error_desc
                .'!'."\n".'$_REQUEST='.print_r($_REQUEST,true)
                ."\n".'$_SERVER='.print_r($_SERVER,true),
                API_LOG_PATH.'ApiError_'.getNowDateStr().'.log'
       );
    
    return responseApiResult($arrayResult);
}
//用于API处理程序返回标准的成功信息
function responseApiOkResult($arrayReturnVals=NULL)
{
    $arrayResult=array();
    $arrayResult[API_RESULT_STATUS]=API_RESULT_OK;
    
    if(NULL!=$arrayReturnVals)
        $arrayResult[API_RESULT_DATA]=$arrayReturnVals;
    //输出调试用数据字段
    if(defined('DUDU_DEBUG_FLAG'))
    {
    debugSimpleLogger(
                'responseApiOKResult(): '.
                print_r($arrayReturnVals,true)
                ."\n".'$_REQUEST='.print_r($_REQUEST,true),
                API_LOG_PATH.'ApiOK_'.getNowDateStr().'.log'
       );
    }

    return responseApiResult($arrayResult);
}

//用于API处理程序根据请求返回指定格式的应答数据
function responseApiResult($arrayResult)
{
    global $gApiRequestAcceptType;
    
    //输出调试用数据字段
    /*if(defined('DUDU_DEBUG_FLAG'))
    {
        $arrayResult[API_RESULT_DEBUG_DATA]=array(
                API_RESULT_DEBUG_ELAPSED_TIME=>debugGetPageElapsedTime(),
                API_RESULT_DEBUG_SQL_STAT=>debugGetSqlStat(),
                API_RESULT_DEBUG_CACHE_STAT=>debugGetCacheStat()
            );
    }*/
    

    if(strcasecmp(API_ACCEPT_TYPE_JSON,$gApiRequestAcceptType)==0)
    {
        echo json_encode($arrayResult);
    }
    else
    {
        echo array2xml($arrayResult,0);
    }
}
//通用的调用API的函数
function commonCallApi(
        $method,     //请求方式
        $api_url,    //API完整url地址
        $array_cookies=NULL, //cookie参数键-值数组，比如访问令牌
        $array_argvs=NULL,   //用于POST或PUT的参数键-值数组
        $default_accept_type=API_ACCEPT_TYPE_JSON  //指定返回的内容编码格式
    )
{
    $objCurl = &new MyCurl($api_url,true,30,4,false,false,false);
    $objCurl->setAccept($default_accept_type);
    if(NULL!=$array_cookies && count($array_cookies)>0)
    {
        foreach($array_cookies as $key=>$value)
        {
            $objCurl->setTempCookie($key,$value);
        }
    }
    
    /*
    $post_string='';
    if(NULL!=$array_argvs && count($array_argvs)>0)
    {
        foreach($array_argvs as $key=>$value)
        {
            $post_string.='&'.$key.'='.urlencode($value);
        }
    }*/

    if(API_METHOD_DELETE==$method)
    {
        $objCurl->setDelete();
    }
    else if(API_METHOD_PUT==$method)
    {
        //$objCurl->setPut($array_argvs);
        //未调试通过，待进一步研究
    }
    else if(API_METHOD_POST==$method)
    {
        //$objCurl->setPost($post_string);
        $objCurl->setPost($array_argvs);
    }
    else
    {   //GET
         
    }
    
    $objCurl->createCurl();
    $error = $objCurl->hasError();
    if ($error) {
        $objCurl=NULL;
        return NULL;
    }
    
    $responseString = $objCurl->__tostring();
    if(file_exists('/tmp'))
        debugSimpleLogger("commonCallApi() $method api_url=$api_url;responseString=$responseString",'/tmp/UtsApiTest'.getNowDateStr().'.log');
    else
        debugSimpleLogger("commonCallApi() $method api_url=$api_url;responseString=$responseString",'c:/DUDUApiTest'.getNowDateStr().'.log');
    
    $objCurl=NULL;

    if(strcasecmp(API_ACCEPT_TYPE_JSON,$default_accept_type)==0)
        return json_decode($responseString,true);
    else
        return @xmlStr2array($responseString);
}
function createFolder($path,$auth = 0777) 
{ 
	if (!file_exists($path)) 
	{ 
		createFolder(dirname($path)); 
		mkdir($path, $auth); 
	} 
} 
function postgisToPoint($postgisPoint){
	preg_match("/POINT\((-?[0-9]*\.[0-9]*) (-?[0-9]*\.[0-9]*)\)/", $postgisPoint, $result);
	
	unset($result[0]); //remove first element from array
	//reverse array, because postgis works with lng-lat, while gmaps with lat-lng
	return $result;
}
/***获取每天每个订单，当天第一个订单时使用**/
function getFirstOrderIdByType($type){
	if($type==1){
		return $type.'0000000001';
	}elseif($type==2){
		return $type.'0000000001';
	}else{
		return false;
	}
}
/**
 * $type :driver/passenger
 * $token
 * $userid:$did/$pid
 * */
function checkToken($type,$token,$userid){
	if(trim($type) == ''
		|| trim($token) == ''
		|| trim($userid) == ''
	){
		return false;
	}
	if($type == DUDU_DRIVER){
		$table = API_TABLE_PRE.'driver_token';
		$conditionUseridColumn = 'did';
	}elseif ($type == DUDU_PASSENGER){
		$table = API_TABLE_PRE.'passenger_token';
		$conditionUseridColumn = 'pid';
	}else{
		return false;
	}
	$sql = 'select token from '.$table.' where '.$conditionUseridColumn.' = '.$userid;
	$rs = myDoSqlQuery($sql);
	$tokenInfo = pg_fetch_assoc($rs);
	if($tokenInfo['token']==$token){
		return true;
	}else{
		return false;
	}
}