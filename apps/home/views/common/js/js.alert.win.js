(function(container, $){
	container.AlertWin = {
		showFillSavecode : function(oo, clubId){
			var aData = {};
			var html = '';
			html += '<div class="J-save-code-win">';
				html += '<img style="float: left; position: relative; top: 63px; border-radius: 5px; left: 150px; width: 120px; height: 48px;border:none;" />';
				html += '<input type="text" class="save-code" style="text-align: center; border-radius: 5px;float: left; position: relative; top: 63px; left: 160px;background:#181326; width: 120px; height: 48px; line-height: 48px; color: #ffffff; font-size: 20px;" />';
				html += '<a class="commit-save-code"></a>';
				html += '<div class="J-select-time"><input type="text" class="st" onclick="WdatePicker({dateFmt:\'yyyy-MM-dd\'});" /><span style="float: left; width: 28px; text-align: center;">至</span><input type="text" class="et" onclick="WdatePicker({dateFmt:\'yyyy-MM-dd\'});" /></div>';
				html += '<div class="J-wait-tip" style="background: #1f1f2f; float: left; position: relative;text-align:center; line-height: 100px; height: 120px; width: 400px; top: -4px; left: 125px;color:#f4e2a9;display:none;">正在获取牌局，请稍等...(获取一天牌局大概12分钟左右^ω^)</div>';
			html += '</div>';
			
			var oHtml = $(html);
			
			function _doImportPaiju(o, aData){
				ajax({
					url : Tools.url('home', 'import/do-import-paiju'),
					data : aData,
					beforeSend : function(){
						$(o).attr('disabled', 'disabled');
					},
					complete : function(){
						$(o).attr('disabled', false);
					},
					success : function(aResult){
						isCanClose = true;
						if(aResult.status == 1){
							$(document).click();
							UBox.show(aResult.msg, aResult.status, function(){
								//location.reload();
							}, 3);
						}else if(aResult.status == 2){
							if(confirm(aResult.msg)){
								aData.retry = 1;
								_doImportPaiju(o, aData);
							}else{
								location.reload();
							}
						}else if(aResult.status == 3){
							$(document).click();
							$(oo).click();
							UBox.show(aResult.msg, aResult.status);
						}else{
							oHtml.find('.J-wait-tip').hide();
							UBox.show(aResult.msg, aResult.status);
						}
					}
				});
			}
			oHtml.find('.commit-save-code').click(function(){
				var o = this;
				var safecode = $(o).prev().val();
				var startTime = $(o).parent().find('.st').val();
				var endTime = $(o).parent().find('.et').val();
				var exponent = '010001';
				var modulus = '008bec657d62f3a746ed28377c0749393d7d7dec2b68835dc7e23bc45551e800174d60bc1bebea362a4206799cd5f7e829118735085afbe684235ac1daea34cf181166f0b9c86e4ccc68bfb18d0b2a52743fe32726c3a388da9c4fa1cb7a9ef17faab6d4e107df24415acf48ab0fb97e5b9104c3222698d5d6707294805216de81';
				var skey = RSAUtils.encryptedString(RSAUtils.getKeyPair(exponent, '', modulus), "name=" + aData.club_login_name + "&pwd=" + hex_md5(aData.club_login_password));
				if($(o).prev().val().length != 4){
					UBox.show('验证码不正确', -1);
					return;
				}
				aData.safecode = safecode;
				aData.skey = skey;
				oHtml.find('.J-wait-tip').show();
				isCanClose = false;
				_doImportPaiju(o, {
					clubId : clubId,
					safecode : aData.safecode,
					skey : aData.skey,
					startTime : startTime,
					endTime : endTime
				});
			});
			
			showAlertWin(oHtml, function(){
				ajax({
					url : Tools.url('home', 'import/get-download-save-code'),
					data : {clubId : clubId},
					success : function(aResult){
						if(aResult.status == 1){
							aData = aResult.data;
							oHtml.find('img').attr('src', App.url.resource + aResult.data.path + '?r=' + Math.random());
							oHtml.find('.st').val(aResult.data.start_time);
							oHtml.find('.et').val(aResult.data.end_time);
							oHtml.find('.save-code').focus();
						}else{
							UBox.show(aResult.msg, aResult.status);
						}
					}
				});
			});
		},
		
		showClubZhangDanDetail : function(lianmengId, aData, clubName){
			var html = '';
			html += '<div class="J-lianmeng-zhongzhang-win J-lianmeng-setting-win">';
				html += '<div class="h100">';
					html += '<div class="h50" style="text-align: center; line-height: 50px; color: #e91e63; font-size: 18px; font-weight: bold;">' + (clubName ? clubName : '俱乐部') + '账单详情</div>';
					html += '<div class="h30">';
						html += '<div style="float:left;width:300px;height:100%;"></div>';
						html += '<div style="float:right;width:300px;height:100%;"><div class="s-lms-btn">联盟设置</div><div class="s-lms-txt">新账单累计: <font class="J-total-zhan-dan" style="color:#f4e2a9;">0</font> 元</div></div>';
					html += '</div>';
				html += '</div>';
				html += '<div class="h50">';
					html += '<table class="ls-th">';
						html += '<tr>';
							html += '<td style="font-weight:bold;color: #fccdaa;">牌局名</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">战绩</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">保险</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">桌子费</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">保险被抽</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">当局账单</td>';
						html += '</tr>';
					html += '</table>';
				html += '</div>';
				html += '<div class="ls-list-wrap" style="height:260px;"></div>';
				
			html += '</div>';
			var oHtml = $(html);
			
			function appendLianmengItemHtml(aDataList){
				var listHtml = '';
				var totalZhanDan = 0;
				for(var i in aDataList){
					var aData = aDataList[i];
					listHtml += '<table class="ls-th">';
						listHtml += '<tr>';
							listHtml += '<td>' + aData.paiju_name + '</td>';
							listHtml += '<td>' + aData.zhanji + '</td>';
							listHtml += '<td>' + aData.baoxian_heji + '</td>';
							listHtml += '<td>' + aData.paiju_fee + '</td>';
							listHtml += '<td>' + aData.baoxian_beichou + '</td>';
							listHtml += '<td>' + aData.zhang_dan + '</td>';
						listHtml += '</tr>';
					listHtml += '</table>';
					listHtml += '<div class="h10"></div>';
					totalZhanDan += parseInt(aData.zhang_dan);
				}
				var oListHtml = $(listHtml);
				oHtml.find('.ls-list-wrap').append(oListHtml);
				oHtml.find('.J-total-zhan-dan').text(totalZhanDan);
				bindLianmengEvent(oHtml);
				
				return oListHtml;
			}
			
			function bindLianmengEvent(oHtml){
				
			}
			
			showAlertWin(oHtml, function(){
				appendLianmengItemHtml(aData);
				oHtml.find('.s-lms-btn').click(function(){
					$(document).click();
					AlertWin.showLianmengClubSetting(lianmengId);
				});
				oHtml.find('.ls-list-wrap').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
			});	
		},
		
		showLianmengClubSetting : function(lianmengId){
			var html = '';
			html += '<div class="J-data-list-win J-lianmeng-setting-win">';
				html += '<div class="h100">';
					html += '<div class="h50" style="text-align: center; line-height: 50px; color: #e91e63; font-size: 18px; font-weight: bold;">联盟名称</div>';
					html += '<div class="h30"></div>';
				html += '</div>';
				html += '<div class="h50">';
					html += '<table class="ls-th">';
						html += '<tr>';
							html += '<td style="font-weight:bold;color: #fccdaa;">俱乐部</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">俱乐部ID</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">对账方法</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">桌子费</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">保险被抽成</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">联盟</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">操作</td>';
						html += '</tr>';
					html += '</table>';
				html += '</div>';
				html += '<div class="ls-list-wrap"></div>';
				html += '<div class="h10"></div>';
				html += '<div class="ls-add-wrap">';
					html += '<table class="ls-th">';
						html += '<tr>';
							html += '<td><input type="text" data-type="club_name" /></td>';
							html += '<td><input type="text" data-type="club_id" value="" /></td>';
							html += '<td><select class="ls-t-select" data-type="duizhangfangfa"><option value="1">0.975</option><option value="2">无水账单</option></select></td>';
							html += '<td><input type="text" data-type="paiju_fee" value="0" /></td>';
							html += '<td><input type="text" data-type="baoxian_choucheng" value="0" style="float:left;width:62px;text-align:right;" /><span class="i-edit" style="background:none;">%</span></td>';
							html += '<td class="J-lianmeng-name">&nbsp;</td>';
							html += '<td style="background:none;"><div class="la-add-btn" style="width:70px;"></div></td>';
						html += '</tr>';
					html += '</table>';
				html += '</div>';
			html += '</div>';
			var oHtml = $(html);
			
			function commitLianmengClubChange(o , aData){
				ajax({
					url : Tools.url('home', 'lianmeng/update-lianmeng-club-info'),
					data : aData,
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
			}
			
			function bindLianmengEvent(oHtml){
				oHtml.find('.i-edit').click(function(){
					$(this).prev().focus();
				});
				oHtml.find('.J-commit-input').keyup(function(e){
					if(e.keyCode == 13){
						commitLianmengClubChange(this, {
							id : $(this).attr('data-id'),
							type : $(this).attr('data-type'),
							value : $(this).val()
						});
					}
				});
				oHtml.find('.ls-t-select').change(function(){
					commitLianmengClubChange(this, {
						id : $(this).attr('data-id'),
						type : $(this).attr('data-type'),
						value : $(this).val()
					});
				});
				oHtml.find('.la-delete-btn').click(function(){
					var o = this;
					if(confirm('确定删除？')){
						ajax({
							url : Tools.url('home', 'lianmeng/delete-club'),
							data : {id : $(o).attr('data-id')},
							beforeSend : function(){
								$(o).attr('disabled', 'disabled');
							},
							complete : function(){
								$(o).attr('disabled', false);
							},
							success : function(aResult){
								if(aResult.status == 1){
									reloadList();
								}else{
									UBox.show(aResult.msg, aResult.status);
								}
							}
						});
					}
				});
			}
			
			function appendLianmengClubItemHtml(aDataList){
				var listHtml = '';
				for(var i in aDataList){
					var aData = aDataList[i];
					listHtml += '<table class="ls-th">';
						listHtml += '<tr>';
							listHtml += '<td><input type="text" class="J-commit-input" data-id="' + aData.id + '" data-type="club_name" value="' + aData.club_name + '" /></td>';
							listHtml += '<td><input type="text" class="J-commit-input" data-id="' + aData.id + '" data-type="club_id" value="' + aData.club_id + '" /></td>';
							var opitonHtml = '';
							if(aData.duizhangfangfa == 1){
								opitonHtml = '<option value="1">0.975</option><option value="2">无水账单</option>';
							}else{
								opitonHtml = '<option value="2">无水账单</option><option value="1">0.975</option>';
							}
							listHtml += '<td><select class="J-commit-input ls-t-select" data-id="' + aData.id + '" data-type="duizhangfangfa">' + opitonHtml + '</select></td>';
							listHtml += '<td><input type="text" class="J-commit-input" data-id="' + aData.id + '" data-type="paiju_fee" value="' + aData.paiju_fee + '" /></td>';
							listHtml += '<td><input type="text" class="J-commit-input" data-id="' + aData.id + '" data-type="baoxian_choucheng" value="' + aData.baoxian_choucheng + '" style="float:left;width:62px;text-align:right;" /><span class="i-edit">%</span></td>';
							listHtml += '<td>' + aData.lianmeng_name + '</td>';
							listHtml += '<td style="background:none;"><div class="la-delete-btn" data-id="' + aData.id + '"></div></td>';
						listHtml += '</tr>';
					listHtml += '</table>';
					listHtml += '<div class="h10"></div>';
				}
				var oListHtml = $(listHtml);
				oHtml.find('.ls-list-wrap').append(oListHtml);
				bindLianmengEvent(oListHtml);
				return oListHtml;
			}
			
			function _loadLianmengClubList(){
				ajax({
					url : Tools.url('home', 'lianmeng/get-club-list'),
					data : {id : lianmengId},
					beforeSend : function(){
						//$(o).attr('disabled', 'disabled');
					},
					complete : function(){
						//$(o).attr('disabled', false);
					},
					success : function(aResult){
						if(aResult.status == 1){
							oHtml.find('.ls-list-wrap').html('');
							if(aResult.data.length != 0){
								appendLianmengClubItemHtml(aResult.data.list);
								oHtml.find('.J-lianmeng-name').text(aResult.data.aLianmeng.name);
								oHtml.find('.ls-list-wrap').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
							}
						}
					}
				});
			}
			
			function reloadList(){
				$(document).click();
				AlertWin.showLianmengClubSetting(lianmengId);
			}
			
			showAlertWin(oHtml, function(){
				bindLianmengEvent(oHtml);
				_loadLianmengClubList();
				oHtml.find('.la-add-btn').click(function(){
					var o = this;
					ajax({
						url : Tools.url('home', 'lianmeng/add-lianmeng-club'),
						data : {
							id : lianmengId,
							clubName : $(o).parent().parent().find('input[data-type=club_name]').val(),
							clubId : $(o).parent().parent().find('input[data-type=club_id]').val(),
							duizhangfangfa : $(o).parent().parent().find('select[data-type=duizhangfangfa]').val(),
							paijuFee : $(o).parent().parent().find('input[data-type=paiju_fee]').val(),
							baoxianChoucheng : $(o).parent().parent().find('input[data-type=baoxian_choucheng]').val()
						},
						beforeSend : function(){
							$(o).attr('disabled', 'disabled');
						},
						complete : function(){
							$(o).attr('disabled', false);
						},
						success : function(aResult){
							if(aResult.status == 1){
								reloadList();
							}
							UBox.show(aResult.msg, aResult.status);
						}
					});
				});
			});	
		},

		showJiaoBanZhuanChu : function(){
			var html = '';
			html += '<div class="J-data-list-win J-lianmeng-setting-win">';
				html += '<div class="h100">';
					html += '<div class="h50" style="text-align: center; line-height: 50px; color: #e91e63; font-size: 18px; font-weight: bold;">交班转出</div>';
					html += '<div class="h30"></div>';
				html += '</div>';
				html += '<div class="h50">';
					html += '<table class="ls-th">';
						html += '<tr>';
							html += '<td style="font-weight:bold;color: #fccdaa;">总抽水</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">总保险</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">总支出</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">交接金额</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">转出渠道</td>';
						html += '</tr>';
					html += '</table>';
				html += '</div>';
				html += '<div class="ls-list-wrap" style="height:260px;">';
					html += '<table class="ls-th">';
						html += '<tr>';
							html += '<td class="J-zhong-chou-shui">0</td>';
							html += '<td class="J-zhong-bao-xian">0</td>';
							html += '<td class="J-total-out-put-type-money">0</td>';
							html += '<td class="J-jiao-ban-zhuan-chu-money">0</td>';
							html += '<td><select class="J-zhuan-chu-qidao" style="color:#ff6a6a;"></select></td>';
						html += '</tr>';
					html += '</table>';
				html += '</div>';
				html += '<div class="J-jbzc-sure-btn jbzc-sure-btn" data-imbalance-money="0"></div>';
			html += '</div>';
			var oHtml = $(html);
			
			function _loadList(){
				ajax({
					url : Tools.url('home', 'user/get-jiao-ban-zhuan-chu-detail'),
					data : {},
					beforeSend : function(){
						//$(o).attr('disabled', 'disabled');
					},
					complete : function(){
						//$(o).attr('disabled', false);
					},
					success : function(aResult){
						if(aResult.status == 1){
							oHtml.find('.J-zhong-chou-shui').text(aResult.data.aJiaoBanZhuanChuDetail.zhongChouShui);
							oHtml.find('.J-zhong-bao-xian').text(aResult.data.aJiaoBanZhuanChuDetail.zhongBaoXian);
							oHtml.find('.J-total-out-put-type-money').text(aResult.data.aJiaoBanZhuanChuDetail.totalOutPutTypeMoney);
							oHtml.find('.J-jiao-ban-zhuan-chu-money').text(aResult.data.aJiaoBanZhuanChuDetail.jiaoBanZhuanChuMoney);
							var selectHtml = '';
							for(var i in aResult.data.aMoneyTypeList){
								selectHtml += '<option value="' + aResult.data.aMoneyTypeList[i].id + '">' + aResult.data.aMoneyTypeList[i].pay_type + '</option>';
							}
							oHtml.find('.J-zhuan-chu-qidao').html(selectHtml);
							oHtml.find('.J-jbzc-sure-btn').attr('data-imbalance-money', aResult.data.imbalanceMoney);
						}else{
							UBox.show(aResult.msg, aResult.status);
						}
					}
				});
			}
			
			showAlertWin(oHtml, function(){
				_loadList();
				oHtml.find('.J-jbzc-sure-btn').click(function(){
					var o = this;
					var tipText = '';
					/*var imbalanceMoney = parseInt($(o).attr('data-imbalance-money'));
					if($(o).attr('data-imbalance-money') != 0){
						var zhuanChuMoney = 0;
						var jiaobanZhuanChuMoney = parseInt($(o).parent().find('.J-jiao-ban-zhuan-chu-money').text());
						var absImbalanceMoney = parseInt(Math.abs(imbalanceMoney));
						if(jiaobanZhuanChuMoney > 0){
							if(imbalanceMoney > 0){
								zhuanChuMoney += absImbalanceMoney;
							}else{
								zhuanChuMoney -= absImbalanceMoney;
							}
						}else{
							if(imbalanceMoney > 0){
								zhuanChuMoney -= absImbalanceMoney;
							}else{
								zhuanChuMoney += absImbalanceMoney;
							}
						}
						if(zhuanChuMoney != 0){
							if(zhuanChuMoney > 0){
								tipText = '交班后你需要转出' + zhuanChuMoney + '金额，你的差额才会为0！';
							}else{
								tipText = '交班后你需要转入' + zhuanChuMoney + '金额，你的差额才会为0！';
							}
						}
					}*/
					if(confirm('确定交班转出？交班后已结算账单将会变为已清算!' + tipText)){
						ajax({
							url : Tools.url('home', 'user/do-jiao-ban-zhuan-chu'),
							data : {moneyTypeId : $(o).parent().find('.J-zhuan-chu-qidao').val()},
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
			});	
		},
		
		showShanZhuoRenShuList : function(){
			var html = '';
			html += '<div class="J-data-list-win J-lianmeng-setting-win">';
				html += '<div class="h100">';
					html += '<div class="h50" style="text-align: center; line-height: 50px; color: #e91e63; font-size: 18px; font-weight: bold;">上桌人数</div>';
					html += '<div class="h30">';
						html += '<div style="float:left;width:300px;height:100%;"></div>';
						html += '<div style="float:right;width:340px;height:100%;"><div class="s-lms-txt">上桌人数: <font class="J-total-shan-zhuo-ren-shu" style="color:#f4e2a9;">0</font> </div></div>';
					html += '</div>';
				html += '</div>';
				html += '<div class="h50">';
					html += '<table class="ls-th">';
						html += '<tr>';
							html += '<td style="font-weight:bold;color: #fccdaa;">牌局名</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">上桌人数</td>';
						html += '</tr>';
					html += '</table>';
				html += '</div>';
				html += '<div class="ls-list-wrap" style="height:260px;"></div>';
				
			html += '</div>';
			var oHtml = $(html);
			
			function appendLianmengItemHtml(aDataList){
				var listHtml = '';
				for(var i in aDataList){
					var aData = aDataList[i];
					listHtml += '<table class="ls-th">';
						listHtml += '<tr>';
							listHtml += '<td>' + aData.paiju_name + '</td>';
							listHtml += '<td>' + aData.shang_zhuo_ren_shu + '</td>';
						listHtml += '</tr>';
					listHtml += '</table>';
					listHtml += '<div class="h10"></div>';
				}
				var oListHtml = $(listHtml);
				oHtml.find('.ls-list-wrap').append(oListHtml);
				
				return oListHtml;
			}
			
			function _loadList(){
				ajax({
					url : Tools.url('home', 'user/get-shang-zhuo-ren-shu-list'),
					data : {},
					beforeSend : function(){
						//$(o).attr('disabled', 'disabled');
					},
					complete : function(){
						//$(o).attr('disabled', false);
					},
					success : function(aResult){
						if(aResult.status == 1){
							oHtml.find('.ls-list-wrap').html('');
							if(aResult.data.length != 0){
								appendLianmengItemHtml(aResult.data.list);
								oHtml.find('.J-total-shan-zhuo-ren-shu').text(aResult.data.totalShangZhuoRenShu);
								oHtml.find('.ls-list-wrap').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
							}
						}
					}
				});
			}
			
			showAlertWin(oHtml, function(){
				_loadList();
			});	
		},
		
		showZhongBaoXianList : function(){
			var html = '';
			html += '<div class="J-data-list-win J-lianmeng-setting-win">';
				html += '<div class="h100">';
					html += '<div class="h50" style="text-align: center; line-height: 50px; color: #e91e63; font-size: 18px; font-weight: bold;">总保险</div>';
					html += '<div class="h30">';
						html += '<div style="float:left;width:300px;height:100%;">';
							html += '<div style="float:left;margin-left:20px;width:70px;height:30px;line-height:30px;color:#ffffff;">保险微调：</div>';
							html += '<input type="text" class="J-baoxian-ajust-value" style="float:left;width:70px;height:18px;line-height:18px;color:#ffffff;text-align: center;color: #f4e2a9;background:#58463d;border-radius: 5px;margin-top: 6px;" value="0" />';
						html += '</div>';
						html += '<div style="float:right;width:340px;height:100%;"><div class="s-lms-txt">总保险: <font class="J-total-baoxian" style="color:#f4e2a9;">0</font> 元</div></div>';
					html += '</div>';
				html += '</div>';
				html += '<div class="h50">';
					html += '<table class="ls-th">';
						html += '<tr>';
							html += '<td style="font-weight:bold;color: #fccdaa;">牌局名</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">牌桌保险</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">保险被抽</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">实际保险</td>';
						html += '</tr>';
					html += '</table>';
				html += '</div>';
				html += '<div class="ls-list-wrap" style="height:260px;"></div>';
				
			html += '</div>';
			var oHtml = $(html);
			
			function appendLianmengItemHtml(aDataList){
				var listHtml = '';
				for(var i in aDataList){
					var aData = aDataList[i];
					listHtml += '<table class="ls-th">';
						listHtml += '<tr>';
							listHtml += '<td>' + aData.paiju_name + '</td>';
							listHtml += '<td>' + aData.baoxian_heji + '</td>';
							listHtml += '<td>' + aData.baoxian_beichou + '</td>';
							listHtml += '<td>' + aData.shiji_baoxian + '</td>';
						listHtml += '</tr>';
					listHtml += '</table>';
					listHtml += '<div class="h10"></div>';
				}
				var oListHtml = $(listHtml);
				oHtml.find('.ls-list-wrap').append(oListHtml);
				
				return oListHtml;
			}
			
			function _loadList(){
				ajax({
					url : Tools.url('home', 'user/get-bao-xian-list'),
					data : {},
					beforeSend : function(){
						//$(o).attr('disabled', 'disabled');
					},
					complete : function(){
						//$(o).attr('disabled', false);
					},
					success : function(aResult){
						if(aResult.status == 1){
							oHtml.find('.ls-list-wrap').html('');
							if(aResult.data.length != 0){
								appendLianmengItemHtml(aResult.data.list);
								oHtml.find('.J-total-baoxian').text(aResult.data.totalBaoXian);
								oHtml.find('.J-baoxian-ajust-value').val(aResult.data.baoxianAjustValue);
								oHtml.find('.ls-list-wrap').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
							}
						}
					}
				});
			}
			
			function reloadList(){
				$(document).click();
				AlertWin.showZhongBaoXianList();
			}
			
			showAlertWin(oHtml, function(){
				_loadList();
				oHtml.find('.J-baoxian-ajust-value').keyup(function(e){
					var o = this;
					if(e.keyCode == 13){
						ajax({
							url : Tools.url('home', 'user/update-user-info'),
							data : {
								type : 'baoxian_ajust_value',
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
									reloadList();
								}
								UBox.show(aResult.msg, aResult.status);
							}
						});
					}
				});
			});	
		},
		
		showChouShuiList : function(){
			var html = '';
			html += '<div class="J-data-list-win J-lianmeng-setting-win">';
				html += '<div class="h100">';
					html += '<div class="h50" style="text-align: center; line-height: 50px; color: #e91e63; font-size: 18px; font-weight: bold;">抽水列表</div>';
					html += '<div class="h30">';
						html += '<div style="float:left;width:300px;height:100%;">';
							html += '<div style="float:left;margin-left:20px;width:70px;height:30px;line-height:30px;color:#ffffff;">抽水微调：</div>';
							html += '<input type="text" class="J-choushui-ajust-value" style="float:left;width:70px;height:18px;line-height:18px;color:#ffffff;text-align: center;color: #f4e2a9;background:#58463d;border-radius: 5px;margin-top: 6px;" value="0" />';
						html += '</div>';
						html += '<div style="float:right;width:340px;height:100%;"><div class="s-lms-txt">总抽水: <font class="J-total-choushui" style="color:#f4e2a9;">0</font> 元</div><div class="s-lms-txt">牌局总数: <font class="J-total-paiju" style="color:#f4e2a9;">0</font> </div></div>';
					html += '</div>';
				html += '</div>';
				html += '<div class="h50">';
					html += '<table class="ls-th">';
						html += '<tr>';
							html += '<td style="font-weight:bold;color: #fccdaa;">牌局名</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">战绩</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">抽水</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">桌子费</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">联盟补贴</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">实际抽水</td>';
						html += '</tr>';
					html += '</table>';
				html += '</div>';
				html += '<div class="ls-list-wrap" style="height:260px;"></div>';
				
			html += '</div>';
			var oHtml = $(html);
			
			function appendLianmengItemHtml(aDataList){
				var listHtml = '';
				for(var i in aDataList){
					var aData = aDataList[i];
					listHtml += '<table class="ls-th">';
						listHtml += '<tr>';
							listHtml += '<td>' + aData.paiju_name + '</td>';
							listHtml += '<td>' + aData.zhanji + '</td>';
							listHtml += '<td>' + aData.choushui_value + '</td>';
							listHtml += '<td>' + aData.paiju_fee + '</td>';
							listHtml += '<td>' + aData.lianmeng_butie + '</td>';
							listHtml += '<td>' + aData.shiji_choushui_value + '</td>';
						listHtml += '</tr>';
					listHtml += '</table>';
					listHtml += '<div class="h10"></div>';
				}
				var oListHtml = $(listHtml);
				oHtml.find('.ls-list-wrap').append(oListHtml);
				
				return oListHtml;
			}
			
			function reloadList(){
				$(document).click();
				AlertWin.showChouShuiList();
			}
			
			function _loadList(){
				ajax({
					url : Tools.url('home', 'user/get-chou-shui-list'),
					data : {},
					beforeSend : function(){
						//$(o).attr('disabled', 'disabled');
					},
					complete : function(){
						//$(o).attr('disabled', false);
					},
					success : function(aResult){
						if(aResult.status == 1){
							oHtml.find('.ls-list-wrap').html('');
							if(aResult.data.length != 0){
								appendLianmengItemHtml(aResult.data.list);
								oHtml.find('.J-total-choushui').text(aResult.data.totalChouShui);
								oHtml.find('.J-total-paiju').text(aResult.data.count);
								oHtml.find('.J-choushui-ajust-value').val(aResult.data.choushuiAjustValue);
								oHtml.find('.ls-list-wrap').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
							}
						}
					}
				});
			}
			
			showAlertWin(oHtml, function(){
				_loadList();
				oHtml.find('.J-choushui-ajust-value').keyup(function(e){
					var o = this;
					if(e.keyCode == 13){
						ajax({
							url : Tools.url('home', 'user/update-user-info'),
							data : {
								type : 'choushui_ajust_value',
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
									reloadList();
								}
								UBox.show(aResult.msg, aResult.status);
							}
						});
					}
				});
			});	
		},
		
		showLianmengZhangDanDetail : function(lianmengId){
			var html = '';
			html += '<div class="J-lianmeng-zhongzhang-win J-lianmeng-setting-win">';
				html += '<div class="h100">';
					html += '<div class="h50" style="text-align: center; line-height: 50px; color: #e91e63; font-size: 18px; font-weight: bold;">联盟账单详情</div>';
					html += '<div class="h30">';
						html += '<div style="float:left;width:300px;height:100%;"><div class="lml-select-wrap"><select class="J-lml-select lml-select" style="color:#ff6a6a;width:100%;height:100%;"></select></div></div>';
						html += '<div style="float:right;width:300px;height:100%;"><div class="s-lms-btn" style="top:-2px;">联盟设置</div><div class="s-lms-txt">新账单累计: <font class="J-total-zhan-dan" style="color:#f4e2a9;">0</font> 元</div></div>';
					html += '</div>';
				html += '</div>';
				html += '<div class="h50">';
					html += '<table class="ls-th">';
						html += '<tr>';
							html += '<td style="font-weight:bold;color: #fccdaa;">牌局名</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">战绩</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">保险</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">桌子费</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">保险被抽</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">当局账单</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">更改联盟</td>';
						html += '</tr>';
					html += '</table>';
				html += '</div>';
				html += '<div class="ls-list-wrap" style="height:260px;"></div>';
				
			html += '</div>';
			var oHtml = $(html);
			
			function appendLianmengItemHtml(lianmengId, aDataList, aLianmengList){
				var listHtml = '';
				var lianmengSelectHtml = '';
				for(var j in aLianmengList){
					lianmengSelectHtml += '<option value="' + aLianmengList[j].id + '">' + aLianmengList[j].name + '</option>';
				}
				for(var i in aDataList){
					var aData = aDataList[i];
					listHtml += '<table class="ls-th">';
						listHtml += '<tr>';
							listHtml += '<td>' + aData.paiju_name + '</td>';
							listHtml += '<td>' + aData.zhanji + '</td>';
							listHtml += '<td>' + aData.baoxian_heji + '</td>';
							listHtml += '<td>' + aData.paiju_fee + '</td>';
							listHtml += '<td>' + aData.baoxian_beichou + '</td>';
							listHtml += '<td>' + aData.zhang_dan + '</td>';
							listHtml += '<td><select class="lml-item-select" data-paiju-id="' + aData.paiju_id + '" data-lianmeng-id="' + aData.lianmeng_id + '" style="color:#ff6a6a;">' + lianmengSelectHtml + '</select></td>';
						listHtml += '</tr>';
					listHtml += '</table>';
					listHtml += '<div class="h10"></div>';
				}
				var oListHtml = $(listHtml);
				oHtml.find('.ls-list-wrap').append(oListHtml);
				oHtml.find('.J-lml-select').html(lianmengSelectHtml);
				oHtml.find('.J-lml-select').val(lianmengId);
				oHtml.find('.ls-list-wrap select').each(function(){
					$(this).val($(this).attr('data-lianmeng-id'));
				});
				bindLianmengEvent(oHtml);
				
				return oListHtml;
			}
			
			function bindLianmengEvent(oHtml){
				oHtml.find('.ls-list-wrap select').unbind();
				oHtml.find('.ls-list-wrap select').on('change', function(){
					var o = this;
					ajax({
						url : Tools.url('home', 'paiju/chang-paiju-lianmeng'),
						data : {
							paijuId : $(o).attr('data-paiju-id'),
							lianmengId : $(o).val(),
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
				oHtml.find('.J-lml-select').unbind();
				oHtml.find('.J-lml-select').on('change', function(){
					oHtml.find('.ls-list-wrap').html('');
					_loadList($(this).val(), this);
				});
			}
			var oScrollBar = {};
			function _loadList(id, o){
				ajax({
					url : Tools.url('home', 'lianmeng/get-lianmeng-zhang-dan-detail-list'),
					data : {id : id},
					beforeSend : function(){
						if(o){
							$(o).attr('disabled', 'disabled');
						}
					},
					complete : function(){
						if(o){
							$(o).attr('disabled', false);
						}
					},
					success : function(aResult){
						if(aResult.status == 1){
							oHtml.find('.ls-list-wrap').html('');
							if(aResult.data.length != 0){
								appendLianmengItemHtml(id, aResult.data.list, aResult.data.aLianmengList);
								oHtml.find('.J-total-zhan-dan').text(aResult.data.totalZhangDan);
								oScrollBar.update('relative');
							}
						}
					}
				});
			}
			
			function reloadList(){
				$(document).click();
				AlertWin.showLianmengZhongZhang();
			}
			
			showAlertWin(oHtml, function(){
				oHtml.find('.s-lms-btn').click(function(){
					$(document).click();
					AlertWin.showLianmengSetting();
				});
				oScrollBar = oHtml.find('.ls-list-wrap').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
				_loadList(lianmengId);
				
			});	
		},
		
		showLianmengZhongZhang : function(){
			var html = '';
			html += '<div class="J-lianmeng-zhongzhang-win J-lianmeng-setting-win">';
				html += '<div class="h100">';
					html += '<div class="h50" style="text-align: center; line-height: 50px; color: #e91e63; font-size: 18px; font-weight: bold;">联盟总账</div>';
					html += '<div class="h30">';
						html += '<div style="float:left;width:300px;height:100%;">';
							html += '<div style="float:left;margin-left:20px;width:100px;height:30px;line-height:30px;color:#ffffff;">联盟总账微调：</div>';
							html += '<input type="text" class="J-lianmeng-zhongzhang-ajust-value" style="float:left;width:70px;height:18px;line-height:18px;color:#ffffff;text-align: center;color: #f4e2a9;background:#58463d;border-radius: 5px;margin-top: 6px;" value="0" />';
						html += '</div>';
						html += '<div style="float:right;width:300px;height:100%;"><div style="line-height:30px;color:#ffffff;padding-left:30px;">所有联盟总账：<font class="J-total-zhong-zhang" style="color:#f4e2a9;">0</font></div><div class="s-lms-btn">联盟设置</div></div>';
					html += '</div>';
				html += '</div>';
				html += '<div class="h50">';
					html += '<table class="ls-th">';
						html += '<tr>';
							html += '<td style="font-weight:bold;color: #fccdaa;">联盟名称</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">联盟总账单</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">上桌人数</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">联盟旧账</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">新账单累计</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">操作</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">清账</td>';
						html += '</tr>';
					html += '</table>';
				html += '</div>';
				html += '<div class="ls-list-wrap" style="height:260px;"></div>';
				
			html += '</div>';
			var oHtml = $(html);
			
			function appendLianmengItemHtml(aDataList){
				var listHtml = '';
				for(var i in aDataList){
					var aData = aDataList[i];
					listHtml += '<table class="ls-th">';
						listHtml += '<tr>';
							listHtml += '<td>' + aData.lianmeng_name + '</td>';
							listHtml += '<td>' + aData.lianmeng_zhong_zhang + '</td>';
							listHtml += '<td>' + aData.lianmeng_shang_zhuo_ren_shu + '</td>';
							listHtml += '<td><input type="text" data-id="' + aData.lianmeng_id + '" data-type="qian_zhang" value="' + aData.lianmeng_qian_zhang + '" style="float:left;width:62px;text-align:right;" /><span class="i-edit"></span></td>';
							listHtml += '<td>' + aData.lianmeng_zhang_dan + '</td>';
							listHtml += '<td class="J-detail-btn op-btn detail-btn" data-id="' + aData.lianmeng_id + '">账单详情</td>';
							listHtml += '<td class="J-qin-zhang op-btn clear-btn" data-id="' + aData.lianmeng_id + '">清账</td>';
						listHtml += '</tr>';
					listHtml += '</table>';
					listHtml += '<div class="h10"></div>';
				}
				var oListHtml = $(listHtml);
				oHtml.find('.ls-list-wrap').html(oListHtml);
				
				bindLianmengEvent(oHtml);
				
				return oListHtml;
			}
			
			function bindLianmengEvent(oHtml){
				oHtml.find('.i-edit').click(function(){
					$(this).prev().focus();
				});
				oHtml.find('.detail-btn').click(function(){
					$(document).click();
					AlertWin.showLianmengZhangDanDetail($(this).attr('data-id'));
				});
				oHtml.find('.ls-list-wrap .J-qin-zhang').click(function(){
					if(confirm('确定清账？')){
						var o = this;
						ajax({
							url : Tools.url('home', 'lianmeng/qin-zhang'),
							data : {
								id : $(o).attr('data-id')
							},
							beforeSend : function(){
								$(o).attr('disabled', 'disabled');
							},
							complete : function(){
								$(o).attr('disabled', false);
							},
							success : function(aResult){
								if(aResult.status == 1){
									reloadList();
								}
								UBox.show(aResult.msg, aResult.status);
							}
						});
					}
				});
				oHtml.find('.ls-list-wrap input[data-type=qian_zhang]').keyup(function(e){
					var o = this;
					if(e.keyCode == 13){
						ajax({
							url : Tools.url('home', 'lianmeng/update-lianmeng-info'),
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
			}
			
			function _loadLianmengList(){
				ajax({
					url : Tools.url('home', 'lianmeng/get-lianmeng-zhong-zhang-list'),
					data : {},
					beforeSend : function(){
						//$(o).attr('disabled', 'disabled');
					},
					complete : function(){
						//$(o).attr('disabled', false);
					},
					success : function(aResult){
						if(aResult.status == 1){
							oHtml.find('.ls-list-wrap').html('');
							if(aResult.data.length != 0){
								appendLianmengItemHtml(aResult.data.list);
								oHtml.find('.J-total-zhong-zhang').text(aResult.data.totalZhongZhang);
								oHtml.find('.J-lianmeng-zhongzhang-ajust-value').val(aResult.data.lianmengZhongzhangAjustValue);
								oHtml.find('.ls-list-wrap').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
							}
						}
					}
				});
			}
			
			function reloadList(){
				$(document).click();
				AlertWin.showLianmengZhongZhang();
			}
			
			showAlertWin(oHtml, function(){
				oHtml.find('.s-lms-btn').click(function(){
					$(document).click();
					AlertWin.showLianmengSetting();
				});
				_loadLianmengList();
				oHtml.find('.J-lianmeng-zhongzhang-ajust-value').keyup(function(e){
					var o = this;
					if(e.keyCode == 13){
						ajax({
							url : Tools.url('home', 'user/update-user-info'),
							data : {
								type : 'lianmeng_zhongzhang_ajust_value',
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
									reloadList();
								}
								UBox.show(aResult.msg, aResult.status);
							}
						});
					}
				});
			});	
		},
		
		showLianmengSetting : function(){
			var html = '';
			html += '<div class="J-lianmeng-setting-win">';
				html += '<div class="h100"></div>';
				html += '<div class="h50">';
					html += '<table class="ls-th">';
						html += '<tr>';
							html += '<td style="font-weight:bold;color: #fccdaa;">联盟名称</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">联盟欠账</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">对账方法</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">上缴桌费/桌</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">保险被抽成</td>';
							html += '<td style="font-weight:bold;color: #fccdaa;">操作</td>';
						html += '</tr>';
					html += '</table>';
				html += '</div>';
				html += '<div class="ls-list-wrap"></div>';
				html += '<div class="h10"></div>';
				html += '<div class="ls-add-wrap">';
					html += '<table class="ls-th">';
						html += '<tr>';
							html += '<td><input type="text" data-type="name" /></td>';
							html += '<td><input type="text" data-type="qianzhang" value="0" /></td>';
							//html += '<td><div class="t-type">r</div><a class="i-select"></a></td>';
							html += '<td><select class="ls-t-select" data-type="duizhangfangfa"><option value="1">0.975</option><option value="2">无水账单</option></select></td>';
							html += '<td><input type="text" data-type="paiju_fee" value="0" /></td>';
							html += '<td><input type="text" data-type="baoxian_choucheng" value="0" style="float:left;width:62px;text-align:right;" /><span class="i-edit" style="background:none;">%</span></td>';
							html += '<td style="background:none;"><div class="la-add-btn"></div></td>';
						html += '</tr>';
					html += '</table>';
				html += '</div>';
			html += '</div>';
			var oHtml = $(html);
			
			function commitLianmengChange(o , aData){
				ajax({
					url : Tools.url('home', 'lianmeng/update-lianmeng-info'),
					data : aData,
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
			}
			
			function bindLianmengEvent(oHtml){
				oHtml.find('.i-edit').click(function(){
					$(this).prev().focus();
				});
				oHtml.find('.J-commit-input').keyup(function(e){
					if(e.keyCode == 13){
						commitLianmengChange(this, {
							id : $(this).attr('data-id'),
							type : $(this).attr('data-type'),
							value : $(this).val()
						});
					}
				});
				oHtml.find('.ls-t-select').change(function(){
					commitLianmengChange(this, {
						id : $(this).attr('data-id'),
						type : $(this).attr('data-type'),
						value : $(this).val()
					});
				});
				oHtml.find('.la-delete-btn').click(function(){
					var o = this;
					if(confirm('确定删除？')){
						ajax({
							url : Tools.url('home', 'lianmeng/delete'),
							data : {id : $(o).attr('data-id')},
							beforeSend : function(){
								$(o).attr('disabled', 'disabled');
							},
							complete : function(){
								$(o).attr('disabled', false);
							},
							success : function(aResult){
								if(aResult.status == 1){
									reloadList();
								}else{
									UBox.show(aResult.msg, aResult.status);
								}
							}
						});
					}
				});
			}
			
			function appendLianmengItemHtml(aDataList){
				var listHtml = '';
				for(var i in aDataList){
					var aData = aDataList[i];
					listHtml += '<table class="ls-th">';
						listHtml += '<tr>';
							listHtml += '<td><input type="text" class="J-commit-input" data-id="' + aData.id + '" data-type="name" value="' + aData.name + '" /></td>';
							listHtml += '<td><input type="text" class="J-commit-input" data-id="' + aData.id + '" data-type="qianzhang" value="' + aData.qianzhang + '" /></td>';
							//listHtml += '<td><div class="t-type">r</div><a class="i-select"></a></td>';
							var opitonHtml = '';
							if(aData.duizhangfangfa == 1){
								opitonHtml = '<option value="1">0.975</option><option value="2">无水账单</option>';
							}else{
								opitonHtml = '<option value="2">无水账单</option><option value="1">0.975</option>';
							}
							listHtml += '<td><select class="J-commit-input ls-t-select" data-id="' + aData.id + '" data-type="duizhangfangfa">' + opitonHtml + '</select></td>';
							listHtml += '<td><input type="text" class="J-commit-input" data-id="' + aData.id + '" data-type="paiju_fee" value="' + aData.paiju_fee + '" /></td>';
							listHtml += '<td><input type="text" class="J-commit-input" data-id="' + aData.id + '" data-type="baoxian_choucheng" value="' + aData.baoxian_choucheng + '" style="float:left;width:62px;text-align:right;" /><span class="i-edit">%</span></td>';
							listHtml += '<td style="background:none;"><div class="la-delete-btn" data-id="' + aData.id + '"></div></td>';
						listHtml += '</tr>';
					listHtml += '</table>';
					listHtml += '<div class="h10"></div>';
				}
				var oListHtml = $(listHtml);
				oHtml.find('.ls-list-wrap').append(oListHtml);
				bindLianmengEvent(oListHtml);
				return oListHtml;
			}
			
			function _loadLianmengList(){
				ajax({
					url : Tools.url('home', 'lianmeng/get-list'),
					data : {},
					beforeSend : function(){
						//$(o).attr('disabled', 'disabled');
					},
					complete : function(){
						//$(o).attr('disabled', false);
					},
					success : function(aResult){
						if(aResult.status == 1){
							oHtml.find('.ls-list-wrap').html('');
							if(aResult.data.length != 0){
								appendLianmengItemHtml(aResult.data);
								oHtml.find('.ls-list-wrap').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
							}
						}
					}
				});
			}
			
			function reloadList(){
				$(document).click();
				AlertWin.showLianmengSetting();
			}
			
			showAlertWin(oHtml, function(){
				bindLianmengEvent(oHtml);
				_loadLianmengList();
				oHtml.find('.la-add-btn').click(function(){
					var o = this;
					ajax({
						url : Tools.url('home', 'lianmeng/add-lianmeng'),
						data : {
							name : $(o).parent().parent().find('input[data-type=name]').val(),
							qianzhang : $(o).parent().parent().find('input[data-type=qianzhang]').val(),
							duizhangfangfa : $(o).parent().parent().find('select[data-type=duizhangfangfa]').val(),
							paijuFee : $(o).parent().parent().find('input[data-type=paiju_fee]').val(),
							baoxianChoucheng : $(o).parent().parent().find('input[data-type=baoxian_choucheng]').val()
						},
						beforeSend : function(){
							$(o).attr('disabled', 'disabled');
						},
						complete : function(){
							$(o).attr('disabled', false);
						},
						success : function(aResult){
							if(aResult.status == 1){
								//$(o).parent().parent().find('input').val('');
								reloadList();
							}
							UBox.show(aResult.msg, aResult.status);
						}
					});
				});

			});	
		},
		
		showPaijuDataList : function(paijuId, isAllRecordData){
			var html = '';
			html += '<div class="J-paiju-data-list-win">';
				html += '<div class="top-title" style="color: #e91e63;"></div>';
				html += '<div class="top-up"><div class="info-detail"></div></div>';
				html += '<div class="top-th">';
					html += '<table>';
						html += '<tr>';
							html += '<td>玩家ID</td>';
							html += '<td>玩家昵称</td>';
							html += '<td>俱乐部ID</td>';
							html += '<td>俱乐部</td>';
							html += '<td>买入</td>';
							html += '<td>带出</td>';
							html += '<td>保险买入</td>';
							html += '<td>保险收入</td>';
							html += '<td>保险</td>';
							html += '<td>俱乐部保险</td>';
							html += '<td>保险合计</td>';
							html += '<td>战绩</td>';
						html += '</tr>';
					html += '</table>';
				html += '</div>';
				html += '<div class="body-list">';
				
				html += '</div>';
			html += '</div>';
			var oHtml = $(html);
			
			function _bulidItemHmtl(aData){
				var html = '';
				for(var i in aData){
					html += '<table>';
						html += '<tr>';
							html += '<td>' + aData[i].player_id + '</td>';
							html += '<td>' + aData[i].player_name + '</td>';
							html += '<td>' + aData[i].club_id + '</td>';
							html += '<td>' + aData[i].club_name + '</td>';
							html += '<td>' + aData[i].mairu + '</td>';
							html += '<td>' + aData[i].daicu + '</td>';
							html += '<td>' + aData[i].baoxian_mairu + '</td>';
							html += '<td>' + aData[i].baoxian_shouru + '</td>';
							html += '<td>' + aData[i].baoxian + '</td>';
							html += '<td>' + aData[i].club_baoxian + '</td>';
							html += '<td style="color:#ff0000;"><input type="text" data-type="baoxian_heji" data-id="' + aData[i].id + '" value="' + aData[i].baoxian_heji + '" style="text-align:center;height:50px;line-height:50px;max-width:80px;float:left;display:block;" /><a class="edit-icn"></a></td>';
							if(aData[i].zhanji != aData[i].original_zhanji){
								html += '<td style="color:#ff0000;"><span style="float:left;display:inline-block;width:25px;line-height:50px;height:50px;min-width:50px;text-align:right;">' + aData[i].original_zhanji + '-></span><input type="text" data-type="zhanji" data-id="' + aData[i].id + '" value="' + aData[i].zhanji + '" style="width:30px;text-align:center;height:50px;line-height:50px;max-width:80px;float:left;display:inline-block;" /><a class="edit-icn" style="float:left;display:inline-block;"></a></td>';
							}else{
								html += '<td style="color:#ff0000;"><input type="text" data-type="zhanji" data-id="' + aData[i].id + '" value="' + aData[i].zhanji + '" style="text-align:center;height:50px;line-height:50px;max-width:80px;float:left;display:block;" /><a class="edit-icn"></a></td>';
							}
						html += '</tr>';
					html += '</table>';
				}
				var oHtml = $(html);
				oHtml.find('.edit-icn').click(function(){
					$(this).prev().focus();
				});
				oHtml.find('input').keyup(function(e){
					if(e.keyCode == 13){
						var o = this;
						ajax({
							url : Tools.url('home', 'import/save-paiju-data-info'),
							data : {
								id : $(o).attr('data-id'),
								type : $(o).attr('data-type'),
								value : $(o).val()
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
					}
				});
				return oHtml;
			}
			
			showAlertWin(oHtml, function(){
				var aData = {paijuId : paijuId};
				if(isAllRecordData){
					aData.isAllRecordData = 1;
				}
				ajax({
					url : Tools.url('home', 'import/get-paiju-data-list'),
					data : aData,
					beforeSend : function(){
						//$(o).attr('disabled', 'disabled');
					},
					complete : function(){
						//$(o).attr('disabled', false);
					},
					success : function(aResult){
						if(aResult.status == 1){
							var aRecord = aResult.data.list[0];
							oHtml.find('.top-title').text(aRecord.paiju_type);
							oHtml.find('.info-detail').html('<a>牌局类型:</a><a class="val">' + aRecord.paiju_type + '</a><a>牌局名:</a><a class="val">' + aRecord.paiju_name + '</a><a>创建者:</a><a class="val">' + aRecord.paiju_creater + '</a><a>盲注:</a><a class="val">' + aRecord.mangzhu + '</a><a>牌桌:</a><a class="val">' + aRecord.paizuo + '</a><a>牌局时长:</a><a class="val">' + aRecord.paiju_duration + '</a><a>总手数:</a><a class="val">' + aRecord.zongshoushu + '</a><a>结束时间:</a><a class="val">' + aRecord.end_time_format + '</a>');
							oHtml.find('.body-list').append(_bulidItemHmtl(aResult.data.list));
							oHtml.find('.body-list').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
						}else{
							UBox.show(aResult.msg, aResult.status);
						}
					}
				});
			});
		},
		
		showAddPlayer : function(){
			var html = '';
			html += '<div class="J-player-list-win add-player">';
				html += '<div class="p-l-head">';
					//html += '<input type="text" class="search-krbh" />';
					//html += '<input type="text" class="add-player"/>';
				html += '</div>';
				html += '<div class="p-l-title"></div>';
				html += '<div class="p-l-body">';
					html += '<div class="p-l-item-wrap">';
						html += '<div class="p-l-item" style="background:none;">';
							html += '<div style="width:58px;" class="c-td"></div>';
							html += '<div style="width:108px;" class="c-td"><input type="text" data-type="keren_bianhao" /></div>';
							html += '<div style="width:33px;" class="c-td"></div>';
							html += '<div style="width:174px;" class="c-td"><input type="text" data-type="benjin" /></div>';
							var playerListHtml = '';
							
							html += '<div style="width:170px;cursor:pointer;" class="J-select-play c-td"><input type="text" data-type="player_name" style="height:100%;width:100%;" /></div>';
							html += '<div style="width:156px;" class="c-td">';
								html += '<input type="text" style="float:left;display:block;width:94px;height:100%;text-align: right;" data-type="ying_chou" />';
								html += '<span style="float:left;display:block;width:10px;height:100%;">%</span>';
								html += '<a class="edit-icn" style="float:left;display:block;width:43px;height:100%;cursor:pointer;"></a>';
							html += '</div>';
							html += '<div style="width:157px;" class="c-td">';
								html += '<input type="text" style="float:left;display:block;width:102px;height:100%;text-align: right;" data-type="shu_fan" />';
								html += '<span style="float:left;display:block;width:10px;height:100%;">%</span>';
								html += '<a class="edit-icn" style="float:left;display:block;width:43px;height:100%;cursor:pointer;"></a>';
							html += '</div>';
							var agentListHtml = '';
							var agentName = '请选择';
							if(aAgentList.length != 0){
								agentListHtml += '<div class="play-select-list"><div class="p-s-wrap">';
								for(var k in aAgentList){
									agentListHtml += '<div class="h10"></div>';
									agentListHtml += '<div class="play-select-list-item" data-id="' + aAgentList[k].id + '">' + aAgentList[k].agent_name + '</div>';
								}
								agentListHtml += '</div></div>';
							}
							html += '<div style="width:154px;cursor:pointer;" class="J-agent-id J-select-play c-td" data-id="0"><div style="width:90px;text-align:right;">' + agentName + '</div>' + agentListHtml + '</div>';
							html += '<div style="width:157px;" class="c-td"><input type="text" data-type="play_id" /></div>';
							html += '<div style="width:116px;" class="c-td"><a class="add-btn" style="position: relative;left: 25px;top: 8px;display: block;width: 78px;height: 33px;cursor:pointer;"></a></div>';
						html += '</div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			
			var oHtml = $(html);
			
			showAlertWin(oHtml, function(){
				oHtml.find('.J-select-play').click(function(){
					$(this).find('.play-select-list').show();
				});
				oHtml.find('.play-select-list').each(function(){
					$(this).show();
					$(this).css({left : 100});
					$(this).hide();
				});
				oHtml.find('.play-select-list').on('mouseleave', function(){
					$(this).hide();
				});
				oHtml.find('.play-select-list .play-select-list-item').on('click', function(){
					var oList = $(this).parent().parent().parent().parent();
					oList.prev().text($(this).text());
					oList.parent().attr('data-id', $(this).attr('data-id'));
					setTimeout(function(){
						oList.hide();
					}, 100);
					
				});
				oHtml.find('.play-select-list .p-s-wrap').each(function(){
					$(this).parent().show();
					$(this).tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
					$(this).parent().hide();
				});
				oHtml.find('.add-btn').click(function(){
					var o = this;
					ajax({
						url : Tools.url('home', 'index/add-keren'),
						data : {
							kerenBianhao : $(o).parent().parent().find('input[data-type=keren_bianhao]').val(),
							benjin : $(o).parent().parent().find('input[data-type=benjin]').val(),
							playerName : $(o).parent().parent().find('input[data-type=player_name]').val(),
							yingChou : $(o).parent().parent().find('input[data-type=ying_chou]').val(),
							shuFan : $(o).parent().parent().find('input[data-type=shu_fan]').val(),
							agentId : $(o).parent().parent().find('.J-agent-id').attr('data-id'),
							playerId : $(o).parent().parent().find('input[data-type=play_id]').val()
						},
						beforeSend : function(){
							$(o).attr('disabled', 'disabled');
						},
						complete : function(){
							$(o).attr('disabled', false);
						},
						success : function(aResult){
							if(aResult.status == 1){
								$(o).parent().parent().parent().parent().parent().parent().remove();
							}
							UBox.show(aResult.msg, aResult.status);
						}
					});
				});
			});
		},
		
		showPlayerList : function(){
			var html = '';
			html += '<div class="J-player-list-win">';
				html += '<div class="p-l-head">';
					html += '<input type="text" class="search-krbh" />';
					html += '<input type="text" class="add-player" onclick="AlertWin.showAddPlayer();"  />';
				html += '</div>';
				html += '<div class="p-l-title"><div class="krbh-sort"></div><div class="bj-sort"></div></div>';
				html += '<div class="p-l-body">';
					html += '<div class="p-l-item-wrap">';
						
					html += '</div>';
				html += '</div>';
			html += '</div>';
			
			var oHtml = $(html);
			
			showAlertWin(oHtml, function(){
				oKerenListObject = new KerenList({oWrapDom : oHtml.find('.p-l-body .p-l-item-wrap')});
				oKerenListObject.show(1);
				oKerenListObject.oScrollBar = oHtml.find('.p-l-body').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
				oKerenListObject.oScrollBar.scrollEndEventFunc = function(){
					var page = oKerenListObject.oWrapDom.attr('data-page');
					oKerenListObject.show(parseInt(page) + 1);
				}
				oHtml.find('.search-krbh').keyup('input propertychange', function(){
					if($(this).val() == ''){
						oKerenListObject.aExtentParam.kerenBianhao = 0;
					}else{
						oKerenListObject.aExtentParam.kerenBianhao = $(this).val();
					}
					oKerenListObject.show(1);
				});
				oHtml.find('.krbh-sort').click(function(){
					oKerenListObject.aExtentParam.benjinSort = 0;
					oHtml.find('.bj-sort').removeClass('active');
					if($(this).hasClass('active')){
						$(this).removeClass('active');
						oKerenListObject.aExtentParam.kerenBianhaoSort = 2;
					}else{
						$(this).addClass('active');
						oKerenListObject.aExtentParam.kerenBianhaoSort = 1;
					}
					oKerenListObject.show(1);
				});
				oHtml.find('.bj-sort').click(function(){
					oKerenListObject.aExtentParam.kerenBianhaoSort = 0;
					oHtml.find('.krbh-sort').removeClass('active');
					if($(this).hasClass('active')){
						$(this).removeClass('active');
						oKerenListObject.aExtentParam.benjinSort = 2;
					}else{
						$(this).addClass('active');
						oKerenListObject.aExtentParam.benjinSort = 1;
					}
					oKerenListObject.show(1);
				});
			});
		},
		
		showAddAgent : function(){
			var html = '';
			html += '<div class="J-add-agent-win">';
				html += '<input type="text" class="agent-name" placeholder="请输入代理名字" />';
				html += '<a class="add-agent"></a>';
			html += '</div>';
			
			var oHtml = $(html);
			
			oHtml.find('.add-agent').click(function(){
				var o = this;
				ajax({
					url : Tools.url('home', 'agent/add'),
					data : {
						agentName : $(o).prev().val()
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
			
			showAlertWin(oHtml, function(){
				oHtml.find('.agent-name').focus();
			});
		},
		
		showMoneyOutPutTypeList : function(aMoneyOutPutTypeList){
			var html = '';
			html += '<div class="J-money-type-list-win">';
				html += '<div class="m-t-l-head"><div class="m-t-l-h-delete"></div></div>';
				html += '<div class="m-t-l-title"><div class="J-select-all m-t-l-h-select"></div></div>';
				html += '<div class="m-t-l-list">';
					for(var i in aMoneyOutPutTypeList){
						html += '<div class="m-t-l-list-item">';
							html += '<div class="m-t-l-list-item-txt txt-pay-type">' + aMoneyOutPutTypeList[i].out_put_type + '</div>';
							html += '<div class="m-t-l-list-item-txt txt-money">' + aMoneyOutPutTypeList[i].money + '</div>';
							html += '<div class="m-t-l-h-select" style="bottom: -16px;" data-id="' + aMoneyOutPutTypeList[i].id + '"></div>';
						html += '</div>';
					}
				html += '</div>';
				html += '<div class="m-t-l-bottom">';
					html += '<input type="text" class="m-t-l-b-txt-pay-type" />';
					html += '<input type="text" class="m-t-l-b-txt-money" />';
					html += '<div class="m-t-l-b-add"></div>';
				html += '</div>';
			html += '</div>';
			
			var oHtml = $(html);
			oHtml.find('.J-select-all').on('click', function(){
				if($(this).hasClass('active')){
					$(this).removeClass('active');
					oHtml.find('.m-t-l-list-item .m-t-l-h-select').removeClass('active');
				}else{
					$(this).addClass('active');
					oHtml.find('.m-t-l-list-item .m-t-l-h-select').addClass('active');
				}
			});
			oHtml.find('.m-t-l-list-item .m-t-l-h-select').on('click', function(){
				if($(this).hasClass('active')){
					$(this).removeClass('active');
					oHtml.find('.J-select-all').removeClass('active');
				}else{
					$(this).addClass('active');
				}
			});
			oHtml.find('.m-t-l-h-delete').on('click', function(){
				var o = this;
				var aId = [];
				oHtml.find('.m-t-l-list-item .m-t-l-h-select.active').each(function(){
					aId.push($(this).attr('data-id'));
				});
				ajax({
					url : Tools.url('home', 'money-out-put-type/delete'),
					data : {
						aId : aId
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
			oHtml.find('.m-t-l-b-add').on('click', function(){
				var o = this;
				ajax({
					url : Tools.url('home', 'money-out-put-type/save'),
					data : {
						outPutType : $(o).parent().find('.m-t-l-b-txt-pay-type').val(),
						money : $(o).parent().find('.m-t-l-b-txt-money').val()
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
			oHtml.find('.m-t-l-b-txt-money').bind('input propertychange', function() {  
				setInputInterval(this);
			});
			
			showAlertWin(oHtml, function(){
				oHtml.find('.m-t-l-list').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
			});
		},
		
		showMoneyTypeList : function(aMoneyTypeList){
			var html = '';
			html += '<div class="J-money-type-list-win">';
				html += '<div class="m-t-l-head"><div class="m-t-l-h-delete"></div></div>';
				html += '<div class="m-t-l-title"><div class="J-select-all m-t-l-h-select"></div></div>';
				html += '<div class="m-t-l-list">';
					for(var i in aMoneyTypeList){
						html += '<div class="m-t-l-list-item">';
							html += '<div class="m-t-l-list-item-txt txt-pay-type">' + aMoneyTypeList[i].pay_type + '</div>';
							html += '<div class="m-t-l-list-item-txt txt-money">' + aMoneyTypeList[i].money + '</div>';
							html += '<div class="m-t-l-h-select" style="bottom: -16px;" data-id="' + aMoneyTypeList[i].id + '"></div>';
						html += '</div>';
					}
				html += '</div>';
				html += '<div class="m-t-l-bottom">';
					html += '<input type="text" class="m-t-l-b-txt-pay-type" />';
					html += '<input type="text" class="m-t-l-b-txt-money" />';
					html += '<div class="m-t-l-b-add"></div>';
				html += '</div>';
			html += '</div>';
			
			var oHtml = $(html);
			oHtml.find('.J-select-all').on('click', function(){
				if($(this).hasClass('active')){
					$(this).removeClass('active');
					oHtml.find('.m-t-l-list-item .m-t-l-h-select').removeClass('active');
				}else{
					$(this).addClass('active');
					oHtml.find('.m-t-l-list-item .m-t-l-h-select').addClass('active');
				}
			});
			oHtml.find('.m-t-l-list-item .m-t-l-h-select').on('click', function(){
				if($(this).hasClass('active')){
					$(this).removeClass('active');
					oHtml.find('.J-select-all').removeClass('active');
				}else{
					$(this).addClass('active');
				}
			});
			oHtml.find('.m-t-l-h-delete').on('click', function(){
				var o = this;
				var aId = [];
				oHtml.find('.m-t-l-list-item .m-t-l-h-select.active').each(function(){
					aId.push($(this).attr('data-id'));
				});
				ajax({
					url : Tools.url('home', 'money-type/delete'),
					data : {
						aId : aId
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
			oHtml.find('.m-t-l-b-add').on('click', function(){
				var o = this;
				ajax({
					url : Tools.url('home', 'money-type/save'),
					data : {
						payType : $(o).parent().find('.m-t-l-b-txt-pay-type').val(),
						money : $(o).parent().find('.m-t-l-b-txt-money').val()
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
			oHtml.find('.m-t-l-b-txt-money').bind('input propertychange', function() {  
				setInputInterval(this);
			});
			showAlertWin(oHtml, function(){
				oHtml.find('.m-t-l-list').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
			});
		},
		
		showAddClub : function(){
			var html = '';
			html += '<div class="J-edit-club-win J-edit-user-info-win">';
				html += '<input type="text" class="J-input J-club-name" value="" />';
				html += '<input type="text" class="J-input J-club-id" value="" />';
				html += '<input type="text" class="J-input J-club-login-name" value="" />';
				html += '<input type="text" class="J-input J-club-login-password" value="" />';
				html += '<div class="J-new-btn"></div>';
			html += '</div>';
			
			var oHtml = $(html);
			
			oHtml.find('.J-new-btn').on('click', function(){
				var o = this;
				ajax({
					url : Tools.url('home', 'club/save'),
					data : {
						clubName : oHtml.find('.J-club-name').val(),
						clubId : oHtml.find('.J-club-id').val(),
						clubLoginName : oHtml.find('.J-club-login-name').val(),
						clubLoginPassword : oHtml.find('.J-club-login-password').val(),
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
			showAlertWin(oHtml, function(){
				
			});
		},
		
		showEditClub : function(aClub){
			var html = '';
			html += '<div class="J-edit-club-win J-edit-user-info-win">';
				html += '<input type="text" class="J-input J-club-name" value="' + aClub.club_name + '" />';
				html += '<input type="text" class="J-input J-club-id" value="' + aClub.club_id + '" />';
				html += '<input type="text" class="J-input J-club-login-name" value="' + aClub.club_login_name + '" />';
				html += '<input type="text" class="J-input J-club-login-password" value="' + aClub.club_login_password + '" />';
				html += '<div class="J-del-btn"></div>';
				html += '<div class="J-edit-btn"></div>';
			html += '</div>';
			
			var oHtml = $(html);
			
			oHtml.find('.J-del-btn').on('click', function(){
				if(confirm('确定删除？')){
					var o = this;
					ajax({
						url : Tools.url('home', 'club/delete'),
						data : {
							id : aClub.id
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
			
			oHtml.find('.J-edit-btn').on('click', function(){
				var o = this;
				ajax({
					url : Tools.url('home', 'club/save'),
					data : {
						id : aClub.id,
						clubName : oHtml.find('.J-club-name').val(),
						clubId : oHtml.find('.J-club-id').val(),
						clubLoginName : oHtml.find('.J-club-login-name').val(),
						clubLoginPassword : oHtml.find('.J-club-login-password').val(),
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
			
			showAlertWin(oHtml, function(){
				
			});
		},
		
		showEditUserInfo : function(){
			var aUser = App.oCurrentUser;
			var html = '';
			html += '<div class="J-edit-user-info-win">';
				html += '<input type="text" class="J-input J-login-name" value="' + aUser.login_name + '" />';
				html += '<input type="text" class="J-input J-password" value="' + aUser.password + '" />';
				html += '<input type="text" class="J-input J-qibu-choushui" value="' + aUser.qibu_choushui + '" />';
				html += '<div class="J-input J-choushui-shuanfa" data-value=""></div>';
				html += '<div class="J-input J-choushui-shuanfa-item-wrap">';
					html += '<div class="J-choushui-shuanfa-item" data-value="1">四舍五入</div>';
					html += '<div class="J-choushui-shuanfa-item" data-value="2">余数抹零</div>';
				html += '</div>';
				html += '<div class="J-input J-save-btn"></div>';
			html += '</div>';
			
			var oHtml = $(html);
			oHtml.find('.J-choushui-shuanfa').on('click', function(){
				if(oHtml.find('.J-choushui-shuanfa-item-wrap').is(':hidden')){
					oHtml.find('.J-choushui-shuanfa-item-wrap').show();
				}else{
					oHtml.find('.J-choushui-shuanfa-item-wrap').hide();
				}
			});
			oHtml.find('.J-choushui-shuanfa-item').on('click', function(){
				oHtml.find('.J-choushui-shuanfa').attr('data-value', $(this).attr('data-value'));
				oHtml.find('.J-choushui-shuanfa').text($(this).text());
				$(this).parent().hide();
			});
			oHtml.find('.J-save-btn').on('click', function(){
				var o = this;
				ajax({
					url : Tools.url('home', 'user/save'),
					data : {
						loginName : oHtml.find('.J-login-name').val(),
						password : oHtml.find('.J-password').val(),
						qibuChoushui : oHtml.find('.J-qibu-choushui').val(),
						choushuiShuanfa : oHtml.find('.J-choushui-shuanfa').attr('data-value')
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
			showAlertWin(oHtml, function(){
				oHtml.find('.J-choushui-shuanfa-item[data-value=' + aUser.choushui_shuanfa + ']').click();
			});
		},
		
		showPaijuList : function(aParam){
			var html = '';
			
			html += '<div class="J-bill-list-win">';
				html += '<div class="content-body">';
					html += '<div class="item-wrap">';
					
					html += '</div>';
				html += '</div>';
				html += '<div class="content-bottom" style="display:none;">';
					html += '<div class="l-btn"></div>';
					html += '<div class="c-txt">1/10</div>';
					html += '<div class="r-btn"></div>';
				html += '</div>';
			html += '</div>';
			
			var oHtml = $(html);
			showAlertWin(oHtml, function(){
				oPaijuListObject = new PaijuList({oWrapDom : oHtml.find('.item-wrap')});
				if(aParam && typeof(aParam.isHistory)){
					oPaijuListObject.aExtentParam.isHistory = 1;
				}
				oPaijuListObject.show(1);
				oPaijuListObject.oScrollBar = oHtml.find('.content-body').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
				oPaijuListObject.oScrollBar.scrollEndEventFunc = function(){
					var page = oPaijuListObject.oWrapDom.attr('data-page');
					oPaijuListObject.show(parseInt(page) + 1);
				}
			});
		}
				
	}
})(window, jQuery);