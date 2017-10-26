<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\User;
use common\model\Club;

class ClubController extends Controller{
	
	public function actionSave(){
		$id = (int)Yii::$app->request->post('id');
		$clubName = trim(strip_tags((string)Yii::$app->request->post('clubName')));
		$clubId = (int)Yii::$app->request->post('clubId');
		$clubLoginName = trim(strip_tags((string)Yii::$app->request->post('clubLoginName')));
		$clubLoginPassword = trim(strip_tags((string)Yii::$app->request->post('clubLoginPassword')));
		
		if(!$clubName){
			return new Response('请填写俱乐部名称', -1);
		}
		if(!$clubId){
			return new Response('请填写俱乐部ID', -1);
		}
		if(!$clubLoginName){
			return new Response('请填写登录账户', -1);
		}
		if(!$clubLoginPassword){
			return new Response('请填写登录密码', -1);
		}
		
		$mUser = Yii::$app->user->getIdentity();
		
		if($id){
			$mClub = Club::findOne($id);
			if(!$mClub){
				return new Response('俱乐部不存在', -1);
			}
			$mClub->set('club_name', $clubName);
			$mClub->set('club_id', $clubId);
			$mClub->set('club_login_name', $clubLoginName);
			$mClub->set('club_login_password', $clubLoginPassword);
			$mClub->save();
		}else{
			$mClub = Club::findOne([
				'user_id' => $mUser->id,
				'club_id' => $clubId,
			]);
			if($mClub){
				return new Response('俱乐部已存在', 0);
			}
			$bindClubLimitCount = $mUser->getBindClubLimitCount();
			$aClubList = $mUser->getUserClubList();
			if(count($aClubList) >= $bindClubLimitCount){
				return new Response('您的会员等级只能绑定' . $bindClubLimitCount . '个俱乐部', -1);
			}
			$isSuccess = Club::addRecord([
				'user_id' => $mUser->id,
				'club_name' => $clubName,
				'club_id' => $clubId,
				'club_login_name' => $clubLoginName,
				'club_login_password' => $clubLoginPassword,
				'create_time' => NOW_TIME,
			]);
			if(!$isSuccess){
				return new Response('保存失败', 0);
			}
			$id = $isSuccess;
		}
		
		return new Response('保存成功', 1, $id);
	}
	
	public function actionDelete(){
		$id = (int)Yii::$app->request->post('id');
		
		$mClub = Club::findOne($id);
		if(!$mClub){
			return new Response('俱乐部不存在', -1);
		}
		if($mClub->user_id != Yii::$app->user->id){
			return new Response('出错了', 0);
		}
		$mClub->set('is_delete', 1);
		$mClub->save();
		return new Response('删除成功', 1);
	}
}
