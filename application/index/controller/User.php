<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/15
 * Time: 9:51
 */

namespace app\index\controller;
use think\Controller;
use think\Paginator;
use think\Session;
use \think\Request;//防注入
use think\Db;
class User extends Controller
{
    public function doLogin(){
        session_start();
        $config=config('msg')['login'];
        if(input('?post.user')&&input('?post.pwd')&&input('?post.code')){
            $uid=input('post.user');
            $pwd=input('post.pwd');
            $code=input('post.code');
            if(!captcha_check($code)) {
                // 校验失败
                /*$this->error('验证码不正确');*/
                return ['code'=>1002,'msg'=>$config['code_error'],'data'=>''];
            }
            else{
                $where=[
                    'uid'=>$uid,
                    'pwd'=>md5($pwd)
                ];
                $data=Db::table('tbl_user')->where($where)->fetchSql(false)->find();
                if(empty($data)){
                    return ['code'=>1001,'msg'=>$config['login_error'],'data'=>''];
                }
                else{
                    $_SESSION['hf170615']=$data;
                    if(input('?post.auto'))
                    {
                        setcookie("hf170615",json_encode($data),time()+7*24*3600);
                    }
                    return ['code'=>1000,'msg'=>$config['login_success'],'data'=>$data];
                }
            }
        }
        else{
            return ['code'=>1003,'msg'=>$config['login_null'],'data'=>''];
        }
    }
    public function getTitle(){
        session_start();
        $nowPage=isset($_POST['nowPage'])?$_POST['nowPage']:1;
        $uid=$_SESSION['hf170615']['uid'];
        $this->assign('uid',$uid);
        $where=[
            'uid'=>$uid,
            'del'=>1
        ];
        $allRow=Db::table('tbl_title')->where($where)->count();
        $count=3;
        $pageAll=ceil($allRow/$count);
        if($nowPage>$pageAll){
            $nowPage=$pageAll;
        }
        if($nowPage<1){
            $nowPage=1;
        }
        $last=$nowPage-1;
        $next=$nowPage+1;
        $start=($nowPage-1)*$count;
        $data=Db::table('tbl_title')->where($where)->limit($start,$count)->select();
        $pageInfo=['last'=>$last,
                  'next'=>$next,
                  'nowPage'=>$nowPage,
                  'pageAll'=>$pageAll];
        echo json_encode([$data,$pageInfo,$allRow]);
    }
    public function delTitle(){
        $tid=$_POST['tid'];
        $res=Db::table('tbl_title')->where('tid',$tid)->setField('del',0);
        echo $res;
    }
    public function addTitle(){
        session_start();
        $uid=$_SESSION['hf170615']['uid'];
        $title=$_POST['title'];
        $_SESSION['title']=$title;
        $data=[
            'tname'=>$title,
            'time'=>date('Y-m-d H:i:s',time()),
            'uid'=>$uid,
            'del'=>1
        ];
        $res= Db::table('tbl_title')->insert($data);
        echo $res;
    }
}