<?php
/**
 * Google SDK
 */

namespace liliuwei\social\sdk;

use liliuwei\social\Oauth;

class Google extends Oauth
{
    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://accounts.google.com/o/oauth2/auth';

    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = 'https://accounts.google.com/o/oauth2/token';

    /**
     * 获取request_code的额外参数 URL查询字符串格式
     * @var srting
     */
    protected $Authorize = 'access_type&offline&approval_prompt&auto&scope=https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email';

    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = 'https://www.googleapis.com/oauth2/v1/';

    /**
     * 组装接口调用参数 并调用接口
     * @param  string $api Google API
     * @param  string $param 调用API的额外参数
     * @param  string $method HTTP请求方法 默认为GET
     * @return json
     */
    public function call($api, $param = '', $method = 'GET', $multi = false)
    {
        /* 新浪微博调用公共参数 */
        $params = array(
            'access_token' => $this->Token['access_token'],
        );
        $data = $this->http($this->url($api), $this->param($params, $param), $method);
        return json_decode($data, true);
    }

    /**
     * 解析access_token方法请求后的返回值
     * @param string $result 获取access_token的方法的返回值
     */
    protected function parseToken($result, $extend)
    {
        $data = json_decode($result, true);
        if ($data['access_token'] && $data['expires_in']) {
            $this->Token = $data;
            $data['openid'] = $this->openid();
            return $data;
        } else
            throw new \Exception("获取Google ACCESS_TOKEN 出错：{$result}");
    }

    /**
     * 获取当前授权应用的openid
     * @return string
     */
    public function openid()
    {
        $data = $this->call('userinfo');
        if (isset($data['id']))
            return $data['id'];
        else
            throw new \Exception('没有获取到 Google 用户ID！');
    }
}