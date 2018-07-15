<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\RtmpUserModel;
use app\index\model\StreamApp;
use app\index\model\StreamGroup;
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
	 * 获取用户信息
	 */
	public function user(Request $req) {
		header('Content-type: text/json');
		$username = input('username');
		$result = array();
		
		
		if(isset($username)) {
			//参数username存在时，查询该用户的信息
			$user = RtmpUserModel::where(['username' => $username])->find();
			if(isset($user)) {
				$result['status'] = 200;
				$result['msg'] = 'Success';
				$result['userinfo'] = array('username' => $user->username,'nickname' => $user->nickname);
			} else {
				$result['status'] = 404;
				$result['msg'] = 'Username Not Found';
			}
		}else{
			//参数username不存在时，获取已登陆用户的信息
			$user = RtmpUserModel::getUserByTK($req->session('user_token'));
			if(isset($user)) {
				$result['status'] = 200;
				$result['msg'] = 'Success';
				$result['userinfo'] = array('username' => $user->username,'nickname' => $user->nickname);
			} else {
				$result['status'] = 403;
				$result['msg'] = 'Forbidden';
			}
		}
		
		return json_encode($result);
	}
	
/* 	public function upload(){
		// 获取表单上传文件 例如上传了001.jpg
		$file = request()->file('image');
		
		// 移动到框架应用根目录/public/uploads/ 目录下
		if($file){
			$info = $file->rule('uniqid')->validate(['size'=>1024*1024*2 ,'ext'=>'jpg,png,gif,bmp'])->move(ROOT_PATH . 'publish' . DS . 'uploads/','1.jpg');
			//$file->move(ROOT_PATH . 'publish' . DS . 'uploads');
			if($info){
				// 成功上传后 获取上传信息
				// 输出 jpg
				echo $info->getExtension();
				// 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
				echo $info->getSaveName();
				// 输出 42a79759f284b767dfcb2a0197904287.jpg
				echo $info->getFilename(); 
			}else{
				// 上传失败获取错误信息
				echo $file->getError();
			}
			
			
		}
		return $file;
	} */


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
					$result['status'] = 200;
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
				
				$result['src'] = 'http://'.HOST.'/hls/'.$appname.'.m3u8';
				$result['alive'] = file_exists('/media/xingkong/Data/Stream/hls/'.$appname.'.m3u8');
			}
			
		}
		return json_encode($result);
	}
	
	/**
	 * 创建一个直播组
	 */
	public function creategroup(Request $req){
		header('Content-type: text/json');
		
		$user = RtmpUserModel::getUserByTK($req->session('user_token'));
		$result = array();

		$name = input('name');
		$title = input('title');
		$maintext = input('maintext');
		$apps = input('apps');
		
		if(!isset($user) || !isset($name) || !isset($title) || !isset($maintext)){
			$result['status'] = 101;
			$result['msg'] = 'Error input';
		}else{
			$apps = json_decode($apps);
			$p_apps = array();
			foreach($apps as $tmp){
				if(isset($tmp))
					array_push($p_apps,$tmp);
			}
			//echo $apps;
			$app = StreamGroup::createGroup($name,$title,$maintext,$user->username,$p_apps);
			if($app == null){
				$result['status'] = 100;
				$result['msg'] = 'Create group fail';
			}else{
				$result['status'] = 200;
				$result['msg'] = 'Create group success';
			}
		}
		

		return json_encode($result);
	}

	/**
	 * 获取单个直播组信息
	 */
	public function group(Request $req) {
		header('Access-Control-Allow-Origin:*');
		header('Content-type: text/json');
		$groupname = input('group');
		$user = RtmpUserModel::getUserByTK($req->session('user_token'));
		$result = array();
		
		if(!isset($groupname)){
			$result['status'] = 101;
			$result['msg'] = 'Error Input';
		}else{
			
			$group = StreamGroup::getGroup($groupname);
			if($group == null){
				$result['status'] = 404;
				$result['msg'] = 'Group Not Found';
			}else{
				$isCreater = (isset($user) && strcmp($user->username,$group->username) == 0);
				if($isCreater){
					$result['status'] = 200;
					$result['msg'] = 'Success';
				}else{
					$result['status'] = 403;
					$result['msg'] = 'Forbidden';
				}
				$result['name'] = $group->groupname;
				$result['title'] = $group->grouptitle;
				$result['maintext'] = $group->groupmaintext;
				
				if(isset($user)){
					$result['user'] = $user->nickname;
					$result['username'] = $user->username;
				}

				$result['apps'] = array();
				$apps = json_decode($group->apps);
				foreach($apps as $appname) {
					$app = StreamApp::getApp($appname);
					if(isset($app)){
						$info = array();
						$info['appname'] = $app->appname;
						$info['title'] = $app->title;
						$info['maintext'] = $app->maintext;
						if($isCreater)
							$info['token'] = $app->token;
						$info['src'] = 'http://'.HOST.'/hls/'.$appname.'.m3u8';
						$info['alive'] = file_exists('/media/xingkong/Data/Stream/hls/'.$appname.'.m3u8');
						array_push($result['apps'],$info);
					}

				}

				

			}
			
		}
		return json_encode($result);
	}
}