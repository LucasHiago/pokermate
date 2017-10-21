<?php
use umeworld\lib\Url;
$this->setTitle('代理分成');
$aFenchengListSetting = [];
if($aCurrentAgent){
	$aFenchengListSetting = $aCurrentAgent['fencheng_setting'];
}
?>
<div class="c-body-wrap">
	<div class="ag-bg">
		<div class="ag-left">
			<div class="panel panel-default" style="float:left;width:297px;min-height: 790px;">
				<div class="panel-heading">
					<h3 class="panel-title"><strong>代理列表</strong></h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive" style="padding:0px;">
						<table class="J-agent-list-table table table-hover">
							<tr><th>代理名称</th><th>操作</th></tr>
						<?php foreach($aAgentList as $aAgent){ ?>
							<tr style="<?php echo $aCurrentAgent && $aCurrentAgent['id'] == $aAgent['id'] ? 'background:#f5f5f5;' : '' ?>"><td style="padding-top:0px;padding-bottom:0px;"><a href="<?php echo Url::to('home', 'agent/index'); ?>?agentId=<?php echo $aAgent['id']; ?>" style="display:block;width:100%;height:45px;line-height:45px;"><?php echo $aAgent['agent_name']; ?></a></td><td style="width:62px;"><button class="btn btn-sm btn-danger" onclick="delAgent(this, <?php echo $aAgent['id']; ?>);">删除</button></td></tr>
						<?php } ?>
							<tr><td><input type="text" class="form-control" /></td><td style="width:62px;"><button class="btn btn-sm btn-primary" onclick="addAgent(this);">添加</button></td></tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="ag-center">
			<div class="panel panel-default" style="float:left;width:297px;min-height: 790px;">
				<div class="panel-heading">
					<h3 class="panel-title"><strong>分成设置</strong></h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive" style="padding:0px;">
						<table class="J-fenchengsetting-list-table table table-hover">
							<tr><th>桌子级别</th><th>赢返</th><th>输返</th></tr>
						<?php foreach($aFenchengListSetting as $aFenchengSetting){ ?>
							<tr>
								<td style="vertical-align: inherit;"><?php echo $aFenchengSetting['zhuozi_jibie']; ?></td>
								<td><input type="text" class="J-feng-setting-input J-fcs-i form-control" data-id="<?php echo $aFenchengSetting['id']; ?>" data-type="yingfan" value="<?php echo $aFenchengSetting['yingfan']; ?>" /></td>
								<td><input type="text" class="J-feng-setting-input J-fcs-i form-control" data-id="<?php echo $aFenchengSetting['id']; ?>" data-type="shufan" value="<?php echo $aFenchengSetting['shufan']; ?>" /></td>
							</tr>
						<?php } ?>
							<tr>
								<td style="vertical-align: inherit;text-align:center;"><strong>赢返</strong></td>
								<td style="vertical-align: inherit;"><input type="text" class="J-fcs-i form-control" value="0" /></td>
								<td style="vertical-align: inherit;"><button class="J-yinfan-onekey btn btn-primary">一键设置</button></td>
							</tr>
							<tr>
								<td style="vertical-align: inherit;text-align:center;"><strong>输返</strong></td>
								<td style="vertical-align: inherit;"><input type="text" class="J-fcs-i form-control" value="0" /></td>
								<td style="vertical-align: inherit;"><button class="J-shufan-onekey btn btn-primary">一键设置</button></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="ag-right">
			<div class="panel panel-default" style="float:left;width:631px;min-height: 790px;">
				<div class="panel-heading">
					<h3 class="panel-title"><strong>分成信息</strong></h3>
				</div>
				<div class="panel-body">
					<div class="row h30">
						<div class="form-group" style="position:relative;top:-10px;">
							<label style="float:left;line-height:32px;">分成微调：</label>
							<input type="text" class="J-agent-fencheng-ajust-value form-control" value="<?php echo $agentFenchengAjustValue; ?>" style="float:left;width:100px;">
							<a href="<?php echo Url::to('home', 'agent/export'); ?>?agentId=<?php echo $aCurrentAgent['id']; ?>" style="float:left;margin-left:30px;margin-top:6px;">导出代理数据</a>
							<label style="float:left;line-height:32px;margin-left:50px;">总分成：<font style="color:#ff5722;"><?php echo $totalFenCheng; ?></font></label>
							<button class="btn btn-sm btn-primary" onclick="cleanAgentFencheng(this);" style="float:right;">清账</button>
						</div>
					</div>
					<div class="h10"></div>
					<div class="table-responsive" style="padding:0px;">
						<table class="J-agent-info-table table table-hover">
							<tr><th><input type="checkbox" class="J-ag-r-select-all" style="cursor:pointer;" /></th><th>牌局名</th><th>桌子级别</th><th>玩家名</th><th>战绩</th><th>分成</th></tr>
						<?php foreach($aAgentUnCleanFenChengList as $aAgentUnCleanFenCheng){ ?>
							<tr>
								<td><input type="checkbox" class="J-ag-r-select" data-id="<?php echo $aAgentUnCleanFenCheng['id']; ?>" style="cursor:pointer;" /></td>
								<td><?php echo $aAgentUnCleanFenCheng['paiju_name']; ?></td>
								<td><?php echo $aAgentUnCleanFenCheng['mangzhu']; ?></td>
								<td><?php echo $aAgentUnCleanFenCheng['player_name']; ?></td>
								<td><?php echo $aAgentUnCleanFenCheng['zhanji']; ?></td>
								<td><?php echo $aAgentUnCleanFenCheng['fencheng']; ?></td>
							</tr>
						<?php } ?>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">	
	
	function addAgent(o){
		ajax({
			url : Tools.url('home', 'agent/add'),
			data : {
				agentName : $(o).parent().parent().find('input').val()
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
	}
	
	function delAgent(o, id){
		var aAgentId = [id];
		
		if(confirm('确定删除？')){
			ajax({
				url : Tools.url('home', 'agent/delete'),
				data : {aAgentId : aAgentId},
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
		}
	}
	
	function initAgentSetting(){
		$('.J-fenchengsetting-list-table .J-fcs-i').each(function(){
			var offset = $(this).offset();
			var oHtml = $('<span style="float: right;position: absolute;top: ' + (offset.top + 8) + 'px;left: ' + (offset.left + $(this).width() + 8) + 'px;">%</span>');
			$(this).after(oHtml);
		});
		
		$('.J-feng-setting-input').keyup(function(e){
			var o = this;
			var id = $(o).attr('data-id');
			var yingfan = $(this).parent().parent().find('input[data-type=yingfan]').val();
			var shufan = $(this).parent().parent().find('input[data-type=shufan]').val();
			if(e.keyCode == 13){
				ajax({
					url : Tools.url('home', 'agent/save-setting'),
					data : {
						id : id,
						yingfan : parseFloat(yingfan),
						shufan : parseFloat(shufan)
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
			}
		});
		
		function oneKeySaveSetting(o, aData){
			ajax({
				url : Tools.url('home', 'agent/one-key-save-setting'),
				data : aData,
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
		}
		$('.J-yinfan-onekey').click(function(){
			oneKeySaveSetting(this, {agentId : <?php echo $aCurrentAgent ? $aCurrentAgent['id'] : 0 ?>, type : 'yingfan', yingfan : parseFloat($(this).parent().parent().find('input').val())});
		});
		$('.J-shufan-onekey').click(function(){
			oneKeySaveSetting(this, {agentId : <?php echo $aCurrentAgent ? $aCurrentAgent['id'] : 0 ?>, type : 'shufan', shufan : parseFloat($(this).parent().parent().find('input').val())});
		});
	}
	
	function initAgentFenCheng(){
		function initSelectAll(){
			$('.J-ag-r-select-all').on('click', function(){
				if($(this).is(':checked')){
					$('.J-ag-r-select').each(function(){
						if(!$(this).is(':checked')){
							$(this).click();
						}
					});
				}else{
					$('.J-ag-r-select').each(function(){
						if($(this).is(':checked')){
							$(this).click();
						}
					});
				}
			});
		}
		initSelectAll();
		$('.J-ag-r-select').on('click', function(){
			if(!$(this).is(':checked')){
				$('.J-ag-r-select-all').replaceWith('<input type="checkbox" class="J-ag-r-select-all" style="cursor:pointer;" />');
				initSelectAll();
			}
		});
		$('.J-agent-fencheng-ajust-value').keyup(function(e){
			var o = this;
			if(e.keyCode == 13){
				ajax({
					url : Tools.url('home', 'user/update-user-info'),
					data : {
						type : 'agent_fencheng_ajust_value',
						value : $(o).val()
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
			}
		});
	}
	
	function cleanAgentFencheng(o){
		var aId = [];
		$('.J-ag-r-select:checked').each(function(){
			aId.push($(this).attr('data-id'));
		});
		if(aId.length == 0){
			UBox.show('请选择要清账的记录', -1);
			return;
		}
		if(confirm('确定要清账？')){
			ajax({
				url : Tools.url('home', 'agent/clean'),
				data : {
					agentId : <?php echo $aCurrentAgent ? $aCurrentAgent['id'] : 0 ?>, 
					aId : aId
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
		}
	}
	
	$(function(){
		$('.J-c-h-t-menu-m2').addClass('active');
		initAgentSetting();
		initAgentFenCheng();
	});
</script>