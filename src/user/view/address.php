<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<title>地址管理</title>
	<?php src\common\JsCssLoader::outCss('modules/address/index.less');?>
</head>
<body>
    <!--地址列表-->
    <input type="hidden" id="J-ajaxurl-address-list" value="/api/UserAddress/getAll" />
    <!--地址：设置默认-->
    <input type="hidden" id="J-ajaxurl-address-setDefault" value="/api/UserAddress/setDefault" />
    <!--地址：保存地址-->
    <input type="hidden" id="J-ajaxurl-address-save" value="/api/UserAddress/edit" />
    <!--地址：删除-->
    <input type="hidden" id="J-ajaxurl-address-del" value="/api/UserAddress/del" />
	<header>
	<a href="/" class="btn-fir"><i class="icon-fir"></i><label>首页</label></a>
	<a href="/user/Order" class="btn-order"><i class="icon-order"></i><label>我的订单</label></a>
    </header>
	<section id="J-address-section"></section>
	<?php
	src\common\JsCssLoader::outJs('lib/mod.js');
	src\common\JsCssLoader::outJs('address/index');
	?>
    <script type="text/javascript">require('address/index');</script>
</body>
</html>
