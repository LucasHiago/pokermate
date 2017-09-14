<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use yii\web\UploadedFile;
use common\model\ImportData;
use common\model\Paiju;
use common\model\Lianmeng;
use common\model\Club;

class ImportController extends Controller{
	
	public function actionIndex(){
		return $this->render('index');
	}
	
	public function actionUploadExcel(){
		$oUploadedFile = UploadedFile::getInstanceByName('filecontent');
		$fileName = Yii::getAlias('@p.resource') . '/' . Yii::getAlias('@p.import') . '/' . md5(microtime()) . '.' . $oUploadedFile->getExtension();
		if(!$oUploadedFile->saveAs($fileName)){
			return new Response('上传Excel文件失败', 0);
		}
		
		$mUser = Yii::$app->user->getIdentity();
		try{
			$aDataList = Yii::$app->excel->getSheetDataInArray($fileName);
			if($aDataList){
				ImportData::importFromExcelDataList($mUser, $aDataList);
			}
		}catch(\Exception $e){
			return new Response('Excel文件格式有错误', 0);
		}
		
		return new Response('导入Excel文件成功', 0);
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
		$filePathName = Yii::getAlias('@p.temp_upload') . '/savecode_' . $mClub->club_id . '.jpg';
		$aData = Yii::$app->downLoadExcel->downSaveCode($filePathName);
		if(!$aData){
			return new Response('获取验证码失败', 0);
		}
		$aData['club_login_name'] = $mClub->club_login_name;
		$aData['club_login_password'] = $mClub->club_login_password;
		
		return new Response('', 1, $aData);
	}
	
	public function actionDoImportPaiju(){
		$clubId = (int)Yii::$app->request->post('clubId');
		$safecode = (string)Yii::$app->request->post('safecode');
		$skey = (string)Yii::$app->request->post('skey');
		$aCookie = (array)Yii::$app->request->post('aCookie');
		$retry = (int)Yii::$app->request->post('retry');
		
		$mUser = Yii::$app->user->getIdentity();
		$mClub = Club::findOne($clubId);
		if(!$mClub){
			return new Response('俱乐部不存在', 0);
		}
		
		$isSuccess = Yii::$app->downLoadExcel->getDownloadExcelUrl($mClub, $skey, $safecode, $aCookie, $retry);
		if(!$isSuccess){
			return new Response('服务器连接中断，是否继续请求完成？', 2, Yii::$app->downLoadExcel->aCookieList);
		}
		//导入下载的Excel文件
		$isSuccess = ImportData::importDownloadExcelFiles($mUser, $mClub->club_id);
		if(!$isSuccess){
			return new Response('导入Excel文件数据失败', 0);
		}
		
		return new Response('Success', 1);
	}
	
}
