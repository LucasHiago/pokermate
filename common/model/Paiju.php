<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class Paiju extends \common\lib\DbOrmModel{
	
	const STATUS_UNDO = 0;	//未结算
	const STATUS_DONE = 1;	//已结算
	const STATUS_FINISH = 2;	//已交班
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@paiju');
	}
	
	public static function addRecord($aData){
		$id = static::insert($aData);
		return parent::findOne($id);
	}
	
	
	/**
	 *	获取列表
	 *	$aCondition = [
	 *		'id' =>
	 *		'user_id' =>
	 *		'status' =>
	 *		'lianmeng_id' =>
	 *		'is_clean' =>
	 *	]
	 *	$aControl = [
	 *		'select' =>
	 *		'order_by' =>
	 *		'page' =>
	 *		'page_size' =>
	 *		'width_hedui_shuzi' =>  
	 *	]
	 */
	public static function getPaijuList($aCondition = [], $aControl = []){
		$aWhere = static::_parsePaijuWhereCondition($aCondition);
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
		$aPaijuId = [];
		$aPaijuHeduishuziList = [];
		if(isset($aControl['width_hedui_shuzi']) && $aControl['width_hedui_shuzi']){
			$aPaijuId = ArrayHelper::getColumn($aList, 'id');
			$sql = 'SELECT `paiju_id`,SUM(`zhanji`) AS `sum_zhanji`,SUM(`baoxian_heji`) AS `sum_baoxian_heji` FROM ' . ImportData::tableName() . ' WHERE `paiju_id` IN(' . implode(',', $aPaijuId) . ') GROUP BY `paiju_id`';
			$aPaijuHeduishuziList = Yii::$app->db->createCommand($sql)->queryAll();
		}
		foreach($aList as $key => $value){
			$aList[$key]['hedui_shuzi'] = 0;
			foreach($aPaijuHeduishuziList as $aPaijuHeduishuzi){
				if($value['id'] == $aPaijuHeduishuzi['paiju_id']){
					$aList[$key]['hedui_shuzi'] = (int)($aPaijuHeduishuzi['sum_zhanji'] - $aPaijuHeduishuzi['sum_baoxian_heji']);
				}
			}
		}
		return $aList;
	}
	
	/**
	 *	获取数量
	 */
	public static function getPaijuCount($aCondition = []){
		$aWhere = static::_parsePaijuWhereCondition($aCondition);
		return (new Query())->from(static::tableName())->where($aWhere)->count();
	}
	
	private static function _parsePaijuWhereCondition($aCondition = []){
		$aWhere = ['and'];
		if(isset($aCondition['id'])){
			$aWhere[] = ['id' => $aCondition['id']];
		}
		if(isset($aCondition['user_id'])){
			$aWhere[] = ['user_id' => $aCondition['user_id']];
		}
		if(isset($aCondition['status'])){
			$aWhere[] = ['status' => $aCondition['status']];
		}
		if(isset($aCondition['lianmeng_id'])){
			$aWhere[] = ['lianmeng_id' => $aCondition['lianmeng_id']];
		}
		if(isset($aCondition['is_clean'])){
			$aWhere[] = ['is_clean' => $aCondition['is_clean']];
		}
		return $aWhere;
	}
	
	/**
	 *	获取列表
	 *	$aCondition = [
	 *		'id' =>
	 *		'user_id' =>
	 *		'club_id' =>
	 *		'status' =>
	 *		'lianmeng_id' =>
	 *		'start_time' =>
	 *		'end_time' =>
	 *	]
	 *	$aControl = [
	 *		'select' =>
	 *		'order_by' =>
	 *		'page' =>
	 *		'page_size' =>
	 *		'width_hedui_shuzi' =>  
	 *	]
	 */
	public static function getList($aCondition = [], $aControl = []){
		$aWhere = static::_parseWhereCondition($aCondition);
		$oQuery = new Query();
		if(isset($aControl['select'])){
			$oQuery->select($aControl['select']);
		}
		$oQuery->from(static::tableName() . ' AS `t1`');
		if(isset($aCondition['club_id']) && $aCondition['club_id']){
			$oQuery->leftJoin(ImportData::tableName() . ' AS `t2` ON `t1`.`id`=`t2`.`paiju_id`');
			$oQuery->groupBy('`t1`.`id`');
		}
		$oQuery->where($aWhere);
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
		$aPaijuId = [];
		$aPaijuHeduishuziList = [];
		if(isset($aControl['width_hedui_shuzi']) && $aControl['width_hedui_shuzi']){
			$aPaijuId = ArrayHelper::getColumn($aList, 'id');
			$sql = 'SELECT `paiju_id`,SUM(`zhanji`) AS `sum_zhanji`,SUM(`baoxian_heji`) AS `sum_baoxian_heji` FROM ' . ImportData::tableName() . ' WHERE `paiju_id` IN(' . implode(',', $aPaijuId) . ') GROUP BY `paiju_id`';
			$aPaijuHeduishuziList = Yii::$app->db->createCommand($sql)->queryAll();
		}
		foreach($aList as $key => $value){
			$aList[$key]['hedui_shuzi'] = 0;
			foreach($aPaijuHeduishuziList as $aPaijuHeduishuzi){
				if($value['id'] == $aPaijuHeduishuzi['paiju_id']){
					$aList[$key]['hedui_shuzi'] = (int)($aPaijuHeduishuzi['sum_zhanji'] - $aPaijuHeduishuzi['sum_baoxian_heji']);
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
		$oQuery = new Query();
		$oQuery->select('t1.*')->from(static::tableName() . ' AS `t1`');
		if(isset($aCondition['club_id']) && $aCondition['club_id']){
			$oQuery->leftJoin(ImportData::tableName() . ' AS `t2` ON `t1`.`id`=`t2`.`paiju_id`');
			$oQuery->groupBy('`t1`.`id`');
		}
		return $oQuery->where($aWhere)->count();
		//return (new Query())->from(static::tableName())->where($aWhere)->count();
	}
	
	private static function _parseWhereCondition($aCondition = []){
		$aWhere = ['and'];
		if(isset($aCondition['id'])){
			$aWhere[] = ['`t1`.`id`' => $aCondition['id']];
		}
		if(isset($aCondition['user_id'])){
			$aWhere[] = ['`t1`.`user_id`' => $aCondition['user_id']];
		}
		if(isset($aCondition['status'])){
			$aWhere[] = ['`t1`.`status`' => $aCondition['status']];
		}
		if(isset($aCondition['lianmeng_id'])){
			$aWhere[] = ['`t1`.`lianmeng_id`' => $aCondition['lianmeng_id']];
		}
		if(isset($aCondition['start_time'])){
			$aWhere[] = ['>', '`t1`.`create_time`', $aCondition['start_time']];
		}
		if(isset($aCondition['end_time'])){
			$aWhere[] = ['<', '`t1`.`create_time`', $aCondition['end_time']];
		}
		if(isset($aCondition['club_id']) && $aCondition['club_id']){
			$aWhere[] = ['`t2`.`club_id`' => $aCondition['club_id']];
		}
		return $aWhere;
	}
	
	public function getLianmengId(){
		return $this->lianmeng_id;
		/*$mImportData = ImportData::findOne(['paiju_id' => $this->id]);
		if($mImportData){
			return $mImportData->lianmeng_id;
		}
		return 0;*/
	}
	
	public function setLianmeng($lianmengId){
		$this->set('lianmeng_id', $lianmengId);
		$this->save();
		/*$sql = 'UPDATE ' . ImportData::tableName() . ' SET `lianmeng_id`=' . $lianmengId . ' WHERE `paiju_id`=' . $this->id . ' AND `user_id`=' . $this->user_id;
		$aResult = Yii::$app->db->createCommand($sql)->execute();*/
	}
	
}