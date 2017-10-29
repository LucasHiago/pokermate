<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\User;
use common\model\MoneyType;
use common\model\AgentQinzhangRecord;
use common\model\Lianmeng;

class UserController extends Controller{
	
	public function actionSave(){
		$loginName = trim(strip_tags((string)Yii::$app->request->post('loginName')));
		$password = trim(strip_tags((string)Yii::$app->request->post('password')));
		$qibuChoushui = (int)Yii::$app->request->post('qibuChoushui');
		$choushuiShuanfa = (int)Yii::$app->request->post('choushuiShuanfa');
		
		if(!$loginName){
			return new Response('请填写账号', -1);
		}
		if(!$password){
			return new Response('请填写密码', -1);
		}
		if(!in_array($choushuiShuanfa, [1, 2])){
			return new Response('抽水算法有误', 0);
		}
		$mUser = Yii::$app->user->getIdentity();
		$mTempUser = User::findOne(['login_name' => $loginName]);
		if($mTempUser && $mTempUser->id != $mUser->id){
			return new Response('账号已在', 0);
		}
		
		$uflag = false;
		if($loginName != $mUser->login_name){
			$mUser->set('login_name', $loginName);
			$uflag = true;
		}
		if($password != $mUser->password){
			$mUser->set('password', User::encryptPassword($password));
			$uflag = true;
		}
		if($qibuChoushui != $mUser->qibu_choushui){
			$mUser->set('qibu_choushui', $qibuChoushui);
			$uflag = true;
		}
		if($choushuiShuanfa != $mUser->choushui_shuanfa){
			$mUser->set('choushui_shuanfa', $choushuiShuanfa);
			$uflag = true;
		}
		if($uflag){
			$mUser->save();
		}
		return new Response('保存成功', 1);
	}
	
	public function actionUpdateUserInfo(){
		$type = (string)Yii::$app->request->post('type');
		$value = Yii::$app->request->post('value');
		
		if(!in_array($type, ['choushui_ajust_value', 'baoxian_ajust_value', 'agent_fencheng_ajust_value', 'lianmeng_zhongzhang_ajust_value'])){
			return new Response('出错了', 0);
		}
		$mUser = Yii::$app->user->getIdentity();
		if($type == 'choushui_ajust_value'){
			$mUser->set('choushui_ajust_value', (int)$value);
		}
		if($type == 'baoxian_ajust_value'){
			$mUser->set('baoxian_ajust_value', (int)$value);
		}
		if($type == 'agent_fencheng_ajust_value'){
			$mUser->set('agent_fencheng_ajust_value', (int)$value);
		}
		if($type == 'lianmeng_zhongzhang_ajust_value'){
			$mUser->set('lianmeng_zhongzhang_ajust_value', (int)$value);
		}
		$mUser->save();
		
		return new Response('保存成功', 1);
	}
	
	public function actionGetChouShuiList(){
		$mUser = Yii::$app->user->getIdentity();
		
		$aChouShuiList = $mUser->getUnJiaoBanPaijuChouShuiList();
		$aAgentQinzhangRecordList = AgentQinzhangRecord::getList(['user_id' => $mUser->id, 'is_show' => 1], ['width_agent_info' => true]);
		
		$aReturnChouShuiList = [];
		foreach($aAgentQinzhangRecordList as $aAgentQinzhangRecord){
			array_push($aReturnChouShuiList, [
				'baoxian_heji' => 0,
				'choushui_value' => 0,
				'duizhangfangfa' => Lianmeng::DUIZHANGFANGFA_LINDIANJIUQIWU,
				'float_choushui_value' => 0,
				'float_lianmeng_butie' => 0,
				'float_shiji_choushui_value' => 0,
				'int_float_shiji_choushui_value' => 0,
				'lianmeng_butie' => 0,
				'paiju_fee' => 0,
				'taifee' => 0,
				'paiju_id' => 0,
				'paiju_name' => '(' . $aAgentQinzhangRecord['agent_info']['agent_name'] . ')清账',
				'shiji_choushui_value' => $aAgentQinzhangRecord['qinzhang_value'],
				'zhanji' => 0,
			]);
		}
		foreach($aChouShuiList as $aChouShui){
			array_push($aReturnChouShuiList, $aChouShui);
		}
		
		/*$totalChouShui = $mUser->choushui_ajust_value;
		foreach($aChouShuiList as $aChouShui){
			$totalChouShui += $aChouShui['float_shiji_choushui_value'];
		}*/
		$aUnJiaoBanPaijuTotalStatistic = $mUser->getUnJiaoBanPaijuTotalStatistic();
		$totalChouShui = $aUnJiaoBanPaijuTotalStatistic['shijiChouShui'];
		$aData = [
			'list' => $aReturnChouShuiList,
			'count' => count($aChouShuiList),
			'totalChouShui' => $totalChouShui,
			'choushuiAjustValue' => $mUser->choushui_ajust_value,
		];
		return new Response('', 1, $aData);
	}
	
	public function actionGetBaoXianList(){
		$mUser = Yii::$app->user->getIdentity();
		
		$aBaoXianList = $mUser->getUnJiaoBanPaijuBaoXianList();
		
		$totalBaoXian = $mUser->baoxian_ajust_value;
		foreach($aBaoXianList as $aBaoXian){
			$totalBaoXian += $aBaoXian['baoxian_heji'];
		}
		$aData = [
			'list' => $aBaoXianList,
			'totalBaoXian' => $totalBaoXian,
			'baoxianAjustValue' => $mUser->baoxian_ajust_value,
		];
		return new Response('', 1, $aData);
	}
	
	public function actionGetShangZhuoRenShuList(){
		$mUser = Yii::$app->user->getIdentity();
		
		$aShangZhuoRenShuList = $mUser->getUnJiaoBanPaijuShangZhuoRenShuList();
		
		$totalShangZhuoRenShu = 0;
		foreach($aShangZhuoRenShuList as $aShangZhuoRenShu){
			$totalShangZhuoRenShu += $aShangZhuoRenShu['shang_zhuo_ren_shu'];
		}
		$aData = [
			'list' => $aShangZhuoRenShuList,
			'totalShangZhuoRenShu' => $totalShangZhuoRenShu,
		];
		return new Response('', 1, $aData);
	}
	
	public function actionGetJiaoBanZhuanChuDetail(){
		$mUser = Yii::$app->user->getIdentity();
		
		$aMoneyTypeList = $mUser->getMoneyTypeList();
		$aJiaoBanZhuanChuDetail = $mUser->getJiaoBanZhuanChuDetail();
		/*if(!$aJiaoBanZhuanChuDetail){
			return new Response('交班转出为0', -1);
		}*/
		return new Response('', 1, [
			'aMoneyTypeList' => $aMoneyTypeList,
			'aJiaoBanZhuanChuDetail' => $aJiaoBanZhuanChuDetail,
			'imbalanceMoney' => $mUser->getImbalanceMoney(),
		]);
	}
	
	public function actionDoJiaoBanZhuanChu(){
		$moneyTypeId = (int)Yii::$app->request->post('moneyTypeId');
		
		$mUser = Yii::$app->user->getIdentity();
		
		$mMoneyType = MoneyType::findOne($moneyTypeId);
		if(!$mMoneyType || $mMoneyType->is_delete){
			return new Response('转出渠道不存在', -1);
		}
		$aMoneyTypeList = $mUser->getMoneyTypeList();
		$aJiaoBanZhuanChuDetail = $mUser->getJiaoBanZhuanChuDetail();
		if(!$aJiaoBanZhuanChuDetail){
			return new Response('交班转出为0', -1);
		}
		if(!$mUser->doJiaoBanZhuanChu($mMoneyType, $aJiaoBanZhuanChuDetail['jiaoBanZhuanChuMoney'])){
			return new Response('交班转出失败', 0);
		}
		
		return new Response('交班转出成功', 1);
	}
	
	public function actionGetUnJiaoBanPaijuTotalStatistic(){
		$mUser = Yii::$app->user->getIdentity();
		$aUnJiaoBanPaijuTotalStatistic = $mUser->getUnJiaoBanPaijuTotalStatistic();
		$moneyTypeTotalMoney = $mUser->getMoneyTypeTotalMoney();
		$moneyOutPutTypeTotalMoney = $mUser->getMoneyOutPutTypeTotalMoney();
		$aMoneyTypeList = $mUser->getMoneyTypeList();
		$aMoneyOutPutTypeList = $mUser->getMoneyOutPutTypeList();
		
		$aCacheData = [
			'aUnJiaoBanPaijuTotalStatistic' => $aUnJiaoBanPaijuTotalStatistic,
			'moneyTypeTotalMoney' => $moneyTypeTotalMoney,
			'moneyOutPutTypeTotalMoney' => $moneyOutPutTypeTotalMoney,
			'aMoneyTypeList' => $aMoneyTypeList,
			'aMoneyOutPutTypeList' => $aMoneyOutPutTypeList,
		];
		$mUser->set('cache_data', $aCacheData);
		$mUser->save();
		
		return new Response('', 1, $aCacheData);
	}
	
	public function actionGetClubAndLianmengList(){
		$mUser = Yii::$app->user->getIdentity();
		$aClubList = $mUser->getUserClubList();
		$aLianmengList = $mUser->getLianmengList();
		
		return new Response('', 1, [
			'aClubList' => $aClubList,
			'aLianmengList' => $aLianmengList,
		]);
	}
		
	public function actionSetActive(){
		$mUser = Yii::$app->user->getIdentity();
		$aClubList = $mUser->getUserClubList();
		$aLianmengList = $mUser->getLianmengList();
		
		if(!$aClubList){
			return new Response('请设置俱乐部', -1);
		}
		if(!$aLianmengList){
			return new Response('请设置联盟', -1);
		}
		
		$mUser->set('is_active', 1);
		if($mUser->active_time){
			$mUser->set('active_time', NOW_TIME);
		}else{
			$mUser->set('active_time', strtotime(date('Y-m-d', NOW_TIME)));
		}
		$mUser->save();
		
		return new Response('启用成功', 1);
	}
	
}
