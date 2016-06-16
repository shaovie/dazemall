<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<title><?php echo $title?></title>
    <?php src\common\JsCssLoader::outCss('modules/goods-list/index.less');?>
</head>
<body>
	<!--列表url-->
	<input id="J-ajax-url" type="hidden" value="<?php echo $ajaxUrl?>" />
    <!--初始化购物车url，返回number-->
    <input id="J-ajaxurl-initCart" type="hidden" value="/api/Cart/getCartAmount" />
    <!--添加到购物车-->
    <input id="J-ajaxurl-addCart" type="hidden" value="/api/Cart/autoAdd" />
	
	<header>
	<a href="/" class="btn-fir"><i class="icon-fir"></i><label>首页</label></a>
	<a href="/user/Order" class="btn-order"><i class="icon-order"></i><label>我的订单</label></a>
    </header>
    <?php if (!empty($act['image_urls'])): ?>
	<div id="banner" class='swipe'>
		<div class="swipe-wrap">
        <?php foreach ($act['image_urls'] as $img):?>
			<div>
			      <a href="#">
			      	<div class="img-wrap" tjz-bgimg="<?php echo $img?>"></div>
				  </a>
			</div>
        <?php endforeach?>
		</div>
		<div id="bannerPager"><span id="page"></span></div>
	</div>
    <?php endif?>
	<section id="J-goods-list" class="goods-list">
        <?php if (!empty($goodsList)):?>
        <?php foreach ($goodsList as $goods):?>
		<div class="goods">
            <a href="/mall/Goods/detail?goodsId=<?php echo $goods['goodsId']?>">
            <?php if (!empty($goods['tagName'])):?>
            <div class="tag-<?php echo $goods['tagColor']?>"><?php echo $goods['tagName']?></div>
            <?php endif?>
			<div class="img-wrap" style="background-image: url(<?php echo $goods['imageUrl']?>)"></div>
			<div class="goods-title"><?php echo $goods['name']?></div>
			<div class="clearfix">
                <div class="price-wrap">
				    <label class="price"><i>&yen;</i><b><?php echo $goods['salePrice']?></b></label>
                    <del class="price price-market"><i>&yen;</i><b><?php echo $goods['marketPrice']?></b></del>
                </div>
                <label class="btn-sm J-add-cart" goods-id="<?php echo $goods['goodsId']?>"></label>
			</div>
		</div>
        <?php endforeach?>
        <?php endif?>
	</section>
    <a href="/mall/Cart" class="cart"><i></i></a>
	<div id="J-loading" class="loading-icon-wrap"><i class="loading-icon"></i></div>
	<!-- <div class="copyright"><i></i></div> -->
    <?php
        src\common\JsCssLoader::outJs('lib/mod.js');
        src\common\JsCssLoader::outJs('goods-list/index');
    ?>
    <script type="text/javascript">require('goods-list/index');</script>
</body>
</html>
