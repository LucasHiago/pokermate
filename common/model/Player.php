<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class Player extends \common\lib\DbOrmModel{
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@player');
	}
	
	public static function addRecord($aData){
		$id = static::insert($aData);
		$mPlayer = parent::findOne($id);
		if(!$mPlayer->keren_bianhao){
			$mPlayer->set('keren_bianhao', $id);
			$mPlayer->save();
		}
		$mKerenBenjin = KerenBenjin::findOne(['keren_bianhao' => $mPlayer->keren_bianhao]);
		if(!$mKerenBenjin){
			KerenBenjin::addRecord(['user_id' => $mPlayer->user_id, 'keren_bianhao' => $mPlayer->keren_bianhao]);
		}
		return $id;
	}
	
	public static function checkAddNewPlayer($userId, $aPlayerList){
		$aPlayerId = ArrayHelper::getColumn($aPlayerList, 'player_id');
		$aList = static::findAll(['user_id' => $userId, 'player_id' => $aPlayerId]);
		$aExistPlayerId = ArrayHelper::getColumn($aList, 'player_id');
		foreach($aPlayerList as $aPlayer){
			if(!in_array($aPlayer['player_id'], $aExistPlayerId)){
				static::addRecord([
					'user_id' => $userId,
					'player_id' => $aPlayer['player_id'],
					'player_name' => $aPlayer['player_name'],
					'create_time' => NOW_TIME,
				]);
			}
		}
	}
}