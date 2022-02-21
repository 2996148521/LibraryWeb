<?php
namespace LibraryWeb\Controller;
//use Think\Controller;
//功能：
class UserConsoleController extends CommonController {
    function BookLib(){
        $obj=new \LibraryWeb\Model\LoginModel;
        $arr=$obj->table->select();
        $this->assign('arr',$arr);
        $this->display("/UserBookLib");
    }

//借书
    function JoinShopCar(){
        $arr=I('post.');
        $id=$_GET['id'];
        $obj=new \LibraryWeb\Model\LoginModel;
        $result=$obj->Find("table",'id='.$id);
        $a=$obj->Find("shopcar",'book_name='."'".$result['book_name']."'");
        // var_dump($a);die;
        if (!$a) {
        $data=array('username'=>session("username"),
                    'book_isbn'=>$result['book_isbn'],
                    'book_name'=>$result['book_name'],
                    'book_author'=>$result['book_author'],
                    'book_press'=>$result['book_press'],
                    'book_date'=>$result['book_date'],
                    'book_price'=>$result['book_price']);
        $bal=$obj->db->where("username="."'".session("username")."'")->field('balance')->find();  //查询该用户账户余额
        $_SESSION['balance'] = $bal['balance'];
            // echo $_SESSION['balance'];die;
        if ($result['book_num'] != 0) {
            if ($bal['balance']>=$result['book_price']) {
                $join=$obj->shopcar->add($data);$obj->history->add($data); //加入购物车
                if ($join) {
                    $data=array("balance"=>$bal['balance']-$result['book_price']);
                    $obj->Save("db","username="."'".session("username")."'",$data);  //更新用户余额
                    $_SESSION['balance'] -= $result['book_price'];
                    $data=array('book_num'=>$result['book_num']-1);
                    $obj->Save("table",'id='.$id,$data);  //更新图书数量
                    $data=array('book_occupy'=>$result['book_occupy']+1);
                    $obj->Save("table",'id='.$id,$data);  //更新图书占用数量
                    $data=array('borrow_time'=>date("Y-m-d H:i:s"));
                    $obj->Save("history","book_name="."'".$result['book_name']."'",$data); //更新用户借书历史
                    echo "<script>alert('借书成功');location.href='".U('BookLib')."'</script>";
                }else{
                    echo "<script>alert('借书失败');location.href='".U('BookLib')."'</script>";
                    die;
                }
            }else{
                echo "<script>alert('余额不足请充值');location.href='".U('BookLib')."'</script>";
                die;
            }
        }
        else{
            echo "<script>alert('数量不足');location.href='".U('BookLib')."'</script></script>";die;
        }
        // print_r($result);die;
        }else{
            echo "<script>alert('你已经借过这本书了噢，看看别的吧');location.href='".U('BookLib')."'</script></script>";die;
        }

    }

//查看书架
    function OpenShopCar(){
        $obj=new \LibraryWeb\Model\LoginModel;
        $arr=$obj->Select("shopcar",'username='."'".session("username")."'");
        // print_r($result);
        $this->assign('arr',$arr);
        $this->display("/ShopCar");
    }
//归还
    function DeleteShopCar(){
         // $count=$obj->execute($sql);
        $obj=new \LibraryWeb\Model\LoginModel;
        $result=$obj->Find("shopcar",'id='.$_GET['id']);
        // print_r($result);
        $arr=$obj->Find("table",'book_name='."'".$result['book_name']."'");
        //print_r($arr);die;
        $f=$obj->DeleteData($_GET['id'],"shopcar",'OpenShopCar');
        $data=array("return_time"=>date("Y-m-d H:i:s"));
        $obj->Save("history","book_name="."'".$arr['book_name']."'",$data);
        $data=array('book_occupy'=>$arr['book_occupy']-1);
        $obj->Save("table",'book_name='."'".$arr['book_name']."'",$data);
        $data=array('book_num'=>$arr['book_num']+1);
        $obj->Save("table",'book_name='."'".$arr['book_name']."'",$data);
    }

//借书历史
function History(){
        $obj=new \LibraryWeb\Model\LoginModel;
        $arr=$obj->Select("history",'username='."'".session("username")."'");
        // print_r($result);
        $this->assign('arr',$arr);
        $this->display("/bookhistory");
}
function ClearBookHistory(){
    $obj=new \LibraryWeb\Model\LoginModel;
    $result=$obj->Delete("history",1);
    if ($result) {
        echo "<script>alert('删除成功');location.href='".U('History')."'</script></script>";die;
    }else{
        echo "<script>alert('删除失败');location.href='".U('History')."'</script></script>";die;
    }
}

}//enclass