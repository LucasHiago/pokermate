<?php 
$this->registerAssetBundle('common\assets\ManageCoreAsset');
$this->beginPage(); 
$mUser = Yii::$app->user->getIdentity();
$aUser = [];
if($mUser){
	$aUser = $mUser->toArray();
	unset($aUser['password']);
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
	</script>
</head>
<body>
	<?php $this->beginBody(); ?>
	<div id="framework"><?php echo $content; ?></div>
	<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage();