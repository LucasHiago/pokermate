<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\Agent;
use common\model\FenchengSetting;
use common\model\ImportData;
use common\model\Calculate;

class AgentController extends Controller{
	
	public function actionIndex(){
		$agentId = (int)Yii::$app->request->get('agentId');
		
		$mUser = Yii::$app->user->getIdentity();
		$aAgentList = $mUser->getAgentList();
		$aCurrentAgent = [];
		if($aAgentList){
			$aCurrentAgent = $aAgentList[0];
			foreach($aAgentList as $aAgent){
				if($aAgent['id'] == $agentId){
					$aCurrentAgent = $aAgent;
					break;
				}
			}
		}
		$aAgentUnCleanFenChengList = [];
		if($aCurrentAgent){
			$aAgentUnCleanFenChengList = $mUser->getAgentUnCleanFenChengList($aCurrentAgent['id']);
		}
		$totalFenCheng = $mUser->agent_fencheng_ajust_value;
		$floatTotalFenCheng = $mUser->agent_fencheng_ajust_value;
		foreach($aAgentUnCleanFenChengList as $aAgentUnCleanFenCheng){
			$totalFenCheng += $aAgentUnCleanFenCheng['fencheng'];
			$floatTotalFenCheng += $aAgentUnCleanFenCheng['float_fencheng'];
		}
		
		return $this->render('agent', [
			'aCurrentAgent' => $aCurrentAgent,
			'aAgentList' => $aAgentList,
			'aAgentUnCleanFenChengList' => $aAgentUnCleanFenChengList,
			'agentFenchengAjustValue' => $mUser->agent_fencheng_ajust_value,
			'totalFenCheng' => $totalFenCheng,
			'floatTotalFenCheng' => Calculate::getIntValueByChoushuiShuanfa($floatTotalFenCheng, $mUser->choushui_shuanfa),
		]);
	}
	
	public function actionAdd(){
		$agentName = Yii::$app->request->post('agentName');
		
		if(!$agentName){
			return new Response('请输入代理名字', -1);
		}
		$mAgent = Agent::findOne(['user_id' => Yii::$app->user->id, 'agent_name' => $agentName]);
		if($mAgent){
			return new Response('代理已存在', -1);
		}
		$isSuccess = Agent::addRecord(['user_id' => Yii::$app->user->id, 'agent_name' => $agentName, 'create_time' => NOW_TIME]);
		if(!$isSuccess){
			return new Response('新增失败', 0);
		}
		$mAgent = Agent::findOne($isSuccess);
		$aAgent = $mAgent->toArray();
		$mUser = Yii::$app->user->getIdentity();
		$mUser->operateLog(27, ['aAgent' => $aAgent]);
		
		return new Response('新增成功', 1);
	}
	
	public function actionDelete(){
		$aAgentId = (array)Yii::$app->request->post('aAgentId');
		
		$mUser = Yii::$app->user->getIdentity();
		if(!$aAgentId){
			return new Response('请选择要删除的代理', -1);
		}
		$aAgentList = Agent::findAll(['id' => $aAgentId]);
		if(!$aAgentList){
			return new Response('代理不存在', -1);
		}
		foreach($aAgentList as $aAgent){
			$mAgent = Agent::toModel($aAgent);
			if($mAgent->user_id == Yii::$app->user->id){
				$aAgent = $mAgent->toArray();
				$mAgent->set('is_delete', 1);
				$mAgent->save();
				$mUser->operateLog(28, ['aAgent' => $aAgent]);
			}
		}
		return new Response('操作成功', 1);
	}
	
	public function actionSaveSetting(){
		$id = (int)Yii::$app->request->post('id');
		$yingfan = (float)Yii::$app->request->post('yingfan');
		$shufan = (float)Yii::$app->request->post('shufan');
		
		$mFenchengSetting = FenchengSetting::findOne($id);
		if(!$mFenchengSetting){
			return new Response('代理分成设置不存在', -1);
		}
		if($mFenchengSetting->user_id != Yii::$app->user->id){
			return new Response('出错了', -1);
		}
		$mFenchengSetting->set('yingfan', $yingfan);
		$mFenchengSetting->set('shufan', $shufan);
		$mFenchengSetting->save();
		
		return new Response('操作成功', 1);
	}
	
	public function actionOneKeySaveSetting(){
		$agentId = (int)Yii::$app->request->post('agentId');
		$type = (string)Yii::$app->request->post('type');
		$yingfan = (float)Yii::$app->request->post('yingfan');
		$shufan = (float)Yii::$app->request->post('shufan');
		if($type == 'yingfan'){
			FenchengSetting::oneKeySaveSetting(Yii::$app->user->id, $agentId, $type, $yingfan);
		}elseif($type == 'shufan'){
			FenchengSetting::oneKeySaveSetting(Yii::$app->user->id, $agentId, $type, $shufan);
		}else{
			return new Response('操作失败', 1);
		}
		return new Response('操作成功', 1);
	}
		
	public function actionClean(){
		$agentId = (int)Yii::$app->request->post('agentId');
		$aId = (array)Yii::$app->request->post('aId');
		
		$mAgent = Agent::findOne($agentId);
		if(!$mAgent){
			return new Response('代理不存在', 0);
		}
		if(!$aId){
			return new Response('请选择要清账的记录', -1);
		}
		$mUser = Yii::$app->user->getIdentity();
		$aAgentUnCleanFenChengList = $mUser->getAgentUnCleanFenChengList($agentId);
		$aUpdateId = [];
		$totalFenCheng = $mUser->agent_fencheng_ajust_value;
		$floatTotalFenCheng = $mUser->agent_fencheng_ajust_value;
		foreach($aAgentUnCleanFenChengList as $aAgentUnCleanFenCheng){
			if(in_array($aAgentUnCleanFenCheng['id'], $aId)){
				$totalFenCheng += $aAgentUnCleanFenCheng['fencheng'];
				$floatTotalFenCheng += $aAgentUnCleanFenCheng['float_fencheng'];
				array_push($aUpdateId, $aAgentUnCleanFenCheng['id']);
			}
		}
		if(!$aUpdateId){
			return new Response('请选择要清账的记录', 0);
		}
		if(!$mUser->agentQinZhang($aUpdateId)){
			return new Response('清账失败', 0);
		}
		
		$mUser->operateLog(29, ['aAgent' => $mAgent->toArray(), 'agent_fencheng_ajust_value' => $mUser->agent_fencheng_ajust_value, 'totalFenCheng' => $totalFenCheng, 'floatTotalFenCheng' => $floatTotalFenCheng, 'aImportId' => $aUpdateId]);
		
		return new Response('清账成功', 1);
	}
	
	public function actionExport(){
		$agentId = (int)Yii::$app->request->get('agentId');
		
		$mUser = Yii::$app->user->getIdentity();
		$aAgentUnCleanFenChengList = $mUser->getAgentUnCleanFenChengList($agentId);
		$aDataList = [
			['牌局名', '桌子级别', '玩家名', '战绩', '分成'],
		];
		if(!$aAgentUnCleanFenChengList){
			return new Response('暂无代理数据', 0);
		}
		foreach($aAgentUnCleanFenChengList as $value){
			array_push($aDataList, [
				$value['paiju_name'],
				$value['mangzhu'],
				$value['player_name'],
				$value['zhanji'],
				$value['fencheng'],
			]);
		}
		
		$fileName = '代理数据.xlsx';
		//$this->_htmlToExcel($aDataList);exit;
		Yii::$app->excel->setSheetDataFromArray($fileName, $aDataList, true);
	}
	
	private function _htmlToExcel($aData){
		$table = '<table border="1" bordercolor="#c0c0c0">';
		foreach($aData as $v){
			$table .= '<tr>';
				$table .= '<td>' . $v[0] . '</td>';
				$table .= '<td>' . $v[1] . '</td>';
				$table .= '<td>' . $v[2] . '</td>';
				$table .= '<td>' . $v[3] . '</td>';
				$table .= '<td>' . $v[4] . '</td>';
			$table .= '</tr>';
		}
		$table .= '</table>';
		$fileName = '代理数据.xlsx';
		Yii::$app->excel->htmlTableToExcel($fileName, $table);
		
	}
}
