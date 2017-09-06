<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class MoneyOutPutType extends \common\lib\DbOrmModel{
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@money_out_put_type');
	}
	
	public static function addRecord($aData){
		return static::insert($aData);
	}
}