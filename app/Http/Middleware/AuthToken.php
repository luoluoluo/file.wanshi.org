<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class AuthToken
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
        $time  = time();
        $token = $request->input('token');

        // 非空
        if (!$token) {
            return Response('无效token4', 401);
        }
        $token = decrypt($token);
        $token = explode("\n", $token);

        if(count($token) !=3){
            app('log')->info('token_auth: ' . $id . ' token不合法');
            return Response('无效token3', 401);
        }
        list($id, $secret, $expire) = $token;

        $app = (new \App\Models\App())->getOne($id);

        if(empty($app)){
            app('log')->info('token_auth: ' . $id . ' token不合法');
            return Response('无效token2', 401);
        }

        if($app->secret != $secret){
            app('log')->info('token_auth: ' . $id . ' token不合法');
            Response('无效token1', 401);
        }

        // 是否过期
        if ($time > $expire) {
            return Response('token 已过期', 401);
        }
        $request->app = $app;
        return $next($request);
    }
}
