<?php
/**
 * Taobao SDK
 */

namespace liliuwei\social\sdk;

use liliuwei\social\Oauth;

class Taobao extends Oauth
{
    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://oauth.taobao.com/authorize';

    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = 'https://oauth.taobao.com/token';

    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = 'http://gw.api.taobao.com/router/rest';

    /**
     * 组装接口调用参数 并调用接口
     * @param  string $api 微博API
     * @param  string $param 调用API的额外参数
     * @param  string $method HTTP请求方法 默认为GET
     * @return json
     */
    public function call($api, $param = '', $method = 'GET', $multi = false)
    {
        /* 淘宝网调用公共参数 */
        $params = array(
            'method' => $api,
            'access_token' => $this->Token['access_token'],
            'format' => 'json',
            'v' => '2.0',
        );
        $paramsAll = $this->param($params, $param);
        ksort($paramsAll);
        $str = '';
        foreach ($paramsAll as $key => $value) {
            $str .= $key . $value;
        }
        $sign = strtoupper(md5($this->AppSecret . $str . $this->AppSecret));
        $params_sign = array(
            'sign' => $sign,
        );
        $data = $this->http($this->url(), array_merge($paramsAll, $params_sign), $method);
        return json_decode($data, true);
    }

    /**
     * 解析access_token方法请求后的返回值
     * @param string $result 获取access_token的方法的返回值
     */
    protected function parseToken($result, $extend)
    {
        $data = json_decode($result, true);
        if ($data['access_token'] && $data['expires_in'] && $data['taobao_user_id']) {
            $this->Token = $data;
            $data['openid'] = $this->openid();
            return $data;
        } else {
            throw new \Exception("获取淘宝网ACCESS_TOKEN出错：{$data['error']}");
        }
    }

    /**
     * 获取当前授权应用的openid
     * @return string
     */
    public function openid($unionid = false)
    {
        if ($unionid) {
            return $this->unionid();
        }
        $data = $this->Token;
        if (isset($data['taobao_user_id'])) {
            return $data['taobao_user_id'];
        } else {
            throw new \Exception('没有获取到淘宝网用户openid！');
        }
    }

    /**
     * 获取当前授权应用的unionid
     * @return string
     */
    public function unionid()
    {
        $data = $this->Token;
        if (isset($data['taobao_open_uid'])) {
            return $data['taobao_open_uid'];
        } else {
            throw new \Exception('没有获取到淘宝网用户unionid！');
        }
    }
}