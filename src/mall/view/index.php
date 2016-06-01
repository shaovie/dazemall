<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection"  content="telephone=no">
    <title>大泽商城</title>
    <?php include CONFIG_PATH . '/wx_share.php';?>
    <?php src\common\JsCssLoader::outCss('modules/index-new/index.less');?>
</head>
<body>
	<input type="hidden" id="J-ajaxurl-initCart" value="/api/Cart/getCartAmount" />
	
    <div class="wrap">
    <form class="serch-panel-form" action="/mall/Goods/search">
        <div id="J-search-panel" class="search-panel">
            <div class="search-wrap">
                <i class="icon-search"></i>
                <input name="key" type="search" placeholder="请输入商品名称" value="西红柿" />
                <i class="icon-clear"></i>
            </div>
            <button type="submit">搜索</button>
        </div>
    </form>
	<section class="tj-section">
        <div id="banner" class='swipe'>
            <div class="swipe-wrap">
            <?php foreach ($bannerList as $banner):?>
                <div>
                      <a href="<?php echo (empty($banner['link']) ? '#' : $banner['link'])?>" >
                        <div class="img-wrap" tjz-bgimg="<?php echo $banner['imageUrl']?>"></div>
                      </a>
                </div>
            <?php endforeach?>
            </div>
            <div id="bannerPager"><span id="page"></span></div>
        </div>
        <!-- <div class="festival-line"></div> -->
        <ul class="tj-types">
            <li>
                <a href="/mall/Category/index?catId=100000000">
                    <i class="icon"></i><label>生鲜到家</label>
                </a>
            </li>
            <li>
                <a href="/mall/Category/index?catId=101000000">
                    <i class="icon"></i><label>鲜果到家</label>
                </a>
            </li>
            <li>
                <a href="/mall/Category/index?catId=102000000">
                    <i class="icon"></i><label>零食百货</label>
                </a>
            </li>
            <li>
                <a href="/mall/Category/index?catId=103000000">
                    <i class="icon"></i><label>海鲜肉类</label>
                </a>
            </li>
        </ul>
        <ul class="tj-types2">
            <li>
                <a href="/mall/Category/index?catId=104000000">
                    <i class="icon"></i><label>牛奶饮品</label>
                </a>
            </li>
            <li>
                <a href="/mall/Category/index?catId=105000000">
                    <i class="icon"></i><label>粮油副食</label>
                </a>
            </li>
            <li>
                <a href="/mall/Category/index?catId=106000000">
                    <i class="icon"></i><label>清洁护理</label>
                </a>
            </li>
            <li>
                <a href="/mall/Category/index?catId=0">
                    <i class="icon"></i><label>全部</label>
                </a>
            </li>
        </ul>
    </section>
    <?php if (!empty($actList)):?>
    <section class="banner-1212">
    <?php foreach ($actList as $act):?>
    <a href="/mall/Activity/index?actId=<?php echo $act['id']?>" class="img-wrap" data-original="<?php echo $act['image_url']?>"></a>
    <?php endforeach?>
    </section>
    <?php endif?>
    <?php if (!empty($goodsModuleList)):?>
    <?php foreach ($goodsModuleList as $module):?>
    <section class="tj-section">
        <div class="section-title3">
            <h3><?php echo $module['title']?></h3>
        </div>
        <div class="temai-list dib-wrap">
        <?php if (!empty($module['goodsList'])):?>
        <?php foreach ($module['goodsList'] as $goods):?>
            <div class="temai-item dib">
                <a href="/mall/Goods/detail?goodsId=<?php echo $goods['goodsId']?>">
                    <div class="img-wrap" style="background-image: url(<?php echo $goods['imageUrl'] ?>)">
                    </div>
                    <div class="item-info">
                        <div class="item-title"><?php echo $goods['name'] ?></div>
                        <div class="item-price">
                            <span class="price">&yen; <?php echo $goods['salePrice'] ?></span>
                            <?php if (true && (float)$goods['discount'] > 0.00):?>
                            <label class="btn btn-sm"><?php echo $goods['discount']?>折</label>
                            <?php endif?>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach?>
        <?php endif?>
        </div>
    </section>
    <?php endforeach?>
    <?php endif?>
    <a href="/mall/Cart" class="cart"><i></i></a>
    <ul class="nav">
        <li class="mall active">
            <a>
                <i class="icon"></i>
                <label>大泽商城</label>
            </a>
        </li>
        <li class="gift">
            <a href="/mall/Cart">
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
	</div>

    <?php
        src\common\JsCssLoader::outJs('lib/mod.js');
        src\common\JsCssLoader::outJs('index-new/index');
    ?>
    <script type="text/javascript">require('index-new/index');</script>

    <?php 
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $rnd = mt_rand(1, 2147483647);
        $url = "http://c.cnzz.com/wapstat.php?siteid=1259357091&r=$referer&rnd=$rnd";
        echo '<img src="' . $url . '" width="0" height="0"/>';
    ?>
</body>
</html>
