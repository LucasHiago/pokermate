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