<?php
namespace home\controllers;

use Yii;
use umeworld\lib\Controller;
//use home\lib\Controller;
use umeworld\lib\Response;

class SiteController extends Controller{
	
	public function actionIndex(){
		 $this->layout = 'login'; 
		return $this->render('index');
	}

}
