<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\User;
use common\model\Paiju;
use common\model\KerenBenjin;
use common\model\MoneyType;
use common\model\Agent;

class IndexController extends Controller{
	
	public function actionIndex(){
		$mUser = Yii::$app->user->getIdentity();
		$aMoneyTypeList = $mUser->getMoneyTypeList();
		$aMoneyOutPutTypeList = $mUser->getMoneyOutPutTypeList();
		$moneyTypeTotalMoney = $mUser->getMoneyTypeTotalMoney();
		$moneyOutPutTypeTotalMoney = $mUser->getMoneyOutPutTypeTotalMoney();
		$aLastPaijuList = $mUser->getLastPaijuList(1, 6);
		$aAgentList = $mUser->getAgentList();
		
		return $this->render('home', [
			'aMoneyTypeList' => $aMoneyTypeList,
			'aMoneyOutPutTypeList' => $aMoneyOutPutTypeList,
			'moneyTypeTotalMoney' => $moneyTypeTotalMoney,
			'moneyOutPutTypeTotalMoney' => $moneyOutPutTypeTotalMoney,
			'aLastPaijuList' => $aLastPaijuList,
			'aAgentList' => $aAgentList,
		]);
	}
	
	public function actionGetPaijuList(){
		$mUser = Yii::$app->user->getIdentity();
		
		$aList = $mUser->getLastPaijuList(1, 6);
		
		return new Response('', 1, $aList);
	}
	
	public function actionGetKerenBenjin(){
		$kerenBianhao = (int)Yii::$app->request->post('kerenBianhao');
		
		$aKerenBenjin = ['keren_bianhao' => $kerenBianhao, 'benjin' => 0];
		$mKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $kerenBianhao]);
		
		if($mKerenBenjin){
			$aKerenBenjin = $mKerenBenjin->toArray();
		}
		
		return new Response('', 1, $aKerenBenjin);
	}
	
	public function actionJiaoshouJiner(){
		$kerenBianhao = (int)Yii::$app->request->post('kerenBianhao');
		$payType = (int)Yii::$app->request->post('payType');
		$jsjer = Yii::$app->request->post('jsjer');
		
		$mKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $kerenBianhao]);
		if(!$mKerenBenjin){
			return new Response('客人不存在', 0);
		}
		$mMoneyType = MoneyType::findOne(['user_id' => Yii::$app->user->id, 'id' => $payType]);
		if(!$mMoneyType){
			return new Response('收缴方式不存在', 0);
		}
		if(!$jsjer || intval($jsjer) != $jsjer){
			return new Response('交收金额必须是大于0的整数', 0);
		}
		
		$mKerenBenjin->set('benjin', ['add', $jsjer]);
		$mKerenBenjin->save();
		
		$mMoneyType->set('money', ['add', $jsjer]);
		$mMoneyType->save();
		
		return new Response('操作成功', 1);
	}
	
	public function actionUpdateBenjin(){
		$kerenBianhao = (int)Yii::$app->request->post('kerenBianhao');
		$benjin = (int)Yii::$app->request->post('benjin');
		
		$mKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $kerenBianhao]);
		if(!$mKerenBenjin){
			return new Response('客人不存在', 0);
		}
		
		$mKerenBenjin->set('benjin', $benjin);
		$mKerenBenjin->save();
		
		return new Response('操作成功', 1);
	}
	
	public function actionGetKerenList(){
		$page = (int)Yii::$app->request->post('page');
		$pageSize = (int)Yii::$app->request->post('pageSize');
		$kerenBianhao = (int)Yii::$app->request->post('kerenBianhao');
		
		if(!$page || $page <= 0){
			$page = 1;
		}
		if(!$pageSize || $pageSize <= 0){
			$pageSize = 10;
		}
		$aCondition = [
			'user_id' => Yii::$app->user->id,
			'is_delete' => 0,
		];
		if($kerenBianhao){
			$aCondition['keren_bianhao'] = $kerenBianhao;
		}
		$aControl = [
			'page' => $page,
			'page_size' => $pageSize,
			'order_by' => ['id' => SORT_DESC],
			'with_player_list' => true,
		];
		$aList = KerenBenjin::getList($aCondition, $aControl);
		
		return new Response('', 1, $aList);
	}
		
	public function actionUpdateKerenInfo(){
		$id = (int)Yii::$app->request->post('id');
		$type = (string)Yii::$app->request->post('type');
		$value = Yii::$app->request->post('value');
		
		$mKerenBenjin = KerenBenjin::findOne($id);
		if(!$mKerenBenjin){
			return new Response('客人不存在', -1);
		}
		if($type == 'keren_bianhao'){
			
		}elseif($type == 'benjin'){
			$mKerenBenjin->set('benjin', (int)$value);
		}elseif($type == 'ying_chou'){
			$mKerenBenjin->set('ying_chou', (float)$value);
		}elseif($type == 'shu_fan'){
			$mKerenBenjin->set('shu_fan', (float)$value);
		}elseif($type == 'remark'){
			$mKerenBenjin->set('remark', $value);
		}else{
			return new Response('出错了', 0);
		}
		$mKerenBenjin->save();
		
		return new Response('更新成功', 1, $mKerenBenjin->$type);
	}
		
	public function actionUpdateKerenAgentId(){
		$id = (int)Yii::$app->request->post('id');
		$value = Yii::$app->request->post('value');
		
		$mKerenBenjin = KerenBenjin::findOne($id);
		if(!$mKerenBenjin){
			return new Response('客人不存在', -1);
		}
		$mAgent = Agent::findOne((int)$value);
		if(!$mAgent){
			return new Response('代理不存在', -1);
		}
		$mKerenBenjin->set('agent_id', (int)$value);
		$mKerenBenjin->save();
		
		return new Response('更新成功', 1);
	}
		
	public function actionDeleteKeren(){
		$id = (int)Yii::$app->request->post('id');
		
		$mKerenBenjin = KerenBenjin::findOne($id);
		if(!$mKerenBenjin){
			return new Response('客人不存在', -1);
		}
		if($mKerenBenjin->user_id != Yii::$app->user->id){
			return new Response('出错了', 0);
		}
		$mKerenBenjin->set('is_delete', 1);
		$mKerenBenjin->save();
		
		return new Response('删除成功', 1);
	}
	
}
