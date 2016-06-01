<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>个人中心</title>
    <?php src\common\JsCssLoader::outCss('modules/person/index.less');?>
</head>
<body>
    <section class="person dib-wrap">
        <div class="img-wrap dib" style="background-image: url('<?php echo $user['imageUrl'];?>')"></div>
        <div class="user-info dib">
            <div class="user-name"><?php echo $user['nickname'];?></div>
            <div class="user-des"><?php echo $user['phone'];?></div>
        </div>
    </section>
    <ul class="wealth-list">
        <li>
            <a href="/user/Wallet">
                <p>我的钱包</p>
            </a>
        </li>
        <li>
            <a href="/user/Coupon/myCoupon">
                <p>优惠券</p>
            </a>
        </li>
        <li>
            <a href="#">
                <p>商城积分</p>
            </a>
        </li>
    </ul>
    <section class="order">
        <div class="order-list">
            <a href='/user/Order/toPay'>
                <i class="icon"></i>
                <label>待支付</label>
            </a>
            <a href='/user/Order/toTakeDelivery'>
                <i class="icon"></i>
                <label>待收货</label>
            </a>
            <a href='/user/Order/finished'>
                <i class="icon"></i>
                <label>已完成</label>
            </a>
        </div>
    </section>
    <section class="mys">
        <a class="item-bar" href="/mall/Cart">
            <i class="icon-cart"></i>
            <label>我的购物车</label>
            <b class="icon-arrow"></b>
        </a>
        <a href="/user/Home/address" class="item-bar">
            <i class="icon-addr"></i>
            <label>我的地址</label>
            <b class="icon-arrow"></b>
        </a>
    </section>
    <?php if (false):?>
    <section class="exit-wrap">
    <p style='font-size:13px'>用户ID：<?php echo $user['id']?></p>
    </section>
    <?php endif?>
    <ul class="nav">
        <li class="mall dib">
            <a href="/">
                <i class="icon"></i>
                <label>大泽商城</label>
            </a>
        </li>
        <li class="gift dib">
            <a href="/mall/Cart">
                <i class="icon"></i>
                <label>购物车</label>
            </a>
        </li>
        <li class="mine active dib">
            <a>
                <i class="icon"></i>
                <label>我的</label>
            </a>
        </li>
    </ul>
    <?php
        src\common\JsCssLoader::outJs('lib/mod.js');
        src\common\JsCssLoader::outJs('person/index');
    ?>
    <script type="text/javascript">require('person/index');</script>
</body>
</html>
