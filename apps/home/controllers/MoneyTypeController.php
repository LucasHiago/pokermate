<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\User;
use common\model\MoneyType;

class MoneyTypeController extends Controller{
	
	public function actionSave(){
		$id = (int)Yii::$app->request->post('id');
		$payType = trim(strip_tags((string)Yii::$app->request->post('payType')));
		$money = (int)Yii::$app->request->post('money');
		
		if(!$id && !$payType){
			return new Response('请填写支付方式', -1);
		}
		if(!is_numeric($money)){
			return new Response('金额必须是数字', -1);
		}
		
		$mUser = Yii::$app->user->getIdentity();
		
		if($id){
			$mMoneyType = MoneyType::findOne($id);
			if(!$mMoneyType){
				return new Response('支付方式不存在', -1);
			}
			$aOldRecord = $mMoneyType->toArray();
			//$mMoneyType->set('pay_type', $payType);
			$mMoneyType->set('money', $money);
			$mMoneyType->save();
			$aNewRecord = $mMoneyType->toArray();
			if($aOldRecord['money'] != $aNewRecord['money']){
				$mUser->operateLog(11, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
			}
		}else{
			$mMoneyType = MoneyType::findOne([
				'user_id' => $mUser->id,
				'pay_type' => $payType,
			]);
			if($mMoneyType && !$mMoneyType->is_delete){
				return new Response('支付方式已存在', 0);
			}
			if($mMoneyType && $mMoneyType->is_delete){
				$aOldRecord = $mMoneyType->toArray();
				$mMoneyType->set('is_delete', 0);
				$mMoneyType->set('money', $money);
				$mMoneyType->save();
				$aNewRecord = $mMoneyType->toArray();
				if($aOldRecord['money'] != $aNewRecord['money']){
					$mUser->operateLog(11, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
				}
			}
			if(!$mMoneyType){
				/*if($money < 0){
					return new Response('金额不能小于0', 0);
				}*/
				$isSuccess = MoneyType::addRecord([
					'user_id' => $mUser->id,
					'pay_type' => $payType,
					'money' => $money,
					'create_time' => NOW_TIME,
				]);
				if(!$isSuccess){
					return new Response('保存失败', 0);
				}
				$mMoneyType = MoneyType::findOne($isSuccess);
				$aMoneyType = $mMoneyType->toArray();
				$mUser->operateLog(10, ['aMoneyType' => $aMoneyType]);
			}
		}
		$aMoneyTypeList = $mUser->getMoneyTypeList();
		return new Response('保存成功', 1, [
			'imbalanceMoney' => $mUser->getImbalanceMoney(),
			'aMoneyTypeList' => $aMoneyTypeList,
		]);
	}
	
	public function actionDelete(){
		$aId = (array)Yii::$app->request->post('aId');
		
		$mUser = Yii::$app->user->getIdentity();
		if(!$aId){
			return new Response('请选择要删除的项', -1);
		}
		foreach($aId as $id){
			$mMoneyType = MoneyType::findOne($id);
			if(!$mMoneyType){
				return new Response('支付方式不存在', -1);
			}
			if($mMoneyType->user_id != Yii::$app->user->id){
				return new Response('出错了', 0);
			}
			$mMoneyType->set('is_delete', 1);
			$mMoneyType->save();
			$aMoneyType = $mMoneyType->toArray();
			$mUser->operateLog(12, ['aMoneyType' => $aMoneyType]);
		}
		$aMoneyTypeList = $mUser->getMoneyTypeList();
		return new Response('删除成功', 1, [
			'imbalanceMoney' => $mUser->getImbalanceMoney(),
			'aMoneyTypeList' => $aMoneyTypeList,
		]);
	}
	
	public function actionAddMoney(){
		$id = (int)Yii::$app->request->post('id');
		$addMoney = (int)Yii::$app->request->post('addMoney');
		
		$mUser = Yii::$app->user->getIdentity();
		$mMoneyType = MoneyType::findOne($id);
		if(!$mMoneyType){
			return new Response('支付方式不存在', -1);
		}
		$aOldRecord = $mMoneyType->toArray();
		$mMoneyType->set('money', ['add', $addMoney]);
		$mMoneyType->save();
		$aNewRecord = $mMoneyType->toArray();
		if($aOldRecord['money'] != $aNewRecord['money']){
			$mUser->operateLog(11, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		$imbalanceMoney = $mUser->getImbalanceMoney();
		if($addMoney > 0){
			return new Response('增加金额成功', 1, ['imbalanceMoney' => $imbalanceMoney]);
		}else{
			return new Response('减少金额成功', 1, ['imbalanceMoney' => $imbalanceMoney]);
		}
	}
	
}
