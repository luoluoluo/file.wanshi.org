<?php
namespace App\Models;

class File
{
    /**
     * 获取单个文件信息
     */
    public function getOne($id)
    {
        return app('db')->table('file')
            ->where('id', $id)
            ->first();
    }

    public function create($appid, $file)
    {
        $id     = $this->uuid();
        $ext    = $file->getClientOriginalExtension() ? $file->getClientOriginalExtension() : $file->extension();
        $name   = $id . '.' . $ext;
        $dir    = realpath(config('app.file.path')) . '/' . date('Ymd') . '/' . substr($id, 0, 2) . '/';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $file->move($dir, $name);

        // 保存至数据库
        $res = app('db')->table('file')->insert([
            'id'            => $id,
            'app_id'        => $appid,
            'path'          => $dir,
            'size'          => $file->getClientSize(),
            'ext'           => $ext,
            'md5'           => md5_file($dir . $name),
            'create_time'   => time(),
        ]);

        if(!$res){
            return false;
        }
        return $id . '.' . $ext;
    }

    //删除文件
    public function delete($appid, $id){
        $args   = [$appid, $id];
        $sql    = 'UPDATE file SET status=0 WHERE app_id=? AND id = ?';
        return app('db')->update($sql, $args);
    }

    //缩略图，居中裁剪
    public function thumb($file, $width, $height){
        $fullname   = $file->path . $file->id . '_' . $width . 'x' . $height . '.' . $file->ext;
        $sql        = 'REPLACE INTO file_thumb(id, fullname, update_time) VALUES(?,?,?)';
        $args       = [$this->uuid(), $fullname, time()];
        app('db')->insert($sql, $args);
        if(is_file($fullname)){
            return $fullname;
        }
        app('image')->make($file->path . $file->id . '.' .$file->ext)
            ->fit($width, $height)
            ->save($fullname);
        return $fullname;
    }

    public function uuid(){
        $item   = app('db')->selectOne('SELECT UUID() AS uuid');
        if(isset($item->uuid)){
            return $item->uuid;
        }
        $charid = strtolower(md5(uniqid('', true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return $uuid;
    }
}
