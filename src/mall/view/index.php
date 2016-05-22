<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection"  content="telephone=no">
    <title>大泽商城</title>
    <?php src\common\JsCssLoader::outCss('modules/index-new/index.less');?>
</head>
<body>
	<input type="hidden" id="J-ajaxurl-initCart" value="/api/Cart/getCartAmount" />
	
    <div class="wrap">
	<section class="tj-section">
        <div id="banner" class='swipe'>
            <div class="swipe-wrap">
            <?php foreach ($bannerList as $banner):?>
                <div>
                      <a href="<?php echo $banner['link']?>" >
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
                <a>
                    <i class="icon"></i><label>食品</label>
                </a>
            </li>
            <li>
                <a>
                    <i class="icon"></i><label>美妆</label>
                </a>
            </li>
            <li>
                <a>
                    <i class="icon"></i><label>保健</label>
                </a>
            </li>
            <li>
                <a>
                    <i class="icon"></i><label>母婴</label>
                </a>
            </li>
            <li>
                <a>
                    <i class="icon"></i><label>其它</label>
                </a>
            </li>
            <!-- <li>
                <a>
                    <i class="icon"></i><label>居家</label>
                </a>
            </li> -->
        </ul>
    </section>
    <section class="tj-section">
        <div class="chr-ms-title">
            <div class="section-title3 title-ms">
                <h3><i class="icon-ms"></i>疯狂秒杀</h3>
            </div>
            <div id="J-miao-nav" class="miao-time">
                <span class="active">10:00</span>
                <span>17:00</span>
                <span>21:00</span>
            </div>
        </div>
        <ul id="J-miao-list" class="banner-list">
            <li>
                <div class="img-wrap" data-original="/static/images/index-new/test/3.jpg">
                    <div class="empty"></div>
                </div>
                <div class="banner-info clearfix">
                    <span class="qi">2折起</span>
                    <span class="banner-title">海洋基因嫩颜秘诀专场海洋基因嫩颜秘诀专场海洋基因嫩颜秘诀专场</span>
                    <span class="J-banner-timer banner-timer right" timer="21100">剩1天</span>
                </div>
            </li>
            <li act-id="25">
                <div class="img-wrap" data-original="/static/images/index-new/test/2.jpg">
                    <div class="ready-tip">
                        <p>距离开抢</p>
                        <p class="J-miao-timer" timer="1000">10:50:10</p>
                        <a class="ready-btn"><i class="icon-ling"></i><label>开抢提醒</label></a>
                    </div>
                </div>
                <div class="banner-info clearfix">
                    <span class="qi">2折起</span>
                    <span class="banner-title">海洋基因嫩颜秘诀专场</span>
                    <span class="J-banner-timer banner-timer right" timer="21100">剩1天</span>
                </div>
            </li>
        </ul>
    </section>
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
                            <label class="btn btn-sm">7.8折</label>
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
    <a class="cart"><i>2</i></a>
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
</body>
</html>
