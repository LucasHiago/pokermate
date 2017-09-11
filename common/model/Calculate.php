<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;

class Calculate extends \yii\base\Object{
	
	/**
	 *	计算某牌局游戏玩家结算值（赢为正数，输为负数）	公式：战绩*(1-抽水系数)
	 *	$zhanji	战绩
	 *	$yingChou	赢抽点数
	 *	$shuFan	输返点数
	 *	$qibuChoushui	起步抽水
	 *	$$choushuiShuanfa	抽水算法：1四舍五入2余数抹零
	 */
	public static function paijuPlayerJiesuanValue($zhanji = 0, $yingChou = 0, $shuFan = 0, $qibuChoushui = 0, $choushuiShuanfa = 0){
		$jiesuanValue = 0;
		if(!$zhanji){
			return 0;
		}
		if($zhanji > 0){
			if($zhanji >= $qibuChoushui){
				$jiesuanValue = $zhanji * (1 - ($yingChou / 100));
			}else{
				$jiesuanValue = $zhanji;
			}
		}else{
			$jiesuanValue = $zhanji * (1 - ($shuFan / 100));
		}
		if($choushuiShuanfa){
			return static::getIntValueByChoushuiShuanfa($jiesuanValue, $choushuiShuanfa);
		}else{
			return static::getIntValueByChoushuiShuanfa($jiesuanValue);
		}
	}
	
	/**
	 *	抽水算法取整
	 *	$value	结算值
	 *	$choushuiShuanfa	抽水算法：1四舍五入2余数抹零
	 */
	public static function getIntValueByChoushuiShuanfa($value = 0, $choushuiShuanfa = User::CHOUSHUI_SHUANFA_YUSHUMOLIN){
		if($choushuiShuanfa == User::CHOUSHUI_SHUANFA_SISHIWURU){
			return round($value);
		}elseif($choushuiShuanfa == User::CHOUSHUI_SHUANFA_YUSHUMOLIN){
			return (int)$value;
		}else{
			return (int)$value;
		}
	}
	
	/**
	 *	计算牌局记录抽水联盟补贴 	公式：（战绩+保险）*(1-对账系数)
	 *	$zhanji			战绩
	 *	$baoxianHeji	保险合计
	 *	$duizhangfangfa	对账方法（1：0.975 2：无水账单）
	 *	$choushuiShuanfa	抽水算法：1四舍五入2余数抹零
	 */
	public static function calculateLianmengButie($zhanji = 0, $baoxianHeji = 0, $duizhangfangfa = 0, $choushuiShuanfa = 0){
		$lianmengButie = ($zhanji + $baoxianHeji) * (1 - Lianmeng::getDuizhangfangfaValue($duizhangfangfa));
		
		if($choushuiShuanfa){
			return static::getIntValueByChoushuiShuanfa($lianmengButie, $choushuiShuanfa);
		}else{
			return static::getIntValueByChoushuiShuanfa($lianmengButie);
		}
	}
	
	/**
	 *	计算牌局记录实际抽水 	公式：实际抽水=抽水数-联盟补贴-桌子费
	 *	$choushuiValue	抽水数
	 *	$lianmengButie	联盟补贴
	 *	$paijuFee		桌子费
	 */
	public static function calculateShijiChouShuiValue($choushuiValue, $lianmengButie, $paijuFee){
		$shijiChouShuiValue = $choushuiValue - $lianmengButie - $paijuFee;
		
		return (int)$shijiChouShuiValue;
	}
	
	/**
	 *	计算牌局记录保险被抽 	公式：保险被抽=抽成系数*保险总和
	 *	$baoxianHeji		保险合计
	 *	$baoxianChoucheng	保险抽成
	 *	$choushuiShuanfa	抽水算法：1四舍五入2余数抹零
	 */
	public static function calculateBaoxianBeichou($baoxianHeji = 0, $baoxianChoucheng = 0, $choushuiShuanfa = 0){
		if($baoxianHeji <= 0){
			return 0;
		}
		$baoxianBeichou = $baoxianHeji * ($baoxianChoucheng / 100);
		
		if($choushuiShuanfa){
			return static::getIntValueByChoushuiShuanfa($baoxianBeichou, $choushuiShuanfa);
		}else{
			return static::getIntValueByChoushuiShuanfa($baoxianBeichou);
		}
	}
	
	/**
	 *	计算牌局记录实际保险 	公式：实际保险=牌局保险-保险被抽
	 *	$baoxianHeji		保险合计
	 *	$baoxianBeichou		保险被抽
	 */
	public static function calculateShijiBaoXian($baoxianHeji = 0, $baoxianBeichou = 0){
		return (int)$baoxianHeji - $baoxianBeichou;
	}
	
	/**
	 *	计算牌局记账单 	公式：（（战绩+保险）*（对账系数））-桌子费-保险被抽
	 *	$zhanji				战绩
	 *	$baoxianHeji		保险合计
	 *	$paijuFee			桌子费
	 *	$baoxianBeichou		保险被抽
	 *	$duizhangfangfa	对账方法（1：0.975 2：无水账单）
	 *	$choushuiShuanfa	抽水算法：1四舍五入2余数抹零
	 */
	public static function calculateZhangDan($zhanji = 0, $baoxianHeji = 0, $paijuFee = 0, $baoxianBeichou = 0, $duizhangfangfa = 0, $choushuiShuanfa = 0){
		$zhangDan = (($zhanji + $baoxianHeji) * Lianmeng::getDuizhangfangfaValue($duizhangfangfa)) - $paijuFee - $baoxianBeichou;
		
		if($choushuiShuanfa){
			return static::getIntValueByChoushuiShuanfa($zhangDan, $choushuiShuanfa);
		}else{
			return static::getIntValueByChoushuiShuanfa($zhangDan);
		}
	}
	
}