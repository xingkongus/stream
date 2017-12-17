<?php
namespace app\index\model;
use think\Model;

class RtmpUserModel extends Model {
	protected $name = 'rtmp_user';

	protected $fields = array(
		'id',
		'username',
		'password',
		'utype',
		'nickname',
		'token'
    );

    public static function signin($username,$password) {
    	if(strlen(trim($username)) < 6 || strlen(trim($password)) < 6 || RtmpUserModel::isUserExist($username))
    		return null;
    	$user = new RtmpUserModel;
    	$user->username = $username;
    	$user->password = RtmpUserModel::passwordEncode($password);
    	//$user->nickname = "";
    	$user->token = RtmpUserModel::makeToken();
    	$user->utype = 1;
    	$user->save();
    	return $user;
    }

    public static function login($username,$password){
    	if(strlen(trim($username)) < 6 || strlen(trim($password)) < 6 || ($user = RtmpUserModel::getUserByUP($username,$password)) == null)
    		return null;
    	$user->token = RtmpUserModel::makeToken();
    	$user->save();
    	return $user;
    }

    public static function isUserExist($username) {
    	return RtmpUserModel::get(['username' => $username]) != null;
    }

    public static function getUserByUP($username,$password){
    	return RtmpUserModel::get(['username' => $username,'password' => RtmpUserModel::passwordEncode($password)]);
    }

    public static function getUserByTK($token){
    	return RtmpUserModel::get(['token' => $token]);
    }

    public static function makeToken(){
    	do{
    		$uniqid = md5(uniqid(microtime(true),true));
    	}while(RtmpUserModel::getUserByTK($uniqid) != null);
		return $uniqid;
    }

    public static function passwordEncode($in){
    	return md5($in);
    }


}
