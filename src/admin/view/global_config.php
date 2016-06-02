<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=10">    
	<link href="/asset/css/bootstrap.min.css<?php echo '?v=' . ASSETS_VERSION;?>" rel="stylesheet">
	<link href="/asset/css/ace.min.css<?php echo '?v=' . ASSETS_VERSION;?>" rel="stylesheet">

    <link rel="stylesheet" href="/asset/css/ace-rtl.min.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <link rel="stylesheet" href="/asset/css/ace-skins.min.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="/asset/css/ace-ie.min.css<?php echo '?v=' . ASSETS_VERSION;?>" />
    <![endif]-->
	<link href="/asset/css/common.css<?php echo '?v=' . ASSETS_VERSION;?>" rel="stylesheet">
	<link type="text/css" rel="stylesheet" href="/asset/css/fontawesome3/css/font-awesome.min.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <script type="text/javascript" src="/asset/js/jquery-1.10.2.min.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <script type="text/javascript" src="/asset/js/common.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <script type="text/javascript" src="/asset/js/bootstrap.min.js<?php echo '?v=' . ASSETS_VERSION;?>"></script> 
    <link type="text/css" rel="stylesheet" href="/asset/css/default.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <!--[if IE 7]>
    <link rel="stylesheet" href="/asset/css/fontawesome3/css/font-awesome-ie7.min.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <![endif]-->
	<script type="text/javascript" src="/asset/js/goods.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
</head>
<body class="no-skin">
	<h3 class="header smaller lighter blue">商城全局配置</h3>
	<form action="<?php echo $action?>" method="post" enctype="multipart/form-data" class="form-horizontal" role="form" id="save-form">
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 包邮价：</label>
			<div class="col-sm-9">
				<input type="text" name="freePostage" id="free_postage" maxlength="100" class="span7" value="<?php if (!empty($info['free_postage'])){echo $info['free_postage'];}?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 邮费：</label>
			<div class="col-sm-9">
				<input type="text" id="postage" maxlength="100" class="span7" value="<?php if (!empty($info['postage'])){echo $info['postage'];}?>">
			</div>
		</div>

		<div class="form-group" style="margin-bottom:1px;">
                <hr/>
			<label class="col-sm-2 control-label no-padding-left"> 库存预警：</label>
        </div>
		<div class="form-group" style="margin-bottom:5px;">
			<label class="col-sm-2 control-label no-padding-left"> 阀值：</label>
			<div class="col-sm-9">
				<input type="text" id="kucun_alarm" maxlength="100" class="span7" value="<?php if (!empty($info['kucun_alarm'])){echo $info['kucun_alarm'];}?>">
			</div>
		</div>
		<div class="form-group" style="margin-bottom:5px;">
			<label class="col-sm-2 control-label no-padding-left"> 通知：</label>
			<div class="col-sm-9">
				<input type="text" id="kucun_alarm_users" maxlength="100" class="span7" value="<?php if (!empty($info['kucun_alarm_users'])){echo $info['kucun_alarm_users'];}?>">
                用户编号，多个以英文,分隔。如 23,219
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 通知模板：</label>
			<div class="col-sm-9">
				<input style="width:250px" type="text" id="kucun_alarm_tpl" maxlength="100" class="span7" value="<?php if (!empty($info['kucun_alarm_tpl'])){echo $info['kucun_alarm_tpl'];}?>">
                预警信息会以微信模板消息形式通知
			</div>
		</div>

		<div class="form-group">
            <hr/>
			<label class="col-sm-2 control-label no-padding-left"> 首页微信分享标题：</label>
			<div class="col-sm-9">
				<input type="text" name="wxShareTitle" id="wxShareTitle" maxlength="120" class="span7" value="<?php if (!empty($info['wx_share_title'])){echo $info['wx_share_title'];}?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 首页微信分享描述：</label>
			<div class="col-sm-9">
				<input style="width:350px" type="text" name="wxShareDesc" id="wxShareDesc" maxlength="240" class="span7" value="<?php if (!empty($info['wx_share_desc'])){echo $info['wx_share_desc'];}?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 首页微信分享图：<br><span style="font-size:12px;color:red">（尺寸尽量小于50K）</span></label>
			<div class="col-sm-9">
              <div id="prev_thumb_img" class="fileupload-preview thumbnail" style="width: 140px; height: 150px;">
               <?php if(!empty($info['wx_share_img'])){?>
                   <img src="<?php echo $info['wx_share_img'];?>" />
                  <a href='javascript:void(0)' onclick='delThumbImg(this);return false;'>删除</a>
               <?php }?>
              </div>
              <!-- SWFUpload控件 -->
              <div id="divSWFUploadUI">
                 <p>
                    <span id="spanButtonPlaceholder"></span>
					<input id="btnCancel" type="hidden" value="全部取消" disabled="disabled"/>
                 </p>
              </div>
              <!-- END -->
            </div>
		</div>	

		<div class="form-group">
            <hr/>
			<label class="col-sm-2 control-label no-padding-left"> 搜索默认关键字：</label>
			<div class="col-sm-9">
				<input type="text" name="search_key" id="search_key" maxlength="100" class="span7" value="<?php if (!empty($info['search_key'])){echo $info['search_key'];}?>">
			</div>
		</div>

		<div class="form-group">
                <hr/>
			<label class="col-sm-2 control-label no-padding-left"></label>
			<div class="col-sm-9">
				<button type="button" id="save-btn" class="btn btn-primary span2" >保存</button>
			</div>
		</div>
		
        <input type="hidden"  id="thumb_img" class="thumb_img" value="<?php if (!empty($info['wx_share_img'])){echo $info['wx_share_img'];}?>">
	</form>
	<script>
        $('#save-btn').click(function(){
            var url = $("#save-form").attr("action");
            $.post(url,{
                postage:$("#postage").val(),
                freePostage:$("#free_postage").val(),
                kucun_alarm:$("#kucun_alarm").val(),
                kucun_alarm_users:$("#kucun_alarm_users").val(),
                kucun_alarm_tpl:$("#kucun_alarm_tpl").val(),
                wx_share_title:$("#wxShareTitle").val(),
                wx_share_desc:$("#wxShareDesc").val(),
                wx_share_img:$("#thumb_img").val(),
                search_key:$("#search_key").val()
                },function(data){
                if(data.code==0) {
                    alert(data.msg);
                    window.location.href= data.url;
                } else {
                    alert(data.msg);
                    return false;
                }
            },'json');
        });

	</script>
    <!-- SWFupload异步图片上传 -->
    <script type="text/javascript" src="/asset/js/swfupload/swfupload.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <script type="text/javascript" src="/asset/js/swfupload/swfupload.swfobject.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <script type="text/javascript" src="/asset/js/swfupload/swfupload.queue.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <script type="text/javascript" src="/asset/js/swfupload/fileprogress.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <script type="text/javascript" src="/asset/js/swfupload/handlers.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <!-- END -->
    <script type="text/javascript" src="/asset/js/swfupload/init.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
</body>
</html>
