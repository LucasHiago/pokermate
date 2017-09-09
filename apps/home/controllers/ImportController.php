<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\ImportData;
use common\model\Paiju;

class ImportController extends Controller{
	
	public function actionIndex(){
		$fileName = Yii::getAlias('@p.resource') . '/' . Yii::getAlias('@p.import') . '/test.xls';
		$aDataList = Yii::$app->excel->getSheetDataInArray($fileName);
		if($aDataList){
			ImportData::importFromExcelDataList($aDataList);
		}
		debug('导入Excel文成功', 11);
	}
	
	public function actionGetPaijuDataList(){
		$paijuId = (int)Yii::$app->request->post('paijuId');
		
		$mUser = Yii::$app->user->getIdentity();
		$aList = $mUser->getPaijuDataList($paijuId);
		if(!$aList){
			return new Response('牌局不存在', 0);
		}
		
		return new Response('', 1, ['list' => $aList]);
	}
	
	public function actionSavePaijuDataInfo(){
		$id = (int)Yii::$app->request->post('id');
		$type = (string)Yii::$app->request->post('type');
		$value = Yii::$app->request->post('value');
		
		$mImportData = ImportData::findOne($id);
		if(!$mImportData){
			return new Response('记录不存在', 0);
		}
		if($mImportData->user_id != Yii::$app->user->id){
			return new Response('出错啦', 0);
		}
		if(in_array($type, ['baoxian_heji', 'zhanji'])){
			$mImportData->set($type, (int)$value);
			$mImportData->save();
		}else{
			return new Response('出错啦', 0);
		}
		return new Response('更新成功', 1);
	}
	
}
