<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class KerenBenjin extends \common\lib\DbOrmModel{
	const YING_CHOU_DEFAULT = 5;
	const SHU_FAN_DEFAULT = 0;
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@keren_benjin');
	}
	
	public static function addRecord($aData){
		if(!isset($aData['ying_chou'])){
			$aData['ying_chou'] = static::YING_CHOU_DEFAULT;
		}
		if(!isset($aData['shu_fan'])){
			$aData['shu_fan'] = static::SHU_FAN_DEFAULT;
		}
		$id = static::insert($aData);
		return $id;
	}
	
	/**
	 *	获取列表
	 *	$aCondition = [
	 *		'id' =>
	 *		'user_id' =>
	 *		'keren_bianhao' =>
	 *		'agent_id' => 
	 *		'is_delete' => 
	 *		'start_time' =>
	 *		'end_time' =>
	 *	]
	 *	$aControl = [
	 *		'select' =>
	 *		'order_by' =>
	 *		'page' =>
	 *		'page_size' =>
	 *		'with_player_list' =>
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
		
		$aPlayerList = [];
		if(isset($aControl['with_player_list']) && $aControl['with_player_list']){
			$aKerenBianhao = ArrayHelper::getColumn($aList, 'keren_bianhao');
			$aPlayerList = Player::findAll(['keren_bianhao' => $aKerenBianhao]);
		}
		
		foreach($aList as $key => $value){
			$aList[$key]['player_list'] = [];
			$aList[$key]['ying_chou'] =floatval($value['ying_chou']);
			$aList[$key]['shu_fan'] = floatval($value['shu_fan']);
			foreach($aPlayerList as $aPlayer){
				if($value['keren_bianhao'] == $aPlayer['keren_bianhao']){
					array_push($aList[$key]['player_list'], $aPlayer);
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
		if(isset($aCondition['agent_id'])){
			$aWhere[] = ['agent_id' => $aCondition['agent_id']];
		}
		if(isset($aCondition['is_delete'])){
			$aWhere[] = ['is_delete' => $aCondition['is_delete']];
		}
		if(isset($aCondition['start_time'])){
			$aWhere[] = ['>', 'create_time', $aCondition['start_time']];
		}
		if(isset($aCondition['end_time'])){
			$aWhere[] = ['<', 'create_time', $aCondition['end_time']];
		}
		return $aWhere;
	}
		
	public function checkIsCanDelete(){
		$aPlayerList = Player::findAll(['user_id' => $this->user_id, 'keren_bianhao' => $this->keren_bianhao]);
		if(!$aPlayerList){
			return true;
		}
		$aPlayerId = ArrayHelper::getColumn($aPlayerList, 'player_id');
		//检查是否有未交班的数据
		$mUser = User::findOne($this->user_id);
		$sql = 'SELECT `t1`.* FROM `paiju` `t1` LEFT JOIN ' . ImportData::tableName() . ' AS `t2` ON `t1`.`id`=`t2`.`paiju_id` WHERE (`t1`.`user_id`=' . $mUser->id . ') AND (`t1`.`status` IN (' . Paiju::STATUS_UNDO . ', ' . Paiju::STATUS_DONE . ')) AND (`t2`.`player_id` IN (' . implode(',', $aPlayerId) . ')) GROUP BY `t1`.`id`';
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		if(!$aResult){
			return true;
		}
		return false;
	}
		
	public function delete(){
		$this->set('is_delete', 1);
		$this->save();
		
		$sql = 'UPDATE ' . Player::tableName() . ' SET `is_delete`=1 WHERE `user_id`=' . $this->user_id . ' AND `keren_bianhao`=' . $this->keren_bianhao;
		Yii::$app->db->createCommand($sql)->execute();
	}
	
}