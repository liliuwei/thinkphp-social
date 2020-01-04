<?php
/**
 * 获取第三方用户信息类
 */

namespace liliuwei\social;
final class GetInfo
{
    public static function getInstance($type, $token)
    {
        return self::$type($token);
    }

    //QQ用户信息
    public static function qq($token)
    {
        $qq = Oauth::getInstance('qq', $token);
        $data = $qq->call('user/get_user_info');
        if ($data['ret'] == 0) {
            $userInfo['type'] = 'qq';
            $userInfo['name'] = $data['nickname'];
            $userInfo['nickname'] = $data['nickname'];
            $userInfo['avatar'] = $data['figureurl_2'];
            $userInfo['gender'] = $data['gender'];
            return $userInfo;
        } else {
            throw new \Exception("获取腾讯QQ用户信息失败：{$data['msg']}");
        }
    }

    //微信用户信息
    public static function weixin($token)
    {
        $weixin = Oauth::getInstance('weixin', $token);
        $data = $weixin->call('sns/userinfo');
        if (isset($data['errcode'])) {
            throw new \Exception("获取微信用户信息失败：errcode:{$data['errcode']} errmsg: {$data['errmsg']}");
        }
        if ($data['openid']) {
            $userInfo['type'] = 'weixin';
            $userInfo['name'] = $data['nickname'];
            $userInfo['nickname'] = $data['nickname'];
            $userInfo['avatar'] = $data['headimgurl'];
            $userInfo['openid'] = $data['openid'];
            $userInfo['unionid'] = $data['unionid'];
            $userInfo['province'] = $data['province'];
            $userInfo['city'] = $data['city'];
            $userInfo['country'] = $data['country'];
            $userInfo['sex'] = $data['sex']==1?'男':'女';
            return $userInfo;
        } else {
            throw new \Exception("获取微信用户信息失败");
        }
    }

    //新浪微博用户信息
    public static function sina($token)
    {
        $sina = Oauth::getInstance('sina', $token);
        $data = $sina->call('users/show',"uid={$sina->openid()}");
        if ($data['id']) {
            $userInfo['type'] = 'sina';
            $userInfo['name'] = $data['name'];
            $userInfo['nickname'] = $data['screen_name'];
            $userInfo['avatar'] = $data['avatar_large'];
            $userInfo['openid'] = $data['id'];
            $userInfo['idstr'] = $data['idstr'];
            $userInfo['province'] = $data['province'];
            $userInfo['city'] = $data['city'];
            $userInfo['location'] = $data['location'];
            $userInfo['created_at'] = $data['created_at'];
            $userInfo['gender'] = $data['gender']=='m'?'男':($data['gender']=='f'?'女':'未知');
            $userInfo['followers_count'] = $data['followers_count'];
            $userInfo['friends_count'] = $data['friends_count'];
            $userInfo['statuses_count'] = $data['statuses_count'];
            $userInfo['favourites_count'] = $data['favourites_count'];
            $userInfo['description'] = $data['description'];
            $userInfo['url'] = $data['url'];
            $userInfo['profile_url'] = $data['profile_url'];
            return $userInfo;
        } else {
            throw new \Exception("获取新浪微博用户信息失败：{$data['error']}");
        }
    }

    //Baidu用户信息
    public static function baidu($token)
    {
        $baidu = Oauth::getInstance('baidu', $token);
        $data = $baidu->call('passport/users/getInfo');
        if (isset($data['userid'])) {
            $userInfo['type'] = 'baidu';
            $userInfo['name'] = $data['username'];
            $userInfo['nickname'] = isset($data['realname'])?$data['realname']:'';
            $userInfo['avatar'] = 'http://tb.himg.baidu.com/sys/portrait/item/'.$data['portrait'];
            $userInfo['openid'] = $data['userid'];
            $userInfo['sex'] = $data['sex']==1?'男':'女';
            return $userInfo;
        } else {
            throw new \Exception("获取Baidu用户信息失败");
        }
    }

    //Gitee用户信息
    public static function gitee($token)
    {
        $google = Oauth::getInstance('gitee', $token);
        $data = $google->call('user');
        if (isset($data['id'])) {
            $userInfo['type'] = 'gitee';
            $userInfo['name'] = $data['name'];
            $userInfo['nickname'] = $data['login'];
            $userInfo['avatar'] = $data['avatar_url'];
            $userInfo['openid'] = $data['id'];
            $userInfo['html_url'] = $data['html_url'];
            $userInfo['blog'] = $data['blog'];
            $userInfo['email'] = $data['email'];
            return $userInfo;
        } else {
            throw new \Exception("获取Gitee用户信息失败");
        }
    }

    //Github用户信息
    public static function github($token)
    {
        $google = Oauth::getInstance('github', $token);
        $data = $google->call('user');
        if (isset($data['id'])) {
            $userInfo['type'] = 'github';
            $userInfo['name'] = $data['name'];
            $userInfo['nickname'] = $data['login'];
            $userInfo['avatar'] = $data['avatar_url'];
            $userInfo['openid'] = $data['id'];
            $userInfo['html_url'] = $data['html_url'];
            $userInfo['blog'] = $data['blog'];
            $userInfo['email'] = $data['email'];
            return $userInfo;
        } else {
            throw new \Exception("获取Gitee用户信息失败");
        }
    }

    //Google用户信息
    public static function google($token)
    {
        $google = Oauth::getInstance('google', $token);
        $data = $google->call('userinfo');
        if (isset($data['id'])) {
            $userInfo['type'] = 'google';
            $userInfo['name'] = $data['name'];
            $userInfo['nickname'] = $data['name'];
            $userInfo['avatar'] = $data['picture'];
            $userInfo['openid'] = $data['id'];
            $userInfo['given_name'] = $data['given_name'];
            $userInfo['family_name'] = $data['family_name'];
            $userInfo['locale'] = $data['locale'];
            $userInfo['email'] = $data['email'];
            return $userInfo;
        } else {
            throw new \Exception("获取Google用户信息失败");
        }
    }

    //Facebook用户信息
    public static function facebook($token)
    {
        $facebook = Oauth::getInstance('facebook', $token);
        $data = $facebook->call('me', 'fields=name,picture,first_name,last_name,short_name,email');
        if (isset($data['id'])) {
            $userInfo['type'] = 'facebook';
            $userInfo['name'] = $data['name'];
            $userInfo['nickname'] = $data['name'];
            $userInfo['avatar'] = $data['picture']['data']['url'];
            $userInfo['openid'] = $data['id'];
            $userInfo['first_name'] = $data['first_name'];
            $userInfo['last_name'] = $data['last_name'];
            $userInfo['short_name'] = $data['short_name'];
            $userInfo['email'] = $data['email'];
            return $userInfo;
        } else {
            throw new \Exception("获取Facebook用户信息失败");
        }
    }

    //Oschina用户信息
    public static function oschina($token)
    {
        $oschina = Oauth::getInstance('oschina', $token);
        $data = $oschina->call('action/openapi/user');
        if (isset($data['id'])) {
            $userInfo['type'] = 'gitee';
            $userInfo['name'] = $data['name'];
            $userInfo['nickname'] = $data['name'];
            $userInfo['avatar'] = $data['avatar'];
            $userInfo['openid'] = $data['id'];
            $userInfo['email'] = $data['email'];
            $userInfo['location'] = $data['location'];
            return $userInfo;
        } else {
            throw new \Exception("获取Gitee用户信息失败");
        }
    }

    //Taobao用户信息
    public static function taobao($token)
    {
        $data = $token;
        if (isset($data['taobao_user_id'])) {
            $userInfo['type'] = 'taobao';
            $userInfo['name'] = urldecode($data['taobao_user_nick']);
            $userInfo['nickname'] = urldecode($data['taobao_user_nick']);
            $userInfo['avatar'] = '';
            $userInfo['openid'] = $data['taobao_user_id'];
            $userInfo['taobao_open_uid'] = $data['taobao_open_uid'];
            return $userInfo;
        } else {
            throw new \Exception("获取淘宝用户信息失败");
        }
    }

    //Douyin用户信息
    public static function douyin($token)
    {
        $douyin = Oauth::getInstance('douyin', $token);
        $data = $douyin->call('oauth/userinfo');
        $data = $data['data'];
        if (isset($data['open_id'])) {
            $userInfo['type'] = 'douyin';
            $userInfo['name'] = $data['nickname'];
            $userInfo['nickname'] = $data['nickname'];
            $userInfo['avatar'] = $data['avatar'];
            $userInfo['openid'] = $data['open_id'];
            $userInfo['unionid'] = $data['union_id'];
            $userInfo['gender'] = $data['sex']==1?'男':'女';
            $userInfo['city'] = $data['city'];
            $userInfo['province'] = $data['province'];
            $userInfo['country'] = $data['country'];
            return $userInfo;
        } else {
            throw new \Exception("获取抖音用户信息失败");
        }
    }

    //Xiaomi用户信息
    public static function xiaomi($token)
    {
        $xiaomi = Oauth::getInstance('xiaomi', $token);
        $data = $xiaomi->call('user/profile');
        $data = $data['data'];
        if (isset($data['unionId'])) {
            $userInfo['type'] = 'xiaomi';
            $userInfo['name'] = $data['miliaoNick'];
            $userInfo['nickname'] = $data['miliaoNick'];
            $userInfo['avatar'] = $data['miliaoIcon'];
            $userInfo['openid'] = array_key_exists('userId',$data)?$data['userId']:$data['unionId'];
            $userInfo['unionid'] = $data['unionId'];
            return $userInfo;
        } else {
            throw new \Exception("获取小米用户信息失败");
        }
    }

    //Dingtalk用户信息
    public static function dingtalk($token)
    {
        $data = $token;
        if ($data['openid']) {
            $userInfo['type'] = 'dingtalk';
            $userInfo['name'] = $data['nick'];
            $userInfo['nickname'] = $data['nick'];
            $userInfo['avatar'] = '';
            $userInfo['openid'] = $data['openid'];
            $userInfo['unionid'] = $data['unionid'];
            return $userInfo;
        } else {
            throw new \Exception("获取钉钉用户信息失败");
        }
    }
}