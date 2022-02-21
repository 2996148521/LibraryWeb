<?php
namespace LibraryWeb\Controller;
//use Think\Controller;
//功能：
class AdminConsoleController extends CommonController {
        function BookLib()
        {
                $find=I('post.');
                $obj=new \LibraryWeb\Model\LoginModel;
                //$_SESSION['search']=$find['search'];
                if ($find['search']==null) {
                        $arr=$obj->table->select();
                        // $row=mysqli_fetch_assoc($arr);
                        // var_dump($arr);
                         //print_r($arr);
                        // $this->assign('arr',$arr);
                        // $this->display("/AdminConsole");
                }
                else{
                        $map['book_isbn | book_name | book_author']=array('like','%'.$find['search'].'%');
                        $arr=$obj->table->where($map)->select();
                        if ($arr==null) {
                                echo "<script>alert('未查询到结果');</script></script>";
                        }
                }
                session("search",$find['search']);
                $this->assign('arr',$arr);
                $this->display("/AdminConsole");
        }
    //添加
    function AddBook(){
        $obj=new \LibraryWeb\Model\LoginModel;
        $arr=I('post.');
        $data=array('book_isbn'=>$arr['isbn'],
                    'book_name'=>$arr['mincheng'],
                    'book_author'=>$arr['zuozhe'],
                    'book_press'=>$arr['chubanshe'],
                    'book_date'=>$arr['chubanriqi'],
                    'book_num'=>$arr['num'],
                    'book_price'=>$arr['zujin']);
        foreach ($arr as $key => $value) {
                if($value==null)
                {
                        echo "<script>alert('添加失败,信息不能为空');location.href='".U('BookLib')."'</script>";die;
                }
        }
        $result=$obj->table->add($data);
        if (result)
                echo "<script>alert('添加成功');</script>";
        else
                echo "<script>alert('添加失败');</script>";
        echo "<script>location.href='".U('BookLib')."'</script>";
    }

    //进入添加界面
    function IntoAdd(){
        $this->display("/addBook");
    }

    //删除
    function DeleteBook(){
        $obj=new \LibraryWeb\Model\LoginModel;
        $obj->DeleteData($_GET['id'],"table",'BookLib');

    }

    //更新
    function UpdateBook()
    {
        $obj=new \LibraryWeb\Model\LoginModel;
        $arr=I('post.');
        $data=array('book_isbn'=>$arr['isbn'],
                    'book_name'=>$arr['mincheng'],
                    'book_author'=>$arr['zuozhe'],
                    'book_press'=>$arr['chubanshe'],
                    'book_date'=>$arr['chubanriqi'],
                    'book_num'=>$arr['num'],
                    'book_price'=>$arr['zujin']);
        $id=$_SESSION['info']['id'];
        //$result=$obj->Save("table",'id='.$id,$data);
        $result=$obj->SaveData($id,"table",$data,'BookLib');
        $_SESSION['info']=array(); //清空$_SESSION数组
   }

   //进入更新界面
    function IntoUpdate()
    {
        $id=$_GET['id'];  //获取点击编辑时那一行的id
        $obj=new \LibraryWeb\Model\LoginModel;
        $result=$obj->Find("table",'id='.$id);  //查询得到id那一行的结果集
        //var_dump($result);
        $_SESSION['info']=$result;
        //var_dump($_SESSION['info']);  //array(8) { ["id"]=> string(2) "86" ["book_isbn"]=> string(8) "20010327" ["book_name"]=> string(24) "如何让富婆爱上我" ["book_author"]=> string(9) "徐赵海" ["book_press"]=> string(15) "南京出版社" ["book_date"]=> string(10) "2022-01-18" ["book_num"]=> string(4) "1000" ["book_price"]=> string(2) "10" }
        //echo $_SESSION['info']['book_isbn'];
        //session("id",$id);
        $this->display("/updateBook");
    }

}//enclass