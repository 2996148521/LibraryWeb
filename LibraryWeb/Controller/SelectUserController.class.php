<?php
namespace LibraryWeb\Controller;
//use Think\Controller;
//功能：查找全部用户
class SelectUserController extends CommonController {
    //展示用户列表
    function Display_User(){
        $obj=new \LibraryWeb\Model\LoginModel;
        $account=$obj->db->order('id')->select();  //查找全部用户
        //var_dump($account);
        $this->assign('account',$account);
        $this->display("/SelectAccount");
    }
    //删除
    function DeleteUser(){
        $id=$_GET['id'];
        $obj=new \LibraryWeb\Model\LoginModel;
        $arr=$obj->Find("db",'id='.$id);
        if ($arr['root']=="是") {
            echo "<script>alert('管理员无法删除');window.location.replace('".U('Display_User')."');</script></script>";die;
        }
        $result=$obj->Delete("db",'id='.$id);
        if ($result){
            $data=array('deletetime'=>date("Y-m-d H:i:s"));
            $obj->recycle->add($arr);
            $recycle_result=$obj->Find("recycle",'username='."'".$arr['username']."'");
            $obj->Save("recycle",'username='."'".$recycle_result['username']."'",$data);
            echo "<script>alert('删除成功');window.location.replace('".U('Display_User')."');</script></script>";
                //$this->success("删除成功",U('BookLib'));
        }
        else
                echo "<script>alert('删除失败');location.href='".U('Display_User')."'</script></script>";

    }

    //更新
    function UpdateUser(){
        $obj=new \LibraryWeb\Model\LoginModel;
        $arr=I('post.');
        //var_dump($arr);die;
        $arr2=$obj->Find("db",'username='."'".$arr['username']."'");
        // $salt=$arr2['salt'];
        $salt=ReturnSalt();
        $password=ReturnPassword($arr['password'],$salt);
        $data=array('username'=>$arr['username'],
                    'password'=>$password,
                    'salt'=>$salt,
                    'root'=>$arr['root']);
        if ($arr['password']==null) {
            echo "<script>alert('密码不能为空');location.href='".U('IntoUpdateUser')."'</script></script>";
            die;
        }
        $id=$_SESSION['Account']['id'];
        $result=$obj->SaveData($id,"db",$data,'Display_User');
        $data=array("lastchangepsd"=>date("Y-m-d H:i:s"));
        $obj->Save("db",'username='."'".$arr['username']."'",$data);
        $_SESSION['Account']=array(); //清空$_SESSION数组
    }

    //进入更新
    function IntoUpdateUser(){
        $id=$_GET['id'];  //获取点击编辑时那一行的id
        $obj=new \LibraryWeb\Model\LoginModel;
        $result=$obj->Find("db",'id='.$id);;  //查询得到id那一行的结果集
        //var_dump($result);
        if ($result['username']=="xzh") {
            echo "<script>alert('该用户禁止修改');location.href='".U('Display_User')."'</script></script>";
            die;
        }
        $_SESSION['Account']=$result;
        $this->display("/updateAccount");
    }
}//enclass
