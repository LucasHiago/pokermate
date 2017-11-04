<?php
use umeworld\lib\Url;
use home\widgets\Table;
use yii\widgets\LinkPager;
use common\model\User;
$this->setTitle('账号管理');
?>

<div class="row">
	<div class="col-lg-12">
		<form role="form" class="J-search-form form-horizontal" name="J-search-form">
			<div class="J-condition-line">
				<label class="control-label" style="float:left;">账号名</label>
				<div class="col-sm-2" style="width:150px;">
					<input type="text" class="J-login-name form-control" name="loginName" value="<?php echo $loginName ? $loginName : ''; ?>" />
				</div>
				
				<label class="control-label" style="float:left;">用户名</label>
				<div class="col-sm-2" style="width:150px;">
					<input type="text" class="J-user-name form-control" name="userName" value="<?php echo $userName ? $userName : ''; ?>" />
				</div>
				
				<div class="form-group">
					<div class="col-sm-2" style="width:90px;">
						<button type="button" class="J-search btn btn-primary" onclick="search();">搜索</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<br />
<br />
<div class="row">
	<a href="<?php echo Url::to('home', 'user-manage/show-edit', ['id' => 0]); ?>" type="button" class="btn btn-primary">新增账号</a>
</div>

<br />

<div class="row">
	<div class="table-responsive">
		<?php
			echo Table::widget([
				'aColumns'	=>	[
					'login_name'	=>	['title' => '账号名','class' => 'col-sm-2'],
					'name'	=>	[
						'title' => '姓名',
						'class' => 'col-sm-2',
						'content' => function($aData){
							return $aData['name'];
						}
					],
					'vip_level'	=>	[
						'title' => 'vip等级',
						'class' => 'col-sm-1',
						'content' => function($aData){
							if($aData['vip_level']){
								$aVipList = User::getVipList();
								return $aVipList[$aData['vip_level']];
							}
							return '普通账号';
						}
					],
					'vip'	=>	[
						'title' => 'vip到期时间',
						'class' => 'col-sm-1',
						'content' => function($aData){
							if($aData['vip_expire_time'] < NOW_TIME){
								return '已过期';
							}
							return date('Y-m-d', $aData['vip_expire_time']);
						}
					],
					'qibu_choushui'	=>	[
						'title' => '起步抽水',
						'class' => 'col-sm-1',
						'content' => function($aData){
							return $aData['qibu_choushui'];
						}
					],
					'choushui_shuanfa'	=>	[
						'title' => '抽水算法',
						'content' => function($aData){
							if($aData['choushui_shuanfa'] == User::CHOUSHUI_SHUANFA_SISHIWURU){
								return '四舍五入';
							}else{
								return '余数抹零';
							}
						}
					],
					'save_code'	=>	[
						'title' => '安全密码',
						'content' => function($aData){
							return $aData['save_code'];
						}
					],
					'operate'	=>	[
						'title' => '操作',
						'class' => 'col-sm-3',
						'content' => function($aData){
							$str = '';
							$str .= '<a href="' . Url::to('home', 'user-manage/show-edit', ['id' => $aData['id']]) . '" type="button" class="btn btn-primary">修改</a>&nbsp;&nbsp;';
							$str .= '<a href="javascript:;" type="button" class="J-clear-save-code-limit btn btn-primary" data-id="' . $aData['id'] . '">清除安全密码输入限制</a>&nbsp;&nbsp;';
							$str .= '<a href="javascript:;" type="button" class="btn btn-danger" onclick="setDelete(this, ' . $aData['id'] . ', 1, \'' . $aData['name'] . '\');">删除</a>';
							return $str;
						}
					],
				],
				'aDataList'	=>	$aUserList,
			]);
			echo LinkPager::widget(['pagination' => $oPage]);
		?>
	</div>
</div>
<script type="text/javascript">
	
	function search(){
		var condition = $('form[name=J-search-form]').serialize();
		location.href = '<?php echo Url::to('home', 'user-manage/index'); ?>?' + condition;
	}
	
	function setDelete(o, id, status, name){
		var tips = '删除账号提示：' + name;
		if(status == 0){
			tips = '确定启用？';
		}
		UBox.confirm(tips, function(){
			ajax({
				url : '<?php echo Url::to('home', 'user-manage/set-forbidden-user'); ?>',
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
		$('.J-clear-save-code-limit').click(function(){
			var o = this;
			ajax({
				url : '<?php echo Url::to('home', 'user-manage/clear-save-code-limit'); ?>',
				data : {id : $(o).attr('data-id')},
				beforeSend : function(){
					$(o).attr('disabled', 'disabled');
				},
				complete : function(){
					$(o).attr('disabled', false);
				},
				success : function(aResult){
					if(aResult.status == 1){
						UBox.show(aResult.msg, aResult.status);
					}else{
						UBox.show(aResult.msg, aResult.status);
					}
				}
			});
		});
	});
</script>