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
	<link type="text/css" rel="stylesheet" href="/asset/css/datetimepicker.css<?php echo '?v=' . ASSETS_VERSION;?>">
	<script type="text/javascript" src="/asset/js/datetimepicker.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
	<script type="text/javascript" src="/asset/js/goods.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
</head>
<body class="no-skin">
	<h3 class="header smaller lighter blue"><?php echo $title?></h3>
	<form id="save-form" action="<?php echo $action?>" method="post" enctype="multipart/form-data" class="form-horizontal">	
		<input type="hidden" id="catId" name="catId" value="<?php echo $catId?>">
		<input type="hidden" id="parentId" name="parentId" value="<?php echo $parentId?>">
		<?php if(!empty($parentId)):?>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left">上级分类</label>
			<div class="col-sm-9">
            <input type="text" readonly="readonly" class="col-xs-10 col-sm-2" value="<?php echo $parentId?>">
			</div>
		</div>
		<?php endif;?>
				
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 排序</label>
			<div class="col-sm-9">
				<input id="sort" type="text" name="sort" class="col-xs-10 col-sm-2" value="<?php if (isset($info['sort'])){echo $info['sort'];}?>">
				<p class="help-block">&nbsp;数值越大，排序越靠前（当前最大可用值<?php echo time();?>）</p>
			</div>
		</div>
	
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 分类名称</label>
			<div class="col-sm-9">												
				<input type="text" id="cateName" name="cateName" class="col-xs-10 col-sm-2" value="<?php if (!empty($info['name'])){echo $info['name'];}?>">
			</div>
		</div>
		
        <div class="form-group">
            <label class="col-sm-2 control-label no-padding-left"> 状态：</label>
            <div class="col-sm-9" id="state-radio">
                <div style="margin-right:20px;display:inline;">
                   <input type="radio" name="state" value="0" <?php if (isset($info['state']) && $info['state'] == 0) { echo 'checked="true"';}?> >无效
                </div>
                <div style="margin-right:20px;display:inline;">
                   <input type="radio" name="state" value="1" <?php if (isset($info['state'])) { if ($info['state'] == 1) { echo 'checked="true"';}} else {echo 'checked="true"';}?> >有效
               </div>
          </div>
       </div>
	
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> </label>
			<div class="col-sm-9">                       
				<input name="button" type="button" value="提交" id="save-btn" class="btn btn-primary span3">
			</div>
		</div>
	</form>
	<script>
        $('#save-btn').click(function(){
            var url = $("#save-form").attr("action");
            $.post(url,{
                catId:$("#catId").val(),
                parentId:$("#parentId").val(),
                name:$("#cateName").val(),
                sort:$("#sort").val(),
                state:$("#state-radio input[name='state']:checked").val()
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
