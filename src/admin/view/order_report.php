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
</head>
<body class="no-skin">
	<form action="/admin/Banner/search" class="form-horizontal" method="get">
	<table class="table table-striped table-bordered table-hover" style="margin-top:10px;">
	<tbody>
		<tr>
			<td>
				<li style="float:left;list-style-type:none;">
					<span>下单时间</span>
		 		    <input name="beginTime" id="beginTime" type="text" value="" > - <input id="endTime" name="endTime" type="text" value="" >		
                    <input type="button" name="undelivery" value="查询" class="btn btn-sm btn-info" onclick="search();">
				</li>
			</td>
		</tr>
	</tbody>
	</table>
	</form>
	<table class="table table-striped table-bordered table-hover">
		<tbody>
		<tr>
            <th class="text-left" style="width:100px;" width="100">&nbsp;&nbsp;&nbsp;&nbsp;类别<br />日期</th>	
			<th class="text-center" style="width:200px;vertical-align:middle;">销售额</th>
			<th class="text-center" style="vertical-align:middle;">订单数</th>
		</tr>
        <?php foreach($data as $val):?>
		<tr>
             <td style="text-align:center;vertical-align:middle;">
                <?php echo $val['ctime'];?>
             </td>
             <td style="text-align:left;vertical-align:middle;">
                 <?php echo $val['seller_amount'];?>
             </td>
             <td style="text-align:left;vertical-align:middle;">
               <?php echo $val['order_num'];?>
             </td>
		</tr>
        <?php endforeach;?>
		</tbody>
	</table>
	<script>
        $(document).ready(function(){
           $('#beginTime,#endTime').datetimepicker({
              format: "yyyy-mm-dd",
              minView: "month",
              //pickerPosition: "top-right",
              autoclose: true
            });
        });
    function search() {
        btime = $('#beginTime').val();
        etime = $('#endTime').val();
        var url = '/admin/Report/order?btime=' + btime + '&etime=' + etime;
        window.location.href = url;
    }
	</script>
</body>
</html>
