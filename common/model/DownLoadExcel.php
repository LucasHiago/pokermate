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
			//$sendContent .= "Origin: http://cms.pokermanager.club/cms/\r\n";
			//$sendContent .= "Referer: http://cms.pokermanager.club/cms/\r\n";
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
		return $cookieString;
	}
	
	public function downSaveCode($savePathName){
		$this->path = $this->savecodePath;
		$returnString = $this->_sentRequest();
		if(!$returnString){
			return false;
		}
		$aReturn = explode("\r\n", $returnString);
		foreach($aReturn as $key => $value){
			$aValue = explode(' ', $value);
			if($aValue && $aValue[0] == 'Set-Cookie:'){
				if(!in_array($aValue[1], $this->aCookieList)){
					array_push($this->aCookieList, $aValue[1]);
				}
			}
			if($value == ''){
				file_put_contents(Yii::getAlias('@p.resource') . '/' . $savePathName, $aReturn[$key + 1]);
				return [
					'path' => $savePathName,
					'aCookie' => $this->aCookieList,
				];
			}
		}
		return false;
	}
	
}