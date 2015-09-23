<?php

namespace Demo\TaobaoOAuth;

class SimpleViewPage{

    public $title = "页面标题";

    function sendErrorMsgAndExit($msg){
        $this->display('error', array('msg' => $msg));
        exit();
    }
    
    function errorPage($data){
        $data['msg'] = htmlspecialchars($data['msg']);
        echo $data['msg'];
    }

    function display($__pagename, $__assigndata = array()){
        header("Content-type: text/html; charset=utf-8");
        echo <<<EOF
<!DOCTYPE html>
<html manifest="NOTEXIST.manifest">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1,height=device-height" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <title>{$this->title}</title>
    <style>
            .container{
                font-size:16px;word-wrap:break-word;word-break:break-all;
            }
    </style>
</head>
<body>
        <div class="container">
EOF;

        $__pagename = $__pagename. 'Page';
        if(method_exists($this, $__pagename)){
            $this->$__pagename($__assigndata);
        }else{
            echo $__pagename. '在View中未定义';
        }
        

        echo <<<EOF
        </div><!-- container -->
</body>
</html>
EOF;
    }

    function __call($name, $args){
        $this->sendErrorMsgAndExit($name. " method IN ViewDemo NOT EXIST");
    }

}


class ViewPage extends SimpleViewPage{

    public $title = "淘宝OAuth登录过程Demo";
    
    function indexPage($data){
        echo <<<EOF
        <div><a href="{$data['url']}">点击此处开始：（1）拼接授权url（并跳转）</a></div>
        <div><a href="https://open.taobao.com/doc/detail.htm?id=102635&spm=a219a.7386781.1998342838.19.lDPmBG">点击此处查看淘宝开放平台OAuth文档</a></div>
EOF;
    }


    function redirectAuthUrlPage($data){
        echo <<<EOF
        <div>\AlibabaSDK\TaobaoOAuth\TaobaoOAuthClient::getAuthUrl()生成的淘宝的OAuth授权页面URL：</div>
        <div><a href="{$data['finalUrl']}">{$data['finalUrl']}</a></div>
        <div>
                在实际开发中，你可以直接将上面的URL通过Header输出跳转，从而使得用户无需点击，即可到达淘宝的OAuth授权页面。
        </div>
        <div>
                点击上面的url以进入淘宝的OAuth授权页面。
        </div>
        <div>
                有关state参数，请<a href="http://blog.sina.com.cn/s/blog_56b798f801018jyb.html">点击这里阅读相关文档。</a>
        </div>
EOF;
    }

    function oauthCallbackPage($data){
        $tokenValue = var_export($data['token'], true);
        echo <<<EOF
        <div>access token信息：<pre>{$tokenValue}</pre></div>
EOF;
    }

}