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
	
	private function _ajustFengChengSettingSort(){
		set_time_limit(0);
		$aUserList = \common\model\User::findAll();
		$aFenchengSetting = \common\model\FenchengSetting::getFenchengConfigList();
		foreach($aUserList as $aUser){
			$mUser = \common\model\User::toModel($aUser);
			$aAgentList = $mUser->getAgentList();
			foreach($aAgentList as $aAgent){
				$aFcsting = $mUser->getFenchengListSetting($aAgent['id']);
				$aFcstingCp = $aFcsting;
				
				foreach($aFenchengSetting as $i => $aFencheng){
					$mFenchengSetting = \common\model\FenchengSetting::toModel($aFcsting[$i]);
					$mFenchengSetting->set('zhuozi_jibie', $aFencheng);
					foreach($aFcstingCp as $acp){
						if($acp['zhuozi_jibie'] == $aFencheng){
							$mFenchengSetting->set('yingfan', $acp['yingfan']);
							$mFenchengSetting->set('shufan', $acp['shufan']);
							break;
						}
					}
					$mFenchengSetting->save();
				}
			}
			
		}
		
		debug('complete');
	}

	public function actionTest(){
		$sql = 'DELETE FROM ' . \common\model\FenchengSetting::tableName() . ' WHERE `zhuozi_jibie`="4/8"';
		Yii::$app->db->createCommand($sql)->execute();debug('complete');
		//$this->_ajustFengChengSettingSort();
		
		/*$sql = "SELECT * from lianmeng where lmzj_paiju_creater!=''";
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		debug($aResult);
		foreach($aResult as $value){
			$name = $value['lmzj_paiju_creater'];
			$value['lmzj_paiju_creater'] = '';
			$mLianmeng = \common\model\Lianmeng::toModel($value);
			$mLianmeng->set('lmzj_paiju_creater', [$name]);
			$mLianmeng->save();
		}
		debug('ok');*/
	}
}
