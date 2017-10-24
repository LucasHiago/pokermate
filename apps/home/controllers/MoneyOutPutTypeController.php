<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\User;
use common\model\MoneyOutPutType;

class MoneyOutPutTypeController extends Controller{
	
	public function actionSave(){
		$id = (int)Yii::$app->request->post('id');
		$outPutType = trim(strip_tags((string)Yii::$app->request->post('outPutType')));
		$money = (int)Yii::$app->request->post('money');
		
		if(!$id && !$outPutType){
			return new Response('请填写支出方式', -1);
		}
		if(!is_numeric($money)){
			return new Response('金额必须是数字', -1);
		}
		
		$mUser = Yii::$app->user->getIdentity();
		
		if($id){
			$mMoneyOutPutType = MoneyOutPutType::findOne($id);
			if(!$mMoneyOutPutType){
				return new Response('支出方式不存在', -1);
			}
			$aOldRecord = $mMoneyOutPutType->toArray();
			//$mMoneyOutPutType->set('out_put_type', $outPutType);
			$mMoneyOutPutType->set('money', $money);
			$mMoneyOutPutType->save();
			$aNewRecord = $mMoneyOutPutType->toArray();
			if($aOldRecord['money'] != $aNewRecord['money']){
				$mUser->operateLog(14, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
			}
		}else{
			$mMoneyOutPutType = MoneyOutPutType::findOne([
				'user_id' => $mUser->id,
				'out_put_type' => $outPutType,
			]);
			if($mMoneyOutPutType && !$mMoneyOutPutType->is_delete){
				return new Response('支出方式已存在', 0);
			}
			if($mMoneyOutPutType && $mMoneyOutPutType->is_delete){
				$aOldRecord = $mMoneyOutPutType->toArray();
				$mMoneyOutPutType->set('is_delete', 0);
				$mMoneyOutPutType->set('money', $money);
				$mMoneyOutPutType->save();
				$aNewRecord = $mMoneyOutPutType->toArray();
				if($aOldRecord['money'] != $aNewRecord['money']){
					$mUser->operateLog(14, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
				}
			}
			if(!$mMoneyOutPutType){
				/*if($money < 0){
					return new Response('金额不能小于0', 0);
				}*/
				$isSuccess = MoneyOutPutType::addRecord([
					'user_id' => $mUser->id,
					'out_put_type' => $outPutType,
					'money' => $money,
					'create_time' => NOW_TIME,
				]);
				if(!$isSuccess){
					return new Response('保存失败', 0);
				}
				$mMoneyOutPutType = MoneyOutPutType::findOne($isSuccess);
				$aMoneyOutPutType = $mMoneyOutPutType->toArray();
				$mUser->operateLog(13, ['aMoneyOutPutType' => $aMoneyOutPutType]);
			}
		}
		
		return new Response('保存成功', 1, ['imbalanceMoney' => $mUser->getImbalanceMoney()]);
	}
	
	public function actionDelete(){
		$aId = (array)Yii::$app->request->post('aId');
		
		$mUser = Yii::$app->user->getIdentity();
		if(!$aId){
			return new Response('请选择要删除的项', -1);
		}
		foreach($aId as $id){
			$mMoneyOutPutType = MoneyOutPutType::findOne($id);
			if(!$mMoneyOutPutType){
				return new Response('支出方式不存在', -1);
			}
			if($mMoneyOutPutType->user_id != Yii::$app->user->id){
				return new Response('出错了', 0);
			}
			$mMoneyOutPutType->set('is_delete', 1);
			$mMoneyOutPutType->save();
			$aMoneyOutPutType = $mMoneyOutPutType->toArray();
			$mUser->operateLog(15, ['aMoneyOutPutType' => $aMoneyOutPutType]);
		}
		return new Response('删除成功', 1, ['imbalanceMoney' => $mUser->getImbalanceMoney()]);
	}
	
	public function actionAddMoney(){
		$id = (int)Yii::$app->request->post('id');
		$addMoney = (int)Yii::$app->request->post('addMoney');
		
		$mUser = Yii::$app->user->getIdentity();
		$mMoneyOutPutType = MoneyOutPutType::findOne($id);
		if(!$mMoneyOutPutType){
			return new Response('支出方式不存在', -1);
		}
		$aOldRecord = $mMoneyOutPutType->toArray();
		$mMoneyOutPutType->set('money', ['add', $addMoney]);
		$mMoneyOutPutType->save();
		$aNewRecord = $mMoneyOutPutType->toArray();
		if($aOldRecord['money'] != $aNewRecord['money']){
			$mUser->operateLog(14, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		$imbalanceMoney = $mUser->getImbalanceMoney();
		if($addMoney > 0){
			return new Response('增加金额成功', 1, ['imbalanceMoney' => $imbalanceMoney]);
		}else{
			return new Response('减少金额成功', 1, ['imbalanceMoney' => $imbalanceMoney]);
		}
	}
	
}
