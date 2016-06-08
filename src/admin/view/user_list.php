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
	<h3 class="header smaller lighter blue"><span style="margin-right:20px">用户总数：<?php echo $totalUserNum;?></span><span class="refresh">刷新</span></h3>
	<form action="/admin/User/search" class="form-horizontal" method="get">
	<table class="table table-striped table-bordered table-hover">
	<tbody>
		<tr>
			<td>
				<li style="float:left;list-style-type:none;">
					<span>关键字</span>	<input style="margin-right:10px;margin-top:10px;width:200px; height:34px; line-height:28px; padding:2px 5px" name="keyword" id="" type="text" value="<?php if (!empty($search['keyword'])) {echo $search['keyword'];} ?>" placeholder="客户编号/昵称/手机号">
				</li>
				<li style="list-style-type:none;">
					<input type="submit" name="submit" class="btn btn-sm btn-primary" style="margin-right:10px;margin-top:10px;" value="搜索"></li>
			</td>
		</tr>
	</tbody>
	</table>
	</form>
		
	<table class="table table-striped table-bordered table-hover">
		<tbody>
		<tr>
			<th class="text-center" style="width:100px;">用户编号</th>
			<th class="text-center" style="width:250px;">昵称</th>
			<th class="text-center" style="width:250px;">手机号</th>
			<th class="text-center" style="width:250px;">openid</th>
			<th class="text-center" style="width:260px;">账户信息</th>
			<th class="text-center" style="width:260px;">时间</th>
			<th class="text-center">操作</th>
		</tr>
        <?php foreach ($userList as $user):?>
		<tr>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $user['id']?></div>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $user['nickname']?></div>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $user['phone']?></div>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                <div><?php echo $user['openid']?></div>
            </td>
			<td style="text-align:left;vertical-align:middle;">
                <div>余额：<?php echo $user['cash_amount']?></div>
            </td>
			<td style="text-align:left;vertical-align:middle;">
                <div>注册时间：<?php echo date('Y-m-d H:i:s', $user['ctime'])?></div>
            </td>
			<td style="text-align:center;vertical-align:middle;">
                 <a class="btn btn-xs btn-info" onclick="recharge(<?php echo $user['id']?>)" >充值</a>
                 <a class="btn btn-xs btn-info" onclick="giveCoupon(<?php echo $user['id']?>)" >发放优惠券</a>
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
					<label class="col-sm-3 control-label no-padding-right"> 金额：</label>
					<div class="col-sm-9">
						<input type="text" name="money" id="money" class="span5">
					</div>
				</div>      	
				<div class="form-group" style="height:25px;">
					<label class="col-sm-3 control-label no-padding-right"> 备注：</label>
					<div class="col-sm-9">
						<input type="text" name="remark" id="remark" class="span5">
					</div>
				</div>      	
			</div>
			<div class="modal-footer">
                <input type="hidden" name="uid" value="" id="uid"/>
				<button type="button" class="btn btn-primary" id="confirmsend-btn" name="confirmsend" value="yes">提交</button>      	
				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
			</div>
			</div>
		</div>
	</div>
	<!-- END -->
	<!--弹窗-->
	<div id="modal-confirmsend2" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title">发优惠券</h4>
			</div>
			<div class="modal-body">
				<div class="form-group" style="height:25px;">
					<label class="col-sm-3 control-label no-padding-right"> 优惠券编号：</label>
					<div class="col-sm-9">
						<input type="text" name="coupon" id="couponId" class="span5">
					</div>
				</div>      	
			</div>
			<div class="modal-footer">
                <input type="hidden" name="uid2" value="" id="uid2"/>
				<button type="button" class="btn btn-primary" id="confirmsend-btn2" name="confirmsend" value="yes">提交</button>      	
				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
			</div>
			</div>
		</div>
	</div>
	<!-- END -->
    <script>
        function recharge(id){
          $('#uid').val(id);
          $('#modal-confirmsend').modal('show');
        }
        function giveCoupon(id){
          $('#uid2').val(id);
          $('#modal-confirmsend2').modal('show');
        }
        $('#confirmsend-btn').click(function(){
            var url = "/admin/User/recharge";
            $.post(url,{uid:$('#uid').val(),money:$('#money').val(),remark:$('#remark').val()},function(data){
                if(data.code==0) {
                    window.location.href= data.url;
                } else {
                    alert(data.msg);
                    return false;
                }
            },'json');
        });
        $('#confirmsend-btn2').click(function(){
            var url = "/admin/User/giveCoupon";
            $.post(url,{uid:$('#uid2').val(),couponId:$('#couponId').val()},function(data){
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

