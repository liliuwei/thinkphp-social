<?php
/**
 * Dingtalk SDK
 */

namespace liliuwei\social\sdk;

use liliuwei\social\Oauth;

class Dingtalk extends Oauth
{
    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://oapi.dingtalk.com/connect/qrconnect';

    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = '';

    /**
     * 获取request_code的额外参数,可在配置中修改 URL查询字符串格式
     * @var srting
     */
    protected $Authorize = 'scope=snsapi_login';

    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = 'https://oapi.dingtalk.com/';

    /**
     * 组装接口调用参数 并调用接口
     * @param  string $api Dingtalk API
     * @param  string $param 调用API的额外参数
     * @param  string $method HTTP请求方法 默认为GET
     * @return json
     */
    public function call($api, $param = '', $method = 'POST', $multi = false)
    {
        $time=$this->getMicrotime();
        // 根据timestamp, appSecret计算签名值
        $s = hash_hmac('sha256', $time, $this->AppSecret, true);
        $signature = base64_encode($s);
        $urlencode_signature = urlencode($signature);
        /* Dingtalk 调用公共参数 */
        $params = array(
            'tmp_auth_code'=>$param['code'],
        );
        $data = $this->http($this->url($api.'?signature='.$urlencode_signature.'&timestamp='.$time.'&accessKey='.$this->AppKey), $this->param($params, $param), $method);
        return json_decode($data, true);
    }

    /**
     * 发送HTTP请求方法，目前只支持CURL发送请求
     * @param  string $url 请求URL
     * @param  array $params 请求参数
     * @param  string $method 请求方法GET/POST
     * @return array  $data   响应数据
     */
    public function http($url, $params, $method = 'POST', $header = array(), $multi = false)
    {
        $data_string = json_encode($params);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($ch);
        return $result;
    }

    /**
     * 时间戳 - 精确到毫秒
     * @return float
     */
    public function getMicrotime()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

    /**
     * 请求code
     */
    public function getRequestCodeURL()
    {
        $this->config();
        //Oauth 标准参数
        $params = array(
            'appid' => $this->AppKey,
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
        $result = $this->call('sns/getuserinfo_bycode',['code'=>$code]);
        $data = $result['user_info'];
        $this->Token = [
            'nick'=>$data['nick'],
            'openid'=>$data['openid'],
            'unionid'=>$data['unionid'],
        ];
        return $this->Token;
    }

    /**
     * 解析access_token方法请求后的返回值
     * @param string $result 获取access_token的方法的返回值
     */
    protected function parseToken($result, $extend){}

    /**
     * 获取当前授权应用的openid
     * @return string
     */
    public function openid($unionid=false)
    {
        if ($unionid){
            return $this->unionid();
        }
        $data = $this->Token;
        if (isset($data['openid'])){
            return $data['openid'];
        }else{
            throw new \Exception('没有获取到 Dingtalk 用户openid！');
        }
    }

    /**
     * 获取当前授权应用的unionid
     */
    public function unionid()
    {
        $data = $this->Token;
        if (isset($data['unionid'])){
            return $data['unionid'];
        }else{
            throw new \Exception('没有获取到 Dingtalk 用户unionid！');
        }
    }

}