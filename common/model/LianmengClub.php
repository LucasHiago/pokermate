<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class LianmengClub extends \common\lib\DbOrmModel{

	public static function tableName(){
		return Yii::$app->db->parseTable('_@lianmeng_club');
	}
	
	public static function addRecord($aData){
		if(!isset($aData['duizhangfangfa'])){
			$aData['duizhangfangfa'] = Lianmeng::DUIZHANGFANGFA_LINDIANJIUQIWU;
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
	
}