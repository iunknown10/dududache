<?php 
/*---------------------------------------------------------------------------\
|                   常用方法                                                                            |
|----------------------------------------------------------------------------|
|         Copyright (C) 2010, Beijing ChenHui. All rights reserved           |
|         Version: 1.0                                                                                 |
|                                                                                                             | 
\---------------------------------------------------------------------------*/
require_once 'common_define.php';

//自行改进Base64变种编码函数，将标准编码的「+」和「/」改成了「!」和「-」,以避免URL解析出错
function mySpecEncode($srcData)
{
    return str_replace(
                '+', '!',
                str_replace(
                    '/', '-',
                    base64_encode($srcData)
                )
             );
}

//自行改进Base64变种解码函数
function mySpecDecode($srcData)
{
    return base64_decode(
                str_replace(
                    '!', '+',
                    str_replace('-', '/', $srcData )
                 )
           );
             
}


//获取来访者浏览器类型
function getBrowserType()
{
    if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE"))
        return BROWSER_TYPE_IE;
    else if(strpos($_SERVER["HTTP_USER_AGENT"],"Firefox"))
        return BROWSER_TYPE_FIREFOX;
    else if(strpos($_SERVER["HTTP_USER_AGENT"],"Chrome"))
        return BROWSER_TYPE_CHROME;
    else if(strpos($_SERVER["HTTP_USER_AGENT"],"Safari"))
        return BROWSER_TYPE_SAFARI;
    else if(strpos($_SERVER["HTTP_USER_AGENT"],"Opera"))
        return BROWSER_TYPE_OPERA;
    else 
        return BROWSER_TYPE_UNKOWN;

}

//获取当前页面url
function getCurrentUrl($includeHost=true) 
{
   $url='';
   if($includeHost)
   {
       $arrayTmp=explode('/',$_SERVER['SERVER_PROTOCOL']);
       $url.=strtolower($arrayTmp[0]).'://'.$_SERVER['HTTP_HOST'];
   }
   if (isset($_SERVER['REQUEST_URI'])) {
       $url .= $_SERVER['REQUEST_URI'];
   }
   else {
       $url .= $_SERVER['PHP_SELF'];
       $url .= empty($_SERVER['QUERY_STRING'])?'':'?'.$_SERVER['QUERY_STRING'];
   }
   return $url;
}
/*
function getCurrentUrl()
{
    global $_SERVER;

     //Filter php_self to avoid a security vulnerability.
    $php_request_uri = htmlentities(substr($_SERVER['REQUEST_URI'], 0,
    strcspn($_SERVER['REQUEST_URI'], "\n\r")), ENT_QUOTES);

    if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
        $protocol = 'https://';
    } else {
        $protocol = 'http://';
    }
    $host = $_SERVER['HTTP_HOST'];
    if ($_SERVER['SERVER_PORT'] != '' &&
        (($protocol == 'http://' && $_SERVER['SERVER_PORT'] != '80') ||
        ($protocol == 'https://' && $_SERVER['SERVER_PORT'] != '443'))) {
            $port = ':' . $_SERVER['SERVER_PORT'];
    } else {
        $port = '';
    }
    return $protocol . $host . $port . $php_request_uri;
}
*/

//返回当前时间戳（单位到毫秒）
function getMilliSecond ()
{ 
    list($usec, $sec) = explode(" ",microtime()); 
    return $sec.sprintf("%03d",(float)$usec*1000); 
} 

//格式化单位到秒的时间值的显示
//$timestamp: 自1970年1月1日0时起的秒数
//$onlydate: 是否至显示日期，缺省为false
//$sepc_time_zone:指定时区，不指定时，将按照当前已登录用户设定时区->服务器设定时区->北京时区为优先级来依次判断取值
function formatTimestampForView($timestamp,$onlydate=false,$sepc_time_zone=NULL)
{
   global $g_fUserLogonTimeZone;
   
   if(isset($sepc_time_zone))
      $time_zone=$sepc_time_zone;
   else if(isset($g_fUserLogonTimeZone))
      $time_zone=$g_fUserLogonTimeZone;
   else if(defined('SERVER_TIME_ZONE'))
      $time_zone=SERVER_TIME_ZONE;
   else
      $time_zone=8;
   
   if($onlydate)
       return $timestamp==0? '--------' :gmdate("Y-m-d", $timestamp+$time_zone*3600);
   else
       return $timestamp==0? '--------' :gmdate("Y-m-d H:i", $timestamp+$time_zone*3600);
}


//获得用于显示的中文日期字符串
function formatDateForView($dateStr)
{
    $resultStr="";
    $tok=strtok($dateStr,"-");
    if($tok) $resultStr=$tok."年";
    $tok=strtok("-");
    if($tok) $resultStr=$resultStr.($tok/1)."月";
    $tok=strtok("-");
    if($tok) $resultStr=$resultStr.($tok/1)."日";
    
    return $resultStr;
}

//获得当前日期,格式为YYYY-MM-DD
function getNowDateStr()
{
    $nowTimeArray=@getdate(time());
    
    return $nowTimeArray[year]."-".$nowTimeArray[mon]."-".$nowTimeArray[mday];
}

//获得当前时间,格式为YYYY-MM-DD hh:mm:ss
function getNowTimeStr()
{
    return strftime("20%y-%m-%d %H:%M:%S",time());
    
    /*
    $nowTimeArray=localtime(time(),1);
    return (1900+$nowTimeArray[tm_year])."-".$nowTimeArray[tm_mon]."-".$nowTimeArray[tm_mday]." ".$nowTimeArray[tm_hour].":".$nowTimeArray[tm_min].":".$nowTimeArray[tm_sec];
    
    $nowTimeArray=getdate(time());
    return $nowTimeArray[year]."-".$nowTimeArray[mon]."-".$nowTimeArray[mday]." ".$nowTimeArray[hours].":".$nowTimeArray[minutes].":".$nowTimeArray[seconds];    
    */
}
//获得指定时间,格式为YYYY-MM-DD hh:mm:ss
function getTimeStr($time){
	return strftime("20%y-%m-%d %H:%M:%S",$time);
}

//得到格式化的日期字符串,格式为YYYY-MM-DD
function formatDateStr($dateStr)
{
    $dateArray=explode("-",$dateStr);
    
    $strTemp=sprintf("%04d-%02d-%02d",$dateArray[0],$dateArray[1],$dateArray[2]);
    
    if("0000-00-00"==$strTemp)
    {
        return "";
    }
    return $strTemp;
}

//获取原始的HTTP传入参数，值类型为字符串。
//注意出于安全，调用该方法获得结果不能直接用于SQL语句
function originalReqChrStr($argvName)
{
    $argValue=trim($_GET[$argvName]);
    
    if($argValue=='')
    {
        $argValue=$_POST[$argvName];
    }
    if (true==get_magic_quotes_gpc()) 
    {    //恢复原样的字符串
        $newArgValue = stripslashes($argValue);
        if(strlen($newArgValue)>0)
            $argValue=$newArgValue;
    }
    return trim($argValue); 
}


//安全获取HTTP传入参数，值类型为字符串
function safeReqChrStr($argvName)
{
    $argValue=trim(@$_GET[$argvName]);
    
    if($argValue=='')
    {
        $argValue=@$_POST[$argvName];
    }
    if (false==get_magic_quotes_gpc()) 
    {
        $newArgValue = @mysql_real_escape_string($argValue);
        if(strlen($newArgValue)>0)
            $argValue=$newArgValue;
    }
    return trim($argValue); 
}

//安全获取HTTP传入参数，值类型为数字
function safeReqNumStr($argvName)
{
    $argValue=trim(@$_GET[$argvName]);
    if($argValue=="")
    {
        $argValue=@$_POST[$argvName];
    }

    if(!is_numeric($argValue))
    {
        return "";
    }
    
    return trim($argValue); 
}

//安全获取HTTP传入参数，值类型为日期型字符串,格式为YYYY-MM-DD
function safeGetHttpArgvDateStr($argvName)
{
    $argValue=trim($_GET[$argvName]);
    if($argValue=="")
    {
        $argValue=$_POST[$argvName];
    }
    return formatDateStr($argValue); 
}

//获取来访者真实ip
function realip()
{
	if(getenv('HTTP_CLIENT_IP')){
		$ip=getenv('HTTP_CLIENT_IP');
	}elseif(getenv('HTTP_X_FORWARDED_FOR')){
		$ip=getenv('HTTP_X_FORWARDED_FOR');
	}elseif(getenv('REMOTE_ADDR')){
		$ip=getenv('REMOTE_ADDR');
	}else{
		$ip=$HTTP_SERVER_VARS['REMOTE_ADDR'];
	}

	return $ip;
}

//显示重定向页面内容
function redirect($url,$message)
{
	echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>";
	echo "<p>$message</p>\n";
	echo "<meta http-equiv=\"refresh\" content=\"1;url=$url\">\n";
}

//打印错误信息页面并终止处理
function error_exit($url,$message)
{
    echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>";
	echo "<p align=center>$message</p>\n";
	echo "<p align=center><br><input type=button value=' << 返回，重新输入 '  name=B1 onclick='history.back(-1)'></p>";
	echo "<p align=center><br>或者,<a href=\"$url\">点击这里到相关页面</a></p>\n";
    
    global $g_objPageCache;
    if(isset($g_objPageCache))
        $g_objPageCache->write(); 
    
    exit;
}

//获取文件扩展名
function getFileExtName($file_name)
{
    $file_ext='';
    $lastPosn=mb_strrpos($file_name,'.');
    if($lastPosn>=0)
        $file_ext=mb_substr($file_name,$lastPosn+1);
        
    return strtolower($file_ext);
}

//暂时保留，需改为调用debugSimpleLogger()
function logger($strlog,$log_file='6yunTest.log')
{

}
//简单的判断手机号
function checkMobilePhone($phoneNumber){
	$phoneNumber = trim($phoneNumber);
	if(strlen($phoneNumber)==11){
		$regex = "/13[0-9]{9}|15[0|1|2|3|5|6|7|8|9]\d{8}|18[0|1|2|3|5|6|7|8|9]\d{8}|147\d{8}/";
		 preg_match_all($regex,$phoneNumber, $phoneFlag);
		 if($phoneFlag[0][0]){
		 	return true;
		 }else{
		 	return false;
		 }
	}else {
		return false;
	}
}
//改善短信
function sendSms($mobilePhone,$content){
	return true;
}
//生成token
function generateToken($mobileId,$mobileNumber,$rand){
	if($mobileId == ''
		|| $mobileNumber == ''
		|| $rand == ''
	){
		return false;
	}else{
		$mobileIdArr = str_split($mobileId);
		$mobileNumberArr = str_split($mobileNumber);
		return md5(
				$mobileIdArr[0].$mobileIdArr[2].$mobileIdArr[4].$mobileIdArr[6].
				$mobileNumberArr[1].$mobileNumberArr[3].$mobileNumberArr[5].$mobileNumberArr[7].
				$rand
				);
		
	}
}
//截取字符串
function cut_str($string, $sublen, $start = 0,$dot='...', $code = 'UTF-8') { 
	if($code == 'UTF-8'){ 
		$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/"; 
		preg_match_all($pa, $string, $t_string); 
		
		if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen)).$dot; 
		return join('', array_slice($t_string[0], $start, $sublen)); 
		} else { 
			$start = $start*2; 
			$sublen = $sublen*2; 
			$strlen = strlen($string); 
			$tmpstr = ''; 
			for($i=0; $i< $strlen; $i++) { 
				if($i>=$start && $i< ($start+$sublen)) { 
					if(ord(substr($string, $i, 1))>129) { 
						$tmpstr.= substr($string, $i, 2); 
					} else { 
						$tmpstr.= substr($string, $i, 1); 
					} 
				} 
				if(ord(substr($string, $i, 1))>129) $i++; 
			} 
			if(strlen($tmpstr)< $strlen ) $tmpstr.= $dot; 
			return $tmpstr; 
		} 
} 

require_once 'debug_func.php';
