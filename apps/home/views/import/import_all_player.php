<?php
use umeworld\lib\Url;
$this->registerAssetBundle('common\assets\ManageCoreAsset');
$this->registerAssetBundle('common\assets\FileAsset');
$this->setTitle('导入全部客人信息Excel文件');
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>导入全部客人信息Excel文件</title>
	<style type="text/css">
		html,body,*{margin:0;padding:0;}
		.J-upfile-wrap{margin: 0 auto; min-height: 600px; background: #ffffff;}
		.J-upfile-wrap .title{height: 100px; line-height: 100px; text-align: center; color: #333; font-size: 22px;}
		.J-upfile-wrap .up-btn{margin: 0 auto; margin-top: 112px; width: 150px; height: 35px; line-height: 35px; text-align: center; color: #FFC107; font-size: 18px; border-radius: 20px; background: #a03e3e; cursor: pointer;}
		.J-upfile-wrap .J-tip-msg{display:none;margin-top: 20px; width: 100%; height: 35px; line-height: 35px; text-align: center; color: #00ff00; font-size: 16px; }
	</style>
</head>
<body>
	<div class="J-upfile-wrap">
		<div class="title">上传Excel文件</div>
		<div class="up-btn" onclick="uploadProfile(this);">请选择文件</div><input type="file" style="display:none;" />
		<div class="J-tip-msg">正在上传，请稍等!</div>
	</div>
	
	<script type="text/javascript">
		function uploadProfile(o){
			$(o).next().trigger('click');
		}
		
		$(function(){
			$('input[type="file"]').on('change', function(){
				var self = this;
				$('.J-tip-msg').show();
				Tools.uploadFileHandle('<?php echo Url::to('home', 'import/upload-all-player-excel'); ?>', self['files'][0], function(aData){
					UBox.show(aData.msg, aData.status);
					$('.J-tip-msg').hide();
				});
			});
		});
	</script>
</body>
</html>