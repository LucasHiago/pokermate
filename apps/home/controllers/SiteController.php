<?php
namespace home\controllers;

use Yii;
use umeworld\lib\Controller;
//use home\lib\Controller;
use umeworld\lib\Response;
use yii\helpers\ArrayHelper;

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
		$this->_repaireMissingKeren();
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
	
	private function _repaireMissingKeren(){
		set_time_limit(0);
		$mUser = Yii::$app->user->getIdentity();
		$aClubList = $mUser->getUserClubList();
		$aClubId = [];
		if($aClubList){
			$aClubId = ArrayHelper::getColumn($aClubList, 'club_id');
		}
		array_push($aClubId, 0);
		$aCondition = [
			'`k1`.`user_id`' => Yii::$app->user->id,
			'`k1`.`is_delete`' => 0,
			'club_id' => $aClubId,
		];
		$aControl = [
			'page' => 1,
			'page_size' => 9999999,
			'order_by' => '`k1`.id DESC',
		];
		$aList = \common\model\KerenBenjin::getList1($aCondition, $aControl);
		$aKerenbianhao = ArrayHelper::getColumn($aList, 'keren_bianhao');
		
		for($i = 1; $i <= 786; $i++){
			if(!in_array($i, $aKerenbianhao)){
				$mKerenBenjin = \common\model\KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $i]);
				if($mKerenBenjin){
					$aPlayerList = $mKerenBenjin->getPlayerList();
					if($aPlayerList){
						foreach($aPlayerList as $aPlayer){
							$mImportData = \common\model\ImportData::findOne(['user_id' => Yii::$app->user->id, 'player_id' => $aPlayer['player_id']]);
							if(!$mImportData){
								\common\model\ImportData::addEmptyDataRecord(Yii::$app->user->id, $aPlayer['player_id'], $aPlayer['player_name']);
							}
						}
					}
				}
			}
		}
	}
	
}
