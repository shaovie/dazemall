<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta http-equiv="refresh" content="<?php echo $second?>;<?php echo $url?>">
    <title>系统提示</title>
    <?php src\common\JsCssLoader::outCss('modules/404/index.less');?>
</head>
<body>
	<div class="img-wrap"></div>
	<p class="error-msg"><?php echo $desc?></p>
	<div class="btnl-wrap">
		<span id="J-timer"><?php echo $second?></span>秒后自动跳转...
		<a id="J-back" href="<?php echo $url?>" class="btnl btnl-border">返回</a>
	</div>
    <br/>
    <?php
        src\common\JsCssLoader::outJs('lib/mod.js');
        src\common\JsCssLoader::outJs('404/index');
    ?>
    <script type="text/javascript">require('404/index');</script>
</body>
</html>
