## thinkphp-social
适用于thinkphp5.1 thinkphp6.0的社会化登录扩展

目前已支持：QQ、微信、新浪、百度、Gitee、Github、Oschina、Google、Facebook、淘宝、抖音、小米、钉钉

欢迎提交pr或者建议!

有问题可以联系邮箱：974829947@qq.com

## 安装（扩展包）
```php
composer require liliuwei/thinkphp-social
```

# 配置Config信息
```php
// 安装之后会在config目录里自动生成social.php配置文件
<?php
return [
    //腾讯QQ登录配置
    'qq' => [
        'app_key' => '*******', //应用注册成功后分配的 APP ID
        'app_secret' => '*******',  //应用注册成功后分配的KEY
        'callback' => 'http://www.youquanya.com/oauth/callback/type/qq', // 应用回调地址
    ],
    //微信扫码登录配置
    'weixin' => [
         'app_key' => '*******', //应用注册成功后分配的 APP ID
         'app_secret' => '*******',  //应用注册成功后分配的KEY
         'callback' => 'http://www.youquanya.com/oauth/callback/type/weixin', // 应用回调地址
    ],
];

```

## 用法示例
````
<a href="{:url('Oauth/login',['type'=>'qq'])}">QQ登录</a>
<a href="{:url('Oauth/login',['type'=>'sina'])}">新浪微博登录</a>
<a href="{:url('Oauth/login',['type'=>'weixin'])}">微信登录</a>
<a href="{:url('Oauth/login',['type'=>'baidu'])}">百度登录</a>
<a href="{:url('Oauth/login',['type'=>'gitee'])}">gitee登录</a>
<a href="{:url('Oauth/login',['type'=>'github'])}">github登录</a>
<a href="{:url('Oauth/login',['type'=>'oschaina'])}">oschaina登录</a>
<a href="{:url('Oauth/login',['type'=>'google'])}">google登录</a>
<a href="{:url('Oauth/login',['type'=>'facebook'])}">facebook登录</a>
<a href="{:url('Oauth/login',['type'=>'taobao'])}">淘宝登录</a>
<a href="{:url('Oauth/login',['type'=>'douyin'])}">抖音登录</a>
<a href="{:url('Oauth/login',['type'=>'xiaomi'])}">小米登录</a>
<a href="{:url('Oauth/login',['type'=>'dingtalk'])}">钉钉登录</a>
   ````
```php
//设置路由
Route::get('oauth/callback','index/oauth/callback');
```

```php
<?php

namespace app\index\controller;
use think\Controller;
class Oauth extends Controller
{
    //登录地址
        public function login($type = null)
        {
            if ($type == null) {
                $this->error('参数错误');
            }
            // 获取对象实例
            $sns = \liliuwei\social\Oauth::getInstance($type);
            //跳转到授权页面
            $this->redirect($sns->getRequestCodeURL());
        }
    
        //授权回调地址
        public function callback($type = null, $code = null)
        {
            if ($type == null || $code == null) {
                $this->error('参数错误');
            }
            $sns = \liliuwei\social\Oauth::getInstance($type);
            // 获取TOKEN
            $token = $sns->getAccessToken($code);
            //获取当前第三方登录用户信息
            if (is_array($token)) {
                $user_info = \liliuwei\social\GetInfo::getInstance($type, $token);
                dump($user_info);// 获取第三方用户资料
                $sns->openid();//统一使用$sns->openid()获取openid
                //$sns->unionid();//QQ和微信、淘宝可以获取unionid
                dump($sns->openid());
                echo '登录成功!!';
                echo '正在持续开发中，敬请期待!!';
            } else {
                echo "获取第三方用户的基本信息失败";
            }
        }
}
```
