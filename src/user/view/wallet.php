<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="format-detection"  content="telephone=no">
	<title>我的钱包</title>
	<?php src\common\JsCssLoader::outCss('modules/wallet/index.less');?>
</head>
<body>
	<header>
	<a href="/" class="btn-fir"><i class="icon-fir"></i><label>首页</label></a>
	<a href="/user/Order" class="btn-order"><i class="icon-order"></i><label>我的订单</label></a>
    </header>
	<section class="money-wrap">
        <div class="my-money">
			<span class="money"><?php echo $cash;?></span>
		</div>
    </section>

	<section class="wallet-detail">
		<ul class="nav">
			<li class="active"><a>全部</a></li>
			<li><a>支出</a></li>
			<li><a>收入</a></li>
		</ul>
		<div class="container">
			<ul class="detail-list" id="J-bill-all">
		   <?php if (!empty($allList)){
			    foreach($allList as $val){?>
				<li>
					<span class="title"><?php echo $val['desc']?></span>
					<span class="price <?php if($val['bill_type']==1) echo 'income';?>">
					      <?php echo $val['amount']?>
					</span>
					<span class="date"><?php echo $val['ctime']?></span>
				</li>
				<?php }
              }?>
			</ul>
			<ul class="detail-list" id="J-bill-out">
			   <?php if (!empty($outList)) {
			       foreach ($outList as $val){?>
				<li>
					<span class="title"><?php echo $val['desc']?></span>
					<span class="price <?php if($val['bill_type']==1) echo 'income';?>">
					      <?php echo $val['amount']?>
					</span>
					<span class="date"><?php echo date('m-d H:i', $val['ctime'])?></span>
				</li>
				<?php }
			   }?>
			</ul>
			<ul class="detail-list" id="J-bill-in">
			  <?php if (!empty($inList)){
			      foreach ($inList as $val){?>
				<li>
					<span class="title"><?php echo $val['desc']?></span>
					<span class="price <?php if($val['bill_type']==1) echo 'income';?>">
					      <?php echo $val['amount']?>
					</span>
					<span class="date"><?php echo date('m-d H:i', $val['ctime'])?></span>
				</li>
				<?php }
			  }?>
			</ul>
		</div>
	</section>

    <?php
        src\common\JsCssLoader::outJs('lib/mod.js');
        src\common\JsCssLoader::outJs('wallet/index');
    ?>
    <script type="text/javascript">
	   require('wallet/index');
	</script>
</body>
</html>
