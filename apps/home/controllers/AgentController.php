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
use common\model\BaoxianFenchengSetting;
use common\model\ImportData;
use common\model\Calculate;
use common\model\KerenBenjin;
use common\model\MoneyType;
use common\model\AgentQinzhangRecord;
use common\model\AgentBaoxianQinzhangRecord;

class AgentController extends Controller{
	
	public function actionIndex(){
		$agentId = (int)Yii::$app->request->get('agentId');
		$st = (string)Yii::$app->request->get('st');
		
		$mUser = Yii::$app->user->getIdentity();
		if(!$mUser->is_active){
			return new Response('提示:您的账号还没开始启用！', 0);
		}
		if(!$st){
			$st = $mUser->getLastMaxJiaobanPaijuEndTime();
		}else{
			$st = strtotime($st);
		}
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
			$aAgentUnCleanFenChengList = $mUser->getAgentUnCleanFenChengList($aCurrentAgent['id'], $st);
		}
		$totalFenCheng = $mUser->agent_fencheng_ajust_value;
		$floatTotalFenCheng = $mUser->agent_fencheng_ajust_value;
		foreach($aAgentUnCleanFenChengList as $aAgentUnCleanFenCheng){
			$totalFenCheng += $aAgentUnCleanFenCheng['fencheng'];
			$floatTotalFenCheng += $aAgentUnCleanFenCheng['float_fencheng'];
		}
		
		$aMoneyTypeList = $mUser->getMoneyTypeList();
		return $this->render('agent', [
			'aMoneyTypeList' => $aMoneyTypeList,
			'aCurrentAgent' => $aCurrentAgent,
			'aAgentList' => $aAgentList,
			'aAgentUnCleanFenChengList' => $aAgentUnCleanFenChengList,
			'agentFenchengAjustValue' => $mUser->agent_fencheng_ajust_value,
			'totalFenCheng' => $totalFenCheng,
			'floatTotalFenCheng' => Calculate::getIntValueByChoushuiShuanfa($floatTotalFenCheng, $mUser->choushui_shuanfa),
			'st' => date('Y-m-d H:i:s', $st),
		]);
	}
	
	public function actionBaoxian(){
		$agentId = (int)Yii::$app->request->get('agentId');
		$st = (string)Yii::$app->request->get('st');
		
		$mUser = Yii::$app->user->getIdentity();
		if(!$mUser->is_active){
			return new Response('提示:您的账号还没开始启用！', 0);
		}
		if(!$st){
			$st = $mUser->getLastMaxJiaobanPaijuEndTime();
		}else{
			$st = strtotime($st);
		}
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
			$aAgentUnCleanFenChengList = $mUser->getAgentUnCleanBaoxianFenChengList($aCurrentAgent['id'], $st);
		}
		$totalFenCheng = $mUser->agent_baoxian_fencheng_ajust_value;
		$floatTotalFenCheng = $mUser->agent_baoxian_fencheng_ajust_value;
		foreach($aAgentUnCleanFenChengList as $aAgentUnCleanFenCheng){
			$totalFenCheng += $aAgentUnCleanFenCheng['fencheng'];
			$floatTotalFenCheng += $aAgentUnCleanFenCheng['float_fencheng'];
		}
		
		$aMoneyTypeList = $mUser->getMoneyTypeList();
		return $this->render('baoxian', [
			'aMoneyTypeList' => $aMoneyTypeList,
			'aCurrentAgent' => $aCurrentAgent,
			'aAgentList' => $aAgentList,
			'aAgentUnCleanFenChengList' => $aAgentUnCleanFenChengList,
			'agentBaoxianFenchengAjustValue' => $mUser->agent_baoxian_fencheng_ajust_value,
			'totalFenCheng' => $totalFenCheng,
			'floatTotalFenCheng' => Calculate::getIntValueByChoushuiShuanfa($floatTotalFenCheng, $mUser->choushui_shuanfa),
			'st' => date('Y-m-d H:i:s', $st),
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
	
	public function actionSaveBaoxianSetting(){
		$id = (int)Yii::$app->request->post('id');
		$yingfan = (float)Yii::$app->request->post('yingfan');
		$shufan = (float)Yii::$app->request->post('shufan');
		
		$mBaoxianFenchengSetting = BaoxianFenchengSetting::findOne($id);
		if(!$mBaoxianFenchengSetting){
			return new Response('代理分成设置不存在', -1);
		}
		if($mBaoxianFenchengSetting->user_id != Yii::$app->user->id){
			return new Response('出错了', -1);
		}
		$mBaoxianFenchengSetting->set('yingfan', $yingfan);
		$mBaoxianFenchengSetting->set('shufan', $shufan);
		$mBaoxianFenchengSetting->save();
		
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
	
	public function actionOneKeySaveBaoxianSetting(){
		$agentId = (int)Yii::$app->request->post('agentId');
		$type = (string)Yii::$app->request->post('type');
		$yingfan = (float)Yii::$app->request->post('yingfan');
		$shufan = (float)Yii::$app->request->post('shufan');
		if($type == 'yingfan'){
			BaoxianFenchengSetting::oneKeySaveSetting(Yii::$app->user->id, $agentId, $type, $yingfan);
		}elseif($type == 'shufan'){
			BaoxianFenchengSetting::oneKeySaveSetting(Yii::$app->user->id, $agentId, $type, $shufan);
		}else{
			return new Response('操作失败', 1);
		}
		return new Response('操作成功', 1);
	}
		
	public function actionClean(){
		$agentId = (int)Yii::$app->request->post('agentId');
		$aId = (array)Yii::$app->request->post('aId');
		$type = (int)Yii::$app->request->post('type');
		$qinzhangValue = (int)Yii::$app->request->post('qinzhangValue');
		$st = (string)Yii::$app->request->post('st');
		
		$mAgent = Agent::findOne($agentId);
		if(!$mAgent){
			return new Response('代理不存在', 0);
		}
		if(!$aId){
			return new Response('请选择要清账的记录', -1);
		}
		$mUser = Yii::$app->user->getIdentity();
		if(!$st){
			$st = $mUser->getLastMaxJiaobanPaijuEndTime();
		}else{
			$st = strtotime($st);
		}
		$aAgentUnCleanFenChengList = $mUser->getAgentUnCleanFenChengList($agentId, $st);
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
		if($qinzhangValue && in_array($type, [1, 2])){
			if($type == 1){
				$moneyTypeId = (int)Yii::$app->request->post('moneyTypeId');
				$mMoneyType = MoneyType::findOne($moneyTypeId);
				if(!$mMoneyType){
					return new Response('请选择资金转出类型', -1);
				}
				$aOldRecord = $mMoneyType->toArray();
				$mMoneyType->set('money', ['sub', $qinzhangValue]);
				$mMoneyType->save();
				$aNewRecord = $mMoneyType->toArray();
				if($aOldRecord['money'] != $aNewRecord['money']){
					$mUser->operateLog(11, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
				}
			}elseif($type == 2){
				$kerenBianhao = (int)Yii::$app->request->post('kerenBianhao');
				$mKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $kerenBianhao, 'is_delete' => 0]);
				if(!$mKerenBenjin){
					return new Response('客人不存在', -1);
				}
				$aOldRecord = $mKerenBenjin->toArray();
				$mKerenBenjin->set('benjin', ['add', $qinzhangValue]);
				$mKerenBenjin->save();
				$aNewRecord = $mKerenBenjin->toArray();
				$mUser->operateLog(1, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
			}
			AgentQinzhangRecord::addRecord([
				'user_id' => $mUser->id,
				'agent_id' => $agentId,
				'qinzhang_value' => $qinzhangValue,
				'import_data_id' => $aUpdateId,
				'is_show' => 1,
				'create_time' => NOW_TIME,
			]);
		}
		
		return new Response('清账成功', 1);
	}
		
	public function actionCleanBaoxian(){
		$agentId = (int)Yii::$app->request->post('agentId');
		$aId = (array)Yii::$app->request->post('aId');
		$type = (int)Yii::$app->request->post('type');
		$qinzhangValue = (int)Yii::$app->request->post('qinzhangValue');
		$st = (string)Yii::$app->request->post('st');
		
		$mAgent = Agent::findOne($agentId);
		if(!$mAgent){
			return new Response('代理不存在', 0);
		}
		if(!$aId){
			return new Response('请选择要清账的记录', -1);
		}
		$mUser = Yii::$app->user->getIdentity();
		if(!$st){
			$st = $mUser->getLastMaxJiaobanPaijuEndTime();
		}else{
			$st = strtotime($st);
		}
		$aAgentUnCleanFenChengList = $mUser->getAgentUnCleanBaoxianFenChengList($agentId, $st);
		$aUpdateId = [];
		$totalFenCheng = $mUser->agent_baoxian_fencheng_ajust_value;
		$floatTotalFenCheng = $mUser->agent_baoxian_fencheng_ajust_value;
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
		if(!$mUser->agentBaoxianQinZhang($aUpdateId)){
			return new Response('清账失败', 0);
		}
		
		$mUser->operateLog(44, ['aAgent' => $mAgent->toArray(), 'agent_baoxian_fencheng_ajust_value' => $mUser->agent_baoxian_fencheng_ajust_value, 'totalFenCheng' => $totalFenCheng, 'floatTotalFenCheng' => $floatTotalFenCheng, 'aImportId' => $aUpdateId]);
		if($qinzhangValue && in_array($type, [1, 2])){
			if($type == 1){
				$moneyTypeId = (int)Yii::$app->request->post('moneyTypeId');
				$mMoneyType = MoneyType::findOne($moneyTypeId);
				if(!$mMoneyType){
					return new Response('请选择资金转出类型', -1);
				}
				$aOldRecord = $mMoneyType->toArray();
				$mMoneyType->set('money', ['sub', $qinzhangValue]);
				$mMoneyType->save();
				$aNewRecord = $mMoneyType->toArray();
				if($aOldRecord['money'] != $aNewRecord['money']){
					$mUser->operateLog(11, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
				}
			}elseif($type == 2){
				$kerenBianhao = (int)Yii::$app->request->post('kerenBianhao');
				$mKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $kerenBianhao, 'is_delete' => 0]);
				if(!$mKerenBenjin){
					return new Response('客人不存在', -1);
				}
				$aOldRecord = $mKerenBenjin->toArray();
				$mKerenBenjin->set('benjin', ['add', $qinzhangValue]);
				$mKerenBenjin->save();
				$aNewRecord = $mKerenBenjin->toArray();
				$mUser->operateLog(1, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
			}
			AgentBaoxianQinzhangRecord::addRecord([
				'user_id' => $mUser->id,
				'agent_id' => $agentId,
				'qinzhang_value' => $qinzhangValue,
				'import_data_id' => $aUpdateId,
				'is_show' => 1,
				'create_time' => NOW_TIME,
			]);
		}
		
		return new Response('清账成功', 1);
	}
	
	public function actionExportBaoxian(){
		$agentId = (int)Yii::$app->request->get('agentId');
		$st = (string)Yii::$app->request->get('st');
		
		$mAgent = Agent::findOne($agentId);
		if(!$mAgent){
			return new Response('代理不存在', 0);
		}
		$mUser = Yii::$app->user->getIdentity();
		if(!$st){
			$st = $mUser->getLastMaxJiaobanPaijuEndTime();
		}else{
			$st = strtotime($st);
		}
		$aAgentUnCleanFenChengList = $mUser->getAgentUnCleanBaoxianFenChengList($agentId, $st);
		$aDataList = [
			['牌局名', '桌子级别', '玩家名', '保险', '分成比例', '分成'],
		];
		if(!$aAgentUnCleanFenChengList){
			return new Response('暂无代理数据', 0);
		}
		$fenchengTotal = 0;
		foreach($aAgentUnCleanFenChengList as $value){
			$fenchengRate = 0;
			if($value['baoxian_heji'] > 0){
				$fenchengRate = $value['yingfan'];
			}else{
				$fenchengRate = $value['shufan'];
			}
			$fenchengRate .= '%';
			array_push($aDataList, [
				$value['paiju_name'],
				$value['mangzhu'],
				$value['player_name'],
				$value['baoxian_heji'],
				$fenchengRate,
				$value['fencheng'],
			]);
			$fenchengTotal += $value['fencheng'];
		}
		array_push($aDataList, [
			'',
			'',
			'',
			'',
			'合计',
			$fenchengTotal,
		]);
		$fileName = '代理保险数据(' . $mAgent->agent_name . ').xlsx';
		//$this->_htmlToExcel($aDataList);exit;
		Yii::$app->excel->setSheetDataFromArray($fileName, $aDataList, true);
	}
	
	public function actionExport(){
		$agentId = (int)Yii::$app->request->get('agentId');
		$st = (string)Yii::$app->request->get('st');
		
		$mAgent = Agent::findOne($agentId);
		if(!$mAgent){
			return new Response('代理不存在', 0);
		}
		$mUser = Yii::$app->user->getIdentity();
		if(!$st){
			$st = $mUser->getLastMaxJiaobanPaijuEndTime();
		}else{
			$st = strtotime($st);
		}
		$aAgentUnCleanFenChengList = $mUser->getAgentUnCleanFenChengList($agentId, $st);
		$aDataList = [
			['牌局名', '桌子级别', '玩家名', '战绩', '结算', '分成比例', '分成'],
		];
		if(!$aAgentUnCleanFenChengList){
			return new Response('暂无代理数据', 0);
		}
		$fenchengTotal = 0;
		foreach($aAgentUnCleanFenChengList as $value){
			$fenchengRate = 0;
			if($value['zhanji'] > 0){
				$fenchengRate = $value['yingfan'];
			}else{
				$fenchengRate = $value['shufan'];
			}
			$fenchengRate .= '%';
			array_push($aDataList, [
				$value['paiju_name'],
				$value['mangzhu'],
				$value['player_name'],
				$value['zhanji'],
				$value['jiesuan_value'],
				$fenchengRate,
				$value['fencheng'],
			]);
			$fenchengTotal += $value['fencheng'];
		}
		array_push($aDataList, [
			'',
			'',
			'',
			'',
			'',
			'合计',
			$fenchengTotal,
		]);
		$fileName = '代理数据(' . $mAgent->agent_name . ').xlsx';
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
				$table .= '<td>' . $v[5] . '</td>';
			$table .= '</tr>';
		}
		$table .= '</table>';
		$fileName = '代理数据.xlsx';
		Yii::$app->excel->htmlTableToExcel($fileName, $table);
		
	}
	
	public function actionGetAllAgentTotalFencheng(){
		$st = (string)Yii::$app->request->post('st');
		
		$mUser = Yii::$app->user->getIdentity();
		if(!$st){
			$st = $mUser->getLastMaxJiaobanPaijuEndTime();
		}else{
			$st = strtotime($st);
		}
		$aAgentList = $mUser->getAgentList();
		
		$totalFenCheng = $mUser->agent_fencheng_ajust_value;
		//$floatTotalFenCheng = $mUser->agent_fencheng_ajust_value;
		foreach($aAgentList as $aCurrentAgent){
			$aAgentUnCleanFenChengList = $mUser->getAgentUnCleanFenChengList($aCurrentAgent['id'], $st);
			foreach($aAgentUnCleanFenChengList as $aAgentUnCleanFenCheng){
				$totalFenCheng += $aAgentUnCleanFenCheng['fencheng'];
				//$floatTotalFenCheng += $aAgentUnCleanFenCheng['float_fencheng'];
			}
		}
		
		return new Response('', 1, $totalFenCheng);
	}
	
	public function actionGetAllAgentTotalBaoxianFencheng(){
		$st = (string)Yii::$app->request->post('st');
		
		$mUser = Yii::$app->user->getIdentity();
		if(!$st){
			$st = $mUser->getLastMaxJiaobanPaijuEndTime();
		}else{
			$st = strtotime($st);
		}
		$aAgentList = $mUser->getAgentList();
		
		$totalFenCheng = $mUser->agent_baoxian_fencheng_ajust_value;
		//$floatTotalFenCheng = $mUser->agent_baoxian_fencheng_ajust_value;
		foreach($aAgentList as $aCurrentAgent){
			$aAgentUnCleanFenChengList = $mUser->getAgentUnCleanBaoxianFenChengList($aCurrentAgent['id'], $st);
			foreach($aAgentUnCleanFenChengList as $aAgentUnCleanFenCheng){
				$totalFenCheng += $aAgentUnCleanFenCheng['fencheng'];
				//$floatTotalFenCheng += $aAgentUnCleanFenCheng['float_fencheng'];
			}
		}
		
		return new Response('', 1, $totalFenCheng);
	}
	
}
