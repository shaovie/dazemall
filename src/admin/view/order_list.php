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
	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th
	{
		border-top: 0px;
	}
</style>
</head>
<body class="no-skin">
<h3 class="header smaller lighter blue">订单管理</h3>
<form action="/admin/Order/search" method="get">	
	<table class="table" style="width:95%;" align="center">
		<tbody>
			<tr>
			<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:110px">订单编号：</td>
			<td style="width:180px"> <input name="orderId" type="text" value="<?php if (isset($search['OrderId'])) {echo $search['OrderId'];}?>"> </td>
			<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:110px">下单时间：</td>
			<td>
				<input name="beginTime" id="beginTime" type="text" value="<?php if (isset($search['beginTime'])) {echo $search['beginTime'];}?>" > - <input id="endTime" name="endTime" type="text" value="<?php if (isset($search['endTime'])) {echo $search['endTime'];}?>" >		
				<script type="text/javascript">
					$("#beginTime,#endTime").datetimepicker({
                    format: "yyyy-mm-dd hh:ii:00",
					minView: "0",
					//pickerPosition: "top-right",
					autoclose: true
				});
			</script> 
			</td>	
		    </tr>
			<tr>
				<td style="vertical-align: middle;font-size: 14px;font-weight: bold;">下单人手机：</td>
				<td><input name="phone" type="text" value="<?php if (isset($search['phone'])) {echo $search['phone'];}?>"></td>
				<td style="vertical-align: middle;font-size: 14px;font-weight: bold;">收货人手机：</td>
			    <td><input name="rePhone" type="text" value="<?php if (isset($search['rePhone'])) {echo $search['rePhone'];}?>"></td>
			</tr>
			<tr>
				<td></td>
				<td colspan="3"><input type="submit" name="submit" value=" 查 询 " class="btn btn-primary">
                <?php if (!empty($error)):?><span style="margin-left:20px;color:red;font-size:12px;"><?php {echo $error;}?><?php endif?></span>
                </td>
			</tr>
		</tbody>
	</table>
	</form>			
	<h3 class="blue">
        <span style="font-size:18px;"><strong>订单总数：<?php echo $totalOrderNum?></strong></span>
    </h3>
	<table class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th style="width:120px;">订单编号</th>
				<th style="width:100px;">收货人姓名</th>
				<th style="width:80px;">联系电话</th>
				<th style="width:80px;">支付方式</th>
				<th style="width:80px;">配送方式</th>
				<th style="width:50px;">运费</th>
				<th style="width:100px;">总价</th>
				<th style="width:80px;">状态</th>
				<th style="width:150px;">下单时间</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
            <?php foreach ($orderList as $order):?>
			<tr>
				<td><?php echo $order['order_id']?></td>
				<td><?php echo $order['re_name']?></td>
				<td><?php echo $order['re_phone']?></td>
				<td><?php echo $order['ol_pay_type']?> </td>
				<td>默认</td>
                <td><?php echo $order['postage']?></td>
				<td><?php echo $order['order_amount']?></td>
				<td><span class="label label-danger">待发货</span></td>
				<td><?php echo date('Y-m-d H:i:s', $order['ctime'])?></td>
				<td><a class="btn btn-xs btn-info" href="/admin/Order/info?orderId=<?php echo $order['order_id']?>"><i class="icon-edit"></i>查看详情</a>&nbsp;&nbsp;</td>
			</tr>
            <?php endforeach?>
		</tbody>
	</table>
    <?php echo $pageHtml;?>
</body>
</html>
