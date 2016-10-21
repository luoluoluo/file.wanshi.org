<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function __construct()
    {
        $this->appModel = new \App\Models\App();
    }

    public function create(Request $request)
    {
        $token = encrypt(sprintf("%s\n%s\n%s", $request->app->id, $request->app->secret, time()+config('app.token.expire')));
        return Response($token, 200);
    }
}
