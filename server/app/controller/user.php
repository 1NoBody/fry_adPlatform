<?php
namespace app\controller;

use app\BaseController;
use app\controller\CommonController;
use think\exception\HttpException;
use think\facade\Db;
use think\facade\Request;
use think\captcha\facade\Captcha;
use think\facade\Session;
use app\model\User as MUser;
use app\model\Menu;
use app\constant\RoleConstant;

class User extends BaseController
{
    protected $middleware = ['check'=> ['except' => [''],'only' => ['changePsw','']]];
    /**
     * 登录
     */
    public function login()
    {
        $username = Request::param('username');
        $password = Request::param('password');
        if ($res = Db::table('ad_user')->where('username', $username)->where('password', $password)->limit(1)->find()) {
            Session::set('user_id', $res['id']); // 保存用户登录信息
            $sessionId=$this->uuid();  // 随机唯一字符串作为token              
            $token=['token'=>$sessionId]; 
            return json(['code'=>20000,'msg'=>'登录成功','data' => $token]);
        } else {
            return json(['code'=>50000,'msg'=>'密码错误']);
        }
    }

    /**
     * 获取用户信息
     */
    public function info(){
        $userId= Session::get("user_id");
        //$userId = 1;
        if(empty($userId)){
            return json(['code'=>50000,'msg'=>'账户失效，请重新登录']);
        }else{
            $user=MUser::where("id",$userId)->field(['id','username','role'])->find();
            $menus=$this->getMenus($user);
            return json(['code'=>20000,'msg'=>"获取用户信息成功",'data'=>$user,'menus'=>$menus]);
        }
     }


    /**
     * 退出登录
     */
    public function logout(){
       Session::delete('user_id');
       return json(['code'=>20000]);
    }

    /**
     * 注册
     */
    public function register()
    {
        $username = Request::param('username');
        $password = Request::param('password');
        $nickname = "普通用户";
        $data = ['username'=>$username,'password'=>$password,'nickname'=>$nickname];
        try {
            if (Db::table('ad_user')->where('username', $username)->limit(1)->find()) {
                return json(['code'=>50000,'msg'=>"该用户已存在"]);
            }
            if (Db::table('ad_user')->strict(false)->insert($data)) {
                return json(['code'=>20000,'msg'=>"恭喜你，注册成功！"]);
            }
        } catch (\Throwable $th) {
            throw new HttpException(500, "服务器错误");
        }
    }

    /**
     * 修改密码
     */
    public function changePsw(){
        $user_id = session('user_id');
        $psw = Request::param('password');
        if(Db::table('ad_user')->where('id',$user_id)->update(['password'=>$psw])){
            return json(['code' => 20000,'msg' => '密码修改成功']);
        }else{
            return json(['code' => 50000,'msg' => '密码修改失败']);
        }
    }

    /**
     * 验证码获取和校验
     */
    public function verify()
    {
        $value = Request::param('captcha');
        if (!$value) {
             return Captcha::create();
        }
        if (!captcha_check($value.'')) {
            // 验证失败
            return json(['code'=>50000,'msg'=>'验证码错误']);
        }
        return json(['code'=>20000,'msg'=>'验证码正确']);
    }


    /**
     * 获取这个用户的菜单
     *  */ 
    public function getMenus($user){
        $userId= Session::get("user_id");
        $list=[];
        if(RoleConstant::$ADMIN==$user['role']){ //如果是管理员，查出所有菜单
            $list=Menu::where([])->select();
        }else{
           //如果是普通用户，根据权限表查出菜单
           $list=Db::table('ad_menu')
           ->alias('menu')
           ->leftJoin('ad_auth auth','menu.id = auth.menu_id')
           ->where("auth.role",$user['role'])
           ->field("menu.*")
           ->select();
        }

        $menus=[];
        $childMenu=[];
        $parentMenu=[];
        foreach($list as $item){
           if($item["parent_id"]>0){
            $childMenu[]=$item;
           }else{
            $parentMenu[]=$item;
           }
         // $menuItem=['path'=>'/'.$item["url"],'component'=>'Layout','name'=>$item["menu_name"],'meta'=>['title'=>$item["menu_name"],]];
        }
         
        foreach($parentMenu as $item){
          $menuItem=['path'=>'/'.$item["url"],'component'=>'Layout'];
          $hasChild=false;
          $childArr=[];
           foreach($childMenu as $citem){
               if($citem["parent_id"]==$item["id"]){
                $hasChild=true;
                $childArr[]=['path'=>$citem['url'],'name'=>$citem['url'],'component'=>$citem['path'],'meta'=>['title'=>$citem['menu_name'],'icon'=>'dashboard']];
               }           
           }
           if(!$hasChild){
            $childArr[]=['path'=>$item['url'],'name'=>$item['url'],'component'=>$item['path'],'meta'=>['title'=>$item['menu_name'],'icon'=>'dashboard']];
            $menuItem['redirect']="/".$item["url"]; 
            $menuItem['children']=$childArr; 
           }else{
            $menuItem['redirect']="noRedirect"; 
            $menuItem["name"]=$item["menu_name"];
            $menuItem["meta"]=['title'=>$item["menu_name"],'icon'=>'dashboard'];
            $menuItem['children']=$childArr;  
           }            
           $menus[]=$menuItem;
        }
       return $menus;
    }

    /**
     * 生成token
     */
    function uuid()  
    {  
        $chars = md5(uniqid(mt_rand(), true));  
        $uuid = substr ( $chars, 0, 8 ) . '-'
                . substr ( $chars, 8, 4 ) . '-' 
                . substr ( $chars, 12, 4 ) . '-'
                . substr ( $chars, 16, 4 ) . '-'
                . substr ( $chars, 20, 12 );  
        return $uuid ;  
    }  
}
