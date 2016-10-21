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
            'status'        => 0,
            'create_time'   => time(),
        ]);

        if(!$res){
            return false;
        }
        return $id;
    }

    //持久化临时文件
    public function persistence($appid, $ids){
        $args   = [$appid];
        $ids    = explode(',', $ids);
        $args   = array_merge($args, $ids);
        $sql    = 'UPDATE file SET status=1 WHERE app_id = ? AND id IN (' . array_fill(0, count($ids), '?') . ')';
        return app('db')->update($sql, $args);
    }

    //删除文件
    public function delete($appid, $ids){
        $args   = [$appid];
        $ids    = explode(',', $ids);
        $args   = array_merge($args, $ids);
        $sql    = 'UPDATE file SET status=0 WHERE app_id=? AND id IN (' . array_fill(0, count($ids), '?') . ')';
        return app('db')->update($sql, $args);
    }

    //删除临时文件,已被删除的文件, 以及长期超过七天未使用的缩略图文件
    public function clean(){
        if(time()%2 == 0){
            return $this->cleanInvalid();
        }else{
            return $this->cleanThumb();
        }
    }

    //删除临时文件或状态置为已被删除的文件
    public function cleanInvalid(){
        $where  = 'status=0 AND create_time < ?';
        $args   = [time()-3*86400];
        $sql    = 'SELECT * FROM file WHERE ' . $where;
        $item   = app('db')->selectOne($sql, $args);
        if(!$item){
            return false;
        }
        $sql    = 'DELETE FROM file WHERE id = ?';
        $args   = [$item->id];
        $res    = app('db')->delete($sql, $args);
        if(!$res){
            return false;
        }
        //删除文件
        $count = 3;
        while($count){
            $res = unlink($item->path . $item->id . '.' . $item->ext);
            if($res){
                break;
            }
            $count--;
        }
        return true;
    }

    //删除超过七天未访问的缩略图
    public function cleanThumb(){
        $sql    = 'SELECT * FROM file_thumb WHERE update_time < ?';
        $args   = [time()-7*86400];
        $item   = app('db')->selectOne($sql, $args);
        if(!$item){
            return false;
        }
        $sql    = 'DELETE FROM file_thumb WHERE id = ?';
        $args   = [$item->id];
        $res    = app('db')->delete($sql, $args);

        if(!$res){
            return false;
        }
        //删除文件
        $count = 3;
        while($count){
            $res = unlink($item->fullname);
            if($res){
                break;
            }
            $count--;
        }
        return true;
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
