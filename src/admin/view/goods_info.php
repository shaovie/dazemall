<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=10">    
	<link href="/asset/css/bootstrap.min.css<?php echo '?v=' . ASSETS_VERSION;?>" rel="stylesheet">
	<link href="/asset/css/ace.min.css<?php echo '?v=' . ASSETS_VERSION;?>" rel="stylesheet">

    <link rel="stylesheet" href="/asset/css/ace-rtl.min.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <link rel="stylesheet" href="/asset/css/ace-skins.min.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="/asset/css/ace-ie.min.css<?php echo '?v=' . ASSETS_VERSION;?>" />
    <![endif]-->
	<link href="/asset/css/common.css<?php echo '?v=' . ASSETS_VERSION;?>" rel="stylesheet">
	<link type="text/css" rel="stylesheet" href="/asset/css/fontawesome3/css/font-awesome.min.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <script type="text/javascript" src="/asset/js/jquery-1.10.2.min.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <script type="text/javascript" src="/asset/js/common.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <script type="text/javascript" src="/asset/js/bootstrap.min.js<?php echo '?v=' . ASSETS_VERSION;?>"></script> 
    <link type="text/css" rel="stylesheet" href="/asset/css/default.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <!--[if IE 7]>
    <link rel="stylesheet" href="/asset/css/fontawesome3/css/font-awesome-ie7.min.css<?php echo '?v=' . ASSETS_VERSION;?>">
    <![endif]-->
	<script type="text/javascript" src="/asset/js/goods.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
</head>
<body class="no-skin">
    <input id="goodsId" name="goodsId" type="hidden" value="<?php echo (isset($goods['id']) ? $goods['id'] : 0);?>"/>
    <input type="hidden" id="J-ajaxurl-addCart" value="/api/Cart/add" />
    <input type="hidden" id="J-ajaxurl-quickBuy" value="/mall/Pay/quickBuy" />
    <input type="hidden" id="J-ajaxurl-initCart" value="/api/Cart/getCartAmount" />
	<h3 class="header smaller lighter blue"><?php echo $title?></h3>
	<form action="<?php echo $action?>" method="post" enctype="multipart/form-data" class="form-horizontal" role="form" id="save-form">
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 商品名称：</label>
			<div class="col-sm-9">
				<input type="text" name="name" id="goodsname" maxlength="100" class="span7" value="<?php if (!empty($goods['name'])){echo $goods['name'];}?>">
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 上架状态：</label>
			<div class="col-sm-9" id="state-radio">
				<div style="margin-right:20px;display:inline;">
                    <input type="radio" name="state" value="0" id="isshow0" <?php if (isset($goods['state']) && $goods['state'] == 0) { echo 'checked="true"';}?> >无效</div>
				<div style="margin-right:20px;display:inline;">
                    <input type="radio" name="state" value="1" id="isshow1" <?php if (isset($goods['state']) && $goods['state'] == 1) { echo 'checked="true"';}?> `>有效</div>
				<div style="margin-right:20px;display:inline;">
                    <input type="radio" name="state" value="2" id="isshow2" <?php if (isset($goods['state']) && $goods['state'] == 2) { echo 'checked="true"';}?> >上架销售</div>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left">排序：</label>
			<div class="col-sm-9">
				<input type="text" name="sort" id="sort"
                onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" 
                value="<?php if (isset($goods['sort'])){echo $goods['sort'];}?>">
				<p class="help-block">数值越大，排序越靠前（当前最大可用值<?php echo time();?>）</p>
          	</div>
		</div>
		
		<div class="form-group">
           <label class="col-sm-2 control-label no-padding-left"> 商品类别：</label>
           <input type="hidden" name="cateid" value="<?php if(isset($goods['category_id'])) echo $goods['category_id'];?>" id="cateid">
           <div class="col-sm-9 category">
               <a class="btn btn-default btn-sm" id="chooseCategory" href="javascript:void(0);" onclick="getCategory(0,0,this);return false;"><?php echo isset($goods['cate_name'])?$goods['cate_name']:'选择分类';?></a>
           </div>
		</div>
		
		
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 市场售价：</label>
			<div class="col-sm-9">
				<input type="number" name="marketPrice" id="marketprice" value="<?php if (isset($goods['market_price'])){echo $goods['market_price'];}?>">&nbsp;元
			</div>
		</div>
				
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 实际销售价：</label>
			<div class="col-sm-9">
				<input type="number" name="salePrice" id="saleprice" value="<?php if (isset($goods['sale_price'])){echo $goods['sale_price'];}?>">&nbsp;元
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left">奖励积分：</label>
			<div class="col-sm-9">
				<input type="text" name="jifen" id="jifen"  
                onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" 
                value="<?php if (!empty($goods['jifen'])){echo $goods['jifen'];}?>"
                readonly="readonly">
				<p class="help-block">会员购买商品赠送的积分, 如果不填写，则默认为不奖励积分</p>
          	</div>
		</div>
		
		
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left">商品主图：<br><span style="font-size:12px;color:red">（标准: 640*640 <br>尺寸尽量小于50K）</span></label>
			<div class="col-sm-9">
              <div id="prev_thumb_img" class="fileupload-preview thumbnail" style="width: 140px; height: 150px;">
               <?php if(!empty($goods['image_url'])){?>
                   <img src="<?php echo $goods['image_url'];?>" />
                  <a href='javascript:void(0)' onclick='delThumbImg(this);return false;'>删除</a>
               <?php }?>
              </div>
              <!-- SWFUpload控件 -->
              <div id="divSWFUploadUI">
                 <p>
                    <span id="spanButtonPlaceholder"></span>
					<input id="btnCancel" type="hidden" value="全部取消" disabled="disabled"/>
                 </p>
              </div>
              <!-- END -->
            </div>
		</div>	
		
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"> 轮播图(最多9张)：</label>
			<div class="col-sm-9">
                <div id="prev_goods_img" class="fileupload-preview thumbnail" style="width: 100%; height: 150px;">
                <ul>
                <?php if (!empty($goods['image_urls'])):?>
                    <?php foreach($goods['image_urls'] as $img):if(!empty($img)):?>
                       <li>
                        <img style="width:100px;height:120px;margin-right:2px" src="<?php echo $img;?>" />
                        <a href='javascript:void(0)' onclick="delGoodsImg(this);return false;">删除</a>
                       </li> 
                    <?php endif;endforeach;?>
                <?php endif?>
                </ul>
                </div>
                <!-- SWFUpload控件 -->
                <div id="divSWFUploadUI2">
                    <p>
                        <span id="spanButtonPlaceholder2"></span>
						<input id="btnCancel2" type="hidden" value="全部取消" disabled="disabled"/>
                    </p>
                </div>
                <!-- END -->
			</div>
		</div>

		<div class="form-group">
             <label class="col-sm-2 control-label no-padding-left"> SKU：</label>
             <div class="col-sm-9">
               <!--SKU大类 -->
               <div class="sku_info" id="skuAttr-radio">
               <?php foreach ($skuAttrList as $skuAttr):?>
                 <label class="radio inline">
                    <input type="radio" name="sku_radio" sku-id="<?php echo $skuAttr['id']?>"
                    value="<?php echo $skuAttr['attr']?>" onclick="getSkuAttr(this)" 
                     <?php if ($skuAttr['attr'] == $curSkuAttr) {echo 'checked="checked"';}
                     if(isset($goods['id'])) {echo 'disabled="disabled;"';} ?>><?php echo $skuAttr['attr']?>
                 </label>
               <?php endforeach?>
               </div>
               <!--E -->

               <!--SKU属性 -->
               <div class="sku_attr">
               <?php if (!empty(isset($goods['id']))):?>
               <label class="title"><?php echo $curSkuAttr?></label>
                 <br />
               <?php foreach ($allSkuValueList as $item):?>
                   <?php $checked = false;
                   foreach ($skuValueList as $val) {
                       if ($val['sku_value'] == $item['value']) {
                           $checked = true;
                           break;
                       }
                   }
                   if ($checked) {
                     echo '<label class="checkbox inline"><input type="checkbox"'
                     . ' value="' . $item['id'] . '"' . ' checked="checked" disabled="disabled;">'
                     . $item['value'] . '</label>';
                   } else {
                     echo '<label class="checkbox inline"><input type="checkbox"'
                     . ' value="' . $item['id'] . '"' . ' disabled="disabled;">'
                     . $item['value'] . '</label>';
                   }
                   ?>
                 <!--<label class="title">颜色</label>
                 <br />
                 <label class="checkbox inline">
                    <input type="checkbox" value="1" onclick="createTable();">红色
                 </label>
                 <label class="checkbox inline">
                    <input type="checkbox" value="2" onclick="createTable();">绿色
                 </label>
                 <label class="checkbox inline">
                   <input type="checkbox" value="3" onclick="createTable();">蓝色
                 </label>
                 <label class="checkbox inline">
                   <input type="checkbox" value="4" onclick="createTable();">白色
                 </label>-->
               <?php endforeach?>
               <?php endif?>
               </div>
               <!--E -->

               <div class="sku_table">
                  <!--<table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>颜色</th>
                        <th>价格</th>
                        <th>库存</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>红色</td>
                        <td width="100"><input type="text" value="">100</td>
                        <td><input type="text" value="">20</td>
                     </tr>
                     <tr>
                       <td>紫色</td>
                        <td width="100"><input type="text" value="">60</td>
                        <td><input type="text" value="">20</td>
                     </tr>
                    </tbody>
                  </table>-->
               </div>

             </div>
        </div>

		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left">商品详细描述：<br><span style="font-size:12px;color:red">(建议图片宽不超过640px)</span></label>
			<div class="col-sm-9">
               <!-- Ueditor -->    
               <div id="goods_details" style="display:none"><?php if (!empty($goods['detail'])){echo $goods['detail'];}?></div>  
               <script id="editor" type="text/plain" style="width:90%; height:360px;overflow-x:hidden;"></script>          
               <script type="text/javascript">
                   $(function(){
                      var ue = UE.getEditor('editor',{initialFrameWidth:"99%",allowDivTransToP:false});
                      ue.ready(function() {
                         ue.setContent($("#goods_details").html());
                     });
                   });      
               </script>
               <!-- End -->
            </div>
		</div>
		
		<div class="alert alert-info" style="margin-left:10px;color:red;">
			注意事项：<br>
		</div>
		
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left"></label>
			<div class="col-sm-9">
				<button type="button" id="save-btn" class="btn btn-primary span2" >保存商品信息</button>
				<button type="button" id="back" class="btn btn-warning span2">返回</button>
			</div>
		</div>
		
        <input type="hidden"  id="thumb_img" class="thumb_img" value="<?php if (!empty($goods['image_url'])){echo $goods['image_url'];}?>">
        <div id="goods_img">
        <?php if (!empty($goods['image_urls'])):?>
            <?php foreach($goods['image_urls'] as $img):if(!empty($img)):?>
               <input type="hidden" name="goods_img[]" value="<?php echo $img;?>" />
            <?php endif; endforeach;?>
            <?php endif?>
        </div>
	</form>
	<script>
       var skuValue = {}; 
       <?php if (!empty($skuValueList)){?>
            <?php foreach ($skuValueList as $val) {?>
                skuValue["<?php echo $val['sku_value'];?>"] = "<?php echo $val['sale_price'] . ':'. $val['amount'] . ':' . $val['bar_code'];?>"; 
       <?php }
       } ?>
       $(document).ready(function(){
           if( $('#goodsId').val() > 0) {
              createTable(true, skuValue);
           } else {
                $("#skuAttr-radio input[name='sku_radio']:checked").click();
           }
       });
       $('#save-btn').click(function(){
            var goodsDetails = UE.getEditor('editor').getContent();
            var url = $("#save-form").attr("action");
            var goods_imgs = '';
             $("#goods_img input").each(function(i,v){
                 goods_imgs += $(v).val()+'|';
             });
             var sku = '';
             $('.sku_table tbody tr').each(function(i,v){
                 var sku_attr_title = $(v).find('td').eq(0).text();
                 var sku_price = $(v).find('input').eq(0).val();
                 var sku_stock = $(v).find('input').eq(1).val();
                 var bar_code = $(v).find('input').eq(2).val();
                 sku += sku_attr_title + ':' + sku_price + ':' + sku_stock + ':' + bar_code + '|';
            });
            $.post(url,{
                goodsId:$("#goodsId").val(),
                cateId:$('#cateid').val(),
                name:$("#goodsname").val(),
                sort:$("#sort").val(),
                state:$("#state-radio input[name='state']:checked").val(),
                marketPrice:$("#marketprice").val(),
                salePrice:$("#saleprice").val(),
                detail:goodsDetails,
                imageUrl:$("#thumb_img").val(),
                imageUrls:goods_imgs,
                jifen:$("#jifen").val(),
                skuAttr:$("#skuAttr-radio input[name='sku_radio']:checked").val(),
                sku:sku
                },function(data){
                if(data.code==0) {
                    alert(data.msg);
                    window.location.href= data.url;
                } else {
                    alert(data.msg);
                    return false;
                }
            },'json');
        });
	</script>
    <!--UE editor编辑器js引入 -->
    <script type="text/javascript" charset="utf-8" src="/asset/js/ueditor/ueditor.config.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <script type="text/javascript" charset="utf-8" src="/asset/js/ueditor/ueditor.all.js<?php echo '?v=' . ASSETS_VERSION;?>"> </script>
    <script type="text/javascript" charset="utf-8" src="/asset/js/ueditor/lang/zh-cn/zh-cn.js<?php echo '?v=' . ASSETS_VERSION;?>"></script> 
    <!-- END -->
    <!-- SWFupload异步图片上传 -->
    <script type="text/javascript" src="/asset/js/swfupload/swfupload.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <script type="text/javascript" src="/asset/js/swfupload/swfupload.swfobject.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <script type="text/javascript" src="/asset/js/swfupload/swfupload.queue.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <script type="text/javascript" src="/asset/js/swfupload/fileprogress.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <script type="text/javascript" src="/asset/js/swfupload/handlers.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <!-- END -->
    <script type="text/javascript" src="/asset/js/swfupload/init.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
    <!-- <link href="/asset/js/swfupload/swfupload.css<?php echo '?v=' . ASSETS_VERSION;?>" rel="stylesheet" type="text/css"/> -->
</body>
</html>
