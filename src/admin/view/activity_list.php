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
	<h3 class="header smaller lighter blue"><a href="/admin/Activity/addPage" class="btn btn-primary">新建</a><span class="refresh">刷新</span></h3>
		
	<table class="table table-striped table-bordered table-hover">
		<tbody>
		<tr>
			<th class="text-center" style="width:100px;">编号</th>
			<th class="text-center" style="width:250px;">标题</th>
			<th class="text-center" style="width:100px;">主图</th>
			<th class="text-center" style="width:150px;">展示位置</th>
			<th class="text-center" style="width:260px;">时间</th>
			<th class="text-center" style="width:200px;">排序</th>
			<th class="text-center">操作</th>
		</tr>
        <?php foreach ($actList as $act):?>
		<tr>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $act['id']?></div>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $act['title']?></div>
            </td>
			<td style="padding:0px;margin:0px;">
                <p style="text-align:center;vertical-align:middle;margin:2px 0px;"> <img src="<?php echo $act['image_url']?>" height="60" width="60"></p>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $act['showArea']?></div>
			</td>
			<td style="text-align:left;vertical-align:middle;">
				<div>开始：<?php echo empty($act['begin_time']) ? '' : date('Y-m-d H:i:s', $act['begin_time'])?></div>
				<div>结束：<?php echo empty($act['end_time']) ? '' : date('Y-m-d H:i:s', $act['end_time'])?></div>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $act['sort']?></div>
			</td>
			<td style="text-align:center;vertical-align:middle;">
				<a class="btn btn-xs btn-info" href="/admin/Activity/editPage?actId=<?php echo $act['id']?>">编辑</a>
                <a class="btn btn-xs btn-info" href="/admin/Activity/del?actId=<?php echo $act['id'];?>" onclick="return confirm(&#39;确认删除吗？&#39;);return false;">删除</a>
				<a class="btn btn-xs btn-info" href="/admin/Activity/goodsList?actId=<?php echo $act['id']?>">商品列表</a>
			</td>
		</tr>
        <?php endforeach?>
		</tbody>
	</table>
    <?php echo $pageHtml;?>
</body>
</html>
