<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=10">    
</head>
<body>
<h4>图片尺寸:</h4>
<p style="margin-left:30px;font-size:14px">首页轮播图 640x346</p>
<p style="margin-left:30px;font-size:14px">活动入口图 640x308</p>
<p style="margin-left:30px;font-size:14px">商品详情图 640x...</p>
<p style="margin-left:30px;font-size:14px">商品主图/轮播图 640x640</p>
<p style="margin-left:30px;font-size:14px">图片大小 单张图片尽量控制在50K以内，商品详情图尽量切成多张小图</p>
<h4>商品配置:</h4>
<p style="margin-left:30px;font-size:14px">地址：[商品管理]-[添加新商品] </p>
<p style="margin-left:30px;font-size:14px">上加状态：无效：此商品不会展示出来并且不能购买</p>
<p style="margin-left:30px;font-size:14px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;有效：商品不会出现在分类页或搜索列表，但可以购买（比如放在活动中或轮播图链接此商品）</p>
<p style="margin-left:30px;font-size:14px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;如果想下架某商品并且不可购买，即可将其置为无效，如果只是想让商品不让用户看到或搜索到，那置为有效即可</p>
<p style="margin-left:30px;font-size:14px">市场价：是用来对比实际销售价计算折扣的</p>
<p style="margin-left:30px;font-size:14px">实际销售价：用来显示在商品列表页的时候商品价格（与SKU无关的价）</p>
<h4>商品SKU:</h4>
<p style="margin-left:30px;font-size:14px">地址：[商品管理]-[SKU管理] </p>
<p style="margin-left:30px">1. 商品SKU是指商品规格 比如 容量：500ml，"容量"是指sku属性，"500ml"是指sku属性值</p>
<p style="margin-left:30px">2. SKU切记 不能全部放到一个属性里边，比如所有的规格全部放到“规格”里边</p>
<p style="margin-left:30px">3. SKU一定要分类，比如 "口味"，“容量”，“颜色”，“重量”，“尺寸”等，这样才利用管理，对用户友好</p>
<p style="margin-left:30px">4. 如果同时表现两种规格 比如“红色500ml”，这种可以参考淘宝，可以创建一个“颜色尺寸”的sku，或者比如500ml是固定的，那么直接写在商品名字上就可以，sku只选颜色</p>
<h4>轮播图配置:</h4>
<p style="margin-left:30px;font-size:14px">地址：[营销管理]-[轮播图管理] </p>
<p style="margin-left:30px">1. 轮播图就是在某个banner区域可以轮流展示多场图的功能，并且可以跳转到指定页面</p>
<p style="margin-left:30px">2. 可以配置展示时间（最好一次配置多个时间段，免的到期后，没有后续的轮播图展现），展示区域目前只支持首页顶部</p>
<p style="margin-left:30px">3. 链接配置项，就是指当点击轮播图时跳转到哪里，目前支持商品详情、活动页</p>
<p style="margin-left:30px">&nbsp;&nbsp;如果链接商品，配置项就填商品编号，如果是活动页，那么要先在[营销管理]-[活动页列表]中配置一个活动</p>
<p style="margin-left:30px">4. 当有多个轮播图时，排序配置项就是用来控制，轮播图出现的次序的</p>
<h4>秒杀活动配置:</h4>
<p style="margin-left:30px;font-size:14px">地址：[营销管理]-[秒杀活动] </p>
<p style="margin-left:30px">1. 秒杀活动分三场，10:00:00 ~ 16:59:59, 17:00:00 ~ 20:59:59, 21:00:00 ~ 09:59:59(次日)</p>
<p style="margin-left:30px">2. 在首页显示时，可以预览当前时间段后边的场次，当查看当前时间段之前的场次时其实是预览明天的场次</p>
<p style="margin-left:30px">3. 配置时间格式必须按照 step.1来</p>
<p style="margin-left:30px">4. 这里只是配置哪些商品参加秒杀活动，至于秒杀的价格和限购个数要去[商品列表]-[商品SKU]-[定时调价]配置，注：<b>商品调价的时间要和秒杀活动的时间保持一致</b></p>
<h4>定时上下架:</h4>
<p style="margin-left:30px;font-size:14px">地址：[营销管理]-[定时上下架] </p>
<p style="margin-left:30px">1. 如果商品需要提前编辑好然后在某一时间段一起上架，那么可以选择定时上架功能</p>
<p style="margin-left:30px">2. 该功能可选择一次性上下架和每天重复上下架</p>
<p style="margin-left:30px">&nbsp;&nbsp;比如：元旦卖元宵，持续一周，那么就可以定时将元宵在01-01 00:00:00 ~ 01-06 23:59:59这个时间段定时上架并自动下架</p>
<p style="margin-left:30px">&nbsp;&nbsp;比如：某些商品分早晚场，只允许早市或晚市上架销售，那么可以使用‘每天上下架’功能</p>
<h4>首页商品专题区块:</h4>
<p style="margin-left:30px;font-size:14px">地址：[营销管理]-[页商品模块列表] </p>
<p style="margin-left:30px">1. 该功能是动态创建专题区域，将产品以专题分类表现在首页。</p>
<p style="margin-left:30px">2. 可以定时展示</p>
<p style="margin-left:30px">3. 运营人员完全可以根据运营活动需求创建不同的专题，以给用户最合适的购物引导</p>
<h4>主题活动创建:</h4>
<p style="margin-left:30px;font-size:14px">地址：[营销管理]-[活动页列表] </p>
<p style="margin-left:30px">1. 当我们想为某一个主题（比如清明节）单独做一场专场活动时，可以使用此功能</p>
<p style="margin-left:30px">2. 创建一个活动（可显示在首页（品类下方位置）也可以隐藏起来通过轮播图进入</p>
<p style="margin-left:30px">3. 然后将精选的商品添加到活动的商品列表中</p>
</body>
</html>
