var settings = {
	flash_url : "/asset/js/swfupload/swf/swfupload.swf",
	upload_url: "http://img.dazemall.com/uploadimg.php",
	post_params: {
		"PHPSESSID" : "NONE",
		"HELLO-WORLD" : "Here I Am",
		".what" : "OKAY"
	},
	file_size_limit : "800KB",
	file_types : "*.jpg;*.jpeg;*.png;",
	file_types_description : "All Files",
	file_upload_limit : 30,
	file_queue_limit : 10,
	custom_settings : {
		progressTarget : "fsUploadProgress",
		cancelButtonId : "btnCancel",
		divStatus : "divStatus"
	},
	debug: false,

	// Button Settings
	button_image_url : "/asset/js/swfupload/images/XPButtonUploadText_61x22.png",
	button_placeholder_id : "spanButtonPlaceholder",
	button_width: 61,
	button_height: 22,

	// The event handler functions are defined in handlers.js
	swfupload_loaded_handler : swfUploadLoaded,
	file_queued_handler : fileQueued,
	file_queue_error_handler : fileQueueError,
	file_dialog_complete_handler : fileDialogComplete,
	upload_start_handler : uploadStart,
	upload_progress_handler : uploadProgress,
	upload_error_handler : uploadError,
	upload_success_handler : uploadSuccess,
	upload_complete_handler : uploadComplete,
	queue_complete_handler : queueComplete,	// Queue plugin event
	
	// SWFObject settings
	minimum_flash_version : "9.0.28",
	swfupload_pre_load_handler : swfUploadPreLoad,
	swfupload_load_failed_handler : swfUploadLoadFailed
};

/*对象深拷贝*/
function deepCopy_settings(source) { 
  var result = {};
  for (var key in source) {
      result[key] = typeof source[key]==='object' ? deepCopy_settings(source[key]) : source[key];
   } 
   return result; 
}

//复制配置文件
var settings1 = deepCopy_settings(settings);
var settings2 = deepCopy_settings(settings);
settings2.custom_settings.progressTarget = "fsUploadProgress2";
settings2.custom_settings.cancelButtonId = "btnCancel2";
settings2.button_placeholder_id = 'spanButtonPlaceholder2';

var settings3 = deepCopy_settings(settings);
settings3.custom_settings.progressTarget = "fsUploadProgress3";
settings3.custom_settings.cancelButtonId = "btnCancel3";
settings3.button_placeholder_id = 'spanButtonPlaceholder3';

SWFUpload.onload = function() {
	var swfu = new SWFUpload(settings1);
    var currAction = window.location.href;
    if(currAction.indexOf('/admin/Goods/addPage')>0
      ||currAction.indexOf('/admin/Goods/editPage')>0  
    ) {
     	var swfu2 = new SWFUpload(settings2);
    }
    if(currAction.indexOf('/admin/Activity/addPage')>0
      || currAction.indexOf('/admin/Activity/editPage')>0
    ) {
        var swfu2 = new SWFUpload(settings2);
        var swfu3 = new SWFUpload(settings3);
    }
}

function preview($filePath, file) {
   	var obj = eval("("+$filePath+")");
   	var imgPath = obj['0'];	
	//商品图
	if(file.id.indexOf('_0_') != -1) {
		$('.thumb_img').attr('value',imgPath);
        if ($('#prev_thumb_img img').length > 0) {
           $('#prev_thumb_img img').attr('src',imgPath); 
        } else { 
           $('#prev_thumb_img').append("<img style='width:100px;height:120px;' src='"+imgPath+"'/><a href='javascript:void(0)' onclick='delThumbImg(this);return false;'>删除</a>");
        }
	//商品缩略图
	} else if (file.id.indexOf('_1_') != -1) {
		$('#prev_goods_img ul').append("<li><img src='" + imgPath + "' style='width:100px;height:120px;margin-right:2px;' /><a onclick='delGoodsImg(this);return false;' href='javascript:void(0)'>删除</a></li>");
		$('#goods_img').append('<input type="hidden" name="goods_img[]" value="'+imgPath+'">');
	} else if (file.id.indexOf('_2_') != -1) {
		$('.thumb_img2').attr('value',imgPath);
        if ($('#prev_thumb_img2 img').length > 0) {
           $('#prev_thumb_img2 img').attr('src',imgPath); 
        } else { 
           $('#prev_thumb_img2').append("<img style='width:100px;height:120px;' src='"+imgPath+"'/><a href='javascript:void(0)' onclick='delThumbImg2(this);return false;'>删除</a>");
        }
	}
}
