<?php
use umeworld\lib\Url;
$this->setTitle('结账台');
?>
<div id="pageWraper">
	<div class="c-head-wrap">
		<div class="c-h-left">
			<a href="javascript:;" class="heitao-icon"></a>
			<a class="vipinfo">VIP7&nbsp;还有15天到期</a>
		</div>
		<div class="c-h-center">
			<div class="c-h-center-w">
				<div class="c-h-item">
					<a href="javascript:;" class="c-h-i-up">俱乐部</a>
					<a href="javascript:;" class="c-h-i-down"></a>
				</div>
				<div class="c-h-item">
					<a  href="javascript:;" class="c-h-i-up">俱乐部</a>
					<a href="javascript:;" class="c-h-i-down"></a>
				</div>
				<div class="c-h-item" style="width:43px;margin-left: 15px;">
					<a href="javascript:;" class="add-icon"></a>
				</div>
			</div>
		</div>
		<div class="c-h-right">
			<a href="javascript:;" class="log-icon"></a>
			<a href="javascript:;" class="setting-icon"></a>
			<a href="javascript:;" class="close-icon"></a>
		</div>
		<div class="c-h-tab-w">
			<a href="<?php echo Url::to('home', 'index/index'); ?>" class="c-h-t-menu m1"></a>
			<a href="<?php echo Url::to('home', 'agent/index'); ?>" class="c-h-t-menu m2 active"></a>
			<a href="javascript:;" class="c-h-t-menu m3"></a>
			<a href="javascript:;" class="c-h-t-jiaoban"></a>
		</div>
	</div>
	<div class="c-body-wrap">
		<div class="ag-bg"></div>
	</div>
</div>

<script type="text/javascript">	
	$(function(){
		$('.c-h-center-w').width(parseInt($('.c-h-item').length) * 160 - 102);
		$('.J-jsfs').on('click', function(){
			var html = '';
			html += '<div class="J-jsfs-select">';
				html += '<div class="J-jsfs-select-item">支付宝</div>';
				html += '<div class="J-jsfs-select-item">微信</div>';
				html += '<div class="J-jsfs-select-item">银行卡</div>';
			html += '</div>';
			var oHtml = $(html);
			$('#pageWraper').append(oHtml);
			oHtml.css({top: $(this).offset().top + 33, left: $(this).offset().left});
			oHtml.find('.J-jsfs-select-item').on('click', function(){
				$('.J-jsfs').text($(this).text());
				oHtml.remove();
			});
			$(document).on('click', function(e){
				if(!$(e.target).hasClass('J-jsfs')){
					oHtml.remove();
				}
			});
		});
	});
</script>