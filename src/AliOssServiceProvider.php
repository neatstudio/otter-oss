<?php
/**
 * @category AliOssServiceProvider
 * @created 2020/8/11 10:18
 * @since
 */

namespace Otter\AliOss;

use AlibabaCloud\Client\AlibabaCloud;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Otter\AliOss\Plugins\UploadFile;
use Otter\AliOss\Plugins\SignedDownloadUrl;
use OSS\OssClient;

class AliOssServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     * @return void
     */
    public function boot()
    {
        Storage::extend('oss', function($app, $config) {
            $ramArn = $config['ram_arn'];
            $appKey = $config['app_key'];
            $appSecret = $config['app_secret'];
            $policyExpired = $config['policy_expired'];

            $accessId = $config['access_id'];
            $accessKey = $config['access_key'];
            $endPoint = $config['endpoint'];
            $bucket = $config['bucket'];

            $prefix = null;
            if(isset($config['prefix'])){
                $prefix = $config['prefix'];
            }
            if($ramArn != null && $appKey !== null){
                AlibabaCloud::accessKeyClient($appKey, $appSecret)
                            ->regionId(env('ALIYUN_REGION', 'cn-shanghai'))// replace regionId as you need
                            ->asDefaultClient();
                $sts = $this->getSts($ramArn, $policyExpired);
                $client = new OssClient($sts['AccessKeyId'], $sts['AccessKeySecret'], $endPoint, false, $sts['SecurityToken']);
            } else{
                $client = new OssClient($accessId, $accessKey, $endPoint);
            }
            $adapter = new AliOssAdapter($client, $bucket, $endPoint, $prefix);

            $filesystem = new Filesystem($adapter);
            $filesystem->addPlugin(new UploadFile());
            $filesystem->addPlugin(new SignedDownloadUrl());

            return $filesystem;
        });
    }

    /**
     * Register bindings in the container.
     * @return void
     */
    public function register()
    {

    }

    /**
     * @param $arn
     * @param $expired
     * @return \AlibabaCloud\Client\Result\Result
     * @throws \AlibabaCloud\Client\Exception\ClientException
     * @throws \AlibabaCloud\Client\Exception\ServerException
     */
    protected function getSts($arn, $expired)
    {
        $result = AlibabaCloud::rpc()
                              ->product('Sts')
                              ->scheme('https')// https | http
                              ->version('2015-04-01')
                              ->action('AssumeRole')
                              ->method('POST')
                              ->options([
                                  'query' => [
                                      'DurationSeconds' => $expired,
                                      'RoleArn'         => $arn,
                                      'RoleSessionName' => "gouki",
                                      'Policy'          => json_encode([
                                          "Version"   => "1",
                                          "Statement" => [
                                              [
                                                  "Action"   => ["oss:*"],
                                                  "Resource" => ['*'],
                                                  "Effect"   => "Allow"
                                              ]
                                          ]
                                      ])
                                  ],
                              ])
                              ->request();
        return $result->toArray()['Credentials'] ?? [];
    }
}
