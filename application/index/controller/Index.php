<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\RtmpUserModel;
use app\index\model\StreamApp;
use think\Request;
use think\Url;
use think\Session;

url::root('?s=');

class Index extends Controller
{
    public function index(Request $req)
    {

    	$user = RtmpUserModel::getUserByTK($req->session('user_token'));
        $this->assign('page',0);
    	$this->assign('user',$user);

    	return $this->fetch('header').$this->fetch().$this->fetch('footer');
    }

    public function createapp(Request $req){
        $user = RtmpUserModel::getUserByTK($req->session('user_token'));

        $this->assign('page',-1);
        $this->assign('user',$user);

        return $this->fetch('header').$this->fetch().$this->fetch('footer');
    }

    public function about(Request $req){
        $user = RtmpUserModel::getUserByTK($req->session('user_token'));
        $this->assign('page',2);
        $this->assign('user',$user);

        return $this->fetch('header').$this->fetch().$this->fetch('footer');
    }

    public function app(Request $req){
        $user = RtmpUserModel::getUserByTK($req->session('user_token'));
        if(!isset($user))
             $this->error('很抱歉,您必须先登录。','index/login');
         $apps = StreamApp::getApps($user->username);
        $this->assign("list",$apps);
        $this->assign('page',1);
        $this->assign('user',$user);

        return $this->fetch('header').$this->fetch('app').$this->fetch('footer');
    }

    public function login(Request $req){
        $user = RtmpUserModel::getUserByTK($req->session('user_token'));
        $this->assign('page',-1);
        $this->assign('user',$user);

        return $this->fetch('header').$this->fetch().$this->fetch('footer');
    }

    public function logout(){
        Session::set("user_token",null);
        $this->redirect('index/index', array(), 5, 'Logout success.');
    }

    public function signin(Request $req){
        $user = RtmpUserModel::getUserByTK($req->session('user_token'));
        $this->assign('page',-1);
        $this->assign('user',$user);

        return $this->fetch('header').$this->fetch().$this->fetch('footer');
    }

}
