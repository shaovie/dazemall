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
</head>
<body class="no-skin">
    <h3 class="header smaller lighter blue"><span style="margin-right:20px">库存管理</span></h3>
	<form action="" class="form-horizontal" method="post" onsubmit="return formcheck(this)">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th class="text-center" style="width:80px;">商品SKU</th>
					<th class="text-center" style="width:50px;">现有库存</th>
					<th class="text-center" style="width:80px;">修改人</th>
					<th class="text-center" style="width:80px;">修改时间</th>
					<th class="text-center" style="width:80px;">操作</th>
				</tr>
			</thead>
			<tbody>
            <?php foreach ($skuList as $item):?>
			<tr>
				<td style="text-align:center;vertical-align:middle;"><?php echo $item['sku']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $item['amount']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $item['m_user']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo date('Y-m-d H:i:s', $item['mtime'])?></td>
				<td style="text-align:center;vertical-align:middle;">
					<a class="btn btn-xs btn-info" href="/admin/Goods/listPage?attrId=<?php echo $item['id'];?>"><i class="icon-plus-sign-alt"></i> 修改库存</a>&nbsp;&nbsp;
				</td>
			</tr>
            <?php endforeach?>
			</tbody>
		</table>
	</form>
</body>
</html>
