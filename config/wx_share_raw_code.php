wx.config({
    debug: false,
    appId: "<?php echo $signPackage["appId"];?>",
    timestamp: "<?php echo $signPackage["timestamp"]?>",
    nonceStr: "<?php echo $signPackage["nonceStr"]?>",
    signature: "<?php echo $signPackage["signature"]?>",
    jsApiList: [
        "onMenuShareTimeline",
        "onMenuShareAppMessage",
        "hideMenuItems"
    ]
});

var title = typeof(wxShareTitle) != "undefined" ? wxShareTitle : "<?php echo $shareCfg['title']?>";
var link  = "<?php echo $shareCfg['url']?>";
var imgUrl= typeof(wxShareImg) != "undefined" ? wxShareImg : "<?php echo $shareCfg['img']?>";
var desc  = "<?php echo $shareCfg['desc']?>";

wx.ready(function() {
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
        },
        cancel: function () { }
    });
    wx.onMenuShareAppMessage({
        title: title,
        desc: desc,
        link: link,
        imgUrl: imgUrl,
        success: function () {
        },
        cancel: function () { }
    });


    wx.hideMenuItems({
        menuList: [
            'menuItem:copyUrl'
        ],
        success: function () {
        },
        fail: function () {
        }
    });
});
