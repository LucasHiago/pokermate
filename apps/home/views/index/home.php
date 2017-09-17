<?php
use umeworld\lib\Url;
$this->setTitle('结账台');
?>
<div class="c-body-wrap">
	<div class="c-b-list">
		<div class="c-b-list-wrap">
		<?php foreach($aLastPaijuList as $aPaiju){ ?>
			<div class="c-b-list-item <?php echo !$aPaiju['status'] ? 'new' : ''; ?>">
				<div class="c-b-l-i-title" style="cursor:pointer;" onclick="AlertWin.showPaijuDataList(<?php echo $aPaiju['id']; ?>, 1);"><?php echo $aPaiju['paiju_name']; ?></div>
				<div class="c-b-l-i-bottom">
					<a class="l-text"><span>核对数字</span><span <?php echo $aPaiju['hedui_shuzi'] ? 'style="color:#ff0000;"' : ''; ?>><?php echo $aPaiju['hedui_shuzi']; ?></span></a>
					<?php if(!$aPaiju['status']){ ?>
					<a class="l-edit" onclick="AlertWin.showPaijuDataList(<?php echo $aPaiju['id']; ?>, 1);"></a>
					<?php }else{ ?>
					<a class="l-edit" style="background:none;cursor:default;"></a>
					<?php } ?>
					<?php if(!$aPaiju['status']){ ?>
					<a href="<?php echo Url::to('home', 'index/index'); ?>?paijuId=<?php echo $aPaiju['id']; ?>" class="l-status <?php echo $aPaiju['status'] == 1 ? 'l-clean' : ''; ?>"></a>
					<?php }else{ ?>
					<a class="l-status <?php echo $aPaiju['status'] == 1 ? 'l-clean' : ''; ?>"></a>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
		</div>
		<div class="c-b-list-arrow" onclick="AlertWin.showPaijuList();"></div>
	</div>
	<div class="c-b-content">
		<div class="c-b-c-left">
			<div class="c-b-c-left-wrap">
				<div class="c-b-c-l-tab">
					<a href="javascript:;" class="cbcl-tab-btn b1"></a>
					<a href="javascript:;" class="cbcl-tab-btn b2"></a>
					<a href="javascript:;" class="cbcl-tab-btn b3"></a>
					<a href="javascript:;" class="cbcl-tab-btn b4"></a>
					<a href="javascript:;" class="cbcl-tab-btn b5"></a>
				</div>
				<div class="c-b-c-l-tab-list">
				<?php foreach($aPaijuDataList as $aPaijuData){ ?>
					<div class="c-b-c-l-tab-list-item">
						<div class="c-b-c-l-tab-list-item-left">
							<div class="i-text" title="<?php echo $aPaijuData['player_name']; ?>"><?php echo $aPaijuData['player_name']; ?></div>
							<div class="i-text"><?php echo $aPaijuData['keren_benjin_info'] ? $aPaijuData['keren_benjin_info']['keren_bianhao'] : 0; ?></div>
							<div class="i-text"><?php echo $aPaijuData['keren_benjin_info'] ? $aPaijuData['keren_benjin_info']['benjin'] : 0; ?></div>
							<div class="i-text"><?php echo $aPaijuData['jiesuan_value']; ?></div>
							<div class="i-text"><?php echo $aPaijuData['new_benjin']; ?></div>
						</div>
						<div class="c-b-c-l-tab-list-item-right <?php echo $aPaijuData['status'] ? 'clean' : ''; ?>" <?php echo $aPaijuData['status'] ? '' : 'onclick="doJieShuanPaijuRecord(this, ' . $aPaijuData['id'] . ');"'; ?>></div>
					</div>
				<?php } ?>
				</div>
			</div>
			<div class="cbb-lm-wrap">
				<?php if($aCurrentPaiju){ ?>
				<a><?php echo $aCurrentPaiju['paiju_name']; ?>当前联盟：-选择联盟</a>
				<a>
					<select class="J-jieshuan-lianmeng-select" data-paiju-id="<?php echo $aCurrentPaiju['id']; ?>" style="border: 1px solid;height:25px;border-radius:5px;">
						<?php foreach($aLianmengList as $aLianmeng){ ?>
							<option value="<?php echo $aLianmeng['id']; ?>"><?php echo $aLianmeng['name']; ?></option>
						<?php } ?>
					</select>
				</a>
				<?php } ?>
			</div>
		</div>
		<div class="c-b-c-center">
			<a href="javascript:;" class="chaer"></a>
			<a href="javascript:;" class="J-imbalance-money ball <?php echo abs($aUnJiaoBanPaijuTotalStatistic['imbalanceMoney']) > 100 ? 'fu' : ''; ?>"><?php echo $aUnJiaoBanPaijuTotalStatistic['imbalanceMoney']; ?></a>
			<a href="javascript:;" class="lmzz" onclick="AlertWin.showLianmengZhongZhang();"></a>
			<a href="javascript:;" class="krxx" onclick="AlertWin.showPlayerList();"></a>
			<a href="javascript:;" class="lspj" onclick="AlertWin.showPaijuList({isHistory : 1});"></a>
			<a href="javascript:;" class="jbzc" onclick="AlertWin.showJiaoBanZhuanChu();"></a>
			<a href="javascript:;" class="J-jiao-ban-zhuan-chu-money ball"><?php echo $aUnJiaoBanPaijuTotalStatistic['jiaoBanZhuanChuMoney']; ?></a>
		</div>
		<div class="c-b-c-right">
			<div class="c-b-c-r-head">
				<div class="J-h-zcs h-zcs" onclick="AlertWin.showChouShuiList();"><?php echo $aUnJiaoBanPaijuTotalStatistic['zhongChouShui']; ?></div>
				<div class="J-h-zbx h-zbx" onclick="AlertWin.showZhongBaoXianList();"><?php echo $aUnJiaoBanPaijuTotalStatistic['zhongBaoXian']; ?></div>
				<div class="J-h-szrs h-szrs" onclick="AlertWin.showShanZhuoRenShuList();"><?php echo $aUnJiaoBanPaijuTotalStatistic['shangZhuoRenShu']; ?></div>
			</div>
			<div class="c-b-c-r-center">
				<a class="krbh"><input type="text" class="J-search-keren-bianhao" value="" /></a>
				<a class="bj"><input type="text" class="J-search-benjin " value="0" /></a>
				<a class="J-jsfs jsfs" data-value="<?php echo $aMoneyTypeList ? $aMoneyTypeList[0]['id'] : 0; ?>"><?php echo $aMoneyTypeList ? $aMoneyTypeList[0]['pay_type'] : ''; ?></a>
				<a class="jsjer"><input type="text" class="J-search-jsjer" value="0" /></a>
				<a class="J-submit-search-benjin sure"></a>
			</div>
			<div class="c-b-c-r-bottom">
				<div class="txt-wrap">
					<a class="momey1"><?php echo $moneyTypeTotalMoney; ?></a>
					<a class="momey2"><?php echo $moneyOutPutTypeTotalMoney; ?></a>
				</div>
				<div class="c-b-c-r-bottom-body">
					<div class="b-b-item-left">
						<div class="h30"></div>
						<div class="b-b-item-left-list">
						<?php foreach($aMoneyTypeList as $aMoneyType){ ?>
							<div class="b-b-i-l-i">
								<div class="type"><?php echo $aMoneyType['pay_type']; ?></div>
								<div class="in-text"><input type="text" value="<?php echo $aMoneyType['money']; ?>" data-id="<?php echo $aMoneyType['id']; ?>" /></div>
								<div class="edit-btn"></div>
							</div>
						<?php } ?>
						</div>
						<div class="op-btn" onclick='AlertWin.showMoneyTypeList(<?php echo json_encode($aMoneyTypeList); ?>);'>新增/删除</div>
					</div>
					<div class="b-b-item-center">
						<div class="h30"></div>
						<div class="b-b-item-center-list">
							<?php foreach($aMoneyOutPutTypeList as $aMoneyOutPutType){ ?>
							<div class="b-b-i-l-i">
								<div class="type"><?php echo $aMoneyOutPutType['out_put_type']; ?></div>
								<div class="in-text"><input type="text" value="<?php echo $aMoneyOutPutType['money']; ?>" data-id="<?php echo $aMoneyOutPutType['id']; ?>" /></div>
								<div class="edit-btn"></div>
							</div>
						<?php } ?>
						</div>
						<div class="op-btn" onclick='AlertWin.showMoneyOutPutTypeList(<?php echo json_encode($aMoneyOutPutTypeList); ?>);'>新增/删除</div>
					</div>
					<div class="b-b-item-right">
						<div class="h30"></div>
						<div class="b-r-stat-w">
							<div class="J-krzbj stat-txt krzbj"><?php echo $aUnJiaoBanPaijuTotalStatistic['kerenTotalBenjinMoney']; ?></div>
							<div class="J-krqk stat-txt krqk"><?php echo $aUnJiaoBanPaijuTotalStatistic['kerenTotalQianKuanMoney']; ?></div>
							<div class="J-krsy stat-txt krsy"><?php echo $aUnJiaoBanPaijuTotalStatistic['kerenTotalShuYin']; ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">	
	var aAgentList = <?php echo json_encode($aAgentList); ?>;
	<?php echo $this->render(Yii::getAlias('@r.js.paiju.list')); ?>
	<?php echo $this->render(Yii::getAlias('@r.js.keren.list')); ?>
	function initMoneyOutPutType(){
		$('.b-b-item-center-list').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
		
		function commitMoneyTypeChange(o){
			ajax({
				url : Tools.url('home', 'money-out-put-type/save'),
				data : {
					id : $(o).attr('data-id'),
					money : $(o).val()
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
		
		function initMoneyTypeListEvent(){
			$('.b-b-item-center-list').find('.edit-btn').on('click', function(){
				var oTxt = $(this).parent().find('input');
				var txt = oTxt.val();
				oTxt.val('');
				oTxt.focus();
				oTxt.val(txt);
			});
			
			$('.b-b-item-center-list').find('input').keyup(function(e){
				if(e.keyCode == 13){
					commitMoneyTypeChange(this);
				}
			});
		}
		/*$('.b-b-item-center input').bind('input propertychange', function() {  
			setInputInterval(this);
		}); */
		initMoneyTypeListEvent();
	}

	function initMoneyType(){
		function initJsfs(){
			$('.J-jsfs').on('click', function(){
				var html = '';
				html += '<div class="J-jsfs-select">';
				<?php foreach($aMoneyTypeList as $aMoneyType){ ?>
					html += '<div class="J-jsfs-select-item" data-value="<?php echo $aMoneyType['id']; ?>"><?php echo $aMoneyType['pay_type']; ?></div>';
				<?php } ?>
				html += '</div>';
				var oHtml = $(html);
				$('#pageWraper').append(oHtml);
				oHtml.css({top: $(this).offset().top + 33, left: $(this).offset().left});
				oHtml.find('.J-jsfs-select-item').on('click', function(){
					$('.J-jsfs').attr('data-value', $(this).attr('data-value'));
					$('.J-jsfs').text($(this).text());
					oHtml.remove();
				});
				$(document).on('click', function(e){
					if(!$(e.target).hasClass('J-jsfs')){
						oHtml.remove();
					}
				});
			});
		}
		
		function initMoneyTypeListScroll(){
			$('.b-b-item-left-list').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
		}
		
		function commitMoneyTypeChange(o){
			ajax({
				url : Tools.url('home', 'money-type/save'),
				data : {
					id : $(o).attr('data-id'),
					money : $(o).val()
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
		
		function initMoneyTypeListEvent(){
			$('.b-b-item-left-list').find('.edit-btn').on('click', function(){
				var oTxt = $(this).parent().find('input');
				var txt = oTxt.val();
				oTxt.val('');
				oTxt.focus();
				oTxt.val(txt);
			});
			
			$('.b-b-item-left-list').find('input').keyup(function(e){
				if(e.keyCode == 13){
					commitMoneyTypeChange(this);
				}
			});
		}
		/*$('.b-b-item-left input').bind('input propertychange', function() {  
			setInputInterval(this);
		}); */
		initJsfs();
		initMoneyTypeListScroll();
		initMoneyTypeListEvent();
	}

	function showAllPaijuList(o){
		ajax({
			url : Tools.url('home', 'index/get-paiju-list'),
			data : {},
			beforeSend : function(){
				$(o).attr('disabled', 'disabled');
			},
			complete : function(){
				$(o).attr('disabled', false);
			},
			success : function(aResult){
				if(aResult.status == 1){
					AlertWin.showPaijuList(aResult.data);
				}else{
					UBox.show(aResult.msg, aResult.status);
				}
			}
		});
	}
	
	function getKerenBenjin(o, kerenBianhao){
		ajax({
			url : Tools.url('home', 'index/get-keren-benjin') + '?r=' + Math.random(),
			data : {
				kerenBianhao : kerenBianhao
			},
			beforeSend : function(){
				//$(o).attr('disabled', 'disabled');
			},
			complete : function(){
				//$(o).attr('disabled', false);
			},
			success : function(aResult){
				if(aResult.status == 1){
					//$(o).val(aResult.data.keren_bianhao);
					$(o).parent().parent().find('.J-search-benjin').val(aResult.data.benjin);
				}
			}
		});
	}
	
	function updateBenjin(o){
		ajax({
			url : Tools.url('home', 'index/update-benjin'),
			data : {
				kerenBianhao : $('.J-search-keren-bianhao').val(),
				benjin : $(o).val()
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
	
	function initJiaoShouJinEr(){
		$('.J-search-keren-bianhao, .J-search-benjin, .J-search-jsjer').bind('input propertychange', function() {  
			setInputInterval(this);
		}); 
		$('.J-search-keren-bianhao').bind('input propertychange', function(){
			var o = this;
			setTimeout(function(){
				getKerenBenjin(o, $(o).val());
			}, 500);
		}); 
		$('.J-submit-search-benjin').on('click', function(){
			var o = this;
			ajax({
				url : Tools.url('home', 'index/jiaoshou-jiner'),
				data : {
					kerenBianhao : $('.J-search-keren-bianhao').val(),
					//benjin : benjin,
					payType : $('.J-jsfs').attr('data-value'),
					jsjer : $('.J-search-jsjer').val()
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
		});
		
		$('.J-search-benjin').keyup(function(e){
			if(e.keyCode == 13){
				updateBenjin(this);
			}
		});
	}
	
	function initPaijuDataList(){
		<?php if($currentPaijuLianmengId){ ?>
			$('.J-jieshuan-lianmeng-select').val(<?php echo $currentPaijuLianmengId; ?>);
		<?php } ?>
		$('.c-b-c-l-tab-list').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
		$('.J-jieshuan-lianmeng-select').on('change', function(){
			var o = this;
			ajax({
				url : Tools.url('home', 'paiju/chang-paiju-lianmeng'),
				data : {
					paijuId : $(o).attr('data-paiju-id'),
					lianmengId : $(o).val()
				},
				beforeSend : function(){
					$(o).attr('disabled', 'disabled');
				},
				complete : function(){
					$(o).attr('disabled', false);
				},
				success : function(aResult){
					UBox.show(aResult.msg, aResult.status);
				}
			});
		});
	}
	
	function doJieShuanPaijuRecord(o, id){
		ajax({
			url : Tools.url('home', 'import/do-jie-shuan'),
			data : {
				id : id
			},
			beforeSend : function(){
				$(o).attr('disabled', 'disabled');
			},
			complete : function(){
				$(o).attr('disabled', false);
			},
			success : function(aResult){
				if(aResult.status == 1){
					$(o).addClass('clean');
					$('.J-h-zcs').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.zhongChouShui);
					$('.J-h-zbx').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.zhongBaoXian);
					$('.J-h-szrs').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.shangZhuoRenShu);
					$('.J-imbalance-money').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.imbalanceMoney);
					$('.J-jiao-ban-zhuan-chu-money').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.jiaoBanZhuanChuMoney);
					$('.J-krzbj').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.kerenTotalBenjinMoney);
					$('.J-krqk').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.kerenTotalQianKuanMoney);
					$('.J-krsy').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.kerenTotalShuYin);
				}
				UBox.show(aResult.msg, aResult.status);
			}
		});
	}
	
	$(function(){
		$('.c-h-t-menu.m1').addClass('active');
		
		initPaijuDataList();
		initMoneyType();
		initJiaoShouJinEr();
		initMoneyOutPutType();
	});
</script>