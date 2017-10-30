## 介绍

简单的文件存储服务


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


```
    request：
    url: http://file.wanshi.org/file
    method: POST
    data: file: 文件，sign: 签名;

    response：
    200, 文件id
```


### 下载/查看

```
    请求：
    url: http://file.wanshi.org/file/{file}
    method: GET
    data: sign: 签名，size: 缩略图 eg:200; 200x300, type: (inline:查看； attachement:下载；);

    返回：
    200, 文件内容
```


### 删除

```
    request：
    url: http://file.wanshi.org/file/{file}
    method: DELETE
    data: sign: 签名

    response：
    200, ok

```

