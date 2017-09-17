<?php
namespace common\model\form;

use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use common\model\KerenBenjin;

class KerenBenjinListForm extends \yii\base\Model{
	public $page = 1;
	public $pageSize = 15;

	public function rules(){
		return [
			['page', 'compare', 'compareValue' => 0, 'operator' => '>'],
		];
	}
	
	public function noCheck(){
		return true;
	}
	
	public function getList(){
		$aCondition = $this->getListCondition();
		$aControl = [
			'page' => $this->page,
			'page_size' => $this->pageSize,
			'order_by' => '`k1`.`id` DESC',
			'with_player_list' => true,
			'with_agent_info' => true,
		];
		$aList = KerenBenjin::getList1($aCondition, $aControl);

		return $aList;
	}

	public function getListCondition(){
		$mUser = Yii::$app->user->getIdentity();
		$aClubList = $mUser->getUserClubList();
		$aClubId = [];
		if($aClubList){
			$aClubId = ArrayHelper::getColumn($aClubList, 'club_id');
		}
		array_push($aClubId, 0);
		$aCondition = ['`k1`.`user_id`' => Yii::$app->user->id, '`k1`.`is_delete`' => 0, 'club_id' => $aClubId];
		
		return $aCondition;
	}

	public function getPageObject(){
		$aCondition = $this->getListCondition();
		$count = KerenBenjin::getCount1($aCondition);
		return new Pagination(['totalCount' => $count, 'pageSize' => $this->pageSize]);
	}
	
}