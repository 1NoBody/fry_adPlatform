<?php
namespace app\controller;

use app\BaseController;
use think\facade\Request;


class Other extends BaseController{
    protected $middleware = ['check'=> ['except' => [''],'only' => ['uploadimg']]];

    public function uploadimg()
    {
        $data = Request::param();
        if (!empty($data['img'])) {
            $reg = '/data:image\/(\w+?);base64,(.+)$/si';
            preg_match($reg, $data['img'], $match_result);
            //return json(['img'=>$match_result]);
            //var_dump($match_result);
            //die;
            $file_name = time().'.'.$match_result[1];
            $logo_path = 'uploadimg/'.$file_name;
            $num = file_put_contents($logo_path, base64_decode($match_result[2]));
            if (!empty($num)) {
                $imgUrl = 'http://'.$_SERVER['HTTP_HOST'].'/'.$logo_path;
                return json(['code'=>20000,'msg'=>'图片上传成功','data'=>['img_url'=>$imgUrl]]);
            } else {
                return json(['code'=>50000,'msg'=>'图片上传失败']);
            }
        } else {
            return json(['code'=>50000,'msg'=>'文件错误']);
        }
    }
}