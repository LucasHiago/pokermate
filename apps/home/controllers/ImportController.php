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
use common\model\Player;
use common\model\KerenBenjin;

class ImportController extends Controller{
	
	public function actionIndex(){
		return $this->render('index');
	}
	
	public function actionUploadExcel(){
		set_time_limit(0);
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
		
		return new Response('导入Excel文件成功', 1);
	}
	
	public function actionShowImportPlayer(){
		return $this->render('import_player');
	}
	
	public function actionUploadPlayerExcel(){
		set_time_limit(0);
		$oUploadedFile = UploadedFile::getInstanceByName('filecontent');
		$fileName = Yii::getAlias('@p.resource') . '/' . Yii::getAlias('@p.import') . '/' . md5(microtime()) . '.' . $oUploadedFile->getExtension();
		if(!$oUploadedFile->saveAs($fileName)){
			return new Response('上传Excel文件失败', 0);
		}
		
		$mUser = Yii::$app->user->getIdentity();
		try{
			$aDataList = Yii::$app->excel->getSheetDataInArray($fileName);
			if($aDataList){
				unset($aDataList[0]);
				foreach($aDataList as $aData){
					$playerName = (string)$aData[0];
					$kerenBianhao = (int)$aData[1];
					$playerId = (int)$aData[2];
					ImportData::addEmptyDataRecord($mUser->id, $playerId, $playerName);
					$mKerenBenjin = KerenBenjin::findOne([
						'user_id' => $mUser->id,
						'keren_bianhao' => $kerenBianhao,
						'is_delete' => 0,
					]);
					if(!$mKerenBenjin){
						KerenBenjin::addRecord([
							'user_id' => $mUser->id, 
							'keren_bianhao' => $kerenBianhao, 
							'create_time' => NOW_TIME
						]);
					}
					$mPlayer = Player::findOne([
						'user_id' => $mUser->id,
						'keren_bianhao' => $kerenBianhao,
						'player_id' => $playerId,
						'is_delete' => 0,
					]);
					if(!$mPlayer){
						Player::addRecord([
							'user_id' => $mUser->id,
							'keren_bianhao' => $kerenBianhao,
							'player_id' => $playerId,
							'player_name' => $aData[0],
							'create_time' => NOW_TIME,
						]);
					}
				}
				return new Response('导入Excel文件成功', 1);
			}
		}catch(\Exception $e){
			return new Response('Excel文件格式有错误', 0);
		}
		
		return new Response('导入Excel文件成功', 1);
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
		if($mImportData->status){
			return new Response('已结算的记录不可以修改', -1);
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
		if($mImportData->status){
			return new Response('不能重复结算', 0);
		}
		if(!$mImportData->doJieShuan()){
			return new Response('结算失败', 0);
		}
		
		$mUser = Yii::$app->user->getIdentity();
		$aUnJiaoBanPaijuTotalStatistic = $mUser->getUnJiaoBanPaijuTotalStatistic();
		$mPaiju = $mImportData->getMPaiju();
		$isReloadPage = 0;
		if($mPaiju->status == Paiju::STATUS_DONE){
			$isReloadPage = 1;
		}
		
		
		return new Response('结算成功', 1, [
			'aUnJiaoBanPaijuTotalStatistic' => $aUnJiaoBanPaijuTotalStatistic,
			'isReloadPage' => $isReloadPage,
		]);
	}
	
	public function actionGetDownloadSaveCode(){
		$clubId = (int)Yii::$app->request->post('clubId');
		
		$mClub = Club::findOne($clubId);
		if(!$mClub){
			return new Response('俱乐部不存在', 0);
		}
		$savePathName = Yii::$app->downLoadExcel->downSaveCode($mClub->club_id);
		if(!$savePathName){
			return new Response('获取验证码失败', 0);
		}
		$aData = [
			'path' => $savePathName,
			'club_login_name' => $mClub->club_login_name,
			'club_login_password' => $mClub->club_login_password,
			'start_time' => date('Y-m-d', NOW_TIME - 86400),
			'end_time' => date('Y-m-d'),
		];
		
		return new Response('', 1, $aData);
	}
	
	public function actionDoImportPaiju(){
		$clubId = (int)Yii::$app->request->post('clubId');
		$safecode = (string)Yii::$app->request->post('safecode');
		$skey = (string)Yii::$app->request->post('skey');
		$startTime = strtotime((string)Yii::$app->request->post('startTime'));
		$endTime = strtotime((string)Yii::$app->request->post('endTime'));
		$retry = (int)Yii::$app->request->post('retry');
		
		$mUser = Yii::$app->user->getIdentity();
		$mClub = Club::findOne($clubId);
		if(!$mClub){
			return new Response('俱乐部不存在', 0);
		}
		if($retry){
			//重新请求完成时，先将已下载的Excel文件导入数据库
			$this->_importDownloadExcelFiles($mUser, $mClub->club_id);
		}
		
		if($startTime && $endTime){
			if($startTime > $endTime){
				return new Response('开始时间不能大于结束时间', 0);
			}
			if($startTime < strtotime('2017-08-15')){
				return new Response('开始时间不能小于2017-08-15', 0);
			}
			if($endTime > NOW_TIME){
				return new Response('结束时间不能大于今天', 0);
			}
		}else{
			return new Response('请选择时间范围', 0);
		}
		$isSuccess = Yii::$app->downLoadExcel->goLoginAndDownloadExcel($mClub, $skey, $safecode, $retry, date('Y-m-d', $startTime), date('Y-m-d', $endTime));
		if(!$isSuccess){
			if(Yii::$app->downLoadExcel->getMessage() == 'login_fail'){
				return new Response('验证码或账号不正确', 3);
			}
			return new Response('服务器连接中断，是否继续请求完成？', 2);
		}
		//导入下载的Excel文件
		$isSuccess = $this->_importDownloadExcelFiles($mUser, $mClub->club_id);
		if(!$isSuccess){
			return new Response('导入Excel文件数据失败', 0);
		}
		
		return new Response('Success', 1);
	}
	
	private  function _importDownloadExcelFiles($mUser, $clubId){
		$isSuccess = ImportData::importDownloadExcelFiles($mUser, $clubId);
		return $isSuccess;
	}
	
}
