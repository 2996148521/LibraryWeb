<?php
namespace LibraryWeb\Controller;
//use Think\Controller;
use \LoginModel;

//用于登录
class IndexController extends CommonController
{

    public function index(){
        //$this->assign("mb",666);  //声明模板变量
        // $salt=ReturnSalt();
        // echo $salt."<br>";
        // echo md5(md5('123456').$salt);
        $this->display("/index");
    }

    public function IntoLogin(){
        $this->display("/Login");
    }

    //生成验证码
    public function Verify(){
        //设置验证码相关参数
        $config = array(
            'fontSize'  =>  16,              // 验证码字体大小(px)
            'imageH'    =>  30,               // 验证码图片高度
            'imageW'    =>  110,               // 验证码图片宽度
            'length'    =>  4,               // 验证码位数
            'codeSet'=>'0123456789'             //输入类型
            );
        //实例化Verify，同时还有命名空间的问题,并传递配置参数
        $Verify = new \Think\Verify($config);
        //输出验证码
        $Verify->entry();
    }

    //查询
    function chaxun(){
        $arr1=I("post.");
        $obj=M('login');  //数据表对象
        /*查询 1
        //$arr=$obj->select();  //无条件查询所有结果 返回值为二维数组
        //$arr=$obj->where("id in (1,2)")->limit(0,1)->field("id,username")->order("id desc")->select();  //where(查询条件) limit(起始下标，结束下标)截取 field(指定字段名) order(按什么排序)
        $sql="select id,username from obj_login where id in(1,2) order by id desc limit 0,1";
        //$count=$obj->execute($sql); //返回实际影响的记录数
        $arr=$obj->query($sql); */

        //查询 2 计算记录数
        //$c=$obj->where('id in (1,2)')->count();
        // $sql="select id as c from obj_login where id in (1,2)";
        // $c=$obj->execute($sql);
        // print_r($c);
        //
        // find() 查询结果为1维数组
        // $arr=$obj->where('id=1')->field("id,username")->find();
        // print_r($arr);
        //
        //增加、
        // $arr=array("username"=>"hhh","password"=>md5("123456"));
        // $c=$obj->add($arr); //影响的是执行增加相应的id值，方便后续关联表操作
        // var_dump($c); //string(1) (id)"7"
        //
        //修改 save
        // $arr=array("username"=>"hhhhhh","password"=>md5("12345678"),"time"=>date('Y-m-d H:i:s'));
        // $c=$obj->where('id=7')->save($arr);
        // var_dump($c); //int(1) 实际影响的记录数
        //
        //删除 实际影响的记录数
        // $c=$obj->where('id in (1,2,7)')->delete();
        // var_dump($c);
    }

    //登录
    function checklogin(){
        $arr=I('post.'); //更安全
        $obj=new \LibraryWeb\Model\LoginModel;
        //echo $_SESSION['id'];die;

        $arr2=$obj->Find("db",'username='."'".$arr['user']."'");

        // if ($arr2['status']=="在线") {
        //     $this->success("登陆成功",U('succ'));
        //     die;
        // }

        // $tj="username='".$arr['user']."'";
        $salt=$arr2['salt'];
        $session_id=session_id();  //获取session_id
        $password_user=ReturnPassword($arr['psd'],$salt); //加密后的密码
        //$password_user=md5(md5($arr['psd']).$salt);


        if($arr['user']==null || $arr['psd']==null){
            echo "<script>alert('账户或密码不能为空');window.location.replace('IntoLogin');</script>";
            die;
        }
        if($arr['user']!=$arr2['username']){
            echo "<script>alert('该用户不存在');location.href='".U('IntoLogin')."'</script>";
            die;
        }


        if($password_user==$arr2['password']){
            $_SESSION['username']=$arr['user'];
            //$_SESSION['uid']=$arr2['id'];  //数据库里的id
            if($arr2['status']=="在线"){
                $data=array('status'=>"离线");
                $obj->Save("db",'username='."'".$arr['user']."'",$data);
                echo "<script>alert('该用户已强制退出,请重新登录');location.href='".U('IntoLogin')."'</script>";
                die;
            }
            $data=array('status'=>"在线",
                        'logintime'=>date("Y-m-d H:i:s"),
                        'sessionid'=>$session_id
                        );  //登录时间
            $obj->Save("db",'username='."'".$arr['user']."'",$data);
            session('root',$arr2['root']);
            $this->success("登陆成功",U('index'));
        }
        else{
            //echo "<script>alert('账户或密码错误');location.href='".U('index')."'</script></script>";
            $this->error("登陆失败",U('IntoLogin'));  //第三个参数省略 默认3s
            die;
        }
    }

    //注册
    function RegisterId(){
        $arr=I("post.");
        // echo $arr['checkRoot'];die;
        $obj=new \LibraryWeb\Model\LoginModel;

        $ver=check_verify($arr['code']);  //获取前端传入的验证码的内容
        $_SESSION["code"]=$arr['code'];  //验证码暂时保存在session里
        if (!$ver) {
            echo "<script>alert('验证码错误');window.location.replace('".U('register')."');</script>";
            $_SESSION["code"]=null;
            die;
        }
        $salt=ReturnSalt();
        $password=ReturnPassword($arr['psd'],$salt);
        $data=array('username'=>$arr['user'],
                    'password'=>$password,
                    'salt'=>$salt,
                    'status'=>"离线",
                    'registertime'=>date("Y-m-d H:i:s"),
                );
        $yanzhen=$obj->db->where('username='."'".$arr['user']."'")->field('username')->find();
        if ($yanzhen) {
            echo "<script>alert('该用户已存在');location.href='".U('register')."'</script>";
            die;
        }
        if($arr['user']==null || $arr['psd']==null){
            echo "<script>alert('账户或密码不能为空');window.location.replace('".U('register')."');</script>";
            die;
        }
        $result=$obj->db->add($data);
        if ($arr['checkRoot']=="no") //判断是否注册管理员
            $data=array('root'=>"否");
        else
            $data=array('root'=>"是");
        $obj->Save("db",'username='."'".$arr['user']."'",$data);
        if ($result) {
            if($_GET['id']='addUser'){ //如果是从添加界面点进来的 注册完返回添加界面
                echo "<script>alert('注册成功');window.location.replace('".U('selectUser/Display_User')."');</script>";
            }else
                echo "<script>alert('注册成功');location.href='".U('IntoLogin')."'</script>";
        }
        else
            echo "<script>alert('注册失败');location.href='".U('register')."'</script>";
    }

    //管理员成功跳转页面
    function AdminSucc(){
        $this->display("/LoginSucc");
    }

    //普通用户成功跳转页面
    function UserSucc(){
        $this->display("/UserLoginSucc");
    }

    //注册页面
    function register(){
        $this->display("/Register");
    }

    //退出方法
    function LoginOut(){
        $arr=$_SESSION['username'];
        $obj=new \LibraryWeb\Model\LoginModel;
        $data=array('offlinetime'=>date("Y-m-d H:i:s"));
        $obj->Save("db",'username='."'".$_SESSION['username']."'",$data);
        // session('[destroy]');
        // echo "session:".$_SESSION['username']."<br>";
        // echo $arr;
        if ($_SESSION['username']==$arr) {
            $data=array('status'=>"离线");
            $obj->Save("db",'username='."'".$_SESSION['username']."'",$data);
            unset($_SESSION['username']);
            unset($_SESSION['id']);
            session('root',null);
            $_SESSION['balance'] = null;
            if ($_SESSION['username']==null && $_SESSION['id']==null)
                 $this->success("退出成功",U('index'));
                // echo "<script>alert('退出成功');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
        }
    }
}//enclass
