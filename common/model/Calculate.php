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
	 *	$choushuiShuanfa	抽水算法：1四舍五入2余数抹零
	 *	$returnInt	是否取整返回
	 */
	public static function paijuPlayerJiesuanValue($zhanji = 0, $yingChou = 0, $shuFan = 0, $qibuChoushui = 0, $choushuiShuanfa = 0, $returnInt = true){
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
			if(-$zhanji >= $qibuChoushui){
				$jiesuanValue = $zhanji * (1 - ($shuFan / 100));
			}else{
				$jiesuanValue = $zhanji;
			}
		}
		if(!$returnInt){
			return $jiesuanValue;
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
	 *	$returnInt	是否取整返回
	 */
	public static function calculateLianmengButie($zhanji = 0, $baoxianHeji = 0, $duizhangfangfa = 0, $choushuiShuanfa = 0, $returnInt = true){
		$lianmengButie = ($zhanji + (-$baoxianHeji)) * (1 - Lianmeng::getDuizhangfangfaValue($duizhangfangfa));
		if(!$returnInt){
			return $lianmengButie;
		}
		return static::_jinyi($lianmengButie);
		//return ceil($lianmengButie);
		/*if($choushuiShuanfa){
			return static::getIntValueByChoushuiShuanfa($lianmengButie, $choushuiShuanfa);
		}else{
			return static::getIntValueByChoushuiShuanfa($lianmengButie);
		}*/
	}
	
	/**
	 *	有小数进一，负向后进一
	 */
	public static function _jinyi($number){
		$number = (string)$number;
		if($number < 0){
			if($number - (int)$number < 0){
				return (int)($number - 1);
			}else{
				return (int)$number;
			}
		}else{
			if($number - (int)$number > 0){
				return (int)($number + 1);
			}else{
				return (int)$number;
			}
		}
	}
	
	/**
	 *	计算牌局记录实际抽水 	公式：实际抽水=抽水数-联盟补贴-桌子费
	 *	$choushuiValue	抽水数
	 *	$lianmengButie	联盟补贴
	 *	$paijuFee		桌子费
	 *	$choushuiShuanfa	抽水算法：1四舍五入2余数抹零
	 *	$returnInt	是否取整返回
	 */
	public static function calculateShijiChouShuiValue($choushuiValue, $lianmengButie, $paijuFee, $choushuiShuanfa = 0, $returnInt = true){
		$shijiChouShuiValue = $choushuiValue - $lianmengButie - $paijuFee;
		if(!$returnInt){
			return $shijiChouShuiValue;
		}
		return (int)$shijiChouShuiValue;
	}
	
	/**
	 *	计算牌局记录保险被抽 	公式：保险被抽=抽成系数*保险总和
	 *	$baoxianHeji		保险合计
	 *	$baoxianChoucheng	保险抽成
	 *	$choushuiShuanfa	抽水算法：1四舍五入2余数抹零
	 *	$returnInt	是否取整返回
	 */
	public static function calculateBaoxianBeichou($baoxianHeji = 0, $baoxianChoucheng = 0, $choushuiShuanfa = 0, $returnInt = true){
		/*if($baoxianHeji <= 0){
			return 0;
		}*/
		$baoxianBeichou = -$baoxianHeji * ($baoxianChoucheng / 100);
		if(!$returnInt){
			return $baoxianBeichou;
		}
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
	 *	$choushuiShuanfa	抽水算法：1四舍五入2余数抹零
	 *	$returnInt	是否取整返回
	 */
	public static function calculateShijiBaoXian($baoxianHeji = 0, $baoxianBeichou = 0, $choushuiShuanfa = 0, $returnInt = true){
		$shijiBaoxian = -$baoxianHeji - $baoxianBeichou;
		if(!$returnInt){
			return $shijiBaoxian;
		}
		if($choushuiShuanfa){
			return static::getIntValueByChoushuiShuanfa($shijiBaoxian, $choushuiShuanfa);
		}else{
			return static::getIntValueByChoushuiShuanfa($shijiBaoxian);
		}
	}
	
	/**
	 *	计算牌局账单 	公式：（（战绩+保险）*（对账系数））-桌子费-保险被抽
	 *	$zhanji				战绩
	 *	$baoxianHeji		保险合计
	 *	$paijuFee			桌子费
	 *	$baoxianBeichou		保险被抽
	 *	$duizhangfangfa	对账方法（1：0.975 2：无水账单）
	 *	$choushuiShuanfa	抽水算法：1四舍五入2余数抹零
	 *	$returnInt	是否取整返回
	 */
	public static function calculateZhangDan($zhanji = 0, $baoxianHeji = 0, $paijuFee = 0, $baoxianBeichou = 0, $duizhangfangfa = 0, $choushuiShuanfa = 0, $returnInt = true){
		$zhangDan = (($zhanji + (-$baoxianHeji)) * Lianmeng::getDuizhangfangfaValue($duizhangfangfa)) - $paijuFee - $baoxianBeichou;
		if(!$returnInt){
			return $zhangDan;
		}
		if($choushuiShuanfa){
			return static::getIntValueByChoushuiShuanfa($zhangDan, $choushuiShuanfa);
		}else{
			return static::getIntValueByChoushuiShuanfa($zhangDan);
		}
	}
	
	/**
	 *	计算差额 	公式：（（所有资金-（所有客人本金+总抽水+总保险-所有支出））+所有联盟总帐
	 *	$totalMoneyTypeMoney		所有资金
	 *	$totalOutPutTypeMoney		所有支出
	 *	$totalKerenBenjin			所有客人本金
	 *	$totalChouShui				总抽水
	 *	$totalBaoXian				总保险
	 *	$totalLianmengZhongZhang	所有联盟总帐
	 *	$choushuiShuanfa	抽水算法：1四舍五入2余数抹零
	 *	$returnInt	是否取整返回
	 */
	public static function calculateImbalanceMoney($totalMoneyTypeMoney = 0, $totalOutPutTypeMoney = 0, $totalKerenBenjin = 0, $totalChouShui = 0, $totalBaoXian = 0, $totalLianmengZhongZhang = 0, $choushuiShuanfa = 0, $returnInt = true){
		$imbalanceMoney = ($totalMoneyTypeMoney - ($totalKerenBenjin + $totalChouShui + $totalBaoXian - $totalOutPutTypeMoney)) + $totalLianmengZhongZhang;
		if(!$returnInt){
			return $imbalanceMoney;
		}
		if($choushuiShuanfa){
			return static::getIntValueByChoushuiShuanfa($imbalanceMoney, $choushuiShuanfa);
		}else{
			return static::getIntValueByChoushuiShuanfa($imbalanceMoney);
		}
	}
	
	/**
	 *	计算交班转出 	公式：（总抽水+总保险）- 总支出
	 *	$totalOutPutTypeMoney		所有支出
	 *	$totalChouShui				总抽水
	 *	$totalBaoXian				总保险
	 *	$choushuiShuanfa	抽水算法：1四舍五入2余数抹零
	 *	$returnInt	是否取整返回
	 */
	public static function calculateJiaoBanZhuanChuMoney($totalOutPutTypeMoney = 0, $totalChouShui = 0, $totalBaoXian = 0, $choushuiShuanfa = 0, $returnInt = true){
		$jiaoban = ($totalChouShui + $totalBaoXian) - $totalOutPutTypeMoney;
		if(!$returnInt){
			return $jiaoban;
		}
		if($choushuiShuanfa){
			return static::getIntValueByChoushuiShuanfa($jiaoban, $choushuiShuanfa);
		}else{
			return static::getIntValueByChoushuiShuanfa($jiaoban);
		}
	}
	
	/**
	 *	计算代理分成 	abs(战绩*赢返) 或 abs(战绩*输返)
	 *	$zhanji				战绩
	 *	$yinFan				赢返
	 *	$shuFan				输返
	 *	$choushuiShuanfa	抽水算法：1四舍五入2余数抹零
	 *	$returnInt	是否取整返回
	 */
	public static function calculateFenchengMoney($zhanji = 0, $yinFan = 0, $shuFan = 0, $choushuiShuanfa = 0, $returnInt = true){
		$fencheng = 0;
		if(!$zhanji){
			return 0;
		}
		$zhanji = abs($zhanji);
		if($zhanji > 0){
			$fencheng = $zhanji * ($yinFan / 100);
		}else{
			$fencheng = $zhanji * ($shuFan / 100);
		}
		if(!$returnInt){
			return $fencheng;
		}
		if($choushuiShuanfa){
			return static::getIntValueByChoushuiShuanfa($fencheng, $choushuiShuanfa);
		}else{
			return static::getIntValueByChoushuiShuanfa($fencheng);
		}
	}
	
}