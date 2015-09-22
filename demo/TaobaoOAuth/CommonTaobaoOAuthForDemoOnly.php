<?php

namespace CommonTaobaoOAuthForDemoOnly;

class SimpleController{
    
    protected $viewClassName = '\CommonTaobaoOAuthForDemoOnly\SimpleView';
    
    /**
     * 
     * @var \CommonTaobaoOAuthForDemoOnly\SimpleView
     */
    protected $view;
    
    public function __construct(){
        $viewClassName = $this->viewClassName;
        $this->view = new $viewClassName();
    }
    
    
    function __call($name, $value){
        $this->sendErrorMsgAndExit($name. ' method IN Controller NOT EXIST');
    }
    
    function sendErrorMsgAndExit($name){
        $this->view->sendErrorMsgAndExit($name);
    }
    
}



class SimpleView{

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