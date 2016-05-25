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
</head>
<body class="no-skin">
<h3 class="header smaller lighter blue">订单管理<span style="margin-left:30px;color:#666;font-size:18px;">订单总数：<?php echo $totalOrderNum?></span><span class="refresh">刷新</span></h3>
<form action="/admin/Order/search" method="get" >	
	<table class="table" border="0" style="width:95%;" align="center">
		<tbody>
			<tr>
			<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:110px">订单编号：</td>
			<td style="width:180px"> <input name="orderId" type="text" value="<?php if (isset($search['OrderId'])) {echo $search['OrderId'];}?>"> </td>
			<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:110px">下单时间：</td>
			<td colspan="2">
				<input name="beginTime" id="beginTime" type="text" value="<?php if (isset($search['beginTime'])) {echo $search['beginTime'];}?>" > - <input id="endTime" name="endTime" type="text" value="<?php if (isset($search['endTime'])) {echo $search['endTime'];}?>" >		
			</td>	
		    </tr>
			<tr>
				<td style="vertical-align: middle;font-size: 14px;font-weight: bold;">下单人手机：</td>
				<td><input name="phone" type="text" value="<?php if (isset($search['phone'])) {echo $search['phone'];}?>"></td>
				<td style="vertical-align: middle;font-size: 14px;font-weight:bold;">收货人手机：</td>
                <td width="350"><input name="rePhone" type="text" value="<?php if (isset($search['rePhone'])) {echo $search['rePhone'];}?>"></td>
                <td><input type="submit" name="submit" value=" 查 询 " class="btn btn-sm  btn-primary">
                   <?php if (!empty($error)):?><span style="margin-left:20px;color:red;font-size:12px;"><?php {echo $error;}?><?php endif?></span>
                </td>
			</tr>
		</tbody>
	</table>
	</form>			
	<table class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th style="width:120px;text-align:center;">订单编号</th>
				<th style="width:100px;text-align:center;">收货人姓名</th>
				<th style="width:90px;text-align:center;">联系电话</th>
				<th style="width:100px;text-align:center;">支付方式</th>
				<th style="width:100px;text-align:center;">配送方式</th>
				<th style="width:60px;text-align:center;">运费</th>
				<th style="width:100px;text-align:center;">总价</th>
				<th style="width:150px;text-align:center;">状态</th>
				<th style="width:250px;text-align:center;">时间</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
            <?php foreach ($orderList as $order):?>
			<tr>
				<td style="text-align:center;vertical-align:middle;"><?php echo $order['order_id']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $order['re_name']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $order['re_phone']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $order['ol_pay_type']?> </td>
				<td style="text-align:center;vertical-align:middle;">默认</td>
                <td style="text-align:center;vertical-align:middle;"><?php echo $order['postage']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $order['order_amount']?></td>
                <td style="text-align:left;vertical-align:middle;">
                    <?php if ($order['pay_state'] == 0):?>
                    <div>支付：<span class="label label-warning">未支付</span> </div>
                    <?php elseif ($order['pay_state'] == 1):?>
                    <div>支付：<span class="label label-success">支付成功</span> </div>
                    <?php else:?>
                    <div>支付：<span class="label label-important">未知</span> </div>
                    <?php endif?>

                    <?php if ($order['order_state'] == 0):?>
                    <div>订单：<span class="label label-warning">提交成功</span> </div>
                    <?php elseif ($order['order_state'] == 1):?>
                    <div>订单：<span class="label label-success">订单完成</span> </div>
                    <?php elseif ($order['order_state'] == 2):?>
                    <div>订单：<span class="label label-info">订单取消</span> </div>
                    <?php else:?>
                    <div>订单：<span class="label label-important">未知</span> </div>
                    <?php endif?>
				<td style="text-align:left;vertical-align:middle;">
                    <div>支付时间：<?php echo empty($order['pay_time']) ? '' : date('Y-m-d H:i:s', $order['pay_time'])?></div>
                    <div>下单时间：<?php echo date('Y-m-d H:i:s', $order['ctime'])?></div>
                </td>
				<td style="text-align:left;vertical-align:middle;">
                    <a class="btn btn-xs btn-info" href="/admin/Order/info?orderId=<?php echo $order['order_id']?>"><i class="icon-edit"></i>查看详情</a>
                    &nbsp;<a class="btn btn-xs btn-info" href="/admin/Order/orderPrint?orderId=<?php echo $order['order_id'];?>" target="_bank">订单打印 </a>
                </td>
			</tr>
            <?php endforeach?>
		</tbody>
	</table>
    <?php echo $pageHtml;?>
</body>
</html>
