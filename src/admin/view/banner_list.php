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
	<h3 class="header smaller lighter blue"><a href="/admin/Banner/addPage" class="btn btn-primary">新建</a><span class="refresh">刷新</span></h3>
	<form action="/admin/Banner/search" class="form-horizontal" method="get">
	<table class="table table-striped table-bordered table-hover">
	<tbody>
		<tr>
			<td>
				<li style="float:left;list-style-type:none;">
					<select name="status" style="margin-right:10px;margin-top:10px;width: 100px; height:34px; line-height:28px; padding:2px 0">
					    <option value="-1" <?php if (!isset($search['status']) || $search['status'] == -1) { echo 'selected="selected"';}?> >状态</option>
					    <option value="0" <?php if (isset($search['status']) && $search['status'] == 0) { echo 'selected="selected"';}?> >无效</option>
					    <option value="1" <?php if (isset($search['status']) && $search['status'] == 1) { echo 'selected="selected"';}?>  >有效</option>
					</select>
				</li>
				<li style="float:left;list-style-type:none;">
					<span>备注</span>	<input style="margin-right:10px;margin-top:10px;width:200px; height:34px; line-height:28px; padding:2px 5px" name="keyword" id="" type="text" value="<?php if (!empty($search['keyword'])) {echo $search['keyword'];} ?>" placeholder="备注">
				</li>
				<li style="list-style-type:none;">
					<input type="submit" name="submit" class="btn btn-sm btn-primary" style="margin-right:10px;margin-top:10px;" disabled="disabled" value="搜索"></li>
			</td>
		</tr>
	</tbody>
	</table>
	</form>
		
	<table class="table table-striped table-bordered table-hover">
		<tbody>
		<tr>
			<th class="text-center" style="width:250px;">备注</th>
			<th class="text-center" style="width:120px;">图片</th>
			<th class="text-center" style="width:150px;">展示区域</th>
			<th class="text-center" style="width:240px;">展示时间</th>
			<th class="text-center" style="width:200px;">排序</th>
			<th class="text-center">操作</th>
		</tr>
        <?php foreach ($bannerList as $banner):?>
		<tr>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $banner['remark']?></div>
            </td>
			<td style="padding:0px;margin:0px;">
                <p style="text-align:center;vertical-align:middle;margin:2px 0px;"> <img src="<?php echo $banner['image_url']?>" height="60" width="60"></p>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $banner['showArea']?></div>
            </td>
			<td style="text-align:left;vertical-align:middle;">
				<div>开始：<?php echo empty($banner['begin_time']) ? '' : date('Y-m-d H:i:s', $banner['begin_time'])?></div>
				<div>结束：<?php echo empty($banner['end_time']) ? '' : date('Y-m-d H:i:s', $banner['end_time'])?></div>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $banner['sort']?></div>
			</td>
			<td style="text-align:center;vertical-align:middle;">
				<a class="btn btn-xs btn-info" href="/admin/Banner/editPage?bannerId=<?php echo $banner['id']?>">编辑</a>
                <a class="btn btn-xs btn-info" href="/admin/Banner/del?bannerId=<?php echo $banner['id'];?>" onclick="return confirm(&#39;确认删除吗？&#39;);return false;">删除</a>
			</td>
		</tr>
        <?php endforeach?>
		</tbody>
	</table>
    <?php echo $pageHtml;?>
</body>
</html>
