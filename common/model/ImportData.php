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
	
	/** 
	 *	为了标记自己导入的客人而插入一条空数据
	 */
	public static function addEmptyDataRecord($userId, $playerId, $playerName = ''){
		static::insert([
			'user_id' => $userId,
			'club_id' => 0,
			'player_id' => $playerId,
			'player_name' => $playerName,
			'create_time' => NOW_TIME,
		]);
	}
		
	private static function _bathInsertData($aInsertList){
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
			'original_zhanji',
			'paiju_id',
			'user_id'
		], $aInsertList)->execute();
	}
	
	public static function bathInsertData($aInsertList){
		$i = 0;
		$aPageList = [];
		foreach($aInsertList as $aData){
			array_push($aPageList, $aData);
			if($i % 50 == 0){
				static::_bathInsertData($aPageList);
				$aPageList = [];
			}
			$i++;
		}
		$aInsertList = null;
		if($aPageList){
			static::_bathInsertData($aPageList);
		}
		$aPageList = null;
	}
	
	public static function importFromExcelDataList($mUser, $aDataList){
		$lianmengId = $mUser->getDefaultLianmengId();
		
		if(!$aDataList){
			return false;
		}
		//去掉表头
		unset($aDataList[0]);
		//过滤已导入过的牌局
		$aPaijuName = array_unique(ArrayHelper::getColumn($aDataList, 1));
		//$aEndTimeFormat = array_unique(ArrayHelper::getColumn($aDataList, 19));
		
		//$aAlreadyImportDataList = static::findAll(['user_id' => $mUser->id, 'paiju_name' => $aPaijuName, 'end_time_format' => $aEndTimeFormat]);
		/*$aAlreadyImportDataList = [];
		$i = 0;
		$aPaijuNameList = [];
		foreach($aPaijuName as $paijuName){
			array_push($aPaijuNameList, $paijuName);
			if($i % 20 == 0 || $i == count($aPaijuName) - 1){
				if($aPaijuNameList){
					$aTempList = static::findAll(['user_id' => $mUser->id, 'paiju_name' => $aPaijuNameList]);
					$aPaijuNameList = [];
					if($aTempList){
						$aAlreadyImportDataList = array_merge($aAlreadyImportDataList, $aTempList);
					}
					$aTempList = null;
				}
			}
			$i++;
		}
		$aPaijuNameList = null;*/
		$aAlreadyImportDataList = static::findAll(['user_id' => $mUser->id, 'paiju_name' => $aPaijuName], ['paiju_name', 'player_id', 'end_time_format']);
		
		$aPaijuName = null;
		//$aEndTimeFormat = null;
		$aImportDataList = [];
		foreach($aDataList as $value){
			$isFind = false;
			foreach($aAlreadyImportDataList as $v){
				if($v['paiju_name'] == $value[1] && $v['player_id'] == $value[7] && $v['end_time_format'] == $value[19]){
					$isFind = true;
					break;
				}
			}
			if(!$isFind && strtotime($value[19]) > $mUser->active_time){
				array_push($aImportDataList, $value);
			}
		}
		$aDataList = null;
		$aAlreadyImportDataList = null;
		$aInserDataList = [];
		$aUniquePaijuList = [];
		//$aPlayerList = [];
		foreach($aImportDataList as $aData){
			 $aData[1] = trim($aData[1]);
			//结束时间转为时间戳
			$endTime = strtotime($aData[19]);
			array_push($aData, $endTime);
			array_push($aData, NOW_TIME);
			array_push($aData, $aData[18]);
			//总手数为0为无效牌局（不计算桌子费）
			if($aData[6]){
				/*array_push($aPlayerList, [
					'player_id' => (int)$aData[7],
					'player_name' => $aData[8],
				]);*/
				$aUniquePaijuInfo = static::_getUniquePaijuInfo($mUser->id, $aUniquePaijuList, $aData[1], $endTime, $lianmengId);
				$aUniquePaijuList = $aUniquePaijuInfo['list'];
				array_push($aData, $aUniquePaijuInfo['id']);
				array_push($aData, $mUser->id);
				/*$mImportData = static::findOne([
					'user_id' => $mUser->id, 
					'paiju_name' => $aData[1], 
					'player_id' => $aData[7], 
					'end_time_format' => $aData[19],
				]);
				if(!$mImportData){
					static::bathInsertData([$aData]);
				}*/
				array_push($aInserDataList, $aData);
			}
		}
		$aUniquePaijuList = null;
		$aImportDataList = null;
		if($aInserDataList){
			foreach($aInserDataList as $kk => $vv){
				$aInserDataList[$kk][12] = (int)$aInserDataList[$kk][12];
				$aInserDataList[$kk][18] = (int)$aInserDataList[$kk][18];
				$aInserDataList[$kk][22] = (int)$aInserDataList[$kk][22];
			}
			//Player::checkAddNewPlayer($mUser->id, $aPlayerList);
			static::bathInsertData($aInserDataList);
			//快速插入的后果可能有重复数据，要检查删了start
			$aPaijuName = array_unique(ArrayHelper::getColumn($aInserDataList, 1));
			$aInserDataList = null;
			$aAlreadyImportDataList = static::findAll(['user_id' => $mUser->id, 'paiju_name' => $aPaijuName], ['id', 'user_id', 'paiju_name', 'player_id', 'end_time_format']);
			$aPaijuName = null;
			$aRecordList = [];
			foreach($aAlreadyImportDataList as $v){
				$keyStr = $v['user_id'] . '_' . $v['paiju_name'] . '_' . $v['player_id'] . '_' . $v['end_time_format'];
				if(!isset($aRecordList[$keyStr])){
					$aRecordList[$keyStr] = 1;
				}else{
					$mImportData = static::toModel($v);
					$mImportData->delete();
				}
			}
			$aAlreadyImportDataList = null;
			$aRecordList = null;
			//快速插入的后果可能有重复数据，要检查删了end
		}
		return true;
	}
		
	private static function _getUniquePaijuInfo($userId, $aDataList, $paijuName, $endTime, $lianmengId){
		foreach($aDataList as $aData){
			if($aData['paiju_name'] == $paijuName && $aData['end_time'] == $endTime){
				return [
					'id' => $aData['id'],
					'list' => $aDataList,
				];
			}
		}
		$mPaiju = Paiju::findOne(['user_id' => $userId, 'paiju_name' => $paijuName, 'end_time' => $endTime]);
		if(!$mPaiju){
			$mPaiju = Paiju::addRecord([
				'user_id' => $userId,
				'paiju_name' => $paijuName, 
				'end_time' => $endTime,
				'status' => Paiju::STATUS_UNDO,
				'lianmeng_id' => $lianmengId,
				'create_time' => NOW_TIME,
			]);
		}
		array_push($aDataList, $mPaiju->toArray(['id', 'paiju_name', 'end_time']));
		
		return [
			'id' => $mPaiju->id,
			'list' => $aDataList,
		];
	}
	
	public static function importDownloadExcelFiles($mUser, $clubId){
		$aWhere = [
			'and',
			['user_id' => $mUser->id],
			['club_id' => $clubId],
			['>', 'download_time', 0],
			['import_time' => 0],
		];
		$aExcelFileList = ExcelFile::findAll($aWhere);
		foreach($aExcelFileList as $aExcelFile){
			$mExcelFile = ExcelFile::toModel($aExcelFile);
			$fileName = Yii::getAlias('@p.resource') . '/' . $mExcelFile->path;
			if($mExcelFile->path && file_exists($fileName)){
				try{
					$aDataList = Yii::$app->excel->getSheetDataInArray($fileName);
					if($aDataList){
						ImportData::importFromExcelDataList($mUser, $aDataList);
						$aDataList = null;
						$mExcelFile->set('import_time', NOW_TIME);
						$mExcelFile->save();
					}
				}catch(\Exception $e){
					continue;
				}
			}
		}
		$aExcelFileList = null;
		return true;
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
		$qibuTaifee = 0;
		$choushuiShuanfa = User::CHOUSHUI_SHUANFA_YUSHUMOLIN;
		if(isset($aCondition['user_id']) && isset($aControl['with_keren_benjin_info']) && $aControl['with_keren_benjin_info']){
			$mUser = User::findOne($aCondition['user_id']);
			if($mUser){
				$qibuChoushui = $mUser->qibu_choushui;
				$qibuTaifee = $mUser->qibu_taifee;
				$choushuiShuanfa = $mUser->choushui_shuanfa;
			}
			$aPlayerId = ArrayHelper::getColumn($aList, 'player_id');
			$sql = 'SELECT `t1`.`player_id`,`t2`.* FROM ' . Player::tableName() . ' AS `t1` LEFT JOIN ' . KerenBenjin::tableName() . ' AS `t2` ON `t1`.`keren_bianhao`=`t2`.`keren_bianhao` WHERE `t1`.`user_id`=' . $aCondition['user_id'] . ' AND `t2`.`user_id`=' . $aCondition['user_id'] . ' AND `t1`.`player_id` IN("' . implode('","', $aPlayerId) .'")';
			$aKerenBenjinList = Yii::$app->db->createCommand($sql)->queryAll();
		}
		foreach($aList as $key => $value){
			$aList[$key]['keren_benjin_info'] = [];
			foreach($aKerenBenjinList as $aKerenBenjin){
				if($value['player_id'] == $aKerenBenjin['player_id']){
					$aList[$key]['keren_benjin_info'] = $aKerenBenjin;
					$taifee = 0;
					if(abs($value['zhanji']) >= $qibuTaifee){
						if($value['zhanji'] > 0){
							$taifee = $aKerenBenjin['ying_fee'];
						}else{
							$taifee = -$aKerenBenjin['shu_fee'];
						}
					}
					$aList[$key]['jiesuan_value'] = Calculate::paijuPlayerJiesuanValue($value['zhanji'], $aKerenBenjin['ying_chou'], $aKerenBenjin['shu_fan'], $qibuChoushui, $choushuiShuanfa) - $taifee;
					$aList[$key]['float_jiesuan_value'] = Calculate::paijuPlayerJiesuanValue($value['zhanji'], $aKerenBenjin['ying_chou'], $aKerenBenjin['shu_fan'], $qibuChoushui, $choushuiShuanfa, false) - $taifee;
					if($value['status']){
						//如果该记录已结算，显示最新本金
						$aList[$key]['new_benjin'] = $aKerenBenjin['benjin'];
					}else{
						$aList[$key]['new_benjin'] = $aKerenBenjin['benjin'] + $aList[$key]['jiesuan_value'];
					}
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
	
	public static function getUserUnJiaoBanPaijuZhongChouShuiOld($userId, $aClubId = []){
		$clubIdWhere = '';
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}else{
			return 0;
		}
		$sql = 'SELECT SUM(`t1`.`choushui_value`) AS `sum_choushui_value` FROM ' . static::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` WHERE `t1`.`user_id`=' . $userId . ' AND `t2`.`user_id`=' . $userId . ' AND `t3`.`user_id`=' . $userId . ' AND `t2`.`status`!=' . Paiju::STATUS_FINISH . ' AND `t1`.`status`=1 AND `t3`.is_delete=0 AND `t1`.`choushui_value`>0' . $clubIdWhere;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return (int)$aResult[0]['sum_choushui_value'];
	}
	
	public static function getUserUnJiaoBanPaijuZhongChouShui($userId, $aClubId = []){
		$clubIdWhere = '';
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}else{
			return 0;
		}
		//*******************//
		$sql = 'SELECT * FROM ' . Paiju::tableName() . ' WHERE `user_id`=' . $userId . ' AND `status` !=' . Paiju::STATUS_FINISH;
		$aPaijuList = Yii::$app->db->createCommand($sql)->queryAll();
		$aPaijuId = ArrayHelper::getColumn($aPaijuList, 'id');
		$importDataSql = 'SELECT * FROM ' . ImportData::tableName() . ' WHERE `user_id`=' . $userId . ' AND `status`=1 AND `choushui_value`>0 AND `club_id` IN(' . implode(',', $aClubId) . ')';
		if($aPaijuId){
			$importDataSql .= ' AND `paiju_id` IN(' . implode(',', $aPaijuId) . ')';
		}
		//*******************//
		$sql = 'SELECT SUM(`t1`.`choushui_value`) AS `sum_choushui_value` FROM (' . $importDataSql . ') AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` WHERE `t1`.`user_id`=' . $userId . ' AND `t2`.`user_id`=' . $userId . ' AND `t3`.`user_id`=' . $userId . ' AND `t2`.`status`!=' . Paiju::STATUS_FINISH . ' AND `t1`.`status`=1 AND `t3`.is_delete=0 AND `t1`.`choushui_value`>0' . $clubIdWhere;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return (int)$aResult[0]['sum_choushui_value'];
	}
	
	public static function getUserUnJiaoBanPaijuZhongBaoXianOld($userId, $aClubId = []){
		$clubIdWhere = '';
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}else{
			return 0;
		}
		$sql = 'SELECT SUM(`t1`.`baoxian_heji`) AS `sum_baoxian_heji` FROM ' . static::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` WHERE `t1`.`user_id`=' . $userId . ' AND `t2`.`user_id`=' . $userId . ' AND `t3`.`user_id`=' . $userId . ' AND `t2`.`status`!=' . Paiju::STATUS_FINISH . ' AND `t1`.`status`=1 AND `t3`.is_delete=0' . $clubIdWhere;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return (int)$aResult[0]['sum_baoxian_heji'];
	}
	
	public static function getUserUnJiaoBanPaijuZhongBaoXian($userId, $aClubId = []){
		$clubIdWhere = '';
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}else{
			return 0;
		}
		//*******************//
		$sql = 'SELECT * FROM ' . Paiju::tableName() . ' WHERE `user_id`=' . $userId . ' AND `status` !=' . Paiju::STATUS_FINISH;
		$aPaijuList = Yii::$app->db->createCommand($sql)->queryAll();
		$aPaijuId = ArrayHelper::getColumn($aPaijuList, 'id');
		$importDataSql = 'SELECT * FROM ' . ImportData::tableName() . ' WHERE `user_id`=' . $userId . ' AND `status`=1 AND `club_id` IN(' . implode(',', $aClubId) . ')';
		if($aPaijuId){
			$importDataSql .= ' AND `paiju_id` IN(' . implode(',', $aPaijuId) . ')';
		}
		//*******************//
		$sql = 'SELECT SUM(`t1`.`baoxian_heji`) AS `sum_baoxian_heji` FROM (' . $importDataSql . ') AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` WHERE `t1`.`user_id`=' . $userId . ' AND `t2`.`user_id`=' . $userId . ' AND `t3`.`user_id`=' . $userId . ' AND `t2`.`status`!=' . Paiju::STATUS_FINISH . ' AND `t1`.`status`=1 AND `t3`.is_delete=0' . $clubIdWhere;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return (int)$aResult[0]['sum_baoxian_heji'];
	}
	
	public static function getUserUnJiaoBanPaijuShangZhuoRenShuOld($userId, $aClubId = []){
		$clubIdWhere = '';
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}else{
			return 0;
		}
		$sql = 'SELECT COUNT(`t1`.`id`) AS `player_num` FROM ' . static::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` WHERE `t1`.`user_id`=' . $userId . ' AND `t2`.`user_id`=' . $userId . ' AND `t3`.`user_id`=' . $userId . ' AND `t2`.`status`!=' . Paiju::STATUS_FINISH . ' AND `t1`.`status`=1 AND `t3`.is_delete=0' . $clubIdWhere;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return (int)$aResult[0]['player_num'];
	}
	
	public static function getUserUnJiaoBanPaijuShangZhuoRenShu($userId, $aClubId = []){
		$clubIdWhere = '';
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}else{
			return 0;
		}
		//*******************//
		$sql = 'SELECT * FROM ' . Paiju::tableName() . ' WHERE `user_id`=' . $userId . ' AND `status` !=' . Paiju::STATUS_FINISH;
		$aPaijuList = Yii::$app->db->createCommand($sql)->queryAll();
		$aPaijuId = ArrayHelper::getColumn($aPaijuList, 'id');
		$importDataSql = 'SELECT * FROM ' . ImportData::tableName() . ' WHERE `user_id`=' . $userId . ' AND `status`=1 AND `club_id` IN(' . implode(',', $aClubId) . ')';
		if($aPaijuId){
			$importDataSql .= ' AND `paiju_id` IN(' . implode(',', $aPaijuId) . ')';
		}
		//*******************//
		$sql = 'SELECT COUNT(`t1`.`id`) AS `player_num` FROM (' . $importDataSql . ') AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` WHERE `t1`.`user_id`=' . $userId . ' AND `t2`.`user_id`=' . $userId . ' AND `t3`.`user_id`=' . $userId . ' AND `t2`.`status`!=' . Paiju::STATUS_FINISH . ' AND `t1`.`status`=1 AND `t3`.is_delete=0' . $clubIdWhere;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return (int)$aResult[0]['player_num'];
	}
	
	public function getMUser(){
		return User::findOne($this->user_id);
	}
	
	public function getMPlayer(){
		return Player::findOne(['user_id' => $this->user_id, 'player_id' => $this->player_id]);
	}
	
	public function getMPaiju(){
		return Paiju::findOne($this->paiju_id);
	}
	
	/**
	 *	修改了玩家的战绩、客人的赢抽点数、输返点数、俱乐的部起步抽水、抽水算法 都要重新计算结算未交班的账单
	 */
	public static function reDoJieShuan($userId){
		
	}
	
	public function doJieShuan(){
		$mUser = $this->getMUser();
		$mKerenBenjin = $this->getMPlayer()->getMKerenBenjin();
		//计算台费
		$taifee = 0;
		if(abs($this->zhanji) >= $mUser->qibu_taifee){
			if($this->zhanji > 0){
				$taifee = $mKerenBenjin->ying_fee;
			}else{
				$taifee = -$mKerenBenjin->shu_fee;
			}
		}
		//1.计算结算值
		$jiesuanValue = Calculate::paijuPlayerJiesuanValue($this->zhanji, $mKerenBenjin->ying_chou, $mKerenBenjin->shu_fan, $mUser->qibu_choushui, $mUser->choushui_shuanfa);
		$floatJiesuanValue = Calculate::paijuPlayerJiesuanValue($this->zhanji, $mKerenBenjin->ying_chou, $mKerenBenjin->shu_fan, $mUser->qibu_choushui, $mUser->choushui_shuanfa, false);
		//2.设置结算状态、结算值、抽水值、台费
		$this->set('status', 1);
		$this->set('jiesuan_value', $jiesuanValue);
		$this->set('choushui_value', $this->zhanji - $jiesuanValue);
		$this->set('float_choushui_value', $this->zhanji - $floatJiesuanValue);
		$this->set('taifee', $taifee);
		$this->save();
		//3.判断是否已结算完当前牌局记录，是则更新牌局状态
		if($mUser->checkIsJieShuanAllPaijuRecord($this->paiju_id)){
			$mPaiju = $this->getMPaiju();
			$mPaiju->set('status', Paiju::STATUS_DONE);
			$mPaiju->save();
		}
		//4.更新客人钱包
		$mKerenBenjin->set('benjin', ['add', $this->zhanji - ($this->choushui_value + $taifee)]);
		$mKerenBenjin->save();
		
		return true;
	}
	
}