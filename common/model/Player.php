<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class Player extends \common\lib\DbOrmModel{
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@player');
	}
	
	public static function addRecord($aData, $isAutoCreate = 0){
		$id = static::insert($aData);
		$mPlayer = parent::findOne($id);
		if(!$mPlayer->keren_bianhao){
			$kerenBianhao = KerenBenjin::getNextKerenbianhao($mPlayer->user_id);
			$mPlayer->set('keren_bianhao', $kerenBianhao);
			$mPlayer->save();
		}
		$mKerenBenjin = KerenBenjin::findOne(['user_id' => $mPlayer->user_id, 'keren_bianhao' => $mPlayer->keren_bianhao]);
		if(!$mKerenBenjin){
			KerenBenjin::addRecord(['user_id' => $mPlayer->user_id, 'keren_bianhao' => $mPlayer->keren_bianhao, 'is_auto_create' => $isAutoCreate, 'create_time' => NOW_TIME]);
		}else{
			if($mKerenBenjin->is_delete){
				$mKerenBenjin->set('is_delete', 0);
				$mKerenBenjin->save();
			}
		}
		return $id;
	}
	
	public static function checkAddNewPlayer($userId, $aPlayerList, $isAutoCreate = 0){
		$aPlayerId = ArrayHelper::getColumn($aPlayerList, 'player_id');
		$aList = static::findAll(['user_id' => $userId, 'player_id' => $aPlayerId]);
		$aExistPlayerId = ArrayHelper::getColumn($aList, 'player_id');
		foreach($aPlayerList as $aPlayer){
			$mPlayer = static::findOne(['user_id' => $userId, 'player_id' => $aPlayer['player_id']]);
			if(!$mPlayer){
				static::addRecord([
					'user_id' => $userId,
					'player_id' => $aPlayer['player_id'],
					'player_name' => $aPlayer['player_name'],
					'create_time' => NOW_TIME,
				], $isAutoCreate);
			}
			/*if(!in_array($aPlayer['player_id'], $aExistPlayerId)){
				static::addRecord([
					'user_id' => $userId,
					'player_id' => $aPlayer['player_id'],
					'player_name' => $aPlayer['player_name'],
					'create_time' => NOW_TIME,
				]);
			}*/
		}
	}
	
	public function getMKerenBenjin(){
		return KerenBenjin::findOne(['user_id' => $this->user_id, 'keren_bianhao' => $this->keren_bianhao]);
	}
		
	/**
	 *	获取列表
	 *	$aCondition = [
	 *		'id' =>
	 *		'user_id' =>
	 *		'keren_bianhao' =>
	 *		'player_id' =>
	 *		'player_name' =>
	 *		'is_delete' =>
	 *	]
	 *	$aControl = [
	 *		'select' =>
	 *		'order_by' =>
	 *		'page' =>
	 *		'page_size' =>
	 *	]
	 */
	public static function getList($aCondition = [], $aControl = []){
		$aWhere = static::_parseWhereCondition($aCondition);
		$oQuery = new Query();
		if(isset($aControl['select'])){
			$oQuery->select($aControl['select']);
		}
		$oQuery->from(static::tableName())->where($aWhere);
		if(isset($aControl['order_by'])){
			$oQuery->orderBy($aControl['order_by']);
		}
		if(isset($aControl['page']) && isset($aControl['page_size'])){
			$offset = ($aControl['page'] - 1) * $aControl['page_size'];
			$oQuery->offset($offset)->limit($aControl['page_size']);
		}
		$aList = $oQuery->all();
		if(!$aList){
			return [];
		}
		
		$aKerenBenjinId = [];
		$aKerenBenjinList = [];
		if(isset($aControl['width_keren_benjin_info']) && $aControl['width_keren_benjin_info']){
			$aKerenBenjinId = array_unique(ArrayHelper::getColumn($aList, 'keren_bianhao'));
			$aKerenBenjinList = KerenBenjin::findAll(['keren_bianhao' => $aKerenBenjinId]);
		}
		
		foreach($aList as $key => $value){
			$aList[$key]['keren_benjin_info'] = [];
			foreach($aKerenBenjinList as $aKerenBenjin){
				if($value['keren_bianhao'] == $aKerenBenjin['keren_bianhao']){
					$aList[$key]['keren_benjin_info'] = $aKerenBenjin;
				}
			}
		}
		
		return $aList;
	}
	
	/**
	 *	获取数量
	 */
	public static function getCount($aCondition = []){
		$aWhere = static::_parseWhereCondition($aCondition);
		return (new Query())->from(static::tableName())->where($aWhere)->count();
	}
	
	private static function _parseWhereCondition($aCondition = []){
		$aWhere = ['and'];
		if(isset($aCondition['id'])){
			$aWhere[] = ['id' => $aCondition['id']];
		}
		if(isset($aCondition['user_id'])){
			$aWhere[] = ['user_id' => $aCondition['user_id']];
		}
		if(isset($aCondition['keren_bianhao'])){
			$aWhere[] = ['keren_bianhao' => $aCondition['keren_bianhao']];
		}
		if(isset($aCondition['player_id'])){
			$aWhere[] = ['player_id' => $aCondition['player_id']];
		}
		if(isset($aCondition['player_name'])){
			$aWhere[] = ['player_name' => $aCondition['player_name']];
		}
		if(isset($aCondition['is_delete'])){
			$aWhere[] = ['is_delete' => $aCondition['is_delete']];
		}
		return $aWhere;
	}
	
	public function getLastPaijuData($page = 1, $pageSize = 30){
		$offset = ($page - 1) * $pageSize;
		$sql = 'SELECT `paiju_name`,`mangzhu`,`player_name`,`zhanji`,`jiesuan_value` FROM ' . ImportData::tableName() . ' WHERE `player_id`=' . $this->player_id . ' ORDER BY `end_time` DESC LIMIT ' . $offset . ',' . $pageSize;
		
		return Yii::$app->db->createCommand($sql)->queryAll();
	}
	
	public static function searchPlayerByPlayerName($userId, $playerName, $page = 1, $pageSize = 10){
		$offset = ($page - 1) * $pageSize;
		$sql = 'SELECT `keren_bianhao`,`player_name` FROM ' . static::tableName() . ' WHERE `user_id`=' . $userId . ' AND `is_delete`=0 AND `player_name` LIKE "%' . $playerName . '%" LIMIT ' . $offset . ',' . $pageSize;
		
		return Yii::$app->db->createCommand($sql)->queryAll();
	}
	
}