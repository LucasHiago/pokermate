<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\form\KerenBenjinListForm;
use common\model\KerenBenjin;

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
		if($kerenBianhao){
			$isMerge = (int)Yii::$app->request->post('isMerge');
			if($mKerenBenjin->keren_bianhao != $kerenBianhao){
				$mTempKerenBenjin = KerenBenjin::findOne(['user_id' => Yii::$app->user->id, 'keren_bianhao' => $kerenBianhao, 'is_delete' => 0]);
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
					return new Response('该编号不存在', -1);
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
	
}
