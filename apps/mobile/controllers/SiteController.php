<?php
namespace mobile\controllers;

use Yii;
use umeworld\lib\Controller;
//use mobile\lib\Controller;
use umeworld\lib\Response;

class SiteController extends Controller{
	
	public function actionIndex(){
		return $this->render('index');
	}

}
