<?php
use umeworld\lib\Url;
$this->setTitle('代理保险分成');
$aFenchengListSetting = [];
if($aCurrentAgent){
	$aFenchengListSetting = $aCurrentAgent['baoxian_fencheng_setting'];
}
?>
<div class="c-body-wrap">
	<div style="height: 30px; width: 100%;">
		<a href="<?php echo Url::to('home', 'agent/index'); ?>" class="btn btn-sm btn-default" style="display: block; float: left;left:35px; position: relative;">抽水分成</a>
		<a href="<?php echo Url::to('home', 'agent/baoxian'); ?>" class="btn btn-sm btn-default" style="display: block; float: left;left:50px; position: relative;  background:#868686;">保险分成</a>
		<a style="display: block; float: right; width: 200px; height: 30px; line-height: 30px; position: relative; background: #eeeeee; border-radius: 20px; text-align: center; right: 25px; font-weight: bold;">代理保险分成总和：<font class="J-all-agent-total-fencheng">0</font></a>
	</div>
	<div class="ag-bg" style="margin-top: 5px;">
		<div class="ag-left">
			<div class="panel panel-info" style="float:left;width:297px;min-height: 817px;">
				<div class="panel-heading">
					<h3 class="panel-title"><strong>代理列表</strong></h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive" style="padding:0px;">
						<table class="J-agent-list-table table table-hover">
							<tr><th>代理名称</th><th>操作</th></tr>
						<?php foreach($aAgentList as $aAgent){ ?>
							<tr style="<?php echo $aCurrentAgent && $aCurrentAgent['id'] == $aAgent['id'] ? 'background:#f5f5f5;' : '' ?>"><td style="padding-top:0px;padding-bottom:0px;"><a href="<?php echo Url::to('home', 'agent/baoxian'); ?>?agentId=<?php echo $aAgent['id']; ?>" style="display:block;width:100%;height:45px;line-height:45px;"><?php echo $aAgent['agent_name']; ?></a></td><td style="width:62px;"><button class="btn btn-sm btn-danger" onclick="delAgent(this, <?php echo $aAgent['id']; ?>);">删除</button></td></tr>
						<?php } ?>
							<tr><td><input type="text" class="form-control" placeholder="请输入代理名称" /></td><td style="width:62px;"><button class="btn btn-sm btn-primary" onclick="addAgent(this);">添加</button></td></tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="ag-center">
			<div class="panel panel-info" style="float:left;width:297px;min-height: 817px;">
				<div class="panel-heading">
					<h3 class="panel-title"><strong>保险分成设置</strong></h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive" style="padding:0px;">
						<table class="J-fenchengsetting-list-table table table-hover">
							<tr><th>桌子级别</th><th>赢返</th><th>输返</th></tr>
						<?php foreach($aFenchengListSetting as $aFenchengSetting){ ?>
							<tr>
								<td style="vertical-align: inherit;"><?php echo $aFenchengSetting['zhuozi_jibie']; ?></td>
								<td><div style="float:left;height:32px;"><input type="text" class="J-feng-setting-input J-fcs-i form-control" data-id="<?php echo $aFenchengSetting['id']; ?>" data-type="yingfan" value="<?php echo $aFenchengSetting['yingfan']; ?>" /><span style="float: right;position: relative;top: -26px;right: 6px;">%</span></div></td>
								<td><div style="float:left;height:32px;"><input type="text" class="J-feng-setting-input J-fcs-i form-control" data-id="<?php echo $aFenchengSetting['id']; ?>" data-type="shufan" value="<?php echo $aFenchengSetting['shufan']; ?>" /><span style="float: right;position: relative;top: -26px;right: 6px;">%</span></div></td>
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
			<div class="panel panel-info" style="float:left;width:631px;min-height: 817px;">
				<div class="panel-heading">
					<h3 class="panel-title"><strong>保险分成信息</strong></h3>
				</div>
				<div class="panel-body">
					<div class="h10"></div>
					<div class="row h30">
						<div class="form-group" style="position:relative;top:-10px;">
							<label style="float:left;line-height:32px;">分成微调：</label>
							<input type="text" class="J-agent-fencheng-ajust-value form-control" value="<?php echo $agentBaoxianFenchengAjustValue; ?>" style="float:left;width:90px;">
							<label style="float:left;line-height:32px;margin-left:20px;">总分成：<font style="color:#ff5722;"><?php echo $totalFenCheng; ?></font></label>
							<label style="float:left;line-height:32px;margin-left:20px;">牌局总数：<font style="color:#ff5722;"><?php echo count($aAgentUnCleanFenChengList); ?></font></label>
							<button class="btn btn-sm btn-primary" onclick="cleanAgentFencheng(this);" style="float:right;margin-left: 10px;">清账</button>
							<?php if($aCurrentAgent){ ?>
							<a class="btn btn-sm btn-primary" href="<?php echo Url::to('home', 'agent/export-baoxian'); ?>?agentId=<?php echo $aCurrentAgent['id']; ?>" style="float:right;">导出代理数据</a>
							<?php } ?>
						</div>
					</div>
					<div class="h10"></div>
					<div class="table-responsive" style="padding:0px;">
						<table class="J-agent-info-table table table-hover">
							<tr><th><input type="checkbox" class="J-ag-r-select-all" style="cursor:pointer;" /></th><th>牌局名</th><th>桌子级别</th><th>玩家名</th><th>保险</th><th>分成</th></tr>
						<?php foreach($aAgentUnCleanFenChengList as $aAgentUnCleanFenCheng){ ?>
							<tr>
								<td><input type="checkbox" class="J-ag-r-select" data-id="<?php echo $aAgentUnCleanFenCheng['id']; ?>" style="cursor:pointer;" /></td>
								<td><?php echo $aAgentUnCleanFenCheng['paiju_name']; ?></td>
								<td><?php echo $aAgentUnCleanFenCheng['mangzhu']; ?></td>
								<td><?php echo $aAgentUnCleanFenCheng['player_name']; ?></td>
								<td><?php echo $aAgentUnCleanFenCheng['baoxian_heji']; ?></td>
								<td class="J-ag-paiju-fc"><?php echo $aAgentUnCleanFenCheng['fencheng']; ?></td>
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
					}, 1);
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
						}, 1);
					}else{
						UBox.show(aResult.msg, aResult.status);
					}
				}
			});
		}
	}
	
	function initAgentSetting(){
		$('.J-feng-setting-input').keyup(function(e){
			var o = this;
			var id = $(o).attr('data-id');
			var yingfan = $(this).parent().parent().parent().find('input[data-type=yingfan]').val();
			var shufan = $(this).parent().parent().parent().find('input[data-type=shufan]').val();
			if(e.keyCode == 13){
				ajax({
					url : Tools.url('home', 'agent/save-baoxian-setting'),
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
							}, 1);
						}else{
							UBox.show(aResult.msg, aResult.status);
						}
					}
				});
			}
		});
		
		function oneKeySaveSetting(o, aData){
			ajax({
				url : Tools.url('home', 'agent/one-key-save-baoxian-setting'),
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
						}, 1);
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
						type : 'agent_baoxian_fencheng_ajust_value',
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
							}, 1);
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
		var selectTotalFencheng = 0;
		$('.J-ag-r-select:checked').each(function(){
			aId.push($(this).attr('data-id'));
			selectTotalFencheng += parseInt($(this).parent().parent().find('.J-ag-paiju-fc').text());
		});
		if(aId.length == 0){
			UBox.show('请选择要清账的记录', -1);
			return;
		}
		AlertWin.showAgentClean(<?php echo $aCurrentAgent ? $aCurrentAgent['id'] : 0 ?>, '<?php echo $aCurrentAgent ? $aCurrentAgent['agent_name'] : '' ?>', aId, selectTotalFencheng, <?php echo json_encode($aMoneyTypeList); ?>, true);
		return;
		if(confirm('确定要清账？')){
			ajax({
				url : Tools.url('home', 'agent/clean-baoxian'),
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
						}, 1);
					}else{
						UBox.show(aResult.msg, aResult.status);
					}
				}
			});
		}
	}
	
	function getAllAgentTotalFencheng(){
		ajax({
			url : Tools.url('home', 'agent/get-all-agent-total-baoxian-fencheng'),
			data : {},
			success : function(aResult){
				if(aResult.status == 1){
					$('.J-all-agent-total-fencheng').text(aResult.data);
				}
			}
		});
	}
	
	$(function(){
		$('.J-c-h-t-menu-m2').addClass('active');
		initAgentSetting();
		initAgentFenCheng();
		getAllAgentTotalFencheng();
	});
</script>