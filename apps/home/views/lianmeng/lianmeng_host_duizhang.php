<?php
use umeworld\lib\Url;
$this->setTitle('联盟主机对账');

$minColumn = 9;
?>

<div class="c-body-wrap lmzj-wrap">
	<div class="h50">
		<div class="h-left">
			<div class="h-select-list-bg h-l-div">
				<select class="J-lianmeng-selector" style="width:100%;height:30px;padding: 2px;color: #ff6a6a;">
					<?php foreach($aLianmengList as $aLianmeng){ ?>
					<option value="<?php echo $aLianmeng['id']; ?>"><?php echo $aLianmeng['name']; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="h-text-bg h-l-div" style="text-align:center;color: #f4e2a9;"><?php echo isset($aLianmengHostDuizhang['totalZhanDan']) ? $aLianmengHostDuizhang['totalZhanDan'] : 0; ?></div>
		</div>
		<div class="h-right">
			<div class="J-club-qin-zhang gy-btn-bg h-l-div" style="margin-left:400px;">清账</div>
			<div class="gy-btn-bg h-l-div" style="margin-left:20px;" onclick="AlertWin.showLianmengClubSetting(<?php echo $lianmengId; ?>);">联盟设置</div>
		</div>
	</div>
	<div class="lmzj-content-wrap">
		<div class="h20"></div>
		<div class="body-list-wrap" style="float:left;position: absolute;">
			<?php if($aLianmengHostDuizhang && $aLianmengHostDuizhang['aClubZhangDanList']){ ?>
			<div class="row-item">
				<div class="col-item thh">新帐</div>
				<?php foreach($aLianmengHostDuizhang['aClubZhangDanList'] as $aClubZhangDan){ ?>
				<div class="col-item"><?php echo $aClubZhangDan['zhang_dan']; ?></div>
				<?php } ?>
				<?php for($i = count($aLianmengHostDuizhang['aClubZhangDanList']); $i < $minColumn; $i++){ ?>
					<div class="col-item" style="background:none;"></div>
				<?php } ?>
			</div>
			<div class="row-item">
				<div class="col-item thh">旧帐</div>
				<?php foreach($aLianmengHostDuizhang['aClubZhangDanList'] as $aClubZhangDan){ ?>
				<div class="col-item"><input type="text" class="J-change-input-selector ci-txt" data-id="<?php echo $aClubZhangDan['lianmeng_club_id']; ?>" value="<?php echo $aClubZhangDan['qianzhang']; ?>" /><i class="ci-edit"></i></div>
				<?php } ?>
				<?php for($i = count($aLianmengHostDuizhang['aClubZhangDanList']); $i < $minColumn; $i++){ ?>
					<div class="col-item" style="background:none;"></div>
				<?php } ?>
			</div>
			<div class="row-item">
				<div class="col-item thh">汇总</div>
				<?php foreach($aLianmengHostDuizhang['aClubZhangDanList'] as $aClubZhangDan){ ?>
				<div class="col-item"><?php echo $aClubZhangDan['hui_zhong']; ?></div>
				<?php } ?>
				<?php for($i = count($aLianmengHostDuizhang['aClubZhangDanList']); $i < $minColumn; $i++){ ?>
					<div class="col-item" style="background:none;"></div>
				<?php } ?>
			</div>
			<div class="row-item">
				<div class="col-item thh">桌子</div>
				<?php foreach($aLianmengHostDuizhang['aClubZhangDanList'] as $aClubZhangDan){ ?>
				<div class="col-item" style="cursor:pointer;" onclick='AlertWin.showClubZhangDanDetail(<?php echo $lianmengId; ?>, <?php echo json_encode($aClubZhangDan['club_zhang_dan_list']); ?>, "<?php echo $aClubZhangDan['club_name']; ?>");'><?php echo $aClubZhangDan['club_name']; ?></div>
				<?php } ?>
				<?php for($i = count($aLianmengHostDuizhang['aClubZhangDanList']); $i < $minColumn; $i++){ ?>
					<div class="col-item" style="background:none;"></div>
				<?php } ?>
			</div>
			<?php foreach($aLianmengHostDuizhang['aPaijuZhangDanList'] as $aPaijuZhangDan){ ?>
			<div class="row-item lbb">
				<div class="col-item" style="cursor:pointer;" onclick="AlertWin.showPaijuDataList(<?php echo $aPaijuZhangDan['paiju_id']; ?>, true);"><?php echo $aPaijuZhangDan['paiju_name']; ?></div>
			<?php 
				foreach($aLianmengHostDuizhang['aClubZhangDanList'] as $aClubZhangDan){ 
					$num = 0;
					foreach($aClubZhangDan['club_zhang_dan_list'] as $aClubPaiju){
						if($aClubPaiju['paiju_id'] == $aPaijuZhangDan['paiju_id']){
							$num += $aClubPaiju['zhang_dan'];
						}
					}
			?>
				<div class="col-item"><?php echo $num; ?></div>
				<?php for($i = count($aLianmengHostDuizhang['aClubZhangDanList']); $i < $minColumn; $i++){ ?>
					<!--<div class="col-item" style="background:#231b2d;width:132px;margin:0px;"></div>-->
				<?php } ?>
			<?php } ?>
			</div>
			<?php } ?>
		<?php } ?>
		</div>
	</div>
</div>

<script type="text/javascript">	
	
	$(function(){
		$('.c-h-t-menu.m3').addClass('active');
		$('.J-lianmeng-selector').val(<?php echo $lianmengId; ?>);
		$('.J-lianmeng-selector').on('change', function(){
			location.href = Tools.url('home', 'lianmeng/lianmeng-host-duizhang') + '?id=' + $(this).val();
		});
		$('.body-list-wrap .row-item').each(function(){
			var colLength = parseInt($(this).find('.col-item').length);
			$(this).width(colLength * 132 + 20);
		});
		$('.body-list-wrap .col-item .ci-edit').click(function(){
			$(this).prev().focus();
		});
		$('.J-club-qin-zhang').click(function(){
			if(confirm('确定清账？')){
				var o = this;
				ajax({
					url : Tools.url('home', 'lianmeng/lianmeng-club-qin-zhang'),
					data : {id : <?php echo $lianmengId; ?>},
					beforeSend : function(){
						$(o).attr('disabled', 'disabled');
					},
					complete : function(){
						$(o).attr('disabled', false);
					},
					success : function(aResult){
						if(aResult.status == 1){
							UBox.show(aResult.msg, aResult.status, function(){
								location.reload();
							}, 3);
						}else{
							UBox.show(aResult.msg, aResult.status);
						}
					}
				});
			}
		});
		$('.J-change-input-selector').keyup(function(e){
			var o = this;
			if(e.keyCode == 13){
				ajax({
					url : Tools.url('home', 'lianmeng/update-lianmeng-club-info'),
					data : {
						id : $(o).attr('data-id'),
						type : 'qianzhang',
						value : $(o).val()
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
								location.reload();
							}, 3);
						}else{
							UBox.show(aResult.msg, aResult.status);
						}
					}
				});
			}
		});
		//$('.lmzj-wrap').height($('.body-list-wrap').height());
		//$('.body-list-wrap').tinyscrollbar({axis : 'x', scrollbarVisable : false, wheelSpeed : 10});
		//$('.body-list-wrap').find('.J-tinyscrollbar-scrollbar').css("right", "-8px");
		console.log(parseInt($('.body-list-wrap .row-item').length));
		var h = parseInt($('.body-list-wrap .row-item').length) * 44 + 20;
		var w = parseInt($('.body-list-wrap .row-item .col-item').length) * 134;
		$('.body-list-wrap').width(w);
		$('.body-list-wrap').height(h);
		$('.body-list-wrap').css('min-height', '655px');
		/*$('#pageWraper').height(h);
		$('.lmzj-wrap').height(h);
		$('.lmzj-content-wrap').height(h);
		$('.body-list-wrap').height(h);
		$('.body-list-wrap').tinyscrollbar({axis : 'x', scrollbarVisable : false, wheelSpeed : 10});*/
	});
</script>