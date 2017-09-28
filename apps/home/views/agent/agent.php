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
			<div class="ag-l-r1">
				<div class="r1-select-all"></div>
				<div class="r1-delete" onclick="deleteAgent(this);"></div>
				<div class="r1-add" onclick="AlertWin.showAddAgent();"></div>
			</div>
			<div class="ag-l-list">
			<?php foreach($aAgentList as $aAgent){ ?>
				<div class="ag-l-list-item <?php echo $aCurrentAgent && $aCurrentAgent['id'] == $aAgent['id'] ? 'active' : '' ?>">
					<a href="javascript:;" class="agi-chk <?php echo $aCurrentAgent && $aCurrentAgent['id'] == $aAgent['id'] ? 'active' : '' ?>" data-id="<?php echo $aAgent['id']; ?>"></a>
					<a href="<?php echo Url::to('home', 'agent/index'); ?>?agentId=<?php echo $aAgent['id']; ?>" class="agi-txt"><?php echo $aAgent['agent_name']; ?></a>
				</div>
			<?php } ?>
			</div>
		</div>
		<div class="ag-center">
			<div class="J-ag-c-list ag-c-list">
			<?php foreach($aFenchengListSetting as $aFenchengSetting){ ?>
				<div class="ag-c-list-item" data-id="<?php echo $aFenchengSetting['id']; ?>">
					<a class="tx1"><?php echo $aFenchengSetting['zhuozi_jibie']; ?></a>
					<a class="tx2"><input type="text" value="<?php echo $aFenchengSetting['yingfan']; ?>%" /></a>
					<a class="ebt1"></a>
					<a class="tx3"><input type="text" value="<?php echo $aFenchengSetting['shufan']; ?>%" /></a>
					<a class="ebt2"></a>
				</div>
			<?php } ?>
				<div class="ag-c-bottom">
					<div class="ag-c-bottom-tx1"><input type="text" value="0.00%" /></div>
					<div class="ag-c-bottom-btn1"></div>
					<div class="ag-c-bottom-tx2"><input type="text" value="0.00%" /></div>
					<div class="ag-c-bottom-btn2"></div>
				</div>
			</div>
		</div>
		<div class="ag-right">
			<div class="ag-r-head">
				<a class="zfc-txt"><?php echo $floatTotalFenCheng; ?></a>
				<a class="qz-btn" onclick="cleanAgentFencheng(this);"></a>
				<a style="float: left;position: relative;display: inline-block;width: 150px;height: 30px;top: 60px;left: -126px;"><span style="float:left;display: inline-block;line-height: 30px;width: 70px;color: #ffffff;">分成微调：</span><input type="text" class="J-agent-fencheng-ajust-value" style="margin-top: 4px;float:left;display: inline-block;width: 70px;text-align:center;color: #f4e2a9;background:#1c1924;border-radius: 5px;" value="<?php echo $agentFenchengAjustValue; ?>" /></a>
			</div>
			<div class="ag-r-title">
				<div class="ag-r-select-all"></div>
			</div>
			<div class="ag-r-list">
			<?php foreach($aAgentUnCleanFenChengList as $aAgentUnCleanFenCheng){ ?>
				<div class="ag-r-list-item">
					<a class="agr-li-chk" data-id="<?php echo $aAgentUnCleanFenCheng['id']; ?>"></a>
					<a class="agr-li-name"><?php echo $aAgentUnCleanFenCheng['paiju_name']; ?></a>
					<a class="agr-li-level"><?php echo $aAgentUnCleanFenCheng['mangzhu']; ?></a>
					<a class="agr-li-uanme"><?php echo $aAgentUnCleanFenCheng['player_name']; ?></a>
					<a class="agr-li-score"><?php echo $aAgentUnCleanFenCheng['zhanji']; ?></a>
					<a class="agr-li-fc"><?php echo $aAgentUnCleanFenCheng['fencheng']; ?></a>
				</div>
			<?php } ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">	
	function deleteAgent(o){
		var aAgentId = [];
		$('.ag-l-list-item .agi-chk.active').each(function(){
			aAgentId.push($(this).attr('data-id'));
		});
		if(aAgentId.length == 0){
			UBox.show('请选择要删除的代理', -1);
			return;
		}
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
						$('.ag-l-list-item .agi-chk.active').parent().remove();
					}
					UBox.show(aResult.msg, aResult.status);
				}
			});
		}
	}
	
	function initAgentList(){
		//$('.ag-l-list').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
		$('.ag-left .r1-select-all').on('click', function(){
			if($(this).hasClass('active')){
				$(this).removeClass('active');
				$('.ag-l-list-item .agi-chk').removeClass('active');
				$('.ag-l-list-item').removeClass('active');
			}else{
				$(this).addClass('active');
				$('.ag-l-list-item .agi-chk').addClass('active');
				$('.ag-l-list-item').addClass('active');
			}
		});
		$('.ag-l-list-item .agi-chk').on('click', function(){
			if($(this).hasClass('active')){
				$(this).removeClass('active');
				$('.ag-left .r1-select-all').removeClass('active');
			}else{
				$(this).addClass('active');
			}
		});
	}
	function initAgentSetting(){
		//$('.J-ag-c-list').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
		function delPercent(o){
			var oTxt = $(o).prev().find('input');
			var txt = oTxt.val();
			oTxt.val('');
			oTxt.focus();
			oTxt.val(parseFloat(txt));
		}
		$('.J-ag-c-list .ag-c-list-item .ebt1, .J-ag-c-list .ag-c-list-item .ebt2').click(function(){
			delPercent(this);
		});
		$('.J-ag-c-list .ag-c-list-item .ebt1, .J-ag-c-list .ag-c-list-item .ebt2, .ag-c-bottom-tx1 input, .ag-c-bottom-tx2 input').prev().find('input').click(function(){
			$(this).parent().next().click();
		});
		function ajustValue(o){
			var oTxt = $(o);
			var txt = oTxt.val();
			if(txt == ''){
				txt = '0.00';
			}
			if(txt.indexOf('%') === -1){
				oTxt.val(parseFloat(txt) + '%');
			}else{
				oTxt.val(txt);
			}
		}
		$('.ag-c-bottom-tx1 input, .ag-c-bottom-tx2 input').click(function(){
			var oTxt = $(this);
			var txt = oTxt.val();
			oTxt.val('');
			oTxt.focus();
			oTxt.val(parseFloat(txt));
		});
		$('.ag-c-bottom-tx1 input, .ag-c-bottom-tx2 input').blur(function(){
			ajustValue(this);
		});
		$('.J-ag-c-list .ag-c-list-item .ebt1, .J-ag-c-list .ag-c-list-item .ebt2').prev().find('input').blur(function(){
			ajustValue(this);
		});
		$('.J-ag-c-list .ag-c-list-item').find('input').keyup(function(e){
			var o = this;
			var id = $(this).parent().parent().attr('data-id');
			var yingfan = $(this).parent().parent().find('.tx2 input').val();
			var shufan = $(this).parent().parent().find('.tx3 input').val();
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
		$('.ag-c-bottom-btn1').click(function(){
			oneKeySaveSetting(this, {agentId : <?php echo $aCurrentAgent ? $aCurrentAgent['id'] : 0 ?>, type : 'yingfan', yingfan : parseFloat($(this).prev().find('input').val())});
		});
		$('.ag-c-bottom-btn2').click(function(){
			oneKeySaveSetting(this, {agentId : <?php echo $aCurrentAgent ? $aCurrentAgent['id'] : 0 ?>, type : 'shufan', shufan : parseFloat($(this).prev().find('input').val())});
		});
	}
	
	function initAgentFenCheng(){
		//$('.ag-r-list').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
		$('.ag-right .ag-r-select-all').on('click', function(){
			if($(this).hasClass('active')){
				$(this).removeClass('active');
				$('.ag-r-list-item .agr-li-chk').removeClass('active');
			}else{
				$(this).addClass('active');
				$('.ag-r-list-item .agr-li-chk').addClass('active');
			}
		});
		$('.ag-r-list-item .agr-li-chk').on('click', function(){
			if($(this).hasClass('active')){
				$(this).removeClass('active');
				$('.ag-right .ag-r-select-all').removeClass('active');
			}else{
				$(this).addClass('active');
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
		$('.ag-r-list-item .agr-li-chk.active').each(function(){
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
		$('.c-h-t-menu.m2').addClass('active');
		initAgentList();
		initAgentSetting();
		initAgentFenCheng();
	});
</script>