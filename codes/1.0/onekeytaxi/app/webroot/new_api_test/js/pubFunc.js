// 统一设置css效果
$(document).ready(function(){
	$(":text,:password").bind("focus", function()
	{
		$(this).addClass("input-over");
	}); 
	$(":text,:password").bind("blur", function()
	{
		$(this).removeClass("input-over");
	}); 
});

//div层屏幕居中
function divCenter(id)
{
	
	isMSIE = (navigator.appName == "Microsoft Internet Explorer");
	if(isMSIE)
	{    
		document.getElementById(id).style.pixelLeft = (document.documentElement.clientWidth-document.getElementById(id).currentStyle.width.replace("px",""))/2 + document.documentElement.scrollLeft;
		document.getElementById(id).style.pixelTop = (document.documentElement.clientHeight-document.getElementById(id).currentStyle.height.replace("px",""))/2 + document.documentElement.scrollTop;
	 }
	 else
	 {
	 	var obj = document.getElementById(id);
	 	
	 	document.getElementById(id).style.left = (document.documentElement.clientWidth-document.defaultView.getComputedStyle(obj,"").getPropertyValue("width").replace("px","")) / 2  + document.documentElement.scrollLeft + document.body.scrollLeft + 'px';
		document.getElementById(id).style.top  = (document.documentElement.clientHeight-document.defaultView.getComputedStyle(obj,"").getPropertyValue("height").replace("px","")) / 2  + document.documentElement.scrollTop + document.body.scrollTop + 'px';	
	 }
	
}

//显示操作提示信息
function pubShowMsg(strMsg,strType)
{
	var className;
	
	switch(strType)
	{
		case "wait"://操作中
			className = "msg-wait";	
		break;
		case "ok"://成功
			className = "msg-ok";	
		break;
		case "wrong"://错误
			className = "msg-wrong";
		break;	
		default:
			className = "msg-ok";
		break;
	}
	
	$('#msg').html(strMsg);
	$('#msg').removeClass();
	$('#msg').addClass(className);
	divCenter('msg');
	$('#msg').fadeIn(500);		
	
	if(strType != "wait")
	{
		setTimeout(function(){
				$('#msg').fadeOut(1000); 		
			},3000
		)
	}	
	
}

// 点击添加Post字段
function doAddPostField(){
	// 获取当前post字段数
	postFieldsNum = parseInt($("#postFieldsNum").val());
	
	newFieldsString = '';
	newFieldsString = newFieldsString + '<p class="post_p">';
		newFieldsString = newFieldsString + '<label>字段' + postFieldsNum + '：</label>';
			newFieldsString = newFieldsString + '<input type="text" name="post' + postFieldsNum + '_name" id="post' + postFieldsNum + '_name" class="input-text" value="">&nbsp;';
			newFieldsString = newFieldsString + '<input type="text" name="post' + postFieldsNum + '_value" id="post' + postFieldsNum + '_value" class="input-text" style="width:300px;" value="">';
	newFieldsString = newFieldsString + '</p>';
	
	$("#postList").html($("#postList").html() + newFieldsString);
	$("#postFieldsNum").val(postFieldsNum + 1);
}

// 保存模版
function saveTemplate()
{
	pubShowMsg("保存中…","wait");

	var fieldsBase64 = $('#fieldsBase64').val();
	var templateName = $('#templateName').val();
	
	if (templateName == "")
	{
		pubShowMsg("请输入模板名称！","wrong");
	} else {
		$.post("ajax_save_template.php",{fieldsBase64:fieldsBase64,templateName:templateName},function(r){
			if(r == '1')
			{
				pubShowMsg("保存成功！","ok");
				initTemplateList();
			}
			else
			{
				pubShowMsg(r,"wrong");
			}
		});
	}	
}

//更新首页模板列表
function initTemplateList(){
	$.post("ajax_template_list.php",function(r){
			$("#templateList").html(r);
	});	
}


// 清空缓存
function doClearTemplate(){
	pubShowMsg("清除中…","wait");

	$.post("ajax_clear_template.php",function(r){
		if(r == '1')
		{
			pubShowMsg("清除成功！","ok");
			initTemplateList();
		}
		else
		{
			pubShowMsg(r,"wrong");
		}
	});
}

// 删除单个模板
function delTemplate(id) {
	pubShowMsg("删除中…","wait");

	$.post("ajax_del_template.php",{id:id},function(r){
		if(r == '1')
		{
			pubShowMsg("删除成功！","ok");
			initTemplateList();
		}
		else
		{
			pubShowMsg(r,"wrong");
		}
	});
}

