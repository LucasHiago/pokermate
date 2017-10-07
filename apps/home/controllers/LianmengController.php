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

class LianmengController extends Controller{
	
	public function actionLianmengHostDuizhang(){
		$id = (int)Yii::$app->request->get('id');
		
		$mUser = Yii::$app->user->getIdentity();
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
		$isSuccess = Lianmeng::addRecord([
			'user_id' => $mUser->id,
			'name' => $name,
			'qianzhang' => $qianzhang,
			'duizhangfangfa' => $duizhangfangfa,
			'paiju_fee' => $paijuFee,
			'baoxian_choucheng' => $baoxianChoucheng,
			'create_time' => NOW_TIME,
		]);
		if(!$isSuccess){
			return new Response('添加失败', 0);
		}
		return new Response('添加成功', 1);
	}
	
	public function actionSaveLianmeng(){
		$id = (int)Yii::$app->request->post('id');
		$name = (string)Yii::$app->request->post('name');
		$qianzhang = (int)Yii::$app->request->post('qianzhang');
		$duizhangfangfa = (int)Yii::$app->request->post('duizhangfangfa');
		$paijuFee = (int)Yii::$app->request->post('paijuFee');
		$baoxianChoucheng = (int)Yii::$app->request->post('baoxianChoucheng');
		
		if(!$name){
			return new Response('请输入联盟名称', -1);
		}
		if(!in_array($duizhangfangfa, [Lianmeng::DUIZHANGFANGFA_LINDIANJIUQIWU, Lianmeng::DUIZHANGFANGFA_WUSHUIDUIZHANG])){
			return new Response('对账方法有误', -1);
		}
		$mUser = Yii::$app->user->getIdentity();
		if(!$id){
			$mLianmeng = Lianmeng::findOne(['user_id' => $mUser->id, 'name' => $name]);
			if($mLianmeng && $mLianmeng->is_delete){
				$mLianmeng->set('name', $name);
				$mLianmeng->set('qianzhang', $qianzhang);
				$mLianmeng->set('duizhangfangfa', $duizhangfangfa);
				$mLianmeng->set('paiju_fee', $paijuFee);
				$mLianmeng->set('baoxian_choucheng', $baoxianChoucheng);
				$mLianmeng->set('is_delete', 0);
				$mLianmeng->save();
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
				'create_time' => NOW_TIME,
			]);
			if(!$isSuccess){
				return new Response('保存失败', 0);
			}
			$id = $isSuccess;
		}else{
			$mLianmeng = Lianmeng::findOne($id);
			if(!$mLianmeng){
				return new Response('联盟不存在', -1);
			}
			if($mLianmeng->user_id != $mUser->id){
				return new Response('出错了', 0);
			}
			$mLianmeng->set('name', $name);
			$mLianmeng->set('qianzhang', $qianzhang);
			$mLianmeng->set('duizhangfangfa', $duizhangfangfa);
			$mLianmeng->set('paiju_fee', $paijuFee);
			$mLianmeng->set('baoxian_choucheng', $baoxianChoucheng);
			$mLianmeng->save();
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
		
		if(!in_array($type, ['name', 'qianzhang', 'duizhangfangfa', 'paiju_fee', 'baoxian_choucheng'])){
			return new Response('出错啦', 0);
		}
		if($type == 'name'){
			$value = (string)$value;
		}else{
			$value = (int)$value;
		}
		$mLianmeng = Lianmeng::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'is_delete' => 0]);
		if(!$mLianmeng){
			return new Response('联盟不存在', 0);
		}
		$mLianmeng->set($type, $value);
		$mLianmeng->save();
		
		return new Response('更新成功', 1);
	}
	
	public function actionDelete(){
		$id = (int)Yii::$app->request->post('id');
		
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
		$mLianmengClub->set($type, $value);
		$mLianmengClub->save();
		
		return new Response('更新成功', 1);
	}
	
	public function actionDeleteClub(){
		$id = (int)Yii::$app->request->post('id');
		
		$mLianmengClub = LianmengClub::findOne(['id' => $id]);
		if(!$mLianmengClub){
			return new Response('俱乐部不存在', 0);
		}
		if($mLianmengClub->user_id != Yii::$app->user->id){
			return new Response('出错啦', 0);
		}
		$mLianmengClub->set('is_delete', 1);
		$mLianmengClub->save();
		
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
		$aLianmengZhangDanDetailList = $mUser->getLianmengZhangDanDetailList($id);
		$totalZhangDan = 0;
		foreach($aLianmengZhangDanDetailList as $aLianmengZhangDanDetail){
			$totalZhangDan += $aLianmengZhangDanDetail['zhang_dan'];
		}
		$aReturn = [
			'list' => $aLianmengZhangDanDetailList,
			'totalZhangDan' => $totalZhangDan,
			'aLianmengList' => $aLianmengList,
		];
		
		return new Response('', 1, $aReturn);
	}
	
	public function actionQinZhang(){
		$id = Yii::$app->request->post('id');
		
		$mLianmeng = Lianmeng::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'is_delete' => 0]);
		if(!$mLianmeng){
			return new Response('联盟不存在', 0);
		}
		$mUser = Yii::$app->user->getIdentity();
		$aLianmengZhongZhangList = $mUser->getLianmengZhongZhangList();
		if(!isset($aLianmengZhongZhangList[$id])){
			return new Response('清账失败', 0);
		}
		$zhangDan = $aLianmengZhongZhangList[$id]['lianmeng_zhang_dan'];
		if(!$zhangDan){
			return new Response('新账单为0', -1);
		}
		if(!$mUser->qinZhang($mLianmeng, $zhangDan)){
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
		if(!$aClubZhangDan){
			return new Response('新账单为0', -1);
		}
		if(!$mUser->clubQinZhang($mLianmeng, $aClubZhangDan)){
			return new Response('清账失败', 0);
		}
		return new Response('清账成功', 1);
	}
	
}
