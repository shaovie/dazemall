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
	<h3 class="header smaller lighter blue"><span style="margin-right:20px">商品总数：<?php echo $totalGoodsNum;?></span><a href="/admin/Goods/addPage" class="btn btn-primary">新建商品</a><span class="refresh">刷新</span></h3>
	<form action="/admin/Goods/search" class="form-horizontal" method="get">
	<table class="table table-striped table-bordered table-hover">
	<tbody>
		<tr>
			<td>
				<li style="float:left;list-style-type:none;">
					<select name="status" style="margin-right:10px;margin-top:10px;width: 100px; height:34px; line-height:28px; padding:2px 0">
					    <option value="-1" <?php if (!isset($search['status']) || $search['status'] == -1) { echo 'selected="selected"';}?> >商品状态</option>
					    <option value="0" <?php if (isset($search['status']) && $search['status'] == 0) { echo 'selected="selected"';}?> >无效</option>
					    <option value="1" <?php if (isset($search['status']) && $search['status'] == 1) { echo 'selected="selected"';}?>  >有效</option>
					    <option value="2" <?php if (isset($search['status']) && $search['status'] == 2) { echo 'selected="selected"';}?> >上架销售</option>
					</select>
				</li>
				<li style="float:left;list-style-type:none;">
					<span>关键字</span>	<input style="margin-right:10px;margin-top:10px;width:200px; height:34px; line-height:28px; padding:2px 5px" name="keyword" id="" type="text" value="<?php if (!empty($search['keyword'])) {echo $search['keyword'];} ?>" placeholder="商品编号/商品名称">
				</li>
				<li style="list-style-type:none;">
					<input type="submit" name="submit" class="btn btn-sm btn-primary" style="margin-right:10px;margin-top:10px;" value="搜索"></li>
			</td>
		</tr>
	</tbody>
	</table>
	</form>
		
	<table class="table table-striped table-bordered table-hover">
		<tbody>
		<tr>
			<th class="text-center" style="width:100px;">主图</th>
			<th class="text-center" style="width:250px;">商品</th>
			<th class="text-center" style="width:250px;">品类</th>
			<th class="text-center" style="width:140px;">价格</th>
			<th class="text-center" style="width:260px;">时间</th>
			<th class="text-center" style="width:200px;">状态</th>
			<th class="text-center" style="width:100px;">排序</th>
			<th class="text-center">操作</th>
		</tr>
        <?php foreach ($goodsList as $goods):?>
		<tr>
			<td style="padding:0px;margin:0px;">
                <p style="text-align:center;vertical-align:middle;margin:2px 0px;"> <img src="<?php echo $goods['image_url']?>" height="60" width="60"></p>
            </td>
			<td style="text-align:left;vertical-align:middle;">
                <div>编号：<?php echo $goods['id']?></div>
                <div>名称：<?php echo $goods['name']?></div>
            </td>
			<td style="text-align:left;vertical-align:middle;">
                <div>类别：<?php echo $goods['category_name']?></div>
            </td>
			<td style="text-align:left;vertical-align:middle;">
                <div>市场价：<?php echo $goods['market_price']?></div>
                <div>销售价：<?php echo $goods['sale_price']?></div>
            </td>
			<td style="text-align:left;vertical-align:middle;">
				<div>创建时间：<?php echo date('Y-m-d H:i:s', $goods['ctime'])?></div>
            </td>
			<td style="text-align:left;vertical-align:middle;">
                <div>上架状态：<?php echo $goods['state']?></div>
			</td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $goods['sort']?></div>
			</td>
			<td style="text-align:center;vertical-align:middle;">
				<a target="_blank" class="btn btn-xs btn-info" href="/mall/Goods/detail?goodsId=<?php echo $goods['id']?>">预览</a>
				<a class="btn btn-xs btn-info" href="/admin/Goods/editPage?goodsId=<?php echo $goods['id']?>">编辑</a>
				<a class="btn btn-xs btn-info" href="/admin/Goods/skuPage?goodsId=<?php echo $goods['id']?>">商品SKU</a>
			</td>
		</tr>
        <?php endforeach?>
		</tbody>
	</table>
    <?php echo $pageHtml;?>
</body>
</html>
