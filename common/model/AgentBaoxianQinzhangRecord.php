<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class AgentBaoxianQinzhangRecord extends \common\lib\DbOrmModel{
	protected $_aEncodeFields = ['import_data_id' => 'json'];
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@agent_baoxian_qinzhang_record');
	}
	
	public static function addRecord($aData){
		$id = static::insert($aData);
		return $id;
	}
	
	
	/**
	 *	获取列表
	 *	$aCondition = [
	 *		'id' =>
	 *		'user_id' =>
	 *		'agent_id' =>
	 *		'is_show' =>
	 *	]
	 *	$aControl = [
	 *		'select' =>
	 *		'order_by' =>
	 *		'page' =>
	 *		'page_size' =>
	 *		'width_agent_info' => true/false
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
		$aAgentList = [];
		$aAgentId = [];
		if(isset($aControl['width_agent_info']) && $aControl['width_agent_info']){
			$aAgentId = array_unique(ArrayHelper::getColumn($aList, 'agent_id'));
			$aAgentList = Agent::findAll(['id' => $aAgentId]);
		}
		foreach($aList as $key => $value){
			$aList[$key]['agent_info'] = [];
			foreach($aAgentList as $aAgent){
				if($aAgent['id'] == $value['agent_id']){
					$aList[$key]['agent_info'] = $aAgent;
					break;
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
		if(isset($aCondition['agent_id'])){
			$aWhere[] = ['agent_id' => $aCondition['agent_id']];
		}
		if(isset($aCondition['is_show'])){
			$aWhere[] = ['is_show' => $aCondition['is_show']];
		}
		return $aWhere;
	}
	
}