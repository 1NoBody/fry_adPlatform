<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\Request;

class Company extends BaseController
{
    protected $middleware = ['check'=> ['except' => [''],'only' => ['auth','findAll','findById','audit']]];

    public function auth()
    {
        $user_id = session('user_id');
        $req = Request::param();
        if(!empty($req["user_id"])){
            $data = ['company'=>$req['company'],'name'=>$req['name'],'telephone'=>$req['telephone'],
            'vx'=>$req['vx'],'email'=>$req['email'],'license'=>$req['license'],
            'license_img'=>$req['license_img'],'audit'=>1,'user_id'=>$user_id];
            // $ret = Db::table('ad_company')->strict(false)->save($data);  // insert TD:save并无更新???
            $ret = Db::table('ad_company')->where("user_id",$user_id)->strict(false)->save($data);  // insert TD:save并无更新???
            return json(['code'=>20000,'msg'=>'更新成功']);
        }else{
            $data = ['company'=>$req['company'],'name'=>$req['name'],'telephone'=>$req['telephone'],
            'vx'=>$req['vx'],'email'=>$req['email'],'license'=>$req['license'],
            'license_img'=>$req['license_img'],'audit'=>1,'user_id'=>$user_id];
            $ret = Db::table('ad_company')->strict(false)->save($data);  // insert TD:save并无更新???
            // $ret = Db::table('ad_company')->strict(false)->save($data);  // insert TD:save并无更新???
            return json(['code'=>20000,'msg'=>'创建成功']);
        }
       
        
           
        
    }

    public function findAll(){
        $num = Request::param('num');
        $page = Request::param('page');
        $list = Db::name('company')->paginate(['list_rows'=>$num,'page'=>$page]);
        return json(["code" => 20000,"msg" => "查询成功","lists" => $list]);
    }

    public function findByUserId(){
        $id = Request::param('id');
        $res = Db::table('ad_company')->where('user_id',$id)->limit(1)->find();  // TD:兼容user_id 和 企业id : 一个user_id 对应 一个企业id
        return json(['code'=>20000,'msg'=>"查询成功",'data'=>$res]);
    }

    public function audit(){
        $req = Request::param();
        if(Db::table('ad_company')->where('id',$req['id'])->update(['audit'=>$req['audit'],'reason'=>$req['reason']])){
            return json(['code'=>20000,'msg'=>'操作成功']);
        }else{
            return json(['code'=>50000,'msg'=>'操作失败']);
        }
    }
}
