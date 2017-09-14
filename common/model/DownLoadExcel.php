<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class DownLoadExcel extends \yii\base\Object{
	public $host;
	public $port;
	public $path;
	public $savecodePath;
	public $loginPath;
	public $selectClubPath;
	public $historyExportPath;
	public $exportRoomPath;
	public $aCookieList = [];
	
	private function _sentRequest($content = '', $isAjax = false){
		try{
			$sendContent = "POST " . $this->path . " HTTP/1.0\r\n";
			$sendContent .= "Host: " . $this->host . "\r\n";
			$sendContent .= "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36\r\n";
			$sendContent .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8\r\n";
			$sendContent .= "Accept-encoding: gzip, deflate\r\n";
			$sendContent .= "Accept-language: zh-CN,zh;q=0.8,en;q=0.6,fr;q=0.4\r\n";
			$sendContent .= "Connection: keep-alive\r\n";
			$sendContent .= "Cache-Control: no-cache\r\n";
			$sendContent .= "Pragma: no-cache\r\n";
			$sendContent .= "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\r\n";
			if($this->aCookieList){
				//$sendContent .= "Cookie: JSESSIONID=" . $JSESSIONID . "; acw_tc=" . $acw_tc . "\r\n";
				$sendContent .= "Cookie: " . $this->_getCookieString($this->aCookieList) . "\r\n";
			}
			if($isAjax){
				$sendContent .= "X-Requested-With: XMLHttpRequest\r\n";
			}
			$sendContent .= "Content-Length: " . strlen($content) . "\r\n\r\n";
			$sendContent .= $content;
			
			$returnString = ''; $errno = 0; $errstr = '';
			$fp = fsockopen($this->host, $this->port, $errno, $errstr, 1);
			if(!$fp){
				Yii::info('fsockopen: ' . $errstr);
				return false;
				//throw Yii::$app->buildError($errstr);
			}else{
				fputs($fp, $sendContent);
				while(!feof($fp)){
					$returnString .= fgets($fp, 4096);
				}
				fclose($fp);
			}
		}catch(\Exception $e){
			Yii::info('sentRequest error: ' . $e->getMessage());
			return false;
		}
		return $returnString;
	}
	
	private function _getCookieString(){
		$cookieString = '';
		foreach($this->aCookieList as $v){
			$cookieString .= $v . ' ';
		}
		$cookieString = rtrim($cookieString);
		$cookieString = rtrim($cookieString, ';');
		
		return $cookieString;
	}
	
	private function _getResponseText($returnString){
		if($returnString === false){
			return false;
		}
		$aReturn = explode("\r\n", $returnString);
		if($aReturn){
			$aHttpStatus = explode(' ', $aReturn[0]);
			if(!isset($aHttpStatus[1]) || $aHttpStatus[1] != 200){
				return false;
			}
		}
		foreach($aReturn as $key => $value){
			$aValue = explode(' ', $value);
			if($aValue && $aValue[0] == 'Set-Cookie:'){
				if(!in_array($aValue[1], $this->aCookieList)){
					array_push($this->aCookieList, $aValue[1]);
				}
			}
			if($value == ''){
				return $aReturn[$key + 1];
			}
		}
		return false;
	}
	
	public function downSaveCode($savePathName){
		$this->path = $this->savecodePath;
		$returnString = $this->_sentRequest();
		if(!$returnString){
			return false;
		}
		$responseText = $this->_getResponseText($returnString);
		if($responseText){
			file_put_contents(Yii::getAlias('@p.resource') . '/' . $savePathName, $responseText);
			return [
				'path' => $savePathName,
				'aCookie' => $this->aCookieList,
			];
		}
		return false;
	}
	
	public function getDownloadExcelUrl($mClub, $skey, $safecode, $aCookie, $retry){
		set_time_limit(0);
		$clubId = $mClub->club_id;
		$aParam = ['key' => $skey, 'safecode' => $safecode];
		$this->aCookieList = $aCookie;
		if(!$retry){
			//登录请求
			$this->path = $this->loginPath;
			$returnString = $this->_sentRequest(http_build_query($aParam), true);
			if(!$returnString){
				return false;
			}
			$isLoginSuccess = false;
			$responseText = $this->_getResponseText($returnString);
			if($responseText === "0"){
				$isLoginSuccess = true;
			}
			if(!$isLoginSuccess){
				return false;
			}
			//选择俱乐部页面请求
			$this->path = $this->selectClubPath . $clubId;
			$returnString = $this->_sentRequest('');
			if(!$returnString){
				return false;
			}
			$responseText = $this->_getResponseText($returnString);
			file_put_contents(Yii::getAlias('@p.resource') . '/' . Yii::getAlias('@p.temp_upload') . '/select_club_' . $clubId . '.html', $responseText);
		}
		//////////////////////////////楼上的代码都不干正事的2333////////////////////////////////////////////
		$type = 1;
		$startTime = '2017-09-12';
		if($mClub->last_import_date){
			$startTime = $mClub->last_import_date;
		}
		while(true){
			if($startTime == date('Y-m-d')){
				break;
			}
			$endTime = date('Y-m-d', strtotime($startTime . ' +1 day'));
			$isSuccess = $this->_checkAndDownloadExcel($mClub, $type, $startTime, $endTime);
			if(!$isSuccess){
				return false;
			}
			$mClub->set('last_import_date', $endTime);
			$mClub->save();
			$startTime = $endTime;
		}
		
		return true;
	}
	
	private function _checkAndDownloadExcel($mClub, $type, $startTime, $endTime){
		$aRoomIdList = [];
		$page = 1;
		$totalPage = 999999999;
		while(true){
			//战绩导出页面请求
			$this->path = $this->historyExportPath . '?startTime=' . $startTime . '&endTime=' . $endTime . '&paramVo.type=' . $type . '&sort=-4&paramVo.pageNumber=' . $page;
			$returnString = $this->_sentRequest('');
			$responseText = $this->_getResponseText($returnString);
			if(!$responseText){
				return false;
			}
			//file_put_contents(Yii::getAlias('@p.resource') . '/' . Yii::getAlias('@p.temp_upload') . '/history_export_' . $clubId . '.html', $responseText);
			//分析出所有房间号
			$aRoomId = $this->_getRoomIdFromHtml($responseText);
			Yii::info('request success:' . $this->path);
			Yii::info('$aRoomIdList:' . json_encode($aRoomId));
			if(!$aRoomId){
				break;
			}
			$aRoomIdList = array_unique(array_merge($aRoomIdList, $aRoomId));
			$page = $page + 1;
		}
		if($aRoomIdList){
			//先把分析出来的房间保存起来先
			$aExcelFileList = ExcelFile::findAll(['user_id' => $mClub->user_id, 'type' => $type, 'club_id' => $mClub->club_id, 'room_id' => $aRoomIdList]);
			foreach($aRoomIdList as $roomId){
				$isFind = false;
				foreach($aExcelFileList as $aExcelFile){
					if($aExcelFile['room_id'] == $roomId){
						$isFind = true;
						break;
					}
				}
				if(!$isFind){
					ExcelFile::addRecord([
						'user_id' => $mClub->user_id,
						'club_id' => $mClub->club_id,
						'room_id' => $roomId,
						'type' => $type,
					]);
				}
			}
		}
		//找出已保存未下载的记录
		$aUnDownloadExcelFileList = ExcelFile::findAll(['user_id' => $mClub->user_id, 'club_id' => $mClub->club_id]);
		//下载Excel文件
		$isSuccess = $this->_downLoadExcelFile($mClub->club_id, $aUnDownloadExcelFileList);
		if(is_array($isSuccess) && $isSuccess){
			//重新下载一次出错的文件
			$this->_downLoadExcelFile($mClub->club_id, $isSuccess);
		}
		return $isSuccess ? true : false;
	}
	
	private function _downLoadExcelFile($clubId, $aUnDownloadExcelFileList){
		$aDownUnSuccessExcelFile = [];
		foreach($aUnDownloadExcelFileList as $aUnDownloadExcelFile){
			$mExcelFile = ExcelFile::toModel($aUnDownloadExcelFile);
			$roomId = $mExcelFile->room_id;
			$this->path = $this->exportRoomPath . '?paramVo.type=' . $mExcelFile->type . '&roomId=' . $roomId;
			$returnString = $this->_sentRequest('');
			$responseText = $this->_getResponseText($returnString);
			if(!$responseText){
				return false;
			}
			$fileName = Yii::getAlias('@p.import') . '/' . $clubId . '_' . $roomId . '.xls';
			$saveName = Yii::getAlias('@p.resource') . '/' . $fileName;
			file_put_contents($saveName, $responseText);
			//检查文件是否下载正常
			try{
				$aDataList = Yii::$app->excel->getSheetDataInArray($saveName);
			}catch(\Exception $e){
				array_push($aDownUnSuccessExcelFile, $aUnDownloadExcelFile);
				continue;
			}
			//$mExcelFile->set('type', $type);
			$mExcelFile->set('path', $fileName);
			$mExcelFile->set('download_time', NOW_TIME);
			$mExcelFile->save();
		}
		if($aDownUnSuccessExcelFile){
			return $aDownUnSuccessExcelFile;
		}
		return true;
	}
	
	private function _getRoomIdFromHtml($html){
		preg_match_all('/exportExcel\(\d+\)/', $html, $aMatchList);
		$aRoomId = [];
		foreach($aMatchList[0] as $match){
			$roomId = '';
			if(strpos($match, 'exportExcel(') === false){
				continue;
			}
			$roomId = ltrim($match, 'exportExcel(');
			$roomId = (int)rtrim($roomId, ')');
			if($roomId){
				array_push($aRoomId, $roomId);
			}
		}
		return array_unique($aRoomId);
	}
}