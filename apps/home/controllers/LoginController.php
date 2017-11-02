<?php
namespace home\controllers;

use Yii;
use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
//use home\lib\Controller;
use umeworld\lib\Response;
use yii\validators\EmailValidator;
use umeworld\lib\PhoneValidator;
use common\model\User;
use umeworld\lib\CaptchaAction;

class LoginController extends Controller{
	
	public function actions(){
		return [
			'error' => [
				'class' => 'umeworld\lib\ErrorAction',
			],
			'captcha' => [
				'class' => 'umeworld\lib\CaptchaAction',
				'minLength' => 5,
				'maxLength' => 5,
			],
		];
	}
		
	public function actionLogin(){
		$account = trim(strip_tags((string)Yii::$app->request->post('account')));
		$password = trim((string)Yii::$app->request->post('password'));
		$captcha = trim((string)Yii::$app->request->post('captcha'));
		
		$isEmail = (new EmailValidator())->validate($account);
		$isMobile = (new PhoneValidator())->validate($account);
		if(!$isEmail && !$isMobile){
			//return new Response('账号必须是邮箱或手机', -1);
		}
		if(!$password || strlen($password) < 6 || strlen($password) > 20){
			return new Response('密码长度为6~20个字符', -1);
		}
		if(!$captcha){
			return new Response('请输入验证码', -1);
		}
		if(!CaptchaAction::validateCaptcha($captcha, 'login/captcha')){
			return new Response('验证码不正确', -1);
		}
		$mUser = false;
		if($isEmail){
			$mUser = User::findOne(['email' => $account]);
		}
		if($isMobile){
			$mUser = User::findOne(['mobile' => $account]);
		}
		if(!$mUser){
			$mUser = User::findOne(['login_name' => $account]);
		}
		if(!$mUser){
			return new Response('账号或密码不正确', -1);
		}
		if($mUser->password != User::encryptPassword($password)){
			return new Response('密码不正确', -1);
		}
		if(!$mUser->isVip()){
			return new Response('VIP已到期', -1);
		}
		if($mUser->is_forbidden){
			return new Response('账号已删除', -1);
		}
		if(!Yii::$app->user->login($mUser, true)){
			return new Response('登录失败', 0);
		}
		return new Response('登录成功', 1, Url::to('home', 'index/index'));
	}
	
	public function actionLogout(){
		Yii::$app->user->logout();
		return Yii::$app->response->redirect(Url::to('home', 'site/index'));
	}
}
