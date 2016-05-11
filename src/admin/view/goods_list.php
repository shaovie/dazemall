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
	<link type="text/css" rel="stylesheet" href="/asset/css/datetimepicker.css">
	<script type="text/javascript" src="/asset/js/datetimepicker.js"></script>
	<style>
		html {overflow-x:hidden; }
		body {
			background-color: #FFFFFF;
		}
		table{border-top: 0px;}
		.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{
			border-top: 0px;
		}
	</style>
</head>
<body class="no-skin">
	<h3 class="header smaller lighter blue"><span style="margin-right:20px">商品总数：<?php echo $totalGoodsNum;?></span><a href="#" class="btn btn-primary">新建商品</a></h3>
	<form action="" class="form-horizontal" method="post">
	<table class="table table-striped table-bordered table-hover">
	<tbody>
		<tr>
			<td>
				<li style="float:left;list-style-type:none;">
					<select name="status" style="margin-right:10px;margin-top:10px;width: 100px; height:34px; line-height:28px; padding:2px 0">
					    <option value="-1" selected="selected">上架状态</option>
						<option value="1">上架中</option>
						<option value="0">已下架</option>
					</select>
				</li>
				<li style="float:left;list-style-type:none;">
					<span>关键字</span>	<input style="margin-right:10px;margin-top:10px;width: 300px; height:34px; line-height:28px; padding:2px 0" name="keyword" id="" type="text" value="">
				</li>
				<li style="list-style-type:none;">
					<button class="btn btn-primary" style="margin-right:10px;margin-top:10px;"><i class="icon-search icon-large"></i> 搜索</button>
				</li>
			</td>
		</tr>
	</tbody>
	</table>
	</form>
		
	<table class="table table-striped table-bordered table-hover">
		<tbody>
		<tr>
			<th class="text-center">首图</th>
			<th class="text-center">商品名称</th>
			<th class="text-center">货号</th>
			<th class="text-center">价格</th>
			<th class="text-center">库存</th>
			<th class="text-center">商品属性</th>    
			<th class="text-center">状态</th>
			<th class="text-center">操作</th>
		</tr>
		<tr>
			<td><p style="text-align:center"> <img src="154677374716783.jpg" height="60" width="60"></p></td>
			<td style="text-align:center;">第一测试商品</td>
			<td style="text-align:center;">001</td>
			<td style="text-align:center;">100.00</td>
			<td style="text-align:center;">60</td>
			<td style="text-align:center;">
				<label data="1" class="label label-info">促销</label>
				<label data="1" class="label label-info">包邮</label>
				<label data="1" class="label label-info">首页推荐</label>
				<label data="1" class="label label-info">新品</label>
				<label data="1" class="label label-info">首发</label>
				<label data="1" class="label label-info">热卖</label>
				<label data="1" class="label label-info">精品</label>                                     
            </td>
			<td style="text-align:center;">
				<span data="1" onclick="setProperty1(this,1,&#39;status&#39;)" class="label label-success" style="cursor:pointer;">上架中</span>&nbsp;<span class="label label-info">虚拟商品</span>
			</td>
			<td style="text-align:center;">
				<a class="btn btn-xs btn-info" href="http://localhost/index.php?mod=site&id=1&op=post&name=shop&do=goods"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
				<a class="btn btn-xs btn-info" href="http://localhost/index.php?mod=site&id=1&op=delete&name=shop&do=goods" onclick="return confirm(&#39;此操作不可恢复，确认删除？&#39;);return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
				&nbsp;&nbsp;
			</td>
		</tr>				 	
		</tbody>
	</table>
    <?php echo $pageHtml;?>
</body>
</html>
