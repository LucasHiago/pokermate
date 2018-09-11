<?php
use umeworld\lib\Url;
$this->setTitle('');
$this->registerJsFile('@r.js.paiju.list');
$this->registerJsFile('@r.js.keren.list');
$mUser = Yii::$app->user->getIdentity();
$imbCss = '';
$imbalanceMoney = 0;
$shijiChouShui = 0;
$zhongBaoXian = 0;
$shangZhuoRenShu = 0;
$jiaoBanZhuanChuMoney = 0;
$kerenTotalBenjinMoney = 0;
$zhengKerenTotalBenjinMoney = 0;
$kerenTotalQianKuanMoney = 0;
$kerenTotalShuYin = 0;
$moneyTypeTotalMoney = 0;
$moneyOutPutTypeTotalMoney = 0;
if($mUser->cache_data){
	$aCacheData = $mUser->cache_data;
	$imbalanceMoney = $aCacheData['aUnJiaoBanPaijuTotalStatistic']['imbalanceMoney'];
	if(abs($imbalanceMoney) > 1){
		$imbCss = 'fu';
	}
	$shijiChouShui = $aCacheData['aUnJiaoBanPaijuTotalStatistic']['shijiChouShui'];
	$zhongBaoXian = $aCacheData['aUnJiaoBanPaijuTotalStatistic']['zhongBaoXian'];
	$shangZhuoRenShu = $aCacheData['aUnJiaoBanPaijuTotalStatistic']['shangZhuoRenShu'];
	$jiaoBanZhuanChuMoney = $aCacheData['aUnJiaoBanPaijuTotalStatistic']['jiaoBanZhuanChuMoney'];
	$kerenTotalBenjinMoney = $aCacheData['aUnJiaoBanPaijuTotalStatistic']['kerenTotalBenjinMoney'];
	$zhengKerenTotalBenjinMoney = $aCacheData['aUnJiaoBanPaijuTotalStatistic']['zhengKerenTotalBenjinMoney'];
	$kerenTotalQianKuanMoney = $aCacheData['aUnJiaoBanPaijuTotalStatistic']['kerenTotalQianKuanMoney'];
	$kerenTotalShuYin = $aCacheData['aUnJiaoBanPaijuTotalStatistic']['kerenTotalShuYin'];
	$moneyTypeTotalMoney = $aCacheData['moneyTypeTotalMoney'];
	$moneyOutPutTypeTotalMoney = $aCacheData['moneyOutPutTypeTotalMoney'];
}
$is_show_dangjujieshuan = 0;
if(isset($mUser->user_setting['is_show_dangjujieshuan'])){
	$is_show_dangjujieshuan = $mUser->user_setting['is_show_dangjujieshuan'];
}
?>
<div class="c-body-wrap">
	<div class="c-b-list">
		<div class="c-b-list-wrap">
		<?php foreach($aLastPaijuList as $aPaiju){ ?>
			<div class="panel panel-<?php echo !$aPaiju['status'] ? 'yellow' : 'green'; ?> paiju-item">
				<div class="panel-heading">
					<h3 class="panel-title" onclick="AlertWin.showPaijuDataList(<?php echo $aPaiju['id']; ?>, 1);"><?php echo $aPaiju['paiju_name']; ?></h3>
				</div>
				<div class="panel-body">
					<div class="pj-cell"><span>账单误差</span><span <?php echo $aPaiju['hedui_shuzi'] ? 'style="color:#ff0000;"' : ''; ?>><?php echo $aPaiju['hedui_shuzi']; ?></span></div>
					<?php if(!$aPaiju['status']){ ?>
					<div class="pj-cell"><button class="btn btn-sm btn-default" onclick="AlertWin.showPaijuDataList(<?php echo $aPaiju['id']; ?>, 1);">修改</button></div>
					<?php }else{ ?>
					<div class="pj-cell"></div>
					<?php } ?>
					<?php if(!$aPaiju['status']){ ?>
					<div class="pj-cell"><a href="<?php echo Url::to('home', 'index/index'); ?>?paijuId=<?php echo $aPaiju['id']; ?>" class="btn btn-sm btn-default <?php echo  $aCurrentPaiju && $aCurrentPaiju['id'] == $aPaiju['id'] ? 'current' : ''; ?>">结算</a></div>
					<?php }else{ ?>
					<div class="pj-cell"><a class="btn btn-sm btn-default <?php echo  $aCurrentPaiju && $aCurrentPaiju['id'] == $aPaiju['id'] ? 'current' : ''; ?>">已结算</a></div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
		</div>
		<?php if(!$mUser->is_active){ ?>
		<div class="c-b-list-arrow" onclick="UBox.show('提示:您的账号还没开始启用！', -1);"><i class="fa fa-chevron-right" style="color: #1e8430;margin-left:4px;"></i></div>
		<?php }else{ ?>
		<div class="c-b-list-arrow" onclick="AlertWin.showPaijuList();"><i class="fa fa-chevron-right" style="color: #1e8430;margin-left:4px;"></i></div>
		<?php } ?>
	</div>
	<div class="c-b-content">
		<div class="c-b-c-left">
			<div class="panel panel-info">
				<div class="panel-heading" style="height:60px;">
					<h3 class="panel-title" style="line-height: 39px;">
						<?php if($aCurrentPaiju){ ?>
						<a style="float: left; font-size: 14px; width: 205px; display: inline-block;">结束时间：<?php echo date('Y-m-d H:i:s', $aCurrentPaiju['end_time']); ?>&nbsp;</a>
						<a style="font-size: 16px; width: 130px; display: inline-block; float: left; text-align: center;"><?php echo $aCurrentPaiju['paiju_name']; ?></a>
						<a>
							<select class="J-jieshuan-lianmeng-select form-control" data-paiju-id="<?php echo $aCurrentPaiju['id']; ?>" style="width: 120px; float: right; position: relative; top: 2px;">
								<?php foreach($aLianmengList as $aLianmeng){ ?>
									<option value="<?php echo $aLianmeng['id']; ?>"><?php echo $aLianmeng['name']; ?></option>
								<?php } ?>
							</select>
						</a>
						<a style="float:right;font-size:14px;">选择交接联盟:</a>
						<?php } ?>
					</h3>
				</div>
				<div class="c-b-c-left-wrap">
					<div class="table-responsive">
						<table class="J-jlbzd-list-table table table-hover table-striped">
						<tr><th>游戏名</th><th>客人编号</th><th>本金</th><th>结算</th><th>新本金</th><?php echo $is_show_dangjujieshuan ? '<th>当桌结算</th>' : ''; ?><th>操作</th></tr>
						<?php if(!$aPaijuDataList && $aCurrentPaiju){ ?>
							<tr style="background:#ffffff;"><td></td><td></td><td>空账单</td><td></td><td><?php echo $is_show_dangjujieshuan ? '<td></td>' : ''; ?></td><td><button class="btn btn-sm btn-warning" onclick="doJieShuanEmptyPaijuRecord(this, <?php echo $aCurrentPaiju['id']; ?>);" style="width:58px;">结算</button></td></tr>
						<?php } ?>
						<?php foreach($aPaijuDataList as $aPaijuData){ ?>
							<tr class="J-jieshuan-row" data-id="<?php echo $aPaijuData['id']; ?>" data-keren-bian-hao="<?php echo $aPaijuData['keren_benjin_info'] ? $aPaijuData['keren_benjin_info']['keren_bianhao'] : 0; ?>" data-status="<?php echo $aPaijuData['status']; ?>">
								<td data-type="player_name" title="<?php echo $aPaijuData['player_name']; ?>"><?php echo $aPaijuData['player_name']; ?></td>
								<?php if($aPaijuData['keren_benjin_info'] && $aPaijuData['keren_benjin_info']['is_auto_create']){ ?>
								<td data-type="keren_bianhao">（新）请编号</td>
								<?php }else{ ?>
								<td data-type="keren_bianhao"><?php echo $aPaijuData['keren_benjin_info'] ? $aPaijuData['keren_benjin_info']['keren_bianhao'] : 0; ?></td>
								<?php } ?>
								<td data-type="benjin"><?php echo $aPaijuData['keren_benjin_info'] ? $aPaijuData['keren_benjin_info']['benjin'] : 0; ?></td>
								<td data-type="jiesuan_value"><?php echo $aPaijuData['jiesuan_value']; ?></td>
								<td data-type="new_benjin"><?php echo $aPaijuData['new_benjin']; ?></td>
								<?php echo $is_show_dangjujieshuan ? '<td data-type="dangjujieshuan">' . ($aPaijuData['mairu'] + $aPaijuData['jiesuan_value']) . '</td>' : ''; ?>
								<td>
									<?php if($aPaijuData['status']){ ?>
										<button class="btn btn-sm btn-success" style="cursor:default;">已结算</button>
									<?php }else{ ?>
										<button class="btn btn-sm btn-warning" onclick="doJieShuanPaijuRecord(this, <?php echo $aPaijuData['id']; ?>);" style="width:58px;">结算</button>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="c-b-c-center">
			<a href="javascript:;" class="chaer btn btn-sm btn-info" style="cursor:default;">核算差额</a>
			<a href="javascript:;" class="J-imbalance-money btn btn-lg btn-success ball <?php echo $imbCss; ?>" style="cursor:default;"><?php echo $imbalanceMoney; ?></a>
			<?php if(!$mUser->is_active){ ?>
			<a href="javascript:;" class="lmzz btn btn-lg btn-primary" onclick="UBox.show('提示:您的账号还没开始启用！', -1);">联盟总账</a>
			<a href="javascript:;" class="krxx btn btn-lg btn-primary" onclick="UBox.show('提示:您的账号还没开始启用！', -1);">客人信息</a>
			<a href="javascript:;" class="lspj btn btn-lg btn-primary" onclick="UBox.show('提示:您的账号还没开始启用！', -1);">历史牌局</a>
			<?php }else{ ?>
			<a href="javascript:;" class="lmzz btn btn-lg btn-primary" onclick="AlertWin.showLianmengZhongZhang();">联盟总账</a>
			<a href="javascript:;" class="krxx btn btn-lg btn-primary" onclick="AlertWin.showPlayerList();">客人信息</a>
			<a href="javascript:;" class="lspj btn btn-lg btn-primary" onclick="AlertWin.showPaijuList({isHistory : 1});">历史牌局</a>
			<?php } ?>
			<a href="javascript:;" class="jbzc btn btn-sm btn-info" style="cursor:default;">当班收益</a>
			<a href="javascript:;" class="J-jiao-ban-zhuan-chu-money btn btn-lg btn-success ball" style="cursor:default;"><?php echo $jiaoBanZhuanChuMoney; ?></a>
		</div>
		<div class="c-b-c-right">
			<div class="c-b-c-r-head">
			<?php if(!$mUser->is_active){ ?>
				<div class="alert alert-danger" style="float: left;width:177px;margin-bottom:0px;cursor:pointer;" onclick="UBox.show('提示:您的账号还没开始启用！', -1);"><strong>总抽水：</strong><font class="J-h-zcs"><?php echo $shijiChouShui; ?></font></div>
				<div class="alert alert-danger" style="float: left;width:177px;margin-bottom:0px;margin-left:4px;cursor:pointer;" onclick="UBox.show('提示:您的账号还没开始启用！', -1);"><strong>总保险：</strong><font class="J-h-zbx"><?php echo $zhongBaoXian; ?></font></div>
				<div class="alert alert-danger" style="float: left;width:177px;margin-bottom:0px;margin-left:4px;cursor:pointer;" onclick="UBox.show('提示:您的账号还没开始启用！', -1);"><strong>上桌人数：</strong><font class="J-h-szrs"><?php echo $shangZhuoRenShu; ?></font></div>
			<?php }else{ ?>
				<div class="alert alert-danger" style="float: left;width:177px;margin-bottom:0px;cursor:pointer;" onclick="AlertWin.showChouShuiList();"><strong>总抽水：</strong><font class="J-h-zcs"><?php echo $shijiChouShui; ?></font></div>
				<div class="alert alert-danger" style="float: left;width:177px;margin-bottom:0px;margin-left:4px;cursor:pointer;" onclick="AlertWin.showZhongBaoXianList();"><strong>总保险：</strong><font class="J-h-zbx"><?php echo $zhongBaoXian; ?></font></div>
				<div class="alert alert-danger" style="float: left;width:177px;margin-bottom:0px;margin-left:4px;cursor:pointer;" onclick="AlertWin.showShanZhuoRenShuList();"><strong>上桌人数：</strong><font class="J-h-szrs"><?php echo $shangZhuoRenShu; ?></font></div>
			<?php } ?>
			</div>
			<div class="c-b-c-r-center">
				<div class="form-group" style="float:left;width:112px;margin:10px;margin-left:10px;margin-right:5px;">
					<label>搜索</label>
					<input type="text" class="J-search-val form-control" value="" placeholder="搜编号或名字" />
				</div>
				<div class="form-group" style="float:left;width:86px;margin:10px;margin-left:0px;margin-right:5px;">
					<label>客人编号</label>
					<input type="text" class="J-search-keren-bianhao form-control" value="" placeholder="客人编号" />
				</div>
				<div class="form-group" style="float:left;width:105px;margin:10px;margin-left:0px;margin-right:5px;">
					<label>游戏名字</label>
					<select class="J-search-player-list form-control"></select>
				</div>
				<div class="form-group" style="float:left;width:86px;margin:10px;margin-left:0px;margin-right:5px;">
					<label>本金</label>
					<input type="text" class="J-search-benjin form-control" value="0" placeholder="本金" />
					<i class="J-search-benjin-edit-btn fa fa-pencil" style="position:relative;float:right;top:-24px;right:5px;cursor:pointer;"></i>
				</div>
				<div class="form-group" style="float:left;width:105px;margin:10px;margin-left:0px;margin-right:0px;">
					<label>资金流向</label>
					<select class="J-jsfs form-control">
					<option value="0">请选择</option>
					<?php foreach($aMoneyTypeList as $aMoneyType){ ?>
						<option value="<?php echo $aMoneyType['id']; ?>"><?php echo $aMoneyType['pay_type']; ?></option>
					<?php } ?>
					</select>
				</div>
			</div>
			<div class="c-b-c-r-bottom">
				<div class="panel panel-info" style="float:left;width:180px;margin-right:4px;min-height: 400px;">
					<div class="panel-heading">
						<h3 class="panel-title"><strong>资金</strong>（<font class="J-money-type-total-money" style="color: #3c763d;"><?php echo $moneyTypeTotalMoney; ?></font>）</h3>
					</div>
					<div class="panel-body">
					<?php foreach($aMoneyTypeList as $aMoneyType){ ?>
						<div class="form-group" style="margin-bottom:5px;">
							<label style="margin-bottom:2px;"><?php echo $aMoneyType['pay_type']; ?></label>
							<input type="text" class="J-money-type-item-input form-control" data-id="<?php echo $aMoneyType['id']; ?>" value="<?php echo $aMoneyType['money']; ?>" style="height:25px;" placeholder="请输入金额" />
							<i class="fa fa-pencil" style="position: relative;float: right;top: -25px;right: 2px;cursor: pointer;height: 25px;line-height: 25px;"></i>
						</div>
					<?php } ?>
					<?php if(!$mUser->is_active){ ?>
					<button class="btn btn-sm btn-primary" onclick="UBox.show('提示:您的账号还没开始启用！', -1);" style="margin-top: 10px;">新增/删除</button>
					<?php }else{ ?>
					<button class="btn btn-sm btn-primary" onclick='AlertWin.showMoneyTypeList(<?php echo json_encode($aMoneyTypeList); ?>);' style="margin-top: 10px;">新增/删除</button>
					<?php } ?>
					</div>
				</div>
				<div class="panel panel-info" style="float:left;width:180px;margin-right:4px;min-height: 400px;">
					<div class="panel-heading">
						<h3 class="panel-title"><strong>支出</strong>（<font class="J-money-out-put-type-total-money" style="color: #3c763d;"><?php echo $moneyOutPutTypeTotalMoney; ?></font>）</h3>
					</div>
					<div class="panel-body">
						<?php foreach($aMoneyOutPutTypeList as $aMoneyType){ ?>
						<div class="form-group" style="margin-bottom:5px;">
							<label style="margin-bottom:2px;"><?php echo $aMoneyType['out_put_type']; ?></label>
							<input type="text" class="J-money-out-put-type-item-input form-control" data-id="<?php echo $aMoneyType['id']; ?>" value="<?php echo $aMoneyType['money']; ?>" style="height:25px;" placeholder="请输入金额" />
							<i class="fa fa-pencil" style="position: relative;float: right;top: -25px;right: 2px;cursor: pointer;height: 25px;line-height: 25px;"></i>
						</div>
					<?php } ?>
					<?php if(!$mUser->is_active){ ?>
					<button class="btn btn-sm btn-primary" onclick="UBox.show('提示:您的账号还没开始启用！', -1);" style="margin-top: 10px;">新增/删除</button>
					<?php }else{ ?>
					<button class="btn btn-sm btn-primary" onclick='AlertWin.showMoneyOutPutTypeList(<?php echo json_encode($aMoneyOutPutTypeList); ?>);' style="margin-top: 10px;">新增/删除</button>
					<?php } ?>
					</div>
				</div>
				<div class="panel panel-info" style="float:left;width:172px;min-height: 400px;">
					<div class="panel-heading">
						<h3 class="panel-title"><strong>俱乐部信息</strong></h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label>客人总本金</label>
							<div class="J-krzbj" style="height:30px;line-height:30px;background:#f5f5f5;border:1px solid #ddd;border-radius:15px;text-align: right;padding-right: 20px;"><?php echo $kerenTotalBenjinMoney; ?></div>
						</div>
						<div class="form-group">
							<label>客人正本金</label>
							<div class="J-zkrzbj" style="height:30px;line-height:30px;background:#f5f5f5;border:1px solid #ddd;border-radius:15px;text-align: right;padding-right: 20px;"><?php echo $zhengKerenTotalBenjinMoney; ?></div>
						</div>
						<div class="form-group">
							<label>客人欠款</label>
							<div class="J-krqk " style="height:30px;line-height:30px;background:#f5f5f5;border:1px solid #ddd;border-radius:15px;text-align: right;padding-right: 20px;"><?php echo $kerenTotalQianKuanMoney; ?></div>
						</div>
						<div class="form-group">
							<label>客人输赢</label>
							<div class="J-krsy" style="height:30px;line-height:30px;background:#f5f5f5;border:1px solid #ddd;border-radius:15px;text-align: right;padding-right: 20px;"><?php echo $kerenTotalShuYin; ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">	
	var aAgentList = <?php echo json_encode($aAgentList); ?>;
	
	function initMoneyOutPutType(){
		function commitMoneyOutPutTypeChange(o){
			<?php if(!$mUser->is_active){ ?>
				UBox.show('提示:您的账号还没开始启用！', -1);
				return;
			<?php } ?>
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
						showImbalanceMoney(aResult.data.imbalanceMoney);
						refreshUnJiaoBanPaijuTotalStatistic();
						UBox.show(aResult.msg, aResult.status);
						/*UBox.show(aResult.msg, aResult.status, function(){
							location.reload();
						}, 1);*/
					}else{
						UBox.show(aResult.msg, aResult.status);
					}
				}
			});
		}
		
		function initMoneyTypeListEvent(){
			$('.J-money-out-put-type-item-input').next().on('click', function(){
				<?php if(!$mUser->is_active){ ?>
					UBox.show('提示:您的账号还没开始启用！', -1);
					return;
				<?php } ?>
				var oTxt = $(this).prev();
				var txt = oTxt.val();
				//oTxt.val('');
				//oTxt.focus();
				//oTxt.val(txt);
				AlertWin.showMoneyOutPutTypeWin(oTxt.attr('data-id'), $(this).prev().prev().text());
			});
			
			$('.J-money-out-put-type-item-input').keyup(function(e){
				if(e.keyCode == 13){
					commitMoneyOutPutTypeChange(this);
				}
			});
			$('.J-money-out-put-type-item-input').blur(function(e){
				commitMoneyOutPutTypeChange(this);
			});
		}
		/*$('.b-b-item-center input').bind('input propertychange', function() {  
			setInputInterval(this);
		}); */
		initMoneyTypeListEvent();
	}

	function initMoneyType(){
		function initJsfs(){
			$('.J-jsfs').on('change', function(){
				<?php if(!$mUser->is_active){ ?>
					UBox.show('提示:您的账号还没开始启用！', -1);
					return;
				<?php } ?>
				var kerenBianhao = $('.J-search-keren-bianhao').val();
				var moneyTypeId = $(this).val();
				var moneyType = $(this).find("option:selected").text();
				if(moneyTypeId == 0){
					return;
				}
				if(kerenBianhao == ''){
					$(this).val(0);
					UBox.show('请输入客人编号', -1);
					return;
				}
				AlertWin.showJiaoShouWin(kerenBianhao, moneyTypeId, moneyType);
			});
			
		}
		
		function commitMoneyTypeChange(o){
			<?php if(!$mUser->is_active){ ?>
				UBox.show('提示:您的账号还没开始启用！', -1);
				return;
			<?php } ?>
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
						showImbalanceMoney(aResult.data.imbalanceMoney);
						refreshUnJiaoBanPaijuTotalStatistic();
						UBox.show(aResult.msg, aResult.status);
						/*UBox.show(aResult.msg, aResult.status, function(){
							location.reload();
						}, 1);*/
					}else{
						UBox.show(aResult.msg, aResult.status);
					}
				}
			});
		}
		
		function initMoneyTypeListEvent(){
			$('.J-money-type-item-input').next().on('click', function(){
				<?php if(!$mUser->is_active){ ?>
					UBox.show('提示:您的账号还没开始启用！', -1);
					return;
				<?php } ?>
				var oTxt = $(this).prev();
				var txt = oTxt.val();
				//oTxt.val('');
				//oTxt.focus();
				//oTxt.val(txt);
				AlertWin.showMoneyTypeWin(oTxt.attr('data-id'), $(this).prev().prev().text());
			});
			
			$('.J-money-type-item-input').keyup(function(e){
				if(e.keyCode == 13){
					commitMoneyTypeChange(this);
				}
			});
			$('.J-money-type-item-input').blur(function(e){
				commitMoneyTypeChange(this);
			});
		}
		/*$('.b-b-item-left input').bind('input propertychange', function() {  
			setInputInterval(this);
		}); */
		initJsfs();
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
	
	function selectSearchItem(o, kerenBianhao){
		$('.J-search-val').val($(o).text());
		$('.J-search-keren-bianhao').val(kerenBianhao);
		getKerenBenjin($('.J-search-keren-bianhao')[0], kerenBianhao);
	}
	
	function showSearchList(o, aList){
		$('.J-float-search-list-wrap').remove();
		var html = '<div class="J-float-search-list-wrap list-group" style="position:absolute;min-width: 112px;">';
		for(var i in aList){
			var aTemp = aList[i];
			html += '<a href="javascript:;" class="list-group-item" data-kerenbianhao="' + aTemp.keren_bianhao + '" onclick="selectSearchItem(this, ' + aTemp.keren_bianhao + ');">' + (typeof(aTemp.player_name) != 'undefined' ? aTemp.player_name : aTemp.keren_bianhao) + '</a>';
		}
		html += '</div>';
		
		var oHtml = $(html);
		$('#pageWraper').append(oHtml);
		oHtml.css({top : $(o).offset().top + 33, left : $(o).offset().left});
	}
	
	function searchKerenBenjin(o, searchValue){
		<?php if(!$mUser->is_active){ ?>
			UBox.show('提示:您的账号还没开始启用！', -1);
			return;
		<?php } ?>
		ajax({
			url : Tools.url('home', 'index/search-keren-benjin') + '?r=' + Math.random(),
			data : {
				searchValue : searchValue
			},
			beforeSend : function(){
				//$(o).attr('disabled', 'disabled');
			},
			complete : function(){
				//$(o).attr('disabled', false);
			},
			success : function(aResult){
				if(aResult.status == 1){
					showSearchList(o, aResult.data);
				}
			}
		});
	}
	
	function getKerenBenjin(o, kerenBianhao){
		<?php if(!$mUser->is_active){ ?>
			UBox.show('提示:您的账号还没开始启用！', -1);
			return;
		<?php } ?>
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
					var html = '';
					for(i in aResult.data.player_list){
						html += '<option value="' + aResult.data.player_list[i].id + '">' + aResult.data.player_list[i].player_name + '</option>';
					}
					$(o).parent().parent().find('.J-search-player-list').html(html);
				}
			}
		});
	}
	
	function updateBenjin(o){
		<?php if(!$mUser->is_active){ ?>
			UBox.show('提示:您的账号还没开始启用！', -1);
			return;
		<?php } ?>
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
					showImbalanceMoney(aResult.data.imbalanceMoney);
					refreshUnJiaoBanPaijuTotalStatistic();
					$('.J-search-keren-bianhao').val('');
					$('.J-search-benjin').val('');
					UBox.show(aResult.msg, aResult.status);
					/*UBox.show(aResult.msg, aResult.status, function(){
						location.reload();
					}, 1);*/
				}else{
					UBox.show(aResult.msg, aResult.status);
				}
			}
		});
	}
	
	function initJiaoShouJinEr(){
		var tts = '';
		$('.J-search-val').bind('input propertychange', function(){
			var o = this;
			clearTimeout(tts);
			tts = setTimeout(function(){
				searchKerenBenjin(o, $(o).val());
			}, 500);
		}); 
		$('.J-search-val').blur(function(){
			setTimeout(function(){
				if($('.J-float-search-list-wrap .list-group-item').length == 1){
					if($('.J-float-search-list-wrap .list-group-item').attr('data-kerenbianhao') == $('.J-search-val').val()){
						$('.J-float-search-list-wrap .list-group-item').click();
					}
				}
				$('.J-float-search-list-wrap').remove();
			}, 300);
		}); 	
		var tt = '';
		$('.J-search-keren-bianhao').bind('input propertychange', function(){
			var o = this;
			clearTimeout(tt);
			tt = setTimeout(function(){
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
						showImbalanceMoney(aResult.data.imbalanceMoney);
						refreshUnJiaoBanPaijuTotalStatistic();
						UBox.show(aResult.msg, aResult.status);
						/*UBox.show(aResult.msg, aResult.status, function(){
							location.reload();
						}, 1);*/
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
		$('.J-search-benjin').blur(function(e){
			updateBenjin(this);
		});
		$('.J-search-benjin-edit-btn').click(function(){
			var oTxt = $(this).prev();
			var txt = oTxt.val();
			oTxt.val('');
			oTxt.focus();
			oTxt.val(txt);
		});
	}
	
	function initPaijuDataList(){
		<?php if($currentPaijuLianmengId){ ?>
			$('.J-jieshuan-lianmeng-select').val(<?php echo $currentPaijuLianmengId; ?>);
		<?php } ?>
		//$('.c-b-c-l-tab-list').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
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
					$(o).removeAttr('onclick');
					$(o).removeClass('btn-warning');
					$(o).addClass('btn-success');
					$(o).text('已结算');
					$(o).parent().parent().attr('data-status', 1);
					ajustUnJieShuanRecordValue($(o).parent().parent().attr('data-keren-bian-hao'), $(o).parent().parent().find('td[data-type=new_benjin]').text());
					/*$('.J-h-zcs').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.shijiChouShui);
					$('.J-h-zbx').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.zhongBaoXian);
					$('.J-h-szrs').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.shangZhuoRenShu);
					$('.J-imbalance-money').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.imbalanceMoney);
					$('.J-jiao-ban-zhuan-chu-money').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.jiaoBanZhuanChuMoney);
					$('.J-krzbj').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.kerenTotalBenjinMoney);
					$('.J-krqk').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.kerenTotalQianKuanMoney);
					$('.J-krsy').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.kerenTotalShuYin);*/
					
					if(aResult.data.isReloadPage == 1){
						refreshUnJiaoBanPaijuTotalStatistic();
						UBox.show(aResult.msg, aResult.status, function(){
							location.href = Tools.url('home', 'index/index');
						}, 2);
						return;
					}else{
						refreshUnJiaoBanPaijuTotalStatistic();
					}
				}
				UBox.show(aResult.msg, aResult.status);
			}
		});
	}
	
	function ajustUnJieShuanRecordValue(kerenBianHao, benjin){
		benjin = parseInt(benjin);
		$('.J-jieshuan-row').each(function(){
			if($(this).attr('data-status') == 0 && kerenBianHao == $(this).attr('data-keren-bian-hao')){
				var jiesuanValue = parseInt($(this).find('td[data-type=jiesuan_value]').text());
				$(this).find('td[data-type=benjin]').text(benjin);
				$(this).find('td[data-type=new_benjin]').text(benjin + jiesuanValue);
			}
		});
	}
	
	function doJieShuanEmptyPaijuRecord(o, id){
		ajax({
			url : Tools.url('home', 'import/do-jie-shuan-empty-paiju'),
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
					$(o).removeAttr('onclick');
					$(o).removeClass('btn-warning');
					$(o).addClass('btn-success');
					$(o).text('已结算');
					/*$('.J-h-zcs').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.shijiChouShui);
					$('.J-h-zbx').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.zhongBaoXian);
					$('.J-h-szrs').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.shangZhuoRenShu);
					$('.J-imbalance-money').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.imbalanceMoney);
					$('.J-jiao-ban-zhuan-chu-money').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.jiaoBanZhuanChuMoney);
					$('.J-krzbj').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.kerenTotalBenjinMoney);
					$('.J-krqk').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.kerenTotalQianKuanMoney);
					$('.J-krsy').text(aResult.data.aUnJiaoBanPaijuTotalStatistic.kerenTotalShuYin);*/
					
					if(aResult.data.isReloadPage == 1){
						refreshUnJiaoBanPaijuTotalStatistic();
						UBox.show(aResult.msg, aResult.status, function(){
							location.href = Tools.url('home', 'index/index');
						}, 2);
						return;
					}else{
						refreshUnJiaoBanPaijuTotalStatistic();
					}
				}
				UBox.show(aResult.msg, aResult.status);
			}
		});
	}
	
	function refreshUnJiaoBanPaijuTotalStatistic(){
		ajax({
			url : Tools.url('home', 'user/get-un-jiao-ban-paiju-total-statistic'),
			data : {},
			beforeSend : function(){
				//$(o).attr('disabled', 'disabled');
			},
			complete : function(){
				//$(o).attr('disabled', false);
			},
			success : function(aResult){
				if(aResult.status == 1){
					updatePageStatisticData(aResult.data);
				}
			}
		});
	}
	
	function updatePageStatisticData(aCacheData){
		$('.J-h-zcs').text(aCacheData.aUnJiaoBanPaijuTotalStatistic.shijiChouShui);
		$('.J-h-zbx').text(aCacheData.aUnJiaoBanPaijuTotalStatistic.zhongBaoXian);
		$('.J-h-szrs').text(aCacheData.aUnJiaoBanPaijuTotalStatistic.shangZhuoRenShu);
		showImbalanceMoney(aCacheData.aUnJiaoBanPaijuTotalStatistic.imbalanceMoney);
		$('.J-jiao-ban-zhuan-chu-money').text(aCacheData.aUnJiaoBanPaijuTotalStatistic.jiaoBanZhuanChuMoney);
		$('.J-krzbj').text(aCacheData.aUnJiaoBanPaijuTotalStatistic.kerenTotalBenjinMoney);
		$('.J-zkrzbj').text(aCacheData.aUnJiaoBanPaijuTotalStatistic.zhengKerenTotalBenjinMoney);
		$('.J-krqk').text(aCacheData.aUnJiaoBanPaijuTotalStatistic.kerenTotalQianKuanMoney);
		$('.J-krsy').text(aCacheData.aUnJiaoBanPaijuTotalStatistic.kerenTotalShuYin);
		$('.J-money-out-put-type-total-money').text(aCacheData.moneyOutPutTypeTotalMoney);
		$('.J-money-type-total-money').text(aCacheData.moneyTypeTotalMoney);
		var aMoneyTypeList = aCacheData.aMoneyTypeList;
		var aMoneyOutPutTypeList = aCacheData.aMoneyOutPutTypeList;
		for(var i in aMoneyTypeList){
			$('.J-money-type-item-input[data-id=' + aMoneyTypeList[i].id + ']').val(aMoneyTypeList[i].money);
		}
		for(var j in aMoneyOutPutTypeList){
			$('.J-money-out-put-type-item-input[data-id=' + aMoneyOutPutTypeList[j].id + ']').val(aMoneyOutPutTypeList[j].money);
		}
	}
	
	$(function(){
		$('.J-c-h-t-menu-m1').addClass('active');
		
		initPaijuDataList();
		initMoneyType();
		initJiaoShouJinEr();
		initMoneyOutPutType();
		<?php if($mUser->cache_data){ ?>
			var aCacheData = <?php echo json_encode($mUser->cache_data); ?>;
			updatePageStatisticData(aCacheData);
		<?php } ?>
		refreshUnJiaoBanPaijuTotalStatistic();
	});
</script>