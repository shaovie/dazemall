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
    <input id="id" name="id" type="hidden" value="<?php echo (isset($info['id']) ? $info['id'] : 0);?>"/>
	<h3 class="header smaller lighter blue"><?php echo $title?></h3>
	<form action="<?php echo $action?>" method="post" enctype="multipart/form-data" class="form-horizontal" role="form" id="save-form">
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 商品编号：</label>
			<div class="col-sm-9">
				<input type="text" name="goods_id" id="goods_id" maxlength="100" class="span7" value="<?php if (!empty($info['goods_id'])){echo $info['goods_id'];}?>"
                <?php if (!empty($info['goods_id'])){ echo 'readonly="readonly"';}?>
                >
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 操作类型：</label>
			<div class="col-sm-9" id="opt-radio">
				<div style="margin-right:20px;display:inline;">
                    <input type="radio" name="opt_type" value="1" id="isshow1" <?php if (isset($info['opt_type']) && $info['opt_type'] == 1) { echo 'checked="true"';}?> 
                    >上架</div>
				<div style="margin-right:20px;display:inline;">
                    <input type="radio" name="opt_type" value="2" id="isshow2" <?php if (isset($info['opt_type']) && $info['opt_type'] == 2) { echo 'checked="true"';}?> 
                    >下架(转为无效状态)</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 定时类型：</label>
			<div class="col-sm-9" id="timing-radio">
				<div style="margin-right:20px;display:inline;">
                    <input type="radio" name="timing_type" value="1" id="isshow11" <?php if (isset($info['timing_type']) && $info['timing_type'] == 1) { echo 'checked="true"';}?> >一次性</div>
				<div style="margin-right:20px;display:inline;">
                    <input type="radio" name="timing_type" value="2" id="isshow22" <?php if (isset($info['timing_type']) && $info['timing_type'] == 2) { echo 'checked="true"';}?> >每天</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 时间：</label>
			<div class="col-sm-9">
				<input type="text" name="beginTime" id="beginTime" maxlength="100" class="span7" value="<?php if (!empty($info['begin_time'])){echo $info['begin_time'];}?>" placeholder="开始时间" 
                 > - 
				<input type="text" name="endTime" id="endTime" maxlength="100" class="span7" value="<?php if (!empty($info['end_time'])){echo $info['end_time'];}?>" placeholder="结束时间"
                 >
				<p class="help-block">格式：2016-01-18 12:30:23，必填！定时类型如为·每天· 系统会忽略掉日期，只有时分秒有效</p>
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"></label>
			<div class="col-sm-9">
				<button type="button" id="save-btn" class="btn btn-primary span2" >保存</button>
				<button type="button" id="back" class="btn btn-warning span2">返回</button>
			</div>
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
            $.post(url,{
                id:$("#id").val(),
                goods_id:$("#goods_id").val(),
                optType:$("#opt-radio input[name='opt_type']:checked").val(),
                timingType:$("#timing-radio input[name='timing_type']:checked").val(),
                beginTime:$("#beginTime").val(),
                endTime:$("#endTime").val()
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
</body>
</html>
