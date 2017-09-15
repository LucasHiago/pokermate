<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\form\ClubListForm;
use common\model\Club;

class ClubManageController extends Controller{
	public $layout = 'manage';
	
	public function actionIndex(){
		$oListForm = new ClubListForm();
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
	
	public function actionSetDelete(){
		$oController = new ClubController($this->id, Yii::$app);
		return $oController->actionDelete();
	}
	
	public function actionShowEdit(){
		$id = Yii::$app->request->get('id');
		
		$mClub = Club::findOne($id);
		$aClub = [];
		if($mClub){
			$aClub = $mClub->toArray();
		}
		
		return $this->render('edit', [
			'aClub' => $aClub,
		]);
	}
	
	public function actionEdit(){
		$oController = new ClubController($this->id, Yii::$app);
		return $oController->actionSave();
	}
	
}
