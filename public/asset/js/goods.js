/**
 *获取商品分类 
 *cate_id：当前分类ID 
 *cate_name：分类名称
 *type：0、一级 1、二级 2、三级
 */
var getCategory = function(cate_id, type, e){
	var url = '/admin/GoodsCategory/getCat';  //请求url
	$.post(url,{cateId:cate_id},function(data){
		var html = '';
		if (data.code == 0) {
			switch (type){
			case 0:
				$('.category div').remove();
				break;
			case 1:
                $(e).parent().parent().find('a').removeClass('show');
                $(e).addClass('show');
				$('.category_2,.category_3').remove();
				break;
			case 2:
			    $('.category_3').remove();
				break;
			}
            if (data.result.category && data.result.category.length>0){
			   html += '<div class="category_' + (type + 1) + '"><dl>';
			   $.each(data.result.category,function(i,v){
				  html += '<dd><a href="javascript:void(0)"';
			  	  if (type == 1) {
				  	 html += ' onclick="setCategory(this);" data-cid="'+v.id+'" class="show">'+v.cate_name+'</a></dd>';
				  } else {
				  	 html += ' onclick="getCategory('+ v.id+','+ (type+1) +',this);">'+v.cate_name+'</a></dd>';
				  }
               });
			   html += '</dl><br /></div>';
			   $('.category').append(html);
            }
			return ;
		}
		alert(data.msg);
	},'json')
}

var setCategory = function(e) {
	var cid = $(e).attr('data-cid');
	var cname = $(e).html();
	$('#cateid').val(cid);
	$('#chooseCategory').html(cname);
	$('.category div').remove();
}

var delThumbImg = function(e) {
      if(confirm("确认要删除该图片吗？")) {
          $(e).parent().empty();
          $('#thumb_img').val('');
       }
}

var delThumbImg2 = function(e) {
      if(confirm("确认要删除该图片吗？")) {
          $(e).parent().empty();
          $('#thumb_img2').val('');
       }
}

var delGoodsImg = function(e) {
      if(confirm("确认要删除该图片吗？")) {
            var img_url = $(e).prev().attr('src');
            $('#goods_img input').each(function(i,v){
                 if($(v).val() == img_url) {
                    $(v).remove();
                    $(e).parent().remove();
                    return ;
                 }
        });      
     }
}

function getSkuAttr(e) {
    var sku_id = $(e).attr('sku-id');
    var sku_value = $(e).parent().text();
    var url = '/admin/SkuValue/getSkuValue?attrId='+sku_id;
    $.post(url,{},function(data){
        if(data.code == 0) {
          var html = '<label class="title">'+sku_value+'</label><br />';
          $.each(data.result,function(i,v){
              html += '<label class="checkbox inline">';
              html += '<input type="checkbox" onclick="createTable(false);" value="'+v.id+'">'+v.value+'</label>';
              html += '</label>';
          });
          $('.sku_attr').empty().append(html);
        }
    },'json');
}

function createTable(readOnly,skuValue) {
    var sku_attr = $('.sku_attr label.title').text();
    skuValue = !skuValue ? {} : skuValue;
    var html = '<table class="table table-bordered">';
    html += '<thead><tr><th>'+sku_attr+'</th><th width="100">价格</th><th width="100">库存</th><th>条码</th></tr></thead><tbody>';
    $('.sku_attr input:checkbox:checked').each(function(i,e){
       var title = $(e).parent().text();
       html += '<tr><td >'+title+'</td><td><input type="text"';
       if (!!skuValue[title]){
          var price_amount = skuValue[title].split(':');
       }
       if (readOnly == true && !!price_amount) {
         html += ' readonly="readonly" value="'+price_amount[0]+'"';
       }
       html += ' ></td><td><input type="text"';
       if (readOnly == true && !!price_amount) {
         html += ' readonly="readonly" value="'+price_amount[1]+'"';
       }
       html += ' ></td><td><input type="text"';
       if (readOnly == true && !!price_amount) {
         html += ' readonly="readonly" value="'+price_amount[2]+'"';
       }
       html += ' ></td></tr>';
    });
    html += '</tbody></table>'; 
    $('.sku_table').empty().append(html);
}
