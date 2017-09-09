<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class ImportData extends \common\lib\DbOrmModel{
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@import_data');
	}
	
	public static function addRecord($aData){
		return static::insert($aData);
	}
	
	public static function bathInsertData($aInsertList){
		(new Query())->createCommand()->batchInsert(static::tableName(), [
			'paiju_type', 
			'paiju_name',
			'paiju_creater',
			'mangzhu',
			'paizuo',
			'paiju_duration',
			'zongshoushu',
			'player_id',
			'player_name',
			'club_id',
			'club_name',
			'mairu',
			'daicu',
			'baoxian_mairu',
			'baoxian_shouru',
			'baoxian_heji',
			'club_baoxian',
			'baoxian',
			'zhanji',
			'end_time_format',
			'end_time',
			'create_time',
			'paiju_id',
			'user_id'
		], $aInsertList)->execute();
	}
	
	public static function importFromExcelDataList($aDataList){
		debug(Paiju::findAll(),11);
		if(!$aDataList){
			return false;
		}
		//去掉表头
		unset($aDataList[0]);
		$aInserDataList = [];
		$aUniquePaijuList = [];
		$aPlayerList = [];
		foreach($aDataList as $aData){
			 $aData[1] = trim($aData[1]);
			//结束时间转为时间戳
			$endTime = strtotime($aData[19]);
			array_push($aData, $endTime);
			array_push($aData, NOW_TIME);
			//总手数为0为无效牌局（不计算桌子费）
			if($aData[6]){
				array_push($aPlayerList, [
					'player_id' => (int)$aData[7],
					'player_name' => $aData[8],
				]);
				$aUniquePaijuInfo = static::_getUniquePaijuInfo($aUniquePaijuList, $aData[1], $endTime);
				$aUniquePaijuList = $aUniquePaijuInfo['list'];
				array_push($aData, $aUniquePaijuInfo['id']);
				array_push($aData, Yii::$app->user->id);
				array_push($aInserDataList, $aData);
			}
		}
		if($aInserDataList){
			static::bathInsertData($aInserDataList);
			Player::checkAddNewPlayer(Yii::$app->user->id, $aPlayerList);
		}
		debug($aInserDataList,11);
	}
		
	private static function _getUniquePaijuInfo($aDataList, $paijuName, $endTime){
		foreach($aDataList as $aData){
			if($aData['paiju_name'] == $paijuName && $aData['end_time'] == $endTime){
				return [
					'id' => $aData['id'],
					'list' => $aDataList,
				];
			}
		}
		$mPaiju = Paiju::findOne(['user_id' => Yii::$app->user->id, 'paiju_name' => $paijuName, 'end_time' => $endTime]);
		if(!$mPaiju){
			$mPaiju = Paiju::addRecord([
				'user_id' => Yii::$app->user->id,
				'paiju_name' => $paijuName, 
				'end_time' => $endTime,
				'status' => Paiju::STATUS_UNDO,
				'create_time' => NOW_TIME,
			]);
		}
		array_push($aDataList, $mPaiju->toArray(['id', 'paiju_name', 'end_time']));
		
		return [
			'id' => $mPaiju->id,
			'list' => $aDataList,
		];
	}
	
	
	/**
	 *	获取列表
	 *	$aCondition = [
	 *		'id' =>
	 *		'user_id' =>
	 *		'player_id' =>
	 *		'club_id' => 
	 *		'paiju_id' => 
	 *		'status' => 
	 *		'start_time' =>
	 *		'end_time' =>
	 *		'create_start_time' =>
	 *		'create_end_time' =>
	 *	]
	 *	$aControl = [
	 *		'select' =>
	 *		'order_by' =>
	 *		'page' =>
	 *		'page_size' =>
	 *		'with_keren_benjin_info' => true/false
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
		
		$aKerenBenjinList = [];
		$qibuChoushui = 0;
		$choushuiShuanfa = User::CHOUSHUI_SHUANFA_YUSHUMOLIN;
		if(isset($aCondition['user_id']) && isset($aControl['with_keren_benjin_info']) && $aControl['with_keren_benjin_info']){
			$mUser = User::findOne($aCondition['user_id']);
			if($mUser){
				$qibuChoushui = $mUser->qibu_choushui;
				$choushuiShuanfa = $mUser->choushui_shuanfa;
			}
			$aPlayerId = ArrayHelper::getColumn($aList, 'player_id');
			//$aPlayerList = Player::findAll(['player_id' => $aPlayerId, 'user_id' => $aCondition['user_id']]);
			$sql = 'SELECT `t1`.`player_id`,`t2`.* FROM ' . Player::tableName() . ' AS `t1` LEFT JOIN ' . KerenBenjin::tableName() . ' AS `t2` ON `t1`.`keren_bianhao`=`t2`.`keren_bianhao` WHERE `t1`.`user_id`=' . $aCondition['user_id'] . ' AND `t2`.`user_id`=' . $aCondition['user_id'] . ' AND `t1`.`player_id` IN("' . implode('","', $aPlayerId) .'")';
			$aKerenBenjinList = Yii::$app->db->createCommand($sql)->queryAll();
		}
		foreach($aList as $key => $value){
			$aList[$key]['keren_benjin_info'] = [];
			foreach($aKerenBenjinList as $aKerenBenjin){
				if($value['player_id'] == $aKerenBenjin['player_id']){
					$aList[$key]['keren_benjin_info'] = $aKerenBenjin;
					$aList[$key]['jiesuan_value'] = Calculate::paijuPlayerJiesuanValue($value['zhanji'], $aKerenBenjin['ying_chou'], $aKerenBenjin['shu_fan'], $qibuChoushui, $choushuiShuanfa);
					$aList[$key]['new_benjin'] = $aKerenBenjin['benjin'] + $aList[$key]['jiesuan_value'];
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
		if(isset($aCondition['player_id'])){
			$aWhere[] = ['player_id' => $aCondition['player_id']];
		}
		if(isset($aCondition['club_id'])){
			$aWhere[] = ['club_id' => $aCondition['club_id']];
		}
		if(isset($aCondition['paiju_id'])){
			$aWhere[] = ['paiju_id' => $aCondition['paiju_id']];
		}
		if(isset($aCondition['status'])){
			$aWhere[] = ['status' => $aCondition['status']];
		}
		if(isset($aCondition['create_start_time'])){
			$aWhere[] = ['>', 'create_time', $aCondition['create_start_time']];
		}
		if(isset($aCondition['create_end_time'])){
			$aWhere[] = ['<', 'create_time', $aCondition['create_end_time']];
		}
		if(isset($aCondition['start_time'])){
			$aWhere[] = ['>', 'end_time', $aCondition['start_time']];
		}
		if(isset($aCondition['end_time'])){
			$aWhere[] = ['<', 'end_time', $aCondition['end_time']];
		}
		return $aWhere;
	}
}