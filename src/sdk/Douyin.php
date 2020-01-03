<?php
/**
 * Douyin SDK
 */

namespace liliuwei\social\sdk;

use liliuwei\social\Oauth;

class Douyin extends Oauth
{
    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://open.douyin.com/platform/oauth/connect';

    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = 'https://open.douyin.com/oauth/access_token/';

    /**
     * 获取request_code的额外参数,可在配置中修改 URL查询字符串格式
     * @var srting
     */
    protected $Authorize = 'scope=user_info';

    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = 'https://open.douyin.com/';

    /**
     * 请求code
     */
    public function getRequestCodeURL()
    {
        $this->config();
        $params = array(
            'client_key ' => $this->AppKey,
            'redirect_uri' => $this->Callback,
            'response_type' => $this->ResponseType,
        );

        //获取额外参数
        if ($this->Authorize) {
            parse_str($this->Authorize, $_param);
            if (is_array($_param)) {
                $params = array_merge($params, $_param);
            } else {
                throw new \Exception('AUTHORIZE配置不正确！');
            }
        }
        return $this->GetRequestCodeURL . '?' . http_build_query($params);
    }

    /**
     * 获取access_token
     * @param string $code 上一步请求到的code
     */
    public function getAccessToken($code, $extend = null)
    {
        $this->config();
        $params = array(
            'client_key' => $this->AppKey,
            'client_secret ' => $this->AppSecret,
            'grant_type' => $this->GrantType,
            'code' => $code,
        );
        $data = $this->http($this->GetAccessTokenURL, $params, 'GET');
        $this->Token = $this->parseToken($data, $extend);
        return $this->Token;
    }

    /**
     * 组装接口调用参数 并调用接口
     * @param  string $api Douyin API
     * @param  string $param 调用API的额外参数
     * @param  string $method HTTP请求方法 默认为GET
     * @return json
     */
    public function call($api, $param = '', $method = 'GET', $multi = false)
    {
        /* Douyin 调用公共参数 */
        $params = array(
            'access_token' => $this->Token['access_token'],
            'open_id' => $this->openid(),
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
        $data = $data['data'];
        if ($data['access_token'] && $data['expires_in']) {
            $this->Token = $data;
            $data['openid'] = $this->openid();
            return $data;
        } else
            throw new \Exception("获取 抖音 ACCESS_TOKEN出错：未知错误");
    }

    /**
     * 获取当前授权应用的openid
     * @return string
     */
    public function openid($unionid=false)
    {
        if ($unionid){
            return $this->unionid();
        }
        $data = $this->call('oauth/userinfo');
        $data = $data['data'];
        if (isset($data['open_id']))
            return $data['open_id'];
        else
            throw new \Exception('没有获取到 抖音 用户openid！');
    }

    /**
     * 获取当前授权应用的unionid
     */
    public function unionid()
    {
        $data = $this->call('oauth/userinfo');
        $data = $data['data'];
        if (isset($data['union_id']))
            return $data['union_id'];
        else
            exit('没有获取到 抖音 用户unionid！');
    }
}