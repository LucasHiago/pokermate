<?php
use umeworld\lib\Url;
$this->setTitle('联盟主机对账');

$minColumn = 9;
$pageCount = 8;
?>

<div class="J-go-scroll-left"></div>
<div class="J-go-scroll-right"></div>
<div class="c-body-wrap lmzj-wrap">
	<div class="h50">
		<div class="h-left">
			<div class="h-select-list-bg h-l-div">
				<select class="J-lianmeng-selector form-control" style="width:120px;">
					<?php foreach($aLianmengList as $aLianmeng){ ?>
					<option value="<?php echo $aLianmeng['id']; ?>"><?php echo $aLianmeng['name']; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="h-text-bg h-l-div"><b>联盟盈利：</b><?php echo isset($aLianmengHostDuizhang['totalZhanDan']) ? $aLianmengHostDuizhang['totalZhanDan'] : 0; ?></div>
		</div>
		<div class="h-right">
			<div class="J-club-qin-zhang btn btn-primary" style="float:right;margin-right:45px;">清账</div>
			<div class="btn btn-primary" style="float:right;margin-right:20px;" onclick="AlertWin.showLianmengClubSetting(<?php echo $lianmengId; ?>);">联盟设置</div>
		</div>
	</div>
	<div class="lmzj-content-wrap">
		<div class="h20"></div>
		<div class="body-list-wrap" style="/*float:left;position: absolute;*/">
			<?php if($aLianmengHostDuizhang && $aLianmengHostDuizhang['aClubZhangDanList']){ ?>
			<div class="row-item">
				<div class="col-item thh">新帐</div>
				<?php $itemPage = 1;$index = 1; foreach($aLianmengHostDuizhang['aClubZhangDanList'] as $aClubZhangDan){ ?>
				<div class="col-item <?php echo 'J-item-page item-page-' . $itemPage; ?>"><?php echo $aClubZhangDan['zhang_dan']; ?></div>
				<?php if($index % $pageCount == 0){$itemPage += 1;}$index++;} ?>
				<?php for($i = count($aLianmengHostDuizhang['aClubZhangDanList']); $i < $minColumn; $i++){ ?>
					<div class="col-item" style="background:none;"></div>
				<?php } ?>
			</div>
			<div class="row-item">
				<div class="col-item thh">旧帐</div>
				<?php $itemPage = 1;$index = 1; foreach($aLianmengHostDuizhang['aClubZhangDanList'] as $aClubZhangDan){ ?>
				<div class="col-item <?php echo 'J-item-page item-page-' . $itemPage; ?>"><input type="text" class="J-change-input-selector ci-txt form-control" data-id="<?php echo $aClubZhangDan['lianmeng_club_id']; ?>" value="<?php echo $aClubZhangDan['qianzhang']; ?>" /><i class="fa fa-pencil ci-edit"></i></div>
				<?php if($index % $pageCount == 0){$itemPage += 1;}$index++;} ?>
				<?php for($i = count($aLianmengHostDuizhang['aClubZhangDanList']); $i < $minColumn; $i++){ ?>
					<div class="col-item" style="background:none;"></div>
				<?php } ?>
			</div>
			<div class="row-item">
				<div class="col-item thh">汇总</div>
				<?php $itemPage = 1;$index = 1; foreach($aLianmengHostDuizhang['aClubZhangDanList'] as $aClubZhangDan){ ?>
				<div class="col-item <?php echo 'J-item-page item-page-' . $itemPage; ?>"><?php echo $aClubZhangDan['hui_zhong']; ?></div>
				<?php if($index % $pageCount == 0){$itemPage += 1;}$index++;} ?>
				<?php for($i = count($aLianmengHostDuizhang['aClubZhangDanList']); $i < $minColumn; $i++){ ?>
					<div class="col-item" style="background:none;"></div>
				<?php } ?>
			</div>
			<div class="row-item">
				<div class="col-item thh">桌子</div>
				<?php $itemPage = 1;$index = 1; foreach($aLianmengHostDuizhang['aClubZhangDanList'] as $aClubZhangDan){ ?>
				<div class="col-item <?php echo 'J-item-page item-page-' . $itemPage; ?>" style="cursor:pointer;" onclick='AlertWin.showClubZhangDanDetail(<?php echo $lianmengId; ?>, <?php echo json_encode($aClubZhangDan['club_zhang_dan_list']); ?>, "<?php echo $aClubZhangDan['club_name']; ?>");'><?php echo $aClubZhangDan['club_name']; ?></div>
				<?php if($index % $pageCount == 0){$itemPage += 1;}$index++;} ?>
				<?php for($i = count($aLianmengHostDuizhang['aClubZhangDanList']); $i < $minColumn; $i++){ ?>
					<div class="col-item" style="background:none;"></div>
				<?php } ?>
			</div>
			<?php foreach($aLianmengHostDuizhang['aPaijuZhangDanList'] as $aPaijuZhangDan){ ?>
			<div class="row-item lbb">
				<div class="col-item" style="cursor:pointer;" onclick="AlertWin.showPaijuDataList(<?php echo $aPaijuZhangDan['paiju_id']; ?>, true);"><?php echo $aPaijuZhangDan['paiju_name']; ?></div>
			<?php 
				$itemPage = 1;$index = 1; 
				foreach($aLianmengHostDuizhang['aClubZhangDanList'] as $aClubZhangDan){
					$num = 0;
					foreach($aClubZhangDan['club_zhang_dan_list'] as $pjid => $aClubPaiju){
						if($pjid == $aPaijuZhangDan['paiju_id']){
							$num += $aClubPaiju['zhang_dan'];
						}
					}
			?>
				<div class="col-item <?php echo 'J-item-page item-page-' . $itemPage; ?>"><?php echo $num; ?></div>
				<?php for($i = count($aLianmengHostDuizhang['aClubZhangDanList']); $i < $minColumn; $i++){ ?>
					<!--<div class="col-item" style="background:#231b2d;width:132px;margin:0px;"></div>-->
				<?php } ?>
			<?php if($index % $pageCount == 0){$itemPage += 1;}$index++;} ?>
			</div>
			<?php } ?>
		<?php }else{ ?>
			<div class="row-item">
				<div class="col-item thh">新帐</div>
			</div>
			<div class="row-item">
				<div class="col-item thh">旧帐</div>
			</div>
			<div class="row-item">
				<div class="col-item thh">汇总</div>
			</div>
			<div class="row-item">
				<div class="col-item thh">桌子</div>
			</div>
			<?php foreach($aLianmengHostDuizhang['aPaijuZhangDanList'] as $aPaijuZhangDan){ ?>
			<div class="row-item lbb">
				<div class="col-item" style="cursor:pointer;" onclick="AlertWin.showPaijuDataList(<?php echo $aPaijuZhangDan['paiju_id']; ?>, true);"><?php echo $aPaijuZhangDan['paiju_name']; ?></div>
			</div>	
			<?php } ?>	
		<?php } ?>
		</div>
	</div>
</div>
<div class="J-go-scroll-left fa fa-chevron-left" onclick="showItemPrevPage(this);"></div>
<div class="J-go-scroll-right fa fa-chevron-right" onclick="showItemNextPage(this);"></div>
<script type="text/javascript">	
	var currentItemPage = 1;
	function _showItemPage(){
		$('.J-item-page').hide();
		$('.J-item-page.item-page-' + currentItemPage).show();
	}
	function showItemNextPage(o){
		var page = currentItemPage + 1;
		if($('.J-item-page.item-page-' + page).length == 0){
			return;
		}
		currentItemPage = page;
		_showItemPage();
	}
	function showItemPrevPage(o){
		var page = currentItemPage - 1;
		if($('.J-item-page.item-page-' + page).length == 0){
			return;
		}
		currentItemPage = page;
		_showItemPage();
	}
	function initItemPage(){
		_showItemPage();
		$('.J-go-scroll-left').css({left : $('.lmzj-wrap').offset().left});
		$('.J-go-scroll-right').css({left : $('.lmzj-wrap').offset().left + 1320 - 40});
		if(parseInt($('.body-list-wrap .row-item.lbb:first .col-item').length) < 10){
			$('.J-go-scroll-left').hide();
			$('.J-go-scroll-right').hide();
		}
	}
	$(function(){
		$('.J-c-h-t-menu-m3').addClass('active');
		initItemPage();
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
		
		/*$('.J-go-scroll-left').css({left : $('.lmzj-wrap').offset().left});
		$('.J-go-scroll-right').css({left : $('.lmzj-wrap').offset().left + 1320 - 40});
		$('.J-go-scroll-left').click(function(){
			$('.body-list-wrap')[0].scrollLeft = $('.body-list-wrap')[0].scrollLeft - 1024;
		});
		$('.J-go-scroll-right').click(function(){
			$('.body-list-wrap')[0].scrollLeft = $('.body-list-wrap')[0].scrollLeft + 1024;
		});
		if(parseInt($('.body-list-wrap .row-item.lbb:first .col-item').length) < 10){
			$('.J-go-scroll-left').hide();
			$('.J-go-scroll-right').hide();
		}*/
		
		//$('.lmzj-wrap').height($('.body-list-wrap').height());
		//$('.body-list-wrap').tinyscrollbar({axis : 'x', scrollbarVisable : false, wheelSpeed : 10});
		//$('.body-list-wrap').find('.J-tinyscrollbar-scrollbar').css("right", "-8px");
		/*
		var h = parseInt($('.body-list-wrap .row-item').length) * 44 + 20;
		var w = parseInt($('.body-list-wrap .row-item.lbb:first .col-item').length) * 134;
		$('.body-list-wrap').width(w);
		$('.body-list-wrap').css('min-width', '134px');
		if(w == 0){
			$('.body-list-wrap').css('overflow', 'hidden');
		}
		$('.body-list-wrap').height(h);
		$('.body-list-wrap').css('min-height', '655px');
		*/
		/*$('#pageWraper').height(h);
		$('.lmzj-wrap').height(h);
		$('.lmzj-content-wrap').height(h);
		$('.body-list-wrap').height(h);
		$('.body-list-wrap').tinyscrollbar({axis : 'x', scrollbarVisable : false, wheelSpeed : 10});*/
	});
</script>