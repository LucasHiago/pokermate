<?php
namespace home\widgets;

use Yii;
use yii\base\Widget;

class Navi extends Widget{
	public function run(){
		$aUser = [];
		$role = '';
		$mManager = Yii::$app->user->getIdentity();
		if($mManager->isManager()){
			$aUser = $mManager->toArray();
			$role = 'manager';
		}else{
			$aUser = $mManager->toArray();
			$role = 'user';
		}
		$aMenuConfig = Yii::$app->params['menu_config'];
		return $this->render('navi', [
			'aUser' => $aUser,
			'role' => $role,
			'aMenuConfig' => $aMenuConfig,
		]);
	}
}