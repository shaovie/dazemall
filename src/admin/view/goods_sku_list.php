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
	<link type="text/css" rel="stylesheet" href="/asset/css/datetimepicker.css<?php echo '?v=' . ASSETS_VERSION;?>">
	<script type="text/javascript" src="/asset/js/datetimepicker.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
	<script type="text/javascript" src="/asset/js/goods.js<?php echo '?v=' . ASSETS_VERSION;?>"></script>
</head>
<body class="no-skin">
    <h3 class="header smaller lighter blue"><span style="margin-right:20px">库存管理</span><span><?php echo $goodsName?></span><span class="refresh">刷新</span></h3>
    <input id="goodsId" name="goodsId" type="hidden" value="<?php echo $goodsId;?>"/>
	<form action="" class="form-horizontal" method="post" onsubmit="return formcheck(this)">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th class="text-center" style="width:80px;">商品SKU</th>
					<th class="text-center" style="width:50px;">价格</th>
					<th class="text-center" style="width:60px;">条码</th>
					<th class="text-center" style="width:50px;">现有库存</th>
					<th class="text-center" style="width:50px;">累计销量</th>
					<th class="text-center" style="width:120px;">定时调价</th>
					<th class="text-center" style="width:80px;">修改人</th>
					<th class="text-center" style="width:80px;">修改时间</th>
					<th class="text-center" style="width:80px;">操作</th>
				</tr>
			</thead>
			<tbody>
            <?php foreach ($skuList as $idx => $item):?>
			<tr>
				<td style="text-align:center;vertical-align:middle;"><?php echo $item['sku']?></td>
				<td style="text-align:center;vertical-align:middle;" class="sale_price"><?php echo $item['sale_price']?></td>
				<td style="text-align:center;vertical-align:middle;" class="bar_code"><?php echo $item['bar_code']?></td>
				<td style="text-align:center;vertical-align:middle;" class="amount"><?php echo $item['amount']?></td>
				<td style="text-align:center;vertical-align:middle;" class="sold_amount"><?php echo $item['sold_amount']?></td>
				<td style="text-align:left;vertical-align:middle;" class="modify_price">
                <div>周期：<?php echo (empty($mpList[$idx]['begin_time']) ? '' : date('Y-m-d H:i:s', $mpList[$idx]['begin_time']))
                . ' - '
                . (empty($mpList[$idx]['end_time']) ? '' : date('Y-m-d H:i:s', $mpList[$idx]['end_time']))?></div>
                <div>调成：<?php echo empty($mpList[$idx]['to_price']) ? '' : $mpList[$idx]['to_price']?></div>
                <div>状态：<?php $st = array('未开始','调整成功','已恢复');echo !isset($mpList[$idx]['state']) ? '' : $st[$mpList[$idx]['state']]?></div>
                <div>限购数量：<?php echo empty($mpList[$idx]['limit_num']) ? '0' : $mpList[$idx]['limit_num']?></div>
                </td>
				<td style="text-align:center;vertical-align:middle;"><?php echo $item['m_user']?></td>
				<td style="text-align:center;vertical-align:middle;"><?php echo date('Y-m-d H:i:s', $item['mtime'])?></td>
				<td style="text-align:center;vertical-align:middle;">
					<button type="button" class="btn btn-primary span2" onclick="modifyGoodsInfo(<?php echo $item['id']?>, <?php echo $item['goods_id']?>,this, 1)" >修改库存</button>
					<button type="button" class="btn btn-primary span2" onclick="modifyGoodsInfo(<?php echo $item['id']?>, <?php echo $item['goods_id']?>,this, 2)" >修改价格</button>
					<button type="button" class="btn btn-primary span2" onclick="modifyBarCode(<?php echo $item['id']?>, <?php echo $item['goods_id']?>, <?php echo (empty($item['bar_code']) ? "''" : $item['bar_code'])?>, 3)" >修改条码</button>
					<button type="button" class="btn btn-danger span2" onclick="timeingModifyPrice(<?php echo $item['id']?>,
                    <?php echo empty($mpList[$idx]['state']) ? 0 :$mpList[$idx]['state']?>,
                    <?php echo empty($mpList[$idx]['synch_sale_price']) ? 0 :$mpList[$idx]['synch_sale_price']?>,
                    <?php echo $item['goods_id']?>,
                    <?php echo empty($mpList[$idx]['id']) ? 0 :$mpList[$idx]['id']?>,
                    <?php echo empty($mpList[$idx]['begin_time']) ? 0 : ("'" . date('Y-m-d H:i:s', $mpList[$idx]['begin_time']) . "'")?>,
                    <?php echo empty($mpList[$idx]['end_time']) ? 0 : ("'" . date('Y-m-d H:i:s', $mpList[$idx]['end_time']) . "'")?>,
                    <?php echo empty($mpList[$idx]['limit_num']) ? 0 : $mpList[$idx]['limit_num']?>,
                    <?php echo empty($mpList[$idx]['to_price']) ? 0 : $mpList[$idx]['to_price']?>, this)" >定时调价</button>
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
				<div class="form-group" id="barCode">
					<label class="col-sm-2 control-label no-padding-left"> 条码：</label>
					<div class="col-sm-9">
						<input type="text" name="barCode" id="barCodeV" class="span5">
					</div>
				</div>
				<div class="form-group" id="kucunAndPrice">
					<label class="col-sm-2 control-label no-padding-left"> 库存：</label>
					<div class="col-sm-9">
						<input type="text" name="newValue" id="newValue" class="span5">
					</div>
				</div>
				<div class="form-group" id="mpBeginTime">
					<label class="col-sm-2 control-label no-padding-left">开始时间：</label>
					<div class="col-sm-9">
						<input type="text" name="mpBeginTimeV" id="mpBeginTimeV" class="span5">
                        格式：2016-05-27 18:13:24
					</div>
				</div>
				<div class="form-group" id="mpEndTime">
					<label class="col-sm-2 control-label no-padding-left">结束时间：</label>
					<div class="col-sm-9">
						<input type="text" name="mpEndTimeV" id="mpEndTimeV" class="span5">
                        格式：2016-05-27 18:13:24
					</div>
				</div>
				<div class="form-group" id="mpLimitNum">
					<label class="col-sm-2 control-label no-padding-left">每人限购数量：</label>
					<div class="col-sm-9">
						<input type="number" name="mpLimitNumV" id="mpLimitNumV" class="span5">
                        <p style="margin-top:10px;"><span style="padding:4px;" class="label-warning">在本时间段内有效，可以不填，即为不限制</span></p>
					</div>
				</div>
				<div class="form-group" id="mpToPrice">
					<label class="col-sm-2 control-label no-padding-left">调整价格：</label>
					<div class="col-sm-9">
						<input type="text" name="mpToPriceV" id="mpToPriceV" class="span5">
                        <p style="margin-top:10px;"><span style="padding:4px;" class="label-warning">到期后自动恢复成开始调价前那一刻的价格</span></p>
					</div>
				</div>
				<div class="form-group" id="isShowPrice">
					<label class="col-sm-2 control-label no-padding-left"> 同步销售价：</label>
					<div class="col-sm-9">
                        <input type="checkbox" id="synchShowPrice" value="1"/>&nbsp;同步为商品销售价(未选SKU的展示价)
					</div>
				</div>

				<div class="form-group" id="mpState">
					<label class="col-sm-2 control-label no-padding-left">状态：</label>
					<div class="col-sm-9">
                    <div style="margin-right:20px;display:inline;">
                    <input type="radio" name="setstate" id="noreset" value="0">保持不变
                    </div>
                    <div style="margin-right:20px;display:inline;">
                    <input type="radio" name="setstate" id="reset" value="1">重置状态
                    </div>
                    <p id="stateDesc" style="margin-top:10px;"></p>
                    </div>
			</div>
			<div class="modal-footer">
                <input type="hidden" name="sku_id" value="" id="sku_id"/>
                <input type="hidden" name="mpriceId" value="" id="mpriceId"/>
                <input type="hidden" value="" id="type"/>
                <input type="hidden" name="goods_id" value="" id="goods_id"/>
				<button type="button" class="btn btn-primary" id="confirmsend-btn" name="confirmsend" value="yes">提交</button>      	
				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
			</div>
            </div>
		</div>
	</div>
	<!-- END -->
	</form>
	<script>
        $(document).ready(function(){
           $('#mpBeginTimeV,#mpEndTimeV').datetimepicker({
              format: "yyyy-mm-dd hh:ii:00",
              minView: "0",
              //pickerPosition: "top-right",
              autoclose: true
            });
        });
        function modifyBarCode(id, goodsId, barCode, type) {
            $('#type').val(type);
            $('#sku_id').val(id);
            $('#goods_id').val(goodsId);
            $('#barCodeV').val(barCode);
            $('#barCode').show();
            $('#mpBeginTime,#mpEndTime,#mpToPrice,#mpLimitNum,#mpState,#kucunAndPrice,#isShowPrice').hide();
            $('#modal-confirmsend').modal('show');
        };
        function modifyGoodsInfo(id, goodsId, e, type) {
            if (type == 1) {
                var title = '库存';
                var oldValue = $(e).closest('tr').find('td.amount').text();
                $('#isShowPrice').hide();
            } else if(type == 2) {
                var title = '价格';
                var oldValue = $(e).closest('tr').find('td.sale_price').text();
                $('#isShowPrice').show();
            }
            $('#modal-confirmsend .modal-title').eq(0).text('修改'+title);
            $('#modal-confirmsend .control-label').eq(0).text(title + '：');
            $('#newValue').val(oldValue);
            $('#type').val(type);
            $('#sku_id').val(id);
            $('#goods_id').val(goodsId);
            $('#kucunAndPrice').show();
            $('#mpBeginTime,#mpEndTime,#mpToPrice,#mpLimitNum,#mpState,#barCode').hide();
            $('#modal-confirmsend').modal('show');
        };
        function timeingModifyPrice(id,
            state,
            synchShowPrice,
            goodsId,
            mpriceId,
            beginTime,
            endTime,
            limitNum,
            toPrice,
            e) {
            var title = '定时调价';
            $('#modal-confirmsend .modal-title').eq(0).text('定时调价');
            $('#mpBeginTimeV').val(beginTime == 0 ? '' : beginTime);
            $('#mpEndTimeV').val(endTime == 0 ? '' : endTime);
            $('#mpToPriceV').val(toPrice);
            $('#mpLimitNumV').val(limitNum);
            $('#type').val(99993);
            if (state == 1) {
                $("#reset").attr("checked","checked");
                $("#stateDesc").text('当前状态：调整成功  (不可再编辑)');
            } else {
                if (state == 0) {
                    $("#noreset").attr("checked","checked");
                    $("#stateDesc").text('当前状态：未开始  (状态保持不变即可)');
                } else {
                    $("#reset").attr("checked","checked");
                    $("#stateDesc").text('当前状态：已恢复  (要想重新调整价格，需要将状态重置)');
                }
            }
            if (synchShowPrice)
                $("#synchShowPrice").attr("checked","checked");
            $('#sku_id').val(id);
            $('#goods_id').val(goodsId);
            $('#mpriceId').val(mpriceId);
            $('#kucunAndPrice,#barCode').hide();
            $('#mpBeginTime,#mpEndTime,#mpToPrice,#mpLimitNum,#mpState,#isShowPrice').show();
            $('#modal-confirmsend').modal('show')
        }
        $('#confirmsend-btn').click(function(){
            if ($('#type').val() == 99993) {
                var url = "/admin/Goods/modifyTimingMPrice";
                var data = {
                     skuId:$("#sku_id").val(), 
                     goodsId:$("#goods_id").val(),
                     mpriceId:$("#mpriceId").val(), 
                     mpBeginTime:$("#mpBeginTimeV").val(),
                     mpEndTime:$("#mpEndTimeV").val(),
                     mpLimitNum:$("#mpLimitNumV").val(),
                     setstate:$("#mpState input[name='setstate']:checked").val(),
                     synchShowPrice:($('#synchShowPrice:checked').length == 1 ? 1 : 0),
                     mpToPrice:$("#mpToPriceV").val()
                };
                $.post(url,data,function(data){
                    if(data.code==0) {
                        window.location.href= data.url;
                    } else {
                        alert(data.msg);
                        return false;
                    }
                },'json');
            } else if ($('#type').val() == 3) {
                var url = "/admin/Goods/modifyBarCode";
                var data = {
                     skuId:$("#sku_id").val(), 
                     goodsId:$("#goods_id").val(),
                     barCode:$("#barCodeV").val()
                };
                $.post(url,data,function(data){
                    if(data.code==0) {
                        window.location.href= data.url;
                    } else {
                        alert(data.msg);
                        return false;
                    }
                },'json');
            } else {
                var url = "/admin/Goods/" + (($('#type').val() ==1) ? 'modifyKuCun' : 'modifySalePrice');
                var param = $('#type').val() ==1 ? 'amount' : 'price';
                var data = {
                     id:$("#sku_id").val(), 
                     synchShowPrice:($('#synchShowPrice:checked').length == 1 ? 1 : 0),
                     goodsId:$("#goods_id").val()
                };
                data[param] = $('#newValue').val();
                $.post(url,data,function(data){
                    if(data.code==0) {
                        window.location.href= data.url;
                    } else {
                        alert(data.msg);
                        return false;
                    }
                },'json');
            }
        });
	</script>
</body>
</html>
