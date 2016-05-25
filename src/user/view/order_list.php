<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>我的订单</title>
    <?php src\common\JsCssLoader::outCss('modules/order-list/index.less');?>
</head>
<body order-type="<?php
    if (isset($isToPay) && $isToPay) {
        echo "pay-wait";
    } elseif (isset($isToTakeDelivery) && $isToTakeDelivery) {
        echo "sign-wait";
    } elseif (isset($isFinished) && $isFinished) {
        echo "complete";
    }?>">
    <?php if (isset($isToPay) && $isToPay):?>
	<input id="J-ajaxurl-list" type="hidden" value="/user/Order/toPayOrderListNextPage" />
	<input id="J-detail-prefix" type="hidden" value="/mall/Pay/payAgain?showwxpaytitle=1&orderId=" />
    <?php elseif (isset($isToTakeDelivery) && $isToTakeDelivery):?>
	<input id="J-ajaxurl-list" type="hidden" value="/user/Order/toTakeDeliveryOrderListNextPage" />
	<input id="J-detail-prefix" type="hidden" value="/user/Order/orderToTakeDelivery?orderId=" />
    <?php elseif (isset($isFinished) && $isFinished):?>
	<input id="J-ajaxurl-list" type="hidden" value="/user/Order/finishedOrderListNextPage" />
	<input id="J-detail-prefix" type="hidden" value="/user/Order/orderFinished?orderId=" />
    <?php endif?>
	<header>
	<a href="/" class="btn-fir"><i class="icon-fir"></i><label>首页</label></a>
	<a href="/user/Home" class="btn-order"><i class="icon-order"></i><label>我的订单</label></a>
    </header>
    <ul class="order-nav">
        <li <?php if (isset($isToPay) && $isToPay) :?>class="active" <?php endif;?> >
            <a href="/user/Order/toPay">待支付</a></li>
        <li <?php if (isset($isToTakeDelivery) && $isToTakeDelivery) :?>class="active" <?php endif;?> >
            <a href="/user/Order/toTakeDelivery">待收货</a></li>
        <li <?php if (isset($isFinished) && $isFinished) :?> class="active" <?php endif;?> >
            <a href="/user/Order/finished">已完成</a></li>
    </ul>
    <?php if (empty($orderList)):?>
    <ul class="order-list nodata">
        <li>
            <i class="icon"></i>
            <p>您还没有相关订单哦！</p>
            <div class="btn-wrap">
                <a href="/" class="btnl btnl-border">去逛逛 >></a>
            </div>
        </li>
    </ul>
    <?php else:?>
    <div class="order-list">
    <?php foreach ($orderList as $order):?>
        <?php if (isset($isToPay) && $isToPay):?>
        <li class="order-item">
            <a href="/mall/Pay/payAgain?showwxpaytitle=1&orderId=<?php echo $order['orderId']?>">
                <p class="top-info clearfix">
                    <span class="date"><label>下单时间：</label><b><?php echo $order['ctime']?></b></span>
                    <span class="state right">待支付</span>
                </p>
                <div class="order-info dib-wrap">
                    <div class="img-wrap dib" style="background-image: url(<?php echo $order['showImageUrl']?>)"></div>
                    <dl class="dib clearfix">
                        <dt>订单编号</dt>
                        <dd><?php echo $order['orderId']?></dd>
                        <dt>订单金额</dt>
                        <dd><span class="price"><i>&yen;</i><b><?php echo $order['orderAmount']?></b></span></dd>
                        <dt>商品件数</dt>
                        <dd><?php echo $order['goodsNumber']?></dd>
                    </dl>
                    <i class="icon-arrow"></i>
                </div>
				<div class="btn-wrap">
					<button class="btnl">支付<span timer="<?php echo $order['leftTime']?>"></span></button>
				</div>
            </a>
        </li>
        <?php endif;?>

        <?php if (isset($isToTakeDelivery) && $isToTakeDelivery):?>
        <li class="order-item">
            <a href="/user/Order/orderToTakeDelivery?orderId=<?php echo $order['orderId']?>">
                <p class="top-info clearfix">
                    <span class="date"><label>下单时间：</label><b><?php echo $order['ctime']; ?></b></span>
                    <span class="state right"><?php echo $order['deliverfyStateDesc']?></span>
                </p>
                <div class="order-info dib-wrap">
                    <div class="img-wrap dib" style="background-image: url(<?php echo $order['showImageUrl']?>)"></div>
                    <dl class="dib clearfix">
                        <dt>订单编号</dt>
                        <dd><?php echo $order['orderId']?></dd>
                        <dt>订单金额</dt>
                        <dd><span class="price"><i>&yen;</i><b><?php echo $order['orderAmount']?></b></span></dd>
                        <dt>商品件数</dt>
                        <dd><?php echo $order['goodsNumber']?></dd>
                    </dl>
                    <i class="icon-arrow"></i>
                </div>
            </a>
        </li>
        <?php endif;?>

        <?php if (isset($isFinished) && $isFinished):?>
        <li class="order-item">
            <a href="/user/Order/orderFinished?orderId=<?php echo $order['orderId']?>">
                <p class="top-info clearfix">
                    <span class="date"><label>下单时间：</label><b><?php echo $order['ctime']; ?></b></span>
                    <span class="state right <?php if($order['orderState']==2) echo ' cancel'?>"><?php echo $order['orderStateDesc']?></span>
                </p>
                <div class="order-info dib-wrap">
                    <div class="img-wrap dib" style="background-image: url(<?php echo $order['showImageUrl']?>)"></div>
                    <dl class="dib clearfix">
                        <dt>订单编号</dt>
                        <dd><?php echo $order['orderId']?></dd>
                        <dt>订单金额</dt>
                        <dd><span class="price"><i>&yen;</i><b><?php echo $order['orderAmount']?></b></span></dd>
                        <dt>商品件数</dt>
                        <dd><?php echo $order['goodsNumber']?></dd>
                    </dl>
                    <i class="icon-arrow"></i>
                </div>
            </a>
        </li>
        <?php endif;?>
    <?php endforeach;?>
    </div>
    <?php endif?>
    <?php
        src\common\JsCssLoader::outJs('lib/mod.js');
        src\common\JsCssLoader::outJs('order-list/index');
    ?>
    <script type="text/javascript"> require('order-list/index'); </script>
</body>
</html>
