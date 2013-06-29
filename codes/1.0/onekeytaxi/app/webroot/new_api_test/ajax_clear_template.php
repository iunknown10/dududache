<?php  
/** 
 * 清除模板ajax处理页
 * 
 * PHP version 5 
 * 
 * @category  UTS-UCS 
 * @package   new_api_test
 * @author    xichengyuan   2011-09-01 <xichengyuan@chinatsp.com> 
 * @copyright 2010-2011 ChinaTsp Inc. All rights reserved. 
 * @license   http://uts.chinatsp.com/developer/licence ChinaTsp Licence 
 * @version   SVN: $Id: ajax_clear_tempate.php,v 0.1.0 2011-09-01 9:55:00 
 *            beijing Exp $ 
 * @link      http://www.chinatsp.com/ 
 */

require_once 'api_test_config.php';

// 从redis中清除模板
clearFile(
	API_CACHE_PATH . 'api_test', 
	$templateJson
); 

echo true;
?>