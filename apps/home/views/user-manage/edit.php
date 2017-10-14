<?php 
use umeworld\lib\Url;
use common\model\User;
$this->setTitle($aUser ? '编辑账号' : '新增账号');
$this->registerJsFile('@r.js.wdate-picker');
?>
<br />
<div class="row">
	<div class="col-lg-12">
		<input class="J-id form-control" type="hidden" value="<?php echo $aUser ? $aUser['id'] : 0; ?>">
		<div class="form-group">
			<label>账号类型</label>
			<select class="J-type form-control" style="width:300px;">
				<option value="<?php echo User::TYPE_NORMAL; ?>">会员账号</option>
				<option value="<?php echo User::TYPE_MANAGE; ?>">超级管理员</option>
			</select>
		</div>
		<div class="form-group">
			<label>姓名</label>
			<input style="width:300px;" class="J-name form-control" value="<?php echo $aUser ? $aUser['name'] : ''; ?>">
		</div>
		<div class="form-group">
			<label>账号名</label>
			<input style="width:300px;" class="J-login-name form-control" <?php echo $aUser ? 'disabled' : ''; ?> value="<?php echo $aUser ? $aUser['login_name'] : ''; ?>">
		</div>
		<div class="form-group">
			<label>密码</label>
			<input type="password" style="width:300px;" class="J-password form-control" value="">
		</div>
		<div class="form-group">
			<label>确认密码</label>
			<input type="password" style="width:300px;" class="J-en-password form-control" value="">
		</div>
		<div class="form-group">
			<label>VIP等级</label>
			<select class="J-vip-level form-control" style="width:300px;">
				<option value="0">普通账号</option>
				<option value="1">VIP1</option>
				<option value="2">VIP2</option>
				<option value="3">VIP3</option>
				<option value="4">VIP4</option>
				<option value="5">VIP5</option>
				<option value="6">VIP6</option>
				<option value="7">VIP7</option>
			</select>
		</div>
		<div class="form-group">
			<label>VIP时间(单位：天)</label>
			<input style="width:300px;" class="J-vip-expire-time form-control" value="<?php echo $aUser ? ($aUser['vip_day']) : ''; ?>">
		</div>
		<div class="form-group">
			<label>起步抽水</label>
			<input style="width:300px;" class="J-qibu-choushui form-control" value="<?php echo $aUser ? $aUser['qibu_choushui'] : ''; ?>">
		</div>
		<div class="form-group">
			<label>	抽水算法</label>
			<select class="J-choushui-shuanfa form-control" style="width:300px;">
				<option value="<?php echo User::CHOUSHUI_SHUANFA_YUSHUMOLIN; ?>">余数抹零</option>
				<option value="<?php echo User::CHOUSHUI_SHUANFA_SISHIWURU; ?>">四舍五入</option>
			</select>
		</div>
		<br />
		<div class="form-group">
			<button type="button" class="J-save-btn btn btn-primary" onclick="save(this);">保存</button>
		</div>
	</div>
</div>

<script type="text/javascript">
	function save(o){
		var aData = {
			id : $('.J-id').val(),
			type : $('.J-type').val(),
			name : $('.J-name').val(),
			loginName : $('.J-login-name').val(),
			password : $('.J-password').val(),
			enPassword : $('.J-en-password').val(),
			vipLevel : $('.J-vip-level').val(),
			vipExpireTime : $('.J-vip-expire-time').val(),
			qibuChoushui : $('.J-qibu-choushui').val(),
			choushuiShuanfa : $('.J-choushui-shuanfa').val()
		};
		
		ajax({
			url : '<?php echo Url::to('home', 'user-manage/edit'); ?>',
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
						location.href = Tools.url('home', 'user-manage/index');
					}, 3);
				}else{
					UBox.show(aResult.msg, aResult.status);
				}
			}
		});
	}
	
	$(function(){
		<?php if($aUser){ ?>
			$('.J-type').val(<?php echo $aUser['type']; ?>);
			$('.J-vip-level').val(<?php echo $aUser['vip_level']; ?>);
			$('.J-choushui-shuanfa').val(<?php echo $aUser['choushui_shuanfa']; ?>);
		<?php } ?>
	});
</script>