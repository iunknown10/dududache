<?php  
/** 
 * 模板列表加载页
 * 
 * PHP version 5 
 * 
 * @category  UTS-UCS 
 * @package   new_api_test
 * @author    xichengyuan   2011-09-01 <xichengyuan@chinatsp.com> 
 * @copyright 2010-2011 ChinaTsp Inc. All rights reserved. 
 * @license   http://uts.chinatsp.com/developer/licence ChinaTsp Licence 
 * @version   SVN: $Id: ajax_tempate_list.php,v 0.1.0 2011-09-01 9:55:00 
 *            beijing Exp $ 
 * @link      http://www.chinatsp.com/ 
 */

require_once 'api_test_config.php';

// 从redis中读取模板列表
$cacheDataJson = getFileContent(API_CACHE_PATH . 'api_test');
$templateArray = json_decode($cacheDataJson, true);
?>
<table class="tab-list" cellpadding="0" cellspacing="0" border="0" width="100%">
	<thead>
	<tr>
		<td width="50%" height="20" align="center">模板名称</td>
		<td width="30%" align="center">创建时间</td>
		<td width="20%" align="center">操作</td>
	</tr>
	</thead>
	<tbody>
	<?php 
	if(isset($templateArray)){
		foreach ($templateArray as $k => $v) {
			echo '<tr>';
			echo '<td height="20" align="center" style="word-break:break-all"><a href="index.php?id='.$v['id'].'">' . $v['templateName'] . '</a></td>';
			echo '<td align="center">' . date('Y-m-d H:i:s',$v['id']) . '</td>';
			echo '<td align="center"><a href="index.php?id='.$v['id'].'">载入</a>&nbsp;&nbsp;<a class="hand" onclick="delTemplate(\''.$v['id'].'\');">删除</a></td>';
			echo '</tr>';
		}
	}
	?>
	</tbody>
</table>