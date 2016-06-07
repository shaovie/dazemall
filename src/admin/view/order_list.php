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
<h3 class="header smaller lighter blue">订单管理
<span style="margin-left:30px;color:#666;font-size:18px;">订单总数：<?php echo $totalOrderNum?></span>
<?php if (isset($totalSoldAmount)):?>
<span style="margin-left:30px;color:#666;font-size:18px;">订单总金额(含邮费)：<?php echo $totalSoldAmount?></span>
<?php endif?>
<span style="margin-left:20px;color:red;font-size:12px;"><?php if (!empty($error)){echo $error;}?></span>
<span class="refresh">刷新</span></h3>
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
                <td>
                <input type="submit" name="submit" value=" 查 询 " class="btn btn-sm btn-primary">
                <input type="button" name="undelivery" value="未发货" class="btn btn-sm btn-warning" onclick="searchUndelivery();">
                <input type="button" name="undelivery" value="今日订单" class="btn btn-sm btn-info" onclick="todayOrder();">
                </td>
			</tr>
		</tbody>
	</table>
	</form>			
	<table class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th style="width:120px;text-align:center;">订单编号</th>
				<th style="width:100px;text-align:center;">下单用户编号</th>
				<th style="width:100px;text-align:center;">收货人姓名</th>
				<th style="width:90px;text-align:center;">收货人电话</th>
				<th style="width:150px;text-align:center;">支付</th>
				<th style="width:60px;text-align:center;">运费</th>
				<th style="width:80px;text-align:center;">优惠金额</th>
				<th style="width:100px;text-align:center;">总价</th>
				<th style="width:120px;text-align:center;">状态</th>
				<th style="width:120px;text-align:center;">发货</th>
				<th style="width:250px;text-align:center;">时间</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
            <?php foreach ($orderList as $order):?>
			<tr>
				<td style="text-align:center;vertical-align:middle;"><?php echo $order['order_id']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $order['user_id']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $order['re_name']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $order['re_phone']?></td>
				<td style="text-align:left;vertical-align:middle;">
                <div><?php if ($order['olPayAmount'] > 0.0001){echo $order['olPayTypeDesc'] . '支付：' . $order['olPayAmount'];}?></div>
                <div><?php echo '余额支付：' . $order['ac_pay_amount']?></div>
                </td>
                <td style="text-align:center;vertical-align:middle;"><?php echo $order['postage']?></td>
                <td style="text-align:center;vertical-align:middle;"><?php echo $order['coupon_pay_amount']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $order['order_amount']?></td>
                <td style="text-align:left;vertical-align:middle;">
                    <div>支付：<span ><?php echo $order['payStateDesc']?></span> </div>
                    <div>订单：<span ><?php echo $order['orderStateDesc']?></span> </div>
                </td>
                <td style="text-align:center;vertical-align:middle;">
                    <?php if ($order['delivery_state'] == \src\user\model\UserOrderModel::ORDER_DELIVERY_ST_NOT):?>
                    <div><span class="label label-warning"><?php echo $order['deliveryStateDesc']?></span></div>
                    <?php else:?>
                    <?php echo $order['deliveryStateDesc']?>
                    <div><?php if (!empty($order['deliverymanName'])) {echo "快递员：" . $order['deliverymanName'];}?></div>
                    <?php endif?>
                </td>
				<td style="text-align:left;vertical-align:middle;">
                    <div>支付时间：<?php echo empty($order['pay_time']) ? '' : date('Y-m-d H:i:s', $order['pay_time'])?></div>
                    <div>下单时间：<?php echo date('Y-m-d H:i:s', $order['ctime'])?></div>
                </td>
				<td style="text-align:left;vertical-align:middle;">
                    <a class="btn btn-xs btn-info" target="_blank" href="/admin/Order/info?orderId=<?php echo $order['order_id']?>"><i class="icon-edit"></i>查看详情</a>
                    &nbsp;<a class="btn btn-xs btn-info" href="/admin/Order/orderPrint?orderId=<?php echo $order['order_id'];?>" target="_bank">订单打印 </a>
                </td>
			</tr>
            <?php endforeach?>
		</tbody>
	</table>
   <!-- 右下角弹窗 -->
    <div id="pop">
       <div id="popHead">
          <a id="popClose" title="关闭">关闭</a>
          <span>温馨提示</span>
       </div>
       <div id="popContent">
          <dl>
             <dt id="popTitle">有新订单啦</dt>
             <dd id="popIntro">刚刚有人下单啦，快去看看!</dd>
          </dl>
          <p id="popMore"><a href="/admin/Order/info" target="_blank">查看 »</a></p>
       </div>
    </div>
    <?php echo $pageHtml;?>
    <!--E-->
    <script>
        $(document).ready(function(){
           $('#beginTime,#endTime').datetimepicker({
              format: "yyyy-mm-dd hh:ii:00",
              minView: "0",
              //pickerPosition: "top-right",
              autoclose: true
            });
        });
      $(function(){
         function showDiv(orderId) {
             $('#popMore a').attr('href', '/admin/Order/info?orderId=' + orderId);
             $('#pop').slideDown(1000).delay(4000).fadeOut(200);
         }
         $('#popClose').click(function(){
             $('#pop').hide();
         });
         function request() {
            var url = '/admin/Order/newOrder';
            $.get(url, function(data){
               if (data) {
                 showDiv(data);
                 $('#audio')[0].play();
               }
           });
         };
         window.setInterval(request,6000); 
    });
    function searchUndelivery() {
        var url = '/admin/Order/search?deliveryState=0';
        window.location.href = url;
    }
    function todayOrder() {
        var url = '/admin/Order/search?today=1';
        window.location.href = url;
    }
    </script>
    <audio id="audio"><source src="/asset/audio/notify.mp3" type="audio/mpeg"></audio>
    </body>
</html>
