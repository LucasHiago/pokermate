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