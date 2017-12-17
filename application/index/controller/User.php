<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\RtmpUserModel;
use app\index\model\StreamApp;
use think\Request;
use think\Session;

class User extends Controller {

	/**
	 * 注册
	 */
	public function signin(){
		header('Content-type: text/json');
		$result = array();
		$username = input('username');
		$password = input('password');
		$nickname = input('nickname');
		$user = RtmpUserModel::signin($username,$password);
		if($user == null){
			$result['status'] = 100;
			$result['msg'] = 'Singin fail';
		}else{
			$user['nickname'] = $nickname;
			$user->save();
			$result['status'] = 200;
			$result['msg'] = 'Singin success';
		}
		return json_encode($result);
	}
	
	/**
	 * 登陆
	 */
	public function login(){
		header('Content-type: text/json');
		$result = array();
		$username = input('username');
		$password = input('password');
		$user = RtmpUserModel::login($username,$password);
		if($user == null){
			$result['status'] = 100;
			$result['msg'] = 'Login fail';
		}else{
			$result['status'] = 200;
			$result['msg'] = 'Login success';
			$result['nickname'] = $user->nickname;

			Session::set('user_token',$user->token);
			Session::set('nickname',$user->nickname);
		}
		return json_encode($result);
	}

	/**
	 * 推流检查
	 */
	public function check(){
		$appName = input('name');
		$token = input('token');
		if($appName == null || $token == null){
			header('HTTP/1.0 404 Not Found');
			exit();
		}
		if(!StreamApp::check($appName,$token)){
			header('HTTP/1.0 404 Not Found');
			exit();
		}else{
			return '';
		}
	}

	/**
	 * 创建一个直播应用
	 */
	public function createapp(Request $req){
		header('Content-type: text/json');
		
		$user = RtmpUserModel::getUserByTK($req->session('user_token'));
		$result = array();

		$appname = input('appname');
		$title = input('apptitle');
		$maintext = input('maintext');

		if(!isset($user) || !isset($appname) || !isset($title) || !isset($maintext)){
			$result['status'] = 101;
			$result['msg'] = 'Error input';
		}else{
			$app = StreamApp::createApp($appname,$title,$maintext,$user->username);
			if($app == null){
				$result['status'] = 100;
				$result['msg'] = 'Create app fail';
			}else{
				$result['status'] = 200;
				$result['msg'] = 'Create app success';
			}
		}
		

		return json_encode($result);
	}
	
	/**
	 * 更新直播信息
	 */
	public function appupdate(Request $req) {
		header('Access-Control-Allow-Origin:*');
		header('Content-type: text/json');
		
		$user = RtmpUserModel::getUserByTK($req->session('user_token'));
		
		
		$result = array();
		$result['status'] = 0;
		
		if($user == null){
			$result['status'] = 403;
			$result['msg'] = 'Forbidden';
		}else{
			$appname = input('app');
			if(isset($appname)){
				$app = StreamApp::where(['appname' => $appname ,'username' => $user->username])->find();
				if($app == null) {
					$result['status'] = 404;
					$result['msg'] = 'App Not Found';
				}else{
					$result['status'] = 200;
					$result['msg'] = 'Success';
					
					$title = input('title');
					if(isset($title))
						$app->title = $title;
					$maintext = input('maintext');
					if(isset($maintext))
						$app->maintext = $maintext;
					
					$app->save();
					
						
					
				}
			}else{
				$result['status'] = 101;
				$result['msg'] = 'Error Input';
			}
		}
		
		return json_encode($result);
	}
	
	/**
	 * 获取直播列表信息
	 */
	public function apps(Request $req){
		header('Access-Control-Allow-Origin:*');
		header('Content-type: application/json');
		$q = input('q');
		
		$result = array();
		$result['status'] = 0;
		$result['src'] = 'http://'.HOST.'/watch/?app=';
		$result['apps'] = array();
		switch($q) {
			case 1:
				$user = RtmpUserModel::getUserByTK($req->session('user_token'));
				if($user == null){
					$result['status'] = 403;
					$result['msg'] = 'Forbidden';
				}else{
					$apps = StreamApp::where(['username' => $user->username])->select();
					foreach($apps as $app){
						array_push($result['apps'],
							array('title' => $app->title ,
							'maintext' => $app->maintext ,
							'appname' => $app->appname ,
							'alive' => file_exists('/media/xingkong/Data/Stream/hls/'.$app->appname.'.m3u8'),
							'token' => $app->token));
					}
				}
				break;
			default:
				$apps = StreamApp::all();
				$result['status'] = 200;
				foreach($apps as $app){
					$item = array('title' => $app->title ,
								'maintext' => $app->maintext ,
								'appname' => $app->appname , 
								'alive' => file_exists('/media/xingkong/Data/Stream/hls/'.$app->appname.'.m3u8'));
					$us = RtmpUserModel::get(['username' => $app->username]);
					if($us != null) {
						$item['user'] = $us->nickname;
						$item['username'] = $us->username;
					}						
					$index = array_push($result['apps'],$item);

				}
				
		}
		
		echo json_encode($result);
	}
	
	/**
	 * 获取单个直播信息
	 */
	public function app(Request $req) {
		header('Access-Control-Allow-Origin:*');
		header('Content-type: text/json');
		$appname = input('app');
		$user = RtmpUserModel::getUserByTK($req->session('user_token'));
		$result = array();
		
		if(!isset($appname)){
			$result['status'] = 101;
			$result['msg'] = 'Error Input';
		}else{
			
			$app = StreamApp::getApp($appname);
			if($app == null){
				$result['status'] = 404;
				$result['msg'] = 'App Not Found';
			}else{
				$info = array('appname' => $app->appname,'title' => $app->title,'maintext' => $app->maintext);
				
				if($user == null){
					$result['status'] = 403;
					$result['result'] = $info;
					$result['msg'] = 'Forbidden';
				}else{
					
					if(strcmp($user->username,$app->username) == 0){
						$result['status'] = 200;
						$result['result'] = $app;
						$result['msg'] = 'Success';
					}else{
						$result['status'] = 403;
						$result['result'] = $info;
						$result['msg'] = 'Forbidden';
					}

				}
				
				$result['src'] = 'http://live.xingkong.us/hls/'.$appname.'.m3u8';
				$result['alive'] = file_exists('/media/xingkong/Data/Stream/hls/'.$appname.'.m3u8');
			}
			
		}
		return json_encode($result);
	}

}