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
			<div class="h-text-bg h-l-div"><b>联盟盈利：</b><?php echo isset($aLianmengHostDuizhang['totalZhanDan']) ? $aLianmengHostDuizhang['totalZhanDan'] : 0; ?></div>
			<div class="h-text-bg h-l-div"><b>牌局总数：</b><?php echo isset($aLianmengHostDuizhang['totalPaijuCount']) ? $aLianmengHostDuizhang['totalPaijuCount'] : 0; ?></div>
			<div class="h-text-bg h-l-div"><b>误差非0牌局总数：</b><?php echo isset($aLianmengHostDuizhang['totalHeduishuziPaijuCount']) ? $aLianmengHostDuizhang['totalHeduishuziPaijuCount'] : 0; ?></div>
			<div class="h-text-bg h-l-div"><b>牌局误差值总和：</b><?php echo isset($aLianmengHostDuizhang['totalHeduishuzi']) ? $aLianmengHostDuizhang['totalHeduishuzi'] : 0; ?></div>
		</div>
		<div class="h-right">
			<div class="J-club-qin-zhang btn btn-primary" style="float:right;margin-right:45px;margin-top: 8px;">清账</div>
			<div class="btn btn-primary" style="float:right;margin-right:10px;margin-top: 8px;" onclick="AlertWin.showLianmengClubSetting(<?php echo $lianmengId; ?>);">联盟设置</div>
			<div class="btn btn-primary" style="float:right;margin-right:10px;margin-top: 8px;" onclick="AlertWin.showLianmengLmzjPaijuCreater(<?php echo $lianmengId; ?>, '<?php echo $aCurrentLianmeng ? $aCurrentLianmeng['lmzj_paiju_creater'] : ''; ?>');">开桌人设置</div>
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
				<div class="col-item <?php echo 'J-item-page item-page-' . $itemPage; ?>" style="cursor:pointer;" onclick='AlertWin.showClubZhangDanDetail(<?php echo $lianmengId; ?>, <?php echo json_encode($aClubZhangDan['club_zhang_dan_list']); ?>, "<?php echo $aClubZhangDan['club_name']; ?>");'>
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
					<span style="float: left; display: block; position: relative; line-height: 15px; width: 100%; font-size: 12px;">核对数值：<font style="color:<?php echo $aPaijuZhangDan['hedui_shuzi'] ? '#ff0000;' : '#00ff00' ?>"><?php echo $aPaijuZhangDan['hedui_shuzi']; ?></font></span>
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