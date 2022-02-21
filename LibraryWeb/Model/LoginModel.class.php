<?php
namespace LibraryWeb\Model;
use Think\Model;
//用户模型
class LoginModel extends Model {
    public $db;
    public $table;
    function __construct(){
        parent::__construct();
        $this->db=M("login");  //用户表
        $this->table=M('book_info');  //书信息表
        $this->recycle=M('recovery');  //回收站表
        $this->shopcar=M('shopcar');   //购物车表
        $this->history=M('history');   //借书历史
    }

    //查询一条数据
    function Find($datalib,$tj){
        return $this->$datalib->where($tj)->find();
    }
        //查询一条数据
    function Select($datalib,$tj){
        return $this->$datalib->where($tj)->select();
    }

    //更新
    function Save($datalib,$tj,$arr){
        return $this->$datalib->where($tj)->save($arr);
    }

    //删除
    function Delete($datalib,$tj){
        return $this->$datalib->where($tj)->delete();
    }

    //删除页面数据
    function DeleteData($id,$datalib,$index){
        $obj=new \LibraryWeb\Model\LoginModel;
        $result=$obj->Delete($datalib,'id='.$id);
        if ($result)
                echo "<script>alert('删除成功');location.href='".U($index)."'</script></script>";
        else
                echo "<script>alert('删除失败');location.href='".U($index)."'</script></script>";
    }

    //更新页面数据
    function SaveData($id,$datalib,$data,$index){
        $obj=new \LibraryWeb\Model\LoginModel;
        $result=$obj->Save($datalib,'id='.$id,$data);
        if ($result)
                echo "<script>alert('更新成功');location.href='".U($index)."'</script></script>";
        else
                echo "<script>alert('更新失败');location.href='".U($index)."'</script></script>";
    }


}//enclass
