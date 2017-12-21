<?php
namespace app\index\model;
use think\Model;

class StreamGroup extends Model{
	protected $name = 'app_group';

	protected $fields = array(
            'id',
			'apps',
			'username',
			'groupname',
			'grouptitle',
			'groupmaintext'
        );
		
    public static function createGroup($name,$title,$maintext,$username,$apps){
    	if(!RtmpUserModel::isUserExist($username) || StreamGroup::isGroupExist($name))
    		return null;

    	$group = new StreamGroup;
    	$group->groupname = $name;
    	$group->username = $username;
    	$group->grouptitle = $title;
        $group->groupmaintext = $maintext;
		$group->apps = json_encode($apps);
    	$group->save();
    	return $group;
    }
	
	public static function isGroupExist($groupName) {
    	return StreamGroup::get(['groupname' => $groupName]) != null;
    }
	
	public static function getGroup($groupName){
        return StreamGroup::get(['groupname' => $groupName]);
    }
	
    public static function getGroups($username){
        $result = array();
        $arr = StreamGroup::where(['username' => $username])->select();
        if(!isset($arr))
            return $result;
        return $arr;
    }
}

?>