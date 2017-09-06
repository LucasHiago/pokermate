<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class FenchengSetting extends \common\lib\DbOrmModel{
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@fencheng_setting');
	}

}