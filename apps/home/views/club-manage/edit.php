<?php 
use umeworld\lib\Url;
use common\model\User;
$this->setTitle($aClub ? '编辑俱乐部' : '新增俱乐部');
?>
<br />
<div class="row">
	<div class="col-lg-12">
		<input class="J-id form-control" type="hidden" value="<?php echo $aClub ? $aClub['id'] : 0; ?>">
		<div class="form-group">
			<label>俱乐部名称</label>
			<input style="width:300px;" class="J-club-name form-control" value="<?php echo $aClub ? $aClub['club_name'] : ''; ?>">
		</div>
		<div class="form-group">
			<label>俱乐部ID</label>
			<input style="width:300px;" class="J-club-id form-control" value="<?php echo $aClub ? $aClub['club_id'] : ''; ?>">
		</div>
		<div class="form-group">
			<label>官网后台账号</label>
			<input style="width:300px;" class="J-club-login-name form-control" value="<?php echo $aClub ? $aClub['club_login_name'] : ''; ?>">
		</div>
		<div class="form-group">
			<label>官网后台密码</label>
			<input type="password" style="width:300px;" class="J-club-login-password form-control" value="<?php echo $aClub ? $aClub['club_login_password'] : ''; ?>">
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
			clubName : $('.J-club-name').val(),
			clubId : $('.J-club-id').val(),
			clubLoginName : $('.J-club-login-name').val(),
			clubLoginPassword : $('.J-club-login-password').val()
		};
		
		ajax({
			url : '<?php echo Url::to('home', 'club-manage/edit'); ?>',
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
						location.href = Tools.url('home', 'club-manage/index');
					}, 1);
				}else{
					UBox.show(aResult.msg, aResult.status);
				}
			}
		});
	}
	
	$(function(){
		<?php if(!$aClub){ ?>
			setTimeout(function(){
				$('.J-club-login-name').val('');
				$('.J-club-login-password').val('');
			}, 2000);
		<?php }else{ ?>
			setTimeout(function(){
				$('.J-club-login-name').val('<?php echo $aClub['club_login_name']; ?>');
				$('.J-club-login-password').val('<?php echo $aClub['club_login_password']; ?>');
			}, 2000);
		<?php } ?>
	});
</script>