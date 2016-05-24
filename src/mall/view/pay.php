<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<title>支付</title>
    <?php src\common\JsCssLoader::outCss('modules/pay-new/index.less');?>
</head>
<body>
<form id="J-pay-form" action="<?php echo $action?>" method="post" enctype="application/x-www-form-urlencoded">
	<?php if (empty($address)):?>
	<section id="J-address" class="address no-address">
		<input type="hidden" id="J-addr-id" class="address no-address" name="address_id" value=""/>
		<a>
			<i class="icon-addr"></i>
			<ul>
				<li class="addr-user">
					<span class="user-name"></span>
					<span class="tel"></span>
				</li>
				<li class="addr-addr"></li>
			</ul>
			<ul class="no-addr">
				<li>请输入您的收货地址</li>
			</ul>
			<i class="icon-arrow"></i>
		</a>
	<?php else:?>
		<section id="J-address" class="address">
	<input type="hidden" id="J-addr-id" name="address_id" value="<?php echo $address['id'];?>"/>
	<a>
		<i class="icon-addr"></i>
		<ul>
			<li class="addr-user">
				<span class="user-name"><?php echo $address['re_name'];?></span>
				<span class="tel"><?php echo $address['re_phone'];?></span>
			</li>
			<li class="addr-addr"><?php echo $address['fullAddr'];?></li>
		</ul>
		<ul class="no-addr">
			<li>请输入您的收货地址</li>
		</ul>
		<i class="icon-arrow"></i>
	</a>
	<?php endif ?>
</section>
<section class="cart-section">
    <?php if (!empty($goodsInfo)):?>
    <input type="hidden" name="skuAttr" value="<?php echo $goodsInfo['skuAttr']; ?>"/>
    <input type="hidden" name="skuValue" value="<?php echo $goodsInfo['skuValue']; ?>"/>
    <input type="hidden" name="amount" value="<?php echo $goodsInfo['amount']; ?>"/>
    <?php endif?>
	<ul class="cart-list">
		<?php foreach($goodsList as $goods): ?>
		<li>
			<input type="hidden" name="ids[]" value="<?php echo $goods['id']; ?>"/>
			<div class="img-wrap" style="background-image:url(<?php echo $goods['imageUrl'];?>)"></div>
			<div class="goods-info">
				<p class="goods-title"><?php echo $goods['name'];?></p>
				<div class="goods-attr"><?php echo $goods['sku'] ?></div>
			</div>
			<div class="price-wrap">
				<span class="price"><i>&yen;</i><b><?php echo $goods['salePrice']?></b></span>
				<div class="goods-amount">x<?php echo $goods['amount']; ?></div>
			</div>
		</li>
		<?php endforeach ?>
	</ul>
</section>
<section class="goods-trans">
	<ul>
		<li class="clearfix">
			<label>运费<?php if ($postage > 0.001) {echo '（满'.$freePostage.'包邮）';}?></label>
			<span class="price right"><i>&yen;</i><b><?php echo $postage;?></b></span>
		</li>
		<li class="clearfix">
			<label>合计</label>
			<span class="price price-total right"><i>&yen;</i><b><?php echo $orderAmount?></b></span>
		</li>
	</ul>
</section>
<ul class="money-list">
	<li id="J-money-last">
		<label>余额</label>
		<div class="col-r">
			<span class="price"><i>&yen;</i><b><?php echo number_format($cash, 2, '.', ''); ?></b></span>
			<i class="icon-radio"></i>
		</div>
	</li>
</ul>
<section id="J-ticket-section" class="ticket-section clearfix
    <?php if (!empty($coupon)) echo 'has-ticket';?>">
	<label>使用优惠券</label>
	<?php if(!empty($coupon)):?>
	<div class="ticket-money right">
		<span class="price"><i>&yen;</i><b id="J-ticket-price"><?php echo $coupon['coupon_amount']?></b></span>
		<span class="ticket-num"><b><?php echo $avalidCouponAmount?></b>张优惠券可用</span>
		<i class="icon-arrow"></i>
	</div>
	<?php endif;?>
	<div class="no-ticket right">暂无优惠券可用</div>
</section>
<section class="money-calculate">
	<div class="calculate">
		<span class="price"><i>&yen;</i><b><?php echo number_format($orderAmount-$postage, 2, '.', ''); ?></b></span>
		<span>+</span>
		<span class="price"><i>&yen;</i><b><?php echo $postage; ?></b></span>运费
		<span id="J-minus-ticket">
			<span>-</span>
			<span class="price"><i>&yen;</i><b></b></span>优惠券
		</span>
		<span id="J-minus-content">
			<span>-</span>
			<span class="price"><i>&yen;</i><b><?php echo $cash?></b></span>余额
		</span>
	</div>
	<div class="result">
    <span >需付</span>：
    <span class="price"><i>&yen;</i>
    <b ><?php echo $orderAmount;?></b></span>
    </div>
</section>
<div class="error-tip">为避免订单失效，建议您在<b>30分钟</b>内完成支付</div>
<footer>
    <a class="J-pay-btn btnl btnl-yue" type="0">余额安全支付</a>
    <a class="J-pay-btn btnl btnl-wx" type="2">微信安全支付</a>
</footer>
<input id="J-use-ticket" name="coupon_id" type="hidden" value="<?php echo !empty($coupon) ? $coupon[0]['coupon_id'] : '';?>" />
<input id="J-total-val" type="hidden" value="<?php echo $orderAmount; ?>" />
<input id="J-trans-val" type="hidden" value="0.00"/>
<input id="J-last-val" type="hidden" value="<?php echo $cash; ?>"/>
<input id="J-use-last" name="is_cash" type="hidden" value="0" />
<input id="J-pay-type" name="pay_type" type="hidden" value="2" />
<input id="J-need-pay" name="total_price" type="hidden" value="<?php echo $orderAmount; ?>" />
<input id="J-order-id" name="orderId" type="hidden" value="<?php echo $orderId?>" />
    <?php
        src\common\JsCssLoader::outJs('lib/mod.js');
        src\common\JsCssLoader::outJs('pay-new/index');
    ?>
    <script type="text/javascript">require('pay-new/index');</script>
</body>
</html>
