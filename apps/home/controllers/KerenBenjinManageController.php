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
		$this->_fixedMarkPlayer();
		$oListForm = new PlayerListForm();
		$aParams = Yii::$app->request->get();
		if($aParams && (!$oListForm->load($aParams, '') || !$oListForm->validate())){
			return new Response(current($oListForm->getErrors())[0]);
		}
		$aList = $oListForm->getList();
		
		return $this->render('player_list', [
			'aList' => $aList,
		]);
	}
	
	private function _fixedMarkPlayer(){
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
		$remark = (int)Yii::$app->request->post('remark');
		
		if(!$id){
			$oController = new IndexController($this->id, Yii::$app);
			return $oController->actionAddKeren();
		}
		$mKerenBenjin = KerenBenjin::findOne(['id' => $id, 'is_delete' => 0]);
		if(!$mKerenBenjin){
			return new Response('会员不存在', -1);
		}
		if(!KerenBenjin::checkKerenbianhao($kerenBianhao)){
			return new Response('客人编号范围有误', -1);
		}
		if($kerenBianhao){
			$isMerge = (int)Yii::$app->request->post('isMerge');
			if($mKerenBenjin->keren_bianhao != $kerenBianhao){
				$mTempKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $kerenBianhao]);
				if($mTempKerenBenjin){	
					if($isMerge){
						$mTempKerenBenjin->set('benjin', ['add', $mKerenBenjin->benjin]);
						$mTempKerenBenjin->set('is_delete', 0);
						$mTempKerenBenjin->save();
						
						$mKerenBenjin->set('is_delete', 1);
						$mKerenBenjin->save();
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
		$mKerenBenjin->set('shu_fan', $shu_fan);
		$mKerenBenjin->set('agent_id', $agentId);
		$mKerenBenjin->set('remark', $remark);
		$mKerenBenjin->save();
		
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
		$mPlayer->set('is_delete', 1);
		$mPlayer->save();
		return new Response('删除成功', 1);
	}
	
}
