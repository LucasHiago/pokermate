<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class FenchengSetting extends \common\lib\DbOrmModel{
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@fencheng_setting');
	}

	public static function getFenchengConfigList(){
		return ['1/2', '2/4', '5/10', '10/20', '20/40', '25/50', '50/100', '100/200', '200/400', '300/600', '1000/2000'];
	}
	
	public static function bathInsertData($aInsertList){
		(new Query())->createCommand()->batchInsert(static::tableName(), [
			'user_id', 
			'zhuozi_jibie',
			'yingfan',
			'shufan'
		], $aInsertList)->execute();
	}
	
	public static function oneKeySaveSetting($userId, $type, $value){
		$sql = 'UPDATE ' . static::tableName() . ' SET `' . $type . '`=' . $value . ' WHERE `user_id`=' . $userId;
		Yii::$app->db->createCommand($sql)->execute();
	}
	
}