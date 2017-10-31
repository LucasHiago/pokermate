<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\form\KerenBenjinListForm;
use common\model\form\PlayerListForm;
use common\model\KerenBenjin;
use common\model\Player;
use common\model\Agent;
use common\model\ImportData;

class KerenBenjinManageController extends Controller{
	public $layout = 'manage';
	
	public function actionIndex(){
		$oListForm = new KerenBenjinListForm();
		$aParams = Yii::$app->request->get();
		if($aParams && (!$oListForm->load($aParams, '') || !$oListForm->validate())){
			return new Response(current($oListForm->getErrors())[0]);
		}
		$aList = $oListForm->getList();
		$oPage = $oListForm->getPageObject();
		
		return $this->render('index', [
			'aList' => $aList,
			'oPage' => $oPage,
		]);
	}
		
	public function actionPlayerList(){
		//$this->_fixedMarkPlayer();
		$oListForm = new PlayerListForm();
		$aParams = Yii::$app->request->get();
		if($aParams && (!$oListForm->load($aParams, '') || !$oListForm->validate())){
			return new Response(current($oListForm->getErrors())[0]);
		}
		$aList = $oListForm->getList();
		$mUser = Yii::$app->user->getIdentity();
		$aAgentList = $mUser->getAgentList();
		
		return $this->render('player_list', [
			'aList' => $aList,
			'aAgentList' => $aAgentList,
			'playerId' => $oListForm->playerId,
			'playerName' => $oListForm->playerName,
		]);
	}
		
	public function actionGetPlayerList(){
		$playerId = (int)Yii::$app->request->post('playerId');
		$playerName = (string)Yii::$app->request->post('playerName');
		
		$mUser = Yii::$app->user->getIdentity();
		$aList = $mUser->getAllPlayerInfoList($playerId, $playerName);
		$aAgentList = $mUser->getAgentList();
		return new Response('', 1, [
			'aAgentList' => $aAgentList,
			'list' => $aList,
		]);
		
	}
	
	private function _fixedMarkPlayer(){
		set_time_limit(0);
		$aList = Player::findAll(['user_id' => Yii::$app->user->id], ['player_id', 'player_name']);
		foreach($aList as $value){
			$mImportData = \common\model\ImportData::findOne(['user_id' => Yii::$app->user->id, 'player_id' => $value['player_id']]);
			if(!$mImportData){
				\common\model\ImportData::addEmptyDataRecord(Yii::$app->user->id, $value['player_id'], $value['player_name']);
			}
		}
	}
	
	public function actionShowEdit(){
		$id = Yii::$app->request->get('id');
		
		$mKerenBenjin = KerenBenjin::findOne($id);
		$aKerenBenjin = [];
		if($mKerenBenjin){
			$aKerenBenjin = $mKerenBenjin->toArray();
		}
		$mUser = Yii::$app->user->getIdentity();
		$aAgentList = $mUser->getAgentList();
		return $this->render('edit', [
			'aKerenBenjin' => $aKerenBenjin,
			'aAgentList' => $aAgentList,
			'aPlayerList' => $mKerenBenjin ? $mKerenBenjin->getPlayerList() : [],
		]);
	}
	
	public function actionEdit(){
		$id = (int)Yii::$app->request->post('id');
		$kerenBianhao = (int)Yii::$app->request->post('kerenBianhao');
		$benjin = (int)Yii::$app->request->post('benjin');
		$yingChou = (int)Yii::$app->request->post('yingChou');
		$shuFan = (int)Yii::$app->request->post('shuFan');
		$agentId = (int)Yii::$app->request->post('agentId');
		$yingFee = (int)Yii::$app->request->post('yingFee');
		$shuFee = (int)Yii::$app->request->post('shuFee');
		$remark = (string)Yii::$app->request->post('remark');
		
		if(!$id){
			$oController = new IndexController($this->id, Yii::$app);
			return $oController->actionAddKeren();
		}
		$mKerenBenjin = KerenBenjin::findOne(['id' => $id, 'is_delete' => 0]);
		if(!$mKerenBenjin){
			return new Response('会员不存在', -1);
		}
		$aOldRecord = $mKerenBenjin->toArray();
		if(!KerenBenjin::checkKerenbianhao($kerenBianhao)){
			return new Response('客人编号范围有误', -1);
		}
		if($kerenBianhao){
			$isMerge = (int)Yii::$app->request->post('isMerge');
			if($mKerenBenjin->keren_bianhao != $kerenBianhao){
				$mTempKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $kerenBianhao]);
				if($mTempKerenBenjin){	
					if($isMerge){
						$mTempKerenBenjin->set('benjin', $mKerenBenjin->benjin);
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
						return new Response('合并成功', 1, 'reload');
					}else{
						return new Response('改编号已有客人使用，是否合并共用？', 2);
					}
				}else{
					//return new Response('该编号不存在', -1);
					$mKerenBenjin->modifyKerenBianhao($value);
				}
			}
		}
		$mAgent = Agent::findOne($agentId);
		if(!$mAgent){
			return new Response('代理不存在', -1);
		}
		$mKerenBenjin->set('benjin', $benjin);
		$mKerenBenjin->set('ying_chou', $yingChou);
		$mKerenBenjin->set('shu_fan', $shuFan);
		$mKerenBenjin->set('ying_fee', $yingFee);
		$mKerenBenjin->set('shu_fee', $shuFee);
		$mKerenBenjin->set('agent_id', $agentId);
		$mKerenBenjin->set('remark', $remark);
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
		
		return new Response('保存成功', 1);
	}
	
	public function actionEditPlayer(){
		$id = (int)Yii::$app->request->post('id');
		$kerenBianhao = (int)Yii::$app->request->post('kerenBianhao');
		$benjin = (int)Yii::$app->request->post('benjin');
		$yingChou = (int)Yii::$app->request->post('yingChou');
		$shuFan = (int)Yii::$app->request->post('shuFan');
		$yingFee = (int)Yii::$app->request->post('yingFee');
		$shuFee = (int)Yii::$app->request->post('shuFee');
		$agentId = (int)Yii::$app->request->post('agentId');
		$remark = (string)Yii::$app->request->post('remark');
		$playerName = (string)Yii::$app->request->post('playerName');
		$playerId = (int)Yii::$app->request->post('playerId');
		
		if(!$id){
			return new Response('玩家不存在', -1);
		}
		$mPlayer = Player::findOne($id);
		if(!$mPlayer){
			return new Response('玩家不存在', -1);
		}
		
		if($kerenBianhao){
			if(!KerenBenjin::checkKerenbianhao($kerenBianhao)){
				return new Response('客人编号范围有误', -1);
			}
			$mTempKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $kerenBianhao]);
			if(!$mTempKerenBenjin){
				return new Response('客人不存在', -1);
			}
			$mKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $mPlayer->keren_bianhao]);
			if(!$mKerenBenjin){
				return new Response('客人不存在', -1);
			}
			$aOldRecord = $mKerenBenjin->toArray();
			if($agentId){
				$mAgent = Agent::findOne($agentId);
				if(!$mAgent){
					return new Response('代理不存在', -1);
				}
			}
			$isMerge = (int)Yii::$app->request->post('isMerge');
			if($mTempKerenBenjin->keren_bianhao != $mKerenBenjin->keren_bianhao){
				//$mTempKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $kerenBianhao]);
				if($mTempKerenBenjin){	
					if($isMerge){
						$aPlayerList = $mKerenBenjin->getPlayerList();
						$aMergeRecord = $mTempKerenBenjin->toArray();
						if($mTempKerenBenjin->is_delete){
							$mTempKerenBenjin->set('is_delete', 0);
							$mTempKerenBenjin->set('benjin', $benjin);
						}else{
							if(count($aPlayerList) == 1){
								$mTempKerenBenjin->set('benjin', ['add', $benjin]);
							}
						}
						$mTempKerenBenjin->set('ying_chou', $yingChou);
						$mTempKerenBenjin->set('shu_fan', $shuFan);
						$mTempKerenBenjin->set('ying_fee', $yingFee);
						$mTempKerenBenjin->set('shu_fee', $shuFee);
						if($agentId){
							$mTempKerenBenjin->set('agent_id', $agentId);
						}
						$mTempKerenBenjin->set('remark', $remark);
						$mTempKerenBenjin->save();
						
						if($aPlayerList){
							foreach($aPlayerList as $aPlayer){
								$mPlayer = Player::toModel($aPlayer);
								if($id == $mPlayer->id){
									$mPlayer->set('keren_bianhao', $mTempKerenBenjin->keren_bianhao);
									$mPlayer->save();
								}
							}
						}
						if(count($aPlayerList) == 1){
							$mKerenBenjin->delete();
						}
						$aNewRecord = $mTempKerenBenjin->toArray();
						$mUser = Yii::$app->user->getIdentity();
						$mUser->operateLog(6, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord, 'aMergeRecord' => $aMergeRecord]);
						return new Response('合并成功', 1, 'reload');
					}else{
						return new Response('改编号已有客人使用，是否合并共用？', 2);
					}
				}else{
					//return new Response('该编号不存在', -1);
					$mKerenBenjin->modifyKerenBianhao($value);
					$aNewRecord = $mKerenBenjin->toArray();
					$mUser = Yii::$app->user->getIdentity();
					$mUser->operateLog(5, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
				}
			}
			
			$aOldRecord = $mKerenBenjin->toArray();
			$mKerenBenjin->set('benjin', $benjin);
			$mKerenBenjin->set('ying_chou', $yingChou);
			$mKerenBenjin->set('shu_fan', $shuFan);
			$mKerenBenjin->set('ying_fee', $yingFee);
			$mKerenBenjin->set('shu_fee', $shuFee);
			$mKerenBenjin->set('agent_id', $agentId);
			$mKerenBenjin->set('remark', $remark);
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
			$aOldPlayer = $mPlayer->toArray();
			if(!$mPlayer->player_id && $playerId){
				ImportData::addEmptyDataRecord(Yii::$app->user->id, $playerId, $playerName);
			}
			$mPlayer->set('player_id', $playerId);
			$mPlayer->set('player_name', $playerName);
			$mPlayer->save();
			$aNewPlayer = $mPlayer->toArray();
			if($aOldPlayer['player_id'] != $aNewPlayer['player_id']){
				$mUser->operateLog(39, ['aOldPlayer' => $aOldPlayer, 'aNewPlayer' => $aNewPlayer]);
			}
			if($aOldPlayer['player_name'] != $aNewPlayer['player_name']){
				$mUser->operateLog(40, ['aOldPlayer' => $aOldPlayer, 'aNewPlayer' => $aNewPlayer]);
			}
		}
		
		return new Response('保存成功', 1);
	}
	
	public function actionExportList(){
		$aList = Player::findAll(['user_id' => Yii::$app->user->id]);
		$aDataList = [
			['游戏名字', '钱包ID', '游戏ID'],
		];
		foreach($aList as $value){
			array_push($aDataList, [
				$value['player_name'],
				$value['keren_bianhao'],
				$value['player_id'],
			]);
		}
		
		$fileName = '客人列表.xlsx';
		
		Yii::$app->excel->setSheetDataFromArray($fileName, $aDataList, true);
	}
	
	public function actionExportPlayerList(){
		$mUser = Yii::$app->user->getIdentity();
		$aList = $mUser->getAllPlayerInfoList();
		$aDataList = [
			['客人编号', '本金', '游戏ID', '游戏名字', '赢抽点数', '输返点数', '代理人', '备注'],
		];
		$aKerenBenjin = [];
		foreach($aList as $value){
			$benjin = $value['benjin'];
			if(!isset($aKerenBenjin[$value['keren_bianhao']])){
				$aKerenBenjin[$value['keren_bianhao']] = $value['benjin'];
			}else{
				$benjin = 0;
			}
			array_push($aDataList, [
				$value['keren_bianhao'],
				$benjin,
				$value['player_id'],
				$value['player_name'],
				$value['ying_chou'],
				$value['shu_fan'],
				$value['agent_id'],
				$value['remark'],
			]);
		}
		
		$fileName = '客人数据列表.xlsx';
		
		Yii::$app->excel->setSheetDataFromArray($fileName, $aDataList, true);
	}
	
	public function actionDeletePlayer(){
		$id = Yii::$app->request->post('id');
		
		$mPlayer = Player::findOne($id);
		if($mPlayer->user_id != Yii::$app->user->id){
			return new Response('出错了', 0);
		}
		$mKerenBenjin = KerenBenjin::findOne(['user_id' => $mPlayer->user_id, 'keren_bianhao' => $mPlayer->keren_bianhao]);
		$aPlayerList = $mKerenBenjin->getPlayerList();
		if(count($aPlayerList) == 1){
			$mKerenBenjin->delete();
		}
		$aPlayer = $mPlayer->toArray();
		$mPlayer->set('keren_bianhao', 0);
		$mPlayer->set('is_delete', 1);
		$mPlayer->save();
		$mUser = Yii::$app->user->getIdentity();
		$mUser->operateLog(9, ['aPlayer' => $aPlayer]);
		
		return new Response('删除成功', 1);
	}
	
	public function actionExportPlayerLastPaijuData(){
		$id = Yii::$app->request->get('id');
		
		$mUser = Yii::$app->user->getIdentity();
		$mPlayer = Player::findOne($id);
		if($mPlayer->user_id != Yii::$app->user->id){
			return new Response('出错了', 0);
		}
		
		$aList = $mPlayer->getLastPaijuData(1, 30);
		$aDataList = [
			['牌局名', '桌子级别', '玩家名', '战绩', '结算'],
		];
		if(!$aList){
			return new Response('暂无数据', 0);
		}
		foreach($aList as $value){
			array_push($aDataList, [
				$value['paiju_name'],
				$value['mangzhu'],
				$value['player_name'],
				$value['zhanji'],
				$value['jiesuan_value'],
			]);
		}
		
		$fileName = '客人牌局数据.xlsx';
		
		Yii::$app->excel->setSheetDataFromArray($fileName, $aDataList, true);
	}
	
	public function actionExportKerenLastPaijuData(){
		$kerenBianhao = Yii::$app->request->get('kerenBianhao');
		
		$mUser = Yii::$app->user->getIdentity();
		$mKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $kerenBianhao]);
		
		if(!$mKerenBenjin){
			return new Response('出错了', 0);
		}
		
		$aList = $mKerenBenjin->getLastPaijuData(1, 30);
		$aDataList = [
			['牌局名', '桌子级别', '玩家名', '战绩', '结算'],
		];
		if(!$aList){
			return new Response('暂无数据', 0);
		}
		foreach($aList as $value){
			array_push($aDataList, [
				$value['paiju_name'],
				$value['mangzhu'],
				$value['player_name'],
				$value['zhanji'],
				$value['jiesuan_value'],
			]);
		}
		
		$fileName = '客人牌局数据.xlsx';
		
		Yii::$app->excel->setSheetDataFromArray($fileName, $aDataList, true);
	}
	
}
