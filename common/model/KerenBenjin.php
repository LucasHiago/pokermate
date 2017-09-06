<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class KerenBenjin extends \common\lib\DbOrmModel{
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@keren_benjin');
	}
	
	public static function addRecord($aData){
		$id = static::insert($aData);
		return $id;
	}
}