<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class Calculate extends \yii\base\Object{
	
	/**
	 *	计算某牌局游戏玩家结算值（赢为正数，输为负数）
	 *	$zhanji	战线
	 *	$yingChou	赢抽点数
	 *	$shuFan	输返点数
	 *	$qibuChoushui	起步抽水
	 *	$$choushuiShuanfa	抽水算法：1四舍五入2余数抹零
	 */
	public static function paijuPlayerJiesuanValue($zhanji, $yingChou = 0, $shuFan = 0, $qibuChoushui = 0, $choushuiShuanfa = User::CHOUSHUI_SHUANFA_YUSHUMOLIN){
		$jiesuanValue = 0;
		if(!$zhanji){
			return 0;
		}
		if($zhanji >= $qibuChoushui){
			if($zhanji > 0){
				$jiesuanValue = $zhanji * (1 - ($yingChou / 100));
			}else{
				$jiesuanValue = $zhanji * (1 - ($shuFan / 100));
			}
		}else{
			$jiesuanValue = $zhanji;
		}
		return static::getIntValueByChoushuiShuanfa($choushuiShuanfa, $jiesuanValue);
	}
	
	/**
	 *	抽水算法取整
	 *	$value	结算值
	 *	$$choushuiShuanfa	抽水算法：1四舍五入2余数抹零
	 */
	public static function getIntValueByChoushuiShuanfa($choushuiShuanfa, $value){
		if($choushuiShuanfa == User::CHOUSHUI_SHUANFA_SISHIWURU){
			return round($value);
		}elseif($choushuiShuanfa == User::CHOUSHUI_SHUANFA_YUSHUMOLIN){
			return (int)$value;
		}else{
			return (int)$value;
		}
	}
	
}