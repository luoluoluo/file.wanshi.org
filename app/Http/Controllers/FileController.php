<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function __construct()
    {
        $this->fileModel = new \App\Models\File();
    }

    //下载或查看文件
    public function index(Request $request, $id, $ext)
    {
        //inline:查看； attachement:下载；
        $type = $request->input('type');
        $type = in_array($type, array('inline', 'attachment')) ? $type : 'inline';
        $item = $this->fileModel->getOne($id);
        //404
        if (empty($item)) {
            return Response('文件不存在', 404);
        }
        if ($item->app_id != $request->app->id) {
            return Response('无权访问', 403);
        }
        //缩略图
        $size       = $request->input('size');
        $fullname   = $item->path . $item->id . '.' .$item->ext;
        if($size){
            list($width, $height) = strpos($size, 'x') === false ? [$size, $size] : explode('x', $size);
            $fullname = $this->fileModel->thumb($item, $width, $height);
        }
        return Response()->download($fullname, null, [], $type);
    }

    //上传文件
    public function create(Request $request)
    {
        $file = $request->file('file');
        // no file
        if (empty($file)) {
            return Response('请选择文件');
        }
        $res = $this->fileModel->create($request->app->id, $file);

        if(!$res){
            return Response('上传失败', 500);
        }
        return Response($res, 200);
    }

    //删除文件
    public function destroy(Request $request, $id, $ext)
    {
        $res = $this->fileModel->delete($request->app->id, $id);
        if (!$res) {
            return Response('删除失败', 404);
        }
        return Response('删除成功', 200);
    }
}
