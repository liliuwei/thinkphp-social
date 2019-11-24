<?php
/**
 * 微信SDK
 */

namespace liliuwei\social\sdk;

use liliuwei\social\Oauth;

class Weixin extends Oauth
{
    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://open.weixin.qq.com/connect/qrconnect';

    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = 'https://api.weixin.qq.com/sns/oauth2/access_token';

    /**
     * 获取request_code的额外参数 URL查询字符串格式
     * @var srting
     */
    protected $Authorize = '';

    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = 'https://api.weixin.qq.com/';

    public function getRequestCodeURL()
    {
        $this->config();
        $md = md5(rand(10000,99999));
        $params = array(
            'appid' => $this->AppKey,
            'redirect_uri' => $this->Callback,
            'response_type' => $this->ResponseType,
            'scope' => 'snsapi_login',
            'state' => $md,
        );
        return $this->GetRequestCodeURL . '?' . http_build_query($params) . "#wechat_redirect";
    }

    /**
     * 获取access_token
     * @param string $code 上一步请求到的code
     */
    public function getAccessToken($code, $extend = null)
    {
        $this->config();
        $params = array(
            'appid' => $this->AppKey,
            'secret' => $this->AppSecret,
            'grant_type' => $this->GrantType,
            'code' => $code,
        );
        $data = $this->http($this->GetAccessTokenURL, $params, 'POST');
        $this->Token = $this->parseToken($data, $extend);
        return $this->Token;
    }

    /**
     * 组装接口调用参数 并调用接口
     * @param  string $api 微信 API
     * @param  string $param 调用API的额外参数
     * @param  string $method HTTP请求方法 默认为GET
     * @return json
     */
    public function call($api, $param = '', $method = 'GET', $multi = false)
    {
        /* 微信调用公共参数 */
        $params = array(
            'access_token' => $this->Token['access_token'],
            'openid' => $this->openid(),
            'lang' => 'zh_CN',
        );
        $data = $this->http($this->url($api), $this->param($params, $param), $method);
        return json_decode($data, true);
    }

    /**
     * 解析access_token方法请求后的返回值
     */
    protected function parseToken($result, $extend)
    {
        $data = json_decode($result, true);
        if ($data['access_token'] && $data['expires_in']) {
            $this->Token = $data;
            $data['openid'] = $this->openid();
            return $data;
        } else
            throw new \Exception("获取微信 ACCESS_TOKEN 出错：{$result}");
    }

    /**
     * 获取当前授权应用的openid
     */
    public function openid($unionid=false)
    {
        if ($unionid){
            return $this->unionid();
        }
        $data = $this->Token;
        if (isset($data['openid']))
            return $data['openid'];
        else
            exit('没有获取到微信用户openid！');
    }

    /**
     * 获取当前授权应用的unionid
     */
    public function unionid()
    {
        $data = $this->Token;
        if (isset($data['unionid']))
            return $data['unionid'];
        else
            exit('没有获取到微信用户unionid！');
    }
}