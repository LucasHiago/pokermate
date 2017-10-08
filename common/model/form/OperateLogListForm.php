<?php
namespace common\model\form;

use Yii;
use yii\data\Pagination;
use common\model\OperateLog;

class OperateLogListForm extends \yii\base\Model{
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
			'order_by' => ['id' => SORT_DESC],
		];
		$aList = OperateLog::getList($aCondition, $aControl);

		return $aList;
	}

	public function getListCondition(){
		$aCondition = ['user_id' => Yii::$app->user->id];
			
		return $aCondition;
	}

	public function getPageObject(){
		$aCondition = $this->getListCondition();
		$count = OperateLog::getCount($aCondition);
		return new Pagination(['totalCount' => $count, 'pageSize' => $this->pageSize]);
	}
	
}