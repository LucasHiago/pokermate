<?php
use umeworld\lib\Url;
use common\model\SystemFeedTag;
$this->setTitle('用户登录');
?>
<style type="text/css">
	#loginModal{background:#ffffff;}
</style>
<div id="loginModal" class="modal show">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="text-center text-primary">登陆</h1>
			</div>
			<div class="modal-body">
				<form id="loginForm" action="" class="" >
					<div class="form-group">
						<font id="msg" color="red"></font>
						<input type="text" id="txtAdminUser" name="txtAdminUser" ng-model="username" class="form-control input-lg ng-pristine ng-untouched ng-invalid ng-invalid-required" required placeholder="账户">
					</div>
					<div class="form-group">
						<input type="password" id="txtAdminPWD" name="txtAdminPWD" ng-model="pwd" class="form-control input-lg ng-pristine ng-untouched ng-invalid ng-invalid-required" required placeholder="密码">
					</div>
					<div class="form-group">
						<button class="btn btn-primary btn-lg btn-block" id="loginbutton" type="button" onclick="doLogin();">立即登陆</button>
					</div>
					<div class="form-group text-center text-danger" id="msg"></div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function doLogin(o){
		if($(o).attr('disabled')){
			return;
		}
		ajax({
			url : Tools.url('home', 'login/login'),
			data : {
				account : $('#txtAdminUser').val(),
				password : $('#txtAdminPWD').val()
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
						location.href = aResult.data;
					}, 3);
				}else{
					UBox.show(aResult.msg, aResult.status);
				}
			}
		});
	}
	
	$(function(){
		
	});
</script>