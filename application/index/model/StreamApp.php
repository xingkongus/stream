<?php
namespace app\index\model;
use think\Model;
use app\index\model\RtmpUserModel;

class StreamApp extends Model {
	protected $name = "app";

	protected $fields = array(
		'id',
		'appname',
		'title',
		'username',
		'token',
        'maintext'
    );

    public static function createApp($name,$title,$maintext,$username){
    	if(!RtmpUserModel::isUserExist($username) || StreamApp::isAppExist($name))
    		return null;

    	$app = new StreamApp;
    	$app->appname = $name;
    	$app->username = $username;
    	$app->title = $title;
        $app->maintext = $maintext;
    	$app->token = RtmpUserModel::makeToken();
    	$app->save();
    	return $app;
    }

    public static function isAppExist($appName) {
    	return StreamApp::get(['appname' => $appName]) != null;
    }

    public static function check($appName,$token){
    	return StreamApp::get(['token' => $token,'appname' => $appName]) != null;
    }

    public static function getApps($username){
        $result = array();
        $arr = StreamApp::where(['username' => $username])->select();
        if(!isset($arr))
            return $result;
        return $arr;
    }
	
    public static function getApp($appname){
        return StreamApp::get(['appname' => $appname]);
    }
}