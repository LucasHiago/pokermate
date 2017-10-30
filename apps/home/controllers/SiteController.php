<?php
namespace home\controllers;

use Yii;
use umeworld\lib\Controller;
//use home\lib\Controller;
use umeworld\lib\Response;

class SiteController extends Controller{
	
	public function actionIndex(){
		 $this->layout = 'login'; 
		return $this->render('index');
	}

	public function actionTest(){
		$sql = "SELECT * from lianmeng where lmzj_paiju_creater!=''";
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		debug($aResult);
		foreach($aResult as $value){
			$name = $value['lmzj_paiju_creater'];
			$value['lmzj_paiju_creater'] = '';
			$mLianmeng = \common\model\Lianmeng::toModel($value);
			$mLianmeng->set('lmzj_paiju_creater', [$name]);
			$mLianmeng->save();
		}
		debug('ok');
	}
}
