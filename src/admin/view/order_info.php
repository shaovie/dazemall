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
	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
		border-top: 0px;
	}
	</style>
</head>
<body class="no-skin">
	<h3 class="header smaller lighter blue">订单基本信息</h3>
	<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
		<table class="table">
		<tbody>
			<tr>
				<th style="width:150px"><label for="">订单编号:</label></th>
				<td><?php echo $info['order_id']?></td>
				<th style="width:150px"><label for="">订单状态:</label></th>
				<td><span class="label label-warning"><?php echo $info['stateDesc']?></span></td>
			</tr>
			<tr>
				<th><label for="">下单时间:</label></th>
				<td><?php echo date('Y-m-d H:i:s', $info['ctime'])?></td>
				<th><label for="">总金额:</label></th>
				<td><?php echo $info['totalAmount']?></td>
			</tr>
			<tr>
				<th><label for="">支付方式:</label></th>
				<td><?php echo $info['paymentDesc']?></td>
				<th><label for="">配送方式:</label></th>
				<td>test</td>
			</tr>
		</tbody>
		</table>
		<h3 class="header smaller lighter blue">收货人信息</h3>
		<table class="table ">
		<tbody>
			<tr>
				<th style="width:150px"><label for="">收货人姓名:</label></th>
				<td ><?php echo $info['re_name']?></td>
				<th style="width:300px"><label for="">收货地址:</label></th>
				<td><?php echo $info['addr']?></td>
			</tr>
			<tr>
				<th style="width:150px"><label for="">收货人电话:</label></th>
				<td><?php echo $info['re_phone']?></td>
				<th><label for="">订单备注:</label></th>
				<td><textarea readonly="readonly" style="width:30px;border: none;" type="text"></textarea>
				</td>
			</tr>
		</tbody>
		</table>
		
		<table class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th style="width:100px;">商品编号</th>
				<th>商品名称</th>
				<th>商品SKU</th>
				<th style="color:red;">成交价</th>
				<th>数量</th>				
			</tr>
		</thead>
		<tbody>
        <?php foreach ($orderGoods as $goods):?>
			<tr>
				<td><?php echo $goods['goods_id']?></td>
				<td><?php echo $goods['name']?></td>
                <td>
                    <div><?php echo $goods['sku_attr']?></div>
                    <div><?php echo $goods['sku_value']?></div>
                 </td>
				<td style="color:red;font-weight:bold;"><?php echo $goods['price']?></td>
				<td><?php echo $goods['amount']?></td>
			</tr>
        <?php endforeach?>
		</tbody>
		</table>
		<table class="table">
		<tbody>
			<tr>
				<th style="width:50px"></th>
				<td>
					<button type="button" class="btn btn-primary span2" name="confirmsend" data-toggle="modal" data-target="#modal-confirmsend" value="confirmsend">确认发货</button>
					<button type="submit" class="btn btn-danger span2" onclick="return confirm(&#39;确认付款此订单吗？&#39;); return false;" name="confrimpay" value="confrimpay">确认付款</button>
					<button type="submit" class="btn span2" name="close" onclick="return confirm(&#39;永久关闭此订单吗？&#39;); return false;" value="close">关闭订单</button>
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
					<label class="col-sm-2 control-label no-padding-left"> 快递公司：</label>
					<div class="col-sm-9">
						<select name="express">
							<option value="-1" data-name="">无需快递</option>
					        <option value="aae" data-name="aae全球专递">aae全球专递</option>
			 				<option value="huitongkuaidi" data-name="汇通快运">汇通快运</option>
			 			</select>
					</div>
				</div>
      	
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-left"> 快递单号：</label>
					<div class="col-sm-9">
						<input type="text" name="expresssn" class="span5">
					</div>
				</div>      	
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary" name="confirmsend" value="yes">确认发货</button>      	
				<button type="button" class="btn btn-default" data-dismiss="modal">关闭窗口</button>
			</div>
			</div>
		</div>
	</div>
	<!-- END -->
	</form>
</body>
</html>
