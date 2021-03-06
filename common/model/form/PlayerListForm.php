<?php
namespace common\model\form;

use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class PlayerListForm extends \yii\base\Model{
	public $page = 1;
	public $pageSize = 9999999999;
	public $playerId = '';
	public $playerName = '';

	public function rules(){
		return [
			['page', 'compare', 'compareValue' => 0, 'operator' => '>'],
			['playerId', 'noCheck'],
			['playerName', 'noCheck'],
		];
	}
	
	public function noCheck(){
		return true;
	}
	
	public function getList(){
		$mUser = Yii::$app->user->getIdentity();
		$aList = $mUser->getAllPlayerInfoList($this->playerId, $this->playerName);

		return $aList;
	}
	
}