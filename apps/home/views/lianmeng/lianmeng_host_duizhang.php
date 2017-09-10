<?php
use umeworld\lib\Url;
$this->setTitle('联盟主机对账');
?>

<div class="c-body-wrap lmzj-wrap">
	<div class="h50">
		<div class="h-left">
			<div class="h-select-list-bg h-l-div">
				<select style="width:100%;height:30px;padding: 2px;">
					<option value="">BOO小</option>
					<option value="">BOO大</option>
				</select>
			</div>
			<div class="h-text-bg h-l-div" style="text-align:center;">1000</div>
		</div>
		<div class="h-right">
			<div class="gy-btn-bg h-l-div" style="margin-left:400px;">清账</div>
			<div class="gy-btn-bg h-l-div" style="margin-left:20px;" onclick="AlertWin.showLianmengSetting();">联盟设置</div>
		</div>
	</div>
	<div class="lmzj-content-wrap">
		<div class="h20"></div>
		<div class="body-list-wrap">
			<div class="row-item">
				<div class="col-item thh">新帐</div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
			</div>
			<div class="row-item">
				<div class="col-item thh">旧帐</div>
				<div class="col-item"><input type="text" class="ci-txt" value="" /><i class="ci-edit"></i></div>
				<div class="col-item"><input type="text" class="ci-txt" value="" /><i class="ci-edit"></i></div>
				<div class="col-item"><input type="text" class="ci-txt" value="" /><i class="ci-edit"></i></div>
				<div class="col-item"><input type="text" class="ci-txt" value="" /><i class="ci-edit"></i></div>
				<div class="col-item"><input type="text" class="ci-txt" value="" /><i class="ci-edit"></i></div>
				<div class="col-item"><input type="text" class="ci-txt" value="" /><i class="ci-edit"></i></div>
				<div class="col-item"><input type="text" class="ci-txt" value="" /><i class="ci-edit"></i></div>
				<div class="col-item"><input type="text" class="ci-txt" value="" /><i class="ci-edit"></i></div>
				<div class="col-item"><input type="text" class="ci-txt" value="" /><i class="ci-edit"></i></div>
			</div>
			<div class="row-item">
				<div class="col-item thh">汇总</div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
			</div>
			<div class="row-item">
				<div class="col-item thh">桌子</div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
			</div>
			<div class="row-item lbb">
				<div class="col-item">牌局名</div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
			</div>
			<div class="row-item lbb">
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
				<div class="col-item"></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">	
	
	$(function(){
		$('.c-h-t-menu.m3').addClass('active');
		$('.body-list-wrap .row-item').each(function(){
			var colLength = parseInt($(this).find('.col-item').length);
			$(this).width(colLength * 132 + 20);
		});
		$('.body-list-wrap .col-item .ci-edit').click(function(){
			$(this).prev().focus();
		});
		//$('.lmzj-wrap').height($('.body-list-wrap').height());
		$('.body-list-wrap').tinyscrollbar({axis : 'x', scrollbarVisable : false, wheelSpeed : 5});
		//$('.body-list-wrap').find('.J-tinyscrollbar-scrollbar').css("right", "-8px");
	});
</script>