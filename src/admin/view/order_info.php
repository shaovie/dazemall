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
	<link type="text/css" rel="stylesheet" href="/asset/css/datetimepicker.css<?php echo '?v=' . ASSETS_VERSION;?>">
	<script type="text/javascript" src="/asset/js/datetimepicker.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
</head>
<body class="no-skin">
	<h3 class="header smaller lighter blue">订单基本信息</h3>
	<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
		<table class="table">
		<tbody>
			<tr>
				<td style="width:100px"><label for="">订单编号：</label></td>
				<td><?php echo $order['orderId'];?></td>
				<td style="width:100px"><label for="">下单时间：</label></td>
				<td><?php echo date('Y-m-d H:i:s', $order['ctime']);?></td>
			</tr>
			<tr>
				<td><label for="">付款方式：</label></td>
				<td><?php echo $order['payType'];?></td>
				<td><label for="">付款时间：</label></td>
				<td><?php echo $order['payTime']?></td>
			</tr>
			<tr>
				<td><label for="">系统备注：</label></td>
				<td><?php echo $order['sysRemark'];?></td>
			</tr>
		</tbody>
		</table>
		<h3 class="header smaller lighter blue">收货人信息</h3>
		<table class="table " >
		<tbody>
			<tr>
				<th style="width:150px"><label for="">收货人姓名:</label></th>
				<td ><?php echo $order['reName']?></td>
				<th style="width:100px"><label for="">收货手机:</label></th>
				<td><?php echo $order['rePhone']?></td>
			</tr>
			<tr>
				<th style="width:150px"><label for="">收货人联系地址:</label></th>
				<td><?php echo $order['fullAddr']?></td>
				<th><label for="">订单备注:</label></th>
				<td><textarea readonly="readonly" style="width:200px;border: none;" type="text"><?php echo $order['remark']?></textarea>
				</td>
			</tr>
		</tbody>
		</table>
		
		<table class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th style="width:100px;">商品编号</th>
                <th>商品图片</th>
				<th>商品名称</th>
				<th>商品SKU</th>
				<th style="color:red;">成交价</th>
				<th>数量</th>				
			</tr>
		</thead>
		<tbody>
        <?php foreach ($order['goodsList'] as $goods):?>
			<tr>
				<td style="text-align:center;vertical-align:middle;"><?php echo $goods['goods_id']?></td>
				<td><img src="<?php echo $goods['img']?>" style="width:60px;height:60px;" /></td>
				<td style="vertical-align:middle;"><?php echo $goods['name']?></td>
                <td style="vertical-align:middle;">
                    <?php echo $goods['sku_attr'] . '：' . $goods['sku_value']?>
                 </td>
				<td style="color:red;font-weight:bold;vertical-align:middle;"><?php echo $goods['price']?></td>
				<td style="vertical-align:middle;"><?php echo $goods['amount']?></td>
			</tr>
        <?php endforeach?>
		</tbody>
		</table>
		<table class="table">
		<tbody>
			<tr>
				<th style="width:50px"></th>
				<td>
					<button type="button" class="btn btn-primary span2" name="confirmsend"
                    data-toggle="modal" data-target="#modal-confirmsend" value="confirmsend"
                    <?php if ($order['orderState'] == \src\user\model\UserOrderModel::ORDER_ST_CANCELED
                        || $order['payState'] != \src\pay\model\PayModel::PAY_ST_SUCCESS
                        || $order['deliveryState'] != \src\user\model\UserOrderModel::ORDER_DELIVERY_ST_NOT) {
                        echo 'disabled="disabled"';
                    }?>
                    >确认发货</button>
					<button type="button" class="btn btn-danger span2" onclick="doConfirmPay()"
                    name="confirmpay" id="confirmpay-btn" value="confrimpay"
                    <?php if ($order['orderState'] == \src\user\model\UserOrderModel::ORDER_ST_CANCELED
                        || $order['payState'] == \src\pay\model\PayModel::PAY_ST_SUCCESS) {
                        echo 'disabled="disabled"';
                    }?>
                    >确认付款</button>
					<button type="button" class="btn span2" name="cancel" onclick="doCancel()" value="cancel"
                    <?php if ($order['orderState'] == \src\user\model\UserOrderModel::ORDER_ST_CANCELED
                        || $order['orderState'] == \src\user\model\UserOrderModel::ORDER_ST_FINISHED
                        || $order['payState'] == \src\pay\model\PayModel::PAY_ST_SUCCESS) { echo 'disabled="disabled"';} ?>
                    >取消订单</button>
					<button type="button" class="btn span2 btn-info" name="sign" onclick="orderSign()" value="sign"
                    <?php if ($order['orderState'] == \src\user\model\UserOrderModel::ORDER_ST_CANCELED
                        || $order['deliveryState'] != \src\user\model\UserOrderModel::ORDER_DELIVERY_ST_ING) {
                        echo 'disabled="disabled"';
                    }?>
                    >订单签收</button>
                    <a class="btn btn-info" href="/admin/Order/orderPrint?orderId=<?php echo $order['orderId'];?>" target="_bank">订单打印 </a>
				</td>
			</tr>
		</tbody>
		</table>
		<!--发货弹窗-->
		<div id="modal-confirmsend" class="modal fade">
			<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title">快递信息</h4>
			</div>
			<div class="modal-body">      	
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-left"> 快递员：</label>
					<div class="col-sm-9">
						<select id="deliveryman" name="express">
							<option value="-1" data-name="">无需快递</option>
                            <?php foreach ($deliverymanList as $man):?> 
					        <option value="<?php echo $man['id']?>" data-name=""><?php echo $man['name']?></option>
                            <?php endforeach?>
			 			</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="confirmsend-btn" name="confirmsend" value="yes">确认发货</button>      	
				<button type="button" class="btn btn-default" data-dismiss="modal">关闭窗口</button>
			</div>
			</div>
		</div>
	</div>
	<!-- END -->
	</form>
	<script>
        $('#confirmsend-btn').click(function(){
            var url = "/admin/Order/confirmDelivery";
            var data = {
                 id:$("#deliveryman option:selected").val(),
                 orderId:"<?php echo $order['orderId']?>"
            };
            $.post(url,data,function(data){
                if(data.code==0) {
                    window.location.href= data.url;
                } else {
                    alert(data.msg);
                    return false;
                }
            },'json');
        });
        function orderSign() {
            if (confirm('确认签收此订单吗？')) {
                var url = "/admin/Order/confirmSign";
                var data = {
                    orderId:"<?php echo $order['orderId']?>"
                };
                $.post(url,data,function(data){
                    if(data.code==0) {
                        window.location.href= data.url;
                    } else {
                        alert(data.msg);
                        return false;
                    }
                },'json');
            }
        }
        function doConfirmPay() {
            if (confirm('确认付款此订单吗？')) {
                var url = "/admin/Order/confirmPayOk";
                var data = {
                    orderId:"<?php echo $order['orderId']?>"
                };
                $.post(url,data,function(data){
                    if(data.code==0) {
                        window.location.href= data.url;
                    } else {
                        alert(data.msg);
                        return false;
                    }
                },'json');
            }
        };
        function doCancel() {
            if (confirm('确认取消此订单吗？')) {
                var url = "/admin/Order/confirmCancel";
                var data = {
                    orderId:"<?php echo $order['orderId']?>"
                };
                $.post(url,data,function(data){
                    if(data.code==0) {
                        window.location.href= data.url;
                    } else {
                        alert(data.msg);
                        return false;
                    }
                },'json');
            }
        };
	</script>
</body>
</html>
