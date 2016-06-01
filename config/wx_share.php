<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
    wx.config({
      debug: false,
      appId: "<?php echo $signPackage["appId"];?>",
      timestamp: "<?php echo $signPackage["timestamp"];?>",
      nonceStr: "<?php echo $signPackage["nonceStr"];?>",
      signature: "<?php echo $signPackage["signature"];?>",
      jsApiList: [
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'hideMenuItems'
      ]
    });
    var title = "<?php echo $shareCfg['title']?>";
	var link = "<?php echo $shareCfg['url']?>";
	var imgUrl = "<?php echo $shareCfg['img']?>";
	var desc = "<?php echo $shareCfg['desc']?>";
	var shareParams = "<?php echo (empty($shareCfg['shareParams']) ? '' : $shareCfg['shareParams'])?>";
	wx.ready(function(){
        function onBridgeReady(){
            WeixinJSBridge.call('showOptionMenu');
            WeixinJSBridge.call('hideMenuItems');
        }
        if (typeof WeixinJSBridge != "undefined") {
            onBridgeReady();
        }
        wx.onMenuShareTimeline({
            title: title,
            link: link,
            imgUrl: imgUrl,
            success: function () {
                shareLog(1);
            },
            cancel: function () { }
        });
        wx.onMenuShareAppMessage({
            title: title,
            desc: desc,
            link: link,
            imgUrl: imgUrl,
            success: function () {
                shareLog(2);
            },
            cancel: function () { }
        });


        wx.hideMenuItems({
            menuList: [
                'menuItem:copyUrl'],
            success: function () { },
            fail: function () { }
        });
        function shareLog(type) {
            $.ajax({
                    url: '/api/User/wxShareLog',
                    type: 'post',
                    data: {
                    type: type,
                    params: shareParams
                },
                success: function(){ },
                error: function(){ }
            });
        }

	});
</script>
