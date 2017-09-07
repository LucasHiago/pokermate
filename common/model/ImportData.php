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
	
}