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
</head>
<body class="no-skin">
	<h3 class="header smaller lighter blue"><span style="margin-right:20px">用户总数：<?php echo $totalUserNum;?></span></h3>
	<form action="/admin/User/search" class="form-horizontal" method="get">
	<table class="table table-striped table-bordered table-hover">
	<tbody>
		<tr>
			<td>
				<li style="float:left;list-style-type:none;">
					<span>关键字</span>	<input style="margin-right:10px;margin-top:10px;width:200px; height:34px; line-height:28px; padding:2px 5px" name="keyword" id="" type="text" value="<?php if (!empty($search['keyword'])) {echo $search['keyword'];} ?>" placeholder="客户编号/昵称/手机号">
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
			<th class="text-center" style="width:100px;">用户编号</th>
			<th class="text-center" style="width:250px;">昵称</th>
			<th class="text-center" style="width:250px;">手机号</th>
			<th class="text-center" style="width:260px;">账户信息</th>
			<th class="text-center" style="width:260px;">时间</th>
			<th class="text-center">操作</th>
		</tr>
        <?php foreach ($userList as $user):?>
		<tr>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $user['id']?></div>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $user['nickname']?></div>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $user['phone']?></div>
            </td>
			<td style="text-align:left;vertical-align:middle;">
                <div>余额：<?php echo $user['cash_amount']?></div>
            </td>
			<td style="text-align:left;vertical-align:middle;">
                <div>注册时间：<?php echo date('Y-m-d H:i:s', $user['ctime'])?></div>
            </td>
			<td style="text-align:center;vertical-align:middle;">
				<a class="btn btn-xs btn-info" href="/admin/User/recharge?userId=<?php echo $user['id']?>">充值</a>
			</td>
		</tr>
        <?php endforeach?>
		</tbody>
	</table>
    <?php echo $pageHtml;?>
</body>
</html>

