<?php
namespace App\Models;

class App
{
    public function __construct()
    {
    }

    public function getOne($id)
    {
        return app('db')->table('app')
            ->where('id', $id)
            ->first();
    }

}
