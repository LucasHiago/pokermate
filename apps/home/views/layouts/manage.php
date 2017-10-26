<?php 
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
$this->registerAssetBundle('common\assets\ManageCoreAsset');
$this->beginPage();
$mUser = Yii::$app->user->getIdentity(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="format-detection" content="telephone=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title><?php echo $this->title ? $this->title : '后台管理'; ?></title>
	<meta name="keywords" content="" />
	<meta name="description" content="" /> 
	<?php $this->head(); ?>
	<style type="text/css">
	body .table-responsive table td{vertical-align: middle;}
	.umNavbar .navbar-collapse{padding-left:0;}
	.navbar-nav a.active{outline: none; background-color:#000 !important; color:#FFF;}

	.umNoBorderTable .column{padding:8px 3px;}
	.umNoBorderTable input{margin-top:-7px; width:90%;}
	.umNoBorderTable .header{background:#F5F5F5;}
	.umNoBorderTable .row{margin-left:0;}
	.umNoBorderTable .row:hover{background:#F5F5F5;}
	
	.container-fluid form{width:1000px;width:100%;height:100%;}

	.edui-container * {
	  -webkit-box-sizing: content-box;
	  -moz-box-sizing: content-box;
	  box-sizing: content-box;
	}

	.navbar-nav a.active {
		outline: none;
		background-color: #000 !important;
		color: #FFF;
	}
	.table-responsive .table{font-size:14px;}
	</style>
</head>
<body style="background: #222;">
	<?php $this->beginBody(); ?>
	<div id="wrapper">
		<div class="container-fluid">
			<?php echo \home\widgets\Navi::widget(); ?>
		</div>
		<div id="page-wrapper">
			<div class="container-fluid">
				<br />
				<br />
				<?= $content ?>
			</div>
		</div>
		<!-- /#page-wrapper -->
	</div>
	<?php $this->endBody(); ?>
	<footer class="footer">
		<center style="color:#999;line-height:50px;font-size:12px;"></center>
	</footer>
	<script type="text/javascript">
		$(function(){
			$('#page-wrapper').css({"min-height": ($(window).height() - 100) + 'px'});
			$('.J-search-form').width($('.page-header').width());
			$('.J-side-menu').css({'max-height': $('#page-wrapper').css('min-height')});
			$('.J-side-menu').css({'overflow-x': 'hidden'});
			<?php if(!$mUser->isManager()){ ?>
				$('.J-side-menu').prepend('<li class="J-top-child "><a class="collapsed" href="<?php echo Url::to('home', 'user-manage/show-edit', ['id' => $mUser->id]); ?>"><i class="fa fa-fw fa-user"></i> 账号管理</a></li>');
			<?php } ?>
			$('.J-top-child a').each(function(){
				var o = this;
				if($(this).attr('href') == Tools.url('home', 'user-manage/clear-user-data')){
					$(this).attr('href', 'javascript:;');
					$(this).click(function(){
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
					});
				}
			});
		});
		
		function showJumpPage(){
			var $oPagination = $('ul.pagination');
			if($oPagination.length > 0){
				$oPagination.append('<input type="text" class="J-jump-page form-control" style="margin:0 10px;width:50px;float:left;text-align:center;" /><button type="button" class="btn" onclick="jumpPage();" >Go</button>');
			}
		}
		function jumpPage(){
			var jumpPage = parseInt($('.J-jump-page').val());
			var url = location.href;
			if(url.indexOf('?') != -1){
				var linkUrl = url.substring(0, url.indexOf('?'));
				var paramStr = url.substring(url.indexOf('?') + 1, url.length);
				var aParam = paramStr.split('&');
				var parm = 'page=' + jumpPage + '&';
				if(aParam.length > 0){
					for(var i in aParam){
						var aTemp = aParam[i].split('=');
						if(aTemp[0] == 'page'){
							continue;
						}
						parm = parm + aParam[i] + '&';
					}
					url = linkUrl + '?' + parm;
				}
			}else{
				url = url + '?page=' + jumpPage;
			}

			location.href = url;
		}
		var isCanCloseWin = true;
		var isCloseWinRefresh = false;
		function showAlertWin(oDom, callback){
			var oHtml = $('<div class="J-alert-win-wrap" style="z-index:100000;position:fixed;top:0px;left:0px;width:100%;height:100%;overflow-y: scroll;background:rgba(0,0,0,0.8);"></div>');
			oHtml.append(oDom);
			$('#wrapper').append(oHtml);
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
	</script>
</body>
</html>
<?php $this->endPage();