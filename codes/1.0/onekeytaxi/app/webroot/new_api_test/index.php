<?php  
/** 
 * API测试
 * 
 * PHP version 5 
 * 
 * @category  UTS-UCS 
 * @package   new_api_test
 * @author    xichengyuan   2011-09-01 <xichengyuan@chinatsp.com> 
 * @copyright 2010-2011 ChinaTsp Inc. All rights reserved. 
 * @license   http://uts.chinatsp.com/developer/licence ChinaTsp Licence 
 * @version   SVN: $Id: index.php,v 0.1.0 2011-09-01 9:55:00 
 *            beijing Exp $ 
 * @link      http://www.chinatsp.com/ 
 */

require_once 'api_test_config.php';



$isTest = safeReqChrStr('isTest');
if('1' == $isTest) {
	$url = safeReqChrStr('url');
	$method = safeReqChrStr('method');
	$cookie0_name = safeReqChrStr('cookie0_name');
	$cookie0_value = safeReqChrStr('cookie0_value');
	$cookie1_name = safeReqChrStr('cookie1_name');
	$cookie1_value = safeReqChrStr('cookie1_value');
	
	$cookieArray = array();
	$cookieArray[] = array(
		'name' => $cookie0_name,
		'value' => $cookie0_value
	);
	$cookieArray[] = array(
		'name' => $cookie1_name,
		'value' => $cookie1_value
	);
	
	// post字段数量
	$postArray = array();
	$postFieldsNum = safeReqNumStr('postFieldsNum');
	for ($i=0;$i<$postFieldsNum;$i++) {
		$postArray[] = array(
			'name' => safeReqChrStr('post' . $i . '_name'),
			'value' => safeReqChrStr('post' . $i . '_value')
		);	
	}
	
	$fieldsArray = array(
		'url' => $url,
		'method' => $method,
		'cookie' => $cookieArray,
		'post' => $postArray
	);
	
} else {
	$fieldsArray = array();
	// 判断是否需要载入模板
	$id = safeReqChrStr('id');
	if(0 < strlen($id)) {
		// 从redis中读取模板内容
		$templateDataArray = array();
		$cacheDataJson = getFileContent(API_CACHE_PATH . 'api_test');
		$templateDataArray = json_decode($cacheDataJson, true);
		
		// 从模板列表中找出相应模板
		foreach ($templateDataArray as $k => $v) {
			if($v['id'] == $id) {
				$fieldsArray = $v['fields'];
				$curTemplateName = $v['templateName'];
			}
		}
	} else {
		// 如果没有传入模板，则加载默认值
		$fieldsArray = array(
			'url' => 'http://local.dududache.com/api/1.0/pmc/vercode/13718861078/',
			'method' => 'GET',
			'cookie' => array(
				'0' => array(
					'name' => '',
					'value' => ''
				),
				'1' => array(
					'name' => '',
					'value' => ''
				)
			),
			'post' => array(
				'0' => array(
					'name' => '',
					'value' => ''
				),
				'1' => array(
					'name' => '',
					'value' => ''
				)
			)
		);
	}
	
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="css/style.css" />
<title>API测试</title>
</head>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/pubFunc.js"></script>
<body style="padding:10px;">
<div id="msg"></div>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td style="width:50%;height:30px;font-size:24px;font-weight:bold;word-break:break-all">API测试</td>
		<td style="width:50%;height:30px;font-size:24px;font-weight:bold">API返回结果</td>
	</tr>
	<tr>
		<td valign="top">
			<form name="test" action = "index.php" method="post">
			<input type="hidden" name="isTest" value="1">
			<table cellpadding="0" cellspacing="0" border="0" align="left" width="640">
				<tr><td height="5"></td></tr>
				<tr>
					<td colspan="2" height="20" style="font-size:14px;font-weight:bold;word-break:break-all;">
						<?php
							if('1' != $isTest) {
								if(empty($fieldsArray)) {
									echo '<font color="red">没有找到相关模板！</font>';
								} else {
									if(empty($curTemplateName)) {
										echo '(当前模板：默认)';
									} else {
										echo '(当前模板：' . $curTemplateName . ')';
									}
								}
							}
						?>
					</td>
				</tr>
				<tr><td height="10"></td></tr>
				<tr>
					<td class="label" width="120">接口地址：</td>
					<td width="520"><input type="text" name="url" id="url" class="input-text" value="<?php echo $fieldsArray['url'];?>" style="width:500px;"></td>
				</tr>
				<tr>
					<td class="label" width="120">请求方式：</td>
					<td>
						<select name="method" id="method" class="input-select" style="width:100px;">
							<option value="GET" <?php if('GET' == $fieldsArray['method']) echo 'selected';?>>GET</option>
							<option value="POST" <?php if('POST' == $fieldsArray['method']) echo 'selected';?>>POST</option>
							<option value="DELETE" <?php if('DELETE' == $fieldsArray['method']) echo 'selected';?>>DELETE</option>
						</select>
					</td>
				</tr>
				<tr><td colspan="2" class="subTitle">Cookie字段</td></tr>
				<?php 
					foreach ($fieldsArray['cookie'] as $k => $v) {
				?>
				<tr>
					<td class="label" width="120">字段<?php echo $k;?>：</td>
					<td>
						<input type="text" name="cookie<?php echo $k;?>_name" id="cookie<?php echo $k;?>_name" class="input-text" value="<?php echo $v['name'];?>">
						<input type="text" name="cookie<?php echo $k;?>_value" id="cookie<?php echo $k;?>_value" class="input-text" style="width:300px;" value="<?php echo $v['value'];?>">
					</td>
				</tr>
				<?php 
					}
				?>
				<tr><td colspan="2" class="subTitle">Post字段</td></tr>
				<?php 
					$postFieldsNum = 0;
					foreach ($fieldsArray['post'] as $k => $v) {
				?>
				<tr>
					<td class="label" width="120">字段<?php echo $k;?>：</td>
					<td>
						<input type="text" name="post<?php echo $k;?>_name" id="post<?php echo $k;?>_name" class="input-text" value="<?php echo $v['name'];?>">
						<input type="text" name="post<?php echo $k;?>_value" id="post<?php echo $k;?>_value" class="input-text" style="width:300px;" value="<?php echo $v['value'];?>">
					</td>
				</tr>
				<?php 
						$postFieldsNum ++;
					}
				?>
				<tr>
					<td colspan="2" valign="top">
						<div id="postList"></div>
					</td>
				</tr>
				<input type="hidden" name="postFieldsNum" id="postFieldsNum" value="<?php echo $postFieldsNum;?>">
				<tr>
					<td></td>
					<td style="padding-top:5px;"><img src="imgs/addpost.png" class="hand" onclick="doAddPostField();"></td>
				</tr>
				<tr><td colspan="2" height="20"></td></tr>
				<tr>
					<td></td>
					<td><img src="imgs/doTest.png" class="hand" onclick="document.test.submit();"></td>
				</tr>
				<tr><td height="30"></td></tr>
<!--				<tr>-->
<!--					<td colspan="2"><img src="imgs/clear.png" class="hand" onclick="doClearTemplate();"></td>-->
<!--				</tr>-->
<!--				<tr><td height="5"></td></tr>-->
				<tr>
					<td colspan="2" valign="top">
						<div id="templateList"></div>
					</td>
				</tr>
			</table>
			</form>
		</td>
		<td valign="top">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr><td height="20"></td></tr>
				<?php 
					if('1' == $isTest) {
						$fieldsJson = json_encode($fieldsArray);
						$fieldsBase64 = base64_encode($fieldsJson);
				?>
				<tr><td colspan="2" height="20"><b>将本次查询的表单保存为模板</b></td></tr>
				<tr>
					<td class="label" width="80">模板名称：</td>
					<td width="350">
						<input type="hidden" name="fieldsBase64" id="fieldsBase64" value="<?php echo $fieldsBase64;?>">
						<input type="text" name="templateName" id="templateName" class="input-text" style="width:200px;">
						<img src="imgs/save.png" class="hand" onclick="saveTemplate();" align="absmiddle">
					</td>
				</tr>
				<?php } ?>
				<tr><td height="20"></td></tr>
				<tr>
					<td colspan="2">
						<?php 
							if('1' == $isTest) {
								require_once 'API/api_common_func.php';
								
								// 获取cookie值
								$array_cookies = array();
								foreach ($fieldsArray['cookie'] as $k => $v) {
									$array_cookies[$v['name']] = $v['value'];
								}
								
								// 获取post值
								$array_argvs = array();
								foreach ($fieldsArray['post'] as $k => $v) {
									$array_argvs[$v['name']] = $v['value'];
								}
								
								$result = commonCallApi(
							        $fieldsArray['method'],     //请求方式
							        $fieldsArray['url'],    //API完整url地址
							        $array_cookies, //cookie参数键-值数组，比如访问令牌
							        $array_argvs  //用于POST或PUT的参数键-值数组
							    );


							    if(empty($result)) {
							    	echo '<font color=red>请检查接口地址填写是否正确！';	
							    } else {
								    echo '<pre>';
								    print_r($result);
							    }
							}
						?>
					</td>
				</tr>	
			</table>
		</td>
	</tr>	
	
</table>
<script language="javascript">
	initTemplateList();
</script>



