<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Cache;

class Index extends Controller
{
    public function homepage()
    {
        return $this->fetch();
    }
    public function login(){
        return $this->fetch();
    }
    public function main(){
        session_start();
        $uid=$_SESSION['hf170615']['uid'];
        $this->assign('uid',$uid);
        $where=[
            'uid'=>$uid,
            'del'=>1
        ];
        if(input('?get.keyWord')){
            $keyWord=input('get.keyWord');
            $options = [
                // 缓存类型为File
                'type'   => 'File',
                // 缓存有效期为永久有效
                'expire' => 0,
                // 指定缓存目录
                'path'   => APP_PATH . 'runtime/cache/',
            ];
            // 缓存初始化:不进行缓存初始化的话，默认使用配置文件中的缓存配置
            //cache($options);

            $wjList=Cache::get('t_'.$keyWord);
            $page=Cache::get('t_Page'.$keyWord);
            if(!$wjList){
                echo '没有缓存';
                $wjList=Db::table('tbl_title')
                    ->fetchSql(false)
                    ->where($where)
                    ->where('tname','like','%'.$keyWord.'%')
                    ->paginate(3);
                // 设置缓存数据
                cache('t_Page'.$keyWord, $wjList->render(), 3600);
                cache('t_'.$keyWord, $wjList, 3600);
                $this->assign('wjList',$wjList);
                $this->assign('page',$wjList->render());
            }
            else{
                echo '有缓存';
                $this->assign('wjList',$wjList['data']);
                $this->assign('page',$page);
            }
        }
        else{
            $wjList=Db::table('tbl_title')->where($where)->paginate(3);
            $this->assign('wjList',$wjList);
            $this->assign('page',$wjList->render());
        }
        return $this->fetch();
    }
    public function select(){
        session_start();
        $uid=$_SESSION['hf170615']['uid'];
        $this->assign('uid',$uid);
        return $this->fetch();
    }
    public function questionnaire(){
        session_start();
        $uid=$_SESSION['hf170615']['uid'];
        $this->assign('uid',$uid);
        return $this->fetch();
    }
    public function create(){
        session_start();
        $title=$_SESSION['title'];
        $this->assign('title',$title);
        return $this->fetch();
    }
}
