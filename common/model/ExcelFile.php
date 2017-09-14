<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class ExcelFile extends \common\lib\DbOrmModel{

	public static function tableName(){
		return Yii::$app->db->parseTable('_@excel_file');
	}
	
	public static function addRecord($aData){
		$id = static::insert($aData);
		return $id;
	}
	
	public static function bathInsertData($aInsertList){
		(new Query())->createCommand()->batchInsert(static::tableName(), [
			'club_id', 
			'room_id',
			'type'
		], $aInsertList)->execute();
	}
	
}