<?php 
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
$this->registerAssetBundle('common\assets\ManageCoreAsset');
$this->beginPage(); 
$mUser = Yii::$app->user->getIdentity();
$aUser = [];
$aClubList = [];
if($mUser){
	$aUser = $mUser->toArray();
	//unset($aUser['password']);
	$aClubList = $mUser->getUserClubList();
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
	<style type="text/css">.phcolor{color:#999999;}</style>
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
		
		function showAlertWin(oDom, callback){
			var oHtml = $('<div class="J-alert-win-wrap" style="z-index:100;position:fixed;top:0px;left:0px;width:100%;height:100%;overflow-y: scroll;background:rgba(0,0,0,0.8);"></div>');
			oHtml.append(oDom);
			$('#framework').append(oHtml);
			document.documentElement.style.overflow = 'hidden';
			
			setTimeout(function(){
				$(document).on('click', function(e){
					if(!$(e.target).parents().hasClass('J-alert-win-wrap') && !$(e.target).hasClass('wrapUBox') && !$(e.target).parents().hasClass('wrapUBox')){
						oHtml.remove();
						document.documentElement.style.overflow = '';
					}
				});
			}, 200);
			function ajust(){
				var winHeight = $(window).height();
				oHtml.css({width : $(window).width(), heigth: winHeight});
				if(winHeight > oDom.height()){
					oDom.css({margin : "0 auto", "margin-top" : ((winHeight - oDom.height()) / 2) + "px"});
				}else{
					oDom.css({margin : "0 auto"});
				}
			}
			ajust();
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
		<?php echo $this->render(Yii::getAlias('@r.js.alert.win')); ?>
	</script>
</head>
<body>
	<?php $this->beginBody(); ?>
	<div id="framework">
		<div id="pageWraper">
			<div class="c-head-wrap">
				<div class="c-h-left">
					<a href="javascript:;" class="heitao-icon" title="<?php echo $mUser->name; ?>"></a>
					<?php if($mUser->isVip()){ ?>
					<a class="vipinfo">VIP<?php echo $mUser->vip_level; ?>&nbsp;还有<?php echo $mUser->vipDaysRemaining(); ?>天到期</a>
					<?php }else{ ?>
					<a class="vipinfo">VIP已到期</a>
					<?php } ?>
				</div>
				<div class="c-h-center">
					<div class="c-h-center-w">
					<?php foreach($aClubList as $aClub){ ?>
						<div class="c-h-item">
							<a href="javascript:;" class="c-h-i-up" onclick='AlertWin.showEditClub(<?php echo json_encode($aClub); ?>);'><?php echo $aClub['club_name']; ?></a>
							<a href="javascript:;" class="c-h-i-down" onclick="AlertWin.showFillSavecode(<?php echo $aClub['id']; ?>);"></a>
						</div>
					<?php } ?>
						<div class="c-h-item" style="width:43px;margin-left: 15px;">
							<a href="javascript:;" class="add-icon" onclick="AlertWin.showAddClub();"></a>
						</div>
					</div>
				</div>
				<div class="c-h-right">
					<a href="<?php echo Url::to('home', 'user-manage/index'); ?>" class="log-icon"></a>
					<a href="javascript:;" class="setting-icon" onclick="AlertWin.showEditUserInfo();"></a>
					<a href="<?php echo Url::to('home', 'login/logout'); ?>" class="close-icon"></a>
				</div>
				<div class="c-h-tab-w">
					<a href="<?php echo Url::to('home', 'index/index'); ?>" class="c-h-t-menu m1"></a>
					<a href="<?php echo Url::to('home', 'agent/index'); ?>" class="c-h-t-menu m2"></a>
					<a href="<?php echo Url::to('home', 'lianmeng/lianmeng-host-duizhang'); ?>" class="c-h-t-menu m3"></a>
					<a href="javascript:;" class="c-h-t-jiaoban" onclick="AlertWin.showJiaoBanZhuanChu();"></a>
				</div>
			</div>
			<?php echo $content; ?>
		</div>	
	</div>
	<?php $this->endBody(); ?>
	<script type="text/javascript">
		$(function(){
			$('.c-h-center-w').width(parseInt($('.c-h-item').length) * 160 - 102);
			$('.c-h-center').tinyscrollbar({axis : 'x', scrollbarVisable : false, wheelSpeed : 5});
		});
	</script>
</body>
</html>
<?php $this->endPage();