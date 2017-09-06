<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class PaiJu extends \common\lib\DbOrmModel{
	
	const STATUS_UNDO = 0;	//未结算
	const STATUS_DONE = 1;	//已结算
	const STATUS_FINISH = 2;	//已交班
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@paiju');
	}
	
	public static function addRecord($aData){
		$id = static::insert($aData);
		return parent::findOne($id);
	}
}