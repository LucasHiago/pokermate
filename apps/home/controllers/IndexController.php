<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use yii\validators\EmailValidator;
use umeworld\lib\PhoneValidator;
use common\model\User;

class IndexController extends Controller{
	
	public function actionIndex(){
		return $this->render('home');
	}
	
}
