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
</head>
<body class="no-skin">
	<h3 class="header smaller lighter blue"><span style="margin-right:20px">商品总数：<?php echo $totalGoodsNum;?></span><a href="/admin/Goods/addPage" class="btn btn-primary">新建商品</a><span class="refresh">刷新</span></h3>
	<form action="/admin/Goods/search" class="form-horizontal" method="get">
	<table class="table table-striped table-bordered table-hover">
	<tbody>
		<tr>
			<td>
				<li style="float:left;list-style-type:none;">
					<select name="status" style="margin-right:10px;margin-top:10px;width: 100px; height:34px; line-height:28px; padding:2px 0">
					    <option value="-1" <?php if (!isset($search['status']) || $search['status'] == -1) { echo 'selected="selected"';}?> >商品状态</option>
					    <option value="0" <?php if (isset($search['status']) && $search['status'] == 0) { echo 'selected="selected"';}?> >无效</option>
					    <option value="1" <?php if (isset($search['status']) && $search['status'] == 1) { echo 'selected="selected"';}?>  >有效</option>
					    <option value="2" <?php if (isset($search['status']) && $search['status'] == 2) { echo 'selected="selected"';}?> >上架销售</option>
					</select>
				</li>
				<li style="float:left;list-style-type:none;">
					<span>关键字</span>	<input style="margin-right:10px;margin-top:10px;width:160px; height:34px; line-height:28px; padding:2px 5px" name="keyword" id="" type="text" value="<?php if (!empty($search['keyword'])) {echo $search['keyword'];} ?>" placeholder="商品编号/商品名称">
				</li>
				<li style="float:left;list-style-type:none;">
					<input type="submit" name="submit" class="btn btn-sm btn-primary" style="margin-right:10px;margin-top:10px;" value="搜索"></li>

				<li style="float:left;list-style-type:none;">
					<select name="category" id="category" style="margin-right:10px;margin-top:10px;width: 160px; height:34px; line-height:28px; padding:2px 0">
					    <option value="-1" <?php if (!isset($search['catId']) || $search['catId'] == -1) { echo 'selected="selected"';}?> >商品种类</option>
                        <?php foreach ($categoryList as $cate):?>
					    <option value="<?php echo $cate['category_id']?>" <?php if (isset($search['catId']) && $search['catId'] == $cate['category_id']) { echo 'selected="selected"';}?> ><?php echo $cate['name']?></option>
                        <?php endforeach?>
					</select>
				</li>
				<li style="float:left;list-style-type:none;">
                    <input type="button" name="category_search" value="分类列表" class="btn btn-sm btn-primary" style="margin-right:10px;margin-top:10px;"  onclick="categorySearch();">
			</td>
		</tr>
	</tbody>
	</table>
	</form>
		
	<table class="table table-striped table-bordered table-hover">
		<tbody>
		<tr>
			<th class="text-center" style="width:80px;">主图</th>
			<th class="text-center" style="width:250px;">商品</th>
			<th class="text-center" style="width:200px;">品类</th>
			<th class="text-center" style="width:120px;">价格</th>
			<th class="text-center" style="width:200px;">时间</th>
			<th class="text-center" style="width:150px;">状态</th>
			<th class="text-center" style="width:60px;">排序</th>
			<th class="text-center">操作</th>
		</tr>
        <?php foreach ($goodsList as $goods):?>
		<tr>
			<td style="padding:0px;margin:0px;">
                <p style="text-align:center;vertical-align:middle;margin:2px 0px;"> <img src="<?php echo $goods['image_url']?>" height="60" width="60"></p>
            </td>
			<td style="text-align:left;vertical-align:middle;">
                <div>编号：<?php echo $goods['id']?></div>
                <div>名称：<?php echo $goods['name']?></div>
            </td>
			<td style="text-align:left;vertical-align:middle;">
                <div>类别：<?php echo $goods['category_name']?></div>
            </td>
			<td style="text-align:left;vertical-align:middle;">
                <div>市场价：<?php echo $goods['market_price']?></div>
                <div>销售价：<?php echo $goods['sale_price']?></div>
            </td>
			<td style="text-align:left;vertical-align:middle;">
				<div>创建：<?php echo date('Y-m-d H:i:s', $goods['ctime'])?></div>
            </td>
			<td style="text-align:left;vertical-align:middle;">
                <div>上架状态：<?php echo $goods['state']?></div>
			</td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $goods['sort']?></div>
			</td>
			<td style="text-align:center;vertical-align:middle;">
				<a target="_blank" class="btn btn-xs btn-info" href="http://<?php echo APP_HOST;?>/mall/Goods/detail?goodsId=<?php echo $goods['id']?>">预览</a>
				<a class="btn btn-xs btn-info" href="/admin/Goods/editPage?goodsId=<?php echo $goods['id']?>">编辑</a>
				<a class="btn btn-xs btn-info" href="/admin/Goods/skuPage?goodsId=<?php echo $goods['id']?>">商品SKU</a>
				<a class="btn btn-xs btn-info" href="/admin/TimingUpDown/editPage?id=<?php echo $goods['id']?>">定时上架</a>
                <a class="btn btn-xs btn-info" onclick="goodsTag(<?php echo $goods['id']?>, <?php echo !empty($goods['tag']['name']) ? ("'" . $goods['tag']['name'] . "'"): "''" ?>, <?php echo isset($goods['tag']['color']) ? (int)$goods['tag']['color'] : 0 ?>)" >标签</a>
			</td>
		</tr>
        <?php endforeach?>
		</tbody>
	</table>
    <?php echo $pageHtml;?>
	<!--弹窗-->
	<div id="modal-confirmsend" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title">充值</h4>
			</div>
			<div class="modal-body">
				<div class="form-group" style="height:25px;">
					<label class="col-sm-3 control-label no-padding-right"> 标签名：</label>
					<div class="col-sm-9">
						<input type="text" name="tag-name" id="tag-name" class="span5" value="">
					</div>
                </div>
				<div class="form-group" style="height:25px;">
					<label class="col-sm-3 control-label no-padding-right"> 标签颜色：</label>
					<div class="col-sm-9" id="tag-color">
                        <div style="margin-right:20px;display:inline;">
                            <input type="radio" name="tagColor" value="1" id="isshow0" ><p class="label label-danger" style="margin-left:10px;">&nbsp;&nbsp;&nbsp;&nbsp;</p></div>
                        <div style="margin-right:20px;display:inline;">
                            <input type="radio" name="tagColor" value="2" id="isshow1" ><p class="label label-warning" style="margin-left:10px;">&nbsp;&nbsp;&nbsp;&nbsp;</p></div>
                        </div>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
                <input type="hidden" name="goodsId" value="" id="goodsId"/>
				<button type="button" class="btn btn-primary" id="confirmsend-btn" name="confirmsend" value="yes">提交</button>      	
				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
			</div>
			</div>
		</div>
	</div>
	<!-- END -->
    <script>
    function goodsTag(id, name, color){
      $('#goodsId').val(id);
      $('#tag-name').val(name);
      if (color == 1)
          $('#isshow0').attr("checked","checked");
      else if (color = 2)
          $('#isshow1').attr("checked","checked");
      $('#modal-confirmsend').modal('show');
    }
    $('#confirmsend-btn').click(function(){
        var url = "/admin/Goods/setTag";
        var color = $("#tag-color input[name='tagColor']:checked").val();
        $.post(url,{goodsId:$('#goodsId').val(),name:$('#tag-name').val(),color:color},function(data){
            if(data.code==0) {
                window.location.href= data.url;
            } else {
                alert(data.msg);
                return false;
            }
        },'json');
    });
    function categorySearch() {
        catId = $("#category option:selected").val();
        if (catId <= 0)
            return false;
        var url = '/admin/Goods/search?catId=' + catId;
        window.location.href = url;
    }
    </script>
</body>
</html>
