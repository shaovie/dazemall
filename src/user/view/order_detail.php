<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>我的订单</title>
    <?php src\common\JsCssLoader::outCss('modules/order-wait-sign/index.less');?>
</head>
<body>
	<header>
	<a href="/" class="btn-fir"><i class="icon-fir"></i><label>首页</label></a>
	<a href="/user/Home" class="btn-order"><i class="icon-order"></i><label>我的订单</label></a>
    </header>
    <section class="order-info">
        <p>订单编号：<?php echo $order['orderId']?></p>
        <p class="date"><span>下单时间：<?php echo date('Y-m-d H:i:s', $order['ctime']);?></span></p>
    </section>
    <section class="address">
        <a>
            <i class="icon-addr"></i>
            <ul>
                <li class="addr-user">
                    <span class="user-name"><?php echo $order['re_name'] ?></span>
                    <span class="tel"><?php echo $order['re_phone']?></span>
                </li>
                <li class="addr-addr"><?php echo $order['fullAddr']?></li>
            </ul>
        </a>
    </section>

    <div class="goods-container">
        <ul class="goods-list">
            <?php foreach ($order['goodsList'] as $goods):?>
            <li>
                <a href="/mall/Goods/detail?goodsId=<?php echo $goods['goods_id'];?>">
                <div class="img-wrap" style="background-image:url(<?php echo $goods['img']?>)"></div>
                <div class="goods-info">
                    <p class="goods-title"><?php echo $goods['name']?></p>
                    <div class="goods-attr"><?php echo $goods['sku_attr'] . '：' . $goods['sku_value']?> </div>
                </div>
                <div class="price-wrap">
                    <span class="price"><i>&yen;</i><b><?php echo $goods['price']?></b></span>
                    <div class="goods-amount">x<?php echo $goods['amount']?></div>
                </div>
                </a>
            </li>
            <?php endforeach?>
            <li class="goods-state dib-wrap">
            <?php if ($order['order_state'] == 2)/*已取消*/:?>
                <span class="state dib">已取消</span>
            <?php else:?>
                <span class="state dib"><?php echo $order['deliveryStateDesc']?></span>
                <?php if ($order['delivery_state'] == 2)/*发货中*/:?>
                    <div class="btn-wrap dib">
                        <a class="j-ajax btnl" ajax-url="/user/yOrder/confirmTakeDelivery" ajax-data='{"orderId":"<?php echo $order['orderId']?>"}' ajax-type="post">确认收货</a>
                    </div>
                <?php endif?>
            <?php endif?>
            </li>
        </ul>
    </div>
    <?php if ($order['order_state'] != 2)/*已取消*/:?>
    <section class="goods-trans">
        <ul>
            <li class="clearfix">
                <label>运费</label>
                <span class="price right"><i>&yen;</i><b><?php echo number_format($order['postage'], 2, '.', '')?></b></span>
            </li>
            <?php if(!empty((float)$order['couponPayment'])):?>
            <li class="clearfix">
                <label>优惠券</label>
                <span class="price right"><i>&yen;</i><b><?php echo $order['couponPayment'];?></b></span>
            </li>
            <?php endif;?>
            <li class="clearfix">
                <label>合计</label>
                <span class="price price-total right"><i>&yen;</i><b><?php echo $order['orderAmount']?></b></span>
            </li>
        </ul>
    </section>
    <section class="money-calculate">
        <div class="calculate">
            <span class="price"><i>&yen;</i><b><?php echo $order['totalPrice']?></b></span>
            <span>+</span>
            <span class="price"><i>&yen;</i><b><?php echo $order['postage']?></b></span>运费
            <span>
                <span>-</span>
                <span class="price"><i>&yen;</i><b><?php echo $order['acPayAmount']?></b></span>余额
            </span>
            <?php if(!empty((float)$order['couponPayment'])):?>
            <span>
                <span>-</span>
                <span class="price"><i>&yen;</i><b><?php echo $order['couponPayment']?></b></span>优惠券
            </span>
            <?php endif;?>
        </div>
        <div class="result"><?php echo $order['payTypeDesc']?>已支付:<span class="price"><i>&yen;</i><b><?php echo $order['payAmount']?></b></span></div>
    </section>
    <?php endif?>
    <?php
        src\common\JsCssLoader::outJs('lib/mod.js');
        src\common\JsCssLoader::outJs('order-wait-sign/index');
    ?>
    <script type="text/javascript">require('order-wait-sign/index'); </script>
</body>
</html>
