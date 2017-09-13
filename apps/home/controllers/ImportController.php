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
use common\model\Lianmeng;
use common\model\Club;

class ImportController extends Controller{
	
	public function actionIndex(){
		$fileName = Yii::getAlias('@p.resource') . '/' . Yii::getAlias('@p.import') . '/test2.xls';
		$aDataList = Yii::$app->excel->getSheetDataInArray($fileName);
		if($aDataList){
			ImportData::importFromExcelDataList($aDataList);
		}
		debug('导入Excel文成功', 11);
	}
	
	public function actionGetPaijuDataList(){
		$paijuId = (int)Yii::$app->request->post('paijuId');
		$isAllRecordData = (int)Yii::$app->request->post('isAllRecordData');
		
		$isAllRecordData = $isAllRecordData ? true : false;
		$mUser = Yii::$app->user->getIdentity();
		$aList = $mUser->getPaijuDataList($paijuId, $isAllRecordData);
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
	
	public function actionDoJieShuan(){
		$id = (int)Yii::$app->request->post('id');
		
		$mImportData = ImportData::findOne($id);
		if(!$mImportData){
			return new Response('记录不存在', 0);
		}
		if($mImportData->user_id != Yii::$app->user->id){
			return new Response('出错啦', 0);
		}
		/*if($mImportData->status){
			return new Response('不能重复结算', 0);
		}*/
		if(!$mImportData->doJieShuan()){
			return new Response('结算失败', 0);
		}
		
		$mUser = Yii::$app->user->getIdentity();
		$aUnJiaoBanPaijuTotalStatistic = $mUser->getUnJiaoBanPaijuTotalStatistic();
		
		return new Response('结算成功', 1, [
			'aUnJiaoBanPaijuTotalStatistic' => $aUnJiaoBanPaijuTotalStatistic,
		]);
	}
	
	public function actionGetDownloadSaveCode(){
		$clubId = (int)Yii::$app->request->post('clubId');
		
		$mClub = Club::findOne($clubId);
		if(!$mClub){
			return new Response('俱乐部不存在', 0);
		}
		$mUser = Yii::$app->user->getIdentity();
		$filePathName = Yii::getAlias('@p.temp_upload') . '/savecode.jpg';
		$aData = Yii::$app->downLoadExcel->downSaveCode($filePathName);
		if(!$aData){
			return new Response('获取验证码失败', 0);
		}
		$aData['club_login_name'] = $mClub->club_login_name;
		$aData['club_login_password'] = $mClub->club_login_password;
		
		return new Response('', 1, $aData);
	}
	
}
