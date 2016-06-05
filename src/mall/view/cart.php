<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<title>购物车</title>
    <?php src\common\JsCssLoader::outCss('modules/cart/index.less');?>
</head>
<body>
	<input type="hidden" id="J-ajaxurl-initCart" value="/api/Cart/getCartAmount" />
	<!--购物车产品数量更新-->
	<input id="J-ajaxurl-updateNum" type="hidden" value="/api/Cart/modifyAmount" />
<?php if (empty($cartList)):?>
	<div class="cart-empty">
		<div class="icon-empty"></div>
		<p>啊哦，购物车是空的！</p>
		<div class="btn-wrap">
			<a href="/" class="btnl btnl-border">去逛逛 >></a>
		</div>
	</div>
    <ul class="nav">
        <li class="mall">
            <a href="/">
                <i class="icon"></i>
                <label>大泽商城</label>
            </a>
        </li>
        <li class="gift active">
            <a href="">
                <i class="icon"></i>
                <label>购物车</label>
            </a>
        </li>
        <li class="mine">
            <a href="/user/Home">
                <i class="icon"></i>
                <label>我的</label>
            </a>
        </li>
    </ul>
<?php else:?>
	<form action="/mall/Pay/cartPay" method='post'>
		<section class="cart-section">
			<div class="cart-title">大泽商城</div>
			<ul class="cart-list">
            <?php foreach ($cartList as $cart):?>
				<li>
					<input class="J-price" type="hidden" value="<?php echo $cart['salePrice']?>"/>
					<input type="hidden" class="J-id" value="<?php echo $cart['id']?>">
					<input type="hidden" class="J-checkbox-val" name="cartId[]"  value="<?php echo $cart['id']?>">
					<div class="radio"><i class="icon-radio"></i></div>
					<a href="/mall/Goods/detail?goodsId=<?php echo $cart['goodsId']?>">
						<div class="img-content">
                        <?php if (false):?>
							<i class="icon-tm"></i>
                        <?php endif?>
							<div class="img-wrap" style="background-image:url(<?php echo $cart['imageUrl']?>)">
                            <?php if (false):?>
								<div class="no-store">库存紧张</div>
                            <?php elseif ($cart['down'] == 1):?>
								<div class="no-store">已下架</div>
                            <?php elseif (false):?>
								<div class="tm-timer">剩余<span class="J-tm-timer" timer="1000000">21:21:21</span></div>
                            <?php endif?>
							</div>
                        <?php if (false):?>
							<p class="tm-tip">*即将恢复原价</p>
                        <?php endif?>
						</div>
					</a>
					<div class="goods-info">
						<p class="goods-title"><?php echo $cart['name']?></p>
                        <?php if (!empty($cart['sku'])):?>
						<div class="goods-attr"><?php echo $cart['sku']?></div>
                        <?php endif?>
						<div class="clearfix">
							<ul class="J-amount-bar amount-bar dib-wrap" max="100" min="1">
	<li class="btn-minus dib"><i class="icon"></i></li>
	<li class="amount-val dib"><?php echo $cart['amount']?></li>
	<li class="btn-add dib"><i class="icon"></i></li>
</ul>
							<span class="price right"><i>&yen;</i><b><?php echo $cart['totalPrice']?></b></span>
						</div>
					</div>
					<div class="btn-del" ajax-url="/api/Cart/del" ajax-type="post" ajax-data='{"id": <?php echo $cart['id']?>}'></div>
				</li>
            <?php endforeach?>
			</ul>
		</section>
		<footer>
			<div id="J-sel-all" class="radio"><i class="icon-radio"></i><span>全选</span></div>
			<div class="pay-r dib-wrap">
				<span class="price dib"><label>实付:</label><i>&yen;</i><b id="J-total-price"><?php echo $allTotalPrice?></b></span>
				<button id="J-buy" class="btnl dib">结算（<span id="J-amount"></span>）</button>
			</div>
		</footer>
	</form>
    <ul class="nav">
        <li class="mall">
            <a href="/">
                <i class="icon"></i>
                <label>大泽商城</label>
            </a>
        </li>
        <li class="gift active cart">
        <span class="cart-num"></span>
            <a href="">
                <i class="icon"></i>
                <label>购物车</label>
            </a>
        </li>
        <li class="mine">
            <a href="/user/Home">
                <i class="icon"></i>
                <label>我的</label>
            </a>
        </li>
    </ul>
<?php endif?>
    <?php
        src\common\JsCssLoader::outJs('lib/mod.js');
        src\common\JsCssLoader::outJs('cart/index');
    ?>
    <script type="text/javascript">require('cart/index');</script>
</body>
</html>
