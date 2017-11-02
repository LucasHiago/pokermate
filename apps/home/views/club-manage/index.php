<?php
use umeworld\lib\Url;
use home\widgets\Table;
use yii\widgets\LinkPager;
$this->setTitle('俱乐部管理');
?>

<div class="row">
	<a href="<?php echo Url::to('home', 'club-manage/show-edit', ['id' => 0]); ?>" type="button" class="btn btn-primary">新增俱乐部</a>
</div>

<br />

<div class="row">
	<div class="table-responsive">
		<?php
			echo Table::widget([
				'aColumns'	=>	[
					'club_name'	=>	['title' => '俱乐部名称','class' => 'col-sm-2'],
					'club_id'	=>	['title' => '俱乐部ID','class' => 'col-sm-2'],
					'club_login_name'	=>	['title' => '登录账户','class' => 'col-sm-2'],
					//'club_login_password'	=>	['title' => '登录密码','class' => 'col-sm-2'],
					'operate'	=>	[
						'title' => '操作',
						'class' => 'col-sm-2',
						'content' => function($aData){
							$str = '';
							$str .= '<a href="' . Url::to('home', 'club-manage/show-edit', ['id' => $aData['id']]) . '" type="button" class="btn btn-primary">修改</a>&nbsp;&nbsp;';
							$str .= '<a href="javascript:;" type="button" class="btn btn-danger" onclick="setDelete(this, ' . $aData['id'] . ', 1);">删除</a>';
							return $str;
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
						}, 1);
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