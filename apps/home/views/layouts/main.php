<?php 
use umeworld\lib\Url;
use common\model\User;
use yii\helpers\ArrayHelper;
$this->registerAssetBundle('common\assets\ManageCoreAsset');
$this->registerJsFile('@r.js.wdate-picker');
$this->registerJsFile('@r.js.alert.win');
$this->beginPage(); 
$mUser = Yii::$app->user->getIdentity();
$aUser = [];
$aClubList = [];
$aVipList = [];
if($mUser){
	$aUser = $mUser->toArray();
	//unset($aUser['password']);
	$aClubList = $mUser->getUserClubList();
	$aVipList = User::getVipList();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="format-detection" content="telephone=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title><?php echo $this->title; ?></title>
	<?php $this->head(); ?>
	<style type="text/css">
		.phcolor{color:#999999;}
		body .table-responsive table td{vertical-align: middle;}
	</style>
	<script type="text/javascript">
		if(window.App && !App.inited){
			App.config({
				isGuest : <?php echo $mUser ? 0 : 1; ?>,
				url : {
					resource : '<?php echo Yii::getAlias('@r.url'); ?>'
				},
				oCurrentUser : <?php echo json_encode($aUser); ?>
			});
		}
		$(function(){  
			//判断浏览器是否支持placeholder属性
			supportPlaceholder = 'placeholder' in document.createElement('input'),
			placeholder = function(input){
				var text = input.attr('placeholder'),
				defaultValue = input.defaultValue;
				if(text == undefined){
					return;
				}
				if(typeof(input.attr('data-type')) == 'undefined'){
					input.attr('data-type', input.attr('type'));
					input.attr('type', 'text');
				}
				if(!defaultValue){
					if(typeof(input.attr('phcolor')) == 'undefined'){
						input.val(text).addClass('phcolor');
					}else{
						input.val(text).addClass(input.attr('phcolor'));
					}
				}
				input.focus(function(){
					if(input.val() == text){
						$(this).val('');
					}
				});
				input.blur(function(){
					if(input.val() == ''){
						if(typeof(input.attr('phcolor')) == 'undefined'){
							$(this).val(text).addClass('phcolor');
						}else{
							$(this).val(text).addClass($(this).attr('phcolor'));
						}
						if(input.val() == text && $(this).attr('data-type') == 'password'){
							$(this).val(text).attr('type', 'text');
						}
					}
				});
				
				//输入的字符不为灰色
				input.keyup(function(){
					if(typeof(input.attr('phcolor')) == 'undefined'){
						$(this).removeClass('phcolor');
					}else{
						$(this).removeClass($(this).attr('phcolor'));
					}
					if($(this).val() == ''){
						$(this).attr('type', 'text');
					}else{
						$(this).attr('type', $(this).attr('data-type'));
					}
				});
			};

			//当浏览器不支持placeholder属性时，调用placeholder函数
			if(!supportPlaceholder){
				$('input').each(function(){
					text = $(this).attr('placeholder');
					if($(this).attr('type') == 'text' || $(this).attr('type') == 'password'){
						placeholder($(this));
					}
				});
			}
		});
		
		var isCanCloseWin = true;
		var isCloseWinRefresh = false;
		function showAlertWin(oDom, callback){
			var oHtml = $('<div class="J-alert-win-wrap" style="z-index:100;position:fixed;top:0px;left:0px;width:100%;height:100%;overflow-y: scroll;background:rgba(0,0,0,0.8);"></div>');
			oHtml.append(oDom);
			$('#framework').append(oHtml);
			document.documentElement.style.overflow = 'hidden';
			
			setTimeout(function(){
				$(document).on('click', function(e){
					if(!$(e.target).parents().hasClass('J-alert-win-wrap') && !$(e.target).hasClass('wrapUBox') && !$(e.target).parents().hasClass('wrapUBox')){
						if(isCanCloseWin){
							clearInterval(tt);
							oHtml.remove();
							document.documentElement.style.overflow = '';
							if(isCloseWinRefresh){
								location.reload();
							}
							isCloseWinRefresh = false;
						}
					}
				});
			}, 200);
			function ajust(){
				var winHeight = $(window).height();
				var winWidth = $(window).width();
				oHtml.css({width : $(window).width(), heigth: winHeight});
				var marginTop = "0px";
				if(oDom.height() < winHeight){
					marginTop = ((winHeight - oDom.height()) / 2) + "px";
				}
				if(winHeight > oDom.height()){
					//oDom.css({margin : "0 auto", "margin-top" : ((winHeight - oDom.height()) / 2) + "px"});
					oDom.css({"margin-left" : ((winWidth - oDom.width()) / 2) + "px", "margin-top" : marginTop});
				}else{
					//oDom.css({margin : "0 auto"});
					oDom.css({"margin-left" : ((winWidth - oDom.width()) / 2) + "px", "margin-top" : marginTop});
				}
				document.documentElement.style.overflow = 'hidden';
			}
			ajust();
			var tt = setInterval(function(){
				ajust();
			}, 100);
			$(window).resize(function(){
				ajust();
			});
			if(callback){
				callback();
			}
		}
		
		function setInputInterval(o){
			if(isNaN($(o).val()) || $(o).val() == ''){
				$(o).val(0);
			}else{
				$(o).val(parseInt($(o).val()));
			}
		}
		
	</script>
</head>
<body>
	<?php $this->beginBody(); ?>
	<div id="framework">
		<div id="pageWraper">
			<div class="c-head-wrap">
				<div class="c-h-left">
					<a href="<?php echo Url::to('home', 'index/index'); ?>" class="heitao-icon fa fa-user" title="<?php echo $mUser->name; ?>"></a>
					<?php if(!$mUser->is_active){ ?>
						<a class="vipinfo">未启用</a>
					<?php }else{ ?>
						<?php if($mUser->isVip()){ ?>
						<a class="vipinfo"><?php echo $aVipList[$mUser->vip_level]; ?>&nbsp;还有<?php echo $mUser->vipDaysRemaining(); ?>天到期</a>
						<?php }else{ ?>
						<a class="vipinfo">会员已到期</a>
						<?php } ?>
					<?php } ?>
				</div>
				<div class="c-h-center">
					<div class="c-h-center-w">
					<?php if(!$mUser->is_active){ ?>
						<div class="c-h-item">
							<div class="panel panel-yellow" style="height: 90px; margin-top: 5px;">
								<div class="panel-heading" style="cursor:pointer;">
									<h3 class="panel-title" style="text-align:center;" onclick="AlertWin.showUserActive(this);">启用设置</h3>
								</div>
								<div class="panel-body" style="text-align:center;">
									<a href="javascript:;" class="btn btn-primary" style="color: #fff; margin-top: -7px;" onclick="AlertWin.showUserActive(this);">开始启用</a>
								</div>
							</div>
						</div>
					<?php }else{ ?>
						<?php foreach($aClubList as $aClub){ ?>
							<div class="c-h-item">
								<div class="panel panel-yellow" style="width:155px;height: 90px; margin-top: 5px;">
									<div class="panel-heading" style="cursor:pointer;">
										<h3 class="panel-title" style="text-align:center;" onclick='AlertWin.showEditClub(<?php echo json_encode($aClub); ?>);'><?php echo $aClub['club_name']; ?></h3>
									</div>
									<div class="panel-body" style="text-align:center;">
										<a href="javascript:;" class="btn btn-primary" style="color: #fff; margin-top: -7px;" onclick="AlertWin.showFillSavecode(this, <?php echo $aClub['id']; ?>);">获取牌局</a>
									</div>
								</div>
							</div>
						<?php } ?>
						<div class="c-h-item" style="width:43px;margin-left: 15px;">
							<a href="javascript:;" class="J-top-btn-hover btn btn-default" onclick="AlertWin.showAddClub();" style="margin-top:36px;background: #e7e7e7;" title="添加俱乐部">+</a>
						</div>
					<?php } ?>
					</div>
				</div>
				<div class="c-h-right">
					<a href="<?php echo Url::to('home', 'login/logout'); ?>" class="btn btn-danger" style="float:right;margin-right:10px;margin-top:30px;">退出登录</a>
					<a class="btn btn-danger" onclick="clearUserData(this);" style="float:right;margin-right:10px;margin-top:30px;">安全密码</a>
					<?php if(!$mUser->is_active){ ?>
					<a onclick="UBox.show('提示:您的账号还没开始启用！', -1);" class="J-top-btn-hover btn btn-default" style="float:right;margin-right:10px;margin-top:30px;background: #e7e7e7;">日志管理</a>
					<a onclick="UBox.show('提示:您的账号还没开始启用！', -1);" class="J-top-btn-hover btn btn-default" style="float:right;margin-right:10px;margin-top:30px;background: #e7e7e7;">账号管理</a>
					<?php }else{ ?>
					<a href="<?php echo Url::to('home', 'operate-log-manage/index'); ?>" class="J-top-btn-hover btn btn-default" style="float:right;margin-right:10px;margin-top:30px;background: #e7e7e7;">日志管理</a>
					<a href="<?php echo Url::to('home', 'user-manage/index'); ?>" class="J-top-btn-hover btn btn-default" style="float:right;margin-right:10px;margin-top:30px;background: #e7e7e7;">账号管理</a>
					<?php } ?>
				</div>
				<div class="h20"></div>
				<div class="navbar navbar-default" style="background-color: #171515;">
					<div class="container" style="width:1320px;">
						<div class="navbar-collapse collapse">
							<ul class="nav navbar-nav">
								<li class="J-c-h-t-menu-m1"><a href="<?php echo Url::to('home', 'index/index'); ?>" style="width:150px;text-align:center;font-weight:bold;font-size:18px;">结账台</a></li>
								<?php if(!$mUser->is_active){ ?>
								<li class="J-c-h-t-menu-m2"><a onclick="UBox.show('提示:您的账号还没开始启用！', -1);" style="width:150px;text-align:center;font-weight:bold;font-size:18px;">代理分成</a></li>
								<?php }else{ ?>
								<li class="J-c-h-t-menu-m2"><a href="<?php echo Url::to('home', 'agent/index'); ?>" style="width:150px;text-align:center;font-weight:bold;font-size:18px;">代理分成</a></li>
								<?php } ?>
								<?php if(!$mUser->is_active){ ?>
									<li class="J-c-h-t-menu-m3"><a href="javascript:;" onclick="UBox.show('提示:您的账号还没开始启用！', -1);" style="width:150px;text-align:center;font-weight:bold;font-size:18px;">联盟主机对账</a></li>
								<?php }else{ ?>
									<?php if(!$mUser->hasLianmengHostDuiZhangFunction()){ ?>
									<li class="J-c-h-t-menu-m3"><a href="javascript:;" onclick="UBox.show('提示:需要黑金会员才能开通此功能！', -1);" style="width:150px;text-align:center;font-weight:bold;font-size:18px;">联盟主机对账</a></li>
									<?php }else{ ?>
									<li class="J-c-h-t-menu-m3"><a href="<?php echo Url::to('home', 'host-lianmeng/lianmeng-host-duizhang'); ?>" style="width:150px;text-align:center;font-weight:bold;font-size:18px;">联盟主机对账</a></li>
									<?php } ?>
								<?php } ?>
							</ul>
							<?php if(!$mUser->is_active){ ?>
							<a href="javascript:;" class="btn btn-lg btn-primary" style="float:right;position:relative;right:16px;top:2px;" onclick="UBox.show('提示:您的账号还没开始启用！', -1);">交班账单</a>
							<?php }else{ ?>
							<a href="javascript:;" class="btn btn-lg btn-primary" style="float:right;position:relative;right:16px;top:2px;" onclick="AlertWin.showJiaoBanZhuanChu();">交班账单</a>
						<?php } ?>
						</div>
						
					</div>
				</div>
			</div>
			<?php echo $content; ?>
			<div class="c-footer-wrap" style="height:20px;"></div>
		</div>	
	</div>
	<?php $this->endBody(); ?>
	<script type="text/javascript">
		function showImbalanceMoney(imbalanceMoney){
			imbalanceMoney = parseInt(imbalanceMoney);
			if(Math.abs(imbalanceMoney) > 1){
				$('.J-imbalance-money').addClass('fu');
			}else{
				$('.J-imbalance-money').removeClass('fu');
			}
			$('.J-imbalance-money').text(imbalanceMoney);
		}
		
		function clearUserData(o){
			var html = '';
			html += '<div style="height:56px;">';
				html += '<div style="height:28px;">';
					html += '确定删除数据？一旦输入安全密码，即清除所有数据，账号数据不留任何痕迹！';
				html += '</div>';
				html += '<div style="height:28px;margin-top: 26px;">';
					html += '<div style="float:left;height: 100%;line-height:28px;">安全密码：</div>';
					html += '<input type="text" class="J-save-code form-control" placeholder="请输入安全密码" style="float:left;height:28px;width:200px;" />';
				html += '</div>';
			html += '</div>';
			UBox.confirm(html, function(){
				ajax({
					url : Tools.url('home', 'user-manage/clear-user-data'),
					data : {
						saveCode : $('.J-save-code').val()
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
								location.href = Tools.url('home', 'index/index');
							}, 1);
						}else{
							UBox.show(aResult.msg, aResult.status);
						}
					}
				});
			});
		}
		
		$(function(){
			$('.c-h-center-w').width(parseInt($('.c-h-item').length) * 160 - 102);
			$('.c-h-center').tinyscrollbar({axis : 'x', scrollbarVisable : false, wheelSpeed : 10});
			
			$('.J-top-btn-hover').hover(function(){
				$(this).css('background', '#868686');
			});
			$('.J-top-btn-hover').on('mouseleave', function(){
				$(this).css('background', '#e7e7e7');
			});
		});
	</script>
</body>
</html>
<?php $this->endPage();