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
		$money = Yii::$app->request->post('money');
		
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
				return new Response('支付方式不存在', -1);
			}
			//$mMoneyOutPutType->set('out_put_type', $outPutType);
			$mMoneyOutPutType->set('money', $money);
			$mMoneyOutPutType->save();
		}else{
			$mMoneyOutPutType = MoneyOutPutType::findOne([
				'user_id' => $mUser->id,
				'out_put_type' => $outPutType,
			]);
			if($mMoneyOutPutType && !$mMoneyOutPutType->is_delete){
				return new Response('支付方式已存在', 0);
			}
			if($mMoneyOutPutType && $mMoneyOutPutType->is_delete){
				$mMoneyOutPutType->set('is_delete', 0);
				$mMoneyOutPutType->set('money', $money);
				$mMoneyOutPutType->save();
			}
			if(!$mMoneyOutPutType){
				$isSuccess = MoneyOutPutType::addRecord([
					'user_id' => $mUser->id,
					'out_put_type' => $outPutType,
					'money' => $money,
					'create_time' => NOW_TIME,
				]);
				if(!$isSuccess){
					return new Response('保存失败', 0);
				}
			}
		}
		
		return new Response('保存成功', 1);
	}
	
	public function actionDelete(){
		$aId = (array)Yii::$app->request->post('aId');
		
		if(!$aId){
			return new Response('请选择要删除的项', -1);
		}
		foreach($aId as $id){
			$mMoneyOutPutType = MoneyOutPutType::findOne($id);
			if(!$mMoneyOutPutType){
				return new Response('支付方式不存在', -1);
			}
			if($mMoneyOutPutType->user_id != Yii::$app->user->id){
				return new Response('出错了', 0);
			}
			$mMoneyOutPutType->set('is_delete', 1);
			$mMoneyOutPutType->save();
		}
		return new Response('删除成功', 1);
	}
}
