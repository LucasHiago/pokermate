<?php
namespace common\model\form;

use Yii;
use yii\data\Pagination;
use common\model\User;

class UserListForm extends \yii\base\Model{
	public $page = 1;
	public $pageSize = 15;
	public $userId = 0;
	public $loginName = '';
	public $userName = '';
	public $mobile = '';
	public $email = '';

	public function rules(){
		return [
			['page', 'compare', 'compareValue' => 0, 'operator' => '>'],
			['userId', 'noCheck'],
			['loginName', 'noCheck'],
			['userName', 'noCheck'],
			['mobile', 'noCheck'],
			['email', 'noCheck'],
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
		$aList = User::getList($aCondition, $aControl);

		return $aList;
	}

	public function getListCondition(){
		$aCondition = ['is_forbidden' => 0];
		if($this->userId){
			$aCondition['id'] = $this->userId;
		}
		if($this->loginName){
			$aCondition['login_name_like'] = $this->loginName;
		}
		if($this->userName){
			$aCondition['name_like'] = $this->userName;
		}
		if($this->mobile){
			$aCondition['mobile'] = $this->mobile;
		}
		if($this->email){
			$aCondition['email'] = $this->email;
		}
				
		return $aCondition;
	}

	public function getPageObject(){
		$aCondition = $this->getListCondition();
		$count = User::getCount($aCondition);
		return new Pagination(['totalCount' => $count, 'pageSize' => $this->pageSize]);
	}
	
}