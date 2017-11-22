<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class KerenBenjin extends \common\lib\DbOrmModel{
	const YING_CHOU_DEFAULT = 5;
	const SHU_FAN_DEFAULT = 0;
	const YING_FEE_DEFAULT = 0;
	
	public $_isUpdateKerenBianhao = false;
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@keren_benjin');
	}
	
	public static function getNextKerenbianhao($userId){
		$sql = 'SELECT MAX(`keren_bianhao`) AS `num` FROM ' . static::tableName() . ' WHERE `user_id`=' . $userId;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		$number = (int)($aResult[0]['num'] + 1);
		if($number < 10000){
			$number = 10000;
		}
		return $number;
	}
	
	public static function checkKerenbianhao($kerenbianhao){
		if($kerenbianhao > 0 && $kerenbianhao < 9999999999){
			return true;
		}
		return false;
	}
	
	public static function addRecord($aData){
		if(!isset($aData['ying_chou'])){
			$aData['ying_chou'] = static::YING_CHOU_DEFAULT;
		}
		if(!isset($aData['shu_fan'])){
			$aData['shu_fan'] = static::SHU_FAN_DEFAULT;
		}
		if(!isset($aData['ying_fee'])){
			$aData['ying_fee'] = static::YING_FEE_DEFAULT;
		}
		$id = static::insert($aData);
		return $id;
	}
	
	public function set($field, $value){
		if($field == 'keren_bianhao' && $this->keren_bianhao != $value){
			$this->_isUpdateKerenBianhao = true;
		}
		$this->_aSetFields[$field] = $value;
		if(is_array($value) && isset($value[0]) && ($value[0] === 'add' || $value[0] === 'sub')){
			if($value[0] === 'add'){
				$this->$field += $value[1];
			}else{
				$this->$field -= $value[1];
			}
		}else{
			$this->$field = $value;
		}
	}
	
	public function save(){
		if($this->is_auto_create && $this->_isUpdateKerenBianhao){
			$this->set('is_auto_create', 0);
		}
		if($this->_isUpdateKerenBianhao){
			$this->_isUpdateKerenBianhao = false;
		}
		return parent::save();
	}
	
	public function getPlayerList(){
		return $aPlayerList = Player::findAll(['user_id' => $this->user_id, 'keren_bianhao' => $this->keren_bianhao]);
	}
	
	private static function _parseWhereCondition1($aCondition){
		$where = '';
		if(isset($aCondition['`k1`.`user_id`'])){
			$where .= ' AND `k1`.`user_id`=' . $aCondition['`k1`.`user_id`'];
		}
		if(isset($aCondition['`k1`.`keren_bianhao`'])){
			$where .= ' AND `k1`.`keren_bianhao`=' . $aCondition['`k1`.`keren_bianhao`'];
		}
		if(isset($aCondition['`k1`.`is_delete`'])){
			$where .= ' AND `k1`.`is_delete`=' . $aCondition['`k1`.`is_delete`'];
		}
		/*if(isset($aCondition['`k3`.`player_id`'])){
			$where .= ' AND `k3`.`player_id`=' . $aCondition['`k3`.`player_id`'];
		}
		if(isset($aCondition['`k3`.`player_name`'])){
			$where .= ' AND `k3`.`player_name`=' . $aCondition['`k3`.`player_name`'];
		}*/
		return $where;
	}
	
	/**
	 *	获取列表	列出用户添加的俱乐部下的客人和自己导入的客人
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
	 *		'with_agent_info' =>
	 *	]
	 */
	public static function getList1($aCondition = [], $aControl = []){
		$where = static::_parseWhereCondition1($aCondition);
		/*$select = '`k1`.*';
		if(isset($aControl['select'])){
			$select = $aControl['select'];
		}*/
		$order = '';
		if(isset($aControl['order_by'])){
			$order = 'ORDER BY ' . $aControl['order_by'];
		}
		$limit = '';
		if(isset($aControl['page']) && isset($aControl['page_size'])){
			$offset = ($aControl['page'] - 1) * $aControl['page_size'];
			$limit = ' LIMIT ' . $offset . ',' . $aControl['page_size'];
		}
		
		$sql = 'SELECT DISTINCT(`k1`.`id`),`k1`.`user_id`,`k1`.`keren_bianhao`,`k1`.`benjin`,`k1`.`ying_chou`,`k1`.`shu_fan`,`k1`.`ying_fee`,`k1`.`shu_fee`,`k1`.`agent_id`,`k1`.`remark`,`k1`.`current_player_id`,`k1`.`is_auto_create`,`k1`.`is_delete`,`k1`.`create_time` FROM ' . KerenBenjin::tableName() . ' AS `k1` RIGHT JOIN (SELECT `k2`.*,`k3`.`keren_bianhao` FROM ((SELECT `player_id` FROM ' . ImportData::tableName() . ' WHERE `user_id`=' . $aCondition['`k1`.`user_id`'] . ' AND `club_id` IN(' . implode(',', $aCondition['club_id']) . ') GROUP BY `player_id`) AS `k2` LEFT JOIN ' . Player::tableName() . ' AS `k3` ON `k2`.`player_id`=`k3`.`player_id`) WHERE `k3`.`user_id`=' . $aCondition['`k1`.`user_id`'] . ') AS `k4` ON `k1`.`keren_bianhao`=`k4`.`keren_bianhao` WHERE 1=1 ' . $where . ' ' . $order . ' ' . $limit;
		
		$aList = Yii::$app->db->createCommand($sql)->queryAll();
		if(!$aList){
			return [];
		}
		
		$aPlayerList = [];
		if(isset($aControl['with_player_list']) && $aControl['with_player_list']){
			$aKerenBianhao = ArrayHelper::getColumn($aList, 'keren_bianhao');
			$aPlayerList = Player::findAll(['user_id' => $aCondition['`k1`.`user_id`'], 'keren_bianhao' => $aKerenBianhao, 'is_delete' => 0]);
		}
		
		$aAgentList = [];
		if(isset($aControl['with_agent_info']) && $aControl['with_agent_info']){
			$aAgentId = ArrayHelper::getColumn($aList, 'agent_id');
			$aAgentList = Agent::findAll(['id' => $aAgentId]);
		}
		
		foreach($aList as $key => $value){
			$aList[$key]['player_list'] = [];
			$aList[$key]['agent_info'] = [];
			$aList[$key]['ying_chou'] =floatval($value['ying_chou']);
			$aList[$key]['shu_fan'] = floatval($value['shu_fan']);
			//将当前玩家放在数组第一个start
			$aCurrentPlayer = [];
			foreach($aPlayerList as $aPlayer){
				if($value['keren_bianhao'] == $aPlayer['keren_bianhao']){
					if($aPlayer['id'] == $value['current_player_id']){
						$aCurrentPlayer = $aPlayer;
					}else{
						array_push($aList[$key]['player_list'], $aPlayer);
					}
				}
			}
			ArrayHelper::multisort($aList[$key]['player_list'], ['id'], [SORT_DESC]);
			if($aCurrentPlayer){
				$aTempPlayerList = [$aCurrentPlayer];
				foreach($aList[$key]['player_list'] as $vv){
					array_push($aTempPlayerList, $vv);
				}
				$aList[$key]['player_list'] = $aTempPlayerList;
			}
			//将当前玩家放在数组第一个end
			foreach($aAgentList as $aAgent){
				if($value['agent_id'] == $aAgent['id']){
					$aList[$key]['agent_info'] = $aAgent;
				}
			}
		}
		
		return $aList;
	}
	
	public static function getCount1($aCondition = []){
		$where = static::_parseWhereCondition1($aCondition);
		$sql = 'SELECT COUNT(DISTINCT(`k1`.`id`)) AS `num` FROM ' . KerenBenjin::tableName() . ' AS `k1` RIGHT JOIN (SELECT `k2`.*,`k3`.`keren_bianhao` FROM ((SELECT DISTINCT(`player_id`) FROM ' . ImportData::tableName() . ' WHERE `user_id`=' . $aCondition['`k1`.`user_id`'] . ' AND `club_id` IN(' . implode(',', $aCondition['club_id']) . ')) AS `k2` LEFT JOIN ' . Player::tableName() . ' AS `k3` ON `k2`.`player_id`=`k3`.`player_id`)) AS `k4` ON `k1`.`keren_bianhao`=`k4`.`keren_bianhao` WHERE 1=1 ' . $where;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return (int)$aResult[0]['num'];
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
	 *		'with_agent_info' =>
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
			$aPlayerList = Player::findAll(['keren_bianhao' => $aKerenBianhao, 'is_delete' => 0]);
		}
		
		$aAgentList = [];
		if(isset($aControl['with_agent_info']) && $aControl['with_agent_info']){
			$aAgentId = ArrayHelper::getColumn($aList, 'agent_id');
			$aAgentList = Agent::findAll(['id' => $aAgentId]);
		}
		
		foreach($aList as $key => $value){
			$aList[$key]['player_list'] = [];
			$aList[$key]['agent_info'] = [];
			$aList[$key]['ying_chou'] =floatval($value['ying_chou']);
			$aList[$key]['shu_fan'] = floatval($value['shu_fan']);
			foreach($aPlayerList as $aPlayer){
				if($value['keren_bianhao'] == $aPlayer['keren_bianhao']){
					array_push($aList[$key]['player_list'], $aPlayer);
				}
			}
			foreach($aAgentList as $aAgent){
				if($value['agent_id'] == $aAgent['id']){
					$aList[$key]['agent_info'] = $aAgent;
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
		
	public function delete($isHide = true){
		$this->set('benjin', 0);
		$this->set('ying_chou', 0);
		$this->set('shu_fan', 0);
		$this->set('agent_id', 0);
		$this->set('remark', '');
		$this->set('current_player_id', 0);
		$this->set('is_auto_create', 0);
		if($isHide || $this->is_auto_create){
			$this->set('is_delete', 1);
		}
		$this->save();
		
		$sql = 'UPDATE ' . Player::tableName() . ' SET `is_delete`=1,`keren_bianhao`=0 WHERE `user_id`=' . $this->user_id . ' AND `keren_bianhao`=' . $this->keren_bianhao;
		Yii::$app->db->createCommand($sql)->execute();
		if(!$isHide){
			Player::addRecord([
				'user_id' => $this->user_id,
				'keren_bianhao' => $this->keren_bianhao,
				'player_id' => 0,
				'player_name' => '',
				'create_time' => NOW_TIME,
			]);
			ImportData::addEmptyDataRecord($this->user_id, 0, '');
		}
	}
		
	public function modifyKerenBianhao($kerenBianhao){
		$sql = 'UPDATE ' . Player::tableName() . ' SET `keren_bianhao`=' . $kerenBianhao . ' WHERE `user_id`=' . $this->user_id . ' AND `keren_bianhao`=' . $this->keren_bianhao;
		Yii::$app->db->createCommand($sql)->execute();
		$this->set('keren_bianhao', $kerenBianhao);
		$this->save();
	}
	
	public function getLastPaijuData($page = 1, $pageSize = 30){
		$aPlayerList = $this->getPlayerList();
		if(!$aPlayerList){
			return [];
		}
		$aPlayerId = ArrayHelper::getColumn($aPlayerList, 'player_id');;
		$offset = ($page - 1) * $pageSize;
		$sql = 'SELECT `paiju_name`,`mangzhu`,`player_name`,`zhanji`,`jiesuan_value` FROM ' . ImportData::tableName() . ' WHERE `player_id` IN(' . implode(',', $aPlayerId) . ') AND `paiju_id`>0 ORDER BY `end_time` DESC LIMIT ' . $offset . ',' . $pageSize;
		
		return Yii::$app->db->createCommand($sql)->queryAll();
	}
}