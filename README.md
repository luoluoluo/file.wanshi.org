## 介绍

一个支持客户端直传，安全的文件存储服务


## 开始使用

### 签名算法

```
    appid: id
    appsecret: 密码
    time: 过期时间
    //签名
    function sign($path, $method='GET', $data = []){
        $dataStr = '';
        if(!empty($data)){
            foreach($data as $k=>$v){
                //文件
                if(is_file($v)){
                    $v = md5_file($v);
                }
            }
            ksort($data);
            foreach($data as $k=>$v){
                $dataStr .= sprintf('[%s:%s]', $k, $v);
            }
        }
        return $appid . '-' . md5(trim($path, '/') . strtoupper($method) . $dataStr . $appsecret . $time) . '-' . $time;
    }
```

### 上传

1.获取上传token（业务服务器发起）

```
    request：
    url: http://file.wanshi.org/token
    method: POST
    data: sign: 签名;

    response：
    token
```

2.上传（客户端直接向文件服务器上传临时文件）

```
    request：
    url: http://file.wanshi.org/file
    method: POST
    data: file: 文件，token: 上传token;

    response：
    200, 文件id
```


3.持久存储文件（业务服务器将临时文件持久化） 考虑到客户端直传会产生大量无效文件

```
    请求：
    url: http://file.wanshi.org/persistence-file
    method: PUT
    data: sign: 签名，ids: 逗号隔开的文件id;

    返回：
    200, ok
```



### 下载/查看

```
    请求：
    url: http://file.wanshi.org/filei/{file_id}
    method: GET
    data: sign: 签名，size: 缩略图 eg:200; 200x300, type: (inline:查看； attachement:下载；);

    返回：
    200, 文件内容
```


### 删除

```
    request：
    url: http://file.wanshi.org/file
    method: DELETE
    data: sign: 签名，ids: 逗号隔开的文件id;

    response：
    200, ok

```

