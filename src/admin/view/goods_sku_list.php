<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=10">    
	<link href="/asset/css/bootstrap.min.css" rel="stylesheet">
	<link href="/asset/css/ace.min.css" rel="stylesheet">

    <link rel="stylesheet" href="/asset/css/ace-rtl.min.css">
    <link rel="stylesheet" href="/asset/css/ace-skins.min.css">
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="/asset/css/ace-ie.min.css" />
    <![endif]-->
	<link href="/asset/css/common.css" rel="stylesheet">
	<link type="text/css" rel="stylesheet" href="/asset/css/fontawesome3/css/font-awesome.min.css">
	<script type="text/javascript" src="/asset/js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="/asset/js/common.js"></script>
    <script type="text/javascript" src="/asset/js/bootstrap.min.js"></script> 
    <link type="text/css" rel="stylesheet" href="/asset/css/default.css">
    <!--[if IE 7]>
    <link rel="stylesheet" href="/asset/css/fontawesome3/css/font-awesome-ie7.min.css">
    <![endif]-->
	<script type="text/javascript" src="/asset/js/goods.js"></script>
</head>
<body class="no-skin">
    <h3 class="header smaller lighter blue"><span style="margin-right:20px">库存管理</span></h3>
    <input id="goodsId" name="goodsId" type="hidden" value="<?php echo $goodsId;?>"/>
	<form action="" class="form-horizontal" method="post" onsubmit="return formcheck(this)">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th class="text-center" style="width:80px;">商品SKU</th>
					<th class="text-center" style="width:50px;">现有库存</th>
					<th class="text-center" style="width:80px;">修改人</th>
					<th class="text-center" style="width:80px;">修改时间</th>
					<th class="text-center" style="width:80px;">操作</th>
				</tr>
			</thead>
			<tbody>
            <?php foreach ($skuList as $item):?>
			<tr>
				<td style="text-align:center;vertical-align:middle;"><?php echo $item['sku']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $item['amount']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $item['m_user']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo date('Y-m-d H:i:s', $item['mtime'])?></td>
				<td style="text-align:center;vertical-align:middle;">
					<button type="button" class="btn btn-primary span2" name="confirmsend" data-toggle="modal" data-target="#modal-confirmsend" value="confirmsend" sku-id="<?php echo $item['id']?>" >修改库存</button>
				</td>
			</tr>
            <?php endforeach?>
			</tbody>
		</table>

		<!--弹窗-->
		<div id="modal-confirmsend" class="modal fade">
			<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title">修改库存</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-left"> 库存：</label>
					<div class="col-sm-9">
						<input type="text" name="amount" class="span5">
					</div>
				</div>      	
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary" id="confirmsend-btn" name="confirmsend" value="yes">提交</button>      	
				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
			</div>
			</div>
		</div>
	</div>
	<!-- END -->
	</form>
	<script>
        $('#confirmsend-btn').click(function(){
            var url = "/admin/Goods/modifyKuCun";
            $.post(url,{id:$("#ipt_account").val(),passwd:$("#ipt_pwd").val()},function(data){
                if(data.code==0) {
                    window.location.href= data.url;
                } else {
                    alert(data.msg);
                    return false;
                }
            },'json');
        });

	</script>
</body>
</html>
