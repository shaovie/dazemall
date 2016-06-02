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
    <link type="text/css" rel="stylesheet" href="/asset/css/datetimepicker.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <script type="text/javascript" src="/asset/js/datetimepicker.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
</head>
<body class="no-skin">
    <input id="actId" name="actId" type="hidden" value="<?php echo (isset($act['id']) ? $act['id'] : 0);?>"/>
	<h3 class="header smaller lighter blue"><?php echo $title?></h3>
	<form action="<?php echo $action?>" method="post" enctype="multipart/form-data" class="form-horizontal" role="form" id="save-form">
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 标题：</label>
			<div class="col-sm-9">
				<input type="text" name="title" id="title" maxlength="100" class="span7" value="<?php if (!empty($act['title'])){echo $act['title'];}?>">
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 展示区域：</label>
			<div class="col-sm-9">
            <select id="showArea" name="showArea" style="margin-right:10px;width: 156px;">
                <option value="-1" <?php if (!isset($act['show_area']) || $act['show_area'] == -1) { echo 'selected="selected"';}?> >选择</option>
                <option value="1" <?php if (isset($act['show_area']) && $act['show_area'] == 1) { echo 'selected="selected"';}?> >隐藏</option>
                <option value="2" <?php if (isset($act['show_area']) && $act['show_area'] == 2) { echo 'selected="selected"';}?> >首页</option>
            </select>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 展示时间段：</label>
			<div class="col-sm-9">
				<input type="text" name="beginTime" id="beginTime" maxlength="100" class="span7" value="<?php if (!empty($act['begin_time'])){echo date('Y-m-d H:i:s', $act['begin_time']);}?>" placeholder="开始时间"> - 
				<input type="text" name="endTime" id="endTime" maxlength="100" class="span7" value="<?php if (!empty($act['end_time'])){echo date('Y-m-d H:i:s', $act['end_time']);}?>" placeholder="结束时间">
				<p class="help-block">格式：2016-01-18 12:30:23，开始时间不填，默认当前时间，结束时间不填，意为永远</p>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left">排序：</label>
			<div class="col-sm-9">
				<input type="text" name="sort" id="sort"
                onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" 
                value="<?php if (!empty($act['sort'])){echo $act['sort'];}?>">
				<p class="help-block">数值越大，排序越靠前（当前最大可用值<?php echo time();?>）</p>
          	</div>
		</div>
	
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 主图：<br><span style="font-size:12px;color:red">（标准: 640宽 <br>尺寸尽量小于50K）</span></label>
			<div class="col-sm-9">
              <div id="prev_thumb_img" class="fileupload-preview thumbnail" style="width: 160px; height: 160px;">
               <?php if(!empty($act['image_url'])){?>
                   <img src="<?php echo $act['image_url'];?>" />
                  <a href='javascript:void(0)' onclick='delThumbImg(this);return false;'>删除</a>
               <?php }?>
              </div>
              <!-- SWFUpload控件 -->
              <div id="divSWFUploadUI">
                 <p>
                    <span id="spanButtonPlaceholder"></span>
					<input id="btnCancel" type="hidden" value="全部取消" disabled="disabled"/>
                 </p>
              <p class="help-block">如果不需要显示在首页，可以不用配置主图</p><br/>
              </div>
              <!-- END -->
            </div>
		</div>	
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 轮播图(最多9张)：</label>
			<div class="col-sm-9">
                <div id="prev_goods_img" class="fileupload-preview thumbnail" style="width: 100%; height: 150px;">
                <ul>
                <?php if (!empty($act['image_urls'])):?>
                    <?php foreach($act['image_urls'] as $img):if(!empty($img)):?>
                       <li>
                        <img style="width:100px;height:120px;margin-right:2px" src="<?php echo $img;?>" />
                        <a href='javascript:void(0)' onclick="delGoodsImg(this);return false;">删除</a>
                       </li> 
                    <?php endif;endforeach;?>
                <?php endif?>
                </ul>
                </div>
                <!-- SWFUpload控件 -->
                <div id="divSWFUploadUI2">
                    <p>
                        <span id="spanButtonPlaceholder2"></span>
						<input id="btnCancel2" type="hidden" value="全部取消" disabled="disabled"/>
                    </p>
                </div>
                <!-- END -->
			</div>
		</div>

		<div class="form-group">
            <hr/>
			<label class="col-sm-2 control-label no-padding-left"> 微信分享标题：</label>
			<div class="col-sm-9">
				<input type="text" name="wxShareTitle" id="wxShareTitle" maxlength="120" class="span7" value="<?php if (!empty($act['wx_share_title'])){echo $act['wx_share_title'];}?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 微信分享描述：</label>
			<div class="col-sm-9">
				<input style="width:350px" type="text" name="wxShareDesc" id="wxShareDesc" maxlength="240" class="span7" value="<?php if (!empty($act['wx_share_desc'])){echo $act['wx_share_desc'];}?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 微信分享图：<br><span style="font-size:12px;color:red">（尺寸尽量小于50K）</span></label>
			<div class="col-sm-9">
              <div id="prev_thumb_img2" class="fileupload-preview thumbnail" style="width: 140px; height: 150px;">
               <?php if(!empty($act['wx_share_img'])){?>
                   <img src="<?php echo $act['wx_share_img'];?>" />
                  <a href='javascript:void(0)' onclick='delThumbImg2(this);return false;'>删除</a>
               <?php }?>
              </div>
              <!-- SWFUpload控件 -->
              <div id="divSWFUploadUI3">
                 <p>
                    <span id="spanButtonPlaceholder3"></span>
					<input id="btnCancel3" type="hidden" value="全部取消" disabled="disabled"/>
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
		
        <input type="hidden"  id="thumb_img" class="thumb_img" value="<?php if (!empty($act['image_url'])){echo $act['image_url'];}?>">
        <input type="hidden"  id="thumb_img2" class="thumb_img2" value="<?php if (!empty($act['wx_share_img'])){echo $act['wx_share_img'];}?>">
        <div id="goods_img">
        <?php if (!empty($act['image_urls'])):?>
            <?php foreach($act['image_urls'] as $img):if(!empty($img)):?>
               <input type="hidden" name="goods_img[]" value="<?php echo $img;?>" />
            <?php endif; endforeach;?>
            <?php endif?>
        </div>
	</form>
	<script>
        $(document).ready(function(){
           $('#beginTime,#endTime').datetimepicker({
              format: "yyyy-mm-dd hh:ii:00",
              minView: "0",
              //pickerPosition: "top-right",
              autoclose: true
            });
        });
        $('#save-btn').click(function(){
            var url = $("#save-form").attr("action");
            var goods_imgs = '';
             $("#goods_img input").each(function(i,v){
                 goods_imgs += $(v).val()+'|';
             });
            $.post(url,{
                actId:$("#actId").val(),
                title:$("#title").val(),
                beginTime:$("#beginTime").val(),
                endTime:$("#endTime").val(),
                sort:$("#sort").val(),
                showArea:$("#showArea option:selected").val(),
                imageUrls:goods_imgs,
                imageUrl:$("#thumb_img").val(),
                wx_share_title:$("#wxShareTitle").val(),
                wx_share_desc:$("#wxShareDesc").val(),
                wx_share_img:$("#thumb_img").val()
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
    <!-- <link href="/asset/js/swfupload/swfupload.css<?php echo '?v=' . ASSETS_VERSION;?>" rel="stylesheet" type="text/css"/> -->
</body>
</html>
