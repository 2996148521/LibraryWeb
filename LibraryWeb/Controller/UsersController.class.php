<?php
namespace LibraryWeb\Controller;
//use Think\Controller;
//功能：用户管理控制器
class UsersController extends CommonController {
    //修改密码
    function Change_SelfPassword(){
        //echo "Change_SelfPassword";
        if (IS_POST) //判断当前网页表单是否为post请求
        {
            $arr=I('post.');
            //print_r($arr);  //Array ( [psd] => [psd1] => [psd2] => [button] => 提交 )
            //先判断老密码psd是否ok
            $obj=new \LibraryWeb\Model\LoginModel;
            $arr2=$obj->Find("db","username='".session("username")."'");

            $salt=$arr2['salt'];
            $password_user=ReturnPassword($arr['psd'],$salt);
            //echo "开始修改密码";
            $salt=ReturnSalt();
            $password=ReturnPassword($arr['npsd1'],$salt);  //输入的新密码
            $arr_m=array(
                'salt'=>$salt,
                'password'=>$password,
                'lastchangepsd'=>date("Y-m-d H:i:s")
            );
            if($arr2['root']=="否"){
                if ($arr['psd']==null){
                    $this->error("密码不能为空");
                    die;
                }
                if($password_user!=$arr2['password'])  //若不相等
                {
                    $this->error('原密码错误');
                    die;
                }

            }
            if ($arr['npsd2'] != $arr['npsd1'])
            {
                $this->error("确认密码失败");
                die;
            }

              if ($arr['psd'] == $arr['npsd1'])
            {
                $this->error("新旧密码不能相同");
                die;
            }

            if ($arr['npsd1']==null || $arr['npsd2']==null) {
                $this->error("密码不能为空");
                die;
            }


            $c=$obj->Save("db",$tj,$arr_m);
            if ($c)
            {
                echo "<script>alert('修改成功！新密码是：".$arr['npsd1']."');top.location.href='".U('index/LoginOut')."';</script>";
            }
            else
                $this->error("修改失败");
            die;
        }
        $this->display("/change_selfpassword");
    }


}//enclass
