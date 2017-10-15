<?php
use umeworld\lib\Url;
use home\widgets\Table;
use yii\widgets\LinkPager;
$this->setTitle('操作日志');
?>

<br />

<div class="row">
	<div class="table-responsive">
		<?php
			echo Table::widget([
				'aColumns'	=>	[
					'data_json'	=>	[
						'title' => '日志记录',
						'content' => function($aData){
							$log = '';
							if($aData['type'] == 1){
								$log = date('Y.m.d', $aData['create_time']) . '   客人编号：' . $aData['data_json']['aNewRecord']['keren_bianhao'] . ' 本金：' . $aData['data_json']['aOldRecord']['benjin'] . '  【修改后】本金：' . $aData['data_json']['aNewRecord']['benjin'];
							}elseif($aData['type'] == 2){
								$log = date('Y.m.d', $aData['create_time']) . '   客人编号：' . $aData['data_json']['aNewRecord']['keren_bianhao'] . ' 本金：' . $aData['data_json']['aOldRecord']['benjin'] . '   【交收后】本金：' . $aData['data_json']['aNewRecord']['benjin'] . '   交收方式：' . $aData['data_json']['aNewMoneyTypeRecord']['pay_type'];
							}elseif($aData['type'] == 3){
								$log = date('Y.m.d', $aData['create_time']) . '   客人编号：' . $aData['data_json']['aNewRecord']['keren_bianhao'] . ' 赢抽点数：' . $aData['data_json']['aOldRecord']['ying_chou'] . '   【修改后】赢抽点数：' . $aData['data_json']['aNewRecord']['ying_chou'];
							}elseif($aData['type'] == 4){
								$log = date('Y.m.d', $aData['create_time']) . '   客人编号：' . $aData['data_json']['aNewRecord']['keren_bianhao'] . ' 输返点数：' . $aData['data_json']['aOldRecord']['shu_fan'] . '   【修改后】输返点数：' . $aData['data_json']['aNewRecord']['shu_fan'];
							}elseif($aData['type'] == 5){
								$log = date('Y.m.d', $aData['create_time']) . '   客人编号：' . $aData['data_json']['aOldRecord']['keren_bianhao'] . '   【修改后】客人编号：' . $aData['data_json']['aNewRecord']['keren_bianhao'];
							}elseif($aData['type'] == 6){
								$log = date('Y.m.d', $aData['create_time']) . '   客人编号：' . $aData['data_json']['aOldRecord']['keren_bianhao'] . '   本金：' . $aData['data_json']['aOldRecord']['benjin'] . '  与编号：' . $aData['data_json']['aMergeRecord']['keren_bianhao'] . '  本金：' . $aData['data_json']['aMergeRecord']['benjin'] . ' 【合并后】客人编号：' . $aData['data_json']['aNewRecord']['keren_bianhao'] . ', 本金 ' . $aData['data_json']['aNewRecord']['benjin'];
							}elseif($aData['type'] == 7){
								$log = date('Y.m.d', $aData['create_time']) . '   客人编号：' . $aData['data_json']['aKerenBenjin']['keren_bianhao'] . '   【添加玩家】玩家ID：' . $aData['data_json']['aPlayer']['player_id'] . '  玩家名称：' . $aData['data_json']['aPlayer']['player_name'];
							}elseif($aData['type'] == 8){
								$log = date('Y.m.d', $aData['create_time']) . '   【删除客人】客人编号：' . $aData['data_json']['aKerenBenjin']['keren_bianhao'];
							}elseif($aData['type'] == 9){
								$log = date('Y.m.d', $aData['create_time']) . '   【删除玩家】客人编号：' . $aData['data_json']['aPlayer']['keren_bianhao'] . '  玩家ID：' . $aData['data_json']['aPlayer']['player_id'] . '  玩家名称：' . $aData['data_json']['aPlayer']['player_name'];
							}elseif($aData['type'] == 10){
								$log = date('Y.m.d', $aData['create_time']) . '   【添加资金项】资金项：' . $aData['data_json']['aMoneyType']['pay_type'];
							}elseif($aData['type'] == 11){
								$log = date('Y.m.d', $aData['create_time']) . '   【修改资金】资金项：' . $aData['data_json']['aOldRecord']['pay_type'] . '   资金：' . $aData['data_json']['aOldRecord']['money'] . '  修改后资金：' . $aData['data_json']['aNewRecord']['money'];
							}elseif($aData['type'] == 12){
								$log = date('Y.m.d', $aData['create_time']) . '   【删除资金项】资金项：' . $aData['data_json']['aMoneyType']['pay_type'];
							}elseif($aData['type'] == 13){
								$log = date('Y.m.d', $aData['create_time']) . '   【添加支出项】支出项：' . $aData['data_json']['aMoneyOutPutType']['out_put_type'];
							}elseif($aData['type'] == 14){
								$log = date('Y.m.d', $aData['create_time']) . '   【修改支出】支出项：' . $aData['data_json']['aOldRecord']['out_put_type'] . '   支出：' . $aData['data_json']['aOldRecord']['money'] . '  修改后支出：' . $aData['data_json']['aNewRecord']['money'];
							}elseif($aData['type'] == 15){
								$log = date('Y.m.d', $aData['create_time']) . '   【删除支出项】支出项：' . $aData['data_json']['aMoneyOutPutType']['out_put_type'];
							}elseif($aData['type'] == 16){
								$log = date('Y.m.d', $aData['create_time']) . '   【战绩修改】牌局名：' . $aData['data_json']['aNewRecord']['paiju_name'] . '  玩家ID：' . $aData['data_json']['aNewRecord']['player_id'] . '  玩家昵称：' . $aData['data_json']['aNewRecord']['player_name'] . '  俱乐部：' . $aData['data_json']['aNewRecord']['club_name'] . '  战绩：' . $aData['data_json']['aOldRecord']['zhanji'] . '  修改后战绩：' . $aData['data_json']['aNewRecord']['zhanji'];
							}elseif($aData['type'] == 17){
								$log = date('Y.m.d', $aData['create_time']) . '   【保险合计修改】牌局名：' . $aData['data_json']['aNewRecord']['paiju_name'] . '  玩家ID：' . $aData['data_json']['aNewRecord']['player_id'] . '  玩家昵称：' . $aData['data_json']['aNewRecord']['player_name'] . '  俱乐部：' . $aData['data_json']['aNewRecord']['club_name'] . '  保险合计：' . $aData['data_json']['aOldRecord']['baoxian_heji'] . '  修改后保险合计：' . $aData['data_json']['aNewRecord']['baoxian_heji'];
							}elseif($aData['type'] == 18){
								$log = date('Y.m.d', $aData['create_time']) . '   【添加联盟】联盟名称：' . $aData['data_json']['aLianmeng']['name'];
							}elseif($aData['type'] == 19){
								$log = date('Y.m.d', $aData['create_time']) . '   【删除联盟】联盟名称：' . $aData['data_json']['aLianmeng']['name'];
							}elseif($aData['type'] == 20){
								$log = date('Y.m.d', $aData['create_time']) . '   【修改联盟】联盟名称：' . $aData['data_json']['aOldRecord']['name'] . ' 修改后联盟名称：' . $aData['data_json']['aNewRecord']['name'];
							}elseif($aData['type'] == 21){
								$log = date('Y.m.d', $aData['create_time']) . '   【修改联盟欠账】联盟名称：' . $aData['data_json']['aNewRecord']['name'] . ' 联盟欠账：' . $aData['data_json']['aOldRecord']['qianzhang'] . '  修改后联盟欠账：' . $aData['data_json']['aNewRecord']['qianzhang'];
							}elseif($aData['type'] == 22){
								$log = date('Y.m.d', $aData['create_time']) . '   【修改联盟对账方法】联盟名称：' . $aData['data_json']['aNewRecord']['name'] . ' 联盟对账方法：' . ($aData['data_json']['aOldRecord']['duizhangfangfa'] == 1 ? '0.975' : '无水账单') . '  修改后联盟对账方法：' . ($aData['data_json']['aNewRecord']['duizhangfangfa'] == 1 ? '0.975' : '无水账单');
							}elseif($aData['type'] == 23){
								$log = date('Y.m.d', $aData['create_time']) . '   【修改联盟上缴桌费】联盟名称：' . $aData['data_json']['aNewRecord']['name'] . ' 上缴桌费：' . $aData['data_json']['aOldRecord']['paiju_fee'] . '  修改后上缴桌费：' . $aData['data_json']['aNewRecord']['paiju_fee'];
							}elseif($aData['type'] == 24){
								$log = date('Y.m.d', $aData['create_time']) . '   【修改联盟保险抽成】联盟名称：' . $aData['data_json']['aNewRecord']['name'] . ' 保险抽成：' . $aData['data_json']['aOldRecord']['baoxian_choucheng'] . '  修改后保险抽成：' . $aData['data_json']['aNewRecord']['baoxian_choucheng'];
							}elseif($aData['type'] == 25){
								$log = date('Y.m.d', $aData['create_time']) . '   【联盟清账】联盟名称：' . $aData['data_json']['aLianmengZhongZhang']['lianmeng_name'] . ' 联盟总账单：' . $aData['data_json']['aLianmengZhongZhang']['lianmeng_zhong_zhang'] . ' 联盟旧账：' . $aData['data_json']['aLianmengZhongZhang']['lianmeng_qian_zhang'] . ' 新账单累计：' . $aData['data_json']['aLianmengZhongZhang']['lianmeng_zhang_dan'];
							}elseif($aData['type'] == 26){
								$log = date('Y.m.d', $aData['create_time']) . '   【交班转出】总抽水：' . $aData['data_json']['aJiaoBanZhuanChuDetail']['zhongChouShui'] . ' 总保险：' . $aData['data_json']['aJiaoBanZhuanChuDetail']['zhongBaoXian'] . ' 交接金额：' . $aData['data_json']['aJiaoBanZhuanChuDetail']['jiaoBanZhuanChuMoney'] . ' 转出渠道：' . $aData['data_json']['aMoneyType']['pay_type'];
							}elseif($aData['type'] == 27){
								$log = date('Y.m.d', $aData['create_time']) . '   【添加代理】代理名称：' . $aData['data_json']['aAgent']['agent_name'];
							}elseif($aData['type'] == 28){
								$log = date('Y.m.d', $aData['create_time']) . '   【删除代理】代理名称：' . $aData['data_json']['aAgent']['agent_name'];
							}elseif($aData['type'] == 29){
								$log = date('Y.m.d', $aData['create_time']) . '   【代理清账】代理名称：' . $aData['data_json']['aAgent']['agent_name'] . '  总分成：' . $aData['data_json']['totalFenCheng'];
							}elseif($aData['type'] == 30){
								$log = date('Y.m.d', $aData['create_time']) . '   【添加联盟俱乐部】俱乐部名称：' . $aData['data_json']['aLianmengClub']['club_name'];
							}elseif($aData['type'] == 31){
								$log = date('Y.m.d', $aData['create_time']) . '   【删除联盟俱乐部】俱乐部名称：' . $aData['data_json']['aLianmengClub']['club_name'];
							}elseif($aData['type'] == 32){
								$log = date('Y.m.d', $aData['create_time']) . '   【修改联盟俱乐部ID】俱乐部名称：' . $aData['data_json']['aOldRecord']['club_name'] . '  俱乐部ID：' . $aData['data_json']['aOldRecord']['club_id'] . '  修改后俱乐部ID：' . $aData['data_json']['aNewRecord']['club_id'];
							}elseif($aData['type'] == 33){
								$log = date('Y.m.d', $aData['create_time']) . '   【修改联盟俱乐部】俱乐部名称：' . $aData['data_json']['aOldRecord']['club_name'] . '  修改后俱乐部名称：' . $aData['data_json']['aNewRecord']['club_name'];
							}elseif($aData['type'] == 34){
								$log = date('Y.m.d', $aData['create_time']) . '   【修改联盟俱乐部欠账】俱乐部名称：' . $aData['data_json']['aOldRecord']['club_name'] . ' 欠账：' . $aData['data_json']['aOldRecord']['qianzhang'] . '  修改后欠账：' . $aData['data_json']['aNewRecord']['qianzhang'];
							}elseif($aData['type'] == 35){
								$log = date('Y.m.d', $aData['create_time']) . '   【修改联盟俱乐部对账方法】俱乐部名称：' . $aData['data_json']['aOldRecord']['club_name'] . ' 对账方法：' . ($aData['data_json']['aOldRecord']['duizhangfangfa'] == 1 ? '0.975' : '无水账单') . '  修改后对账方法：' . ($aData['data_json']['aNewRecord']['duizhangfangfa'] == 1 ? '0.975' : '无水账单');
							}elseif($aData['type'] == 36){
								$log = date('Y.m.d', $aData['create_time']) . '   【修改联盟俱乐部上缴桌费】俱乐部名称：' . $aData['data_json']['aOldRecord']['club_name'] . ' 上缴桌费：' . $aData['data_json']['aOldRecord']['paiju_fee'] . '  修改后上缴桌费：' . $aData['data_json']['aNewRecord']['paiju_fee'];
							}elseif($aData['type'] == 37){
								$log = date('Y.m.d', $aData['create_time']) . '   【修改联盟俱乐部保险抽成】俱乐部名称：' . $aData['data_json']['aOldRecord']['club_name'] . ' 保险抽成：' . $aData['data_json']['aOldRecord']['baoxian_choucheng'] . '  修改后保险抽成：' . $aData['data_json']['aNewRecord']['baoxian_choucheng'];
							}elseif($aData['type'] == 38){
								$log = date('Y.m.d', $aData['create_time']) . '   【联盟俱乐部清账】联盟名称：' . $aData['data_json']['aLianmeng']['name'] . ' 新帐：' . $aData['data_json']['zhandan'];
							}elseif($aData['type'] == 39){
								$log = date('Y.m.d', $aData['create_time']) . '   【修改玩家ID】玩家名称：' . $aData['data_json']['aOldPlayer']['player_name'] . ' 玩家ID：' . $aData['data_json']['aOldPlayer']['player_id'] . '  修改后玩家ID：' . $aData['data_json']['aNewPlayer']['player_id'];
							}elseif($aData['type'] == 40){
								$log = date('Y.m.d', $aData['create_time']) . '   【修改玩家名称】玩家名称：' . $aData['data_json']['aOldPlayer']['player_name'] . '  修改后玩家名称：' . $aData['data_json']['aNewPlayer']['player_name'];
							}
							return $log;
						}
					],
				],
				'aDataList'	=>	$aList,
			]);
			echo LinkPager::widget(['pagination' => $oPage]);
		?>
	</div>
</div>
<script type="text/javascript">
	
	function search(){
		var condition = $('form[name=J-search-form]').serialize();
		location.href = '<?php echo Url::to('home', 'club-manage/index'); ?>?' + condition;
	}
	
	function setDelete(o, id, status){
		var tips = '确定删除？';
		if(status == 0){
			tips = '确定启用？';
		}
		UBox.confirm(tips, function(){
			ajax({
				url : '<?php echo Url::to('home', 'club-manage/set-delete'); ?>',
				data : {
					id : id,
					status : status
				},
				beforeSend : function(){
					$(o).attr('disabled', 'disabled');
				},
				complete : function(){
					$(o).attr('disabled', false);
				},
				success : function(aResult){
					if(aResult.status == 1){
						UBox.show(aResult.msg, aResult.status, function(){
							location.reload();
						}, 3);
					}else{
						UBox.show(aResult.msg, aResult.status);
					}
				}
			});
		});
	}
	
	$(function(){
		//showJumpPage();
	});
</script>