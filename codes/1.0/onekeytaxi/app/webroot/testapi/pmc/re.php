<?php
require_once '../MyCurl.php';
$objCurl = new MyCurl($_POST['post']['url'],true,30,4,false,false,false);
if('POST'==$_POST['post']['method'])
    {
        $array_argvs = $_POST['post'];
        unset($array_argvs['url']);
        unset($array_argvs['method']);
        $objCurl->setPost($array_argvs);
        $objCurl->createCurl();
	    $error = $objCurl->hasError();
	    if ($error) {
	        $objCurl=NULL;
	        return NULL;
	    }
	    
	    $responseString = $objCurl->__tostring();
	    echo $responseString;
//	    $arr =  json_decode($responseString,true);
//	    print_r($arr);
    }
?>