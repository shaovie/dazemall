<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<title>商品详情</title>
    <?php src\common\JsCssLoader::outCss('modules/goods/index.less');?>
</head>
<body isPop="login">
	<!--初始化购物车url，返回number-->
	<input type="hidden" id="J-ajaxurl-initCart" value="/api/Cart/getCartAmount" />
	<header>
	<a href="/" class="btn-fir"><i class="icon-fir"></i><label>首页</label></a>
	<a href="/user/Home" class="btn-order"><i class="icon-order"></i><label>我的订单</label></a>
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
						<li><span class="price price-cur">
                        <label>现价：</label><i>&yen;</i>
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
					<i class="icon icon-by"></i><label>全场包邮</label>
				</a>
			</li>
			<li class="dib">
				<a>
					<i class="icon icon-js"></i><label>极速到货</label>
				</a>
			</li>
			<li class="dib">
				<a>
					<i class="icon icon-bt"></i><label>淘金补贴</label>
				</a>
			</li>
			<li class="dib">
				<a>
					<i class="icon icon-zp"></i><label>正品保证</label>
				</a>
			</li>
		</ul>
	</section>
	<section class="goods-des">
		<div class="section-title">商品详情</div>
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

	<a herf="/mall/Cart" class="cart"><i id="J-cart-num"></i></a>

	<div id="J-panel-cm"  class="panel-cm">
		<div class="mask"></div>
		<div class="panel-content">
			<form>
				<div class="g-item dib-wrap">
					<div class="img-wrap dib" style="background-image: url(<?php echo $imageUrl?>)"></div>
					<div class="g-mid dib">
						<div class="g-title"><?php echo $name?></div>
						<div class="price"><i>&yen;</i><b id="J-sku-price"><?php echo $salePrice?></b></div>
					</div>
					<div class="close dib"></div>
				</div>
				<!-- <section class="cm-content">
					<dl id="J-cm">
						<dt>口味</dt>
                        <dd>
	                         <label class="sel">西瓜味</label>
	                         <label >烧烤味</label>
	                         <label >奶油味</label>
	                         <label >榴莲味</label>
	                         <label >苹果味</label>
	                     </dd>
                         <dt>颜色</dt>
                        <dd>
	                         <label class="sel">藏蓝红心</label>
	                         <label >奶瓶</label>
	                         <label >蓝条</label>
	                         <label >黄绿色</label>
	                         <label >浅黄色</label>
	                         <label >米黄色</label>
	                     </dd>
                         <dt>尺寸</dt>
                        <dd>
	                          <label class="sel">105</label>
	                         <label >110</label>
	                         <label >120</label>
	                         <label >150</label>
	                         <label >73</label>
                         </dd>
                    </dl>
				</section> -->
				<div id="J-cm-amount" class="cm-amount clearfix">
					<label>库存量：<span id="J-last-num"></span></label>
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
	<input type="hidden" id="price" value="<?php $salePrice?>" />
	<input type="hidden" id="gid" value="<?php $goodsId?>" />
    <input type="hidden" id="goods_sku_json" value='<?php echo $skuJson;?>' />

    <?php
        src\common\JsCssLoader::outJs('lib/mod.js');
        src\common\JsCssLoader::outJs('goods/index');
    ?>
    <script type="text/javascript">require('goods/index');</script>
</body>
</html>
