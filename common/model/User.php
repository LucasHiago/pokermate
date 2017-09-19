<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

class User extends \common\lib\DbOrmModel implements IdentityInterface{
	//用户类型：0普通用户1后台用户2微信用户3QQ用户4微博用户5支付宝用户
	const TYPE_NORMAL = 0;
	const TYPE_MANAGE = 1;
	const TYPE_WEIXIN = 2;
	const TYPE_QQ = 3;
	const TYPE_WEIBO = 4;
	const TYPE_ALIPAY = 5;
	
	const SEX_NONE = 0;
	const SEX_BOY = 1;
	const SEX_GIRL = 2;
	
	const CHOUSHUI_SHUANFA_SISHIWURU = 1;
	const CHOUSHUI_SHUANFA_YUSHUMOLIN = 2;
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@user');
	}

	/**
     * @inheritdoc 必须要实现的方法
     */
	public function allow($permissionName){
		return true;
	}
	
	/**
     * @inheritdoc 必须要实现的方法
     */
    public static function findIdentity($id){
        return static::findOne($id);
    }
	
	/**
     * @inheritdoc 必须要实现的方法
     */
    public static function findIdentityByAccessToken($token, $type = null){
        throw new NotSupportedException('根据令牌找用户 的方法未实现');
    }
	
	/**
     * @inheritdoc 必须要实现的方法
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc 必须要实现的方法
     */
    public function getAuthKey(){
        return $this->_authKey;
    }

    /**
     * @inheritdoc 必须要实现的方法
     */
    public function validateAuthKey($authKey){
        return $this->getAuthKey() === $authKey;
    }
	
	
	public static function encryptPassword($password){
		return $password;
		//return md5($password);
	}
	
	public static function register($aData){
		$id = static::insert($aData);
		$aData['id'] = $id;
		
		return static::toModel($aData);
	}
	
	/**
	 *	获取列表
	 *	$aCondition = [
	 *		'id' =>
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
		if(isset($aCondition['name'])){
			$aWhere[] = ['name' => $aCondition['name']];
		}
		if(isset($aCondition['mobile'])){
			$aWhere[] = ['mobile' => $aCondition['mobile']];
		}
		if(isset($aCondition['email'])){
			$aWhere[] = ['email' => $aCondition['email']];
		}
		if(isset($aCondition['login_name'])){
			$aWhere[] = ['login_name' => $aCondition['login_name']];
		}
		if(isset($aCondition['is_forbidden'])){
			$aWhere[] = ['is_forbidden' => $aCondition['is_forbidden']];
		}
		if(isset($aCondition['name_like']) && $aCondition['name_like']){
			$aWhere[] = ['like', 'name', $aCondition['name_like']];
		}
		if(isset($aCondition['login_name_like']) && $aCondition['login_name_like']){
			$aWhere[] = ['like', 'login_name', $aCondition['login_name_like']];
		}
		return $aWhere;
	}
	
	public function isManager(){
		if($this->type == static::TYPE_MANAGE){
			return true;
		}
		return false;
	}
	
	public function isVip(){
		if($this->vip_level && $this->vip_expire_time > NOW_TIME){
			return true;
		}
		return false;
	}
	
	public function vipDaysRemaining(){
		if($this->isVip()){
			return ceil(($this->vip_expire_time - NOW_TIME) / 86400);
		}
		return 0;
	}
	
	public function getUserClubList(){
		return Club::findAll([
			'user_id' => $this->id,
			'is_delete' => 0,
		]);
	}
		
	public function getMoneyTypeList(){
		return MoneyType::findAll([
			'user_id' => $this->id,
			'is_delete' => 0,
		]);
	}
	
	public function getMoneyOutPutTypeList(){
		return MoneyOutPutType::findAll([
			'user_id' => $this->id,
			'is_delete' => 0,
		]);
	}
	
	public function getMoneyTypeTotalMoney(){
		$sql = 'SELECT SUM(`money`) as `total_money` FROM ' . MoneyType::tableName() . ' WHERE `user_id`=' . $this->id . ' AND `is_delete`=0';
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return $aResult[0]['total_money'];
	}
	
	public function getMoneyOutPutTypeTotalMoney(){
		$sql = 'SELECT SUM(`money`) as `total_money` FROM ' . MoneyOutPutType::tableName() . ' WHERE `user_id`=' . $this->id . ' AND `is_delete`=0';
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return (int)$aResult[0]['total_money'];
	}
	
	public function getTotalKerenBenjiMoney(){
		$sql = 'SELECT SUM(`benjin`) as `total_money` FROM ' . KerenBenjin::tableName() . ' WHERE `user_id`=' . $this->id . ' AND `is_delete`=0';
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return (int)$aResult[0]['total_money'];
	}
	
	public function getTotalQianKuanMoney(){
		$sql = 'SELECT SUM(`benjin`) as `total_money` FROM ' . KerenBenjin::tableName() . ' WHERE `user_id`=' . $this->id . ' AND `is_delete`=0 AND `benjin`<0';
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return (int)$aResult[0]['total_money'];
	}
	
	public function getTotalShuYin(){
		$aClubList = $this->getUserClubList();
		if($aClubList){
			$aClubId = ArrayHelper::getColumn($aClubList, 'club_id');
		}else{
			return 0;
		}
		$sql = 'SELECT SUM(`zhanji`) as `total_zhanji` FROM ' . ImportData::tableName() . ' WHERE `user_id`=' . $this->id . ' AND `club_id` IN(' . implode(',', $aClubId) . ')';
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return (int)$aResult[0]['total_zhanji'];
	}
	
	public function getAgentList(){
		$aList = Agent::getList(['user_id' => $this->id, 'is_delete' => 0]);
		foreach($aList as $key => $value){
			$aList[$key]['fencheng_setting'] = $this->getFenchengListSetting($value['id']);
		}
		return $aList;
	}
	
	public function getFenchengListSetting($agentId){
		$aFenchengConfigList = FenchengSetting::getFenchengConfigList();
		$aList = FenchengSetting::findAll(['user_id' => $this->id, 'agent_id' => $agentId, 'zhuozi_jibie' => $aFenchengConfigList]);
		$aInsertList = [];
		foreach($aFenchengConfigList as $zhuoziJibie){
			$flag = false;
			foreach($aList as $value){
				if(in_array($value['zhuozi_jibie'], $aFenchengConfigList)){
					$flag = true;
				}
			}
			if(!$flag){
				array_push($aInsertList, [
					'user_id' => $this->id,
					'agent_id' => $agentId,
					'zhuozi_jibie' => $zhuoziJibie,
					'yingfan' => 0,
					'shufan' => 0,
				]);
			}
		}
		if($aInsertList){
			FenchengSetting::bathInsertData($aInsertList);
		}
		return FenchengSetting::findAll(['user_id' => $this->id, 'agent_id' => $agentId, 'zhuozi_jibie' => $aFenchengConfigList]);
	}
	
	public function getLastPaijuList($page = 1, $pageSize = 0, $aParam = ['status' => [Paiju::STATUS_UNDO, Paiju::STATUS_DONE]], $aOrder = ['`t1`.`end_time`' => SORT_DESC]){
		$aCondition = [
			'user_id' => $this->id,
			'status' => $aParam['status'],
		];
		$aClubList = $this->getUserClubList();
		if($aClubList){
			$aCondition['club_id'] = ArrayHelper::getColumn($aClubList, 'club_id');
		}else{
			return [];
		}
		$aControll = [
			'select' => '`t1`.*',
			'order_by' => $aOrder,
			'width_hedui_shuzi' => true,
		];
		if($pageSize){
			$aControll['page'] = $page;
			$aControll['page_size'] = $pageSize;
		}
		return Paiju::getList($aCondition, $aControll);
	}
	
	public function getPaijuDataList($paijuId, $isAllRecordData = false){
		$mPaiju = Paiju::findOne(['id' => $paijuId, 'user_id' => $this->id]);
		if(!$mPaiju){
			return false;
		}
		$aClubId = [];
		$aClubList = $this->getUserClubList();
		if($aClubList){
			$aClubId = ArrayHelper::getColumn($aClubList, 'club_id');
		}else{
			return [];
		}
		
		$aCondition = ['paiju_id' => $paijuId, 'user_id' => $this->id, 'club_id' => $aClubId];
		if($isAllRecordData){
			$aCondition = ['paiju_id' => $paijuId, 'user_id' => $this->id];
		}
		$aControll = ['with_keren_benjin_info' => true];
		$aList = ImportData::getList($aCondition, $aControll);
		//过滤掉删除的客人记录
		$aReturnList = [];
		foreach($aList as $key => $value){
			if(isset($value['keren_benjin_info']) && $value['keren_benjin_info'] && isset($value['keren_benjin_info']['is_delete']) && !$value['keren_benjin_info']['is_delete']){
				array_push($aReturnList, $value);
			}
		}
		return $aReturnList;
	}
	
	public function getDefaultLianmengId(){
		$mLianmeng = Lianmeng::findOne(['user_id' => $this->id, 'is_delete' => 0]);
		if(!$mLianmeng){
			return Lianmeng::addRecord([
				'user_id' => $this->id,
				'name' => '默认联盟',
				'create_time' => NOW_TIME,
			]);
		}
		return $mLianmeng->id;
	}
	
	public function getLianmengList(){
		return Lianmeng::findAll(['user_id' => $this->id, 'is_delete' => 0]);
	}
	
	public function checkIsJieShuanAllPaijuRecord($paijuId){
		$flag = true;
		$aPaijuDataList = $this->getPaijuDataList($paijuId);
		foreach($aPaijuDataList as $aPaijuData){
			if(!$aPaijuData['status']){
				$flag = false;
				break;
			}
		}
		return $flag;
	}
	
	/**
	 *	统计未交班的牌局总抽水、总保险、上桌人数、差额、交班转出、客人总本金
	 */
	public function getUnJiaoBanPaijuTotalStatistic(){
		$aUnJiaoBanPaijuTotalStatistic = $this->_getUnJiaoBanPaijuTotalStatistic();
		$aUnJiaoBanPaijuTotalStatistic['imbalanceMoney'] = $this->getImbalanceMoney();
		$aUnJiaoBanPaijuTotalStatistic['jiaoBanZhuanChuMoney'] = $this->getJiaoBanZhuanChuMoney();
		$aUnJiaoBanPaijuTotalStatistic['kerenTotalBenjinMoney'] = $this->getTotalKerenBenjiMoney();
		$aUnJiaoBanPaijuTotalStatistic['kerenTotalQianKuanMoney'] = $this->getTotalQianKuanMoney();
		$aUnJiaoBanPaijuTotalStatistic['kerenTotalShuYin'] = $this->getTotalShuYin();
		
		return $aUnJiaoBanPaijuTotalStatistic;
	}
	
	/**
	 *	统计未交班的牌局总抽水、总保险、上桌人数,实际抽水
	 */
	private function _getUnJiaoBanPaijuTotalStatistic(){
		$aClubId = [];
		$aClubList = $this->getUserClubList();
		if($aClubList){
			$aClubId = ArrayHelper::getColumn($aClubList, 'club_id');
		}else{
			return [
				'zhongChouShui' => 0,
				'zhongBaoXian' => 0,
				'shangZhuoRenShu' => 0,
			];
		}
		//获取总抽水
		$zhongChouShui = ImportData::getUserUnJiaoBanPaijuZhongChouShui($this->id, $aClubId) + $this->choushui_ajust_value;
		//获取总保险
		$zhongBaoXian = ImportData::getUserUnJiaoBanPaijuZhongBaoXian($this->id, $aClubId) + $this->baoxian_ajust_value;
		//上桌人数
		$shangZhuoRenShu = ImportData::getUserUnJiaoBanPaijuShangZhuoRenShu($this->id, $aClubId);
		//实际抽水
		$shijiChouShui = $this->getShijiChouShui();
		
		return [
			'zhongChouShui' => $zhongChouShui,
			'zhongBaoXian' => $zhongBaoXian,
			'shangZhuoRenShu' => $shangZhuoRenShu,
			'shijiChouShui' => $shijiChouShui,
		];
	}
	
	/**
	 *	统计实际抽水
	 */
	public function getShijiChouShui(){
		$shijiChouShui = $this->choushui_ajust_value;
		$aChouShuiList = $this->getUnJiaoBanPaijuChouShuiList();
		foreach($aChouShuiList as $aChouShui){
			$shijiChouShui += $aChouShui['shiji_choushui_value'];
		}
		return $shijiChouShui;
	}
	
	private function _getUnJiaoBanPaijuChouShuiDataListWithLianmengInfo(){
		$clubIdWhere = '';
		$aClubId = [];
		$aClubList = $this->getUserClubList();
		if($aClubList){
			$aClubId = ArrayHelper::getColumn($aClubList, 'club_id');
		}else{
			return [];
		}
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}
		$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t2`.`lianmeng_id`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`status`=' . Paiju::STATUS_DONE . ' AND `t1`.`status`=1 AND `t3`.`is_delete`=0' . $clubIdWhere;
		return Yii::$app->db->createCommand($sql)->queryAll();
		
	}
	
	/**
	 *	获取抽水列表
	 */
	public function getUnJiaoBanPaijuChouShuiList(){
		$aResult = $this->_getUnJiaoBanPaijuChouShuiDataListWithLianmengInfo();
		$aReturnList = [];
		/*foreach($aResult as $value){
			if(!isset($aReturnList[$value['paiju_id']])){
				$aReturnList[$value['paiju_id']] = [
					'paiju_name' => $value['paiju_name'],
					'zhanji' => 0,
					'choushui_value' => 0,
					'lianmeng_butie' => 0,
					'shiji_choushui_value' => 0,
					'paiju_fee' => 0,
				];
			}
			$aReturnList[$value['paiju_id']]['zhanji'] += $value['zhanji'];
			$aReturnList[$value['paiju_id']]['choushui_value'] += $value['choushui_value'];
			$lianmengButie = Calculate::calculateLianmengButie($value['zhanji'], $value['baoxian_heji'], $value['duizhangfangfa'], $this->choushui_shuanfa);
			$aReturnList[$value['paiju_id']]['lianmeng_butie'] += $lianmengButie;
			$aReturnList[$value['paiju_id']]['shiji_choushui_value'] += Calculate::calculateShijiChouShuiValue($value['choushui_value'], $lianmengButie, $value['paiju_fee']);
			$aReturnList[$value['paiju_id']]['paiju_fee'] += $value['paiju_fee'];
		}*/
		foreach($aResult as $value){
			if(!isset($aReturnList[$value['paiju_id']])){
				$aReturnList[$value['paiju_id']] = [
					'paiju_name' => $value['paiju_name'],
					'zhanji' => 0,
					'choushui_value' => 0,
					'lianmeng_butie' => 0,
					'shiji_choushui_value' => 0,
					'baoxian_heji' => 0,
					'paiju_fee' => $value['paiju_fee'],
					'duizhangfangfa' => $value['duizhangfangfa'],
				];
			}
			$aReturnList[$value['paiju_id']]['zhanji'] += $value['zhanji'];
			$aReturnList[$value['paiju_id']]['baoxian_heji'] += $value['baoxian_heji'];
			$aReturnList[$value['paiju_id']]['choushui_value'] += $value['choushui_value'];
		}
		foreach($aReturnList as $paijuId => $v){
			$lianmengButie = Calculate::calculateLianmengButie($v['zhanji'], $v['baoxian_heji'], $v['duizhangfangfa'], $this->choushui_shuanfa);
			$aReturnList[$paijuId]['lianmeng_butie'] = $lianmengButie;
			$aReturnList[$paijuId]['shiji_choushui_value'] = Calculate::calculateShijiChouShuiValue($v['choushui_value'], $lianmengButie, $v['paiju_fee']);
		}
		
		return $aReturnList;
	}
	
	private function _getUnJiaoBanPaijuBaoXianDataListWithLianmengInfo(){
		$clubIdWhere = '';
		$aClubId = [];
		$aClubList = $this->getUserClubList();
		if($aClubList){
			$aClubId = ArrayHelper::getColumn($aClubList, 'club_id');
		}else{
			return [];
		}
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}
		$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`status`=' . Paiju::STATUS_DONE . ' AND `t1`.`status`=1 AND `t3`.`is_delete`=0' . $clubIdWhere;
		return Yii::$app->db->createCommand($sql)->queryAll();
		
	}
	
	/**
	 *	获取保险列表
	 */
	public function getUnJiaoBanPaijuBaoXianList(){
		$aResult = $this->_getUnJiaoBanPaijuBaoXianDataListWithLianmengInfo();
		$aReturnList = [];
		foreach($aResult as $value){
			if(!isset($aReturnList[$value['paiju_id']])){
				$aReturnList[$value['paiju_id']] = [
					'paiju_name' => $value['paiju_name'],
					'baoxian_heji' => 0,
					'baoxian_beichou' => 0,
					'shiji_baoxian' => 0,
				];
			}
			$aReturnList[$value['paiju_id']]['baoxian_heji'] += $value['baoxian_heji'];
			$baoxianBeichou = Calculate::calculateBaoxianBeichou($value['baoxian_heji'], $value['baoxian_choucheng'], $this->choushui_shuanfa);
			$aReturnList[$value['paiju_id']]['baoxian_beichou'] += $baoxianBeichou;
			$aReturnList[$value['paiju_id']]['shiji_baoxian'] += Calculate::calculateShijiBaoXian($value['baoxian_heji'], $baoxianBeichou);
		}
		
		return $aReturnList;
	}

	/**
	 *	获取上桌人数列表
	 */
	public function getUnJiaoBanPaijuShangZhuoRenShuList(){
		$clubIdWhere = '';
		$aClubId = [];
		$aClubList = $this->getUserClubList();
		if($aClubList){
			$aClubId = ArrayHelper::getColumn($aClubList, 'club_id');
		}else{
			return [];
		}
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}
		
		$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,COUNT(*) AS `shang_zhuo_ren_shu` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`status`=' . Paiju::STATUS_DONE . ' AND `t1`.`status`=1 AND `t3`.`is_delete`=0' . $clubIdWhere . ' GROUP BY `t1`.`paiju_id`';
		$aReturnList = Yii::$app->db->createCommand($sql)->queryAll();
		
		return $aReturnList;
	}
	
	/**
	 *	获取已结算的并且联盟未清账的牌局结算记录带有联盟信息
	 */
	private function _getAlreadyJieShuanPaijuDataListWithLianmengInfo($lianmengId = 0){
		$clubIdWhere = '';
		$aClubId = [];
		$aClubList = $this->getUserClubList();
		if($aClubList){
			$aClubId = ArrayHelper::getColumn($aClubList, 'club_id');
		}else{
			return [];
		}
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}
		$lianmengIdWhere = '';
		if($lianmengId){
			$lianmengIdWhere = ' AND `t2`.`lianmeng_id`=' . $lianmengId;
		}
		$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t2`.`is_clean`,`t2`.`lianmeng_id`,`t4`.`name` AS `lianmeng_name`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`status`>=' . Paiju::STATUS_DONE . ' AND `t1`.`status`=1 AND `t3`.`is_delete`=0' . $lianmengIdWhere . $clubIdWhere;
		return Yii::$app->db->createCommand($sql)->queryAll();
	}
	
	public function getLianmengZhangDanDetailList($lianmengId = 0){
		$aResult = $this->_getAlreadyJieShuanPaijuDataListWithLianmengInfo($lianmengId);
		$aReturnList = [];
		foreach($aResult as $value){
			if(!isset($aReturnList[$value['paiju_id']])){
				$aReturnList[$value['paiju_id']] = [
					'paiju_id' => $value['paiju_id'],
					'paiju_name' => $value['paiju_name'],
					'zhanji' => 0,
					'baoxian_heji' => 0,
					'paiju_fee' => 0,
					'baoxian_beichou' => 0,
					'zhang_dan' => 0,
					'lianmeng_id' => $value['lianmeng_id'],
				];
			}
			$aReturnList[$value['paiju_id']]['zhanji'] += $value['zhanji'];
			$aReturnList[$value['paiju_id']]['baoxian_heji'] += $value['baoxian_heji'];
			$aReturnList[$value['paiju_id']]['paiju_fee'] += $value['paiju_fee'];
			$baoxianBeichou = Calculate::calculateBaoxianBeichou($value['baoxian_heji'], $value['baoxian_choucheng'], $this->choushui_shuanfa);
			$aReturnList[$value['paiju_id']]['baoxian_beichou'] += $baoxianBeichou;
			if(!$value['is_clean']){
				$aReturnList[$value['paiju_id']]['zhang_dan'] += Calculate::calculateZhangDan($value['zhanji'], $value['baoxian_heji'], $value['paiju_fee'], $baoxianBeichou, $value['duizhangfangfa'], $this->choushui_shuanfa);
			}
		}
		return $aReturnList;
	}
		
	public function getLianmengZhongZhangList(){
		$aLianmengList = $this->getLianmengList();
		$aResult = $this->_getAlreadyJieShuanPaijuDataListWithLianmengInfo();
		$aReturnList = [];
		foreach($aLianmengList as $aLianmeng){
			if(!isset($aReturnList[$aLianmeng['id']])){
				$aReturnList[$aLianmeng['id']] = [
					'lianmeng_id' => $aLianmeng['id'],
					'lianmeng_name' => $aLianmeng['name'],
					'lianmeng_zhong_zhang' => 0,
					'lianmeng_shang_zhuo_ren_shu' => 0,
					'lianmeng_qian_zhang' => $aLianmeng['qianzhang'],
					'lianmeng_zhang_dan' => 0,
				];
			}
		}
		foreach($aResult as $value){
			//$aReturnList[$value['lianmeng_id']]['lianmeng_zhong_zhang'] += 1;
			$aReturnList[$value['lianmeng_id']]['lianmeng_shang_zhuo_ren_shu'] += 1;
			$baoxianBeichou = Calculate::calculateBaoxianBeichou($value['baoxian_heji'], $value['baoxian_choucheng'], $this->choushui_shuanfa);
			if(!$value['is_clean']){
				$aReturnList[$value['lianmeng_id']]['lianmeng_zhang_dan'] += Calculate::calculateZhangDan($value['zhanji'], $value['baoxian_heji'], $value['paiju_fee'], $baoxianBeichou, $value['duizhangfangfa'], $this->choushui_shuanfa);
			}
		}
		
		foreach($aReturnList as $lianmengId => $aValue){
			$aReturnList[$lianmengId]['lianmeng_zhong_zhang'] = $aValue['lianmeng_qian_zhang'] + $aValue['lianmeng_zhang_dan'];
		}
		
		return $aReturnList;
	}
	
	/**
	 *	联盟清账
	 */
	public function qinZhang($mLianmeng, $zhangDan){
		if(!$zhangDan){
			return false;
		}
		//更新账单牌局已清账状态
		$aLianmengZhangDanDetailList = $this->getLianmengZhangDanDetailList($mLianmeng->id);
		if(!$aLianmengZhangDanDetailList){
			return false;
		}
		$aPaijuId = array_keys($aLianmengZhangDanDetailList);
		$sql = 'UPDATE ' . Paiju::tableName() . ' SET `is_clean`=1 WHERE `id` IN(' . implode(',', $aPaijuId) . ')';
		Yii::$app->db->createCommand($sql)->execute();
		//更新联盟欠账
		$mLianmeng->set('qianzhang', ['add', $zhangDan]);
		$mLianmeng->save();
		
		return true;
	}
	
	/**
	 *	获取差额值
	 */
	public function getImbalanceMoney(){
		$totalMoneyTypeMoney = $this->getMoneyTypeTotalMoney();
		$totalOutPutTypeMoney = $this->getMoneyOutPutTypeTotalMoney();
		$totalKerenBenjin = $this->getTotalKerenBenjiMoney();
		$aUnJiaoBanPaijuTotalStatistic = $this->_getUnJiaoBanPaijuTotalStatistic();
		$totalChouShui = $aUnJiaoBanPaijuTotalStatistic['zhongChouShui'];
		$totalBaoXian = $aUnJiaoBanPaijuTotalStatistic['zhongBaoXian'];
		$totalLianmengZhongZhang = $this->getLianmengZhongZhang();
		
		return Calculate::calculateImbalanceMoney($totalMoneyTypeMoney, $totalOutPutTypeMoney, $totalKerenBenjin, $totalChouShui, $totalBaoXian, $totalLianmengZhongZhang);
	}
	
	/**
	 *	获取联盟总账
	 */
	public function getLianmengZhongZhang(){
		$totalLianmengZhongZhang = $this->lianmeng_zhongzhang_ajust_value;
		$aLianmengZhongZhangList = $this->getLianmengZhongZhangList();
		foreach($aLianmengZhongZhangList as $aLianmengZhongZhang){
			$totalLianmengZhongZhang += $aLianmengZhongZhang['lianmeng_zhong_zhang'];
		}
		return $totalLianmengZhongZhang;
	}

	/**
	 *	获取交班转出值
	 */
	public function getJiaoBanZhuanChuMoney(){
		$totalOutPutTypeMoney = $this->getMoneyOutPutTypeTotalMoney();
		$aUnJiaoBanPaijuTotalStatistic = $this->_getUnJiaoBanPaijuTotalStatistic();
		$totalChouShui = $aUnJiaoBanPaijuTotalStatistic['zhongChouShui'];
		$totalBaoXian = $aUnJiaoBanPaijuTotalStatistic['zhongBaoXian'];
				
		return Calculate::calculateJiaoBanZhuanChuMoney($totalOutPutTypeMoney, $totalChouShui, $totalBaoXian);
	}

	/**
	 *	获取交班转出明细
	 */
	public function getJiaoBanZhuanChuDetail(){
		$totalMoneyTypeMoney = $this->getMoneyTypeTotalMoney();
		$aUnJiaoBanPaijuTotalStatistic = $this->_getUnJiaoBanPaijuTotalStatistic();
		$totalOutPutTypeMoney = $this->getMoneyOutPutTypeTotalMoney();
		$jiaoBanZhuanChuMoney = $this->getJiaoBanZhuanChuMoney();
		/*$jiaojieMoney = $totalMoneyTypeMoney - $jiaoBanZhuanChuMoney;*/
		if(!$jiaoBanZhuanChuMoney){
			return false;
		}
		return [
			'zhongChouShui' => $aUnJiaoBanPaijuTotalStatistic['zhongChouShui'],
			'zhongBaoXian' => $aUnJiaoBanPaijuTotalStatistic['zhongBaoXian'],
			'totalOutPutTypeMoney' => $totalOutPutTypeMoney,
			'jiaoBanZhuanChuMoney' => $jiaoBanZhuanChuMoney,
			//'jiaojieMoney' => $jiaojieMoney,
		];
	}

	/**
	 *	交班转出操作
	 */
	public function doJiaoBanZhuanChu($mMoneyType, $jiaoBanZhuanChuMoney){
		if(!$jiaoBanZhuanChuMoney){
			return false;
		}
		//1.所有联盟清账
		$aLianmengZhongZhangList = $this->getLianmengZhongZhangList();
		foreach($aLianmengZhongZhangList as $aLianmengZhongZhang){
			$zhangDan = $aLianmengZhongZhang['lianmeng_zhang_dan'];
			if($zhangDan){
				$mLianmeng = Lianmeng::findOne($aLianmengZhongZhang['lianmeng_id']);
				if(!$mLianmeng){
					return false;
				}
				if(!$this->qinZhang($mLianmeng, $zhangDan)){
					return false;
				}
			}
		}
		//2.设置牌局状态为已交班
		$sql = 'UPDATE ' . Paiju::tableName() . ' SET `status`=' . Paiju::STATUS_FINISH . ' WHERE `user_id`=' . $this->id . ' AND `status`=' . Paiju::STATUS_DONE;
		Yii::$app->db->createCommand($sql)->execute();
		//3.清空支出类型金额
		$sql = 'UPDATE ' . MoneyOutPutType::tableName() . ' SET `money`=0 WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		//4.交班转出金额转到转出渠道
		$mMoneyType->set('money', ['sub', $jiaoBanZhuanChuMoney]);
		$mMoneyType->save();
		
		return true;
	}

	/**
	 *	获取代理未清账分成列表
	 */
	public function getAgentUnCleanFenChengList($agentId){
		$clubIdWhere = '';
		$aClubId = [];
		$aClubList = $this->getUserClubList();
		if($aClubList){
			$aClubId = ArrayHelper::getColumn($aClubList, 'club_id');
		}else{
			return [];
		}
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}
		//$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`mangzhu`,`t1`.`player_id`,`t1`.`player_name`,`t1`.`zhanji` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` LEFT JOIN ' . KerenBenjin::tableName() . ' AS `t6` ON `t6`.`keren_bianhao`=`t6`.`keren_bianhao` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`status`>=' . Paiju::STATUS_DONE . ' AND `t1`.`status`=1 AND `t1`.`agent_is_clean`=0 AND `t3`.`is_delete`=0 AND `t6`.`agent_id`=' . $agentId . $clubIdWhere;
		$sql = 'SELECT `t7`.* FROM (SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`mangzhu`,`t1`.`player_id`,`t1`.`player_name`,`t1`.`zhanji`,`t3`.`keren_bianhao` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`status`>=' . Paiju::STATUS_DONE . ' AND `t1`.`status`=1 AND `t1`.`agent_is_clean`=0 AND `t3`.`is_delete`=0 ' . $clubIdWhere . ') AS `t7` LEFT JOIN ' . KerenBenjin::tableName() . ' AS `t8` ON `t7`.`keren_bianhao`=`t8`.`keren_bianhao` WHERE `t8`.`agent_id`=' . $agentId;
		$aResult =  Yii::$app->db->createCommand($sql)->queryAll();
		
		$aFenchengSetting = ArrayHelper::index($this->getFenchengListSetting($agentId), 'zhuozi_jibie');
		foreach($aResult as $key => $value){
			$yinFan = 0;
			$shuFan = 0;
			if(isset($aFenchengSetting[$value['mangzhu']])){
				$yinFan = (float)$aFenchengSetting[$value['mangzhu']]['yingfan'];
				$shuFan = (float)$aFenchengSetting[$value['mangzhu']]['shufan'];
			}
			$aResult[$key]['fencheng'] = Calculate::calculateFenchengMoney($value['zhanji'], $yinFan, $shuFan, $this->choushui_shuanfa);
		}
		
		return $aResult;
	}
	
	/**
	 *	代理清账
	 */
	public function agentQinZhang($aImportDataId){
		if(!$aImportDataId){
			return false;
		}
		
		$sql = 'UPDATE ' . ImportData::tableName() . ' SET `agent_is_clean`=1 WHERE `id` IN(' . implode(',', $aImportDataId) . ')';
		Yii::$app->db->createCommand($sql)->execute();
		
		return true;
	}
	
	/**
	 *	获取联盟主机对账列表
	 */
	public function getLianmengHostDuizhang($lianmengId){
		set_time_limit(0);
		$mLianmeng = Lianmeng::findOne($lianmengId);
		if(!$mLianmeng){
			return [];
		}
		$clubIdWhere = '';
		$aClubId = [];
		$aClubList = $mLianmeng->getLianmengClubList();
		if($aClubList){
			$aClubId = ArrayHelper::getColumn($aClubList, 'club_id');
		}else{
			return [];
		}
		$aClubList = ArrayHelper::index($aClubList, 'club_id');
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}
		$lianmengIdWhere = '';
		if($lianmengId){
			$lianmengIdWhere = ' AND `t2`.`lianmeng_id`=' . $lianmengId;
		}
		//$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t1`.`club_id`,`t1`.`club_name`,`t1`.`club_is_clean`,`t2`.`lianmeng_id`,`t4`.`name` AS `lianmeng_name`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . '' . $lianmengIdWhere . $clubIdWhere;
		$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t1`.`club_id`,`t1`.`club_name`,`t1`.`club_is_clean`,`t2`.`lianmeng_id`,`t4`.`name` AS `lianmeng_name`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`status`>=1' . $lianmengIdWhere . $clubIdWhere;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		$aPaijuDataZhangDanList = [];
		$aPaijuZhangDanList = [];
		$totalZhanDan = 0;
		foreach($aResult as $value){
			$baoxianChoucheng = 0;
			$paijuFee = 0;
			$duizhangfangfa = 0;
			foreach($aClubList as $aClub){
				if($aClub['club_id'] == $value['club_id']){
					$baoxianChoucheng = $aClub['baoxian_choucheng'];
					$paijuFee = $aClub['paiju_fee'];
					$duizhangfangfa = $aClub['duizhangfangfa'];
					break;
				}
			}
			$aPaijuZhangDanList[$value['paiju_id']] = [
				'paiju_id' => $value['paiju_id'],
				'paiju_name' => $value['paiju_name'],
				'club_is_clean' => $value['club_is_clean'],
			];
			$aTemp = [
				'paiju_id' => $value['paiju_id'],
				'paiju_name' => $value['paiju_name'],
				'club_id' => $value['club_id'],
				'paiju_fee' => $paijuFee,
				'duizhangfangfa' => $duizhangfangfa,
				'zhang_dan' => 0,
				'zhanji' => $value['zhanji'],
				'baoxian_heji' => $value['baoxian_heji'],
				'baoxian_beichou' => 0,
				'club_is_clean' => $value['club_is_clean'],
			];
			$baoxianBeichou = Calculate::calculateBaoxianBeichou($value['baoxian_heji'], $baoxianChoucheng, $this->choushui_shuanfa);
			$aTemp['baoxian_beichou'] = $baoxianBeichou;
			/*if(!$value['club_is_clean']){
				//账单值与自己俱乐部联盟账单值相反
				$aTemp['zhang_dan'] -= Calculate::calculateZhangDan($value['zhanji'], $value['baoxian_heji'], $paijuFee, $baoxianBeichou, $duizhangfangfa, $this->choushui_shuanfa);
				$totalZhanDan += $aTemp['zhang_dan'];
			}*/
			$aPaijuDataZhangDanList[] = $aTemp;
		}
		$aClubPaijuDataZhangDanList = [];
		foreach($aPaijuDataZhangDanList as $value){
			if(!isset($aClubPaijuDataZhangDanList[$value['club_id']])){
				$aClubPaijuDataZhangDanList[$value['club_id']] = [];
			}
			$aClubPaijuDataZhangDanList[$value['club_id']][] = $value;
		}
		//俱乐部没有牌局记录，则制造假记录
		foreach($aClubList as $aClub){
			if(!isset($aClubPaijuDataZhangDanList[$aClub['club_id']])){
				foreach($aPaijuZhangDanList as $mm){
					$aTempData = [
						'paiju_id' => $mm['paiju_id'],
						'paiju_name' => $mm['paiju_name'],
						'club_id' => $aClub['club_id'],
						'paiju_fee' => $aClub['paiju_fee'],
						'duizhangfangfa' => $aClub['duizhangfangfa'],
						'zhang_dan' => 0,
						'zhanji' => 0,
						'baoxian_heji' => 0,
						'baoxian_beichou' => 0,
						'club_is_clean' => $mm['club_is_clean'],
					];
					$aClubPaijuDataZhangDanList[$aClub['club_id']][] = $aTempData;
					array_push($aPaijuDataZhangDanList, $aTempData);
				}
			}
		}
		$aClubZhangDanList = [];
		foreach($aClubList as $aClub){
			$aClubZhangDanList[$aClub['club_id']] = [
				'lianmeng_club_id' => $aClub['id'],
				'club_id' => $aClub['club_id'],
				'club_name' => $aClub['club_name'],
				'qianzhang' => $aClub['qianzhang'],
				'duizhangfangfa' => $aClub['duizhangfangfa'],
				'paiju_fee' => $aClub['paiju_fee'],
				'club_is_clean' => 0,
				'zhang_dan' => 0,
				'zhanji' => 0,
				'baoxian_heji' => 0,
				'baoxian_beichou' => 0,
				'hui_zhong' => 0,
				'club_zhang_dan_list' => [],
			];
			if(isset($aClubPaijuDataZhangDanList[$aClub['club_id']])){
				foreach($aClubPaijuDataZhangDanList[$aClub['club_id']] as $v){
					if(!isset($aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']])){
						$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']] = [
							'paiju_id' => $v['paiju_id'],
							'paiju_name' => $v['paiju_name'],
							'club_id' => $v['club_id'],
							'paiju_fee' => $v['paiju_fee'],
							'club_is_clean' => $v['club_is_clean'],
							'zhang_dan' => 0,
							'zhanji' => 0,
							'baoxian_heji' => 0,
							'baoxian_beichou' => 0,
						];
					}
					$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']]['zhang_dan'] += $v['zhang_dan'];
					$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']]['zhanji'] += $v['zhanji'];
					$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']]['baoxian_heji'] += $v['baoxian_heji'];
					$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']]['baoxian_beichou'] += $v['baoxian_beichou'];
					$aClubZhangDanList[$aClub['club_id']]['club_is_clean'] = $v['club_is_clean'];
					$aClubZhangDanList[$aClub['club_id']]['zhanji'] += $v['zhanji'];
					$aClubZhangDanList[$aClub['club_id']]['baoxian_heji'] += $v['baoxian_heji'];
					$aClubZhangDanList[$aClub['club_id']]['baoxian_beichou'] += $v['baoxian_beichou'];
					//$aClubZhangDanList[$aClub['club_id']]['zhang_dan'] += $v['zhang_dan'];
				}
			}else{
				//即使俱乐部没有牌局数据也要算上桌子费
				
			}
			//账单值与自己俱乐部联盟账单值相反
			if(!$aClubZhangDanList[$aClub['club_id']]['club_is_clean']){
				$aClubZhangDanList[$aClub['club_id']]['zhang_dan'] = -Calculate::calculateZhangDan($aClubZhangDanList[$aClub['club_id']]['zhanji'], $aClubZhangDanList[$aClub['club_id']]['baoxian_heji'], $aClubZhangDanList[$aClub['club_id']]['paiju_fee'], $aClubZhangDanList[$aClub['club_id']]['baoxian_beichou'], $aClubZhangDanList[$aClub['club_id']]['duizhangfangfa'], $this->choushui_shuanfa);
			}
			$aClubZhangDanList[$aClub['club_id']]['hui_zhong'] = $aClubZhangDanList[$aClub['club_id']]['zhang_dan'] + $aClubZhangDanList[$aClub['club_id']]['qianzhang'];
			$totalZhanDan += $aClubZhangDanList[$aClub['club_id']]['hui_zhong'];
		}
		
		//如果没有新账单就不显示牌局记录列表了
		if(!$totalZhanDan){
			$aPaijuZhangDanList = [];
		}
		return [
			'totalZhanDan' => $totalZhanDan,
			'aClubZhangDanList' => $aClubZhangDanList,
			'aPaijuZhangDanList' => $aPaijuZhangDanList,
		];
	}
	
	/**
	 *	联盟俱乐部清账
	 */
	public function clubQinZhang($mLianmeng, $aZhangDan){
		if(!$aZhangDan){
			return false;
		}
		foreach($aZhangDan as $value){
			//更新俱乐部账单牌局已清账状态
			$sql = 'UPDATE ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . LianmengClub::tableName() . ' AS `t2` ON `t1`.`club_id`=`t2`.`club_id` SET `t1`.`club_is_clean`=1 WHERE `t1`.`user_id`=' . $this->id . ' AND `t1`.`club_id`=' . $value['club_id'] . ' AND `t1`.`club_is_clean`=0 AND `t2`.`lianmeng_id`=' . $mLianmeng->id . ' AND `t2`.`is_delete`=0';
			Yii::$app->db->createCommand($sql)->execute();
			//更新俱乐部欠账
			$mLianmengClub = LianmengClub::findOne(['user_id' => $this->id, 'lianmeng_id' => $mLianmeng->id, 'club_id' => $value['club_id']]);
			if(!$mLianmengClub){
				return false;
			}
			if($value['zhang_dan']){
				$mLianmengClub->set('qianzhang', ['add', $value['zhang_dan']]);
				$mLianmengClub->save();
			}
		}
	
		return true;
	}
	
	
	public function checkAddNewPlayer($paijuId){
		if(!$paijuId){
			return false;
		}
		$aPlayerList = [];
		$aPaijuDataList = $this->getPaijuDataList($paijuId);
		if(!$aPaijuDataList){
			return false;
		}
		foreach($aPaijuDataList as $aPaijuData){
			if(!$aPaijuData['status']){
				array_push($aPlayerList, [
					'player_id' => $aPaijuData['player_id'],
					'player_name' => $aPaijuData['player_name'],
				]);
			}
		}
		if($aPlayerList){
			Player::checkAddNewPlayer($this->id, $aPlayerList);
		}
		return true;
	}
	
}