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
    <input id="actId" name="actId" type="hidden" value="<?php echo (isset($actId) ? $actId : 0);?>"/>
	<h3 class="header smaller lighter blue"><?php echo $title?></h3>
	<form action="<?php echo $action?>" method="post" enctype="multipart/form-data" class="form-horizontal" role="form" id="save-form">
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left">商品ID：</label>
			<div class="col-sm-9">
				<input type="text" name="goodsId" id="goodsId"
                onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" 
                <?php if (!empty($goods['goods_id'])){echo 'value="' . $goods['goods_id'] . '"' . ' readonly="readonly"';}?>
                >
          	</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left">排序：</label>
			<div class="col-sm-9">
				<input type="text" name="sort" id="sort"
                onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" 
                value="<?php if (!empty($goods['sort'])){echo $goods['sort'];}?>">
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
        $('#save-btn').click(function(){
            var url = $("#save-form").attr("action");
            $.post(url,{
                actId:$("#actId").val(),
                goodsId:$("#goodsId").val(),
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
</body>
</html>
