<!DOCTYPE html>
<html>
<head>
	<title>登录</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<!--<link rel="shortcut icon" href="http://localhost/favicon.ico" type="image/x-icon">-->
	<link rel="stylesheet" href="/asset/css/component-min.css<?php echo '?v=' . ASSETS_VERSION;?>">
	<link rel="stylesheet" href="/asset/css/jbox-min.css<?php echo '?v=' . ASSETS_VERSION;?>">
	<link rel="stylesheet" href="/asset/css/common_login_reg.css<?php echo '?v=' . ASSETS_VERSION;?>">
</head>
<body>
	<form method="post" target="_parent" name="login" id="login-form" action="/admin/Login/in">
	<div class="login">
		<a href="#" class="logo"></a>
		<div class="login-inner">
			<h1 class="login-title"></h1>
			<div class="login-item mgb20">
				<input type="text" class="clearError" name="account" id="ipt_account" placeholder="请输入登录账号" tabindex="1">
				<a href="javascript:;" class="clearIpt j-clearIpt"><i class="gicon-remove"></i></a>
			</div>
			<div class="login-item mgb20">
				<input type="password" class="clearError" name="passwd" id="ipt_pwd" placeholder="请输入密码" tabindex="2">
				<a href="javascript:;" class="clearIpt j-clearIpt"><i class="gicon-remove"></i></a>
			</div>
			<div>
				<button type="button" id="login-btn" class="login-btn">登 陆</button>	
			 </div>			 
		</div>
		<p class="copyright">©大泽商城</p>
	</div>
   </form>
   <div class="tooltips" data-origin="ipt_account" data-currentleft="0">
		<span class="tooltips-arrow tooltips-arrow-left"><em>◆</em><i>◆</i></span>
		<span class="tooltips-content">请输入手机号码或邮箱</span>
	</div>
	<div class="tooltips" data-origin="ipt_pwd" data-currentleft="0">
		<span class="tooltips-arrow tooltips-arrow-left"><em>◆</em><i>◆</i></span>
		<span class="tooltips-content">请输入密码</span>
	</div>
	<script src="/asset/js/lib-min.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
	<script src="/asset/js/jquery.jbox-min.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
	<script src="/asset/js/common_login_reg.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
	<!--[if lt IE 10]>
		<script src="asset/js/jquery.placeholder-min.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
		<script>
		$(function(){
			//修复IE下的placeholder
			$('input').placeholder();
		});
		</script>
	<![endif]-->
	<script>
		document.onkeydown =ieHandler; 
		function  ieHandler(){ 
			if(window.event.keyCode==13){
				dologin();
			}
		}
		function dologin(){
			var pw = $("#ipt_pwd");
			var idname = $("#ipt_account");
			if(idname.val() == "") {
				LoginShowError(idname,"请输入用户名!");
				return false;
			} else {
				LoginClearError(idname);
			}
			if (pw.val() == "" ){
				LoginShowError(pw,"请输入密码!");
				return false;
			} else {
				LoginClearError(pw);
			}
			return true;
		}		
        $('#login-btn').click(function(){
            if (dologin()==false) {
                return false;
            }
            var url = $("#login-form").attr("action");
            $.post(url,{account:$("#ipt_account").val(),passwd:$("#ipt_pwd").val()},function(data){
                if(data.code==0) {
                    window.location.href= data.url;
                } else {
                    alert(data.msg);
                    return false;
                }
            },'json');
        });

	</script>
</body>
</html>
