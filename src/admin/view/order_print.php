<?php $h1List = array('存根', '客户', '超市');?>
<!--1-->
<?php foreach ($h1List as $key => $val):?>
<style type="text/css">body,td {font-size:13px;}</style>
<h1 align="left"><?php echo $val?></h1>
<table width="227px" cellpadding="1">
<tr><td align="left">购 货 人：</td> <td><?php echo $userName?></td></tr>
<tr><td align="left">订单编号：</td><td><?php echo $order['orderId']?></td></tr>
<tr><td align="left">下单时间：</td><td><?php echo date('Y-m-d H:i:s', $order['ctime'])?></td> </tr>
<tr><td align="left">付款时间：</td><td><?php echo $order['payTime']?></td></tr>
<tr><td align="left">支付方式：</td><td><?php echo $order['payType']?></td></tr>
<tr><td align="left">发货时间：</td><td><?php echo $order['deliveryTime']?></td></tr>
<tr><td align="left">配 送 员：</td><td><?php echo $order['deliverymanName']?></td></tr>
<tr><td align="left">收货地址：</td><td><?php echo $order['regionAddr']?></td></tr>
<tr><td colspan="7">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $order['detailAddr']?>&nbsp;</tr>
<tr><td align="left">收 货 人：</td><td><?php echo $order['reName']?>&nbsp;</td></tr>
<tr><td align="left">手 机 号：</td><td><?php echo $order['rePhone']?>&nbsp;</td></tr>
</table>
<br/>
<table width="227px" border="1" style="border-collapse:collapse;border-color:#ccc;table-layout:fixed">
<tr align="center">
<td bgcolor="#ccc" width="70" >商品名称</td>
<td bgcolor="#ccc" width="115" >价格</td>
<td bgcolor="#ccc" width="42" >小计</td>
</tr>
<?php foreach ($order['goodsList'] as $goods):?>
<tr>
<td align="left" style="word-wrap:break-word"><?php echo $goods['name']?></td>
<td align="left" style="word-wrap:break-word"><div>价格：<?php echo $goods['price']?></div><div>数量：<?php echo $goods['amount']?></div><div>条码：<?php echo $goods['bar_code']?></div></td>
<td align="center" ><?php echo ($goods['price'] * $goods['amount'])?></td>
</tr>
<?php endforeach?>
<tr>
<!-- 发票抬头和发票内容 -->
<tr><td colspan="2"> </td>
<!-- 商品总金额 -->
<td colspan="2" align="left">商品总金额：￥<?php echo $order['orderAmount'] - $order['postage']?></td>
</tr>
</table>
<table width="227px" border="0">
<tr align="left">
<td>= 运费&nbsp;&nbsp;&nbsp;：￥<?php echo $order['postage']?> </td>
</tr>
<tr align="left">
<td>= 优惠券金额：￥<?php echo $order['couponPayAmount']?> </td>
</tr>
<tr align="left">
<td>= 订单总金额：￥<?php echo $order['orderAmount']?> </td>
</tr>
<tr align="left">
<td> </td>
<!-- 如果已付了部分款项, 减去已付款金额 -->
<!-- 如果使用了余额支付, 减去已使用的余额 -->
<!-- 如果使用了积分支付, 减去已使用的积分 -->
<!-- 如果使用了红包支付, 减去已使用的红包 -->
<!-- 应付款金额
= 应付款金额：￥69.25 </td> -->
</tr>
</table>
<br/>
<table width="227px" border="0">
<tr><!-- 网店名称, 网店地址, 网店URL以及联系电话 -->
<td> 大泽商城－茂业百货优质商品网购商城－生鲜食品、肉类海鲜、母婴奶粉、粮油、酒水饮料品质保证 极速直达!（微信公众号：大泽商城）</td>
</tr>
<!-- <tr><td>地址：</td></tr> -->
<!-- <tr><td>电话：</td></tr> -->
<tr align="left"><!-- 订单操作员以及订单打印的日期 -->
<td>打印时间：<?php echo date('Y-m-d H:i:s')?></td>
</tr>
<!-- <tr align="left"><td>操作者：admin</td></tr> -->
</table>
<?php if ($key < 2):?>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<?php endif?>
<?php endforeach?>
<script>window.print();</script>
