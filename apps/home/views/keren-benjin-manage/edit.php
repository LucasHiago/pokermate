<?php 
use umeworld\lib\Url;
use common\model\User;
$this->setTitle($aKerenBenjin ? '编辑会员' : '新增会员');
?>
<br />
<div class="row">
	<div class="col-lg-12">
		<input class="J-id form-control" type="hidden" value="<?php echo $aKerenBenjin ? $aKerenBenjin['id'] : 0; ?>">
		<div class="form-group">
			<label>编号</label>
			<input style="width:300px;" class="J-keren-bianhao form-control" value="<?php echo $aKerenBenjin ? $aKerenBenjin['keren_bianhao'] : ''; ?>">
		</div>
		<div class="form-group">
			<label>本金</label>
			<input style="width:300px;" class="J-benjin form-control" value="<?php echo $aKerenBenjin ? $aKerenBenjin['benjin'] : ''; ?>">
		</div>
		<?php if($aKerenBenjin){ ?>
		<div class="form-group">
			<label>玩家游戏昵称</label>
			<select class="J-palayer-list form-control" style="width:300px;">
			<?php foreach($aPlayerList as $aPlayer){ ?>
				<option value="<?php echo $aPlayer['id']; ?>"><?php echo $aPlayer['player_name']; ?></option>
			<?php } ?>
			</select>
		</div>
		<?php }else{ ?>
		<div class="form-group">
			<label>玩家游戏昵称</label>
			<input style="width:300px;" class="J-player-name form-control" value="">
		</div>
		<div class="form-group">
			<label>玩家游戏ID</label>
			<input style="width:300px;" class="J-player-id form-control" value="">
		</div>
		<?php } ?>
		<div class="form-group">
			<label>赢抽点数</label>
			<input style="width:300px;" class="J-ying-chou form-control" value="<?php echo $aKerenBenjin ? $aKerenBenjin['ying_chou'] : ''; ?>"><span style="position: relative; top: -26px; left: 310px;">%</span>
		</div>
		<div class="form-group">
			<label>输返点数</label>
			<input style="width:300px;" class="J-shu-fan form-control" value="<?php echo $aKerenBenjin ? $aKerenBenjin['shu_fan'] : ''; ?>"><span style="position: relative; top: -26px; left: 310px;">%</span>
		</div>
		<div class="form-group">
			<label>赢收台费</label>
			<input style="width:300px;" class="J-ying-fee form-control" value="<?php echo $aKerenBenjin ? $aKerenBenjin['ying_fee'] : ''; ?>">
		</div>
		<div class="form-group">
			<label>输返台费</label>
			<input style="width:300px;" class="J-shu-fee form-control" value="<?php echo $aKerenBenjin ? $aKerenBenjin['shu_fee'] : ''; ?>">
		</div>
		<div class="form-group">
			<label>代理人</label>
			<select class="J-agent-id form-control" style="width:300px;">
			<?php foreach($aAgentList as $aAgent){ ?>
				<option value="<?php echo $aAgent['id']; ?>"><?php echo $aAgent['agent_name']; ?></option>
			<?php } ?>
			</select>
		</div>
		<div class="form-group">
			<label>备注</label>
			<input style="width:300px;" class="J-remark form-control" value="<?php echo $aKerenBenjin ? $aKerenBenjin['remark'] : ''; ?>">
		</div>
		<br />
		<div class="form-group">
			<button type="button" class="J-save-btn btn btn-primary" onclick="save(this);">保存</button>
		</div>
	</div>
</div>

<script type="text/javascript">
	function _goSave(o, aData){
		ajax({
			url : '<?php echo Url::to('home', 'keren-benjin-manage/edit'); ?>',
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
						_goSave(o, aData);
					}
					return;
				}
				if(aResult.status == 1){
					UBox.show(aResult.msg, aResult.status, function(){
						location.href = Tools.url('home', 'keren-benjin-manage/index');
					}, 1);
				}else{
					UBox.show(aResult.msg, aResult.status);
				}
			}
		});
	}

	function save(o){
		var aData = {
			id : $('.J-id').val(),
			kerenBianhao : $('.J-keren-bianhao').val(),
			benjin : $('.J-benjin').val(),
			yingChou : $('.J-ying-chou').val(),
			shuFan : $('.J-shu-fan').val(),
			yingFee : $('.J-ying-fee').val(),
			shuFee : $('.J-shu-fee').val(),
			agentId : $('.J-agent-id').val(),
			remark : $('.J-remark').val()
		};
		<?php if(!$aKerenBenjin){ ?>
			aData.playerName = $('.J-player-name').val();
			aData.playerId = $('.J-player-id').val();
		<?php } ?>
		
		_goSave(o, aData);
	}
	
	$(function(){
		
	});
</script>