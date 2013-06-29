<?php  
/** 
 * 保存模板ajax处理页
 * 
 * PHP version 5 
 * 
 * @category  UTS-UCS 
 * @package   new_api_test
 * @author    xichengyuan   2011-09-01 <xichengyuan@chinatsp.com> 
 * @copyright 2010-2011 ChinaTsp Inc. All rights reserved. 
 * @license   http://uts.chinatsp.com/developer/licence ChinaTsp Licence 
 * @version   SVN: $Id: ajax_save_tempate.php,v 0.1.0 2011-09-01 9:55:00 
 *            beijing Exp $ 
 * @link      http://www.chinatsp.com/ 
 */

require_once 'api_test_config.php';

$fieldsBase64 = originalReqChrStr('fieldsBase64');
$templateName = safeReqChrStr('templateName');

if(empty($templateName)) {
	echo '模板名称不能为空！';
	exit;
} else if (empty($fieldsBase64)) {
	echo '测试表单不能为空！';
	exit;	
}

$templateId = time();
$fieldsJson = base64_decode($fieldsBase64);
$fieldsArray = json_decode($fieldsJson, true);
$templateArray = array(
	'id' => $templateId,
	'templateName' => $templateName,
	'fields' => $fieldsArray
);

// 从redis中读取此前保存的模板列表
$oldCacheDataArray = array();
$oldCacheDataJson = getFileContent(API_CACHE_PATH . 'api_test');
$oldCacheDataArray = json_decode($oldCacheDataJson, true);

// 验证模板名称是否存在 
foreach ($oldCacheDataArray as $k => $v) {
	if($v['templateName'] == $templateName) {
		echo '模版名称已存在！';
		exit();		
	}
	if($v['id'] == $templateId) {
		echo '模版编号已存在！';
		exit();		
	}
}

// 保存模板
$templateListArray = array();
$templateListArray = $oldCacheDataArray;
$templateListArray[] = $templateArray;

$templateJson = json_encode($templateListArray);

// 保存模板到redis中
writeFileContent(
	API_CACHE_PATH . 'api_test', 
	$templateJson
); 

echo true;
?>