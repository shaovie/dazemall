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
	<h3 class="header smaller lighter blue"><span style="margin-right:20px"><?php echo $title;?></span><a href="/admin/GoodsModule/addGoodsPage?moduleId=<?php echo $moduleId?>" class="btn btn-primary">添加商品</a><span class="refresh">刷新</span></h3>
		
	<table class="table table-striped table-bordered table-hover">
		<tbody>
		<tr>
			<th class="text-center" style="width:100px;">商品编号</th>
			<th class="text-center" style="width:250px;">商品名</th>
			<th class="text-center" style="width:200px;">排序</th>
			<th class="text-center">操作</th>
		</tr>
        <?php foreach ($goodsList as $goods):?>
		<tr>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $goods['goods_id']?></div>
			</td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $goods['name']?></div>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $goods['sort']?></div>
			</td>
			<td style="text-align:center;vertical-align:middle;">
                <a class="btn btn-xs btn-info" href="/admin/GoodsModule/editGoodsPage?moduleId=<?php echo $moduleId;?>&goodsId=<?php echo $goods['goods_id']?>">编辑</a>
                <a class="btn btn-xs btn-info" href="/admin/GoodsModule/delGoods?moduleId=<?php echo $moduleId;?>&goodsId=<?php echo $goods['goods_id']?>" onclick="return confirm(&#39;确认删除吗？&#39;);return false;">删除</a>
			</td>
		</tr>
        <?php endforeach?>
		</tbody>
	</table>
</body>
</html>
