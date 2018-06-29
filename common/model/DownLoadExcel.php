<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class DownLoadExcel extends \yii\base\Object{
	public $savecodeUrl;
	public $savecodeUrl1;
	public $loginPageUrl;
	public $loginUrl;
	public $loginUrl1;
	public $selectClubUrl;
	public $selectClubUrl1;
	public $historyExportUrl;
	public $exportRoomUrl;
	public $exportUrl;
	public $tokenUrl;
	
	private $_cookieFile = '';
	private $_message = '';
	
	public function init(){
		parent::init();
		$this->_cookieFile = Yii::getAlias('@p.resource') . '/data/temp/cookie.tmp';
	}
	
	public function getMessage(){
		return $this->_message;
	}
	
	private function _doHttpResponsePost($url, $aParam = [], $headers = []){
		$para = '';
		if($aParam){
			$para = http_build_query($aParam);
		}
		$curl = curl_init($url);
		if($this->_cookieFile){
			curl_setopt($curl, CURLOPT_COOKIEFILE, $this->_cookieFile);
		}
		if($this->_cookieFile){
			curl_setopt($curl, CURLOPT_COOKIEJAR, $this->_cookieFile);
		}
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);	// SSL证书认证
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);	// 严格认证
		curl_setopt($curl, CURLOPT_HEADER, 0 ); 	// 过滤HTTP头
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);	// 显示输出结果
		if($headers){
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}
		curl_setopt($curl, CURLOPT_POST, true);	 // post传输数据
		curl_setopt($curl, CURLOPT_POSTFIELDS, $para);	// post传输数据
		$responseText = curl_exec($curl);
		curl_close($curl);

		return $responseText;
	}
	
	private function _doHttpResponseGet($url, $aParam = [], $headers = []){
		$para = '';
		if($aParam){
			$para = http_build_query($aParam);
		}
		$curl = curl_init($url);
		if($this->_cookieFile){
			curl_setopt($curl, CURLOPT_COOKIEFILE, $this->_cookieFile);
		}
		if($this->_cookieFile){
			curl_setopt($curl, CURLOPT_COOKIEJAR, $this->_cookieFile);
		}
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);	// SSL证书认证
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);	// 严格认证
		curl_setopt($curl, CURLOPT_HEADER, 0 ); 	// 过滤HTTP头
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);	// 显示输出结果
		if($headers){
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}
		$responseText = curl_exec($curl);
		curl_close($curl);

		return $responseText;
	}
	
	public function getModulusAndExponentValueValue(){
		$returnString = $this->_doHttpResponsePost($this->loginPageUrl);
		if(!$returnString){
			return false;
		}
		$modulusValue = $this->_getModulusValueFromHtml($returnString);
		$exponentValue = $this->_getExponentValueFromHtml($returnString);
		return [
			'modulusValue' => $modulusValue,
			'exponentValue' => $exponentValue,
		];
	}
	
	public function downSaveCode($clubId){
		$this->_cookieFile = Yii::getAlias('@p.resource') . '/data/temp/cookie_' . Yii::$app->user->id . '_' . $clubId . '.tmp';
		$savePathName = Yii::getAlias('@p.temp_upload') . '/savecode_' . $clubId . '.jpg';
		$returnString = $this->_doHttpResponsePost($this->savecodeUrl);
		if(!$returnString){
			return false;
		}
		file_put_contents(Yii::getAlias('@p.resource') . '/' . $savePathName, $returnString);
		
		return $savePathName;
	}
	
	public function getLoginToken($clubId){
		$this->_cookieFile = Yii::getAlias('@p.resource') . '/data/temp/cookie_' . Yii::$app->user->id . '_' . $clubId . '.tmp';
		
		$returnString = $this->_doHttpResponsePost($this->tokenUrl);
		if(!$returnString){
			return false;
		}
		$aData = json_decode($returnString, 1);
		if(!isset($aData['result']) || !$aData['result']){
			return false;
		}
		
		return $aData['result'];
	}
	
	public function downSaveCode1($clubId, $token){
		$this->_cookieFile = Yii::getAlias('@p.resource') . '/data/temp/cookie_' . Yii::$app->user->id . '_' . $clubId . '.tmp';
		//$savePathName = Yii::getAlias('@p.temp_upload') . '/savecode_' . $clubId . '.jpg';
		$aParam = ['token' => $token];
		$returnString = $this->_doHttpResponsePost($this->savecodeUrl1, $aParam);
		if(!$returnString){
			return false;
		}
		$aData = json_decode($returnString, 1);
		if(!isset($aData['result']) || !$aData['result']){
			return false;
		}
		//file_put_contents(Yii::getAlias('@p.resource') . '/' . $savePathName, $aData['result']);
		
		return 'data:image/jpg;base64,' . $aData['result'];
	}
	
	public function goLoginAndDownloadExcel($mClub, $skey, $safecode, $retry, $startDay, $endDay){
		set_time_limit(0);
		//ini_set("memory_limit", "1024M");
		$clubId = $mClub->club_id;
		$this->_cookieFile = Yii::getAlias('@p.resource') . '/data/temp/cookie_' . $mClub->user_id . '_' . $clubId . '.tmp';
		$aParam = ['key' => $skey, 'safecode' => $safecode];
		if(!$retry){
			//登录请求
			$returnString = $this->_doHttpResponsePost($this->loginUrl, $aParam);
			if($returnString){
				$this->_message = 'login_fail';
				return false;
			}
			//选择俱乐部页面请求
			$returnString = $this->_doHttpResponsePost($this->selectClubUrl . $clubId);
			if(!$returnString){
				return false;
			}
			file_put_contents(Yii::getAlias('@p.resource') . '/' . Yii::getAlias('@p.temp_upload') . '/select_club_' . $clubId . '.html', $returnString);
		}
		//////////////////////////////楼上的代码都不干正事的2333////////////////////////////////////////////
		/*for($i = strtotime($startDay); $i <= strtotime($endDay); $i += 86400){
			$isSuccess = $this->_getDownLoadAndExecuteOneExcel($mClub, date('Y-m-d', $i), date('Y-m-d', $i));
			if(!$isSuccess){
				return false;
			}
		}*/
		$isSuccess = $this->_getDownLoadAndExecuteOneExcel($mClub, $startDay, $endDay);
		if(!$isSuccess){
			return false;
		}
		return true;
	}
	
	public function goLoginAndDownloadExcel1($mClub, $token, $data, $safecode, $retry, $startDay, $endDay){
		set_time_limit(0);
		//ini_set("memory_limit", "1024M");
		$clubId = $mClub->club_id;
		$this->_cookieFile = Yii::getAlias('@p.resource') . '/data/temp/cookie_' . $mClub->user_id . '_' . $clubId . '.tmp';
		$aParam = ['token' => $token, 'data' => $data, 'safeCode' => $safecode, 'locale' => 'zh'];
		
		if(!$retry){
			//登录请求
			$returnString = $this->_doHttpResponsePost($this->loginUrl1, $aParam);
			$aData = json_decode($returnString, 1);
			if(!isset($aData['iErrCode']) || $aData['iErrCode']){
				$this->_message = 'login_fail';
				return false;
			}
			/*if($returnString){
				$this->_message = 'login_fail';
				return false;
			}*/
			//选择俱乐部页面请求
			$returnString = $this->_doHttpResponsePost($this->selectClubUrl1, [], ['token:' . $token]);
			$returnString1 = $this->_doHttpResponsePost('http://cms.pokermanager.club/cms-api/user/getCurrentUserInfo', [], ['token:' . $token]);
			$aUserData = json_decode($returnString1, 1);
			$returnString = $this->_doHttpResponsePost('http://cms.pokermanager.club/cms-api/resource/getMenuList', ['clubId' => $clubId, 'uuid' => $aUserData['result']['uuid']], ['token:' . $token]);
			$returnString = $this->_doHttpResponsePost('http://cms.pokermanager.club/cms-api/notice/getNotice', [], ['token:' . $token]);
			$returnString = $this->_doHttpResponsePost('http://cms.pokermanager.club/cms-api/resource/checkPermission', ['clubId' => $clubId, 'resourceId' => 21], ['token:' . $token]);
			$returnString = $this->_doHttpResponsePost('http://cms.pokermanager.club/cms-api/resource/checkPermission', ['clubId' => $clubId, 'resourceId' => 22], ['token:' . $token]);
			$returnString = $this->_doHttpResponsePost('http://cms.pokermanager.club/cms-api/game/getBuyinCount', ['clubId' => $clubId], ['token:' . $token]);
			$returnString = $this->_doHttpResponsePost('http://cms.pokermanager.club/cms-api/club/clubInfo', ['clubId' => $clubId], ['token:' . $token]);
			$returnString = $this->_doHttpResponsePost('http://cms.pokermanager.club/cms-api/game/getBuyinCount', ['clubId' => $clubId], ['token:' . $token]);
			$returnString = $this->_doHttpResponsePost('http://cms.pokermanager.club/cms-api/user/getClubUserLevel', ['clubId' => $clubId, 'uuid' => $aUserData['result']['uuid']], ['token:' . $token]);
			//$returnString = $this->_doHttpResponsePost($this->selectClubUrl . $clubId);
			/*if(!$returnString){
				return false;
			}*/
			file_put_contents(Yii::getAlias('@p.resource') . '/' . Yii::getAlias('@p.temp_upload') . '/select_club_' . $clubId . '.html', $returnString);
				
		}
		
		$isSuccess = $this->_newDownLoadExcel($mClub, $startDay, $endDay, 1, $token);
		if(!$isSuccess){
			return false;
		}
		$isSuccess = $this->_newDownLoadExcel($mClub, $startDay, $endDay, 2, $token);
		if(!$isSuccess){
			return false;
		}
		$isSuccess = $this->_newDownLoadExcel($mClub, $startDay, $endDay, 6, $token);
		if(!$isSuccess){
			return false;
		}
		$isSuccess = $this->_newDownLoadExcel($mClub, $startDay, $endDay, 5, $token);
		if(!$isSuccess){
			//return false;
		}
		$isSuccess = $this->_newDownLoadExcel($mClub, $startDay, $endDay, 4, $token);
		if(!$isSuccess){
			//return false;
		}
		return true;
	}
	
	private function _newDownLoadExcel($mClub, $startDay, $endDay, $gameType, $token){
		$downloadUrl = 'http://cms.pokermanager.club/cms-api/game/exportGameResultList?clubId=' . $mClub->club_id . '&startTime=' . strtotime($startDay) . '000&endTime=' . strtotime($endDay) . '000&gameName=&order=-1&gameType=' . $gameType . '&token=' . $token;
		//$returnString = file_get_contents($downloadUrl);
		$returnString = $this->_doHttpResponsePost($downloadUrl);
		$dir = Yii::getAlias('@p.import') . '/' . date('Ymd');
		if(!is_dir(Yii::getAlias('@p.resource') . '/' . $dir)){
			mkdir(Yii::getAlias('@p.resource') . '/' . $dir);
		}
		$fileName = $dir . '/' . $mClub->club_id . '_' . $startDay . '_' . $gameType . '.xls';
		$saveName = Yii::getAlias('@p.resource') . '/' . $fileName;
		file_put_contents($saveName, $returnString);
		//检查文件是否下载正常
		try{
			$aDataList = Yii::$app->excel->getSheetDataInArray($saveName);
			$mUser = User::findOne($mClub->user_id);
			$isSuccess = ImportData::importFromExcelDataList($mUser, $aDataList);
			$aDataList = null;
			if($isSuccess){
				return true;
			}else{
				return false;
			}
		}catch(\Exception $e){
			return false;
		}
	}
	
	private function _getDownLoadAndExecuteOneExcel($mClub, $startDay, $endDay){
		/*$mExcelFile = ExcelFile::findOne([
			'user_id' => $mClub->user_id,
			'club_id' => $mClub->club_id,
			'path' => $startDay,
		]);
		if($mExcelFile){
			return true;
		}*/
		$clubId = $mClub->club_id;
		$type = 1;
		//http://cms.pokermanager.club/cms/club/export?startTime=2017-09-25&endTime=2017-09-25&paramVo.type=1&sort=-4
		$exportUrl = $this->exportUrl . '?startTime=' . $startDay . '&endTime=' . $endDay . '&paramVo.type=' . $type . '&sort=-4';
		$returnString = $this->_doHttpResponsePost($exportUrl);
		if(!$returnString){
			return false;
		}
		$dir = Yii::getAlias('@p.import') . '/' . date('Ymd');
		if(!is_dir(Yii::getAlias('@p.resource') . '/' . $dir)){
			mkdir(Yii::getAlias('@p.resource') . '/' . $dir);
		}
		$fileName = $dir . '/' . $clubId . '_' . $startDay . '.xls';
		$saveName = Yii::getAlias('@p.resource') . '/' . $fileName;
		file_put_contents($saveName, $returnString);
		//检查文件是否下载正常
		try{
			$aDataList = Yii::$app->excel->getSheetDataInArray($saveName);
			$mUser = User::findOne($mClub->user_id);
			$isSuccess = ImportData::importFromExcelDataList($mUser, $aDataList);
			$aDataList = null;
			if($isSuccess){
				/*if(strtotime($startDay) < NOW_TIME){
					ExcelFile::addRecord([
						'user_id' => $mClub->user_id,
						'club_id' => $mClub->club_id,
						'path' => $startDay,
						'download_time' => NOW_TIME,
						'import_time' => NOW_TIME,
					]);
				}*/
				return true;
			}else{
				return false;
			}
		}catch(\Exception $e){
			return false;
		}
		return true;
	}
	
	private function _getModulusValueFromHtml($html){
		preg_match_all('/ng-init=\"modulus=\'\w+\'\"/', $html, $aMatchList);
		if(isset($aMatchList[0][0])){
			$aData = explode("'", $aMatchList[0][0]);
			if(isset($aData[1])){
				return $aData[1];
			}
		}
		return false;
	}
	
	private function _getExponentValueFromHtml($html){
		preg_match_all('/ng-init=\"exponent=\'\w+\'\"/', $html, $aMatchList);
		if(isset($aMatchList[0][0])){
			$aData = explode("'", $aMatchList[0][0]);
			if(isset($aData[1])){
				return $aData[1];
			}
		}
		return false;
	}
	
}