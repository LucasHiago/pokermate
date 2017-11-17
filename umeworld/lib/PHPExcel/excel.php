<?php
namespace umeworld\lib\PHPExcel;

use Yii;
use PHPExcel_IOFactory;
use common\model\Student;
use common\model\Teacher;
use umeworld\lib\Query;
use common\model\School;
use yii\helpers\ArrayHelper;

class Excel extends \yii\base\Object{

	public function __construct(){
		require_once Yii::getAlias('@umeworld/lib/PHPExcel/') . 'PHPExcel.php';
		require_once Yii::getAlias('@umeworld/lib/PHPExcel/') . 'PHPExcel/IOFactory.php';
		require_once Yii::getAlias('@umeworld/lib/PHPExcel/') . 'PHPExcel/Reader/Excel5.php';
		require_once Yii::getAlias('@umeworld/lib/PHPExcel/') . 'PHPExcel/Reader/Excel2007.php';
		require_once Yii::getAlias('@umeworld/lib/PHPExcel/') . 'PHPExcel/Writer/Excel5.php';
		require_once Yii::getAlias('@umeworld/lib/PHPExcel/') . 'PHPExcel/Writer/Excel2007.php';
        parent::__construct();
    }
	
	/**
	 * 读excel表数据放入数组中
	 * @author jay
	 * @param $inputPath excel文件路径
	 * @param $page 页码
	 * @param $pageSize 页个数
	 * @param $sheetIndex 读取excel文件表格下标
	 */
	public function getSheetDataInArray($inputPath = '', $page = 0, $pageSize = 0, $sheetIndex = 0){
		if(!$inputPath){
			return [];
		}
		$aPathInfo = pathinfo($inputPath);
		if($aPathInfo['extension'] =='xlsx'){
			$className = 'excel2007';
		}else{
			$className = 'Excel5';
		}
		$objReader = PHPExcel_IOFactory::createReader($className);
		$objPHPExcel = $objReader->load($inputPath);
		$sheet = $objPHPExcel->getSheet($sheetIndex);
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		$aReturn = [];
		$offset = 0;
		if($page && $pageSize){
			$offset = ($page - 1) * $pageSize;
		}
		$count = 0;
		for($j = 1; $j <= $highestRow; $j++){
			$index = 0;
			$aRow = [];
			for($k = 'A'; $k <= $highestColumn; $k++){
				$aRow[$index++] = mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue(), 'utf8', 'auto');
			}
			if($page && $pageSize){
				if($count >= $offset){
					array_push($aReturn, $aRow);
				}
				if(count($aReturn) == $pageSize){
					break;
				}
				$count = $count + 1;
			}else{
				array_push($aReturn, $aRow);
			}
			$aRow = null;
		}
		$objReader = null;
		$objPHPExcel = null;
		$sheet = null;
		return $aReturn;
	}
	
	/**
	 * 将数组写到excel表中
	 * @author jay
	 * @param $outputPath excel文件路径
	 * @param $sheetIndex 读取excel文件表格下标
	 */
	public function setSheetDataFromArray($outputPath = '', $aData, $isOutPutDirectory = false, $sheetIndex = 0, $startCell = 'A1'){
		if(!$outputPath){
			return false;
		}
		$aPathInfo = pathinfo($outputPath);
		$className = 'Excel5';
		if($aPathInfo['extension'] =='xlsx'){
			$className = 'excel2007';
		}
		$objPHPExcel = PHPExcel_IOFactory::createPHPExcelObject();	
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $className);
		//$objPHPExcel->getSheet($sheetIndex)->fromArray($aData, NULL, $startCell);
		$row = $startCell[1];
		foreach($aData as $value){
			$col = $startCell[0];
			foreach($value as $v){
				$dataType = 's';
				if(is_numeric($v)){
					$dataType = 'n';
				}
				$objPHPExcel->getSheet($sheetIndex)->setCellValueExplicit("$col$row", $v, $dataType);
				$col++;
			}
			$row++;
		}
		$aData = null;
		$objPHPExcel = null;
		if($isOutPutDirectory){
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $outputPath . '"');
			header('Cache-Control: max-age=0');
			$objWriter->save('php://output');
			exit;
		}else{
			$objWriter->save($outputPath);
			$objWriter = null;
			return true;
		}
	}
	
	/**
	 * 将Html table写到excel表中
	 * @author jay
	 * @param $outputPath excel文件路径
	 * @param $sheetIndex 读取excel文件表格下标
	 */
	public function setSheetDataFromHtmlTable($outputPath, $htmlTable, $isOutPutDirectory = false){
		if(!$outputPath){
			return false;
		}
		$aPathInfo = pathinfo($outputPath);
		$excelWriterType = 'Excel5';
		if($aPathInfo['extension'] =='xlsx'){
			$excelWriterType = 'Excel2007';
		}
		$oHtmlPHPExcelObject = PHPExcel_IOFactory::createHtmlPHPExcelObject();
		$oHtmlPHPExcelObject->setExcelObject(PHPExcel_IOFactory::createPHPExcelObject());
		$oHtmlPHPExcelObject->setHtmlStringOrFile($htmlTable);
		if($isOutPutDirectory){
			$oHtmlPHPExcelObject->process()->output($outputPath, $excelWriterType);
		}else{
			$oHtmlPHPExcelObject->process()->save($outputPath, $excelWriterType);
			return true;
		}
	}
	
	/**
	 * 将数组写到excel表中
	 * @author jay
	 * @param $outputPath excel文件路径
	 * @param $sheetIndex 读取excel文件表格下标
	 */
	public function setSheetDataFromArrayCsv($outputPath = '', $aData, $isOutPutDirectory = false, $sheetIndex = 0, $startCell = 'A1'){
		if(!$outputPath){
			return false;
		}
		
		$objPHPExcel = PHPExcel_IOFactory::createPHPExcelObject();	
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
		$objWriter->setUseBOM(true);
		$row = $startCell[1];
		foreach($aData as $value){
			$col = $startCell[0];
			foreach($value as $v){
				$objPHPExcel->getSheet($sheetIndex)->setCellValueExplicit("$col$row", $v, 's');
				$col++;
			}
			$row++;
		}
		$aData = null;
		$objPHPExcel = null;
		if($isOutPutDirectory){
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $outputPath . '"');
			header('Cache-Control: max-age=0');
			$objWriter->save('php://output');
			exit;
		}else{
			$objWriter->save($outputPath);
			$objWriter = null;
			return true;
		}
	}
	
	/**
	 * 读excel表数据放入数组中
	 * @author jay
	 * @param $inputPath excel文件路径
	 * @param $page 页码
	 * @param $pageSize 页个数
	 * @param $sheetIndex 读取excel文件表格下标
	 */
	public function getSheetDataInArrayCsv($inputPath = '', $page = 0, $pageSize = 0, $sheetIndex = 0){
		if(!$inputPath){
			return [];
		}
		$objReader = PHPExcel_IOFactory::createReader('CSV');
		$objPHPExcel = $objReader->load($inputPath);
		$sheet = $objPHPExcel->getSheet($sheetIndex);
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		$aReturn = [];
		$offset = 0;
		if($page && $pageSize){
			$offset = ($page - 1) * $pageSize;
		}
		$count = 0;
		for($j = 1; $j <= $highestRow; $j++){
			$index = 0;
			$aRow = [];
			for($k = 'A'; $k <= $highestColumn; $k++){
				$aRow[$index++] = mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue(), 'utf8', 'auto');
			}
			if($page && $pageSize){
				if($count >= $offset){
					array_push($aReturn, $aRow);
				}
				if(count($aReturn) == $pageSize){
					break;
				}
				$count = $count + 1;
			}else{
				array_push($aReturn, $aRow);
			}
			$aRow = null;
		}
		$objReader = null;
		$objPHPExcel = null;
		$sheet = null;
		return $aReturn;
	}
	
	/**
	 * 将Html table写到excel表中
	 * @author jay
	 * @param $outputPath excel文件路径
	 * @param $sheetIndex 读取excel文件表格下标
	 */
	public function htmlTableToExcel($outputPath, $htmlTable){
		if(!$outputPath){
			return false;
		}
		$html = '<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name></x:Name><x:WorksheetOptions><x:Selected/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>';
		$html .= $htmlTable;
		$html .= '</html>';
		//$this->setSheetDataFromHtmlTable($outputPath, $html, true);exit;
		header("Content-type: application/vnd.ms-excel; charset=utf8");
		header('Content-Disposition: attachment;filename="' . $outputPath . '"');
		echo $html . "\t";
		exit;
	}
}
?>