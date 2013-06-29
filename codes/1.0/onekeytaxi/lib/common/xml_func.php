<?php  
/*---------------------------------------------------------------------------\
|                   常用xml处理方法                                                               |
|----------------------------------------------------------------------------|
|         Copyright (C) 2010, Beijing ChenHui. All rights reserved           |
|         Version: 1.0                                                                                 |
|                                                                                                             | 
\---------------------------------------------------------------------------*/

/* 将数组格式化为XML字符串 */
function array2xml($array,$level = 0){
    $return = '';
    if($level == 0){
        $return = '<?xml version="1.0" encoding="utf-8"?><root>';
    }
    foreach($array as $key => $item){
        if(!is_array($item)){
            // modify by chenhui 2011-5-9
            $return .= "<item key='{$key}'>".formatXmlSpecialChar($item)."</item>";
        }else{
            $return .= "<item key='{$key}'>";
            $return .= array2xml($item,$level + 1);
            $return .= "</item>";
        }
    }
    if($level == 0){
        $return .= '</root>';
    }
    return $return;
}

/* 辅助函数用来获取DOM跟节点 */
function getXmlRoot($xml){
	$doc = new DOMDocument();
	$doc->loadXML($xml);
	$root = $doc->documentElement;
	return $root;
}

/* 将被array2xml格式化的XML字符串还原 */
function xmlStr2array($xmlStr){
    return xml2array(getXmlRoot($xmlStr));
}
/* 将被array2xml格式化的XML对象还原 */
function xml2array($xml){
	$return_array = array();
	foreach($xml->childNodes as $node){
		$length = $node->childNodes->length;
		$key = $node->getAttribute('key');

		if($length == 0){//modified by chenhui,20101009
            $return_array[$key]=NULL;
        }else if($length == 1 and $node->firstChild->nodeType == XML_TEXT_NODE){
			$return_array[$key] = $node->nodeValue;
		}else{
			$return_array[$key] = xml2array($node);
		}
	}
	return $return_array;
}

//输出xml文档头信息
function  outputXmlHeader( )
{
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-Type: text/html; charset=utf-8");
}

//添加一个子节点
function  appendXmlNode($doc,$parent,$name)
{
    $tmpItem=$doc->createElement($name); 
    $tmpItem=$parent->appendChild($tmpItem);
    return $tmpItem;
}

//设置子节点属性值
function  setXmlNodeAttribute($doc,$node,$name,$value)
{
    $attribute=$doc->createAttribute($name);  #创建节点属性对象实体   
    $attribute=$node->appendChild($attribute);  #把属性添加到节点info中  
    $attribute->appendChild($doc->createTextNode($value));  
}

//添加一个叶子节点
function  appendXmlLeafNode($doc,$parent,$name,$value)
{
    $tmpItem=appendXmlNode($doc,$parent,$name);
    $tmpItem->appendChild($doc->createTextNode($value));  
}

//格式化特殊字符，比如&,<
function formatXmlSpecialChar($noFormatChar){
    //return htmlspecialchars($noFormatChar);
    return $noFormatChar;
}

