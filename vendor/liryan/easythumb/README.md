#使用说明

#安装
composer require "liryan/easythumb" "dev-master"

#使用方法

1.在config/app.php 
增加provider
```
providers' => [
...
EasyThumb\EasyThumbProvider::class
];
```
增加facade
```
'aliases' => [
...
'EasyThumb'=>EasyThumb\EasyThumb::class
];
```

2.在controller中使用
开头增加
```
use EasyThumb\EasyThumb;
use Exception;


function upload_test()
{
    try{
        $url=EasyThumb::upload("upfile")           //上传文件表单字段名
        ->where(public_path().'/upimages')         //文件要存放的路径
        ->autodir(EasyThumb::SHA1_DIR)             //自动创建目录sha1(file) 取前４个字符增加两级目录 TIME_DIR 年月/日小时
        ->limit(1000*1000,EasyThumb::PNG|EasyThumb::GIF|EasyThumb::JPG)  //限制尺寸字节，格式，
        ->size(100,100,EasyThumb::SCALE_FREE)       //自由缩放  size可以调用多个
        ->size(200,200,EasyThumb::SCALE_PROJECTIVE) //等比缩放
        ->done();
    }
    catch(Exception $e){
        echo $e->getMessage();
    }
}

function local_test()
{
    try{
        $url=EasyThumb::from(public_path().'/upimages/filename.jpg')    //要处理的文件路径
        ->size(100,100,EasyThumb::SCALE_FREE)       //自由缩放  size可以调用多个
        ->size(200,200,EasyThumb::SCALE_PROJECTIVE) //等比缩放
        ->done();
    }
    catch(Exception $e){
        echo $e->getMessage();
    }
}
```
