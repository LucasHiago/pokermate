<?php
use umeworld\lib\Url;
use home\widgets\Table;
use yii\widgets\LinkPager;
$this->setTitle('会员管理');
?>

<div class="row">
	<a style="float:left;" href="<?php echo Url::to('home', 'keren-benjin-manage/show-edit', ['id' => 0]); ?>" type="button" class="btn btn-primary">新增会员</a>
	<a style="float:left;margin-left: 15px;" href="<?php echo Url::to('home', 'keren-benjin-manage/export-list'); ?>" type="button" class="btn btn-primary" onclick="exportExcel(this);">导出列表</a>

	<div class="col-lg-12" style="float:left;width: 513px;margin-left: 15px;">
		<form role="form" class="J-search-form form-horizontal" name="J-search-form">
			<div class="J-condition-line">
				<label class="control-label" style="float:left;">玩家名字</label>
				<div class="col-sm-2" style="width:150px;">
					<input type="text" class="J-player-name form-control" name="playerName" value="<?php echo $playerName ? $playerName : ''; ?>" />
				</div>
				
				<label class="control-label" style="float:left;">玩家ID</label>
				<div class="col-sm-2" style="width:150px;">
					<input type="text" class="J-player-id form-control" name="playerId" value="<?php echo $playerId ? $playerId : ''; ?>" />
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

<div class="row">
	<div class="table-responsive">
		<?php
			echo Table::widget([
				'aColumns'	=>	[
					'keren_bianhao'	=>	['title' => '客人编号'],
					'benjin'	=>	['title' => '本金'],
					'player_name'	=>	[
						'title' => '玩家名字',
						'class' => 'col-sm-1',
						'content' => function($aData){
							return $aData['player_name'];
						}
					],
					'player_id'	=>	['title' => '玩家ID'],
					'ying_chou'	=>	['title' => '赢抽点数'],
					'shu_fan'	=>	['title' => '输返点数'],
					'ying_fee'	=>	['title' => '赢收台费'],
					'shu_fee'	=>	['title' => '输返台费'],
					'agent_id'	=>	['title' => '代理人ID', 'class' => 'col-sm-1'],
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
						'class' => 'col-sm-3',
						'content' => function($aData){
							$str = '';
							//$str .= '<a href="' . Url::to('home', 'keren-benjin-manage/show-edit', ['id' => $aData['keren_bianhao']]) . '" type="button" class="btn btn-primary">修改</a>&nbsp;&nbsp;';
							$str .= '<a href="javascript:;" type="button" class="btn btn-primary" onclick="showEditPlayer(this, ' . $aData['id'] . ');">修改</a>&nbsp;&nbsp;';
							$str .= '<a href="javascript:;" type="button" class="btn btn-danger" onclick="setDeletePlayer(this, ' . $aData['id'] . ', 1);">删除</a>&nbsp;&nbsp;';
							$str .= '<a href="' . Url::to('home', 'keren-benjin-manage/export-player-last-paiju-data') . '?id=' . $aData['id'] . '" type="button" class="btn btn-primary">导出记录</a>';
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
	var aAgentList = <?php echo json_encode($aAgentList); ?>;
	
	function search(){
		var condition = $('form[name=J-search-form]').serialize();
		location.href = '<?php echo Url::to('home', 'keren-benjin-manage/player-list'); ?>?' + condition;
	}
	
	function setDeletePlayer(o, id, status){
		var tips = '删除用户将清空该用户在代理中的结算记录，是否确认删除？';
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
						}, 1);
					}else{
						UBox.show(aResult.msg, aResult.status);
					}
				}
			});
		});
	}
	
	function showEditPlayer(o, playerId){
		var aPlayerKeyMap = ['keren_bianhao', 'benjin', 'player_name', 'player_id', 'ying_chou', 'shu_fan', 'ying_fee', 'shu_fee', 'agent_id', 'agent_name', 'remark'];
		var i = 0;
		$(o).parent().parent().find('td').each(function(){
			var tdTxt = $(this).text();
			if(typeof(aPlayerKeyMap[i]) != 'undefined'){
				if(aPlayerKeyMap[i] == 'agent_id'){
					var agSelHtml = '<select data-type="' + aPlayerKeyMap[i] + '" class="form-control" style="width:100%;" >';
					agSelHtml += '<option value="0">请选择</option>';
					for(var tt in aAgentList){
						agSelHtml += '<option value="' + aAgentList[tt].id + '">' + aAgentList[tt].agent_name + '</option>';
					}
					agSelHtml += '</select>';
					$(this).html(agSelHtml);
					if(tdTxt != 0){
						$(this).find('select').val(tdTxt);
					}
					$(this).find('select').on('change', function(){
						if($(this).val() != 0){
							$(this).parent().parent().find('input[data-type="agent_name"]').val($(this).parent().parent().find("option:selected").text());
						}
					});
				}else if(aPlayerKeyMap[i] == 'agent_name'){
					$(this).html('<input class="form-control" style="width:100%;" type="text" data-type="' + aPlayerKeyMap[i] + '" value="' + tdTxt + '" disabled />');
				}else{
					$(this).html('<input class="form-control" style="width:100%;" type="text"  data-type="' + aPlayerKeyMap[i] + '" value="' + tdTxt + '" />');
				}
			}
			i++;
		});
		$(o).removeClass('btn-primary');
		$(o).addClass('btn-success');
		$(o).text('保存');
		$(o).attr('onclick', 'savePlayer(this, ' + playerId + ');');
	}
	
	function _goSavePlayer(o, aData){
		ajax({
			url : '<?php echo Url::to('home', 'keren-benjin-manage/edit-player'); ?>',
			data : aData,
			beforeSend : function(){
				$(o).attr('disabled', 'disabled');
			},
			complete : function(){
				$(o).attr('disabled', false);
			},
			success : function(aResult){
				if(aResult.status == 2){
					if(confirm(aResult.msg)){
						aData.isMerge = 1;
						_goSavePlayer(o, aData);
					}
					return;
				}
				if(aResult.status == 1){
					UBox.show(aResult.msg, aResult.status, function(){
						location.reload();
					}, 1);
				}else{
					UBox.show(aResult.msg, aResult.status);
				}
			}
		});
	}

	function savePlayer(o, id){
		var aData = {
			id : id,
			kerenBianhao : $(o).parent().parent().find('input[data-type="keren_bianhao"]').val(),
			benjin : $(o).parent().parent().find('input[data-type="benjin"]').val(),
			playerName : $(o).parent().parent().find('input[data-type="player_name"]').val(),
			playerId : $(o).parent().parent().find('input[data-type="player_id"]').val(),
			yingChou : $(o).parent().parent().find('input[data-type="ying_chou"]').val(),
			shuFan : $(o).parent().parent().find('input[data-type="shu_fan"]').val(),
			yingFee : $(o).parent().parent().find('input[data-type="ying_fee"]').val(),
			shuFee : $(o).parent().parent().find('input[data-type="shu_fee"]').val(),
			agentId : $(o).parent().parent().find('select[data-type="agent_id"]').val(),
			remark : $(o).parent().parent().find('input[data-type="remark"]').val()
		};
		
		_goSavePlayer(o, aData);
	}
	
	$(function(){
		//showJumpPage();
	});
</script>