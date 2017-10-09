<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class DownLoadExcel extends \yii\base\Object{
	public $savecodeUrl;
	public $loginPageUrl;
	public $loginUrl;
	public $selectClubUrl;
	public $historyExportUrl;
	public $exportRoomUrl;
	public $exportUrl;
	
	private $_cookieFile = '';
	private $_message = '';
	
	public function init(){
		parent::init();
		$this->_cookieFile = Yii::getAlias('@p.resource') . '/data/temp/cookie.tmp';
	}
	
	public function getMessage(){
		return $this->_message;
	}
	
	private function _doHttpResponsePost($url, $aParam = []){
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
		curl_setopt($curl, CURLOPT_POST, true);	 // post传输数据
		curl_setopt($curl, CURLOPT_POSTFIELDS, $para);	// post传输数据
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
			if(!$returnString){echo 1;exit;
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
		if(!$isSuccess){echo 2;exit;
			return false;
		}
		return true;
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
		if(!$returnString){echo 3;exit;
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
				if(strtotime($startDay) < NOW_TIME){
					ExcelFile::addRecord([
						'user_id' => $mClub->user_id,
						'club_id' => $mClub->club_id,
						'path' => $startDay,
						'download_time' => NOW_TIME,
						'import_time' => NOW_TIME,
					]);
				}
				return true;
			}else{echo 4;exit;
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