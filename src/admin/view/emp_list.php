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
	<h3 class="header smaller lighter blue"><a href="/admin/Employee/addPage" class="btn btn-primary">新增用户</a><span class="refresh">刷新</span></h3>
	<table class="table table-striped table-bordered table-hover">
		<tbody>
		<tr>
			<th class="text-center" style="width:120px;">账户</th>
			<th class="text-center" style="width:150px;">姓名</th>
			<th class="text-center" style="width:240px;">手机号</th>
			<th class="text-center" style="width:240px;">状态</th>
			<th class="text-center" style="width:200px;">创建时间</th>
			<th class="text-center">操作</th>
		</tr>
        <?php foreach ($empList as $emp):?>
		<tr>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $emp['account']?></div>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $emp['name']?></div>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $emp['phone']?></div>
			</td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $emp['state'] == 0 ? '无效' : '有效'?></div>
			</td>
			<td style="text-align:center;vertical-align:middle;">
				<?php echo date('Y-m-d H:i:s', $emp['ctime'])?>
            </td>
			<td style="text-align:center;vertical-align:middle;">
				<a class="btn btn-xs btn-info" href="/admin/Employee/info?account=<?php echo $emp['account']?>">编辑</a>
                <a class="btn btn-xs btn-info" href="/admin/Employee/stateOpt?account=<?php echo $emp['account'];?>" onclick="return confirm(&#39;确认要操作吗？&#39;);return false;"><?php echo $emp['state'] == 1 ? '置为无效' : '置为有效'?></a>
			</td>
		</tr>
        <?php endforeach?>
		</tbody>
	</table>
</body>
</html>
