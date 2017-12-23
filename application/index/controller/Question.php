<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/17
 * Time: 21:05
 */

namespace app\index\controller;
use think\Controller;
use think\Db;

class Question extends Controller
{
    public function setDetail(){
        session_start();
        $title=$_REQUEST['tname'];
        $_SESSION['title']=$title;
        echo $title;
    }
    public function getQuestion(){
        session_start();
        $uid=$_SESSION['hf170615']['uid'];
        $title=$_SESSION['title'];
        // 返回某个字段的值
        $tid=Db::table('tbl_title')->where('tname',$title)->value('tid');
        $where=[
            't2.uid'=>$uid,
            't2.tname'=>$title,
            't1.del'=>1
        ];
        $question=Db::table('tbl_question')
            ->alias('t1')
            ->join('tbl_title t2','t1.tid=t2.tid')
            ->join('tbl_option t3','t1.qid=t3.qid')
            ->field('t1.*,count(t1.qid) count')
            ->group('t1.qid')
            ->where($where)
            ->select();
        $option=Db::table('tbl_question')
            ->alias('t1')
            ->join('tbl_title t2','t1.tid=t2.tid')
            ->join('tbl_option t3','t1.qid=t3.qid')
            ->field('t3.*')
            ->where($where)
            ->select();
        $num=Db::table('tbl_question')
            ->alias('t1')
            ->join('tbl_title t2','t1.tid=t2.tid')
            ->join('tbl_option t3','t1.qid=t3.qid')
            ->field('count(t1.qid) count')
            ->group('t1.qid')
            ->where($where)
            ->select();
        echo json_encode([$question,$num,$option,$tid]);
    }
    public function delQuestion(){
        $qid=$_POST['qid'];
        $res=Db::table('tbl_question')->where('qid',$qid)->setField('del',0);
        echo $res;
    }
    public function addQuestion(){
        session_start();
        $uid=$_SESSION['hf170615']['uid'];
        $title=$_SESSION['title'];
        $question=$_POST['question'];
        $option=$_POST['option'];
        $qid=$_POST['qid'];
        $result=Db::table('tbl_option')->where('qid',$qid)->delete();
        $data=Db::table('tbl_question')->where('qid',$qid)->find();
        $where=['tname'=>$title,'uid'=>$uid];
        $tidArr=Db::table('tbl_title')->where($where)->field('tid')->fetchSql(true)->select();
        var_dump($tidArr);exit;
        $tid=$tidArr[0]['tid'];
        if(empty($data)){
            $data=[
                'qnamae'=>$question,
                'tid'=>$tid,
                'del'=>1
            ];
            $res= Db::table('tbl_question')->insert($data);
            if($res!=0){
                $data=Db::table('tbl_question')->max('qid');
                $qid=$data[0]['max'];
                foreach($option as $val){
                    $data=['oname'=>$val,'qid'=>$qid,'tid'=>$tid];
                    $res= Db::table('tbl_option')->insert($data);
                }
                echo $res;
            }
            else{
                echo 'no';
            }
        }
        else{
            $data=['qnamae'=>$question,'tid'=>$tid,'del'=>1];
            $res= Db::table('tbl_question')->insert($data);
            $data=Db::table('tbl_question')->max('qid');
            $qid=$data[0]['max'];
            foreach($option as $val){
                $res=$this->model->addData('tbl_option','(oname,qid,tid)',"'{$val}','{$qid}','{$tid}'");
            }
            echo $res;
        }
    }
}