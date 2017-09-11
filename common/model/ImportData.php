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
			'original_zhanji',
			'paiju_id',
			'user_id',
			'lianmeng_id'
		], $aInsertList)->execute();
	}
	
	public static function importFromExcelDataList($mUser, $aDataList){
		$lianmengId = $mUser->getDefaultLianmengId();
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
			array_push($aData, $aData[18]);
			//总手数为0为无效牌局（不计算桌子费）
			if($aData[6]){
				array_push($aPlayerList, [
					'player_id' => (int)$aData[7],
					'player_name' => $aData[8],
				]);
				$aUniquePaijuInfo = static::_getUniquePaijuInfo($mUser->id, $aUniquePaijuList, $aData[1], $endTime);
				$aUniquePaijuList = $aUniquePaijuInfo['list'];
				array_push($aData, $aUniquePaijuInfo['id']);
				array_push($aData, $mUser->id);
				array_push($aData, $lianmengId);
				array_push($aInserDataList, $aData);
			}
		}
		if($aInserDataList){
			static::bathInsertData($aInserDataList);
			Player::checkAddNewPlayer($mUser->id, $aPlayerList);
		}
		debug($aInserDataList,11);
	}
		
	private static function _getUniquePaijuInfo($userId, $aDataList, $paijuName, $endTime){
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
			$sql = 'SELECT `t1`.`player_id`,`t2`.* FROM ' . Player::tableName() . ' AS `t1` LEFT JOIN ' . KerenBenjin::tableName() . ' AS `t2` ON `t1`.`keren_bianhao`=`t2`.`keren_bianhao` WHERE `t1`.`user_id`=' . $aCondition['user_id'] . ' AND `t2`.`user_id`=' . $aCondition['user_id'] . ' AND `t1`.`player_id` IN("' . implode('","', $aPlayerId) .'")';
			$aKerenBenjinList = Yii::$app->db->createCommand($sql)->queryAll();
		}
		foreach($aList as $key => $value){
			$aList[$key]['keren_benjin_info'] = [];
			foreach($aKerenBenjinList as $aKerenBenjin){
				if($value['player_id'] == $aKerenBenjin['player_id']){
					$aList[$key]['keren_benjin_info'] = $aKerenBenjin;
					$aList[$key]['jiesuan_value'] = Calculate::paijuPlayerJiesuanValue($value['zhanji'], $aKerenBenjin['ying_chou'], $aKerenBenjin['shu_fan'], $qibuChoushui, $choushuiShuanfa);
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
	
	public static function getUserUnJiaoBanPaijuZhongChouShui($userId, $aClubId = []){
		$clubIdWhere = '';
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}else{
			return 0;
		}
		$sql = 'SELECT SUM(`t1`.`choushui_value`) AS `sum_choushui_value` FROM ' . static::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` WHERE `t1`.`user_id`=' . $userId . ' AND `t2`.`status`!=' . Paiju::STATUS_FINISH . ' AND `t1`.`status`=1 AND `t3`.is_delete=0 AND `t1`.`choushui_value`>0' . $clubIdWhere;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return $aResult[0]['sum_choushui_value'];
	}
	
	public static function getUserUnJiaoBanPaijuZhongBaoXian($userId, $aClubId = []){
		$clubIdWhere = '';
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}else{
			return 0;
		}
		$sql = 'SELECT SUM(`t1`.`baoxian_heji`) AS `sum_baoxian_heji` FROM ' . static::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` WHERE `t1`.`user_id`=' . $userId . ' AND `t2`.`status`!=' . Paiju::STATUS_FINISH . ' AND `t1`.`status`=1 AND `t3`.is_delete=0' . $clubIdWhere;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return $aResult[0]['sum_baoxian_heji'];
	}
	
	public static function getUserUnJiaoBanPaijuShangZhuoRenShu($userId, $aClubId = []){
		$clubIdWhere = '';
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}else{
			return 0;
		}
		$sql = 'SELECT COUNT(`t1`.`id`) AS `player_num` FROM ' . static::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` WHERE `t1`.`user_id`=' . $userId . ' AND `t2`.`status`!=' . Paiju::STATUS_FINISH . ' AND `t1`.`status`=1 AND `t3`.is_delete=0' . $clubIdWhere;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return $aResult[0]['player_num'];
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
		//1.计算结算值
		$jiesuanValue = Calculate::paijuPlayerJiesuanValue($this->zhanji, $mKerenBenjin->ying_chou, $mKerenBenjin->shu_fan, $mUser->qibu_choushui, $mUser->choushui_shuanfa);
		//2.设置结算状态、结算值、抽水值
		$this->set('status', 1);
		$this->set('jiesuan_value', $jiesuanValue);
		$this->set('choushui_value', $this->zhanji - $jiesuanValue);
		$this->save();
		//3.判断是否已结算完当前牌局记录，是则更新牌局状态
		if($mUser->checkIsJieShuanAllPaijuRecord($this->paiju_id)){
			$mPaiju = $this->getMPaiju();
			$mPaiju->set('status', Paiju::STATUS_DONE);
			$mPaiju->save();
		}
		//4.更新客人钱包
		$mKerenBenjin->set('benjin', ['add', $jiesuanValue]);
		$mKerenBenjin->save();
		
		return true;
	}
	
}