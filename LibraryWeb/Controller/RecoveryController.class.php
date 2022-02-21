<?php
namespace LibraryWeb\Controller;
//use Think\Controller;
//功能：回收站
class RecoveryController extends CommonController {
    //显示回收站用户
    function RecoveryUser(){
        $obj=new \LibraryWeb\Model\LoginModel;
        $recovery=$obj->recycle->select();  //查找全部用户
        //var_dump($account);
        $this->assign('recovery',$recovery);
        $this->display("/RecoveryAccount");
    }

    //删除回收站用户
    function DeleteRecoveryUser(){
        $obj=new \LibraryWeb\Model\LoginModel;
        $obj->DeleteData($_GET['id'],"recycle",'RecoveryUser');
        // $id=$_GET['id'];
        // $obj=new \LibraryWeb\Model\LoginModel;
        // $result=$obj->Delete("table",'id='.$id);;
        // if ($result)
        //         echo "<script>alert('删除成功');location.href='".U('RecoveryUser')."'</script></script>";
        //         //$this->success("删除成功",U('BookLib'));
        // else
        //         echo "<script>alert('删除成功');location.href='".U('RecoveryUser')."'</script></script>";
    }

    function DeleteAll(){
        $obj=new \LibraryWeb\Model\LoginModel;
        $result=$obj->recycle->where('1')->delete();
        if ($result)
                echo "<script>alert('删除成功');location.href='".U('RecoveryUser')."'</script></script>";
        else
                echo "<script>alert('删除成功');location.href='".U('RecoveryUser')."'</script></script>";
    }

    //恢复回收站用户
    function ReturnUser(){
        $obj=new \LibraryWeb\Model\LoginModel;
        $arr=$obj->Find("recycle",'id='.$_GET['id']);
        $obj->Delete("recycle",'id='.$_GET['id']);
        $result=$obj->db->add($arr);
        if ($result)
            echo "<script>alert('恢复成功');location.href='".U('RecoveryUser')."'</script></script>";
        else
            echo "<script>alert('恢复失败');location.href='".U('RecoveryUser')."'</script></script>";

    }

}//enclass
