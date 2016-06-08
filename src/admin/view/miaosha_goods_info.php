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
	<h3 class="header smaller lighter blue"><?php echo $title?></h3>
	<form action="<?php echo $action?>" method="post" enctype="multipart/form-data" class="form-horizontal" role="form" id="save-form">
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 商品编号：</label>
			<div class="col-sm-9">
				<input type="text" name="goods_id" id="goods_id" maxlength="100" class="span7" value="<?php if (!empty($act['goods_id'])){echo $act['goods_id'];}?>">
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 时间段：</label>
			<div class="col-sm-9">
				<input type="text" name="beginTime" id="beginTime" maxlength="100" class="span7" value="<?php if (!empty($info['begin_time'])){echo date('Y-m-d H:i:s', $info['begin_time']);}?>" placeholder="开始时间"> - 
				<input type="text" name="endTime" id="endTime" maxlength="100" class="span7" value="<?php if (!empty($info['end_time'])){echo date('Y-m-d H:i:s', $info['end_time']);}?>" placeholder="结束时间">
				<p class="help-block">格式：2016-01-18 12:30:23，必填，不能为空. </p>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left">展示价格：</label>
			<div class="col-sm-9">
				<input type="number" name="price" id="price" 
                value="<?php if (!empty($act['price'])){echo $act['price'];}?>">
				<p class="help-block">一定要与定时调价保持一致</p>
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
			<label class="col-sm-2 control-label no-padding-left"></label>
			<div class="col-sm-9">
				<button type="button" id="save-btn" class="btn btn-primary span2" >保存</button>
			</div>
		</div>
		
	</form>
	<script>
        $(document).ready(function(){
            return ;
           $('#beginTime,#endTime').datetimepicker({
              format: "yyyy-mm-dd hh:ii:00",
              minView: "0",
              //pickerPosition: "top-right",
              minuteStep:1,
              showSecond:true,
              autoclose: true
            });
        });
        $('#save-btn').click(function(){
            var url = $("#save-form").attr("action");
            $.post(url,{
                sort:$("#sort").val(),
                goods_id:$("#goods_id").val(),
                beginTime:$("#beginTime").val(),
                endTime:$("#endTime").val(),
                price:$("#price").val()
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
