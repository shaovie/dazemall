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
	<script type="text/javascript" src="/asset/js/goods.js"></script>
</head>
<body class="no-skin">
    <h3 class="header smaller lighter blue"><span style="margin-right:20px">分类列表</span><a href="/admin/GoodsCategory/addPage?parentId=0" class="btn btn-primary">新建分类</a></h3>
	<form action="" class="form-horizontal" method="post" onsubmit="return formcheck(this)">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th class="text-center" style="width:100px;">分类图片</th>
					<th class="text-center" style="width:80px;">分类名称</th>
					<th class="text-center" style="width:50px;">显示顺序</th>
					<th class="text-center" style="width:80px;">操作</th>
				</tr>
			</thead>
			<tbody>
            <?php foreach ($categoryList as $cat):?>
			<tr>
				<td style="padding:0px;margin:0px;">
                <p style="text-align:center;vertical-align:middle;margin:2px 0px;"> <img src="<?php echo $cat['image_url']?> " height="60" width="60"></p></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $cat['name']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $cat['sort']?></td>
				<td style="text-align:center;vertical-align:middle;">
					<a class="btn btn-xs btn-info" href="/admin/GoodsCategory/addPage?parentId=<?php echo $cat['category_id'];?>"><i class="icon-plus-sign-alt"></i> 添加子分类</a>&nbsp;&nbsp;
					<a class="btn btn-xs btn-info" href="/admin/GoodsCategory/catInfo?catId=<?php echo $cat['category_id'];?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
					<a class="btn btn-xs btn-info" href="/admin/GoodsCategory/del?catId=<?php echo $cat['category_id'];?>" onclick="return confirm(&#39;确认删除此分类吗？&#39;);return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
				</td>
			</tr>
            <?php endforeach?>
			</tbody>
		</table>
	</form>
</body>
</html>
