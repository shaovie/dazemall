<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<title>商品搜索</title>
    <?php src\common\JsCssLoader::outCss('modules/goods-list/index.less');?>
</head>
<body>
	<!--列表url-->
	<input id="J-ajax-url" type="hidden" value="/api/Goods/search" />
    <!--初始化购物车url，返回number-->
    <input id="J-ajaxurl-initCart" type="hidden" value="/api/Cart/getCartAmount" />
    <!--添加到购物车-->
    <input id="J-ajaxurl-addCart" type="hidden" value="/api/Cart/autoAdd" />
	
	<header>
	<a href="/" class="btn-fir"><i class="icon-fir"></i><label>首页</label></a>
	<a href="/user/Order" class="btn-order"><i class="icon-order"></i><label>我的订单</label></a>
    </header>
    <form class="serch-panel-form" action="/mall/Goods/search">
        <div id="J-search-panel" class="search-panel">
            <div class="search-wrap">
                <i class="icon-search"></i>
                <input name="key" type="search" placeholder="请输入商品名称" value="<?php echo $key;?>" />
                <i class="icon-clear"></i>
            </div>
            <button type="submit">搜索</button>
        </div>
    </form>
    <?php if (!empty($goodsList)):?>
	<section id="J-goods-list" class="goods-list">
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
	</section>
    <?php else:?>
    <div class="page-empty">
        <p>没有找到相关的商品哦~</p>
        <div class="btn-wrap">
            <a href="/" class="btnl btnl-border">去逛逛 >></a>
        </div>
    </div>
    <?php endif?>
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
