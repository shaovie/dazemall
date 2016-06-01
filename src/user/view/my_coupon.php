<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="format-detection"  content="telephone=no">
    <title>我的优惠券</title>
    <?php src\common\JsCssLoader::outCss('modules/ticket-list/index.less');?>
</head>
<body>
	<header>
	<a href="/" class="btn-fir"><i class="icon-fir"></i><label>首页</label></a>
	<a href="/user/Order" class="btn-order"><i class="icon-order"></i><label>我的订单</label></a>
    </header>
    <ul class="ticket-nav">
        <li <?php if($type==0):?>class="active"<?php endif;?>><a href="/user/Coupon/myCoupon?type=0">未使用</a></li>
        <li <?php if($type==1):?>class="active"<?php endif;?>><a href="/user/Coupon/myCoupon?type=1">已使用</a></li>
        <li <?php if($type==2):?>class="active"<?php endif;?>><a href="/user/Coupon/myCoupon?type=2">已过期</a></li>
    </ul>
    <?php if(!empty($couponList)):?>
    <ul class="ticket-list">
        <?php foreach ($couponList as $coupon):?>
        <li class="<?php echo $coupon['state'] == 1 ? 'used' : ($coupon['end_time'] <= CURRENT_TIME ? 'dated' : '')?>">
            <div class="info">
                <p class="ticket-name"><?php echo $coupon['name']?></p>
                <p class="tip">满<?php echo $coupon['order_amount']?>元可用</p>
                <p class="ticket-date"><?php echo date('Y-m-d H:i', $coupon['begin_time']) .'~' . date('Y-m-d H:i', $coupon['end_time'])?></p>
            </div>
            <div class="money"><small>&yen;</small><b><?php echo $coupon['coupon_amount']?></b></div>
        </li>
        <?php endforeach;?>
    </ul>
    <?php else:?>
    <div class="empty">
        <div class="icon-empty"></div>
        <p>啊哦，您还没有优惠券哦！</p>
    </div>
    <?php endif;?>
    <?php
        src\common\JsCssLoader::outJs('lib/mod.js');
        src\common\JsCssLoader::outJs('ticket-list/index');
    ?>
    <script type="text/javascript">require('ticket-list/index');</script>
</body>
</html>
