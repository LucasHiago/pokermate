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

class AgentController extends Controller{
	
	public function actionIndex(){
		$mUser = Yii::$app->user->getIdentity();
		$aAgentList = $mUser->getAgentList();
		$aFenchengListSetting = $mUser->getFenchengListSetting();
		
		return $this->render('agent', [
			'aAgentList' => $aAgentList,
			'aFenchengListSetting' => $aFenchengListSetting,
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
		if(!Agent::addRecord(['user_id' => Yii::$app->user->id, 'agent_name' => $agentName, 'create_time' => NOW_TIME])){
			return new Response('新增失败', 0);
		}
		return new Response('新增成功', 1);
	}
	
	public function actionDelete(){
		$aAgentId = (array)Yii::$app->request->post('aAgentId');
		
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
				$mAgent->set('is_delete', 1);
				$mAgent->save();
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
		$type = (string)Yii::$app->request->post('type');
		$yingfan = (float)Yii::$app->request->post('yingfan');
		$shufan = (float)Yii::$app->request->post('shufan');
		if($type == 'yingfan'){
			FenchengSetting::oneKeySaveSetting(Yii::$app->user->id, $type, $yingfan);
		}elseif($type == 'shufan'){
			FenchengSetting::oneKeySaveSetting(Yii::$app->user->id, $type, $shufan);
		}else{
			return new Response('操作失败', 1);
		}
		return new Response('操作成功', 1);
	}
	
}
