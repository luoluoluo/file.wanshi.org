<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class AuthSign
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string|null              $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $gurad = null)
    {
        $sign = app('request')->input('sign');
        if (!$sign) {
            return Response('签名错误', 401);
        }
        $sign = explode('-', $sign);
        if(count($sign) != 3){
            return Response('签名错误', 401);
        }
        list($id, $key, $time) = $sign;

        $app = (new \App\Models\App())->getOne($id);

        //检验id
        if (!$app) {
            app('log')->info('sign_auth: ' . $id . ' 不存在的appid');
            return Response('签名错误', 401);
        }

        //检验key
        $data = app('request')->all();
        $dataStr = '';
        if(!empty($data)){
            foreach($data as $k=>$v){
                if($k == 'sign'){
                    unset($data[$k]);
                }
                //文件
                if($v instanceof \Illuminate\Http\UploadedFile){
                    $data[$k] = md5_file( $v->getPathname());
                }
            }
            ksort($data);
            foreach($data as $k=>$v){
                $dataStr .= sprintf('[%s:%s]', $k, $v);
            }
        }

        if($key != md5(trim(app('request')->path(), '/') . app('request')->method() . $dataStr . $app->secret . $time)){
            app('log')->info('sign_auth: ' . $id . ' sign不合法');
            return Response('签名错误', 401);
        }

        // 是否过期
        if (time() > $time + config('app.sign.expire')) {
            app('log')->info('sign_auth: ' . $id . ' sign过期');
            return Response('签名过期', 401);
        }

        $request->app = $app;

        return $next($request);
    }

}
