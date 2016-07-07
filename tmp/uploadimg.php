<?php

//获取存储目录 兼容百度编辑器
if (isset($_GET['fetch'])) {
    header('Content-Type: text/javascript');
    echo 'updateSavePath(' . json_encode(array('images')) .');';
    exit;
}

if (isset($_FILES['Filedata'])
    || !is_uploaded_file($_FILES['Filedata']['tmp_name'])
    || $_FILES['Filedata']['error'] != 0) {

    $uploadFile = $_FILES['Filedata'];
    $fileInfo   = pathinfo($uploadFile['name']);
    $fileType   = $fileInfo['extension'];
    if (!in_array($fileType,  array('jpg', 'png', 'jpeg', 'bmp', 'gif'))) {
        exit('format error');
    }

    $dir = 'images/';

    $dirImgDes = $dir . date('ymd');
    if (!is_dir($dirImgDes)) {
        if (mkdir($dirImgDes) === false) {
            exit('error');
        }
    }
    $fileName = md5(uniqid($_FILES['Filedata']['name']) . $dirImgDes);
    $save = $dirImgDes . '/' . $fileName . '.' . $fileInfo['extension'];
    $name = $_FILES['Filedata']['tmp_name'];
    if (move_uploaded_file($name, $save)) {
        $cdnNum = ord($fileName) % 4;
        $imgUrl = 'http://cdn'. $cdnNum . '.dazemall.com/' . $save;
        if (filesize($save) > 0) {
            //图片压缩
            if (isset($_GET['isCompress'])
                && $_GET['isCompress'] == 1
                && (int)(filesize($save) / 1024) > 50) {
                require_once 'libraries/image.class.php';
                $img = new image($save);
                $width = $img->width() * 0.75;
                $height = $img->height() * 0.75;
                $img->thumb($width, $height);
                $img->save($save);
            }
            if (isset($_GET['type']) && $_GET['type'] == 'ueditor') {
                //兼容百度编辑器
                echo "{'url':'" . $imgUrl ."','title':'" . $fileName
                    . "','original':'" . $_FILES["Filedata"]['name']
                    . "','state':'SUCCESS'}";
            } else {
                echo json_encode(array($imgUrl));
            }
        } else {
            exit('error');
        }
    }
}
?>
