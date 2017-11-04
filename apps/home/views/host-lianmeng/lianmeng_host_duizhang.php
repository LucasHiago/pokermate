<?php
use umeworld\lib\Url;
$this->setTitle('联盟主机对账');

$minColumn = 9;
$pageCount = 8;
?>

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
			<div class="h-text-bg h-l-div" style="background:none;width:100px;min-width:58px;"><div class="btn btn-primary" onclick="AlertWin.showLianmengClubSetting(<?php echo $lianmengId; ?>);">俱乐部设置</div></div>
			<div class="h-text-bg h-l-div" style="background:none;width:58px;min-width:85px;"><div class="J-club-qin-zhang btn btn-primary">清理账单</div></div>
			<div class="h-text-bg h-l-div" style="background:none;width:110px;min-width:85px;"><div class="btn btn-primary" onclick='AlertWin.showLianmengClubDetail(<?php echo isset($aLianmengHostDuizhang['aClubDetailList']) ? json_encode($aLianmengHostDuizhang['aClubDetailList']) : json_encode([]); ?>);'>俱乐部详情</div></div>
			<div class="h-text-bg h-l-div"><b>总局数：</b><?php echo isset($aLianmengHostDuizhang['totalPaijuCount']) ? $aLianmengHostDuizhang['totalPaijuCount'] : 0; ?></div>
			<!--<div class="h-text-bg h-l-div"><b>保险盈利：</b><?php echo isset($aLianmengHostDuizhang['totalHeduishuziPaijuCount']) ? $aLianmengHostDuizhang['totalHeduishuziPaijuCount'] : 0; ?></div>-->
			<div class="h-text-bg h-l-div"><b>保险抽成：</b><?php echo isset($aLianmengHostDuizhang['totalBaoXianBeiChou']) ? $aLianmengHostDuizhang['totalBaoXianBeiChou'] : 0; ?></div>
			<div class="h-text-bg h-l-div"><b>误差盈利：</b><?php echo isset($aLianmengHostDuizhang['totalZhanDan']) ? $aLianmengHostDuizhang['totalZhanDan'] : 0; ?></div>
			<!--<div class="h-text-bg h-l-div"><b>牌局误差值总和：</b><?php echo isset($aLianmengHostDuizhang['totalHeduishuzi']) ? $aLianmengHostDuizhang['totalHeduishuzi'] : 0; ?></div>-->
		</div>
		<div class="h-right">
			<div class="btn btn-warning" style="float:right;margin-right:42px;margin-top: 8px;" onclick='AlertWin.showLianmengLmzjPaijuCreater();'>联盟设置</div>
			<div class="btn btn-warning" style="float:right;margin-right:10px;margin-top: 8px;" onclick='refreshPaijuTime(this);'>更新牌局</div>
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
				<div class="col-item thh" style="border-right: 128px solid transparent; border-bottom: 40px solid transparent;">
					<span style="position: relative; float: left; width: 63px; height: 0px; top: 8px; left: 0px; font-size: 14px;">牌局</span>
					<span style="position: relative; float: right; width: 74px; height: 0px; top: -9px; right: -132px; font-size: 14px;">俱乐部</span>
					<i style="float: left;height: 1px; width: 132px; position: relative; display: block; background: #ffffff; transform: rotate(17deg); -webkit-transform: rotate(17deg); top: 20px; left: -2px;"></i>
				</div>
				<?php $itemPage = 1;$index = 1; foreach($aLianmengHostDuizhang['aClubZhangDanList'] as $aClubZhangDan){ ?>
				<div class="col-item <?php echo 'J-item-page item-page-' . $itemPage; ?>" style="cursor:pointer;" onclick='AlertWin.showClubZhangDanDetail(<?php echo $lianmengId; ?>, <?php echo json_encode($aClubZhangDan['club_zhang_dan_list']); ?>, "<?php echo $aClubZhangDan['club_name']; ?>");' title="<?php echo $aClubZhangDan['club_name']; ?>">
					<?php if($aClubZhangDan['lianmeng_club_id']){ ?>
						<?php echo $aClubZhangDan['club_name']; ?>
					<?php }else{ ?>
						<font style="color:#ff0000;"><?php echo $aClubZhangDan['club_name']; ?></font>
					<?php } ?>
				</div>
				<?php if($index % $pageCount == 0){$itemPage += 1;}$index++;} ?>
				<?php for($i = count($aLianmengHostDuizhang['aClubZhangDanList']); $i < $minColumn; $i++){ ?>
					<div class="col-item" style="background:none;"></div>
				<?php } ?>
			</div>
			<?php foreach($aLianmengHostDuizhang['aPaijuZhangDanList'] as $aPaijuZhangDan){ ?>
			<div class="row-item lbb">
				<div class="col-item" style="cursor:pointer;" onclick="AlertWin.showPaijuDataList(<?php echo $aPaijuZhangDan['paiju_id']; ?>, true);">
					<span style="position: relative; display: block; height: 25px; line-height: 25px; font-size: 16px; float: left; width: 100%;"><?php echo $aPaijuZhangDan['paiju_name']; ?></span>
					<span style="float: left; display: block; position: relative; line-height: 15px; width: 100%; font-size: 12px;">账单误差：<font style="color:<?php echo $aPaijuZhangDan['hedui_shuzi'] ? '#ff0000;' : '#00ff00' ?>"><?php echo $aPaijuZhangDan['hedui_shuzi']; ?></font></span>
				</div>
			<?php 
				$itemPage = 1;$index = 1; 
				foreach($aLianmengHostDuizhang['aClubZhangDanList'] as $aClubZhangDan){
					$num = 0;
					$floatNum = 0;
					foreach($aClubZhangDan['club_zhang_dan_list'] as $pjid => $aClubPaiju){
						if($pjid == $aPaijuZhangDan['paiju_id']){
							$num += $aClubPaiju['zhang_dan'];
							$floatNum += $aClubPaiju['float_zhang_dan'];
						}
					}
			?>
				<div class="col-item <?php echo 'J-item-page item-page-' . $itemPage; ?>" data-float-zhang-dang="<?php echo $floatNum; ?>"><?php echo $num; ?></div>
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
				<div class="col-item thh" style="border-right: 128px solid transparent; border-bottom: 40px solid transparent;">
					<span style="position: relative; float: left; width: 63px; height: 0px; top: 8px; left: 0px; font-size: 14px;">牌局</span>
					<span style="position: relative; float: right; width: 74px; height: 0px; top: -9px; right: -132px; font-size: 14px;">俱乐部</span>
					<i style="float: left;height: 1px; width: 132px; position: relative; display: block; background: #ffffff; transform: rotate(17deg); -webkit-transform: rotate(17deg); top: 20px; left: -2px;"></i>
				</div>
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
<div class="J-go-scroll-left" onclick="showItemPrevPage(this);"><i class="fa fa-chevron-left"></i></div>
<div class="J-go-scroll-right" onclick="showItemNextPage(this);"><i class="fa fa-chevron-right"></i></div>
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
		$('.J-go-scroll-left').css({left : $('.lmzj-wrap').offset().left + 5});
		$('.J-go-scroll-right').css({left : $('.lmzj-wrap').offset().left + 1320 - 40});
		if(parseInt($('.body-list-wrap .row-item:first .col-item').length) < 10){
			$('.J-go-scroll-left').hide();
			$('.J-go-scroll-right').hide();
		}
	}
	function refreshPaijuTime(o){
		ajax({
				url : Tools.url('home', 'host-lianmeng/refresh-lianmeng-paiju-time'),
				data : {id : <?php echo $lianmengId; ?>},
				beforeSend : function(){
					$(o).attr('disabled', 'disabled');
				},
				complete : function(){
					$(o).attr('disabled', false);
				},
				success : function(aResult){
					if(aResult.status == 1){
						location.reload();
					}else{
						UBox.show(aResult.msg, aResult.status);
					}
				}
			});
	}
	$(function(){
		isHostLianmengPage = true;
		$('.J-c-h-t-menu-m3').addClass('active');
		initItemPage();
		$('.J-lianmeng-selector').val(<?php echo $lianmengId; ?>);
		$('.J-lianmeng-selector').on('change', function(){
			location.href = Tools.url('home', 'host-lianmeng/lianmeng-host-duizhang') + '?id=' + $(this).val();
		});
		$('.body-list-wrap .row-item').each(function(){
			var colLength = parseInt($(this).find('.col-item').length);
			$(this).width(colLength * 132 + 20);
		});
		$('.body-list-wrap .col-item .ci-edit').click(function(){
			$(this).prev().focus();
		});
		$('.J-club-qin-zhang').click(function(){
			if(confirm('清理账单后，已添加的各俱乐部的新账将加到旧账中，同时清理显示的牌局。')){
				var o = this;
				ajax({
					url : Tools.url('home', 'host-lianmeng/lianmeng-club-qin-zhang'),
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
							}, 1);
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
				if($(o).attr('data-id') == 0){
					return;
				}
				ajax({
					url : Tools.url('home', 'host-lianmeng/update-lianmeng-club-info'),
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
							}, 1);
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
		
		
		/*$('.row-item.lbb').each(function(){
			var total = 0;
			$(this).find('.J-item-page').each(function(){
				total += parseFloat($(this).attr('data-float-zhang-dang'));
			});
			$(this).find('.col-item:first').attr('title', $(this).find('.col-item:first').attr('title') + '单局之和：' + total);
		});*/
	});
</script>