<?php
use umeworld\lib\Url;
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
						<input type="text" id="username" name="username" ng-model="username" class="form-control input-lg ng-pristine ng-untouched ng-invalid ng-invalid-required" required placeholder="账户">
					</div>
					<div class="form-group">
						<input type="password" id="password" name="password" ng-model="pwd" class="form-control input-lg ng-pristine ng-untouched ng-invalid ng-invalid-required" required placeholder="密码">
					</div>
					<div class="form-group">
						<img id="verifyImg" class="pull-left" style="position: relative; top: -8px;width: 174px; height: 60px;cursor:pointer;" src="<?php echo Url::to('home', 'login/captcha') . '?v=' . NOW_TIME; ?>" alt="" onclick="refreshCaptcha(this);">
						<input class="form-control input-lg ng-pristine ng-untouched ng-invalid ng-invalid-required" type="text" id="verifycode" name="verifycode" style="width:390px;" placeholder="验证码" />
					</div>
					<div class="form-group">
						<button class="btn btn-primary btn-lg btn-block" id="loginbutton" type="button" onclick="doLogin();">立即登陆</button>
					</div>
					<div class="form-group text-center text-danger" id="msg"></div>
				</form>
				<p class="help-block" style="text-align:center;">建议使用1920*1080分辨率以及Google Chrome浏览器以获得更好的体验</p>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function refreshCaptcha(o){
		$(o).attr('src', Tools.url('home', 'login/captcha') + '?v=' + Date.parse(new Date()));
	}
	
	function doLogin(o){
		if($(o).attr('disabled')){
			return;
		}
		ajax({
			url : Tools.url('home', 'login/login'),
			data : {
				account : $('#username').val(),
				password : $('#password').val(),
				captcha : $('#verifycode').val()
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
					}, 1);
				}else{
					UBox.show(aResult.msg, aResult.status);
				}
			}
		});
	}
	
	$(function(){
		$('input').keyup(function(e){
			if(e.keyCode == 13){
				$('#loginbutton').click();	
			}
		});
	});
</script>