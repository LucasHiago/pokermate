<?php
namespace home\controllers;

use Yii;
//use umeworld\lib\Controller;
use umeworld\lib\StringHelper;
use umeworld\lib\Url;
use home\lib\Controller;
use umeworld\lib\Response;
use common\model\ImportData;

class ImportController extends Controller{
	
	public function actionIndex(){
		$fileName = Yii::getAlias('@p.resource') . '/' . Yii::getAlias('@p.import') . '/test.xls';
		$aDataList = Yii::$app->excel->getSheetDataInArray($fileName);
		if($aDataList){
			ImportData::importFromExcelDataList($aDataList);
		}
		debug('导入Excel文成功', 11);
	}
	
}
