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
use common\model\Player;

class IndexController extends Controller{
	
	public function actionIndex(){
		$paijuId = (int)Yii::$app->request->get('paijuId');
		
		$mUser = Yii::$app->user->getIdentity();
		$aAgentList = $mUser->getAgentList();
		$aMoneyTypeList = $mUser->getMoneyTypeList();
		$aMoneyOutPutTypeList = $mUser->getMoneyOutPutTypeList();
		$moneyTypeTotalMoney = $mUser->getMoneyTypeTotalMoney();
		$moneyOutPutTypeTotalMoney = $mUser->getMoneyOutPutTypeTotalMoney();
		$aLastPaijuList = $mUser->getLastPaijuList(1, 6);
		$aCurrentPaiju = [];
		if($aLastPaijuList){
			$aCurrentPaiju = current($aLastPaijuList);
			$paijuId = $aCurrentPaiju['id'];
		}
		
		$aPaijuDataList = [];
		if($paijuId){
			$aPaijuDataList = $mUser->getPaijuDataList($paijuId, true);
			if(!$aPaijuDataList){
				return new Response('牌局不存在', 0);
			}
		}
		
		return $this->render('home', [
			'aMoneyTypeList' => $aMoneyTypeList,
			'aMoneyOutPutTypeList' => $aMoneyOutPutTypeList,
			'moneyTypeTotalMoney' => $moneyTypeTotalMoney,
			'moneyOutPutTypeTotalMoney' => $moneyOutPutTypeTotalMoney,
			'aLastPaijuList' => $aLastPaijuList,
			'aAgentList' => $aAgentList,
			'aPaijuDataList' => $aPaijuDataList,
		]);
	}
	
	public function actionGetPaijuList(){
		$page = (int)Yii::$app->request->post('page');
		$pageSize = (int)Yii::$app->request->post('pageSize');
		$isHistory = (int)Yii::$app->request->post('isHistory');
		
		if(!$page || $page <= 0){
			$page = 1;
		}
		if(!$pageSize || $pageSize <= 0){
			$pageSize = 20;
		}
		
		$mUser = Yii::$app->user->getIdentity();
		if($isHistory){
			$aList = $mUser->getLastPaijuList($page, $pageSize, ['status' => [Paiju::STATUS_UNDO, Paiju::STATUS_DONE, Paiju::STATUS_FINISH]]);
		}else{
			$aList = $mUser->getLastPaijuList($page, $pageSize);
		}
		
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
		$kerenBianhaoSort = (int)Yii::$app->request->post('kerenBianhaoSort');
		$benjinSort = (int)Yii::$app->request->post('benjinSort');
		
		if(!$page || $page <= 0){
			$page = 1;
		}
		if(!$pageSize || $pageSize <= 0){
			$pageSize = 20;
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
		if($kerenBianhaoSort == 1){
			$aControl['order_by'] = ['keren_bianhao' => SORT_DESC];
		}elseif($kerenBianhaoSort == 2){
			$aControl['order_by'] = ['keren_bianhao' => SORT_ASC];
		}
		if($benjinSort == 1){
			$aControl['order_by'] = ['benjin' => SORT_DESC];
		}elseif($benjinSort == 2){
			$aControl['order_by'] = ['benjin' => SORT_ASC];
		}
		$aList = KerenBenjin::getList($aCondition, $aControl);
		
		return new Response('', 1, $aList);
	}
		
	public function actionUpdateKerenInfo(){
		$id = (int)Yii::$app->request->post('id');
		$type = (string)Yii::$app->request->post('type');
		$value = Yii::$app->request->post('value');
		
		$mKerenBenjin = KerenBenjin::findOne(['id' => $id, 'is_delete' => 0]);
		if(!$mKerenBenjin){
			return new Response('客人不存在', -1);
		}
		if($type == 'keren_bianhao'){
			$value = (int)$value;
			$isMerge = (int)Yii::$app->request->post('isMerge');
			if($mKerenBenjin->keren_bianhao != $value){
				$mTempKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $value, 'is_delete' => 0]);
				if($mTempKerenBenjin){	
					if($isMerge){
						$mTempKerenBenjin->set('benjin', ['add', $mKerenBenjin->benjin]);
						$mTempKerenBenjin->save();
						
						$mKerenBenjin->set('is_delete', 1);
						$mKerenBenjin->save();
						return new Response('合并成功', 1, 'reload');
					}else{
						return new Response('改编号已有客人使用，是否合并共用？', 2);
					}
				}else{
					/*KerenBenjin::addRecord([
						'user_id' => Yii::$app->user->id, 
						'keren_bianhao' => $value, 
						'benjin' => $mKerenBenjin->benjin, 
						'shu_fan' => $mKerenBenjin->shu_fan, 
						'agent_id' => $mKerenBenjin->agent_id, 
						'remark' => $mKerenBenjin->remark, 
						'create_time' => NOW_TIME
					]);*/
					return new Response('该编号不存在', -1);
				}
			}
		}elseif($type == 'benjin'){
			$mKerenBenjin->set('benjin', (int)$value);
			$mKerenBenjin->save();
		}elseif($type == 'ying_chou'){
			$mKerenBenjin->set('ying_chou', (float)$value);
			$mKerenBenjin->save();
		}elseif($type == 'shu_fan'){
			$mKerenBenjin->set('shu_fan', (float)$value);
			$mKerenBenjin->save();
		}elseif($type == 'remark'){
			$mKerenBenjin->set('remark', $value);
			$mKerenBenjin->save();
		}else{
			return new Response('出错了', 0);
		}
		
		
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
		
	public function actionAddKeren(){
		$kerenBianhao = (int)Yii::$app->request->post('kerenBianhao');
		$benjin = (int)Yii::$app->request->post('benjin');
		$playerName = (string)Yii::$app->request->post('playerName');
		$yingChou = (float)Yii::$app->request->post('yingChou');
		$shuFan = (float)Yii::$app->request->post('shuFan');
		$agentId = (int)Yii::$app->request->post('agentId');
		$playerId = (int)Yii::$app->request->post('playerId');
		
		if(!$kerenBianhao){
			return new Response('请输入客人编号', -1);
		}
		if(!$playerId){
			return new Response('请输入玩家ID', -1);
		}
		$mPlayer = Player::findOne(['user_id' => Yii::$app->user->id, 'player_id' => $playerId]);
		if($mPlayer){
			return new Response('玩家ID已存在', -1);
		}
		if($agentId){
			$mAgent = Agent::findOne($agentId);
			if(!$mAgent){
				return new Response('代理不存在', -1);
			}
		}
		$isSuccess = Player::addRecord([
			'user_id' => Yii::$app->user->id,
			'keren_bianhao' => $kerenBianhao,
			'player_id' => $playerId,
			'player_name' => $playerName,
			'create_time' => NOW_TIME,
		]);
		$mKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $kerenBianhao]);
		if(!$mKerenBenjin){
			return new Response('出错啦', 0);
		}
		if(!$mKerenBenjin->benjin){
			$mKerenBenjin->set('benjin', $benjin);
		}
		$mKerenBenjin->set('ying_chou', $yingChou);
		$mKerenBenjin->set('shu_fan', $shuFan);
		if($agentId){
			$mKerenBenjin->set('agent_id', $agentId);
		}
		$mKerenBenjin->save();
		
		return new Response('添加成功', 1);
	}
	
}
