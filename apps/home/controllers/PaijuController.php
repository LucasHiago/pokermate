<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use umeworld\lib\Cookie;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\Paiju;
use common\model\Lianmeng;

class PaijuController extends Controller{
	
	public function actionChangPaijuLianmeng(){
		$paijuId = (int)Yii::$app->request->post('paijuId');
		$lianmengId = (int)Yii::$app->request->post('lianmengId');
		
		$mUser = Yii::$app->user->getIdentity();
		
		$mPaiju = Paiju::findOne(['user_id' => $mUser->id, 'id' => $paijuId]);
		if(!$mPaiju){
			return new Response('牌局不存在', 0);
		}
		$mLianmeng = Lianmeng::findOne(['user_id' => $mUser->id, 'id' => $lianmengId, 'is_delete' => 0]);
		if(!$mLianmeng){
			return new Response('联盟不存在', 0);
		}
		$mPaiju->setLianmeng($lianmengId);
		
		Cookie::set('last_lianmeng_id_' . $mUser->id, $lianmengId);
		
		return new Response('更新牌局联盟成功', 1);
	}
	
}
