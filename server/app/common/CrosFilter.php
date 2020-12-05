<?php
namespace app\common;


class CrosFilter
{

    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
        //OPTIONS请求返回204请求
        if ($request->method(true) === 'OPTIONS') {
            $response->code(204);
        }
        $response->header([
            'Access-Control-Allow-Origin'      => $origin,
            'Access-Control-Allow-Methods'     => 'GET,POST,PUT',
            'Access-Control-Allow-Headers'     => 'X-Token,token, Origin, X-Requested-With, Content-Type, Accept, Authorization,Content-Type',
            'Access-Control-Allow-Credentials' => 'true'
        ]);

        return $response;
    }
    /*
     * 中间结束调度
     */

}