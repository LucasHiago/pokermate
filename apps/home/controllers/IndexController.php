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
		$mUser = Yii::$app->user->getIdentity();
		$aMoneyTypeList = $mUser->getMoneyTypeList();
		$aMoneyOutPutTypeList = $mUser->getMoneyOutPutTypeList();
		$moneyTypeTotalMoney = $mUser->getMoneyTypeTotalMoney();
		$moneyOutPutTypeTotalMoney = $mUser->getMoneyOutPutTypeTotalMoney();
		
		return $this->render('home', [
			'aMoneyTypeList' => $aMoneyTypeList,
			'aMoneyOutPutTypeList' => $aMoneyOutPutTypeList,
			'moneyTypeTotalMoney' => $moneyTypeTotalMoney,
			'moneyOutPutTypeTotalMoney' => $moneyOutPutTypeTotalMoney,
		]);
	}
	
}
