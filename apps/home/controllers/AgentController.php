<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;

class AgentController extends Controller{
	
	public function actionIndex(){
		return $this->render('agent');
	}
	
}
