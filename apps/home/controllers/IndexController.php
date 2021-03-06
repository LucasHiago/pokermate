<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use yii\helpers\ArrayHelper;
use umeworld\lib\Url;
use umeworld\lib\Cookie;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\User;
use common\model\Paiju;
use common\model\KerenBenjin;
use common\model\MoneyType;
use common\model\Agent;
use common\model\Player;
use common\model\ImportData;
use common\model\Lianmeng;

class IndexController extends Controller{
	
	private function _checkRepeatJieshuanRequest($paijuId){
		$mUser = Yii::$app->user->getIdentity();
		
		$aCacheData = $mUser->cache_data;
		$isRepeat = false;
		if(!isset($aCacheData['aRepeatJieshuangRequest'])){
			$aCacheData['aRepeatJieshuangRequest'] = [];
		}else{
			foreach($aCacheData['aRepeatJieshuangRequest'] as $pjid => $rtime){
				if(abs($rtime - NOW_TIME) > 5){
					unset($aCacheData['aRepeatJieshuangRequest'][$pjid]);
				}else{
					if($pjid == $paijuId){
						$isRepeat = true;
					}
				}
			}
		}
		if(!$isRepeat){
			$aCacheData['aRepeatJieshuangRequest'][$paijuId] = NOW_TIME;
		}
		$mUser->set('cache_data', $aCacheData);
		$mUser->save();
		$mUser = null;
		$aCacheData = null;
		if($isRepeat){
			sleep(5);
			return false;
		}
		return true;
	}
	
	public function actionIndex(){
		$paijuId = (int)Yii::$app->request->get('paijuId');
		
		//5秒内重复请求结算页面，则睡5秒
		if($paijuId){
			$this->_checkRepeatJieshuanRequest($paijuId);
			/*if(!$this->_checkRepeatJieshuanRequest($paijuId)){
				return new Response('操作过于频繁', 0);
			}*/
		}
		
		$mUser = Yii::$app->user->getIdentity();
		/********************这里非常重要*********************/
		//先检查客人是否存在，不存在则创建
		//$mUser->checkAddNewPlayer($paijuId);
		/********************这里非常重要*********************/
		$aAgentList = $mUser->getAgentList();
		$aLianmengList = $mUser->getLianmengList();
		$aMoneyTypeList = $mUser->getMoneyTypeList();
		$aMoneyOutPutTypeList = $mUser->getMoneyOutPutTypeList();
		$moneyTypeTotalMoney = $mUser->getMoneyTypeTotalMoney();
		$moneyOutPutTypeTotalMoney = $mUser->getMoneyOutPutTypeTotalMoney();
		//$aUnJiaoBanPaijuTotalStatistic = $mUser->getUnJiaoBanPaijuTotalStatistic();
		//$imbalanceMoney = $mUser->getImbalanceMoney();
		//$jiaoBanZhuanChuMoney = $mUser->getJiaoBanZhuanChuMoney();
		
		$aCurrentPaiju = [];
		$currentPaijuLianmengId = 0;
		$aCondition = [
			'user_id' => $mUser->id,
			'status' => [Paiju::STATUS_UNDO, Paiju::STATUS_DONE],
		];
		$aControl = [
			'page' => 1,
			'page_size' => 6,
			'order_by' => ['status' => SORT_ASC, 'end_time' => SORT_DESC],
			'width_hedui_shuzi' => true,
		];
		//$aLastPaijuList = Paiju::getPaijuList($aCondition, $aControl);
		$aLastPaijuList = $mUser->getLastPaijuList(1, 6, ['status' => [Paiju::STATUS_UNDO, Paiju::STATUS_DONE]], ['`t1`.`status`' => SORT_ASC, '`t1`.`end_time`' => SORT_DESC]);
		/*if(!$paijuId && $aLastPaijuList){
			$aCurrentPaiju = $aLastPaijuList[0];
			$paijuId = $aCurrentPaiju['id'];
			$mPaiju = Paiju::toModel($aCurrentPaiju);
			$currentPaijuLianmengId = $aCurrentPaiju['lianmeng_id'];
		}*/
		
		$aPaijuDataList = [];
		if($paijuId){
			$aPaijuDataList = $mUser->getPaijuDataList($paijuId);
			if($aPaijuDataList === false){
				return new Response('牌局不存在', 0);
			}
			if(!$aCurrentPaiju){
				$mPaiju = Paiju::findOne($paijuId);
				$currentPaijuLianmengId = $mPaiju->lianmeng_id;
				$aCurrentPaiju = $mPaiju->toArray();
			}
		}
		//如果该未结算牌局默认联盟删除了，则重新绑定一个
		if($aCurrentPaiju){
			if($aPaijuDataList){
				$paijuCreater = $aPaijuDataList[0]['paiju_creater'];
				$mLianmeng = Lianmeng::findOne(['user_id' => $mUser->id, 'paiju_creater' => $paijuCreater]);
				if($mLianmeng){
					Cookie::set('last_lianmeng_id_' . $mUser->id, $mLianmeng->id);
				}
			}
			$mPaiju = Paiju::toModel($aCurrentPaiju);
			if(!$aCurrentPaiju['status']){
				$lastLianmengId = Cookie::get('last_lianmeng_id_' . $mUser->id);
				if($lastLianmengId){
					$mLianmeng = Lianmeng::findOne($lastLianmengId);
					if($mLianmeng && !$mLianmeng->is_delete){
						$currentPaijuLianmengId = $lastLianmengId;
					}
				}else{
					$mLianmeng = Lianmeng::findOne($mPaiju->lianmeng_id);
					if(!$mLianmeng || $mLianmeng->is_delete){
						$currentPaijuLianmengId = $mUser->getDefaultLianmengId();
					}
				}
				if($currentPaijuLianmengId != $mPaiju->lianmeng_id){
					$mPaiju->set('lianmeng_id', $currentPaijuLianmengId);
					$mPaiju->save();
				}
			}
		}
		
		return $this->render('home', [
			//'aUnJiaoBanPaijuTotalStatistic' => $aUnJiaoBanPaijuTotalStatistic,
			'aMoneyTypeList' => $aMoneyTypeList,
			'aMoneyOutPutTypeList' => $aMoneyOutPutTypeList,
			'moneyTypeTotalMoney' => $moneyTypeTotalMoney,
			'moneyOutPutTypeTotalMoney' => $moneyOutPutTypeTotalMoney,
			'aLastPaijuList' => $aLastPaijuList,
			'aAgentList' => $aAgentList,
			'aPaijuDataList' => $aPaijuDataList,
			'aLianmengList' => $aLianmengList,
			'aCurrentPaiju' => $aCurrentPaiju,
			'currentPaijuLianmengId' => $currentPaijuLianmengId,
			//'imbalanceMoney' => $imbalanceMoney,
			//'jiaoBanZhuanChuMoney' => $jiaoBanZhuanChuMoney,
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
		//$aOrder = ['`t1`.`status`' => SORT_ASC, '`t1`.`end_time`' => SORT_DESC];
		$aList = [];
		$count = [];
		if($isHistory){
			/*$aCondition = [
				'user_id' => $mUser->id,
				'status' => [Paiju::STATUS_UNDO, Paiju::STATUS_DONE, Paiju::STATUS_FINISH],
			];
			$aControl = [
				'page' => $page,
				'page_size' => $pageSize,
				'order_by' => ['end_time' => SORT_DESC],
				'width_hedui_shuzi' => true,
			];
			$aList = Paiju::getPaijuList($aCondition, $aControl);
			$count = Paiju::getPaijuCount($aCondition);*/
			$aOrder = ['`t1`.`end_time`' => SORT_DESC];
			$aList = $mUser->getLastPaijuList($page, $pageSize, ['status' => [Paiju::STATUS_UNDO, Paiju::STATUS_DONE, Paiju::STATUS_FINISH]], $aOrder);
			$count = $mUser->getLastPaijuListCount(['status' => [Paiju::STATUS_UNDO, Paiju::STATUS_DONE, Paiju::STATUS_FINISH]], $aOrder);
		}else{
			/*$aCondition = [
				'user_id' => $mUser->id,
				'status' => [Paiju::STATUS_UNDO, Paiju::STATUS_DONE],
			];
			$aControl = [
				'page' => $page,
				'page_size' => $pageSize,
				'order_by' => ['status' => SORT_ASC, 'end_time' => SORT_DESC],
				'width_hedui_shuzi' => true,
			];
			$aList = Paiju::getPaijuList($aCondition, $aControl);
			$count = Paiju::getPaijuCount($aCondition);*/
			$aOrder = ['`t1`.`status`' => SORT_ASC, '`t1`.`end_time`' => SORT_DESC];
			$aList = $mUser->getLastPaijuList($page, $pageSize, ['status' => [Paiju::STATUS_UNDO, Paiju::STATUS_DONE]], $aOrder);
			$count = $mUser->getLastPaijuListCount(['status' => [Paiju::STATUS_UNDO, Paiju::STATUS_DONE]], $aOrder);
		}
		
		return new Response('', 1, [
			'list' => $aList,
			'count' => $count,
		]);
	}
	
	public function actionGetKerenBenjin(){
		$kerenBianhao = (int)Yii::$app->request->post('kerenBianhao');
		
		$aKerenBenjin = ['keren_bianhao' => $kerenBianhao, 'benjin' => 0, 'player_list' => []];
		$mKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $kerenBianhao, 'is_delete' => 0]);
		
		if($mKerenBenjin){
			$aKerenBenjin = $mKerenBenjin->toArray();
			$aKerenBenjin['player_list'] = $mKerenBenjin->getPlayerList();
		}
		
		return new Response('', 1, $aKerenBenjin);
	}
	
	public function actionSearchKerenBenjin(){
		$searchValue = Yii::$app->request->post('searchValue');
		
		if(!$searchValue){
			return new Response('', 1, []);
		}
		
		$mUser = Yii::$app->user->getIdentity();
		
		$aList = [];
		if(is_numeric($searchValue)){
			$aList = KerenBenjin::searchKerenByKerenBianhao($mUser->id, $searchValue, 1, 5);
		}else{
			$aList = Player::searchPlayerByPlayerName($mUser->id, $searchValue, 1, 5);;
		}
		
		return new Response('', 1, $aList);
	}
	
	public function actionJiaoshouJiner(){
		$kerenBianhao = (int)Yii::$app->request->post('kerenBianhao');
		$payType = (int)Yii::$app->request->post('payType');
		$jsjer = (int)Yii::$app->request->post('jsjer');
		
		if(!$kerenBianhao){
			return new Response('请输入客人编号', -1);
		}
		$mKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $kerenBianhao, 'is_delete' => 0]);
		if(!$mKerenBenjin){
			return new Response('客人不存在', 0);
		}
		$aOldRecord = $mKerenBenjin->toArray();
		$mMoneyType = MoneyType::findOne(['user_id' => Yii::$app->user->id, 'id' => $payType]);
		if(!$mMoneyType){
			return new Response('收缴方式不存在', 0);
		}
		$aOldMoneyTypeRecord = $mMoneyType->toArray();
		if(!$jsjer){
			return new Response('交收金额不能为0', 0);
		}
		
		$mKerenBenjin->set('benjin', ['add', $jsjer]);
		$mKerenBenjin->save();
		
		$mMoneyType->set('money', ['add', $jsjer]);
		$mMoneyType->save();
		$aNewMoneyTypeRecord = $mMoneyType->toArray();
		$aNewRecord = $mKerenBenjin->toArray();
		$mUser = Yii::$app->user->getIdentity();
		$mUser->operateLog(2, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord, 'aOldMoneyTypeRecord' => $aOldMoneyTypeRecord, 'aNewMoneyTypeRecord' => $aNewMoneyTypeRecord]);
		
		$imbalanceMoney = $mUser->getImbalanceMoney();
		if($jsjer > 0){
			return new Response('存入成功', 1, ['imbalanceMoney' => $imbalanceMoney]);
		}else{
			return new Response('转出成功', 1, ['imbalanceMoney' => $imbalanceMoney]);
		}
		//return new Response('操作成功', 1);
	}
	
	public function actionUpdateBenjin(){
		$kerenBianhao = (int)Yii::$app->request->post('kerenBianhao');
		$benjin = (int)Yii::$app->request->post('benjin');
		
		$mKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $kerenBianhao, 'is_delete' => 0]);
		if(!$mKerenBenjin){
			return new Response('客人不存在', 0);
		}
		$aOldRecord = $mKerenBenjin->toArray();
		$mKerenBenjin->set('benjin', $benjin);
		$mKerenBenjin->save();
		$aNewRecord = $mKerenBenjin->toArray();
		$mUser = Yii::$app->user->getIdentity();
		$mUser->operateLog(1, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		
		return new Response('操作成功', 1, ['imbalanceMoney' => $mUser->getImbalanceMoney()]);
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
		if($kerenBianhao){
			$aCondition['`k1`.`keren_bianhao`'] = $kerenBianhao;
		}
		$aControl = [
			'page' => $page,
			'page_size' => $pageSize,
			'order_by' => '`k1`.id DESC',
			'with_player_list' => true,
		];
		if($kerenBianhaoSort == 1){
			$aControl['order_by'] = '`k1`.`keren_bianhao` DESC';
		}elseif($kerenBianhaoSort == 2){
			$aControl['order_by'] = '`k1`.`keren_bianhao` ASC';
		}
		if($benjinSort == 1){
			$aControl['order_by'] = '`k1`.`benjin` DESC';
		}elseif($benjinSort == 2){
			$aControl['order_by'] = '`k1`.`benjin` ASC';
		}
		$aList = KerenBenjin::getList1($aCondition, $aControl);
		
		return new Response('', 1, $aList);
	}
		
	public function actionUpdateKerenInfo(){
		$id = (int)Yii::$app->request->post('id');
		$type = (string)Yii::$app->request->post('type');
		$value = Yii::$app->request->post('value');
		$isMergeKerenBianhao = Yii::$app->request->post('isMergeKerenBianhao');
		
		$mKerenBenjin = KerenBenjin::findOne(['id' => $id, 'is_delete' => 0]);
		if(!$mKerenBenjin){
			return new Response('客人不存在', -1);
		}
		$aOldRecord = $mKerenBenjin->toArray();
		if($type == 'keren_bianhao'){
			$value = (int)$value;
			if(!KerenBenjin::checkKerenbianhao($value)){
				return new Response('客人编号范围有误', -1);
			}
			$isMerge = (int)Yii::$app->request->post('isMerge');
			if($mKerenBenjin->keren_bianhao != $value){
				$mTempKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $value]);
				if(!$mTempKerenBenjin && $isMergeKerenBianhao){
					return new Response('合并目标编号不存在', -1);
				}
				if($mTempKerenBenjin){	
					if($isMerge){
						$aMergeRecord = $mTempKerenBenjin->toArray();
						if($mTempKerenBenjin->is_delete){
							$mTempKerenBenjin->set('benjin', $mKerenBenjin->benjin);
						}else{
							$mTempKerenBenjin->set('benjin', ['add', $mKerenBenjin->benjin]);
						}
						$mTempKerenBenjin->set('is_delete', 0);
						$mTempKerenBenjin->save();
						
						$aPlayerList = $mKerenBenjin->getPlayerList();
						if($aPlayerList){
							foreach($aPlayerList as $aPlayer){
								$mPlayer = Player::toModel($aPlayer);
								$mPlayer->set('keren_bianhao', $mTempKerenBenjin->keren_bianhao);
								$mPlayer->save();
							}
						}
						$mKerenBenjin->delete();
						$aNewRecord = $mTempKerenBenjin->toArray();
						$mUser = Yii::$app->user->getIdentity();
						$mUser->operateLog(6, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord, 'aMergeRecord' => $aMergeRecord]);
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
					//return new Response('该编号不存在', -1);
					$mKerenBenjin->modifyKerenBianhao($value);
					$aNewRecord = $mKerenBenjin->toArray();
					$mUser = Yii::$app->user->getIdentity();
					$mUser->operateLog(5, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
				}
			}
		}elseif($type == 'benjin'){
			$aOldRecord = $mKerenBenjin->toArray();
			$mKerenBenjin->set('benjin', (int)$value);
			$mKerenBenjin->save();
			$aNewRecord = $mKerenBenjin->toArray();
			$mUser = Yii::$app->user->getIdentity();
			$mUser->operateLog(1, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}elseif($type == 'ying_chou'){
			$aOldRecord = $mKerenBenjin->toArray();
			$mKerenBenjin->set('ying_chou', (float)$value);
			$mKerenBenjin->save();
			$aNewRecord = $mKerenBenjin->toArray();
			$mUser = Yii::$app->user->getIdentity();
			if($aOldRecord['ying_chou'] != $aNewRecord['ying_chou']){
				$mUser->operateLog(3, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
			}
		}elseif($type == 'shu_fan'){
			$aOldRecord = $mKerenBenjin->toArray();
			$mKerenBenjin->set('shu_fan', (float)$value);
			$mKerenBenjin->save();
			$aNewRecord = $mKerenBenjin->toArray();
			$mUser = Yii::$app->user->getIdentity();
			if($aOldRecord['shu_fan'] != $aNewRecord['shu_fan']){
				$mUser->operateLog(4, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
			}
		}elseif($type == 'ying_fee'){
			$aOldRecord = $mKerenBenjin->toArray();
			$mKerenBenjin->set('ying_fee', (int)$value);
			$mKerenBenjin->save();
			$aNewRecord = $mKerenBenjin->toArray();
			$mUser = Yii::$app->user->getIdentity();
			if($aOldRecord['ying_fee'] != $aNewRecord['ying_fee']){
				$mUser->operateLog(41, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
			}
		}elseif($type == 'shu_fee'){
			$aOldRecord = $mKerenBenjin->toArray();
			$mKerenBenjin->set('shu_fee', (int)$value);
			$mKerenBenjin->save();
			$aNewRecord = $mKerenBenjin->toArray();
			$mUser = Yii::$app->user->getIdentity();
			if($aOldRecord['shu_fee'] != $aNewRecord['shu_fee']){
				$mUser->operateLog(42, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
			}
		}elseif($type == 'remark'){
			$mKerenBenjin->set('remark', $value);
			$mKerenBenjin->save();
		}else{
			return new Response('出错了', 0);
		}
		
		
		return new Response('更新成功', 1, $mKerenBenjin->$type);
	}
		
	public function actionUpdateKerenPlayerId(){
		$id = (int)Yii::$app->request->post('id');
		$playerId = (int)Yii::$app->request->post('playerId');
		
		$mKerenBenjin = KerenBenjin::findOne($id);
		if(!$mKerenBenjin){
			return new Response('客人不存在', -1);
		}
		$mPlayer = Player::findOne($playerId);
		if(!$mPlayer){
			return new Response('玩家不存在', -1);
		}
		$mKerenBenjin->set('current_player_id', $playerId);
		$mKerenBenjin->save();
		
		return new Response('更新成功', 1);
	}
		
	public function actionUpdateKerenAgentId(){
		$id = (int)Yii::$app->request->post('id');
		$value = Yii::$app->request->post('value');
		
		$mKerenBenjin = KerenBenjin::findOne($id);
		if(!$mKerenBenjin){
			return new Response('客人不存在', -1);
		}
		/*$mAgent = Agent::findOne((int)$value);
		if(!$mAgent){
			return new Response('代理不存在', -1);
		}*/
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
		if(!$mKerenBenjin->checkIsCanDelete()){
			return new Response('客人尚有牌局数据未处理，不能删除', -1);
		}
		$mKerenBenjin->delete();
		$mUser = Yii::$app->user->getIdentity();
		$aKerenBenjin = $mKerenBenjin->toArray();
		$mUser->operateLog(8, ['aKerenBenjin' => $aKerenBenjin]);
		
		return new Response('删除成功', 1);
	}
		
	public function actionAddKeren(){
		$kerenBianhao = (int)Yii::$app->request->post('kerenBianhao');
		$benjin = (int)Yii::$app->request->post('benjin');
		$playerName = (string)Yii::$app->request->post('playerName');
		$yingChou = (float)Yii::$app->request->post('yingChou');
		$shuFan = (float)Yii::$app->request->post('shuFan');
		$yingFee = (int)Yii::$app->request->post('yingFee');
		$shuFee = (int)Yii::$app->request->post('shuFee');
		$agentId = (int)Yii::$app->request->post('agentId');
		$playerId = (int)Yii::$app->request->post('playerId');
		$remark = (string)Yii::$app->request->post('remark');
		$isAddPlayer = (int)Yii::$app->request->post('isAddPlayer');
		
		if(!$kerenBianhao){
			return new Response('请输入客人编号', -1);
		}
		if(!KerenBenjin::checkKerenbianhao($kerenBianhao)){
			return new Response('客人编号范围有误', -1);
		}
		
		if(!$playerId){
			//return new Response('请输入玩家ID', -1);
		}
		$mPlayer = Player::findOne(['user_id' => Yii::$app->user->id, 'player_id' => $playerId, 'is_delete' => 0]);
		/*if($mPlayer){
			return new Response('玩家ID已存在', -1);
		}*/
		if($mPlayer && $playerId){
			return new Response('玩家ID已存在', -1);
		}
		if($agentId){
			$mAgent = Agent::findOne($agentId);
			if(!$mAgent){
				return new Response('代理不存在', -1);
			}
		}
		$mKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $kerenBianhao]);
		if(!$mKerenBenjin && $isAddPlayer){
			return new Response('插入的编号不存在', -1);
		}
		if(!$mKerenBenjin){
			//return new Response('出错啦', 0);
			$kerenId = KerenBenjin::addRecord([
				'user_id' => Yii::$app->user->id, 
				'keren_bianhao' => $kerenBianhao ? $kerenBianhao : KerenBenjin::getNextKerenbianhao(Yii::$app->user->id), 
				'benjin' => $benjin, 
				'ying_chou' => $yingChou, 
				'shu_fan' => $shuFan, 
				'ying_fee' => $yingFee, 
				'shu_fee' => $shuFee, 
				'agent_id' => $agentId, 
				'remark' => $remark, 
				'create_time' => NOW_TIME
			]);
			$mKerenBenjin = KerenBenjin::findOne($kerenId);
		}
		if($mKerenBenjin->is_delete){
			$mKerenBenjin->set('is_delete', 0);
			$mKerenBenjin->save();
		}
		$mPlayer = Player::findOne(['user_id' => Yii::$app->user->id, 'player_id' => $playerId, 'is_delete' => 1]);
		$isSuccess = 0;
		if($playerId){
			if($mPlayer){
				$isSuccess = $mPlayer->id;
				$mPlayer->set('keren_bianhao', $kerenBianhao);
				$mPlayer->set('is_delete', 0);
				$mPlayer->save();
			}else{
				$isSuccess = Player::addRecord([
					'user_id' => Yii::$app->user->id,
					'keren_bianhao' => $kerenBianhao,
					'player_id' => $playerId,
					'player_name' => $playerName,
					'create_time' => NOW_TIME,
				]);
			}
		}else{
			$isSuccess = Player::addRecord([
				'user_id' => Yii::$app->user->id,
				'keren_bianhao' => $kerenBianhao,
				'player_id' => $playerId,
				'player_name' => $playerName,
				'create_time' => NOW_TIME,
			]);
		}
		ImportData::addEmptyDataRecord(Yii::$app->user->id, $playerId, $playerName);
		
		$aOldRecord = $mKerenBenjin->toArray();
		if(!$isAddPlayer){
			$mKerenBenjin->set('benjin', $benjin);
			$mKerenBenjin->set('ying_chou', $yingChou);
			$mKerenBenjin->set('shu_fan', $shuFan);
			$mKerenBenjin->set('ying_fee', $yingFee);
			$mKerenBenjin->set('shu_fee', $shuFee);
			$mKerenBenjin->set('remark', $remark);
			if($agentId){
				$mKerenBenjin->set('agent_id', $agentId);
			}
		}
		$mKerenBenjin->save();
		$aNewRecord = $mKerenBenjin->toArray();
		$mUser = Yii::$app->user->getIdentity();
		if($aOldRecord['benjin'] != $aNewRecord['benjin']){
			$mUser->operateLog(1, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		if($aOldRecord['ying_chou'] != $aNewRecord['ying_chou']){
			$mUser->operateLog(3, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		if($aOldRecord['shu_fan'] != $aNewRecord['shu_fan']){
			$mUser->operateLog(4, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		if($aOldRecord['ying_fee'] != $aNewRecord['ying_fee']){
			$mUser->operateLog(41, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		if($aOldRecord['shu_fee'] != $aNewRecord['shu_fee']){
			$mUser->operateLog(42, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		$mPlayer = Player::findOne($isSuccess);
		$aPlayer = $mPlayer->toArray();
		$mUser->operateLog(7, ['aPlayer' => $aPlayer, 'aKerenBenjin' => $aNewRecord]);
		
		return new Response('添加成功', 1);
	}
	
}
