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
</head>
<body class="no-skin">
	<h3 class="header smaller lighter blue">
    <a href="/admin/Coupon/addPage" class="btn btn-primary">新建</a>
    <a href="/admin/Coupon/configPage" class="btn btn-primary">配置优惠券使用</a>
    <span class="refresh">刷新</span></h3>
		
	<table class="table table-striped table-bordered table-hover">
		<tbody>
		<tr>
			<th class="text-center" style="width:100px;">编号</th>
			<th class="text-center" style="width:250px;">名称</th>
			<th class="text-center" style="width:200px;">备注</th>
			<th class="text-center" style="width:200px;">时间</th>
			<th class="text-center" style="width:150px;">面额</th>
			<th class="text-center" style="width:160px;">订单金额</th>
			<th class="text-center" style="width:160px;">限定品类</th>
			<th class="text-center" style="width:200px;">状态</th>
			<th class="text-center">操作</th>
		</tr>
        <?php foreach ($couponList as $coupon):?>
		<tr>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $coupon['id']?></div>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $coupon['name']?></div>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $coupon['remark']?></div>
            </td>
			<td style="text-align:left;vertical-align:middle;">
				<div>开始：<?php echo empty($coupon['begin_time']) ? '' : date('Y-m-d H:i:s', $coupon['begin_time'])?></div>
				<div>结束：<?php echo empty($coupon['end_time']) ? '' : date('Y-m-d H:i:s', $coupon['end_time'])?></div>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $coupon['coupon_amount']?></div>
			</td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $coupon['order_amount']?></div>
			</td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $coupon['category']?></div>
			</td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $coupon['stateDesc']?></div>
			</td>
			<td style="text-align:center;vertical-align:middle;">
				<a class="btn btn-xs btn-info" href="/admin/Coupon/editPage?couponId=<?php echo $coupon['id']?>">编辑</a>
			</td>
		</tr>
        <?php endforeach?>
		</tbody>
	</table>
    <?php echo $pageHtml;?>
</body>
</html>
