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
		return $aWhere;
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
		return $aResult[0]['total_money'];
	}
	
	public function getAgentList(){
		return Agent::findAll(['user_id' => $this->id, 'is_delete' => 0]);
	}
	
	public function getFenchengListSetting(){
		$aFenchengConfigList = FenchengSetting::getFenchengConfigList();
		$aList = FenchengSetting::findAll(['user_id' => $this->id, 'zhuozi_jibie' => $aFenchengConfigList]);
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
					'zhuozi_jibie' => $zhuoziJibie,
					'yingfan' => 0,
					'shufan' => 0,
				]);
			}
		}
		if($aInsertList){
			FenchengSetting::bathInsertData($aInsertList);
		}
		return FenchengSetting::findAll(['user_id' => $this->id, 'zhuozi_jibie' => $aFenchengConfigList]);
	}
	
	public function getLastPaijuList($page = 1, $pageSize = 0, $aParam = ['status' => [Paiju::STATUS_UNDO, Paiju::STATUS_DONE]], $aOrder = ['`t1`.`id`' => SORT_DESC]){
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
		}debug($aControll,11);
		return Paiju::getList($aCondition, $aControll);
	}
	
	public function getPaijuDataList($paijuId, $withKerenBbenjinInfo = false){
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
		$aControll = [];
		if($withKerenBbenjinInfo){
			$aControll['with_keren_benjin_info'] = true;
		}
		$aList = ImportData::getList($aCondition, $aControll);
		//过滤掉删除的客人记录
		$aReturnList = [];
		foreach($aList as $key => $value){
			if(!$value['keren_benjin_info']['is_delete']){
				array_push($aReturnList, $value);
			}
		}
		return $aReturnList;
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
	 *	统计未交班的牌局总抽水、总保险、上桌人数、差额、交班转出
	 */
	public function getUnJiaoBanPaijuTotalStatistic(){
		$aClubId = [];
		$aClubList = $this->getUserClubList();
		if($aClubList){
			$aClubId = ArrayHelper::getColumn($aClubList, 'club_id');
		}
		//获取总抽水
		$zhongChouShui = ImportData::getUserUnJiaoBanPaijuZhongChouShui($this->id, $aClubId);
		//获取总保险
		$zhongBaoXian = ImportData::getUserUnJiaoBanPaijuZhongBaoXian($this->id, $aClubId);
		//上桌人数
		$shangZhuoRenShu = ImportData::getUserUnJiaoBanPaijuShangZhuoRenShu($this->id, $aClubId);
		
		return [
			'zhongChouShui' => $zhongChouShui,
			'zhongBaoXian' => $zhongBaoXian,
			'shangZhuoRenShu' => $shangZhuoRenShu,
		];
	}
	
	/**
	 *	获取抽水列表
	 */
	public function getUnJiaoBanPaijuChouShuiList(){
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
		$sql = 'SELECT `t1`.`paiju_id`,`t1`.`paiju_name`,`t1`.`zhanji`,`t1`.`choushui_value`,`t1`.`baoxian_heji`,`t1`.`club_baoxian`,`t1`.`baoxian`,`t4`.`qianzhang`,`t4`.`duizhangfangfa`,`t4`.`paiju_fee`,`t4`.`baoxian_choucheng` FROM ' . ImportData::tableName() . ' AS `t1` LEFT JOIN ' . Paiju::tableName() . ' AS `t2` ON `t1`.`paiju_id`=`t2`.`id` LEFT JOIN ' . Player::tableName() . ' AS `t3` ON `t1`.`player_id`=`t3`.`player_id` LEFT JOIN ' . Lianmeng::tableName() . ' AS `t4` ON `t1`.`lianmeng_id`=`t4`.`id` WHERE `t1`.`user_id`=' . $this->id . ' AND `t2`.`status`=' . Paiju::STATUS_DONE . ' AND `t1`.`status`=1 AND `t1`.`choushui_value`>0 AND `t3`.`is_delete`=0' . $clubIdWhere;
		$aResult = Yii::$app->db->createCommand($sql)->queryAll();
		
		$aReturnList = [];
		foreach($aResult as $value){
			if(!isset($aReturnList[$value['paiju_id']])){
				$aReturnList['paiju_id'] = [
					'paiju_name' => $value['paiju_name'],
					'zhanji' => 0,
					'choushui_value' => 0,
					'lianmeng_butie' => 0,
					'shiji_choushui_value' => 0,
					'paiju_fee' => 0,
				];
			}
			$aReturnList['paiju_id']['zhanji'] += $value['zhanji'];
			$aReturnList['paiju_id']['choushui_value'] += $value['choushui_value'];
			$lianmengButie = Calculate::calculateLianmengButie($value['zhanji'], $value['baoxian'], $value['duizhangfangfa']);
			$aReturnList['paiju_id']['lianmeng_butie'] += $lianmengButie;
			$aReturnList['paiju_id']['shiji_choushui_value'] += Calculate::calculateShijiChouShuiValue($value['choushui_value'], $lianmengButie, $value['paiju_fee']);
			$aReturnList['paiju_id']['paiju_fee'] += $value['paiju_fee'];
		}
		
		return $aReturnList;
	}
	
}