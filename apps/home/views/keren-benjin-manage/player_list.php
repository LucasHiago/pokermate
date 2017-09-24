<?php
use umeworld\lib\Url;
use home\widgets\Table;
use yii\widgets\LinkPager;
$this->setTitle('会员管理');
?>

<div class="row">
	<a href="<?php echo Url::to('home', 'keren-benjin-manage/show-edit', ['id' => 0]); ?>" type="button" class="btn btn-primary">新增会员</a>
	<a href="<?php echo Url::to('home', 'keren-benjin-manage/export-list'); ?>" type="button" class="btn btn-primary" onclick="exportExcel(this);">导出列表</a>
</div>

<br />

<div class="row">
	<div class="table-responsive">
		<?php
			echo Table::widget([
				'aColumns'	=>	[
					'keren_bianhao'	=>	['title' => '客人编号'],
					'benjin'	=>	['title' => '本金'],
					'player_name'	=>	[
						'title' => '玩家名字',
						'class' => 'col-sm-2',
						'content' => function($aData){
							return $aData['player_name'];
						}
					],
					'player_id'	=>	['title' => '玩家ID'],
					'ying_chou'	=>	['title' => '赢抽点数'],
					'shu_fan'	=>	['title' => '输返点数'],
					'agent_id'	=>	['title' => '代理人ID'],
					'agent_name'	=>	[
						'title' => '代理人',
						'class' => 'col-sm-1',
						'content' => function($aData){
							return isset($aData['agent_info']['agent_name']) ? $aData['agent_info']['agent_name'] : '';
						}
					],
					'remark'	=>	['title' => '备注'],
					
					'operate'	=>	[
						'title' => '操作',
						'class' => 'col-sm-2',
						'content' => function($aData){
							$str = '';
							$str .= '<a href="' . Url::to('home', 'keren-benjin-manage/show-edit', ['id' => $aData['keren_bianhao']]) . '" type="button" class="btn btn-primary">修改</a>&nbsp;&nbsp;';
							$str .= '<a href="javascript:;" type="button" class="btn btn-danger" onclick="setDeletePlayer(this, ' . $aData['id'] . ', 1);">删除</a>';
							return $str;
						}
					],
				],
				'aDataList'	=>	$aList,
			]);
		?>
	</div>
</div>
<script type="text/javascript">

	function setDeletePlayer(o, id, status){
		var tips = '确定删除？';
		if(status == 0){
			tips = '确定启用？';
		}
		UBox.confirm(tips, function(){
			ajax({
				url : '<?php echo Url::to('home', 'keren-benjin-manage/delete-player'); ?>',
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