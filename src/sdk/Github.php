<?php
/**
 * Github SDK
 */

namespace liliuwei\social\sdk;

use liliuwei\social\Oauth;

class Github extends Oauth
{
    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://github.com/login/oauth/authorize';

    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = 'https://github.com/login/oauth/access_token';

    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = 'https://api.github.com/';

    /**
     * 组装接口调用参数 并调用接口
     * @param  string $api Github API
     * @param  string $param 调用API的额外参数
     * @param  string $method HTTP请求方法 默认为GET
     * @return json
     */
    public function call($api, $param = '', $method = 'GET', $multi = false)
    {
        /* Github 调用公共参数 */
        $params = array();
        $header = array("Authorization: bearer {$this->Token['access_token']}","User-Agent:thinkphp-social");
        $data = $this->http($this->url($api), $this->param($params, $param), $method, $header);
        return json_decode($data, true);
    }

    /**
     * 解析access_token方法请求后的返回值
     * @param string $result 获取access_token的方法的返回值
     */
    protected function parseToken($result, $extend)
    {
        parse_str($result, $data);
        if ($data['access_token'] && $data['token_type']) {
            $this->Token = $data;
            $data['openid'] = $this->openid();
            return $data;
        } else
            throw new \Exception("获取 Github ACCESS_TOKEN出错：未知错误");
    }

    /**
     * 获取当前授权应用的openid
     * @return string
     */
    public function openid()
    {
        $data = $this->call('user');
        if (isset($data['id']))
            return $data['id'];
        else
            throw new \Exception('没有获取到 Github 用户ID！');
    }
}