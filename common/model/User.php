<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

class User extends \common\lib\DbOrmModel implements IdentityInterface{
	protected $_aEncodeFields = ['cache_data' => 'json'];
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
	
	const DEFAULT_SAVE_CODE_REMAIN_TIMES = 3;
	
	private $_aUnJiaoBanPaijuChouShuiList = false;
	private $_aUnJiaoBanPaijuTotalStatistic = false;
	private $_aUnJiaoBanPaijuTotalStatistic1 = false;
	
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
	
	private static function createSaveCode(){
		return mt_rand(100000, 999999);
	}
	
	public static function register($aData){
		$aData['save_code'] = static::createSaveCode();
		$aData['save_code_remain_times'] = static::DEFAULT_SAVE_CODE_REMAIN_TIMES;
		$id = static::insert($aData);
		$aData['id'] = $id;
		$mUser = static::toModel($aData);
		static::_initUserData($mUser);
		
		return $mUser;
	}
	
	private static function _initUserData($mUser){
		MoneyType::addRecord([
			'user_id' => $mUser->id,
			'pay_type' => '微信',
			'money' => 0,
			'create_time' => NOW_TIME,
		]);
		MoneyType::addRecord([
			'user_id' => $mUser->id,
			'pay_type' => '支付宝',
			'money' => 0,
			'create_time' => NOW_TIME,
		]);
		MoneyType::addRecord([
			'user_id' => $mUser->id,
			'pay_type' => '银行卡',
			'money' => 0,
			'create_time' => NOW_TIME,
		]);
		
		MoneyOutPutType::addRecord([
			'user_id' => $mUser->id,
			'out_put_type' => '奖励',
			'money' => 0,
			'create_time' => NOW_TIME,
		]);
		MoneyOutPutType::addRecord([
			'user_id' => $mUser->id,
			'out_put_type' => '伙食',
			'money' => 0,
			'create_time' => NOW_TIME,
		]);
		MoneyOutPutType::addRecord([
			'user_id' => $mUser->id,
			'out_put_type' => '杂费',
			'money' => 0,
			'create_time' => NOW_TIME,
		]);
	}
	
	public static function getClientLoginToken(){
		return \umeworld\lib\Cookie::get('login_token');
	}
	
	public function operateLog($type, $aDataJson = []){
		OperateLog::addRecord([
			'user_id' => $this->id,
			'type' => $type,
			'data_json' => $aDataJson,
			'create_time' => NOW_TIME,
		]);
	}
	
	public static function getVipList(){
		return [
			1 => '黄金会员',
			2 => '钻石会员',
			3 => '黑金会员',
		];
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
	
	public function isYellowGoldVip(){
		return $this->vip_level == 1 && $this->vip_expire_time > NOW_TIME;
	}
	
	public function isDiamondVip(){
		return $this->vip_level == 2 && $this->vip_expire_time > NOW_TIME;
	}
	
	public function isBlackGoldVip(){
		return $this->vip_level == 3 && $this->vip_expire_time > NOW_TIME;
	}
	
	public function getBindClubLimitCount(){
		$count = 0;
		if($this->isYellowGoldVip()){
			$count = 1;
		}elseif($this->isDiamondVip()){
			$count = PHP_INT_MAX;
		}elseif($this->isBlackGoldVip()){
			$count = PHP_INT_MAX;
		}
		return $count;
	}
	
	public function hasLianmengHostDuiZhangFunction(){
		if($this->isYellowGoldVip()){
			return false;
		}elseif($this->isDiamondVip()){
			return false;
		}elseif($this->isBlackGoldVip()){
			return true;
		}
		return false;
	}
	
	public function clearUserData(){
		$sql = 'DELETE FROM ' . Agent::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . Club::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . ExcelFile::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . FenchengSetting::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . ImportData::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . KerenBenjin::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . Lianmeng::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . LianmengClub::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . MoneyOutPutType::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . MoneyType::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . Paiju::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . Player::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . OperateLog::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . HostLianmeng::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . HostLianmengClub::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . AgentQinzhangRecord::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . AgentBaoxianQinzhangRecord::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'DELETE FROM ' . BaoxianFenchengSetting::tableName() . ' WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		/*$sql = 'DELETE FROM ' . User::tableName() . ' WHERE `id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();*/
		$this->set('choushui_ajust_value', 0);
		$this->set('baoxian_ajust_value', 0);
		$this->set('agent_fencheng_ajust_value', 0);
		$this->set('agent_baoxian_fencheng_ajust_value', 0);
		$this->set('lianmeng_zhongzhang_ajust_value', 0);
		$this->set('cache_data', '');
		$this->set('last_save_code_error_time', 0);
		$this->set('save_code_remain_times', static::DEFAULT_SAVE_CODE_REMAIN_TIMES);
		$this->set('qibu_zhanji', 0);
		$this->set('is_active', 0);
		//$this->set('active_time', 0);
		$this->save();
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
		foreach($aList as $key => $value){
			if(isset($value['save_code']) && !$value['save_code']){
				$saveCode = static::createSaveCode();
				$aList[$key]['save_code'] = $saveCode;
				$mUser = static::toModel($value);
				$mUser->set('save_code', $saveCode);
				$mUser->save();
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
		return (int)$aResult[0]['total_money'];
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
	
	public function getTotalZhengKerenBenjiMoney(){
		$sql = 'SELECT SUM(`benjin`) as `total_money` FROM ' . KerenBenjin::tableName() . ' WHERE `user_id`=' . $this->id . ' AND `benjin`>0 AND `is_delete`=0';
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
		//$sql = 'SELECT SUM(`zhanji`) as `total_zhanji` FROM ' . ImportData::tableName() . ' WHERE `user_id`=' . $this->id . ' AND `club_id` IN(' . implode(',', $aClubId) . ')';
		$sql = 'SELECT SUM(t1.`zhanji`) AS `total_zhanji` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t1`.`status`=1 AND `t2`.`status`=' . Paiju::STATUS_DONE . ' AND `club_id` IN(' . implode(',', $aClubId) . ')';
		//debug($sql);
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return (int)$aResult[0]['total_zhanji'];
	}
	
	public function getAgentList(){
		$aList = Agent::getList(['user_id' => $this->id, 'is_delete' => 0]);
		foreach($aList as $key => $value){
			$aList[$key]['fencheng_setting'] = $this->getFenchengListSetting($value['id']);
			$aList[$key]['baoxian_fencheng_setting'] = $this->getBaoxianFenchengListSetting($value['id']);
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
				if($value['zhuozi_jibie'] == $zhuoziJibie){
					$flag = true;
					break;
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
	
	public function getBaoxianFenchengListSetting($agentId){
		$aFenchengConfigList = BaoxianFenchengSetting::getFenchengConfigList();
		$aList = BaoxianFenchengSetting::findAll(['user_id' => $this->id, 'agent_id' => $agentId, 'zhuozi_jibie' => $aFenchengConfigList]);
		$aInsertList = [];
		foreach($aFenchengConfigList as $zhuoziJibie){
			$flag = false;
			foreach($aList as $value){
				if($value['zhuozi_jibie'] == $zhuoziJibie){
					$flag = true;
					break;
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
			BaoxianFenchengSetting::bathInsertData($aInsertList);
		}
		return BaoxianFenchengSetting::findAll(['user_id' => $this->id, 'agent_id' => $agentId, 'zhuozi_jibie' => $aFenchengConfigList]);
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
	
	public function getLastPaijuListCount($aParam = ['status' => [Paiju::STATUS_UNDO, Paiju::STATUS_DONE]], $aOrder = ['`t1`.`end_time`' => SORT_DESC]){
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
		];
		return Paiju::getCount($aCondition, $aControll);
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
		/********************这里非常重要*********************/
		//先检查客人是否存在，不存在则创建
		$aPlayerList = [];
		foreach($aList as $aPaijuData){
			if(!$aPaijuData['status']){
				array_push($aPlayerList, [
					'player_id' => $aPaijuData['player_id'],
					'player_name' => $aPaijuData['player_name'],
				]);
			}
		}
		if(!$isAllRecordData && $aPlayerList){
			Player::checkAddNewPlayer($this->id, $aPlayerList, 1);
		}
		/********************这里非常重要*********************/
		$aList = ImportData::getList($aCondition, $aControll);
		$aReturnList = [];
		if(!$isAllRecordData){
			//过滤掉删除的客人记录
			foreach($aList as $key => $value){
				if(isset($value['keren_benjin_info']) && $value['keren_benjin_info'] && isset($value['keren_benjin_info']['is_delete']) && !$value['keren_benjin_info']['is_delete']){
					array_push($aReturnList, $value);
				}else{
					//恢复玩家，创建新的钱包ID start
					$kerenId = KerenBenjin::addRecord(['user_id' => $value['user_id'], 'keren_bianhao' => KerenBenjin::getNextKerenbianhao($value['user_id']), 'create_time' => NOW_TIME]);
					$mKerenBenjin = KerenBenjin::findOne($kerenId);
					$mPlayer = Player::findOne(['user_id' => $value['user_id'], 'player_id' => $value['player_id']]);
					if($mPlayer){
						$mPlayer->set('keren_bianhao', $mKerenBenjin->keren_bianhao);
						$mPlayer->set('is_delete', 0);
						$mPlayer->save();
					}
					//恢复玩家，创建新的钱包ID end
					$value['keren_benjin_info'] = $mKerenBenjin->toArray();
					$value['keren_benjin_info']['player_id'] = $value['player_id'];
					$jiesuanValue = Calculate::paijuPlayerJiesuanValue($value['zhanji'], $mKerenBenjin->ying_chou, $mKerenBenjin->shu_fan, $this->qibu_choushui, $this->choushui_shuanfa);
					$floatJiesuanValue = Calculate::paijuPlayerJiesuanValue($value['zhanji'], $mKerenBenjin->ying_chou, $mKerenBenjin->shu_fan, $this->qibu_choushui, $this->choushui_shuanfa, false);
					$value['jiesuan_value'] = $jiesuanValue;
					$value['float_jiesuan_value'] = $floatJiesuanValue;
					if($value['status']){
						//如果该记录已结算，显示最新本金
						$value['new_benjin'] = $mKerenBenjin->benjin;
					}else{
						$value['new_benjin'] = $mKerenBenjin->benjin + $value['jiesuan_value'];
					}
					array_push($aReturnList, $value);
				}
			}
		}else{
			$aReturnList = $aList;
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
		$this->getDefaultLianmengId();
		return Lianmeng::findAll(['user_id' => $this->id, 'is_delete' => 0]);
	}
	
	public function getDefaultHostLianmengId(){
		$mLianmeng = HostLianmeng::findOne(['user_id' => $this->id, 'is_delete' => 0]);
		if(!$mLianmeng){
			return 0;
			/*return Lianmeng::addRecord([
				'user_id' => $this->id,
				'name' => '默认联盟',
				'create_time' => NOW_TIME,
			]);*/
		}
		return $mLianmeng->id;
	}
	
	public function getHostLianmengList(){
		return HostLianmeng::findAll(['user_id' => $this->id, 'is_delete' => 0]);
	}
	
	public function checkIsJieShuanAllPaijuRecord($paijuId){
		$flag = true;
		$aPaijuDataList = $this->getPaijuDataList($paijuId);
		if($aPaijuDataList){
			foreach($aPaijuDataList as $aPaijuData){
				if(!$aPaijuData['status']){
					$flag = false;
					break;
				}
			}
		}
		return $flag;
	}
	
	/**
	 *	统计未交班的牌局总抽水、总保险、上桌人数、差额、交班转出、客人总本金
	 */
	public function getUnJiaoBanPaijuTotalStatistic(){
		if($this->_aUnJiaoBanPaijuTotalStatistic !== false){
			return $this->_aUnJiaoBanPaijuTotalStatistic;
		}
		$aUnJiaoBanPaijuTotalStatistic = $this->_getUnJiaoBanPaijuTotalStatistic();
		$aUnJiaoBanPaijuTotalStatistic['imbalanceMoney'] = $this->getImbalanceMoney();
		$aUnJiaoBanPaijuTotalStatistic['jiaoBanZhuanChuMoney'] = $this->getJiaoBanZhuanChuMoney();
		$aUnJiaoBanPaijuTotalStatistic['kerenTotalBenjinMoney'] = $this->getTotalKerenBenjiMoney();
		$aUnJiaoBanPaijuTotalStatistic['zhengKerenTotalBenjinMoney'] = $this->getTotalZhengKerenBenjiMoney();
		$aUnJiaoBanPaijuTotalStatistic['kerenTotalQianKuanMoney'] = $this->getTotalQianKuanMoney();
		$aUnJiaoBanPaijuTotalStatistic['kerenTotalShuYin'] = $this->getTotalShuYin();
		
		$this->_aUnJiaoBanPaijuTotalStatistic = $aUnJiaoBanPaijuTotalStatistic;
		return $aUnJiaoBanPaijuTotalStatistic;
	}
	
	/**
	 *	统计未交班的牌局总抽水、总保险、上桌人数,实际抽水
	 */
	private function _getUnJiaoBanPaijuTotalStatistic(){
		if($this->_aUnJiaoBanPaijuTotalStatistic1 !== false){
			return $this->_aUnJiaoBanPaijuTotalStatistic1;
		}
		$aClubId = [];
		$aClubList = $this->getUserClubList();
		if($aClubList){
			$aClubId = ArrayHelper::getColumn($aClubList, 'club_id');
		}else{
			return [
				'zhongChouShui' => 0,
				'zhongBaoXian' => 0,
				'shangZhuoRenShu' => 0,
				'shijiChouShui' => 0,
			];
		}
		//获取总抽水
		$zhongChouShui = ImportData::getUserUnJiaoBanPaijuZhongChouShui($this->id, $aClubId) + $this->choushui_ajust_value;
		//获取总保险
		$zhongBaoXian = -ImportData::getUserUnJiaoBanPaijuZhongBaoXian($this->id, $aClubId) + $this->baoxian_ajust_value;
		//总保险减去代理清账额
		$totalQinzhangValue = $this->getTotalAgentBaoxianQinzhangValue();
		$zhongBaoXian -= $totalQinzhangValue;
		//上桌人数
		$shangZhuoRenShu = ImportData::getUserUnJiaoBanPaijuShangZhuoRenShu($this->id, $aClubId);
		//实际抽水
		//$shijiChouShui = $this->getShijiChouShui();
		$shijiChouShui = $this->getShijiChouShuiByType();
		$this->_aUnJiaoBanPaijuTotalStatistic1 = [
			'zhongChouShui' => $zhongChouShui,
			'zhongBaoXian' => $zhongBaoXian,
			'shangZhuoRenShu' => $shangZhuoRenShu,
			'shijiChouShui' => $shijiChouShui,
		];
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
	
	/**
	 *	统计未交班代理清账总额
	 */
	public function getTotalAgentQinzhangValue(){
		$sql = 'SELECT SUM(`qinzhang_value`) AS `total_qinzhang_value` from ' . AgentQinzhangRecord::tableName() . ' where `user_id`=' . $this->id . ' AND `is_show`=1';
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return (int)$aResult[0]['total_qinzhang_value'];
	}
	
	/**
	 *	统计未交班代理保险清账总额
	 */
	public function getTotalAgentBaoxianQinzhangValue(){
		$sql = 'SELECT SUM(`qinzhang_value`) AS `total_qinzhang_value` from ' . AgentBaoxianQinzhangRecord::tableName() . ' where `user_id`=' . $this->id . ' AND `is_show`=1';
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return (int)$aResult[0]['total_qinzhang_value'];
	}
	
	/**
	 *	统计实际抽水，浮点数计算后取整
	 */
	public function getShijiChouShuiByType($returnInt = true){
		$shijiChouShui = $this->choushui_ajust_value;
		$aChouShuiList = $this->getUnJiaoBanPaijuChouShuiList();
		foreach($aChouShuiList as $aChouShui){
			$shijiChouShui += $aChouShui['float_shiji_choushui_value'];
		}
		//实际抽水减去代理清账额
		$totalQinzhangValue = $this->getTotalAgentQinzhangValue();
		$shijiChouShui -= $totalQinzhangValue;
		if(!$returnInt){
			return $shijiChouShui;
		}
		return Calculate::getIntValueByChoushuiShuanfa($shijiChouShui, $this->choushui_shuanfa);
	}
	
	private function _getUnJiaoBanPaijuChouShuiDataListWithLianmengInfoOld(){
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
		
		//$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`float_choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t2`.`lianmeng_id`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t3`.`user_id`=' . $this->id . ' AND `t4`.`user_id`=' . $this->id . ' AND `t2`.`status`=' . Paiju::STATUS_DONE . ' AND `t1`.`status`=1 AND `t3`.`is_delete`=0' . $clubIdWhere;
		$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`float_choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t2`.`lianmeng_id`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng`,`t5`.`ying_fee`,`t5`.`shu_fee` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` LEFT JOIN ' . KerenBenjin::tableName() . ' AS `t5` ON `t3`.`keren_bianhao`=`t5`.`keren_bianhao` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t3`.`user_id`=' . $this->id . ' AND `t4`.`user_id`=' . $this->id . ' AND `t5`.`user_id`=' . $this->id . ' AND `t2`.`status`=' . Paiju::STATUS_DONE . ' AND `t1`.`status`=1 AND `t3`.`is_delete`=0' . $clubIdWhere;
		
		$aList = Yii::$app->db->createCommand($sql)->queryAll();
		
		return $aList;
		
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
		//*******************//
		$sql = 'SELECT * FROM ' . Paiju::tableName() . ' WHERE `user_id`=' . $this->id . ' AND `status`=' . Paiju::STATUS_DONE;
		$aPaijuList = Yii::$app->db->createCommand($sql)->queryAll();
		$aPaijuId = ArrayHelper::getColumn($aPaijuList, 'id');
		$importDataSql = 'SELECT * FROM ' . ImportData::tableName() . ' WHERE `user_id`=' . $this->id . ' AND `status`=1';
		if($aClubId){
			$importDataSql .= ' AND `club_id` IN(' . implode(',', $aClubId) . ')';
		}
		if($aPaijuId){
			$importDataSql .= ' AND `paiju_id` IN(' . implode(',', $aPaijuId) . ')';
		}
		//*******************//
		//$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`float_choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t2`.`lianmeng_id`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t3`.`user_id`=' . $this->id . ' AND `t4`.`user_id`=' . $this->id . ' AND `t2`.`status`=' . Paiju::STATUS_DONE . ' AND `t1`.`status`=1 AND `t3`.`is_delete`=0' . $clubIdWhere;
		$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`float_choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t2`.`lianmeng_id`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng`,`t5`.`ying_fee`,`t5`.`shu_fee` FROM (' . $importDataSql . ') AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` LEFT JOIN ' . KerenBenjin::tableName() . ' AS `t5` ON `t3`.`keren_bianhao`=`t5`.`keren_bianhao` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t3`.`user_id`=' . $this->id . ' AND `t4`.`user_id`=' . $this->id . ' AND `t5`.`user_id`=' . $this->id . ' AND `t2`.`status`=' . Paiju::STATUS_DONE . ' AND `t1`.`status`=1 AND `t3`.`is_delete`=0' . $clubIdWhere;
		
		$aList = Yii::$app->db->createCommand($sql)->queryAll();
		
		return $aList;
		
	}
	
	private function _getUnJiaoBanPaijuList(){
		$aLianmengList = $this->getLianmengList();
		$aLianmengId = [];
		if($aLianmengList){
			$aLianmengId = ArrayHelper::getColumn($aLianmengList, 'id');
		}else{
			return [];
		}
		$aCondition = [
			'user_id' => $this->id,
			'status' => Paiju::STATUS_DONE,
		];
		if($aLianmengId){
			$aCondition['lianmeng_id'] = $aLianmengId;
		}
		$aPaijuList = Paiju::findAll($aCondition);
		foreach($aPaijuList as $key => $value){
			foreach($aLianmengList as $aLianmeng){
				if($aLianmeng['id'] == $value['lianmeng_id']){
					$aPaijuList[$key]['lianmeng_info'] = $aLianmeng;
					break;
				}
			}
		}
		return $aPaijuList;
	}
	
	/**
	 *	获取抽水列表
	 */
	public function getUnJiaoBanPaijuChouShuiList(){
		if($this->_aUnJiaoBanPaijuChouShuiList !== false){
			return $this->_aUnJiaoBanPaijuChouShuiList;
		}
		$aResult = $this->_getUnJiaoBanPaijuChouShuiDataListWithLianmengInfo();
		$aReturnList = [];
		
		foreach($aResult as $value){
			$taifee = 0;
			if(abs($value['zhanji']) >= $this->qibu_taifee){
				if($value['zhanji'] > 0){
					$taifee = $value['ying_fee'];
				}else{
					$taifee = -$value['shu_fee'];
				}
			}
			if(!isset($aReturnList[$value['paiju_id']])){
				$aReturnList[$value['paiju_id']] = [
					'paiju_name' => $value['paiju_name'],
					'zhanji' => 0,
					'choushui_value' => 0,
					'lianmeng_butie' => 0,
					'float_lianmeng_butie' => 0,
					'shiji_choushui_value' => 0,
					'float_shiji_choushui_value' => 0,
					'float_choushui_value' => 0,
					'baoxian_heji' => 0,
					'paiju_fee' => $value['paiju_fee'],
					'taifee' => 0,
					'duizhangfangfa' => $value['duizhangfangfa'],
				];
			}
			$aReturnList[$value['paiju_id']]['taifee'] += $taifee;
			$aReturnList[$value['paiju_id']]['zhanji'] += $value['zhanji'];
			$aReturnList[$value['paiju_id']]['baoxian_heji'] += $value['baoxian_heji'];
			$aReturnList[$value['paiju_id']]['choushui_value'] += $value['choushui_value'];
			$aReturnList[$value['paiju_id']]['float_choushui_value'] += $value['float_choushui_value'];
			/*$mImportData = ImportData::findOne($value['id']);
			$mKerenBenjin = $mImportData->getMPlayer()->getMKerenBenjin();
			$floatJiesuanValue = Calculate::paijuPlayerJiesuanValue($value['zhanji'], $mKerenBenjin->ying_chou, $mKerenBenjin->shu_fan, $this->qibu_choushui, $this->choushui_shuanfa, false);
			$floatChoushuiValue = $value['zhanji'] - $floatJiesuanValue;
			$mImportData->set('float_choushui_value', $floatChoushuiValue);
			$mImportData->save();
			$aReturnList[$value['paiju_id']]['float_choushui_value'] += $floatChoushuiValue;*/
		}
		foreach($aReturnList as $paijuId => $v){
			$lianmengButie = Calculate::calculateLianmengButie($v['zhanji'], $v['baoxian_heji'], $v['duizhangfangfa'], $this->choushui_shuanfa);
			$floatLianmengButie = Calculate::calculateLianmengButie($v['zhanji'], $v['baoxian_heji'], $v['duizhangfangfa'], $this->choushui_shuanfa, false);
			$aReturnList[$paijuId]['lianmeng_butie'] = $lianmengButie;
			$aReturnList[$paijuId]['float_lianmeng_butie'] = $floatLianmengButie;
			$aReturnList[$paijuId]['shiji_choushui_value'] = Calculate::calculateShijiChouShuiValue($v['choushui_value'], $lianmengButie, $v['paiju_fee'], $v['taifee'], $this->choushui_shuanfa);
			//$aReturnList[$paijuId]['float_shiji_choushui_value'] = Calculate::calculateShijiChouShuiValue($v['float_choushui_value'], $floatLianmengButie, $v['paiju_fee'], $v['taifee'], $this->choushui_shuanfa, false);
			$aReturnList[$paijuId]['float_shiji_choushui_value'] = Calculate::calculateShijiChouShuiValue($v['choushui_value'], $floatLianmengButie, $v['paiju_fee'], $v['taifee'], $this->choushui_shuanfa, false);
		}
		foreach($aReturnList as $paijuId => $v){
			$aReturnList[$paijuId]['int_float_shiji_choushui_value'] = Calculate::getIntValueByChoushuiShuanfa($aReturnList[$paijuId]['float_shiji_choushui_value'], $this->choushui_shuanfa);
		}
		//组装上空账单
		$aUnJiaoBanPaijuIdList = $this->_getUnJiaoBanPaijuList();
		foreach($aUnJiaoBanPaijuIdList as $k => $v){
			if(!isset($aReturnList[$v['id']])){
				$aReturnList[$v['id']] = [
					'paiju_name' => $v['paiju_name'],
					'zhanji' => 0,
					'choushui_value' => 0,
					'lianmeng_butie' => 0,
					'float_lianmeng_butie' => 0,
					'shiji_choushui_value' => -$v['lianmeng_info']['paiju_fee'],
					'float_shiji_choushui_value' => -$v['lianmeng_info']['paiju_fee'],
					'float_choushui_value' => 0,
					'baoxian_heji' => 0,
					'paiju_fee' =>  $v['lianmeng_info']['paiju_fee'],
					'taifee' =>  0,
					'duizhangfangfa' =>  $v['lianmeng_info']['duizhangfangfa'],
					'int_float_shiji_choushui_value' => -$v['lianmeng_info']['paiju_fee'],
				];
			}
		}
		//排序
		if($aReturnList){
			$aPaijuId = array_keys($aReturnList);
			$aPaijuList = Paiju::findAll(['id' => $aPaijuId], ['id', 'end_time'], 0, 0, ['end_time' => SORT_DESC]);
			$aSortList = [];
			foreach($aPaijuList as $aPaiju){
				$aReturnList[$aPaiju['id']]['paiju_id'] = $aPaiju['id'];
				$aSortList[] = $aReturnList[$aPaiju['id']];
			}
			$aReturnList = $aSortList;
		}
		$this->_aUnJiaoBanPaijuChouShuiList = $aReturnList;
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
		$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t3`.`user_id`=' . $this->id . ' AND `t4`.`user_id`=' . $this->id . ' AND `t2`.`status`=' . Paiju::STATUS_DONE . ' AND `t1`.`status`=1 AND `t3`.`is_delete`=0' . $clubIdWhere;
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
					'float_baoxian_beichou' => 0,
					'shiji_baoxian' => 0,
					'float_shiji_baoxian' => 0,
				];
			}
			$aReturnList[$value['paiju_id']]['baoxian_heji'] += $value['baoxian_heji'];
			$baoxianBeichou = Calculate::calculateBaoxianBeichou($value['baoxian_heji'], $value['baoxian_choucheng'], $this->choushui_shuanfa);
			$floatBaoxianBeichou = Calculate::calculateBaoxianBeichou($value['baoxian_heji'], $value['baoxian_choucheng'], $this->choushui_shuanfa, false);
			$aReturnList[$value['paiju_id']]['baoxian_beichou'] += $baoxianBeichou;
			$aReturnList[$value['paiju_id']]['float_baoxian_beichou'] += $floatBaoxianBeichou;
			$aReturnList[$value['paiju_id']]['shiji_baoxian'] += Calculate::calculateShijiBaoXian($value['baoxian_heji'], $baoxianBeichou, $this->choushui_shuanfa);
			$aReturnList[$value['paiju_id']]['float_shiji_baoxian'] += Calculate::calculateShijiBaoXian($value['baoxian_heji'], $floatBaoxianBeichou, $this->choushui_shuanfa, false);
		}
		foreach($aReturnList as $key => $value){
			$aReturnList[$key]['baoxian_heji'] = -$aReturnList[$key]['baoxian_heji'];
		}
		//组装上空账单
		$aUnJiaoBanPaijuIdList = $this->_getUnJiaoBanPaijuList();
		foreach($aUnJiaoBanPaijuIdList as $k => $v){
			if(!isset($aReturnList[$v['id']])){
				$aReturnList[$v['id']] = [
					'paiju_name' => $v['paiju_name'],
					'baoxian_beichou' => 0,
					'baoxian_heji' => 0,
					'float_baoxian_beichou' => 0,
					'float_shiji_baoxian' => 0,
					'shiji_baoxian' => 0,
				];
			}
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
		
		$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,COUNT(*) AS `shang_zhuo_ren_shu` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t3`.`user_id`=' . $this->id . ' AND `t2`.`status`=' . Paiju::STATUS_DONE . ' AND `t1`.`status`=1 AND `t3`.`is_delete`=0' . $clubIdWhere . ' GROUP BY `t1`.`paiju_id`';
		$aReturnList = Yii::$app->db->createCommand($sql)->queryAll();
		
		return $aReturnList;
	}
	
	/**
	 *	获取已结算的并且联盟未清账的牌局结算记录带有联盟信息
	 */
	private function _getAlreadyJieShuanPaijuDataListWithLianmengInfoOld($lianmengId = 0){
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
		$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`club_id`,`t2`.`is_clean`,`t2`.`lianmeng_id`,`t4`.`name` AS `lianmeng_name`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t3`.`user_id`=' . $this->id . ' AND `t4`.`user_id`=' . $this->id . ' AND `t2`.`status`=' . Paiju::STATUS_DONE . ' AND `t2`.`is_clean`=0 AND `t1`.`status`=1 AND `t3`.`is_delete`=0' . $lianmengIdWhere . $clubIdWhere;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		//合并空账单start
		$aPaijuId = ArrayHelper::getColumn($aResult, 'paiju_id');
		$aUnJiaoBanPaijuIdList = $this->_getUnJiaoBanPaijuList();
		$aAllPaijuId = ArrayHelper::getColumn($aUnJiaoBanPaijuIdList, 'id');
		$aEmptyPaijuId = [];
		foreach($aUnJiaoBanPaijuIdList as $aUnJiaoBanPaiju){
			if(!$lianmengId){
				if(!in_array($aUnJiaoBanPaiju['id'], $aPaijuId)){
					array_push($aEmptyPaijuId, $aUnJiaoBanPaiju['id']);
				}
			}else{
				if($lianmengId == $aUnJiaoBanPaiju['lianmeng_id'] && !in_array($aUnJiaoBanPaiju['id'], $aPaijuId)){
					array_push($aEmptyPaijuId, $aUnJiaoBanPaiju['id']);
				}
			}
		}
		if($aEmptyPaijuId){
			$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`club_id`,`t2`.`is_clean`,`t2`.`lianmeng_id`,`t4`.`name` AS `lianmeng_name`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t4`.`user_id`=' . $this->id . ' AND `t2`.`status`=' . Paiju::STATUS_DONE . ' AND `t2`.`is_clean`=0 AND `t1`.`paiju_id` IN (' . implode(',', $aEmptyPaijuId) . ')';
			$aEmptyRecordList = Yii::$app->db->createCommand($sql)->queryAll();
			if($aEmptyRecordList){
				foreach($aEmptyRecordList as $key => $value){
					$aEmptyRecordList[$key]['zhanji'] = 0;
					$aEmptyRecordList[$key]['baoxian_heji'] = 0;
				}
				$aResult = array_merge($aResult, $aEmptyRecordList);
			}
		}
		//合并空账单end
		return $aResult;
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
		//*******************//
		$sql = 'SELECT * FROM ' . Paiju::tableName() . ' WHERE `user_id`=' . $this->id . ' AND `is_clean`=0 AND `status`=' . Paiju::STATUS_DONE;
		$aPaijuList = Yii::$app->db->createCommand($sql)->queryAll();
		$aPaijuId = ArrayHelper::getColumn($aPaijuList, 'id');
		$importDataSql = 'SELECT * FROM ' . ImportData::tableName() . ' WHERE `user_id`=' . $this->id . ' AND `status`=1';
		if($aClubId){
			$importDataSql .= ' AND `club_id` IN(' . implode(',', $aClubId) . ')';
		}
		if($aPaijuId){
			$importDataSql .= ' AND `paiju_id` IN(' . implode(',', $aPaijuId) . ')';
		}
		//*******************//
		$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`club_id`,`t2`.`is_clean`,`t2`.`lianmeng_id`,`t4`.`name` AS `lianmeng_name`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM (' . $importDataSql . ') AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t3`.`user_id`=' . $this->id . ' AND `t4`.`user_id`=' . $this->id . ' AND `t2`.`status`=' . Paiju::STATUS_DONE . ' AND `t2`.`is_clean`=0 AND `t1`.`status`=1 AND `t3`.`is_delete`=0' . $lianmengIdWhere . $clubIdWhere;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		//合并空账单start
		$aPaijuId = ArrayHelper::getColumn($aResult, 'paiju_id');
		$aUnJiaoBanPaijuIdList = $this->_getUnJiaoBanPaijuList();
		$aAllPaijuId = ArrayHelper::getColumn($aUnJiaoBanPaijuIdList, 'id');
		$aEmptyPaijuId = [];
		foreach($aUnJiaoBanPaijuIdList as $aUnJiaoBanPaiju){
			if(!$lianmengId){
				if(!in_array($aUnJiaoBanPaiju['id'], $aPaijuId)){
					array_push($aEmptyPaijuId, $aUnJiaoBanPaiju['id']);
				}
			}else{
				if($lianmengId == $aUnJiaoBanPaiju['lianmeng_id'] && !in_array($aUnJiaoBanPaiju['id'], $aPaijuId)){
					array_push($aEmptyPaijuId, $aUnJiaoBanPaiju['id']);
				}
			}
		}
		if($aEmptyPaijuId){
			$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`club_id`,`t2`.`is_clean`,`t2`.`lianmeng_id`,`t4`.`name` AS `lianmeng_name`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t4`.`user_id`=' . $this->id . ' AND `t2`.`status`=' . Paiju::STATUS_DONE . ' AND `t2`.`is_clean`=0 AND `t1`.`paiju_id` IN (' . implode(',', $aEmptyPaijuId) . ')';
			$aEmptyRecordList = Yii::$app->db->createCommand($sql)->queryAll();
			if($aEmptyRecordList){
				foreach($aEmptyRecordList as $key => $value){
					$aEmptyRecordList[$key]['zhanji'] = 0;
					$aEmptyRecordList[$key]['baoxian_heji'] = 0;
				}
				$aResult = array_merge($aResult, $aEmptyRecordList);
			}
		}
		//合并空账单end
		return $aResult;
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
					'paiju_fee' => $value['paiju_fee'],
					'baoxian_choucheng' => $value['baoxian_choucheng'],
					'duizhangfangfa' => $value['duizhangfangfa'],
					'baoxian_beichou' => 0,
					'float_baoxian_beichou' => 0,
					'zhang_dan' => 0,
					'float_zhang_dan' => 0,
					'is_clean' => $value['is_clean'],
					'lianmeng_id' => $value['lianmeng_id'],
				];
			}
			$aReturnList[$value['paiju_id']]['zhanji'] += $value['zhanji'];
			$aReturnList[$value['paiju_id']]['baoxian_heji'] += $value['baoxian_heji'];
			//$aReturnList[$value['paiju_id']]['paiju_fee'] += $value['paiju_fee'];
			/*$baoxianBeichou = Calculate::calculateBaoxianBeichou($value['baoxian_heji'], $value['baoxian_choucheng'], $this->choushui_shuanfa);
			$aReturnList[$value['paiju_id']]['baoxian_beichou'] += $baoxianBeichou;
			if(!$value['is_clean']){
				$aReturnList[$value['paiju_id']]['zhang_dan'] += Calculate::calculateZhangDan($value['zhanji'], $value['baoxian_heji'], $value['paiju_fee'], $baoxianBeichou, $value['duizhangfangfa'], $this->choushui_shuanfa);
			}*/
		}
		foreach($aReturnList as $key => $value){
			$aReturnList[$key]['fu_baoxian_heji'] = -$value['baoxian_heji'];
			$baoxianBeichou = Calculate::calculateBaoxianBeichou($value['baoxian_heji'], $value['baoxian_choucheng'], $this->choushui_shuanfa);
			$floatBaoxianBeichou = Calculate::calculateBaoxianBeichou($value['baoxian_heji'], $value['baoxian_choucheng'], $this->choushui_shuanfa, true);
			$aReturnList[$key]['baoxian_beichou'] = $baoxianBeichou;
			$aReturnList[$key]['float_baoxian_beichou'] = $floatBaoxianBeichou;
			if(!$value['is_clean']){
				$aReturnList[$key]['zhang_dan'] = Calculate::calculateZhangDan($value['zhanji'], $value['baoxian_heji'], $value['paiju_fee'], $baoxianBeichou, $value['duizhangfangfa'], $this->choushui_shuanfa);
				$aReturnList[$key]['float_zhang_dan'] = Calculate::calculateZhangDan($value['zhanji'], $value['baoxian_heji'], $value['paiju_fee'], $floatBaoxianBeichou, $value['duizhangfangfa'], $this->choushui_shuanfa, false);
			}
		}
		//排序
		if($aReturnList){
			$aPaijuId = array_keys($aReturnList);
			$aPaijuList = Paiju::findAll(['id' => $aPaijuId], ['id', 'end_time'], 0, 0, ['end_time' => SORT_DESC]);
			$aSortList = [];
			foreach($aPaijuList as $aPaiju){
				$aReturnList[$aPaiju['id']]['paiju_id'] = $aPaiju['id'];
				$aSortList[] = $aReturnList[$aPaiju['id']];
			}
			$aReturnList = $aSortList;
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
					'float_lianmeng_zhong_zhang' => 0,
					'lianmeng_shang_zhuo_ren_shu' => 0,
					'lianmeng_qian_zhang' => $aLianmeng['qianzhang'],
					'lianmeng_zhang_dan' => 0,
					'float_lianmeng_zhang_dan' => 0,
				];
			}
		}
		$aClubId = [];
		$aClubList = $this->getUserClubList();
		if($aClubList){
			$aClubId = ArrayHelper::getColumn($aClubList, 'club_id');
		}
		foreach($aResult as $value){
			//$aReturnList[$value['lianmeng_id']]['lianmeng_zhong_zhang'] += 1;
			if(in_array($value['club_id'], $aClubId)){
				$aReturnList[$value['lianmeng_id']]['lianmeng_shang_zhuo_ren_shu'] += 1;
			}
			//$baoxianBeichou = Calculate::calculateBaoxianBeichou($value['baoxian_heji'], $value['baoxian_choucheng'], $this->choushui_shuanfa);
			/*if(!$value['is_clean']){
				$aLianmengZhangDanDetailList = $this->getLianmengZhangDanDetailList($value['lianmeng_id']);
				foreach($aLianmengZhangDanDetailList as $aValue){
					$aReturnList[$value['lianmeng_id']]['lianmeng_zhang_dan'] += $aValue['zhang_dan'];
				}
				//$aReturnList[$value['lianmeng_id']]['lianmeng_zhang_dan'] += Calculate::calculateZhangDan($value['zhanji'], $value['baoxian_heji'], $value['paiju_fee'], $baoxianBeichou, $value['duizhangfangfa'], $this->choushui_shuanfa);
			}*/
		}
		
		foreach($aReturnList as $lianmengId => $aValue){
			$aLianmengZhangDanDetailList = $this->getLianmengZhangDanDetailList($lianmengId);
			foreach($aLianmengZhangDanDetailList as $aLianmengZhangDanDetail){
				$aReturnList[$lianmengId]['lianmeng_zhang_dan'] += $aLianmengZhangDanDetail['zhang_dan'];
				$aReturnList[$lianmengId]['float_lianmeng_zhang_dan'] += $aLianmengZhangDanDetail['float_zhang_dan'];
			}
			$aReturnList[$lianmengId]['lianmeng_zhong_zhang'] = $aReturnList[$lianmengId]['lianmeng_qian_zhang'] + $aReturnList[$lianmengId]['lianmeng_zhang_dan'];
			$aReturnList[$lianmengId]['float_lianmeng_zhong_zhang'] = $aReturnList[$lianmengId]['lianmeng_qian_zhang'] + $aReturnList[$lianmengId]['float_lianmeng_zhang_dan'];
			$aReturnList[$lianmengId]['int_float_lianmeng_zhong_zhang'] = Calculate::getIntValueByChoushuiShuanfa($aReturnList[$lianmengId]['float_lianmeng_zhong_zhang'], $this->choushui_shuanfa);
		}
		foreach($aReturnList as $lianmengId => $aValue){
			$aReturnList[$lianmengId]['lianmeng_zhang_dan'] = Calculate::getIntValueByChoushuiShuanfa($aReturnList[$lianmengId]['float_lianmeng_zhang_dan'], $this->choushui_shuanfa);
		}
		
		return $aReturnList;
	}
	
	/**
	 *	联盟清账
	 */
	public function qinZhang($mLianmeng, $zhangDan, $aLianmengZhongZhang = [], $aOldLianmengZhangDanDetailList = []){
		$aOldRecord = $mLianmeng->toArray();
		if(!$zhangDan){
			return false;
		}
		//更新账单牌局已清账状态
		$aLianmengZhangDanDetailList = $this->getLianmengZhangDanDetailList($mLianmeng->id);
		if(!$aLianmengZhangDanDetailList){
			return false;
		}
		//$aPaijuId = array_keys($aLianmengZhangDanDetailList);
		$aPaijuId = ArrayHelper::getColumn($aLianmengZhangDanDetailList, 'paiju_id');
		$sql = 'UPDATE ' . Paiju::tableName() . ' SET `is_clean`=1 WHERE `id` IN(' . implode(',', $aPaijuId) . ')';
		Yii::$app->db->createCommand($sql)->execute();
		//更新联盟欠账
		$mLianmeng->set('qianzhang', ['add', $zhangDan]);
		$mLianmeng->save();
		$aNewRecord = $mLianmeng->toArray();
		$this->operateLog(25, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord, 'zhangDan' => $zhangDan, 'aLianmengZhongZhang' => $aLianmengZhongZhang, 'aLianmengZhangDanDetailList' => $aOldLianmengZhangDanDetailList]);
		
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
		//$shijiChouShui = $aUnJiaoBanPaijuTotalStatistic['shijiChouShui'];
		$shijiChouShui = $this->getShijiChouShuiByType(false);
		//$totalChouShui = $aUnJiaoBanPaijuTotalStatistic['zhongChouShui'];
		$totalBaoXian = $aUnJiaoBanPaijuTotalStatistic['zhongBaoXian'];
		//$totalLianmengZhongZhang = $this->getLianmengZhongZhang();
		//$totalLianmengZhongZhang = $this->getLianmengTotalZhongZhang();
		$totalLianmengZhongZhang = $this->getLianmengTotalZhongZhangByType(false);
		//Yii::info('totalMoneyTypeMoney:'.$totalMoneyTypeMoney.';'.'totalOutPutTypeMoney:'.$totalOutPutTypeMoney.';'.'totalKerenBenjin:'.$totalKerenBenjin.';'.'shijiChouShui:'.$shijiChouShui.';'.'totalBaoXian:'.$totalBaoXian.';'.'totalLianmengZhongZhang:'.$totalLianmengZhongZhang.';');
		return Calculate::calculateImbalanceMoney($totalMoneyTypeMoney, $totalOutPutTypeMoney, $totalKerenBenjin, $shijiChouShui, $totalBaoXian, $totalLianmengZhongZhang, $this->choushui_shuanfa);
		//return Calculate::calculateImbalanceMoney($totalMoneyTypeMoney, $totalOutPutTypeMoney, $totalKerenBenjin, $totalChouShui, $totalBaoXian, $totalLianmengZhongZhang);
	}
	
	/**
	 *	获取联盟总账新账单
	 */
	public function getLianmengZhongZhang(){
		$totalLianmengZhongZhang = $this->lianmeng_zhongzhang_ajust_value;
		$aLianmengList = $this->getLianmengList();
		foreach($aLianmengList as $aLianmeng){
			$aLianmengZhangDanDetailList = $this->getLianmengZhangDanDetailList($aLianmeng['id']);
			foreach($aLianmengZhangDanDetailList as $aValue){
				$totalLianmengZhongZhang += $aValue['float_zhang_dan'];
			}
		}
		/*$totalLianmengZhongZhang = $this->lianmeng_zhongzhang_ajust_value;
		$aLianmengZhongZhangList = $this->getLianmengZhongZhangList();
		foreach($aLianmengZhongZhangList as $aLianmengZhongZhang){
			$totalLianmengZhongZhang += $aLianmengZhongZhang['lianmeng_zhong_zhang'];
		}*/
		//return $totalLianmengZhongZhang;
		return Calculate::getIntValueByChoushuiShuanfa($totalLianmengZhongZhang, $this->choushui_shuanfa);
	}

	/**
	 *	获取联盟总账
	 */
	public function getLianmengTotalZhongZhang(){
		$totalLianmengZhongZhang = $this->lianmeng_zhongzhang_ajust_value;
		$aLianmengList = $this->getLianmengZhongZhangList();
		foreach($aLianmengList as $aLianmeng){
			$totalLianmengZhongZhang += $aLianmeng['float_lianmeng_zhong_zhang'];
		}
		/*$totalLianmengZhongZhang = $this->lianmeng_zhongzhang_ajust_value;
		$aLianmengZhongZhangList = $this->getLianmengZhongZhangList();
		foreach($aLianmengZhongZhangList as $aLianmengZhongZhang){
			$totalLianmengZhongZhang += $aLianmengZhongZhang['lianmeng_zhong_zhang'];
		}*/
		//return $totalLianmengZhongZhang;
		return Calculate::getIntValueByChoushuiShuanfa($totalLianmengZhongZhang, $this->choushui_shuanfa);
	}

	/**
	 *	获取联盟总账，有小数
	 */
	public function getLianmengTotalZhongZhangByType($returnInt = true){
		$totalLianmengZhongZhang = $this->lianmeng_zhongzhang_ajust_value;
		$aLianmengList = $this->getLianmengZhongZhangList();
		foreach($aLianmengList as $aLianmeng){
			$totalLianmengZhongZhang += $aLianmeng['float_lianmeng_zhong_zhang'];
		}
		if(!$returnInt){
			return $totalLianmengZhongZhang;
		}
		return Calculate::getIntValueByChoushuiShuanfa($totalLianmengZhongZhang, $this->choushui_shuanfa);
	}

	/**
	 *	获取交班转出值
	 */
	public function getJiaoBanZhuanChuMoney(){
		$totalOutPutTypeMoney = $this->getMoneyOutPutTypeTotalMoney();
		$aUnJiaoBanPaijuTotalStatistic = $this->_getUnJiaoBanPaijuTotalStatistic();
		//$totalChouShui = $aUnJiaoBanPaijuTotalStatistic['zhongChouShui'];
		$totalBaoXian = $aUnJiaoBanPaijuTotalStatistic['zhongBaoXian'];
		//$shijiChouShui = $aUnJiaoBanPaijuTotalStatistic['shijiChouShui'];
		$shijiChouShui = $this->getShijiChouShuiByType(false);
				
		return Calculate::calculateJiaoBanZhuanChuMoney($totalOutPutTypeMoney, $shijiChouShui, $totalBaoXian, $this->choushui_shuanfa);
		//return Calculate::calculateJiaoBanZhuanChuMoney($totalOutPutTypeMoney, $totalChouShui, $totalBaoXian);
	}

	/**
	 *	获取交班转出明细
	 */
	public function getJiaoBanZhuanChuDetail(){
		$totalMoneyTypeMoney = $this->getMoneyTypeTotalMoney();
		$aUnJiaoBanPaijuTotalStatistic = $this->_getUnJiaoBanPaijuTotalStatistic();
		$totalOutPutTypeMoney = $this->getMoneyOutPutTypeTotalMoney();
		$jiaoBanZhuanChuMoney = $this->getJiaoBanZhuanChuMoney();
		$shijiChouShui = $this->getShijiChouShuiByType();
		/*$jiaojieMoney = $totalMoneyTypeMoney - $jiaoBanZhuanChuMoney;*/
		if(!$jiaoBanZhuanChuMoney){
			return false;
		}
		return [
			//'zhongChouShui' => $aUnJiaoBanPaijuTotalStatistic['zhongChouShui'],
			'zhongChouShui' => $shijiChouShui,
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
		$aJiaoBanZhuanChuDetail = $this->getJiaoBanZhuanChuDetail();
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
				if(!$this->qinZhang($mLianmeng, $zhangDan, $aLianmengZhongZhang)){
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
		//5.清空微调值
		$this->set('choushui_ajust_value', 0);
		$this->set('baoxian_ajust_value', 0);
		$this->set('cache_data', '');
		$this->save();
		//6.将代理清账记录设置为不可见
		$sql = 'UPDATE ' . AgentQinzhangRecord::tableName() . ' SET `is_show`=0 WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		$sql = 'UPDATE ' . AgentBaoxianQinzhangRecord::tableName() . ' SET `is_show`=0 WHERE `user_id`=' . $this->id;
		Yii::$app->db->createCommand($sql)->execute();
		//7.记录资金修改日志
		$aMoneyType = $mMoneyType->toArray();
		$this->operateLog(26, ['aMoneyType' => $aMoneyType, 'jiaoBanZhuanChuMoney' => $jiaoBanZhuanChuMoney, 'aJiaoBanZhuanChuDetail' => $aJiaoBanZhuanChuDetail]);
		
		return true;
	}
	
	/**
	 *	获取最后的已交班牌局结束时间
	 */
	public function getLastMaxJiaobanPaijuEndTime(){
		$sql = 'SELECT MAX(`end_time`) AS `max_end_time` FROM ' . Paiju::tableName() . ' WHERE `user_id`=' . $this->id . ' AND `status`=' . Paiju::STATUS_FINISH;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		return (int)$aResult[0]['max_end_time'];
	}

	/**
	 *	获取代理未清账分成列表
	 */
	public function getAgentUnCleanFenChengList($agentId){
		$lastMaxJiaobanPaijuEndTime = $this->getLastMaxJiaobanPaijuEndTime();
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
		$sql = 'SELECT `t7`.* FROM (SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`mangzhu`,`t1`.`player_id`,`t1`.`player_name`,`t1`.`zhanji`,`t1`.`jiesuan_value`,`t3`.`keren_bianhao` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t3`.`user_id`=' . $this->id . ' AND `t2`.`status`>=' . Paiju::STATUS_DONE . ' AND `t1`.`status`=1 AND `t1`.`agent_is_clean`=0 AND `t1`.`end_time`>' . $lastMaxJiaobanPaijuEndTime . ' AND `t3`.`is_delete`=0 ' . $clubIdWhere . ') AS `t7` LEFT JOIN ' . KerenBenjin::tableName() . ' AS `t8` ON `t7`.`keren_bianhao`=`t8`.`keren_bianhao` WHERE `t8`.`agent_id`=' . $agentId;
		$aResult =  Yii::$app->db->createCommand($sql)->queryAll();
		if($aResult){
			Yii::info('agentcleansql:' . $sql);
			Yii::info('agentcleanData:' . json_encode($aResult));
		}
		$aFenchengSetting = ArrayHelper::index($this->getFenchengListSetting($agentId), 'zhuozi_jibie');
		foreach($aResult as $key => $value){
			$yinFan = 0;
			$shuFan = 0;
			if(isset($aFenchengSetting[$value['mangzhu']])){
				$yinFan = (float)$aFenchengSetting[$value['mangzhu']]['yingfan'];
				$shuFan = (float)$aFenchengSetting[$value['mangzhu']]['shufan'];
			}
			$aResult[$key]['yingfan'] = $yinFan;
			$aResult[$key]['shufan'] = $shuFan;
			$aResult[$key]['fencheng'] = Calculate::calculateFenchengMoney($value['zhanji'], $yinFan, $shuFan, $this->choushui_shuanfa);
			$aResult[$key]['float_fencheng'] = Calculate::calculateFenchengMoney($value['zhanji'], $yinFan, $shuFan, $this->choushui_shuanfa, false);
		}
		
		return $aResult;
	}
	
	/**
	 *	获取代理未清账保险分成列表
	 */
	public function getAgentUnCleanBaoxianFenChengList($agentId){
		$lastMaxJiaobanPaijuEndTime = $this->getLastMaxJiaobanPaijuEndTime();
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
		$sql = 'SELECT `t7`.* FROM (SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`mangzhu`,`t1`.`player_id`,`t1`.`player_name`,`t1`.`zhanji`,`t1`.`baoxian_heji`,`t1`.`jiesuan_value`,`t3`.`keren_bianhao` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t3`.`user_id`=' . $this->id . ' AND `t2`.`status`>=' . Paiju::STATUS_DONE . ' AND `t1`.`status`=1 AND `t1`.`agent_baoxian_is_clean`=0 AND `t1`.`end_time`>' . $lastMaxJiaobanPaijuEndTime . ' AND `t3`.`is_delete`=0 ' . $clubIdWhere . ') AS `t7` LEFT JOIN ' . KerenBenjin::tableName() . ' AS `t8` ON `t7`.`keren_bianhao`=`t8`.`keren_bianhao` WHERE `t7`.`baoxian_heji`!=0 AND `t8`.`agent_id`=' . $agentId;
		$aResult =  Yii::$app->db->createCommand($sql)->queryAll();
		if($aResult){
			Yii::info('agentbaoxiancleansql:' . $sql);
			Yii::info('agentbaoxiancleanData:' . json_encode($aResult));
		}
		$aFenchengSetting = ArrayHelper::index($this->getBaoxianFenchengListSetting($agentId), 'zhuozi_jibie');
		foreach($aResult as $key => $value){
			$yinFan = 0;
			$shuFan = 0;
			if(isset($aFenchengSetting[$value['mangzhu']])){
				$yinFan = (float)$aFenchengSetting[$value['mangzhu']]['yingfan'];
				$shuFan = (float)$aFenchengSetting[$value['mangzhu']]['shufan'];
			}
			$aResult[$key]['yingfan'] = $yinFan;
			$aResult[$key]['shufan'] = $shuFan;
			$aResult[$key]['fencheng'] = Calculate::calculateBaoxianFenchengMoney($value['baoxian_heji'], $yinFan, $shuFan, $this->choushui_shuanfa);
			$aResult[$key]['float_fencheng'] = Calculate::calculateBaoxianFenchengMoney($value['baoxian_heji'], $yinFan, $shuFan, $this->choushui_shuanfa, false);
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
	 *	代理保险清账
	 */
	public function agentBaoxianQinZhang($aImportDataId){
		if(!$aImportDataId){
			return false;
		}
		
		$sql = 'UPDATE ' . ImportData::tableName() . ' SET `agent_baoxian_is_clean`=1 WHERE `id` IN(' . implode(',', $aImportDataId) . ')';
		Yii::$app->db->createCommand($sql)->execute();
		
		return true;
	}
	
	/**
	 *	获取联盟主机对账列表
	 */
	public function getLianmengHostDuizhangOld($lianmengId){
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
			//return [];
		}
		$aClubList = ArrayHelper::index($aClubList, 'club_id');
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}
		$lianmengIdWhere = '';
		if($lianmengId){
			//$lianmengIdWhere = ' AND `t2`.`lianmeng_id`=' . $lianmengId;	//旧的方法
			$aLmzjPaijuCreater = $mLianmeng->lmzj_paiju_creater;
			if(!$aLmzjPaijuCreater){
				$aLmzjPaijuCreater = ['D_D'];
			}
			$lianmengIdWhere = ' AND `t1`.`paiju_creater` IN("' . implode('","', $mLianmeng->lmzj_paiju_creater) . '")';	//新的方法
		}
		//$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t1`.`club_id`,`t1`.`club_name`,`t1`.`club_is_clean`,`t2`.`lianmeng_id`,`t4`.`name` AS `lianmeng_name`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . '' . $lianmengIdWhere . $clubIdWhere;
		//$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t1`.`club_id`,`t1`.`club_name`,`t1`.`club_is_clean`,`t2`.`lianmeng_id`,`t4`.`name` AS `lianmeng_name`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t1`.`club_is_clean`=0 AND `t4`.`user_id`=' . $this->id . ' AND `t2`.`status`>=1' . $lianmengIdWhere . $clubIdWhere;
		$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t1`.`club_id`,`t1`.`club_name`,`t1`.`club_is_clean`,`t2`.`lianmeng_id`,`t4`.`name` AS `lianmeng_name`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t1`.`club_is_clean`=0 AND `t4`.`user_id`=' . $this->id . ' AND `t2`.`status`>=1' . $lianmengIdWhere;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		$totalHeduishuzi = 0;
		$totalPaijuCount = 0;
		$totalHeduishuziPaijuCount = 0;
		$aPaijuList = [];
		//补上未添加到联盟的俱乐部的牌局
		if($aResult){
			$aPaijuId = array_unique(ArrayHelper::getColumn($aResult, 'paiju_id'));
			$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t1`.`club_id`,`t1`.`club_name`,`t1`.`club_is_clean`,`t2`.`lianmeng_id`,`t4`.`name` AS `lianmeng_name`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t1`.`club_is_clean`=0 AND `t4`.`user_id`=' . $this->id . ' AND `t2`.`status`>=1' . $lianmengIdWhere . ' AND `t1`.paiju_id IN(' . implode(',', $aPaijuId) . ')';
			//$aOtherClubResult = Yii::$app->db->createCommand($sql)->queryAll();
			$aResult = Yii::$app->db->createCommand($sql)->queryAll();
			
			$aPaijuList = Paiju::getPaijuList(['id' => $aPaijuId], ['width_hedui_shuzi' => true]);
			$totalPaijuCount = count($aPaijuList);
			foreach($aPaijuList as $aPaiju){
				$totalHeduishuzi += $aPaiju['hedui_shuzi'];
				if($aPaiju['hedui_shuzi']){
					$totalHeduishuziPaijuCount += 1;
				}
			}
		}
		
		$aPaijuDataZhangDanList = [];
		$aPaijuZhangDanList = [];
		$totalZhanDan = 0;
		$aTotalClubList = $aClubList;
		$aClubIds = array_keys($aClubList);
		foreach($aResult as $value){
			if(!isset($aTotalClubList[$value['club_id']])){
				$aTotalClubList[$value['club_id']] = [
					'id' => 0,
					'club_id' => $value['club_id'],
					'club_name' => $value['club_name'],
					'qianzhang' => 0,
					'duizhangfangfa' => Lianmeng::DUIZHANGFANGFA_LINDIANJIUQIWU,
					'paiju_fee' => 0,
					'baoxian_choucheng' => $value['baoxian_choucheng'],
				];
			}
			$baoxianChoucheng = $value['baoxian_choucheng'];
			$paijuFee = $value['paiju_fee'];
			$duizhangfangfa = $value['duizhangfangfa'];
			/*foreach($aClubList as $aClub){
				if($aClub['club_id'] == $value['club_id']){
					$baoxianChoucheng = $aClub['baoxian_choucheng'];
					$paijuFee = $aClub['paiju_fee'];
					$duizhangfangfa = $aClub['duizhangfangfa'];
					break;
				}
			}*/
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
				'float_zhang_dan' => 0,
				'zhanji' => $value['zhanji'],
				'baoxian_heji' => $value['baoxian_heji'],
				'baoxian_beichou' => 0,
				'float_baoxian_beichou' => 0,
				'club_is_clean' => $value['club_is_clean'],
			];
			$baoxianBeichou = Calculate::calculateBaoxianBeichou($value['baoxian_heji'], $baoxianChoucheng, $this->choushui_shuanfa);
			$floatBaoxianBeichou = Calculate::calculateBaoxianBeichou($value['baoxian_heji'], $baoxianChoucheng, $this->choushui_shuanfa, false);
			$aTemp['baoxian_beichou'] = $baoxianBeichou;
			$aTemp['float_baoxian_beichou'] = $floatBaoxianBeichou;
			$aPaijuDataZhangDanList[] = $aTemp;
		}
		$aClubPaijuDataZhangDanList = [];
		foreach($aPaijuDataZhangDanList as $value){
			if(!isset($aClubPaijuDataZhangDanList[$value['club_id']])){
				$aClubPaijuDataZhangDanList[$value['club_id']] = [];
			}
			$aClubPaijuDataZhangDanList[$value['club_id']][] = $value;
		}
		///////////////////////////////////////这里好操蛋/////////////////////////////////////////////
		//俱乐部没有牌局记录，则制造假记录，即使俱乐部没有牌局数据也要算上桌子费
		foreach($aTotalClubList as $aClub){
			if(!isset($aClubPaijuDataZhangDanList[$aClub['club_id']])){
				$aClubPaijuDataZhangDanList[$aClub['club_id']] = [];
				//确保有俱乐部的记录,因为要计桌子费用，不然联盟 吃什么
				foreach($aPaijuZhangDanList as $mm){
					$aTempData = [
						'paiju_id' => $mm['paiju_id'],
						'paiju_name' => $mm['paiju_name'],
						'club_id' => $aClub['club_id'],
						'paiju_fee' => $aClub['paiju_fee'],
						'duizhangfangfa' => $aClub['duizhangfangfa'],
						'zhang_dan' => 0,
						'float_zhang_dan' => 0,
						'zhanji' => 0,
						'baoxian_heji' => 0,
						'baoxian_beichou' => 0,
						'float_baoxian_beichou' => 0,
						'club_is_clean' => $mm['club_is_clean'],
					];
					$aClubPaijuDataZhangDanList[$aClub['club_id']][] = $aTempData;
					array_push($aPaijuDataZhangDanList, $aTempData);
				}
			}else{
				//把所有牌局都检查一下，确保有俱乐部的记录,因为要计桌子费用，不然联盟 吃什么
				foreach($aPaijuZhangDanList as $mm){
					$isFind = false;
					foreach($aClubPaijuDataZhangDanList[$aClub['club_id']] as $nn){
						if($mm['paiju_id'] == $nn['paiju_id']){
							$isFind = true;
							break;
						}
					}
					if(!$isFind){
						$aTempData = [
							'paiju_id' => $mm['paiju_id'],
							'paiju_name' => $mm['paiju_name'],
							'club_id' => $aClub['club_id'],
							'paiju_fee' => $aClub['paiju_fee'],
							'duizhangfangfa' => $aClub['duizhangfangfa'],
							'zhang_dan' => 0,
							'float_zhang_dan' => 0,
							'zhanji' => 0,
							'baoxian_heji' => 0,
							'baoxian_beichou' => 0,
							'float_baoxian_beichou' => 0,
							'club_is_clean' => $mm['club_is_clean'],
						];
						$aClubPaijuDataZhangDanList[$aClub['club_id']][] = $aTempData;
						array_push($aPaijuDataZhangDanList, $aTempData);
					}
				}
			}
		}
		///////////////////////////////////////这里好操蛋/////////////////////////////////////////////
		$aClubZhangDanList = [];
		
		foreach($aTotalClubList as $aClub){
			$aClubZhangDanList[$aClub['club_id']] = [
				'lianmeng_club_id' => $aClub['id'],
				'club_id' => $aClub['club_id'],
				'club_name' => $aClub['club_name'],
				'qianzhang' => $aClub['qianzhang'],
				'duizhangfangfa' => $aClub['duizhangfangfa'],
				'paiju_fee' => $aClub['paiju_fee'],
				'club_is_clean' => 0,
				'zhang_dan' => 0,
				'float_zhang_dan' => 0,
				'zhanji' => 0,
				'baoxian_heji' => 0,
				'baoxian_beichou' => 0,
				'float_baoxian_beichou' => 0,
				'hui_zhong' => 0,
				'club_zhang_dan_list' => [],
			];
			foreach($aClubPaijuDataZhangDanList[$aClub['club_id']] as $v){
				if(!isset($aClubZhangDanList[$aClub['club_id']])){
					$aClubZhangDanList[$aClub['club_id']] = [
						'lianmeng_club_id' => $aClub['id'],
						'club_id' => $aClub['club_id'],
						'club_name' => $aClub['club_name'],
						'qianzhang' => $aClub['qianzhang'],
						'duizhangfangfa' => $aClub['duizhangfangfa'],
						'paiju_fee' => $aClub['paiju_fee'],
						'club_is_clean' => 0,
						'zhang_dan' => 0,
						'float_zhang_dan' => 0,
						'zhanji' => 0,
						'baoxian_heji' => 0,
						'baoxian_beichou' => 0,
						'float_baoxian_beichou' => 0,
						'hui_zhong' => 0,
						'club_zhang_dan_list' => [],
					];
				}
				if(!isset($aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']])){
					$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']] = [
						'paiju_id' => $v['paiju_id'],
						'paiju_name' => $v['paiju_name'],
						'club_id' => $v['club_id'],
						'paiju_fee' => $v['paiju_fee'],
						'club_is_clean' => $v['club_is_clean'],
						'duizhangfangfa' => $aClub['duizhangfangfa'],
						'zhang_dan' => 0,
						'float_zhang_dan' => 0,
						'zhanji' => 0,
						'baoxian_heji' => 0,
						'baoxian_beichou' => 0,
					];
				}
				$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']]['zhanji'] += $v['zhanji'];
				$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']]['baoxian_heji'] += $v['baoxian_heji'];
				$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']]['baoxian_beichou'] += $v['baoxian_beichou'];
				$aClubZhangDanList[$aClub['club_id']]['club_is_clean'] = $v['club_is_clean'];
				$aClubZhangDanList[$aClub['club_id']]['zhanji'] += $v['zhanji'];
				$aClubZhangDanList[$aClub['club_id']]['baoxian_heji'] += $v['baoxian_heji'];
				$aClubZhangDanList[$aClub['club_id']]['baoxian_beichou'] += $v['baoxian_beichou'];
				$aClubZhangDanList[$aClub['club_id']]['float_baoxian_beichou'] += $v['float_baoxian_beichou'];
			}
			if(!$aClubZhangDanList[$aClub['club_id']]['club_is_clean']){
				$hasClubZhangDan = false;
				foreach($aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'] as $kk => $vv){
					$hasClubZhangDan = true;
					//账单值与自己俱乐部联盟账单值相反
					//$zhandan = -Calculate::calculateZhangDan($vv['zhanji'], $vv['baoxian_heji'], $vv['paiju_fee'], $vv['baoxian_beichou'], $vv['duizhangfangfa'], $this->choushui_shuanfa);
					$floatZhandan = Calculate::calculateZhangDan($vv['zhanji'], $vv['baoxian_heji'], $vv['paiju_fee'], $vv['baoxian_beichou'], $vv['duizhangfangfa'], $this->choushui_shuanfa, false);
					$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$kk]['zhang_dan'] = Calculate::getIntValueByChoushuiShuanfa($floatZhandan, $this->choushui_shuanfa);
					$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$kk]['float_zhang_dan'] = $floatZhandan;
					//$aClubZhangDanList[$aClub['club_id']]['zhang_dan'] += $zhandan;
					//$aClubZhangDanList[$aClub['club_id']]['float_zhang_dan'] += $floatZhandan;
				}
				if($hasClubZhangDan){
					$clubFloatZhandan = Calculate::calculateZhangDan($aClubZhangDanList[$aClub['club_id']]['zhanji'], $aClubZhangDanList[$aClub['club_id']]['baoxian_heji'], $aClubZhangDanList[$aClub['club_id']]['paiju_fee'], $aClubZhangDanList[$aClub['club_id']]['baoxian_beichou'], $aClubZhangDanList[$aClub['club_id']]['duizhangfangfa'], $this->choushui_shuanfa, false);
					$aClubZhangDanList[$aClub['club_id']]['float_zhang_dan'] = $clubFloatZhandan;
					$aClubZhangDanList[$aClub['club_id']]['zhang_dan'] = Calculate::getIntValueByChoushuiShuanfa($clubFloatZhandan, $this->choushui_shuanfa);
				}
			}
			
			$aClubZhangDanList[$aClub['club_id']]['hui_zhong'] = $aClubZhangDanList[$aClub['club_id']]['float_zhang_dan'] + $aClubZhangDanList[$aClub['club_id']]['qianzhang'];
			$totalZhanDan += $aClubZhangDanList[$aClub['club_id']]['hui_zhong'];
			$aClubZhangDanList[$aClub['club_id']]['hui_zhong'] = Calculate::getIntValueByChoushuiShuanfa($aClubZhangDanList[$aClub['club_id']]['hui_zhong'], $this->choushui_shuanfa);
		}
		$totalZhanDan = Calculate::getIntValueByChoushuiShuanfa($totalZhanDan, $this->choushui_shuanfa);
		//如果没有新账单就不显示牌局记录列表了
		$hasUncleanZhangDan = false;
		if($aPaijuZhangDanList){
			foreach($aPaijuZhangDanList as $aPaijuZhangDan){
				if(!$aPaijuZhangDan['club_is_clean']){
					$hasUncleanZhangDan = true;
					break;
				}
			}
		}
		if(!$hasUncleanZhangDan && $aTotalClubList){
			$aPaijuZhangDanList = [];
		}
		foreach($aPaijuZhangDanList as $k => $v){
			foreach($aPaijuList as $aPaiju){
				if($aPaiju['id'] == $v['paiju_id']){
					$aPaijuZhangDanList[$k]['hedui_shuzi'] = $aPaiju['hedui_shuzi'];
					break;
				}
			}
		}
		//排序
		if($aPaijuZhangDanList){
			$aPaijuId = array_keys($aPaijuZhangDanList);
			$aPaijuList = Paiju::findAll(['id' => $aPaijuId], ['id', 'end_time'], 0, 0, ['end_time' => SORT_DESC]);
			$aSortList = [];
			foreach($aPaijuList as $aPaiju){
				$aSortList[] = $aPaijuZhangDanList[$aPaiju['id']];
			}
			$aPaijuZhangDanList = $aSortList;
		}
		return [
			'totalPaijuCount' => $totalPaijuCount,
			'totalHeduishuziPaijuCount' => $totalHeduishuziPaijuCount,
			'totalHeduishuzi' => $totalHeduishuzi,
			'totalZhanDan' => $totalZhanDan,
			'aClubZhangDanList' => $aClubZhangDanList,
			'aPaijuZhangDanList' => $aPaijuZhangDanList,
		];
	}
	
	/**
	 *	获取联盟主机对账列表
	 */
	public function getLianmengHostDuizhang($lianmengId, $isReturnRecordId = false){
		set_time_limit(0);
		$mLianmeng = HostLianmeng::findOne($lianmengId);
		if(!$mLianmeng){
			return [];
		}
		$clubIdWhere = '';
		$aClubId = [];
		$aClubList = $mLianmeng->getLianmengClubList();
		if($aClubList){
			$aClubId = ArrayHelper::getColumn($aClubList, 'club_id');
		}else{
			//return [];
		}
		$aClubList = ArrayHelper::index($aClubList, 'club_id');
		if($aClubId){
			$clubIdWhere = ' AND `t1`.`club_id` IN(' . implode(',', $aClubId) . ')';
		}
		$lianmengIdWhere = '';
		if($lianmengId){
			//$lianmengIdWhere = ' AND `t2`.`lianmeng_id`=' . $lianmengId;	//旧的方法
			$aLmzjPaijuCreater = $mLianmeng->lmzj_paiju_creater;
			if(!$aLmzjPaijuCreater){
				//$aLmzjPaijuCreater = ['D_D'];
				$aLmzjPaijuCreater = 'D_D';
			}
			//$lianmengIdWhere = ' AND `t1`.`paiju_creater` IN("' . implode('","', $mLianmeng->lmzj_paiju_creater) . '")';	//新的方法
			$lianmengIdWhere = ' AND `t1`.`paiju_creater`="' . $mLianmeng->lmzj_paiju_creater . '"';	//新的方法
		}
		$cleanTime = $mLianmeng->clean_time;
		//没有俱乐部清账记录且注册时间是不只一个星期的,则从最后一次交班后的牌局开始获取数据
		$mOperateLog = OperateLog::findOne(['user_id' => $this->id, 'type' => 38]);
		if(!$mOperateLog && $this->create_time < (NOW_TIME - 86400 * 7)){
			$cleanTime = $this->getLastMaxJiaobanPaijuEndTime();
		}
		$updatePaijuTime = $mLianmeng->update_paiju_time;
		if(!$updatePaijuTime){
			$updatePaijuTime = NOW_TIME;
		}
		$timeWhere = ' AND `t1`.`create_time`>' . $cleanTime . ' AND `t1`.`create_time`<=' . $updatePaijuTime;
		//$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t1`.`club_id`,`t1`.`club_name`,`t1`.`club_is_clean`,`t2`.`lianmeng_id`,`t4`.`name` AS `lianmeng_name`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . '' . $lianmengIdWhere . $clubIdWhere;
		//$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t1`.`club_id`,`t1`.`club_name`,`t1`.`club_is_clean`,`t2`.`lianmeng_id`,`t4`.`name` AS `lianmeng_name`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t2`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t1`.`club_is_clean`=0 AND `t4`.`user_id`=' . $this->id . ' AND `t2`.`status`>=1' . $lianmengIdWhere . $clubIdWhere;
		//$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t1`.`club_id`,`t1`.`club_name`,`t1`.`club_is_clean`,`t2`.`lianmeng_id` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t1`.`club_is_clean`=0 AND `t2`.`status`>=1' . $lianmengIdWhere;
		$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_id`,`t1`.`club_name`,`t1`.`club_is_clean`,`t1`.`player_id` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t1`.`club_is_clean`=0' . $lianmengIdWhere . $timeWhere;
		
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		$totalHeduishuzi = 0;
		$totalPaijuCount = 0;
		$totalHeduishuziPaijuCount = 0;
		$aPaijuList = [];
		//补上未添加到联盟的俱乐部的牌局
		if($aResult){
			$aPaijuId = array_unique(ArrayHelper::getColumn($aResult, 'paiju_id'));
			//$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t1`.`club_id`,`t1`.`club_name`,`t1`.`club_is_clean` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t1`.`club_is_clean`=0 AND `t2`.`status`>=1' . $lianmengIdWhere . ' AND `t1`.paiju_id IN(' . implode(',', $aPaijuId) . ')';
			$sql = 'SELECT distinct(`t1`.`id`),`t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_id`,`t1`.`club_name`,`t1`.`club_is_clean`,`t1`.`player_id` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t1`.`club_is_clean`=0' . $lianmengIdWhere . $timeWhere . ' AND `t1`.paiju_id IN(' . implode(',', $aPaijuId) . ')';
			//$aOtherClubResult = Yii::$app->db->createCommand($sql)->queryAll();
			$aResult = Yii::$app->db->createCommand($sql)->queryAll();
			
			$aPaijuList = Paiju::getPaijuList(['id' => $aPaijuId], ['width_hedui_shuzi' => true]);
			$totalPaijuCount = count($aPaijuList);
			foreach($aPaijuList as $aPaiju){
				$totalHeduishuzi += $aPaiju['hedui_shuzi'];
				if($aPaiju['hedui_shuzi']){
					$totalHeduishuziPaijuCount += 1;
				}
			}
		}
		
		if($isReturnRecordId){
			return ArrayHelper::getColumn($aResult, 'id');
		}
		
		$aClubDetailList = [];
		$aClubShangzuorenshuList = [];
		$aPaijuDataZhangDanList = [];
		$aPaijuZhangDanList = [];
		$totalZhanDan = 0;
		$aTotalClubList = $aClubList;
		$aClubIds = array_keys($aClubList);
		foreach($aResult as $value){
			if(!isset($aTotalClubList[$value['club_id']])){
				$aTotalClubList[$value['club_id']] = [
					'id' => 0,
					'club_id' => $value['club_id'],
					'club_name' => $value['club_name'],
					'qianzhang' => 0,
					'duizhangfangfa' => Lianmeng::DUIZHANGFANGFA_LINDIANJIUQIWU,
					'paiju_fee' => 0,
					'baoxian_choucheng' => 0,
					'qibu_zhanji' => 0,
				];
			}
			//$baoxianChoucheng = $value['baoxian_choucheng'];
			$baoxianChoucheng = 0;
			//$paijuFee = $value['paiju_fee'];
			$paijuFee = 0;
			//$duizhangfangfa = $value['duizhangfangfa'];
			$duizhangfangfa = Lianmeng::DUIZHANGFANGFA_LINDIANJIUQIWU;
			
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
				'baoxian_choucheng' => $baoxianChoucheng,
				'zhang_dan' => 0,
				'float_zhang_dan' => 0,
				'zhanji' => $value['zhanji'],
				'baoxian_heji' => $value['baoxian_heji'],
				'baoxian_beichou' => 0,
				'float_baoxian_beichou' => 0,
				'club_is_clean' => $value['club_is_clean'],
				'player_id' => $value['player_id'],
			];
			$baoxianBeichou = Calculate::calculateBaoxianBeichou($value['baoxian_heji'], $baoxianChoucheng, $this->choushui_shuanfa);
			$floatBaoxianBeichou = Calculate::calculateBaoxianBeichou($value['baoxian_heji'], $baoxianChoucheng, $this->choushui_shuanfa, false);
			$aTemp['baoxian_beichou'] = $baoxianBeichou;
			$aTemp['float_baoxian_beichou'] = $floatBaoxianBeichou;
			$aPaijuDataZhangDanList[] = $aTemp;
		}
		$aClubPaijuDataZhangDanList = [];
		foreach($aPaijuDataZhangDanList as $value){
			if(!isset($aClubPaijuDataZhangDanList[$value['club_id']])){
				$aClubPaijuDataZhangDanList[$value['club_id']] = [];
			}
			$aClubPaijuDataZhangDanList[$value['club_id']][] = $value;
		}
		///////////////////////////////////////这里好操蛋/////////////////////////////////////////////
		//俱乐部没有牌局记录，则制造假记录，即使俱乐部没有牌局数据也要算上桌子费
		foreach($aTotalClubList as $aClub){
			$aClubShangzuorenshuList[$aClub['club_id']] = [];
			
			$aClubDetailList[$aClub['club_id']] = [
				'club_name' => $aClub['club_name'],
				'club_zhanji' => 0,
				'club_baoxian' => 0,
				'club_shangzuorenshu' => 0,
				'qibu_zhanji' => $this->qibu_zhanji,
			];
			if(!isset($aClubPaijuDataZhangDanList[$aClub['club_id']])){
				$aClubPaijuDataZhangDanList[$aClub['club_id']] = [];
				//确保有俱乐部的记录,因为要计桌子费用，不然联盟 吃什么
				foreach($aPaijuZhangDanList as $mm){
					$aTempData = [
						'paiju_id' => $mm['paiju_id'],
						'paiju_name' => $mm['paiju_name'],
						'club_id' => $aClub['club_id'],
						'paiju_fee' => $aClub['paiju_fee'],
						'duizhangfangfa' => $aClub['duizhangfangfa'],
						'baoxian_choucheng' => $aClub['baoxian_choucheng'],
						'zhang_dan' => 0,
						'float_zhang_dan' => 0,
						'zhanji' => 0,
						'baoxian_heji' => 0,
						'baoxian_beichou' => 0,
						'float_baoxian_beichou' => 0,
						'club_is_clean' => $mm['club_is_clean'],
						'player_id' => 0,
					];
					$aClubPaijuDataZhangDanList[$aClub['club_id']][] = $aTempData;
					array_push($aPaijuDataZhangDanList, $aTempData);
				}
			}else{
				//把所有牌局都检查一下，确保有俱乐部的记录,因为要计桌子费用，不然联盟 吃什么
				foreach($aPaijuZhangDanList as $mm){
					$isFind = false;
					foreach($aClubPaijuDataZhangDanList[$aClub['club_id']] as $nn){
						if($mm['paiju_id'] == $nn['paiju_id']){
							$isFind = true;
							break;
						}
					}
					if(!$isFind){
						$aTempData = [
							'paiju_id' => $mm['paiju_id'],
							'paiju_name' => $mm['paiju_name'],
							'club_id' => $aClub['club_id'],
							'paiju_fee' => $aClub['paiju_fee'],
							'duizhangfangfa' => $aClub['duizhangfangfa'],
							'baoxian_choucheng' => $aClub['baoxian_choucheng'],
							'zhang_dan' => 0,
							'float_zhang_dan' => 0,
							'zhanji' => 0,
							'baoxian_heji' => 0,
							'baoxian_beichou' => 0,
							'float_baoxian_beichou' => 0,
							'club_is_clean' => $mm['club_is_clean'],
							'player_id' => 0,
						];
						$aClubPaijuDataZhangDanList[$aClub['club_id']][] = $aTempData;
						array_push($aPaijuDataZhangDanList, $aTempData);
					}
				}
			}
		}
		///////////////////////////////////////这里好操蛋/////////////////////////////////////////////
		$aClubZhangDanList = [];
		
		$totalBaoXianChouCheng = 0;
		$totalBaoXianBeiChou = 0;
		foreach($aTotalClubList as $aClub){
			$aClubZhangDanList[$aClub['club_id']] = [
				'lianmeng_club_id' => $aClub['id'],
				'club_id' => $aClub['club_id'],
				'club_name' => $aClub['club_name'],
				'qianzhang' => $aClub['qianzhang'],
				'duizhangfangfa' => $aClub['duizhangfangfa'],
				'baoxian_choucheng' => $aClub['baoxian_choucheng'],
				'paiju_fee' => $aClub['paiju_fee'],
				'club_is_clean' => 0,
				'zhang_dan' => 0,
				'float_zhang_dan' => 0,
				'zhanji' => 0,
				'baoxian_heji' => 0,
				'baoxian_beichou' => 0,
				'float_baoxian_beichou' => 0,
				'hui_zhong' => 0,
				'club_zhang_dan_list' => [],
			];
			foreach($aClubPaijuDataZhangDanList[$aClub['club_id']] as $v){
				if(!isset($aClubZhangDanList[$aClub['club_id']])){
					$aClubZhangDanList[$aClub['club_id']] = [
						'lianmeng_club_id' => $aClub['id'],
						'club_id' => $aClub['club_id'],
						'club_name' => $aClub['club_name'],
						'qianzhang' => $aClub['qianzhang'],
						'duizhangfangfa' => $aClub['duizhangfangfa'],
						'baoxian_choucheng' => $aClub['baoxian_choucheng'],
						'paiju_fee' => $aClub['paiju_fee'],
						'club_is_clean' => 0,
						'zhang_dan' => 0,
						'float_zhang_dan' => 0,
						'zhanji' => 0,
						'baoxian_heji' => 0,
						'baoxian_beichou' => 0,
						'float_baoxian_beichou' => 0,
						'hui_zhong' => 0,
						'club_zhang_dan_list' => [],
					];
				}
				if(!isset($aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']])){
					$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']] = [
						'paiju_id' => $v['paiju_id'],
						'paiju_name' => $v['paiju_name'],
						'club_id' => $v['club_id'],
						'paiju_fee' => $v['paiju_fee'],
						'club_is_clean' => $v['club_is_clean'],
						'duizhangfangfa' => $aClub['duizhangfangfa'],
						'baoxian_choucheng' => $aClub['baoxian_choucheng'],
						'zhang_dan' => 0,
						'float_zhang_dan' => 0,
						'zhanji' => 0,
						'baoxian_heji' => 0,
						'baoxian_beichou' => 0,
						'float_baoxian_beichou' => 0,
					];
				}
				$aClubDetailList[$aClub['club_id']]['club_zhanji'] += abs($v['zhanji']);
				$aClubDetailList[$aClub['club_id']]['club_baoxian'] += abs($v['baoxian_heji']);
				if(abs($v['zhanji']) > $aClubDetailList[$aClub['club_id']]['qibu_zhanji']){
					if(!in_array($v['player_id'], $aClubShangzuorenshuList[$aClub['club_id']])){
						array_push($aClubShangzuorenshuList[$aClub['club_id']], $v['player_id']);
						$aClubDetailList[$aClub['club_id']]['club_shangzuorenshu'] += 1;
					}
				}
				$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']]['zhanji'] += $v['zhanji'];
				$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']]['baoxian_heji'] += $v['baoxian_heji'];
				///
				$totalBaoXianChouCheng += $v['baoxian_choucheng'];
				$baoxianBeichou = Calculate::calculateBaoxianBeichou($v['baoxian_heji'], $aClubZhangDanList[$v['club_id']]['baoxian_choucheng'], $this->choushui_shuanfa);
				$floatBaoxianBeichou = Calculate::calculateBaoxianBeichou($v['baoxian_heji'], $aClubZhangDanList[$v['club_id']]['baoxian_choucheng'], $this->choushui_shuanfa, false);
				$totalBaoXianBeiChou += $floatBaoxianBeichou;
				///
				//$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']]['baoxian_beichou'] += $v['baoxian_beichou'];
				$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']]['baoxian_beichou'] += $baoxianBeichou;
				$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$v['paiju_id']]['float_baoxian_beichou'] += $floatBaoxianBeichou;
				$aClubZhangDanList[$aClub['club_id']]['club_is_clean'] = $v['club_is_clean'];
				$aClubZhangDanList[$aClub['club_id']]['zhanji'] += $v['zhanji'];
				$aClubZhangDanList[$aClub['club_id']]['baoxian_heji'] += $v['baoxian_heji'];
				//$aClubZhangDanList[$aClub['club_id']]['baoxian_beichou'] += $v['baoxian_beichou'];
				$aClubZhangDanList[$aClub['club_id']]['baoxian_beichou'] += $baoxianBeichou;
				$aClubZhangDanList[$aClub['club_id']]['float_baoxian_beichou'] += $floatBaoxianBeichou;
			}
			if(!$aClubZhangDanList[$aClub['club_id']]['club_is_clean']){
				$hasClubZhangDan = false;
				foreach($aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'] as $kk => $vv){
					$hasClubZhangDan = true;
					//账单值与自己俱乐部联盟账单值相反
					//$zhandan = -Calculate::calculateZhangDan($vv['zhanji'], $vv['baoxian_heji'], $vv['paiju_fee'], $vv['baoxian_beichou'], $vv['duizhangfangfa'], $this->choushui_shuanfa);
					$floatZhandan = Calculate::calculateZhangDan($vv['zhanji'], $vv['baoxian_heji'], $vv['paiju_fee'], $vv['float_baoxian_beichou'], $vv['duizhangfangfa'], $this->choushui_shuanfa, false);
					$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$kk]['zhang_dan'] = Calculate::getIntValueByChoushuiShuanfa($floatZhandan, $this->choushui_shuanfa);
					$aClubZhangDanList[$aClub['club_id']]['club_zhang_dan_list'][$kk]['float_zhang_dan'] = $floatZhandan;
					//$aClubZhangDanList[$aClub['club_id']]['zhang_dan'] += $zhandan;
					//$aClubZhangDanList[$aClub['club_id']]['float_zhang_dan'] += $floatZhandan;
				}
				if($hasClubZhangDan){
					$clubFloatZhandan = Calculate::calculateZhangDan($aClubZhangDanList[$aClub['club_id']]['zhanji'], $aClubZhangDanList[$aClub['club_id']]['baoxian_heji'], $aClubZhangDanList[$aClub['club_id']]['paiju_fee'], $aClubZhangDanList[$aClub['club_id']]['float_baoxian_beichou'], $aClubZhangDanList[$aClub['club_id']]['duizhangfangfa'], $this->choushui_shuanfa, false);
					$aClubZhangDanList[$aClub['club_id']]['float_zhang_dan'] = $clubFloatZhandan;
					$aClubZhangDanList[$aClub['club_id']]['zhang_dan'] = Calculate::getIntValueByChoushuiShuanfa($clubFloatZhandan, $this->choushui_shuanfa);
				}
			}
			
			$aClubZhangDanList[$aClub['club_id']]['hui_zhong'] = $aClubZhangDanList[$aClub['club_id']]['float_zhang_dan'] + $aClubZhangDanList[$aClub['club_id']]['qianzhang'];
			$totalZhanDan += $aClubZhangDanList[$aClub['club_id']]['hui_zhong'];
			$aClubZhangDanList[$aClub['club_id']]['hui_zhong'] = Calculate::getIntValueByChoushuiShuanfa($aClubZhangDanList[$aClub['club_id']]['hui_zhong'], $this->choushui_shuanfa);
		}
		unset($aClubShangzuorenshuList);
		//debug($totalBaoXianBeiChou,11);
		$totalBaoXianBeiChou = Calculate::getIntValueByChoushuiShuanfa($totalBaoXianBeiChou, $this->choushui_shuanfa);
		$totalZhanDan = Calculate::getIntValueByChoushuiShuanfa($totalZhanDan * 0.975, $this->choushui_shuanfa);
		//如果没有新账单就不显示牌局记录列表了
		$hasUncleanZhangDan = false;
		if($aPaijuZhangDanList){
			foreach($aPaijuZhangDanList as $aPaijuZhangDan){
				if(!$aPaijuZhangDan['club_is_clean']){
					$hasUncleanZhangDan = true;
					break;
				}
			}
		}
		if($hasUncleanZhangDan && !$mLianmeng->update_paiju_time){
			$mLianmeng->set('update_paiju_time', NOW_TIME);
			$mLianmeng->save();
		}
		if(!$hasUncleanZhangDan && $aTotalClubList){
			$aPaijuZhangDanList = [];
			//$totalZhanDan = 0;
		}
		foreach($aPaijuZhangDanList as $k => $v){
			foreach($aPaijuList as $aPaiju){
				if($aPaiju['id'] == $v['paiju_id']){
					$aPaijuZhangDanList[$k]['hedui_shuzi'] = $aPaiju['hedui_shuzi'];
					break;
				}
			}
		}
		//排序
		if($aPaijuZhangDanList){
			$aPaijuId = array_keys($aPaijuZhangDanList);
			$aPaijuList = Paiju::findAll(['id' => $aPaijuId], ['id', 'end_time'], 0, 0, ['end_time' => SORT_DESC]);
			$aSortList = [];
			foreach($aPaijuList as $aPaiju){
				$aSortList[] = $aPaijuZhangDanList[$aPaiju['id']];
			}
			$aPaijuZhangDanList = $aSortList;
		}
		
		return [
			'totalPaijuCount' => $totalPaijuCount,
			'totalHeduishuziPaijuCount' => $totalHeduishuziPaijuCount,
			'totalHeduishuzi' => $totalHeduishuzi,
			'totalZhanDan' => -$totalZhanDan,
			'aClubZhangDanList' => $aClubZhangDanList,
			'aPaijuZhangDanList' => $aPaijuZhangDanList,
			'totalBaoXianChouCheng' => $totalBaoXianChouCheng,
			'totalBaoXianBeiChou' => $totalBaoXianBeiChou,
			'aClubDetailList' => $aClubDetailList,
		];
	}
	
	/**
	 *	联盟俱乐部清账
	 */
	public function clubQinZhang($mLianmeng, $aZhangDan, $aLianmengHostDuizhang){
		/*if(!$aZhangDan){
			//清除没添加俱乐部联盟的账单
			if(isset($aLianmengHostDuizhang['aPaijuZhangDanList']) && $aLianmengHostDuizhang['aPaijuZhangDanList']){
				$aPaijuId = ArrayHelper::getColumn($aLianmengHostDuizhang['aPaijuZhangDanList'], 'paiju_id');
				$sql = 'UPDATE ' . ImportData::tableName() . ' SET `club_is_clean`=1 WHERE `user_id`=' . $this->id . ' AND `paiju_id` IN (' . implode(',', $aPaijuId) . ')';
				Yii::$app->db->createCommand($sql)->execute();
				return true;
			}
			return false;
		}*/
		$zhandan = 0;
		foreach($aZhangDan as $value){
			//更新俱乐部账单牌局已清账状态
			//$sql = 'UPDATE ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . LianmengClub::tableName() . ' AS `t2` ON `t1`.`club_id`=`t2`.`club_id` SET `t1`.`club_is_clean`=1 WHERE `t1`.`user_id`=' . $this->id . ' AND `t1`.`club_id`=' . $value['club_id'] . ' AND `t1`.`club_is_clean`=0 AND `t2`.`lianmeng_id`=' . $mLianmeng->id . ' AND `t2`.`is_delete`=0';
			//Yii::$app->db->createCommand($sql)->execute();
			//更新俱乐部欠账
			$mLianmengClub = HostLianmengClub::findOne(['user_id' => $this->id, 'lianmeng_id' => $mLianmeng->id, 'club_id' => $value['club_id']]);
			if(!$mLianmengClub){
				continue;
			}
			if($value['zhang_dan']){
				$zhandan += $value['zhang_dan'];
				$mLianmengClub->set('qianzhang', ['add', $value['zhang_dan']]);
				$mLianmengClub->save();
			}
		}
		//更新俱乐部账单牌局已清账状态
		if(isset($aLianmengHostDuizhang['aPaijuZhangDanList']) && $aLianmengHostDuizhang['aPaijuZhangDanList']){
			$aPaijuId = ArrayHelper::getColumn($aLianmengHostDuizhang['aPaijuZhangDanList'], 'paiju_id');
			$sql = 'UPDATE ' . ImportData::tableName() . ' SET `club_is_clean`=1 WHERE `user_id`=' . $this->id . ' AND `paiju_id` IN (' . implode(',', $aPaijuId) . ')';
			Yii::$app->db->createCommand($sql)->execute();
		}
		//更新时间
		$mLianmeng->set('clean_time', $mLianmeng->update_paiju_time);
		$mLianmeng->set('update_paiju_time', 0);
		$mLianmeng->save();
		$this->operateLog(38, ['aLianmeng' => $mLianmeng->toArray(), 'zhandan' => $zhandan, 'aZhangDan' => $aZhangDan]);
		
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
	
	public function getAllPlayerInfoList($playerId = '', $playerName = '', $kerenBianhao = ''){
		/*$sql = 'SELECT `t1`.*,`t2`.`benjin`,`t2`.`ying_chou`,`t2`.`shu_fan`,`t2`.`agent_id`,`t2`.`remark` FROM ' . Player::tableName() . ' AS `t1` LEFT JOIN ' . KerenBenjin::tableName() . ' AS `t2` ON `t1`.`keren_bianhao`=`t2`.`keren_bianhao` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`user_id`=' . $this->id . ' AND `t1`.`is_delete`=0 ORDER BY `t1`.`keren_bianhao` ASC';
		return Yii::$app->db->createCommand($sql)->queryAll();*/
		
		$aClubList = $this->getUserClubList();
		$aClubId = [];
		if($aClubList){
			$aClubId = ArrayHelper::getColumn($aClubList, 'club_id');
		}
		array_push($aClubId, 0);
		$aCondition = [
			'`k1`.`user_id`' => $this->id,
			'`k1`.`is_delete`' => 0,
			'club_id' => $aClubId,
		];
		
		$aControl = [
			'page' => 1,
			'page_size' => 9999999999,
			'order_by' => '`k1`.keren_bianhao ASC',
			'with_player_list' => true,
			'with_agent_info' => true,
		];
		
		$aList = KerenBenjin::getList1($aCondition, $aControl);
		$aReturn = [];
		foreach($aList as $value){
			foreach($value['player_list'] as $aPlayer){
				if($kerenBianhao && $kerenBianhao != $aPlayer['keren_bianhao']){
					continue;
				}
				if($playerId && $playerId != $aPlayer['player_id']){
					continue;
				}
				if($playerName && $playerName != $aPlayer['player_name']){
					continue;
				}
				$aTemp = $aPlayer;
				$aTemp['benjin'] = $value['benjin'];
				$aTemp['ying_chou'] = $value['ying_chou'];
				$aTemp['shu_fan'] = $value['shu_fan'];
				$aTemp['ying_fee'] = $value['ying_fee'];
				$aTemp['shu_fee'] = $value['shu_fee'];
				$aTemp['agent_id'] = $value['agent_id'];
				$aTemp['remark'] = $value['remark'];
				$aTemp['is_auto_create'] = $value['is_auto_create'];
				$aTemp['agent_info'] = $value['agent_info'];
				$aReturn[] = $aTemp;
			}
		}
		return $aReturn;
	}
}