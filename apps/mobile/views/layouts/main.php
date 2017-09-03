<?php 
$this->registerAssetBundle('common\assets\CoreAsset');
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
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title><?php echo $this->title; ?></title>
	<?php $this->head(); ?>
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
	</script>
</head>
<body>
	<?php $this->beginBody(); ?>
	<div id="framework"><?php echo $content; ?></div>
	<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage();