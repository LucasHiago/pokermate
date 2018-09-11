<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class Lianmeng extends \common\lib\DbOrmModel{
	protected $_aEncodeFields = ['lmzj_paiju_creater' => 'json'];
	//对账方法（1：0.975 2：无水账单 3：0.985 4：0.95）
	const DUIZHANGFANGFA_LINDIANJIUQIWU = 1;
	const DUIZHANGFANGFA_WUSHUIDUIZHANG = 2;
	const DUIZHANGFANGFA_LINDIANJIUBAWU = 3;
	const DUIZHANGFANGFA_LINDIANJIUWU = 4;
	
	public static function getDuizhangfangfaList(){
		return [
			static::DUIZHANGFANGFA_LINDIANJIUQIWU => 0.975,
			static::DUIZHANGFANGFA_WUSHUIDUIZHANG => 1,
			static::DUIZHANGFANGFA_LINDIANJIUBAWU => 0.985,
			static::DUIZHANGFANGFA_LINDIANJIUWU => 0.95,
		];
	}
	
	public static function getDuizhangfangfaName($duizhangfangfa = 0){
		if($duizhangfangfa == static::DUIZHANGFANGFA_LINDIANJIUQIWU){
			return '0.975';
		}elseif($duizhangfangfa == static::DUIZHANGFANGFA_WUSHUIDUIZHANG){
			return '无水账单';
		}elseif($duizhangfangfa == static::DUIZHANGFANGFA_LINDIANJIUBAWU){
			return '0.985';
		}elseif($duizhangfangfa == static::DUIZHANGFANGFA_LINDIANJIUWU){
			return '0.95';
		}else{
			return '';
		}
	}
	
	public static function getDuizhangfangfaValue($duizhangfangfa = 0){
		$aDuizhangfangfaList = static::getDuizhangfangfaList();
		if($duizhangfangfa){
			return $aDuizhangfangfaList[$duizhangfangfa];
		}
		return static::DUIZHANGFANGFA_LINDIANJIUQIWU;
	}
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@lianmeng');
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
		$aLianmengClubList = LianmengClub::findAll(['user_id' => $this->user_id, 'lianmeng_id' => $this->id, 'is_delete' => 0]);
		foreach($aLianmengClubList as $key => $value){
			$aLianmengClubList[$key]['lianmeng_name'] = $this->name;
		}
		return $aLianmengClubList;
	}
		
	public function checkIsCanDelete(){
		//检查是否有未清账单
		$mUser = User::findOne($this->user_id);
		$aLianmengList = $mUser->getLianmengList();
		$aLianmengZhangDanDetailList = $mUser->getLianmengZhangDanDetailList($this->id);
		$totalZhangDan = 0;
		foreach($aLianmengZhangDanDetailList as $aLianmengZhangDanDetail){
			$totalZhangDan += $aLianmengZhangDanDetail['zhang_dan'];
		}
		if(!$totalZhangDan){
			return true;
		}
		return false;
	}
	
}