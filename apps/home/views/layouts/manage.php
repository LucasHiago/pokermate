<?php 
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
$this->registerAssetBundle('common\assets\ManageCoreAsset');
$this->beginPage(); 
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
	</script>
</body>
</html>
<?php $this->endPage();