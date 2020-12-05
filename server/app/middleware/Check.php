<?php
declare (strict_types = 1);

namespace app\middleware;

use think\facade\Session;


class Check
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        //判断用户是否已经登录
        if (!Session::get('user_id')) {
            return json(['code'=>50000,'msg'=>'用户未登录，请先登录']);
        }else{
            return $next($request);
        }
    }
}
