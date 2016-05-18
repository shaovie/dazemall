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
	<script type="text/javascript" src="/asset/js/goods.js"></script>
    <link type="text/css" rel="stylesheet" href="/asset/css/datetimepicker.css">
    <script type="text/javascript" src="/asset/js/datetimepicker.js"></script>
</head>
<body class="no-skin">
    <input id="bannerId" name="bannerId" type="hidden" value="<?php echo (isset($banner['id']) ? $banner['id'] : 0);?>"/>
	<h3 class="header smaller lighter blue"><?php echo $title?></h3>
	<form action="<?php echo $action?>" method="post" enctype="multipart/form-data" class="form-horizontal" role="form" id="save-form">
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 备注：</label>
			<div class="col-sm-9">
				<input type="text" name="remark" id="remark" maxlength="100" class="span7" value="<?php if (!empty($banner['remark'])){echo $banner['remark'];}?>">
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 展示时间段：</label>
			<div class="col-sm-9">
				<input type="text" name="beginTime" id="beginTime" maxlength="100" class="span7" value="<?php if (!empty($banner['begin_time'])){echo date('Y-m-d H:i:s', $banner['begin_time']);}?>" placeholder="开始时间"> - 
				<input type="text" name="endTime" id="endTime" maxlength="100" class="span7" value="<?php if (!empty($banner['end_time'])){echo date('Y-m-d H:i:s', $banner['end_time']);}?>" placeholder="结束时间">
				<p class="help-block">格式：2016-01-18 12:30:23，开始时间不填，即为立即开始，结束时间不填，意为永远</p>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 展示区域：</label>
			<div class="col-sm-9">
            <select id="showArea" name="showArea" style="margin-right:10px;width: 100px;">
                <option value="-1" <?php if (!isset($banner['show_area']) || $banner['show_area'] == -1) { echo 'selected="selected"';}?> >选择</option>
                <option value="1" <?php if (isset($banner['show_area']) && $banner['show_area'] == 1) { echo 'selected="selected"';}?> >首页顶部</option>
            </select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 链接：</label>
			<div class="col-sm-9">
            <select name="linkType" style="margin-right:10px;width: 100px;">
                <option value="-1" <?php if (!isset($banner['linkType']) || $banner['linkType'] == -1) { echo 'selected="selected"';}?> >链接目标类型</option>
                <option value="1" <?php if (isset($banner['linkType']) && $banner['linkType'] == 1) { echo 'selected="selected"';}?> >商品</option>
            </select>
				<input style="width:300px;" type="text" name="linkValue" id="linkValue" maxlength="300" class="span7" value="<?php if (!empty($banner['link_value'])){echo $banner['link_value'];}?>" placeholder="链接值">
                <p class="help-block">目录类型：商品 对应链接值：商品编号</br>
                </p>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left">排序：</label>
			<div class="col-sm-9">
				<input type="text" name="sort" id="sort"
                onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" 
                value="<?php if (!empty($banner['sort'])){echo $banner['sort'];}?>">
				<p class="help-block">数值越小，排序越靠前</p>
          	</div>
		</div>
		
		
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left">图片：<br><span style="font-size:12px;color:red">（标准: 640宽 <br>尺寸尽量小于50K）</span></label>
			<div class="col-sm-9">
              <div id="prev_thumb_img" class="fileupload-preview thumbnail" style="width: 160px; height: 160px;">
               <?php if(!empty($banner['image_url'])){?>
                   <img src="<?php echo $banner['image_url'];?>" />
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
			<label class="col-sm-2 control-label no-padding-left"></label>
			<div class="col-sm-9">
				<button type="button" id="save-btn" class="btn btn-primary span2" >保存</button>
			</div>
		</div>
		
        <input type="hidden"  id="thumb_img" class="thumb_img" value="<?php if (!empty($banner['image_url'])){echo $banner['image_url'];}?>">
	</form>
	<script>
        $('#save-btn').click(function(){
            var url = $("#save-form").attr("action");
            $.post(url,{
                bannerId:$("#bannerId").val(),
                remark:$("#remark").val(),
                beginTime:$("#beginTime").val(),
                endTime:$("#endTime").val(),
                sort:$("#sort").val(),
                linkType:$("#linkType").val(),
                linkValue:$("#linkValue").val(),
                showArea:$("#showArea option:selected").val(),
                imageUrl:$("#thumb_img").val()
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
    <!-- <link href="/asset/js/swfupload/swfupload.css" rel="stylesheet" type="text/css"/> -->
</body>
</html>
