<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class DownLoadExcel extends \yii\base\Object{
	public $savecodeUrl;
	public $loginUrl;
	public $selectClubUrl;
	public $historyExportUrl;
	public $exportRoomUrl;
	
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
	
	public function downSaveCode($clubId){
		$this->_cookieFile = Yii::getAlias('@p.resource') . '/data/temp/cookie_' . $clubId . '.tmp';
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
		$clubId = $mClub->club_id;
		$this->_cookieFile = Yii::getAlias('@p.resource') . '/data/temp/cookie_' . $clubId . '.tmp';
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
		$type = 1;
		//$startTime = '2017-09-14';
		$startTime = $startDay;
		/*if($mClub->last_import_date){
			$startTime = $mClub->last_import_date;
		}*/
		$isComplete = false;
		while(true){
			if($startTime == $endDay){
				$isComplete = true;
			}
			$endTime = date('Y-m-d', strtotime($startTime . ' +1 day'));
			$isSuccess = $this->_checkAndDownloadExcel($mClub, $type, $startTime, $endTime);
			if(!$isSuccess){
				return false;
			}
			unset($isSuccess);
			if(!$mClub->last_import_date || $startTime == $mClub->last_import_date){
				$mClub->set('last_import_date', $endTime);
				$mClub->save();
			}
			$startTime = $endTime;
			if($isComplete){
				break;
			}
		}
		
		return true;
	}
	
	private function _checkAndDownloadExcel($mClub, $type, $startTime, $endTime){
		$aRoomIdList = [];
		$page = 1;
		$totalPage = 999999999;
		while(true){
			//战绩导出页面请求
			$aParam = [
				'startTime' => $startTime,
				'endTime' => $endTime,
				'paramVo.type' => $type,
				'sort' => -4,
				'paramVo.pageNumber' => $page,
			];
			$historyExportUrl = $this->historyExportUrl . '?startTime=' . $startTime . '&endTime=' . $endTime . '&paramVo.type=' . $type . '&sort=-4&paramVo.pageNumber=' . $page;
			$returnString = $this->_doHttpResponsePost($historyExportUrl);
			if(!$returnString){
				return false;
			}
			//file_put_contents(Yii::getAlias('@p.resource') . '/' . Yii::getAlias('@p.temp_upload') . '/history_export_' . $clubId . '.html', $responseText);
			//分析出所有房间号
			$aRoomId = $this->_getRoomIdFromHtml($returnString);
			Yii::info('request success:' . $historyExportUrl);
			Yii::info('$aRoomIdList:' . json_encode($aRoomId));
			if(!$aRoomId){
				break;
			}
			$aRoomIdList = array_unique(array_merge($aRoomIdList, $aRoomId));
			unset($aRoomId);
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
			unset($aExcelFileList);
		}
		unset($aRoomIdList);
		//找出已保存未下载的记录
		$aUnDownloadExcelFileList = ExcelFile::findAll(['user_id' => $mClub->user_id, 'club_id' => $mClub->club_id]);
		//下载Excel文件
		$isSuccess = $this->_downLoadExcelFile($mClub->club_id, $aUnDownloadExcelFileList);
		unset($aUnDownloadExcelFileList);
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
			$exportRoomUrl = $this->exportRoomUrl . '?paramVo.type=' . $mExcelFile->type . '&roomId=' . $roomId;
			$returnString = $this->_doHttpResponsePost($exportRoomUrl);
			if(!$returnString){
				return false;
			}
			$dir = Yii::getAlias('@p.import') . '/' . date('Ymd');
			if(!is_dir(Yii::getAlias('@p.resource') . '/' . $dir)){
				mkdir(Yii::getAlias('@p.resource') . '/' . $dir);
			}
			$fileName = $dir . '/' . $clubId . '_' . $roomId . '.xls';
			$saveName = Yii::getAlias('@p.resource') . '/' . $fileName;
			file_put_contents($saveName, $returnString);
			//检查文件是否下载正常
			try{
				$aDataList = Yii::$app->excel->getSheetDataInArray($saveName);
				unset($aDataList);
			}catch(\Exception $e){
				array_push($aDownUnSuccessExcelFile, $aUnDownloadExcelFile);
				continue;
			}
			//$mExcelFile->set('type', $type);
			$mExcelFile->set('path', $fileName);
			$mExcelFile->set('download_time', NOW_TIME);
			$mExcelFile->save();
		}
		unset($aUnDownloadExcelFileList);
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