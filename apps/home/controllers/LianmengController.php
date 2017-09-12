<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\Lianmeng;

class LianmengController extends Controller{
	
	public function actionLianmengHostDuizhang(){
		return $this->render('lianmeng_host_duizhang');
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
		if(!$mLianmeng->checkIsCanDelete()){
			return new Response('联盟尚有账单未清账，不能删除', -1);
		}
		$mLianmeng->set('is_delete', 1);
		$mLianmeng->save();
		
		return new Response('删除成功', 1);
	}
	
	public function actionGetLianmengZhongZhangList(){
		$mUser = Yii::$app->user->getIdentity();
		
		$aLianmengZhongZhangList = $mUser->getLianmengZhongZhangList();
		$totalZhongZhang = 0;
		foreach($aLianmengZhongZhangList as $aLianmengZhongZhang){
			$totalZhongZhang += $aLianmengZhongZhang['lianmeng_zhong_zhang'];
		}
		$aReturn = [
			'list' => $aLianmengZhongZhangList,
			'totalZhongZhang' => $totalZhongZhang,
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
	
}
