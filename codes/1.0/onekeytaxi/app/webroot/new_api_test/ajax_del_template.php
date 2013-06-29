<?php  
/** 
 * 删除模板ajax处理页
 * 
 * PHP version 5 
 * 
 * @category  UTS-UCS 
 * @package   new_api_test
 * @author    xichengyuan   2011-09-01 <xichengyuan@chinatsp.com> 
 * @copyright 2010-2011 ChinaTsp Inc. All rights reserved. 
 * @license   http://uts.chinatsp.com/developer/licence ChinaTsp Licence 
 * @version   SVN: $Id: ajax_del_tempate.php,v 0.1.0 2011-09-01 9:55:00 
 *            beijing Exp $ 
 * @link      http://www.chinatsp.com/ 
 */

require_once 'api_test_config.php';

$id = safeReqChrStr('id');

if(empty($id)) {
	echo '请选择所要删除的模板！';
	exit;
}

// 从redis中读取此前保存的模板列表
$oldCacheDataArray = array();
$oldCacheDataJson = getFileContent(API_CACHE_PATH . 'api_test');
$oldCacheDataArray = json_decode($oldCacheDataJson, true);

// 从模板中删除选中项 
foreach ($oldCacheDataArray as $k => $v) {
	if($v['id'] == $id) {
		unset($oldCacheDataArray[$k]);
	}
}

$templateJson = json_encode($oldCacheDataArray);

// 保存模板到redis中
writeFileContent(
	API_CACHE_PATH . 'api_test', 
	$templateJson
); 

echo true;
?>