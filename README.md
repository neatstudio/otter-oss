#代码来自于 https://github.com/apollopy/flysystem-aliyun-oss

#说明
* 本来叫：otter/oss，但otter已经有人用了。只能用neatstudio了
* 该代码本来也就是自用

#使用方式（本地模式）
* 将代码拷到指定目录，如根目录下 的xxx，当前文件的路径 为 xxx/oss
* 在composer.json里加入
```json
"repositories":[
        {
            "type":"path",
            "url":"app/Plugins/oss",
            "options":{
                "symlink":true
            }
        },
]
```
* 在composer.json的require中加入
```
"neatstudio/otter-oss":"^1.0",
```
* 执行composer update即可

###配置文件
* 在config/filesystems.php 加入
```php
        'oss' => [
            'driver'         => 'oss',
            'ram_arn'        => env('ALIYUN_RAM_ARN', null),
            'app_key'        => env('OSS_APP_KEY', null),
            'app_secret'     => env('OSS_APP_SECRET', null),
            'policy_expired' => env('ALIYUN_RAM_ARN_EXPIRED', 3600),
            'access_id'      => env('OSS_ACCESS_ID', 'your id'),
            'access_key'     => env('OSS_ACCESS_KEY', 'your key'),
            'bucket'         => env('OSS_BUCKET', env('OSS_PUBLIC_BUCKET','your bucket')),
            'endpoint'       => env('OSS_ENDPOINT', 'your endpoint'),
            'prefix'         => env('OSS_PREFIX', ''), // optional
            'url'            => env('OSS_PUBLIC_URL', ''),
        ],
```
* 如果要默认就设置 'default'=>'oss'

### .env中增加
```dotenv
#aliyun
ALIYUN_RAM_ARN=
#授权过期时间
ALIYUN_RAM_ARN_EXPIRED=900
ALIYUN_REGION=cn-shanghai
OSS_APP_KEY=
OSS_APP_SECRET=
OSS_REGION=oss-cn-shanghai
OSS_ENDPOINT=oss-cn-shanghai.aliyuncs.com
OSS_INTERNAL_ENDPOINT=oss-cn-shanghai-internal.aliyuncs.com

OSS_BUCKET=xx-public
OSS_PUBLIC_URL=//xx--shanghai.aliyuncs.com/
```

### 其他用法
```php
$adapter = new AliOssAdapter($config);
$flysystem = new League\Flysystem\Filesystem($adapter);
```
```php
Storage::disk('oss')->xxx()
```

