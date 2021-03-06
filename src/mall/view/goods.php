<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="format-detection"  content="telephone=no">
	<title>商品详情</title>
    <?php include CONFIG_PATH . '/wx_share.php';?>
    <?php src\common\JsCssLoader::outCss('modules/goods/index.less');?>
</head>
<body isPop="login">
	<input type="hidden" id="J-ajaxurl-initCart" value="/api/Cart/getCartAmount" />
    <input type="hidden" id="J-ajaxurl-addCart" value="/api/Cart/add" />
    <input type="hidden" id="J-ajaxurl-quickBuy" value="/mall/Pay/quickBuy" />
    <!-- <input type="hidden" id="J-ajaxurl-moreComment" value="/api/Goods/moreComment" /> -->
    <input type="hidden" id="J-ajaxurl-initProductLikeData" value="/api/Goods/likeInfo?goodsId=<?php echo $goodsId?>" />
	<header>
	<a href="/" class="btn-fir"><i class="icon-fir"></i><label>首页</label></a>
	<a href="/user/Order" class="btn-order"><i class="icon-order"></i><label>我的订单</label></a>
    </header>
	<section class="goods-item">
		<div id="banner" class='swipe'>
	<div class="swipe-wrap">
    <?php if (!empty($imageUrls)):?>
    <?php foreach ($imageUrls as $imageUrl):?>
		<div>
		      <a href="#">
		      	<div class="img-wrap" style="background-image:url(<?php echo $imageUrl?>)"></div>
			  </a>
		</div>
    <?php endforeach?>
    <?php endif?>
    </div>
    <div id="bannerPager"><span id="page"></span></div>
</div>
		<div class="row">
			<div class="item-infos">
				<a>
					<div class="item-title"><?php echo $name?></div>
                    <?php $price = explode('.', $salePrice);?>
					<ul class="item-b">
						<li><span class="price price-cur"><label>现价：</label><i>&yen;</i>
                        <b><?php echo $price[0]?>.<small><?php echo $price[1]?></small></b>
                        </span></li>
					</ul>
					<i id="J-share" class="icon-share"></i>
				</a>
			</div>
			<input id="J-goods-type" value="1" type="hidden"/>
		</div>
	</section>
	<section>
		<ul class="tj-types dib-wrap">
			<li class="dib">
				<a>
					<i class="icon icon-by"></i><label>快速到货</label>
				</a>
			</li>
			<li class="dib">
				<a>
					<i class="icon icon-js"></i><label>源头正品</label>
				</a>
			</li>
			<li class="dib">
				<a>
					<i class="icon icon-bt"></i><label>坏件必赔</label>
				</a>
			</li>
			<li class="dib">
				<a>
					<i class="icon icon-zp"></i><label>全城最惠</label>
				</a>
			</li>
		</ul>
	</section>
    <section class="comment-section">
        <div class="nav-wrap">
            <ul class="comment-nav">
                <li id="J-support-btn" ajax-url="/api/Goods/likeGoods?goodsId=<?php echo $goodsId?>"><i class="icon-support"></i>喜欢(<span class="J-num">0</span>)</li>
                <li></li>
            </ul>
        </div>
        <div class="comment-content">
            <ul class="support-list">
            </ul>
        </div>
    </section>
	<section class="goods-des">
		<!-- <div class="section-title">商品详情</div> -->
		<div class="goods-des-content">
        <?php echo empty($goodsDetail) ? '' : $goodsDetail; ?>
		</div>
	</section>
	<footer>
		<button id="J-btn-cart" class="btnl btnl-default">加入购物车</button>
		<button id="J-btn-buy" class="btnl">立即购买</button>
	</footer>

	<!-- <footer class="full">
		<button id="J-btn-buy" class="btnl">立即购买</button>
	</footer> -->

	<a href="/mall/Cart" class="cart"><i id="J-cart-num"></i></a>

	<div id="J-panel-cm"  class="panel-cm">
		<div class="mask"></div>
		<div class="panel-content">
			<form action="/api/Cart/add">
				<div class="g-item dib-wrap">
					<div class="img-wrap dib" style="background-image: url(<?php echo $imageUrl?>)"></div>
					<div class="g-mid dib">
						<div class="g-title"><?php echo $name?></div>
						<div class="price"><i>&yen;</i><b id="J-sku-price"><?php echo $defaultSku['sale_price']?></b></div>
					</div>
					<div class="close dib"></div>
				</div>
				<section class="cm-content">
					<dl id="J-cm">
						<dt><?php echo $skuAttr?></dt>
                        <dd>
                             <?php foreach ($skuValue as $i => $val):?>
                             <?php if ($i == 0):?>
	                         <label class="sel"><?php echo $val['sku_value']?></label>
                             <?php else:?>
	                         <label ><?php echo $val['sku_value']?></label>
                             <?php endif?>
                             <?php endforeach?>
	                     </dd>
                    </dl>
				</section>
				<div id="J-cm-amount" class="cm-amount clearfix">
					<label>库存量：<span id="J-last-num"><?php echo $defaultSku['amount']?></span></label>
					<div class="right">
						<ul class="J-amount-bar amount-bar dib-wrap" max="100" min="1">
                            <li class="btn-minus dib"><i class="icon"></i></li>
                            <li class="amount-val dib">1</li>
                            <li class="btn-add dib"><i class="icon"></i></li>
                        </ul>
					</div>
				</div>
				<section id="J-phone-panel" class="phone-panel">
					<div class="phone-title">免登录直接购买</div>
					<div class="phone-content">
						<div class="input-wrap">
							<label>手机号</label>
						<input id="phone2" type="tel" placeholder="请输入您的手机号码" value="" />
						</div>
						<div class="yzm clearfix">
							<div class="input-wrap">
								<label>验证码</label>
								<input id="code2" type="number" placeholder="输入短信验证码" />
							</div>
							<button id="J-yzm-bt2" type="button">获取验证码</button>
						</div>
						<div>
							<button id="J-phone-ok2" class="btnl btnl-wx">确认</button>
						</div>
					</div>
				</section>
				<section class="cm-foot">
					<button type="submit" class="J-ok btn">确定</button>
				</section>
			</form>
		</div>
	</div>

	<input type="hidden" id="goods_number" value="1" />
	<input type="hidden" id="price" value="<?php echo $salePrice?>" />
	<input type="hidden" id="gid" value="<?php echo $goodsId?>" />
    <input type="hidden" id="goods_sku_json" value='<?php echo $skuJson;?>' />

    <?php
        src\common\JsCssLoader::outJs('lib/mod.js');
        src\common\JsCssLoader::outJs('goods/index');
    ?>
    <script type="text/javascript">require('goods/index');</script>
</body>
</html>
