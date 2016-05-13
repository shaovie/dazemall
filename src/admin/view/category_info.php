<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=10">    
	<link href="/asset/css/bootstrap.min.css" rel="stylesheet">
	<link href="/asset/css/ace.min.css" rel="stylesheet">

    <link rel="stylesheet" href="/asset/css/ace-rtl.min.css">
    <link rel="stylesheet" href="/asset/css/ace-skins.min.css">
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="/asset/css/ace-ie.min.css" />
    <![endif]-->
	<link href="/asset/css/common.css" rel="stylesheet">
	<link type="text/css" rel="stylesheet" href="/asset/css/fontawesome3/css/font-awesome.min.css">
	<script type="text/javascript" src="/asset/js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="/asset/js/common.js"></script>
    <script type="text/javascript" src="/asset/js/bootstrap.min.js"></script> 
    <link type="text/css" rel="stylesheet" href="/asset/css/default.css">
    <!--[if IE 7]>
    <link rel="stylesheet" href="/asset/css/fontawesome3/css/font-awesome-ie7.min.css">
    <![endif]-->
	<link type="text/css" rel="stylesheet" href="/asset/css/datetimepicker.css">
	<script type="text/javascript" src="/asset/js/datetimepicker.js"></script>
	<script type="text/javascript" src="/asset/js/goods.js"></script>
</head>
<body class="no-skin">
	<h3 class="header smaller lighter blue">新增分类</h3>
	<form id="save-form" action="/admin/GoodsCategory/add" method="post" enctype="multipart/form-data" class="form-horizontal">	
		<input type="hidden" name="parentId" value="<?php echo $parentCatId?>">
		<?php if(!empty($parentCatId)):?>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left">上级分类</label>
			<div class="col-sm-9">
            <input type="text" readonly="readonly" class="col-xs-10 col-sm-2" value="<?php echo $parentCatId?>">
			</div>
		</div>
		<?php endif;?>
				
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 排序</label>
			<div class="col-sm-9">
				<input id="sort" type="text" readonly="readonly" name="sort" class="col-xs-10 col-sm-2" value="0">
			</div>
		</div>
	
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 分类名称</label>
			<div class="col-sm-9">												
				<input type="text" name="cateName" class="col-xs-10 col-sm-2" value="<?php if (!empty($info['name'])){echo $info['name'];}?>">
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 分类图片</label>
			<div class="col-sm-9">
				<div id="prev_thumb_img" class="fileupload-preview thumbnail" style="width: 160px; height: 160px;">
					
               <?php if(!empty($info['image_url'])){?>
                   <img src="<?php echo $info['image_url'];?>" />
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
			<label class="col-sm-2 control-label no-padding-left"> </label>
			<div class="col-sm-9">                       
				<input name="button" type="button" value="提交" id="save-btn" class="btn btn-primary span3">
			</div>
		</div>
		<input type="hidden" id="thumb_img" name="thumb_img" class="thumb_img" value="">
	</form>
	<script>
        $('#save-btn').click(function(){
            var url = $("#save-form").attr("action");

            $.post(url,{
                name:$("#cateName").val(),
                imageUrl:$("#thumb_img").val(),
                sort:$("#sort").val()
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
    <script type="text/javascript" src="/asset/js/swfupload/swfupload.js"></script>
    <script type="text/javascript" src="/asset/js/swfupload/swfupload.swfobject.js"></script>
    <script type="text/javascript" src="/asset/js/swfupload/swfupload.queue.js"></script>
    <script type="text/javascript" src="/asset/js/swfupload/fileprogress.js"></script>
    <script type="text/javascript" src="/asset/js/swfupload/handlers.js"></script>
    <!-- END -->
    <script type="text/javascript" src="/asset/js/swfupload/init.js"></script>
</body>
</html>