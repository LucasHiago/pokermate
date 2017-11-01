<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class HostLianmeng extends \common\lib\DbOrmModel{
	//protected $_aEncodeFields = ['lmzj_paiju_creater' => 'json'];
	
	//对账方法（1：0.975 2：无水账单）
	const DUIZHANGFANGFA_LINDIANJIUQIWU = 1;
	const DUIZHANGFANGFA_WUSHUIDUIZHANG = 2;
	
	public static function getDuizhangfangfaList(){
		return [
			static::DUIZHANGFANGFA_LINDIANJIUQIWU => 0.975,
			static::DUIZHANGFANGFA_WUSHUIDUIZHANG => 1,
		];
	}
	
	public static function getDuizhangfangfaValue($duizhangfangfa = 0){
		$aDuizhangfangfaList = static::getDuizhangfangfaList();
		if($duizhangfangfa){
			return $aDuizhangfangfaList[$duizhangfangfa];
		}
		return static::DUIZHANGFANGFA_LINDIANJIUQIWU;
	}
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@host_lianmeng');
	}
	
	public static function addRecord($aData){
		if(!isset($aData['duizhangfangfa'])){
			$aData['duizhangfangfa'] = static::DUIZHANGFANGFA_LINDIANJIUQIWU;
		}
		if(!isset($aData['paiju_fee'])){
			$aData['paiju_fee'] = 0;
		}
		if(!isset($aData['baoxian_choucheng'])){
			$aData['baoxian_choucheng'] = 0;
		}
		$id = static::insert($aData);
		return $id;
	}
	
	public function getLianmengClubList(){
		$aLianmengClubList = HostLianmengClub::findAll(['user_id' => $this->user_id, 'lianmeng_id' => $this->id, 'is_delete' => 0]);
		foreach($aLianmengClubList as $key => $value){
			$aLianmengClubList[$key]['lianmeng_name'] = $this->name;
		}
		return $aLianmengClubList;
	}
		
	public function checkIsCanDelete(){
		return true;
		/*//检查是否有未清账单
		$mUser = User::findOne($this->user_id);
		$aLianmengList = $mUser->getHostLianmengList();
		$aLianmengZhangDanDetailList = $mUser->getLianmengZhangDanDetailList($this->id);
		$totalZhangDan = 0;
		foreach($aLianmengZhangDanDetailList as $aLianmengZhangDanDetail){
			$totalZhangDan += $aLianmengZhangDanDetail['zhang_dan'];
		}
		if(!$totalZhangDan){
			return true;
		}
		return false;*/
	}
	
}