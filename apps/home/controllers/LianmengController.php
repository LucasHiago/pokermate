<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\Lianmeng;
use common\model\LianmengClub;
use common\model\Calculate;

class LianmengController extends Controller{
	
	public function actionLianmengHostDuizhang(){
		$id = (int)Yii::$app->request->get('id');
		
		$mUser = Yii::$app->user->getIdentity();
		if(!$mUser->is_active){
			return new Response('提示:您的账号还没开始启用！', 0);
		}
		if(!$mUser->hasLianmengHostDuiZhangFunction()){
			return new Response('抱歉，您还没开通这个功能使用权限！', 0);
		}
		$aLianmengList = $mUser->getLianmengList();
		if($id){
			$mLianmeng = Lianmeng::findOne(['id' => $id, 'user_id' => $mUser->id, 'is_delete' => 0]);
			if(!$mLianmeng){
				$id = $mUser->getDefaultLianmengId();
			}
		}else{
			$id = $mUser->getDefaultLianmengId();
		}
		$aLianmengHostDuizhang = $mUser->getLianmengHostDuizhang($id);
		
		return $this->render('lianmeng_host_duizhang', [
			'aLianmengList' => $aLianmengList,
			'aLianmengHostDuizhang' => $aLianmengHostDuizhang,
			'lianmengId' => $id,
		]);
	}
	
	public function actionAddLianmeng(){
		$name = (string)Yii::$app->request->post('name');
		$qianzhang = (int)Yii::$app->request->post('qianzhang');
		$duizhangfangfa = (int)Yii::$app->request->post('duizhangfangfa');
		$paijuFee = (int)Yii::$app->request->post('paijuFee');
		$baoxianChoucheng = (int)Yii::$app->request->post('baoxianChoucheng');
		$paijuCreater = (string)Yii::$app->request->post('paijuCreater');
		
		if(!$name){
			return new Response('请输入联盟名称', -1);
		}
		if(!in_array($duizhangfangfa, [Lianmeng::DUIZHANGFANGFA_LINDIANJIUQIWU, Lianmeng::DUIZHANGFANGFA_WUSHUIDUIZHANG])){
			return new Response('对账方法有误', -1);
		}
		$mUser = Yii::$app->user->getIdentity();
		
		$mLianmeng = Lianmeng::findOne(['user_id' => $mUser->id, 'name' => $name, 'is_delete' => 0]);
		if($mLianmeng){
			return new Response('联盟已存在', -1);
		}
		$mLianmeng = Lianmeng::findOne(['user_id' => $mUser->id, 'name' => $name, 'is_delete' => 1]);
		if($mLianmeng){
			$mLianmeng->set('is_delete', 0);
			$mLianmeng->save();
			return new Response('添加成功', 1);
		}
		if($paijuCreater){
			$mTempLianmeng = Lianmeng::findOne(['user_id' => $mUser->id, 'paiju_creater' => $paijuCreater]);
			if($mTempLianmeng){
				return new Response('开桌人名字不能重复', -1);
			}
		}
		$isSuccess = Lianmeng::addRecord([
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
			return new Response('添加失败', 0);
		}
		$mLianmeng = Lianmeng::findOne($isSuccess);
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
		if(!in_array($duizhangfangfa, [Lianmeng::DUIZHANGFANGFA_LINDIANJIUQIWU, Lianmeng::DUIZHANGFANGFA_WUSHUIDUIZHANG])){
			return new Response('对账方法有误', -1);
		}
		if($paijuCreater){
			$mTempLianmeng = Lianmeng::findOne(['user_id' => $mUser->id, 'paiju_creater' => $paijuCreater]);
			if($mTempLianmeng && !$id){
				return new Response('开桌人名字不能重复', -1);
			}
		}
		$mUser = Yii::$app->user->getIdentity();
		if(!$id){
			$mLianmeng = Lianmeng::findOne(['user_id' => $mUser->id, 'name' => $name]);
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
			$isSuccess = Lianmeng::addRecord([
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
			$mLianmeng = Lianmeng::findOne($id);
			$aLianmeng = $mLianmeng->toArray();
			$mUser->operateLog(18, ['aLianmeng' => $aLianmeng]);
		}else{
			$mLianmeng = Lianmeng::findOne($id);
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
		
		$aList = $mUser->getLianmengList();
		
		return new Response('', 1, $aList);
	}
	
	public function actionUpdateLianmengInfo(){
		$id = (int)Yii::$app->request->post('id');
		$type = (string)Yii::$app->request->post('type');
		$value = Yii::$app->request->post('value');
		
		$mUser = Yii::$app->user->getIdentity();
		if(!in_array($type, ['name', 'qianzhang', 'duizhangfangfa', 'paiju_fee', 'baoxian_choucheng', 'paiju_creater'])){
			return new Response('出错啦', 0);
		}
		if($type == 'paiju_creater'){
			if($value){
				$mTempLianmeng = Lianmeng::findOne(['user_id' => $mUser->id, 'paiju_creater' => (string)$value]);
				if($mTempLianmeng){
					return new Response('开桌人名字不能重复', -1);
				}
			}
		}
		if($type == 'name' || $type == 'paiju_creater'){
			$value = (string)$value;
		}else{
			$value = (int)$value;
		}
		$mLianmeng = Lianmeng::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'is_delete' => 0]);
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
		
		return new Response('更新成功', 1);
	}
	
	public function actionDelete(){
		$id = (int)Yii::$app->request->post('id');
		
		$mUser = Yii::$app->user->getIdentity();
		$mLianmeng = Lianmeng::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'is_delete' => 0]);
		if(!$mLianmeng){
			return new Response('联盟不存在', 0);
		}
		$aLianmengList = $mUser->getLianmengList();
		if(count($aLianmengList) == 1){
			return new Response('必须保留一个联盟', 0);
		}
		if(!$mLianmeng->checkIsCanDelete()){
			return new Response('联盟尚有账单未清账，不能删除', -1);
		}
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
		
		$mLianmeng = Lianmeng::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'is_delete' => 0]);
		if(!$mLianmeng){
			return new Response('联盟不存在', 0);
		}
		if(!$clubId){
			return new Response('请输入俱乐部ID', -1);
		}
		if(!in_array($duizhangfangfa, [Lianmeng::DUIZHANGFANGFA_LINDIANJIUQIWU, Lianmeng::DUIZHANGFANGFA_WUSHUIDUIZHANG])){
			return new Response('对账方法有误', -1);
		}
		$mUser = Yii::$app->user->getIdentity();
		
		$mLianmengClub = LianmengClub::findOne(['user_id' => $mUser->id, 'lianmeng_id' => $id, 'club_id' => $clubId, 'is_delete' => 0]);
		if($mLianmengClub){
			return new Response('俱乐部已存在', -1);
		}
		$isSuccess = LianmengClub::addRecord([
			'user_id' => $mUser->id,
			'lianmeng_id' => $id,
			'club_id' => $clubId,
			'club_name' => $clubName,
			'duizhangfangfa' => $duizhangfangfa,
			'paiju_fee' => $paijuFee,
			'baoxian_choucheng' => $baoxianChoucheng,
			'create_time' => NOW_TIME,
		]);
		if(!$isSuccess){
			return new Response('添加失败', 0);
		}
		$mLianmengClub = LianmengClub::findOne($isSuccess);
		$aLianmengClub = $mLianmengClub->toArray();
		$mUser->operateLog(30, ['aLianmengClub' => $aLianmengClub]);
		
		return new Response('添加成功', 1);
	}
	
	public function actionGetClubList(){
		$id = (string)Yii::$app->request->post('id');
		
		$mLianmeng = Lianmeng::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'is_delete' => 0]);
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
		if(!in_array($type, ['club_id', 'club_name', 'qianzhang', 'duizhangfangfa', 'paiju_fee', 'baoxian_choucheng'])){
			return new Response('出错啦', 0);
		}
		if($type == 'club_id'){
			$mTempLianmengClub = LianmengClub::findOne(['user_id' => Yii::$app->user->id, 'club_id' => $value]);
			if($mTempLianmengClub && $mTempLianmengClub->id != $id){
				return new Response('俱乐部已存在', -1);
			}
		}
		if($type == 'club_name'){
			$value = (string)$value;
		}else{
			$value = (int)$value;
		}
		$mLianmengClub = LianmengClub::findOne($id);
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
		$mLianmengClub = LianmengClub::findOne(['id' => $id]);
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
	
	public function actionGetLianmengZhongZhangList(){
		$mUser = Yii::$app->user->getIdentity();
		
		$aLianmengZhongZhangList = $mUser->getLianmengZhongZhangList();
		$totalZhongZhang = $mUser->getLianmengTotalZhongZhang();
		$aReturn = [
			'list' => $aLianmengZhongZhangList,
			'totalZhongZhang' => $totalZhongZhang,
			'lianmengZhongzhangAjustValue' => $mUser->lianmeng_zhongzhang_ajust_value,
		];
		
		return new Response('', 1, $aReturn);
	}
		
	public function actionGetLianmengZhangDanDetailList(){
		$id = Yii::$app->request->post('id');
		
		$mLianmeng = Lianmeng::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'is_delete' => 0]);
		if(!$mLianmeng){
			return new Response('联盟不存在', 0);
		}
		$mUser = Yii::$app->user->getIdentity();
		$aLianmengList = $mUser->getLianmengList();
		/*$aLianmengZhangDanDetailList = $mUser->getLianmengZhangDanDetailList($id);
		$totalZhangDan = 0;
		foreach($aLianmengZhangDanDetailList as $aLianmengZhangDanDetail){
			$totalZhangDan += $aLianmengZhangDanDetail['float_zhang_dan'];
		}
		$totalZhangDan = Calculate::getIntValueByChoushuiShuanfa($totalZhangDan, $mUser->choushui_shuanfa);*/
		$aReturn = $this->_getLianmengZhangDanDetailList($id);
		$aReturn['aLianmengList'] = $aLianmengList;
		/*$aReturn = [
			'list' => $aLianmengZhangDanDetailList,
			'totalZhangDan' => $totalZhangDan,
			'aLianmengList' => $aLianmengList,
		];*/
		
		return new Response('', 1, $aReturn);
	}
	
	private function _getLianmengZhangDanDetailList($id){
		$mUser = Yii::$app->user->getIdentity();
		$aLianmengList = $mUser->getLianmengList();
		$aLianmengZhangDanDetailList = $mUser->getLianmengZhangDanDetailList($id);
		$totalZhangDan = 0;
		foreach($aLianmengZhangDanDetailList as $aLianmengZhangDanDetail){
			$totalZhangDan += $aLianmengZhangDanDetail['float_zhang_dan'];
		}
		$totalZhangDan = Calculate::getIntValueByChoushuiShuanfa($totalZhangDan, $mUser->choushui_shuanfa);
		return [
			'list' => $aLianmengZhangDanDetailList,
			'totalZhangDan' => $totalZhangDan,
		];
	}
	
	public function actionQinZhang(){
		$id = Yii::$app->request->post('id');
		
		$mLianmeng = Lianmeng::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'is_delete' => 0]);
		if(!$mLianmeng){
			return new Response('联盟不存在', 0);
		}
		$aLianmengZhangDanDetailList = $this->_getLianmengZhangDanDetailList($id);
		$mUser = Yii::$app->user->getIdentity();
		$aLianmengZhongZhangList = $mUser->getLianmengZhongZhangList();
		if(!isset($aLianmengZhongZhangList[$id])){
			return new Response('清账失败', 0);
		}
		$zhangDan = $aLianmengZhongZhangList[$id]['lianmeng_zhang_dan'];
		if(!$zhangDan){
			return new Response('新账单为0', -1);
		}
		if(!$mUser->qinZhang($mLianmeng, $zhangDan, $aLianmengZhongZhangList[$id], $aLianmengZhangDanDetailList)){
			return new Response('清账失败', 0);
		}
		return new Response('清账成功', 1);
	}
	
	public function actionLianmengClubQinZhang(){
		$id = Yii::$app->request->post('id');
		
		$mLianmeng = Lianmeng::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'is_delete' => 0]);
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
	
}
