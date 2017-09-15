<?php 
use umeworld\lib\Url;

$controllerId = Yii::$app->controller->id;
$actionId = Yii::$app->controller->action->id;
?>
<style type="text/css">
	.top_menu_current{color: #fff;background-color: #000;}
</style>
<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header">
		<a class="navbar-brand">
			后台管理
		</a>
	</div>
	<!-- Top Menu Items -->
	<ul class="nav navbar-right top-nav">
		<li class="J-dropdown dropdown">
			<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $aUser['name']; ?> <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li>
					<a href="<?php echo Url::to('home', 'index/index'); ?>"><i class="fa fa-fw fa-home"></i> 返回前端</a>
				</li>
				<li>
					<a href="<?php echo Url::to('home', 'login/logout'); ?>"><i class="fa fa-fw fa-power-off"></i> 退出登录</a>
				</li>
			</ul>
		</li>
	</ul>
	<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
	<div class="collapse navbar-collapse navbar-ex1-collapse">
		<ul class="J-side-menu nav navbar-nav side-nav">
		<?php 
			foreach($aMenuConfig as $key => $aValue){
				if(!in_array($role, $aValue['permission'])){
					continue;
				}
				$hasChild = false;
				$isCurrent = false;
				$isChildCurrent = false;
				if($aValue['child']){
					$hasChild = true;
					foreach($aValue['child'] as $k => $aChild){
						if(isset($aChild['url']) && $controllerId . '/' . $actionId == $aChild['url'][0]){
							$isChildCurrent = true;
							break;
						}
					}
				}
				if(isset($aValue['url']) && $controllerId . '/' . $actionId == $aValue['url'][0]){
					$isCurrent = true;
				}
				$cls = '';
				if($isChildCurrent){
					$cls = 'collapsed in';
					$isCurrent = true;
				}
				if(!$isCurrent){
					$cls = 'collapsed';
				}
		?>
			<li class="J-top-child <?php echo $isCurrent ? 'active' : ''; ?>">
				<a class="<?php echo $isCurrent ? '' : 'collapsed'; ?>" <?php echo !$hasChild ? 'href="' . Url::to('home', $aValue['url'][0]) . '"' : 'href="javascript:;" data-toggle="collapse" data-target="#' . $aValue['en_title'] . '"'; ?>><i class="fa fa-fw fa-<?php echo $aValue['icon_class']; ?>"></i> <?php echo $aValue['title']; ?></a>
				<?php if($hasChild){ ?>
					<ul id="<?php echo $aValue['en_title']; ?>" class="collapse <?php echo $cls; ?>">
					<?php 
						foreach($aValue['child'] as $k => $aChild){ 
							$activeCls = '';
							if(isset($aChild['url']) && $controllerId . '/' . $actionId == $aChild['url'][0]){
								$activeCls = 'active';
							}
					?>
						<li>
							<a class="<?php echo $activeCls; ?>" href="<?php echo Url::to('home', $aChild['url'][0]); ?>"><i class="fa fa-fw fa-<?php echo $aChild['icon_class']; ?>"></i> <?php echo $aChild['title']; ?></a>
						</li>
					<?php } ?>
					</ul>
				<?php } ?>
			</li>
		<?php } ?>
		</ul>
	</div>
	<!-- /.navbar-collapse -->
</nav>
<script type="text/javascript">
	function toggleTopMenu(typeStr, o){
		if(o){
			$(o).parent().parent().find('li a').removeClass('top_menu_current');
			$(o).addClass('top_menu_current');
			location.href = $(o).attr('data-url');
		}else{
			$('.J-top-child').hide();
			$('.J-top-' + typeStr).show();
		}
	}
	
	$(function(){
		$('.J-side-menu').css({'max-height': $('#page-wrapper').css('min-height')});
		$('.J-dropdown').on('click', function(){
			if($(this).hasClass('open')){
				$(this).removeClass('open');
			}else{
				$(this).addClass('open');
			}
		});
	});
</script>