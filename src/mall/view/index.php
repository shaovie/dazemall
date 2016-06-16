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
    <!--添加到购物车-->
    <input id="J-ajaxurl-addCart" type="hidden" value="/api/Cart/autoAdd" />
    <input id="J-ajaxurl-miaoList" type="hidden" value="/api/Goods/getMiaoShaList" />
	
    <div class="wrap">
    <form class="serch-panel-form" action="/mall/Goods/search">
        <div id="J-search-panel" class="search-panel">
            <div class="search-wrap">
                <i class="icon-search"></i>
                <input name="key" type="search" placeholder="请输入商品名称" value="<?php echo $searchKey?>" />
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
                <a href="/mall/Category/index?catId=112000000">
                    <i class="icon"></i><label>生鲜到家</label>
                </a>
            </li>
            <li>
                <a href="/mall/Category/index?catId=113000000">
                    <i class="icon"></i><label>粮油速食</label>
                </a>
            </li>
            <li>
                <a href="/mall/Category/index?catId=114000000">
                    <i class="icon"></i><label>冷冻冷藏</label>
                </a>
            </li>
            <li>
                <a href="/mall/Category/index?catId=115000000">
                    <i class="icon"></i><label>零食饮品</label>
                </a>
            </li>
        </ul>
        <ul class="tj-types2">
            <li>
                <a href="/mall/Category/index?catId=116000000">
                    <i class="icon"></i><label>美容洗护</label>
                </a>
            </li>
            <li>
                <a href="/mall/Category/index?catId=117000000">
                    <i class="icon"></i><label>家庭清洁</label>
                </a>
            </li>
            <li>
                <a href="/mall/Category/index?catId=118000000">
                    <i class="icon"></i><label>日用百货</label>
                </a>
            </li>
            <li>
                <a href="/mall/Category/index?catId=0">
                    <i class="icon"></i><label>全部</label>
                </a>
            </li>
        </ul>
    </section>
    <section class="banner-1212">
    <!-- <a href="/mall/Activity/deliveryRule" class="img-wrap" data-original=""></a> -->
    <?php foreach ($actList as $act):?>
    <a href="/mall/Activity/index?actId=<?php echo $act['id']?>" class="img-wrap" data-original="<?php echo $act['image_url']?>"></a>
    <?php endforeach?>
    </section>
<?php if(!empty($miaoSha['goodsList'])):?>
<section class="tj-section">
    <div class="chr-ms-title">
        <div class="section-title3 title-ms">
            <h3><i class="icon-ms"></i>疯狂秒杀</h3>
        </div>
        <div id="<?php if (true) {echo 'J-miao-nav';}?>" class="miao-time">
            <?php foreach ($miaoSha['titleList'] as $t):?>
            <span <?php if ($t['active'] == 1) { echo 'class="active"';}?> ><?php echo $t['title']?></span>
            <?php endforeach?>
        </div>
    </div>
	<div id="J-miao-list" class="temai-list dib-wrap">
        <?php foreach ($miaoSha['goodsList'] as $goods):?>
        <div class="temai-item dib">
            <a href="/mall/Goods/detail?goodsId=<?php echo $goods['goods_id']?>">
                <div class="img-wrap" style="background-image: url(<?php echo $goods['image_url'];?>)">
                    <?php if ($goods['soldout'] == 1):?>
                    <div class="empty"></div>
                    <?php elseif ($goods['start'] != 1):?>
                    <div class="ready-tip">
                        <p>距离开抢</p>
                        <p class="J-miao-timer" timer="<?php echo $goods['leftTime']?>">10:50:10</p>
                    </div>
                    <?php endif?>
                </div>
                <div class="item-info">
                    <div class="item-title"><?php echo $goods['name'];?></div>
                    <div class="item-price">
                        <span class="price">&yen;<?php echo $goods['sale_price'];?></span>
                        <a class="btn-sm <?php if ($goods['soldout'] == 0 && $goods['start'] == 1) {echo 'J-add-cart';}?>" goods-id="<?php echo $goods['goods_id']?>"></a>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach;?>
    </div>
</section>
<?php endif;?>
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
                    <?php if (!empty($goods['tagName'])):?>
                    <div class="tag-<?php echo $goods['tagColor']?>"><?php echo $goods['tagName']?></div>
                    <?php endif?>
                    <div class="img-wrap" data-original="<?php echo $goods['imageUrl']?>">
                    </div>
                    <div class="item-info">
                        <div class="item-title"><?php echo $goods['name'] ?></div>
                        <div class="item-price">
                            <div class="price-wrap">
                                <label class="price"><i>&yen;</i><b><?php echo $goods['salePrice']?></b></label>
                                <del class="price price-market"><i>&yen;</i><b><?php echo $goods['marketPrice']?></b></del>
                            </div>
                            <label class="btn-sm J-add-cart" goods-id="<?php echo $goods['goodsId']?>"></label>
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
        <li class="gift cart">
        <span class="cart-num"></span>
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
