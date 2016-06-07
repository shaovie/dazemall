var settings = {
	flash_url : "/asset/js/swfupload/swf/swfupload.swf",
	upload_url: "http://testimg.taojinzi.com/uploadimg.php?wq_key=0a749c0e333727b0ee70333d2db3fd03",
	post_params: {
		"PHPSESSID" : "NONE",
		"HELLO-WORLD" : "Here I Am",
		".what" : "OKAY"
	},
	file_size_limit : "10 MB",
	file_types : "*.jpg;*.jpeg;*.png;*.bmp;*.gif",
	file_types_description : "All Files",
	file_upload_limit : 3,
	file_queue_limit : 0,
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
var reg = /[goodsAction|shareAction]/;
if(reg.test(window.location.href)) {
	settings1.file_upload_limit = 9;
	var settings2 = deepCopy_settings(settings);
	var settings3 = deepCopy_settings(settings);	
	settings2.custom_settings.progressTarget = "fsUploadProgress2";
	settings2.custom_settings.cancelButtonId = "btnCancel2";
	settings2.button_placeholder_id = 'spanButtonPlaceholder2';
	settings3.custom_settings.progressTarget = "fsUploadProgress3";
	settings3.custom_settings.cancelButtonId = "btnCancel3";
	settings3.button_placeholder_id = 'spanButtonPlaceholder3';
}

SWFUpload.onload = function () {
	var swfu = new SWFUpload(settings1);
	if (reg.test(window.location.href)) {
		var swfu2 = new SWFUpload(settings2);
		var swfu3 = new SWFUpload(settings3);
	}
}

function preview($filePath,file) {
   	var obj = eval("("+$filePath+")");
   	var imgPath = obj['0'];	
	var action = window.location.href;
	var reg = /[goodsAction|shareAction]/;
	var html = '';
	if(reg.test(action)) {
		//商品图
		if(file.id.indexOf('_0_')!=-1) {
			$('.prev_img').append("<li><img src='" + imgPath + "' /><a href='" + imgPath + "'>删除</a></li>");
			$('#imgs').append('<input type="hidden" name="img_url[]" value="'+imgPath+'">');
		//商品缩略图
		}else if (file.id.indexOf('_1_') != -1) {
			$('#thumb_img').attr('value', imgPath);
			$('.prev_thumb_img img').attr('src', imgPath);
	    //商品热销图
		}else if (file.id.indexOf('_2_') != -1) {
		   $('#big_img').attr('value', imgPath);
		   $('.prev_big_img img').attr('src', imgPath);
	    }		
	} else {
	   //单张图片
       $('#img_url').attr('value',imgPath);
	   $('.prev_img img').attr('src',imgPath);
	}  
}