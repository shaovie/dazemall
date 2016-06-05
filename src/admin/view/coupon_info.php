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
    <input id="couponId" name="couponId" type="hidden" value="<?php echo (isset($coupon['id']) ? $coupon['id'] : 0);?>"/>
	<h3 class="header smaller lighter blue"><?php echo $title?></h3>
	<form action="<?php echo $action?>" method="post" enctype="multipart/form-data" class="form-horizontal" role="form" id="save-form">
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 名称：</label>
			<div class="col-sm-9">
				<input type="text" name="name" id="name" maxlength="100" class="span7" value="<?php if (!empty($coupon['name'])){echo $coupon['name'];}?>"
                <?php if (isset($coupon['id'])){echo 'readonly="readonly"';}?>
                >
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 备注：</label>
			<div class="col-sm-9">
				<input type="text" name="remark" id="remark" maxlength="100" class="span7" value="<?php if (!empty($coupon['remark'])){echo $coupon['remark'];}?>"
                >
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 状态：</label>
			<div class="col-sm-9" id="state-radio">
				<div style="margin-right:20px;display:inline;">
                    <input type="radio" name="state" value="0" id="isshow0" <?php if (isset($coupon['state']) && $coupon['state'] == 0) { echo 'checked="true"';}?>
                    >无效</div>

				<div style="margin-right:20px;display:inline;">
                    <input type="radio" name="state" value="1" id="isshow1" <?php if (isset($coupon['state']) && $coupon['state'] == 1) { echo 'checked="true"';}?> 
                    >有效</div>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 有效时间段：</label>
			<div class="col-sm-9">
				<input type="text" name="beginTime" id="beginTime" maxlength="100" class="span7" value="<?php if (!empty($coupon['begin_time'])){echo date('Y-m-d H:i:s', $coupon['begin_time']);}?>" placeholder="开始时间"
                <?php if (isset($coupon['id'])){echo 'disabled="disabled"';}?>
                > - 
				<input type="text" name="endTime" id="endTime" maxlength="100" class="span7" value="<?php if (!empty($coupon['end_time'])){echo date('Y-m-d H:i:s', $coupon['end_time']);}?>" placeholder="结束时间"
                <?php if (isset($coupon['id'])){echo 'disabled="disabled"';}?>
                >
				<p class="help-block">格式：2016-01-18 12:30:23，开始时间不填，默认当前时间，结束时间不填，意为永远</p>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left">适合品类：</label>
			<div class="col-sm-9">
				<input type="text" name="category_id" id="category_id"
                onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" 
                value="<?php if (isset($coupon['category_id'])){echo $coupon['category_id'];}?>"
                <?php if (isset($coupon['id'])){echo 'readonly="readonly"';}?>
                >
				<p class="help-block">输入品类编号（0 为全品类）(只限一个品类)</p>
          	</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 面额：</label>
			<div class="col-sm-9">
				<input type="number" name="couponAmount" id="couponAmount" value="<?php if (isset($coupon['coupon_amount'])){echo $coupon['coupon_amount'];}?>"
                <?php if (isset($coupon['id'])){echo 'readonly="readonly"';}?>
                >&nbsp;元
			</div>
		</div>
				
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 订单限定金额：</label>
			<div class="col-sm-9">
				<input type="number" name="orderAmount" id="orderAmount" value="<?php if (isset($coupon['order_amount'])){echo $coupon['order_amount'];}?>"
                <?php if (isset($coupon['id'])){echo 'readonly="readonly"';}?>
                >&nbsp;元
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
                couponId:$("#couponId").val(),
                name:$("#name").val(),
                remark:$("#remark").val(),
                state:$("#state-radio input[name='state']:checked").val(),
                beginTime:$("#beginTime").val(),
                endTime:$("#endTime").val(),
                couponAmount:$("#couponAmount").val(),
                orderAmount:$("#orderAmount").val(),
                categoryId:$("#category_id").val()
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
