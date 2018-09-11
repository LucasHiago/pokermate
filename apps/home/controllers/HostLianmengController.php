<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\HostLianmeng;
use common\model\HostLianmengClub;
use common\model\Calculate;
use common\model\ImportData;

class HostLianmengController extends Controller{
	public $enableCsrfValidation = false;
	
	public function actionLianmengHostDuizhang(){
		$id = (int)Yii::$app->request->get('id');
		
		$mUser = Yii::$app->user->getIdentity();
		if(!$mUser->is_active){
			return new Response('提示:您的账号还没开始启用！', 0);
		}
		if(!$mUser->hasLianmengHostDuiZhangFunction()){
			return new Response('抱歉，您还没开通这个功能使用权限！', 0);
		}
		$aLianmengList = $mUser->getHostLianmengList();
		if($id){
			$mLianmeng = HostLianmeng::findOne(['id' => $id, 'user_id' => $mUser->id, 'is_delete' => 0]);
			if(!$mLianmeng){
				$id = $mUser->getDefaultHostLianmengId();
			}
		}else{
			$id = $mUser->getDefaultHostLianmengId();
		}
		$aCurrentLianmeng = [];
		$mLianmeng = HostLianmeng::findOne($id);
		if($mLianmeng){
			$aCurrentLianmeng = $mLianmeng->toArray();
		}
		$aLianmengHostDuizhang = $mUser->getLianmengHostDuizhang($id);
		if(!$aLianmengHostDuizhang){
			$aLianmengHostDuizhang = [
				'aPaijuZhangDanList' => [],
				'aClubZhangDanList' => [],
			];
		}
		return $this->render('lianmeng_host_duizhang', [
			'aLianmengList' => $aLianmengList,
			'aLianmengHostDuizhang' => $aLianmengHostDuizhang,
			'lianmengId' => $id,
			'aCurrentLianmeng' => $aCurrentLianmeng,
			'qibuZhanji' => $mUser->qibu_zhanji,
		]);
	}
	
	public function actionAddLianmeng(){
		$name = (string)Yii::$app->request->post('name');
		$qianzhang = (int)Yii::$app->request->post('qianzhang');
		$duizhangfangfa = (int)Yii::$app->request->post('duizhangfangfa');
		$paijuFee = (int)Yii::$app->request->post('paijuFee');
		$baoxianChoucheng = (int)Yii::$app->request->post('baoxianChoucheng');
		$paijuCreater = (string)Yii::$app->request->post('paijuCreater');
		$lmzjPaijuCreater = (string)Yii::$app->request->post('lmzjPaijuCreater');
		
		if(!$name){
			return new Response('请输入联盟名称', -1);
		}
		if(!$duizhangfangfa){
			$duizhangfangfa = HostLianmeng::DUIZHANGFANGFA_LINDIANJIUQIWU;
		}
		if(!in_array($duizhangfangfa, [HostLianmeng::DUIZHANGFANGFA_LINDIANJIUQIWU, HostLianmeng::DUIZHANGFANGFA_WUSHUIDUIZHANG, HostLianmeng::DUIZHANGFANGFA_LINDIANJIUBAWU])){
			return new Response('对账方法有误', -1);
		}
		$mUser = Yii::$app->user->getIdentity();
		
		$mLianmeng = HostLianmeng::findOne(['user_id' => $mUser->id, 'name' => $name, 'is_delete' => 0]);
		if($mLianmeng){
			return new Response('联盟已存在', -1);
		}
		$mLianmeng = HostLianmeng::findOne(['user_id' => $mUser->id, 'name' => $name, 'is_delete' => 1]);
		if($mLianmeng){
			$mLianmeng->set('is_delete', 0);
			$mLianmeng->save();
			return new Response('添加成功', 1);
		}
		if($paijuCreater){
			$mTempLianmeng = HostLianmeng::findOne(['user_id' => $mUser->id, 'paiju_creater' => $paijuCreater]);
			if($mTempLianmeng){
				return new Response('开桌人名字不能重复', -1);
			}
		}
		$isSuccess = HostLianmeng::addRecord([
			'user_id' => $mUser->id,
			'name' => $name,
			'qianzhang' => $qianzhang,
			'duizhangfangfa' => $duizhangfangfa,
			'paiju_fee' => $paijuFee,
			'baoxian_choucheng' => $baoxianChoucheng,
			'paiju_creater' => $paijuCreater,
			'lmzj_paiju_creater' => $lmzjPaijuCreater,
			'create_time' => NOW_TIME,
		]);
		if(!$isSuccess){
			return new Response('添加失败', 0);
		}
		$mLianmeng = HostLianmeng::findOne($isSuccess);
		$aLianmeng = $mLianmeng->toArray();
		$mUser->operateLog(18, ['aLianmeng' => $aLianmeng]);
		
		return new Response('添加成功', 1);
	}
	
	public function actionSaveLianmeng(){
		$id = (int)Yii::$app->request->post('id');
		$name = (string)Yii::$app->request->post('name');
		$qianzhang = (int)Yii::$app->request->post('qianzhang');
		$duizhangfangfa = (int)Yii::$app->request->post('duizhangfangfa');
		$paijuFee = (int)Yii::$app->request->post('paijuFee');
		$baoxianChoucheng = (int)Yii::$app->request->post('baoxianChoucheng');
		$paijuCreater = (string)Yii::$app->request->post('paijuCreater');
		
		if(!$name){
			return new Response('请输入联盟名称', -1);
		}
		if(!in_array($duizhangfangfa, [HostLianmeng::DUIZHANGFANGFA_LINDIANJIUQIWU, HostLianmeng::DUIZHANGFANGFA_WUSHUIDUIZHANG, HostLianmeng::DUIZHANGFANGFA_LINDIANJIUBAWU])){
			return new Response('对账方法有误', -1);
		}
		if($paijuCreater){
			$mTempLianmeng = HostLianmeng::findOne(['user_id' => $mUser->id, 'paiju_creater' => $paijuCreater]);
			if($mTempLianmeng && !$id){
				return new Response('开桌人名字不能重复', -1);
			}
		}
		$mUser = Yii::$app->user->getIdentity();
		if(!$id){
			$mLianmeng = HostLianmeng::findOne(['user_id' => $mUser->id, 'name' => $name]);
			if($mLianmeng && $mLianmeng->is_delete){
				$aOldRecord = $mLianmeng->toArray();
				$mLianmeng->set('name', $name);
				$mLianmeng->set('qianzhang', $qianzhang);
				$mLianmeng->set('duizhangfangfa', $duizhangfangfa);
				$mLianmeng->set('paiju_fee', $paijuFee);
				$mLianmeng->set('baoxian_choucheng', $baoxianChoucheng);
				$mLianmeng->set('paiju_creater', $paijuCreater);
				$mLianmeng->set('is_delete', 0);
				$mLianmeng->save();
				$aNewRecord = $mLianmeng->toArray();
				if($aOldRecord['name'] != $aNewRecord['name']){
					$mUser->operateLog(20, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
				}
				if($aOldRecord['qianzhang'] != $aNewRecord['qianzhang']){
					$mUser->operateLog(21, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
				}
				if($aOldRecord['duizhangfangfa'] != $aNewRecord['duizhangfangfa']){
					$mUser->operateLog(22, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
				}
				if($aOldRecord['paiju_fee'] != $aNewRecord['paiju_fee']){
					$mUser->operateLog(23, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
				}
				if($aOldRecord['baoxian_choucheng'] != $aNewRecord['baoxian_choucheng']){
					$mUser->operateLog(24, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
				}
				return new Response('保存成功', 1, $mLianmeng->id);
			}
			if($mLianmeng && $mLianmeng->id != $id){
				return new Response('联盟名字已存在', -1);
			}
			$isSuccess = HostLianmeng::addRecord([
				'user_id' => $mUser->id,
				'name' => $name,
				'qianzhang' => $qianzhang,
				'duizhangfangfa' => $duizhangfangfa,
				'paiju_fee' => $paijuFee,
				'baoxian_choucheng' => $baoxianChoucheng,
				'paiju_creater' => $paijuCreater,
				'create_time' => NOW_TIME,
			]);
			if(!$isSuccess){
				return new Response('保存失败', 0);
			}
			$id = $isSuccess;
			$mLianmeng = HostLianmeng::findOne($id);
			$aLianmeng = $mLianmeng->toArray();
			$mUser->operateLog(18, ['aLianmeng' => $aLianmeng]);
		}else{
			$mLianmeng = HostLianmeng::findOne($id);
			if(!$mLianmeng){
				return new Response('联盟不存在', -1);
			}
			if($mLianmeng->user_id != $mUser->id){
				return new Response('出错了', 0);
			}
			$aOldRecord = $mLianmeng->toArray();
			$mLianmeng->set('name', $name);
			$mLianmeng->set('qianzhang', $qianzhang);
			$mLianmeng->set('duizhangfangfa', $duizhangfangfa);
			$mLianmeng->set('paiju_fee', $paijuFee);
			$mLianmeng->set('baoxian_choucheng', $baoxianChoucheng);
			$mLianmeng->set('paiju_creater', $paijuCreater);
			$mLianmeng->save();
			$aNewRecord = $mLianmeng->toArray();
			if($aOldRecord['name'] != $aNewRecord['name']){
				$mUser->operateLog(20, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
			}
			if($aOldRecord['qianzhang'] != $aNewRecord['qianzhang']){
				$mUser->operateLog(21, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
			}
			if($aOldRecord['duizhangfangfa'] != $aNewRecord['duizhangfangfa']){
				$mUser->operateLog(22, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
			}
			if($aOldRecord['paiju_fee'] != $aNewRecord['paiju_fee']){
				$mUser->operateLog(23, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
			}
			if($aOldRecord['baoxian_choucheng'] != $aNewRecord['baoxian_choucheng']){
				$mUser->operateLog(24, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
			}
		}
		return new Response('保存成功', 1, $id);
	}
	
	public function actionGetList(){
		$mUser = Yii::$app->user->getIdentity();
		
		$aList = $mUser->getHostLianmengList();
		
		return new Response('', 1, $aList);
	}
	
	public function actionUpdateLianmengInfo(){
		$id = (int)Yii::$app->request->post('id');
		$type = (string)Yii::$app->request->post('type');
		$value = Yii::$app->request->post('value');
		
		$mUser = Yii::$app->user->getIdentity();
		if(!in_array($type, ['name', 'qianzhang', 'duizhangfangfa', 'paiju_fee', 'baoxian_choucheng', 'paiju_creater', 'lmzj_paiju_creater'])){
			return new Response('出错啦', 0);
		}
		if($type == 'paiju_creater'){
			if($value){
				$mTempLianmeng = HostLianmeng::findOne(['user_id' => $mUser->id, 'paiju_creater' => (string)$value]);
				if($mTempLianmeng){
					return new Response('开桌人名字不能重复', -1);
				}
			}
		}
		if($type == 'name' || $type == 'paiju_creater'){
			$value = (string)$value;
		}elseif($type == 'lmzj_paiju_creater'){
			//$value = (array)$value;
			$value = (string)$value;
		}else{
			$value = (int)$value;
		}
		$mLianmeng = HostLianmeng::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'is_delete' => 0]);
		if(!$mLianmeng){
			return new Response('联盟不存在', 0);
		}
		$aOldRecord = $mLianmeng->toArray();
		$mLianmeng->set($type, $value);
		$mLianmeng->save();
		$aNewRecord = $mLianmeng->toArray();
		if($type == 'name' && $aOldRecord['name'] != $aNewRecord['name']){
			$mUser->operateLog(20, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		if($type == 'qianzhang' && $aOldRecord['qianzhang'] != $aNewRecord['qianzhang']){
			$mUser->operateLog(21, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		if($type == 'duizhangfangfa' && $aOldRecord['duizhangfangfa'] != $aNewRecord['duizhangfangfa']){
			$mUser->operateLog(22, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		if($type == 'paiju_fee' && $aOldRecord['paiju_fee'] != $aNewRecord['paiju_fee']){
			$mUser->operateLog(23, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		if($type == 'baoxian_choucheng' && $aOldRecord['baoxian_choucheng'] != $aNewRecord['baoxian_choucheng']){
			$mUser->operateLog(24, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		
		return new Response('保存成功', 1);
	}
	
	public function actionDelete(){
		$id = (int)Yii::$app->request->post('id');
		
		$mUser = Yii::$app->user->getIdentity();
		$mLianmeng = HostLianmeng::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'is_delete' => 0]);
		if(!$mLianmeng){
			return new Response('联盟不存在', 0);
		}
		$aLianmengList = $mUser->getHostLianmengList();
		/*if(count($aLianmengList) == 1){
			return new Response('必须保留一个联盟', 0);
		}
		if(!$mLianmeng->checkIsCanDelete()){
			return new Response('联盟尚有账单未清账，不能删除', -1);
		}*/
		$mLianmeng->set('is_delete', 1);
		$mLianmeng->save();
		$aLianmeng = $mLianmeng->toArray();
		$mUser->operateLog(19, ['aLianmeng' => $aLianmeng]);
		
		return new Response('删除成功', 1);
	}
	
	
	public function actionAddLianmengClub(){
		$id = (string)Yii::$app->request->post('id');
		$clubName = (string)Yii::$app->request->post('clubName');
		$clubId = (int)Yii::$app->request->post('clubId');
		$duizhangfangfa = (int)Yii::$app->request->post('duizhangfangfa');
		$paijuFee = (int)Yii::$app->request->post('paijuFee');
		$baoxianChoucheng = (int)Yii::$app->request->post('baoxianChoucheng');
		$qibuZhanji = (int)Yii::$app->request->post('qibuZhanji');
		
		$mLianmeng = HostLianmeng::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'is_delete' => 0]);
		if(!$mLianmeng){
			return new Response('联盟不存在', 0);
		}
		if(!$clubId){
			return new Response('请输入俱乐部ID', -1);
		}
		if(!in_array($duizhangfangfa, [HostLianmeng::DUIZHANGFANGFA_LINDIANJIUQIWU, HostLianmeng::DUIZHANGFANGFA_WUSHUIDUIZHANG, HostLianmeng::DUIZHANGFANGFA_LINDIANJIUBAWU])){
			return new Response('对账方法有误', -1);
		}
		$mUser = Yii::$app->user->getIdentity();
		
		$mLianmengClub = HostLianmengClub::findOne(['user_id' => $mUser->id, 'lianmeng_id' => $id, 'club_id' => $clubId, 'is_delete' => 0]);
		if($mLianmengClub){
			return new Response('俱乐部已存在', -1);
		}
		$isSuccess = HostLianmengClub::addRecord([
			'user_id' => $mUser->id,
			'lianmeng_id' => $id,
			'club_id' => $clubId,
			'club_name' => $clubName,
			'duizhangfangfa' => $duizhangfangfa,
			'paiju_fee' => $paijuFee,
			'baoxian_choucheng' => $baoxianChoucheng,
			'qibu_zhanji' => $qibuZhanji,
			'create_time' => NOW_TIME,
		]);
		if(!$isSuccess){
			return new Response('添加失败', 0);
		}
		$mLianmengClub = HostLianmengClub::findOne($isSuccess);
		$aLianmengClub = $mLianmengClub->toArray();
		$mUser->operateLog(30, ['aLianmengClub' => $aLianmengClub]);
		
		return new Response('添加成功', 1);
	}
	
	public function actionGetClubList(){
		$id = (string)Yii::$app->request->post('id');
		
		$mLianmeng = HostLianmeng::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'is_delete' => 0]);
		if(!$mLianmeng){
			return new Response('联盟不存在', 0);
		}
		$mUser = Yii::$app->user->getIdentity();
		
		$aList = $mLianmeng->getLianmengClubList();
		
		return new Response('', 1, [
			'list' => $aList,
			'aLianmeng' => $mLianmeng->toArray(),
		]);
	}
	
	public function actionUpdateLianmengClubInfo(){
		$id = (int)Yii::$app->request->post('id');
		$type = (string)Yii::$app->request->post('type');
		$value = Yii::$app->request->post('value');
		
		$mUser = Yii::$app->user->getIdentity();
		if(!in_array($type, ['club_id', 'club_name', 'qianzhang', 'duizhangfangfa', 'paiju_fee', 'baoxian_choucheng', 'qibu_zhanji'])){
			return new Response('出错啦', 0);
		}
		if($type == 'club_id'){
			$mTempLianmengClub = HostLianmengClub::findOne(['user_id' => Yii::$app->user->id, 'club_id' => $value]);
			if($mTempLianmengClub && $mTempLianmengClub->id != $id){
				return new Response('俱乐部已存在', -1);
			}
		}
		if($type == 'club_name'){
			$value = (string)$value;
		}else{
			$value = (int)$value;
		}
		$mLianmengClub = HostLianmengClub::findOne($id);
		if(!$mLianmengClub){
			return new Response('俱乐部不存在', 0);
		}
		if($mLianmengClub->user_id != Yii::$app->user->id){
			return new Response('出错啦', 0);
		}
		$aOldRecord = $mLianmengClub->toArray();
		$mLianmengClub->set($type, $value);
		$mLianmengClub->save();
		$aNewRecord = $mLianmengClub->toArray();
		if($type == 'club_id' && $aOldRecord['club_id'] != $aNewRecord['club_id']){
			$mUser->operateLog(32, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		if($type == 'club_name' && $aOldRecord['club_name'] != $aNewRecord['club_name']){
			$mUser->operateLog(33, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		if($type == 'qianzhang' && $aOldRecord['qianzhang'] != $aNewRecord['qianzhang']){
			$mUser->operateLog(34, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		if($type == 'duizhangfangfa' && $aOldRecord['duizhangfangfa'] != $aNewRecord['duizhangfangfa']){
			$mUser->operateLog(35, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		if($type == 'paiju_fee' && $aOldRecord['paiju_fee'] != $aNewRecord['paiju_fee']){
			$mUser->operateLog(36, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		if($type == 'baoxian_choucheng' && $aOldRecord['baoxian_choucheng'] != $aNewRecord['baoxian_choucheng']){
			$mUser->operateLog(37, ['aOldRecord' => $aOldRecord, 'aNewRecord' => $aNewRecord]);
		}
		
		return new Response('更新成功', 1);
	}
	
	public function actionDeleteClub(){
		$id = (int)Yii::$app->request->post('id');
		
		$mUser = Yii::$app->user->getIdentity();
		$mLianmengClub = HostLianmengClub::findOne(['id' => $id]);
		if(!$mLianmengClub){
			return new Response('俱乐部不存在', 0);
		}
		if($mLianmengClub->user_id != Yii::$app->user->id){
			return new Response('出错啦', 0);
		}
		$mLianmengClub->set('is_delete', 1);
		$mLianmengClub->save();
		$aLianmengClub = $mLianmengClub->toArray();
		$mUser->operateLog(31, ['aLianmengClub' => $aLianmengClub]);
		
		return new Response('删除成功', 1);
	}
	
	public function actionLianmengClubQinZhang(){
		$id = Yii::$app->request->post('id');
		//$aLianmengHostDuizhang = (string)Yii::$app->request->post('aLianmengHostDuizhang');
		//$aLianmengHostDuizhang = json_decode($aLianmengHostDuizhang,1);
		
		$mLianmeng = HostLianmeng::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'is_delete' => 0]);
		if(!$mLianmeng){
			return new Response('联盟不存在', 0);
		}
		$mUser = Yii::$app->user->getIdentity();
		$aLianmengHostDuizhang = $mUser->getLianmengHostDuizhang($id);
		if(!$aLianmengHostDuizhang){
			return new Response('清账失败', 0);
		}
		$aClubZhangDan = [];
		foreach($aLianmengHostDuizhang['aClubZhangDanList'] as $aClubZhangDanList){
			array_push($aClubZhangDan, [
				'club_id' => $aClubZhangDanList['club_id'],
				'zhang_dan' => $aClubZhangDanList['zhang_dan'],
			]);
		}
		/*if(!$aClubZhangDan){
			return new Response('新账单为0', -1);
		}*/
		if(!$mUser->clubQinZhang($mLianmeng, $aClubZhangDan, $aLianmengHostDuizhang)){
			return new Response('清账失败', 0);
		}
		return new Response('清账成功', 1);
	}
	
	public function actionRefreshLianmengPaijuTime(){
		$id = Yii::$app->request->post('id');
		
		$mLianmeng = HostLianmeng::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'is_delete' => 0]);
		if(!$mLianmeng){
			return new Response('联盟不存在', 0);
		}
		$mLianmeng->set('update_paiju_time', 0);
		$mLianmeng->save();
		
		return new Response('更新成功', 1);
	}
	
	public function actionExportClubPaijuStatistic(){
		$id = (int)Yii::$app->request->get('id');
		$clubId = (int)Yii::$app->request->get('clubId');
		
		$mLianmeng = HostLianmeng::findOne($id);
		if(!$mLianmeng){
			return new Response('联盟不存在', 0);
		}
		$mLianmengClub = HostLianmengClub::findOne([
			'user_id' => Yii::$app->user->id,
			'lianmeng_id' => $id,
			'club_id' => $clubId,
		]);
		if(!$mLianmengClub){
			return new Response('俱乐部不存在', 0);
		}
		
		$mUser = Yii::$app->user->getIdentity();
		$aImportDataId = $mUser->getLianmengHostDuizhang($id, true);
		if(!$aImportDataId){
			return new Response('暂无数据', 0);
		}
		$aClubZhangDanList = ImportData::findAll(
			['id' => $aImportDataId, 'user_id' => $mUser->id, 'club_id' => $clubId],
			['id', 'paiju_id', 'paiju_name', 'zhanji', 'choushui_value', 'baoxian_heji', 'club_name', 'mangzhu', 'paizuo', 'player_name']
		);
		
		/*$aLianmengHostDuizhang = $mUser->getLianmengHostDuizhang($id);
		
		$aClubZhangDanList = [];
		if(isset($aLianmengHostDuizhang['aClubZhangDanList'][$clubId]['club_zhang_dan_list'])){
			$aClubZhangDanList = array_values($aLianmengHostDuizhang['aClubZhangDanList'][$clubId]['club_zhang_dan_list']);
		}
		*/
		$xishu = (string)(1 - 0.975);
		if($mLianmengClub->duizhangfangfa == HostLianmeng::DUIZHANGFANGFA_LINDIANJIUBAWU){
			$xishu = (string)(1 - 0.985);
		}elseif($mLianmengClub->duizhangfangfa == HostLianmeng::DUIZHANGFANGFA_LINDIANJIUWU){
			$xishu = (string)(1 - 0.95);
		}
		foreach($aClubZhangDanList as $k => $aClubZhangDan){
			$floatBaoxianBeichou = Calculate::calculateBaoxianBeichou($aClubZhangDan['baoxian_heji'], $mLianmengClub->baoxian_choucheng, $mUser->choushui_shuanfa, false);
			$aClubZhangDanList[$k]['float_baoxian_beichou'] = (string)$floatBaoxianBeichou;
			$aClubZhangDanList[$k]['zhanji_add_baoxian'] = $aClubZhangDan['zhanji'] + $aClubZhangDan['baoxian_heji'];
			$fanDian = $aClubZhangDanList[$k]['zhanji_add_baoxian'] * $xishu;
			$aClubZhangDanList[$k]['fan_dian'] = $fanDian;
			$aClubZhangDanList[$k]['jie_shuan'] = $aClubZhangDanList[$k]['zhanji_add_baoxian'] - $fanDian - $aClubZhangDanList[$k]['float_baoxian_beichou'];
		}
		if(!$aClubZhangDanList){
			return new Response('暂无数据', 0);
		}
		$aDataList = [
			['牌桌', '盲注', '俱乐部名称', '牌局名', '玩家名称', '战绩', '保险', '合计', '反点', '保险被抽', '结算'],
		];
		$totalJieShuan = 0;
		foreach($aClubZhangDanList as $aClubZhangDan){
			array_push($aDataList, [
				$aClubZhangDan['paizuo'],
				$aClubZhangDan['mangzhu'],
				$aClubZhangDan['club_name'],
				$aClubZhangDan['paiju_name'],
				$aClubZhangDan['player_name'],
				$aClubZhangDan['zhanji'],
				$aClubZhangDan['baoxian_heji'],
				$aClubZhangDan['zhanji_add_baoxian'],
				$aClubZhangDan['fan_dian'],
				$aClubZhangDan['float_baoxian_beichou'],
				$aClubZhangDan['jie_shuan'],
			]);
			$totalJieShuan += $aClubZhangDan['jie_shuan'];
		}
		array_push($aDataList, [
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			$totalJieShuan,
			'合计',
		]);
		$fileName = '联盟俱乐部数据(' . $mLianmengClub->club_name . ').xlsx';
		Yii::$app->excel->setSheetDataFromArray($fileName, $aDataList, true);
	}
	
}
