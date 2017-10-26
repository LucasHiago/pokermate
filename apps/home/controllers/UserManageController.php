<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\form\UserListForm;
use common\model\User;

class UserManageController extends Controller{
	public $layout = 'manage';
	
	public function init(){
		$mUser = Yii::$app->user->getIdentity();
		if(!$mUser->isManager()){
			return Yii::$app->response->redirect(Url::to('home', 'club-manage/index'));
		}
		return parent::init();
	}
	
	public function actionIndex(){
		$oUserListForm = new UserListForm();
		$aParams = Yii::$app->request->get();
		if($aParams && (!$oUserListForm->load($aParams, '') || !$oUserListForm->validate())){
			return new Response(current($oUserListForm->getErrors())[0]);
		}
		$aList = $oUserListForm->getList();
		$oPage = $oUserListForm->getPageObject();
		
		return $this->render('index', [
			'aUserList' => $aList,
			'oPage' => $oPage,
			'loginName' => $oUserListForm->loginName,
			'userName' => $oUserListForm->userName,
		]);
	}
	public function actionSetForbiddenUser(){
		$id = (int)Yii::$app->request->post('id');
		$status = (int)Yii::$app->request->post('status');
		
		$mUser = User::findOne($id);
		if(!$mUser){
			return new Response('账号不存在', -1);
		}
		$mUser->set('is_forbidden', $status);
		$mUser->save();
		
		return new Response('操作成功', 1);
	}
	
	public function actionShowEdit(){
		$id = Yii::$app->request->get('id');
		
		$mUser = User::findOne($id);
		$aUser = [];
		if($mUser){
			$aUser = $mUser->toArray();
			$aUser['vip_day'] = 0;
			if($mUser->vip_expire_time > NOW_TIME){
				$aUser['vip_day'] = ceil(($mUser->vip_expire_time - NOW_TIME) / 86400);
			}
		}
		
		return $this->render('edit', [
			'aUser' => $aUser,
		]);
	}
	
	public function actionEdit(){
		$id = (int)Yii::$app->request->post('id');
		$type = (int)Yii::$app->request->post('type');
		$name = (string)Yii::$app->request->post('name');
		$loginName = (string)Yii::$app->request->post('loginName');
		$password = (string)Yii::$app->request->post('password');
		$enPassword = (string)Yii::$app->request->post('enPassword');
		$vipLevel = (int)Yii::$app->request->post('vipLevel');
		$vipExpireTime = (string)Yii::$app->request->post('vipExpireTime');
		$qibuChoushui = (int)Yii::$app->request->post('qibuChoushui');
		$choushuiShuanfa = (int)Yii::$app->request->post('choushuiShuanfa');
		
		if(!$name || StringHelper::getStringLength($name) > 20){
			return new Response('姓名名长度为1~20个字', -1);
		}
		
		$mUser = false;
		
		if(!in_array($type, [User::TYPE_NORMAL, User::TYPE_MANAGE])){
			return new Response('账号类型不正确', -1);
		}
		if(!$loginName){
			return new Response('请输入账号名', -1);
		}
		if($vipLevel < 0){
			return new Response('VIP等级不正确', -1);
		}
		if($qibuChoushui < 0){
			return new Response('起步抽水不能小于0', -1);
		}
		if(!in_array($choushuiShuanfa, [User::CHOUSHUI_SHUANFA_YUSHUMOLIN, User::CHOUSHUI_SHUANFA_SISHIWURU])){
			return new Response('抽水算法不正确', -1);
		}
		if($password){
			if(!$password || strlen($password) < 6 || strlen($password) > 20){
				return new Response('密码长度为6~20个字符', -1);
			}
			if($password != $enPassword){
				return new Response('密码不一至', -1);
			}
		}
		if($id){
			$mUser = User::findOne($id);
			if(!$mUser){
				return new Response('账号不存在', 0);
			}
			$mUser->set('type', $type);
			$mUser->set('name', $name);
			$mUser->set('qibu_choushui', $qibuChoushui);
			$mUser->set('choushui_shuanfa', $choushuiShuanfa);
			$mUser->set('vip_level', $vipLevel);
			//$mUser->set('vip_expire_time', strtotime($vipExpireTime));
			$mUser->set('vip_expire_time', (((int)$vipExpireTime) * 86400 + NOW_TIME));
			if($password){
				$mUser->set('password', User::encryptPassword($password));
			}
			$mUser->save();
		}else{
			if(!$loginName || StringHelper::getStringLength($loginName) > 20){
				return new Response('账号名长度为1~20个字', -1);
			}
			$mUser = User::findOne(['login_name' => $loginName]);
			if($mUser){
				return new Response('账号已存在', -1);
			}
			$mUser = User::register([
				'type' => $type,
				'login_name' => $loginName,
				'name' => $name,
				'password' => $password,
				'qibu_choushui' => $qibuChoushui,
				'choushui_shuanfa' => $choushuiShuanfa,
				'vip_level' => $vipLevel,
				'vip_expire_time' => (((int)$vipExpireTime) * 86400 + NOW_TIME),
				'create_time' => NOW_TIME,
			]);
			if(!$mUser){
				return new Response('保存失败', 1);
			}
		}
		return new Response('保存成功', 1);
	}
	
	public function actionClearUserData(){
		$saveCode = (string)Yii::$app->request->post('saveCode');
		
		if(!$saveCode){
			return new Response('请输入安全码', -1);
		}
		
		$mUser = Yii::$app->user->getIdentity();
		if($mUser->save_code != $saveCode){
			return new Response('安全码不正确', -1);
		}
		if($mUser->type == User::TYPE_MANAGE){
			return new Response('超级管理员账号不能清除数据', -1);
		}
		
		$mUser->clearUserData();
		
		return new Response('清除数据成功', 1);
	}
	
}
