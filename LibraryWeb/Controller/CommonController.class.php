<?php
namespace LibraryWeb\Controller;
use Think\Controller;
//功能：公共类
class CommonController extends Controller {
    function __construct(){
        parent::__construct();
        // echo MODULE_NAME; //当前模块名
        // echo CONTROLLER_NAME; //当前控制器名
        // echo ACTION_NAME; //当前操作名
        //安全防范 防止未登录进入
        if (CONTROLLER_NAME=="Index") {
            $arr=array("succ");
            if (in_array(ACTION_NAME, $arr)){
                $this->safe();
            }
        }
        else
            $this->safe();
    }

    function safe(){
        if ( $_SESSION['username']==null){
            $this->error("请登录",U('index/index'));
            die;
        }
    }
}//enclass
