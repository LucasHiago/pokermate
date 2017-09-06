<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\User;

class UserController extends Controller{
	
	public function actionSave(){
		$loginName = trim(strip_tags((string)Yii::$app->request->post('loginName')));
		$password = trim(strip_tags((string)Yii::$app->request->post('password')));
		$qibuChoushui = (int)Yii::$app->request->post('qibuChoushui');
		$choushuiShuanfa = (int)Yii::$app->request->post('choushuiShuanfa');
		
		if(!$loginName){
			return new Response('请填写账号', -1);
		}
		if(!$password){
			return new Response('请填写密码', -1);
		}
		if(!in_array($choushuiShuanfa, [1, 2])){
			return new Response('抽水算法有误', 0);
		}
		$mUser = Yii::$app->user->getIdentity();
		$mTempUser = User::findOne(['login_name' => $loginName]);
		if($mTempUser && $mTempUser->id != $mUser->id){
			return new Response('账号已在', 0);
		}
		
		$uflag = false;
		if($loginName != $mUser->login_name){
			$mUser->set('login_name', $loginName);
			$uflag = true;
		}
		if($password != $mUser->password){
			$mUser->set('password', User::encryptPassword($password));
			$uflag = true;
		}
		if($qibuChoushui != $mUser->qibu_choushui){
			$mUser->set('qibu_choushui', $qibuChoushui);
			$uflag = true;
		}
		if($choushuiShuanfa != $mUser->choushui_shuanfa){
			$mUser->set('choushui_shuanfa', $choushuiShuanfa);
			$uflag = true;
		}
		if($uflag){
			$mUser->save();
		}
		return new Response('保存成功', 1);
	}
	
}
