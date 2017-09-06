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
			'create_time'
		], $aInsertList)->execute();
	}
	
	private static function _getUniquePaiJuInfo($aDataList, $paijuName, $endTime){
		foreach($aDataList as $aData){
			if($aData['paiju_name'] == $paijuName && $aData['end_time'] == $endTime){
				return [
					'id' => $aData['id'],
					'list' => $aDataList,
				];
			}
		}
		$mPaiJu = PaiJu::findOne(['paiju_name' => $paijuName, 'end_time' => $endTime]);
		if(!$mPaiJu){
			$mPaiJu = PaiJu::addRecord([
				'paiju_name' => $paijuName, 
				'end_time' => $endTime,
				'status' => PaiJu::STATUS_UNDO,
				'create_time' => NOW_TIME,
			]);
		}
		array_push($aDataList, $mPaiJu->toArray(['id', 'paiju_name', 'end_time']));
		
		return [
			'id' => $mPaiJu->id,
			'list' => $aDataList,
		];
	}
	
	public static function importFromExcelDataList($aDataList){debug(PaiJu::findOne(1),11);
		if(!$aDataList){
			return false;
		}
		//去掉表头
		unset($aDataList[0]);
		$aInserDataList = [];
		$aUniquePaiJuList = [];
		foreach($aDataList as $aData){
			 $aData[1] = trim($aData[1]);
			//结束时间转为时间戳
			$endTime = strtotime($aData[19]);
			array_push($aData, $endTime);
			array_push($aData, NOW_TIME);
			//总手数为0为无效牌局（不计算桌子费）
			if($aData[6]){
				$aUniquePaiJuInfo = static::_getUniquePaiJuInfo($aUniquePaiJuList, $aData[1], $endTime);
				$aUniquePaiJuList = $aUniquePaiJuInfo['list'];
				array_push($aData, $aUniquePaiJuInfo['id']);
				array_push($aInserDataList, $aData);
			}
		}
		//static::bathInsertData($aInserDataList);
		debug($aInserDataList,11);
	}
}