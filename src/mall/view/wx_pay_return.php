<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<title>支付完成</title>
    <?php src\common\JsCssLoader::outCss('modules/pay-suc/index.less');?>
</head>
<body>
	<div class="console"></div>
	<section class="success"><i class="icon"></i><?php echo $title?></section>
	<section class="order">
		<div class="price">&yen; <?php echo $payAmount?></div>
		<dl class="clearfix">
			<dt>订单号</dt>
			<dd><?php echo $orderId?></dd>
			<dt>交易方式</dt>
			<dd>微信支付</dd>
		</dl>
	</section>
	<div class="btnl-wrap">
		<a href="/user/Order" class="btnl btnl-wx">查看订单状态</a>
		<a href="/"  class="btnl">继续逛逛 >></a>
	</div>
    <?php
        src\common\JsCssLoader::outJs('lib/mod.js');
        src\common\JsCssLoader::outJs('pay-suc/index');
    ?>
<script type="text/javascript">require('pay-suc/index'); </script>
</body>
</html>
