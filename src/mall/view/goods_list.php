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
	
	<header>
	<a href="/" class="btn-fir"><i class="icon-fir"></i><label>首页</label></a>
	<a href="/user/Home" class="btn-order"><i class="icon-order"></i><label>我的订单</label></a>
</header>
	<ul class="goods-order">
		<li class="cat-panel">
			<a id="J-cat-btn"><i class="icon-cat"></i>
            <span class="cat-name"><?php echo $catId == $parentCatId ? '全部' : $title;?>
            </span></a>
			<ul id="J-cat-list" class="cat-list">
				<li>
					<a href="/mall/Category/index?catId=<?php echo $parentCatId?>">全部</a>
				</li>
                <?php if (!empty($catList)):?>
                <?php foreach ($catList as $cat):?>
                <li <?php if ($catId == $cat['category_id']) echo 'class="active"';?> >
                <a href="/mall/Category/index?catId=<?php echo $cat['category_id']?>"><?php echo $cat['name'];?></a>
                <?php endforeach?>
                <?php endif?>
			</ul>
		</li>
	</ul>
	<section id="J-goods-list" class="goods-list">
        <?php if (!empty($goodsList)):?>
        <?php foreach ($goodsList as $goods):?>
		<div class="goods">
            <a href="/mall/Goods/detail?goodsId=<?php echo $goods['goodsId']?>">
			<div class="img-wrap" style="background-image: url(<?php echo $goods['imageUrl']?>)"></div>
			<div class="goods-title"><?php echo $goods['name']?></div>
			<div class="clearfix">
				<label class="price"><i>&yen;</i><b><?php echo $goods['salePrice']?></b></label>
				<label class="btn btn-sm right"><?php echo $goods['discount']?>折</label>
			</div>
		</div>
        <?php endforeach?>
        <?php endif?>
	</section>
	<div id="J-loading" class="loading-icon-wrap"><i class="loading-icon"></i></div>
	<!-- <div class="copyright"><i></i></div> -->
    <?php
        src\common\JsCssLoader::outJs('lib/mod.js');
        src\common\JsCssLoader::outJs('goods-list/index');
    ?>
    <script type="text/javascript">require('goods-list/index');</script>
</body>
</html>
