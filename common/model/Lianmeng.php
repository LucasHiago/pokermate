<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class Lianmeng extends \common\lib\DbOrmModel{
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@lianmeng');
	}
	
	public static function addRecord($aData){
		$id = static::insert($aData);
		return $id;
	}
}