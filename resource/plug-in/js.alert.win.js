(function(container, $){
	container.AlertWin = {
		showMoneyTypeWin : function(moneyTypeId, moneyType){
			var html = '';
			html += '<div class="J-data-list-win J-lianmeng-setting-win" style="width:460px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">（' + moneyType + '）</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h20"></div>';
						html += '<div style="height:52px;margin-bottom: 20px;">';
							html += '<div class="alert alert-success" style="float:left;width:90px;height:100%;margin-left:90px;margin-bottom: 0px;">增加金额</div>';
							html += '<div style="float:left;width:50px;height:100%;line-height: 52px;text-align: center;font-size: 32px;">+</div>';
							html += '<div style="float:left;width:150px;height:100%;"><input type="text" class="form-control" data-type="add" style="height:100%;text-align: center;" /></div>';
						html += '</div>';
						html += '<div style="height:52px;margin-bottom: 20px;">';
							html += '<div class="alert alert-danger" style="float:left;width:90px;height:100%;margin-left:90px;margin-bottom: 0px;">减少金额</div>';
							html += '<div style="float:left;width:50px;height:100%;line-height: 52px;text-align: center;font-size: 32px;">-</div>';
							html += '<div style="float:left;width:150px;height:100%;"><input type="text" class="form-control" data-type="sub" style="height:100%;text-align: center;" /></div>';
						html += '</div>';
							
						html += '<div class="h20"></div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			var oHtml = $(html);
						
			showAlertWin(oHtml, function(){
				oHtml.find('input').keyup(function(e){
					if(e.keyCode == 13){
						var addMoney = parseInt($(this).val());
						if($(this).attr('data-type') == 'sub'){
							addMoney = -addMoney;
						}
						var o = this;
						ajax({
							url : Tools.url('home', 'money-type/add-money'),
							data : {
								id : moneyTypeId,
								addMoney : addMoney
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
									/*UBox.show(aResult.msg, aResult.status);
									$(document).click();*/
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
			});	
		},
		
		showMoneyOutPutTypeWin : function(moneyTypeId, moneyType){
			var html = '';
			html += '<div class="J-data-list-win J-lianmeng-setting-win" style="width:460px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">（' + moneyType + '）</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h20"></div>';
						html += '<div style="height:52px;margin-bottom: 20px;">';
							html += '<div class="alert alert-success" style="float:left;width:90px;height:100%;margin-left:90px;margin-bottom: 0px;">增加金额</div>';
							html += '<div style="float:left;width:50px;height:100%;line-height: 52px;text-align: center;font-size: 32px;">+</div>';
							html += '<div style="float:left;width:150px;height:100%;"><input type="text" class="form-control" data-type="add" style="height:100%;text-align: center;" /></div>';
						html += '</div>';
						html += '<div style="height:52px;margin-bottom: 20px;">';
							html += '<div class="alert alert-danger" style="float:left;width:90px;height:100%;margin-left:90px;margin-bottom: 0px;">减少金额</div>';
							html += '<div style="float:left;width:50px;height:100%;line-height: 52px;text-align: center;font-size: 32px;">-</div>';
							html += '<div style="float:left;width:150px;height:100%;"><input type="text" class="form-control" data-type="sub" style="height:100%;text-align: center;" /></div>';
						html += '</div>';
							
						html += '<div class="h20"></div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			var oHtml = $(html);
						
			showAlertWin(oHtml, function(){
				oHtml.find('input').keyup(function(e){
					if(e.keyCode == 13){
						var addMoney = parseInt($(this).val());
						if($(this).attr('data-type') == 'sub'){
							addMoney = -addMoney;
						}
						var o = this;
						ajax({
							url : Tools.url('home', 'money-out-put-type/add-money'),
							data : {
								id : moneyTypeId,
								addMoney : addMoney
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
									/*UBox.show(aResult.msg, aResult.status);
									$(document).click();*/
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
			});	
		},
		
		showJiaoShouWin : function(kerenBianhao, moneyTypeId, moneyType){
			var html = '';
			html += '<div class="J-data-list-win J-lianmeng-setting-win" style="width:460px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">（客人编号：' + kerenBianhao + '）→（' + moneyType + '）</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h20"></div>';
						html += '<div style="height:52px;margin-bottom: 20px;">';
							html += '<div class="alert alert-success" style="float:left;width:90px;height:100%;margin-left:90px;margin-bottom: 0px;">存入金额</div>';
							html += '<div style="float:left;width:50px;height:100%;line-height: 52px;text-align: center;font-size: 32px;">+</div>';
							html += '<div style="float:left;width:150px;height:100%;"><input type="text" class="form-control" data-type="add" style="height:100%;text-align: center;" /></div>';
						html += '</div>';
						html += '<div style="height:52px;margin-bottom: 20px;">';
							html += '<div class="alert alert-danger" style="float:left;width:90px;height:100%;margin-left:90px;margin-bottom: 0px;">转出金额</div>';
							html += '<div style="float:left;width:50px;height:100%;line-height: 52px;text-align: center;font-size: 32px;">-</div>';
							html += '<div style="float:left;width:150px;height:100%;"><input type="text" class="form-control" data-type="sub" style="height:100%;text-align: center;" /></div>';
						html += '</div>';
							
						html += '<div class="h20"></div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			var oHtml = $(html);
						
			showAlertWin(oHtml, function(){
				oHtml.find('input').keyup(function(e){
					if(e.keyCode == 13){
						var jsjer = parseInt($(this).val());
						if($(this).attr('data-type') == 'sub'){
							jsjer = -jsjer;
						}
						var o = this;
						ajax({
							url : Tools.url('home', 'index/jiaoshou-jiner'),
							data : {
								kerenBianhao : kerenBianhao,
								payType : moneyTypeId,
								jsjer : jsjer
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
									/*UBox.show(aResult.msg, aResult.status);
									$(document).click();*/
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
			});	
		},
		
		showUserActive : function(o){
			var html = '';
			html += '<div class="J-data-list-win J-lianmeng-setting-win" style="width:800px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">启用设置</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h20"></div>';
						html += '<div class="J-ls-list-wrap" style="min-height:260px;padding: 0 10px;">';
							html += '<div class="panel panel-default">';
								html += '<div class="panel-heading">';
									html += '<h3 class="panel-title">俱乐部设置</h3>';
								html += '</div>';
								html += '<div class="panel-body">';
									html += '<div class="table-responsive">';
										html += '<table class="J-club-list-table table">';
										html += '<tr><th>俱乐部名称</th><th>俱乐部ID</th><th>官网后台账号</th><th>官网后台密码</th><th>操作</th></tr>';
										html += '</table>';
									html += '</div>';
									html += '<div class="h30"><button type="button" class="J-add-club-item btn btn-sm btn-primary" style="float:right;margin-right: 8px;">增加</button></div>';
								html += '</div>';
							html += '</div>';
							html += '<div class="panel panel-default">';
								html += '<div class="panel-heading">';
									html += '<h3 class="panel-title">联盟设置</h3>';
								html += '</div>';
								html += '<div class="panel-body">';
									html += '<div class="table-responsive">';
										html += '<table class="J-lianmeng-list-table table">';
										html += '<tr><th>联盟名称</th><th>联盟欠账</th><th style="min-width:100px;">对账方法</th><th>桌子费</th><th>保险被抽成</th><th>开桌人名称</th><th>操作</th></tr>';
										html += '</table>';
									html += '</div>';
									html += '<div class="h30"><button type="button" class="J-add-lianmeng-item btn btn-sm btn-primary" style="float:right;margin-right: 8px;">增加</button></div>';
								html += '</div>';
							html += '</div>';
						html += '</div>';
						html += '<div class="h30" style="text-align:center;"><button type="button" class="J-set-active btn btn-sm btn-primary">开始启用</button></div>';
						html += '<div class="h20"></div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			var oHtml = $(html);
			
			function _appendClubList(aData){
				var trHtml = '';
				for(var i in aData){
					trHtml += '<tr>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" value="' + aData[i].club_name + '" data-type="club_name" placeholder="输入名称" /></td>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" value="' + aData[i].club_id + '" data-type="club_id" placeholder="输入ID" /></td>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" value="' + aData[i].club_login_name + '" data-type="club_login_name" placeholder="输入账号" /></td>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" value="' + aData[i].club_login_password + '" data-type="club_login_password" placeholder="输入密码" /></td>';
						trHtml += '<td><button type="button" class="J-save-club-item btn btn-sm btn-primary" style="float:right;" data-id="' + aData[i].id + '">保存</button></td>';
					trHtml += '</tr>';
				}
				var oTrHtml = $(trHtml);
				oTrHtml.find('.J-save-club-item').click(function(){
					_saveClubItem(this);
				});
				oHtml.find('.J-club-list-table').append(oTrHtml);
			}
			
			function _saveClubItem(o){
				ajax({
					url : Tools.url('home', 'club/save'),
					data : {
						id : $(o).attr('data-id'),
						clubName : $(o).parent().parent().find('input[data-type=club_name]').val(),
						clubId : $(o).parent().parent().find('input[data-type=club_id]').val(),
						clubLoginName : $(o).parent().parent().find('input[data-type=club_login_name]').val(),
						clubLoginPassword : $(o).parent().parent().find('input[data-type=club_login_password]').val()
					},
					beforeSend : function(){
						$(o).attr('disabled', 'disabled');
					},
					complete : function(){
						$(o).attr('disabled', false);
					},
					success : function(aResult){
						if(aResult.status == 1){
							if($(o).attr('data-id') == 0){
								$(o).attr('data-id', aResult.data);
							}
						}
						UBox.show(aResult.msg, aResult.status);
					}
				});
			}
			
			function _appendLianmengList(aData){
				var trHtml = '';
				for(var i in aData){
					trHtml += '<tr>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" value="' + aData[i].name + '" data-type="name" placeholder="输入名称" /></td>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" value="' + aData[i].qianzhang + '" data-type="qianzhang" placeholder="输入欠账" /></td>';
						if(aData[i].duizhangfangfa == 1){
							trHtml += '<td><select class="form-control" style="width:100%;" data-type="duizhangfangfa"><option value="1">0.975</option><option value="2">无水账单</option></select></td>';
						}else{
							trHtml += '<td><select class="form-control" style="width:100%;" data-type="duizhangfangfa"><option value="2">无水账单</option><option value="1">0.975</option></select></td>';
						}
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" value="' + aData[i].paiju_fee + '" data-type="paiju_fee" placeholder="输入桌子费" /></td>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" value="' + aData[i].baoxian_choucheng + '" data-type="baoxian_choucheng" placeholder="输入保险被抽成" /></td>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" value="' + aData[i].paiju_creater + '" data-type="paiju_creater" placeholder="输入开桌人名称" /></td>';
						trHtml += '<td><button type="button" class="J-save-lianmeng-item btn btn-sm btn-primary" style="float:right;" data-id="' + aData[i].id + '">保存</button></td>';
					trHtml += '</tr>';
				}
				var oTrHtml = $(trHtml);
				oTrHtml.find('.J-save-lianmeng-item').click(function(){
					_saveLianmengItem(this);
				});
				oHtml.find('.J-lianmeng-list-table').append(oTrHtml);
			}
			
			function _saveLianmengItem(o){
				ajax({
					url : Tools.url('home', 'lianmeng/save-lianmeng'),
					data : {
						id : $(o).attr('data-id'),
						name : $(o).parent().parent().find('input[data-type=name]').val(),
						qianzhang : $(o).parent().parent().find('input[data-type=qianzhang]').val(),
						duizhangfangfa : $(o).parent().parent().find('select[data-type=duizhangfangfa]').val(),
						paijuFee : $(o).parent().parent().find('input[data-type=paiju_fee]').val(),
						baoxianChoucheng : $(o).parent().parent().find('input[data-type=baoxian_choucheng]').val(),
						paijuCreater : $(o).parent().parent().find('input[data-type=paiju_creater]').val()
					},
					beforeSend : function(){
						$(o).attr('disabled', 'disabled');
					},
					complete : function(){
						$(o).attr('disabled', false);
					},
					success : function(aResult){
						if(aResult.status == 1){
							if($(o).attr('data-id') == 0){
								$(o).attr('data-id', aResult.data);
							}
						}
						UBox.show(aResult.msg, aResult.status);
					}
				});
			}
			
			function _loadList(){
				ajax({
					url : Tools.url('home', 'user/get-club-and-lianmeng-list'),
					data : {},
					beforeSend : function(){
						$(o).attr('disabled', 'disabled');
					},
					complete : function(){
						$(o).attr('disabled', false);
					},
					success : function(aResult){
						if(aResult.status == 1){
							_appendClubList(aResult.data.aClubList);
							_appendLianmengList(aResult.data.aLianmengList);
						}else{
							UBox.show(aResult.msg, aResult.status);
						}
					}
				});
			}
			
			showAlertWin(oHtml, function(){
				_loadList();
				oHtml.find('.J-add-club-item').click(function(){
					var trHtml = '<tr>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" data-type="club_name" placeholder="输入名称" /></td>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" data-type="club_id" placeholder="输入ID" /></td>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" data-type="club_login_name" placeholder="输入账号" /></td>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" data-type="club_login_password" placeholder="输入密码" /></td>';
						trHtml += '<td><button type="button" class="J-save-club-item btn btn-sm btn-primary" style="float:right;" data-id="0">保存</button></td>';
					trHtml += '</tr>';
					var oTrHtml = $(trHtml);
					oTrHtml.find('.J-save-club-item').click(function(){
						_saveClubItem(this);
					});
					oHtml.find('.J-club-list-table').append(oTrHtml);
				});
				
				oHtml.find('.J-add-lianmeng-item').click(function(){
					var trHtml = '<tr>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" value="" data-type="name" placeholder="输入名称" /></td>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" value="0" data-type="qianzhang" placeholder="输入欠账" /></td>';
						trHtml += '<td><select class="form-control" style="width:100%;" data-type="duizhangfangfa"><option value="1">0.975</option><option value="2">无水账单</option></select></td>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" value="0" data-type="paiju_fee" placeholder="输入桌子费" /></td>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" value="0" data-type="baoxian_choucheng" placeholder="输入保险被抽成" /></td>';
						trHtml += '<td><input type="text" class="form-control" style="width:100%;" value="" data-type="paiju_creater" placeholder="输入开桌人名称" /></td>';
						trHtml += '<td><button type="button" class="J-save-lianmeng-item btn btn-sm btn-primary" style="float:right;" data-id="0">保存</button></td>';
					trHtml += '</tr>';
					var oTrHtml = $(trHtml);
					oTrHtml.find('.J-save-lianmeng-item').click(function(){
						_saveLianmengItem(this);
					});
					oHtml.find('.J-lianmeng-list-table').append(oTrHtml);
				});
				
				oHtml.find('.J-set-active').click(function(){
					ajax({
						url : Tools.url('home', 'user/set-active'),
						data : {},
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
				});
			});	
		},
		
		showFillSavecode : function(oo, clubId){
			var aData = {};
			var exponent = '';
			var modulus = '';
			var html = '';
			html += '<div class="J-data-list-win J-save-code-win" style="background:none;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">获取牌局</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;min-height:182px;">';
						html += '<img class="img-thumbnail" style="float: left; position: relative; top: 63px; border-radius: 5px; left: 150px; width: 120px; height: 48px;border:1px solid #ccc;" />';
						html += '<input type="text" class="form-control save-code" style="text-align: center; border-radius: 5px;float: left; position: relative; top: 63px; left: 160px; width: 120px; height: 48px; line-height: 48px; font-size: 20px;" />';
						html += '<a class="btn btn-lg btn-primary commit-save-code">确定</a>';
						html += '<div class="J-select-time"><input type="text" class="st" onclick="WdatePicker({dateFmt:\'yyyy-MM-dd\'});" /><span style="float: left; width: 28px; text-align: center;">至</span><input type="text" class="et" onclick="WdatePicker({dateFmt:\'yyyy-MM-dd\'});" /></div>';
						html += '<div class="J-wait-tip" style="background:#ffffff;float: left; position: relative;text-align:center; line-height: 100px; height: 120px; width: 400px; top: -4px; left: -120px;color:#333;display:none;">正在获取牌局，请稍等...</div>';
					html += '</div>';
				html += '</div>';
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
						isCanCloseWin = true;
						if(aResult.status == 1){
							$(document).click();
							UBox.show(aResult.msg, aResult.status, function(){
								location.reload();
							}, 1);
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
						}else if(aResult.status == 100){
							aData.retry = 1;
							aData.startTime = aResult.data;
							_doImportPaiju(o, aData);
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
				var skey = RSAUtils.encryptedString(RSAUtils.getKeyPair(exponent, '', modulus), "name=" + aData.club_login_name + "&pwd=" + hex_md5(aData.club_login_password));
				if($(o).prev().val().length != 4){
					UBox.show('验证码不正确', -1);
					return;
				}
				aData.safecode = safecode;
				aData.skey = skey;
				oHtml.find('.J-wait-tip').show();
				isCanCloseWin = false;
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
							exponent = aResult.data.exponentValue;
							modulus = aResult.data.modulusValue;
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
			html += '<div class="J-data-list-win" style="float:left;width:900px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">' + (clubName ? clubName : '俱乐部') + '账单详情</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h10"></div>';
						html += '<div class="h30 breadcrumb">';
							html += '<div style="float:left;width:300px;height:100%;"></div>';
							html += '<div style="float:right;width:300px;height:100%;"><div class="J-s-lms-btn btn btn-sm btn-primary" style="float:right;margin-right:10px;">联盟俱乐部设置</div><div class="s-lms-txt">新账单累计: <font class="J-total-zhan-dan" style="color:#ff5722;">0</font> 元</div></div>';
						html += '</div>';
					html += '<div class="h10"></div>';
						html += '<div class="table-responsive" style="padding:0px 10px;">';
							html += '<table class="J-jlbzd-list-table table table-hover table-striped">';
							html += '<tr><th>牌局名</th><th>战绩</th><th>保险</th><th>桌子费</th><th>保险被抽</th><th>当局账单</th></tr>';
							html += '</table>';
						html += '</div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			var oHtml = $(html);
			
			function appendLianmengItemHtml(aDataList){
				var listHtml = '';
				var totalZhanDan = 0;
				for(var i in aDataList){
					var aData = aDataList[i];
					listHtml += '<tr>';
						listHtml += '<td>' + aData.paiju_name + '</td>';
						listHtml += '<td>' + aData.zhanji + '</td>';
						listHtml += '<td>' + aData.baoxian_heji + '</td>';
						listHtml += '<td>' + aData.paiju_fee + '</td>';
						listHtml += '<td>' + aData.baoxian_beichou + '</td>';
						listHtml += '<td>' + aData.zhang_dan + '</td>';
					listHtml += '</tr>';
					totalZhanDan += parseInt(aData.zhang_dan);
				}
				var oListHtml = $(listHtml);
				oHtml.find('.J-jlbzd-list-table').append(oListHtml);
				oHtml.find('.J-total-zhan-dan').text(totalZhanDan);
				bindLianmengEvent(oHtml);
				
				return oListHtml;
			}
			
			function bindLianmengEvent(oHtml){
				
			}
			
			showAlertWin(oHtml, function(){
				appendLianmengItemHtml(aData);
				oHtml.find('.J-s-lms-btn').click(function(){
					$(document).click();
					AlertWin.showLianmengClubSetting(lianmengId);
				});
			});	
		},
		
		showLianmengClubSetting : function(lianmengId){
			var html = '';
			html += '<div class="J-data-list-win" style="float:left;width:900px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">联盟俱乐部设置</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h10"></div>';
						html += '<div class="table-responsive" style="padding:0px 10px;">';
							html += '<table class="J-lmjlbst-list-table table table-hover table-striped">';
							html += '<tr><th>俱乐部</th><th>俱乐部ID</th><th style="min-width:120px;">对账方法</th><th>桌子费</th><th>保险被抽成</th><th style="min-width:120px;">联盟</th><th>操作</th></tr>';
							html += '</table>';
						html += '</div>';
					html += '</div>';
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
				oHtml.find('.J-ls-t-select').change(function(){
					if($(this).attr('data-id') == 0){
						return;
					}
					commitLianmengClubChange(this, {
						id : $(this).attr('data-id'),
						type : $(this).attr('data-type'),
						value : $(this).val()
					});
				});
				oHtml.find('.J-la-delete-btn').click(function(){
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
				oHtml.find('.J-la-add-btn').click(function(){
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
			}
			
			function appendLianmengClubItemHtml(aDataList){
				var listHtml = '';
				for(var i in aDataList){
					var aData = aDataList[i];
					listHtml += '<tr>';
						listHtml += '<td><input type="text" class="J-commit-input form-control" data-id="' + aData.id + '" data-type="club_name" value="' + aData.club_name + '" /></td>';
						listHtml += '<td><input type="text" class="J-commit-input form-control" data-id="' + aData.id + '" data-type="club_id" value="' + aData.club_id + '" /></td>';
						var opitonHtml = '';
						if(aData.duizhangfangfa == 1){
							opitonHtml = '<option value="1">0.975</option><option value="2">无水账单</option>';
						}else{
							opitonHtml = '<option value="2">无水账单</option><option value="1">0.975</option>';
						}
						listHtml += '<td><select class="J-commit-input J-ls-t-select form-control" data-id="' + aData.id + '" data-type="duizhangfangfa">' + opitonHtml + '</select></td>';
						listHtml += '<td><input type="text" class="J-commit-input form-control" data-id="' + aData.id + '" data-type="paiju_fee" value="' + aData.paiju_fee + '" /></td>';
						listHtml += '<td><input type="text" class="J-commit-input form-control" data-id="' + aData.id + '" data-type="baoxian_choucheng" value="' + aData.baoxian_choucheng + '" /></td>';
						listHtml += '<td>' + aData.lianmeng_name + '</td>';
						listHtml += '<td><div class="J-la-delete-btn btn btn-sm btn-danger" data-id="' + aData.id + '">删除</div></td>';
					listHtml += '</tr>';
				}
				listHtml += '<tr>';
					listHtml += '<td><input type="text" class="J-commit-input form-control" data-type="club_name" value="" /></td>';
					listHtml += '<td><input type="text" class="J-commit-input form-control" data-type="club_id" value="" /></td>';
					listHtml += '<td><select class="J-commit-input J-ls-t-select form-control" data-id="0" data-type="duizhangfangfa"><option value="1">0.975</option><option value="2">无水账单</option></select></td>';
					listHtml += '<td><input type="text" class="J-commit-input form-control" data-type="paiju_fee" value="0" /></td>';
					listHtml += '<td><input type="text" class="J-commit-input form-control" data-type="baoxian_choucheng" value="0" /></td>';
					listHtml += '<td class="J-lianmeng-name">&nbsp;</td>';
					listHtml += '<td><div class="J-la-add-btn btn btn-sm btn-primary" data-id="0">添加</div></td>';
				listHtml += '</tr>';
				var oListHtml = $(listHtml);
				oHtml.find('.J-lmjlbst-list-table').append(oListHtml);
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
							if(aResult.data.length != 0){
								appendLianmengClubItemHtml(aResult.data.list);
								oHtml.find('.J-lianmeng-name').text(aResult.data.aLianmeng.name);
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
			});	
		},

		showJiaoBanZhuanChu : function(){
			var html = '';
			html += '<div class="J-data-list-win J-lianmeng-setting-win">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">交班转出</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;min-height:190px;">';
						html += '<div class="h20"></div>';
						html += '<div class="table-responsive" style="padding:0px 10px;">';
							html += '<table class="J-club-list-table table">';
							html += '<tr><th>总抽水</th><th>总保险</th><th>总支出</th><th>交接金额</th><th>转出渠道</th></tr>';
							html += '<tr><td class="J-zhong-chou-shui" style="line-height:34px;border-bottom: 1px solid #ddd;">0</td><td class="J-zhong-bao-xian" style="line-height:34px;border-bottom: 1px solid #ddd;">0</td><td class="J-total-out-put-type-money" style="line-height:34px;border-bottom: 1px solid #ddd;">0</td><td class="J-jiao-ban-zhuan-chu-money" style="line-height:34px;border-bottom: 1px solid #ddd;">0</td><td style="border-bottom: 1px solid #ddd;"><select class="J-zhuan-chu-qidao form-control"></select></td></tr>';
							html += '</table>';
						html += '</div>';
						html += '<div class="J-jbzc-sure-btn btn btn-primary" data-imbalance-money="0" style="margin-left: 290px;">确定</div>';
					html += '</div>';
				html += '</div>';
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
									}, 1);
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
			html += '<div class="J-data-list-win" style="float:left;width:650px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">上桌人数</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h10"></div>';
						html += '<div class="h30 breadcrumb">';
							html += '<div style="float:left;width:300px;height:100%;"></div>';
							html += '<div style="float:right;width:340px;height:100%;"><div class="s-lms-txt">上桌人数: <font class="J-total-shan-zhuo-ren-shu" style="color:#ff5722;">0</font> </div></div>';
						html += '</div>';
					html += '<div class="h10"></div>';
						html += '<div class="table-responsive" style="padding:0px 10px;">';
							html += '<table class="J-szrs-list-table table table-hover table-striped">';
							html += '<tr><th>牌局名</th><th>上桌人数</th></tr>';
							html += '</table>';
						html += '</div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			var oHtml = $(html);
			
			function appendSzrsItemHtml(aDataList){
				var listHtml = '';
				for(var i in aDataList){
					var aData = aDataList[i];
					listHtml += '<tr>';
						listHtml += '<td>' + aData.paiju_name + '</td>';
						listHtml += '<td>' + aData.shang_zhuo_ren_shu + '</td>';
					listHtml += '</tr>';
				}
				var oListHtml = $(listHtml);
				oHtml.find('.J-szrs-list-table').append(oListHtml);
				
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
							if(aResult.data.length != 0){
								appendSzrsItemHtml(aResult.data.list);
								oHtml.find('.J-total-shan-zhuo-ren-shu').text(aResult.data.totalShangZhuoRenShu);
								//oHtml.find('.ls-list-wrap').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
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
			html += '<div class="J-data-list-win" style="float:left;width:650px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">总保险</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h10"></div>';
						html += '<div class="h30 breadcrumb">';
							html += '<div style="float:left;width:300px;height:100%;">';
								html += '<div style="float:left;margin-left:20px;width:70px;height:30px;line-height:30px;color:#333;">保险微调：</div>';
								html += '<input type="text" class="J-baoxian-ajust-value" style="float:left;width:70px;height:18px;line-height:18px;color:#333;text-align: center;color: #f4e2a9;background:#58463d;border-radius: 5px;margin-top: 6px;" value="0" />';
							html += '</div>';
							html += '<div style="float:right;width:340px;height:100%;"><div class="s-lms-txt">总保险: <font class="J-total-baoxian" style="color:#ff5722;">0</font> 元</div></div>';
						html += '</div>';
					html += '<div class="h10"></div>';
						html += '<div class="table-responsive" style="padding:0px 10px;">';
							html += '<table class="J-baoxian-list-table table table-hover table-striped">';
							html += '<tr><th>牌局名</th><th>牌桌保险</th><th>保险被抽</th><th>实际保险</th></tr>';
							html += '</table>';
						html += '</div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			var oHtml = $(html);
			
			function appendLianmengItemHtml(aDataList){
				var listHtml = '';
				for(var i in aDataList){
					var aData = aDataList[i];
					listHtml += '<tr>';
						listHtml += '<td style="cursor:pointer;" onclick="AlertWin.showPaijuDataList(' + i + ');">' + aData.paiju_name + '</td>';
						listHtml += '<td>' + aData.baoxian_heji + '</td>';
						listHtml += '<td>' + aData.baoxian_beichou + '</td>';
						listHtml += '<td>' + aData.shiji_baoxian + '</td>';
					listHtml += '</tr>';
				}
				var oListHtml = $(listHtml);
				oHtml.find('.J-baoxian-list-table').append(oListHtml);
				
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
								//oHtml.find('.ls-list-wrap').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
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
									//reloadList();
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
			});	
		},
		
		showChouShuiList : function(){
			var html = '';
			html += '<div class="J-data-list-win" style="float:left;width:650px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">抽水列表</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h10"></div>';
						html += '<div class="h30 breadcrumb">';
							html += '<div style="float:left;width:300px;height:100%;">';
								html += '<div style="float:left;margin-left:20px;width:70px;height:30px;line-height:30px;color:#333;">抽水微调：</div>';
								html += '<input type="text" class="J-choushui-ajust-value" style="float:left;width:70px;height:18px;line-height:18px;color:#333;text-align: center;color: #f4e2a9;background:#58463d;border-radius: 5px;margin-top: 6px;" value="0" />';
							html += '</div>';
							html += '<div style="float:right;width:340px;height:100%;"><div class="s-lms-txt">总抽水: <font class="J-total-choushui" style="color:#ff5722;">0</font> 元</div><div class="s-lms-txt">牌局总数: <font class="J-total-paiju" style="color:#ff5722;">0</font> </div></div>';
						html += '</div>';
						html += '<div class="h10"></div>';
						html += '<div class="table-responsive" style="padding:0px 10px;">';
							html += '<table class="J-chousui-list-table table table-hover table-striped">';
							html += '<tr><th>牌局名</th><th>战绩</th><th>抽水</th><th>桌子费</th><th>联盟补贴</th><th>实际抽水</th></tr>';
							html += '</table>';
						html += '</div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			var oHtml = $(html);
			
			function appendLianmengItemHtml(aDataList){
				var listHtml = '';
				for(var i in aDataList){
					var aData = aDataList[i];
					listHtml += '<tr>';
						listHtml += '<td onclick="AlertWin.showPaijuDataList(' + aData.paiju_id + ', 1);" style="cursor:pointer;">' + aData.paiju_name + '</td>';
						listHtml += '<td>' + aData.zhanji + '</td>';
						listHtml += '<td>' + aData.choushui_value + '</td>';
						listHtml += '<td>' + aData.paiju_fee + '</td>';
						listHtml += '<td>' + aData.lianmeng_butie + '</td>';
						listHtml += '<td>' + aData.shiji_choushui_value + '</td>';
					listHtml += '</tr>';
				}
				var oListHtml = $(listHtml);
				oHtml.find('.J-chousui-list-table').append(oListHtml);
				
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
								//oHtml.find('.ls-list-wrap').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
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
									//reloadList();
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
			});	
		},
		
		showLianmengZhangDanDetail : function(lianmengId){
			var html = '';
			html += '<div class="J-data-list-win" style="float:left;width:650px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">联盟账单详情</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h10"></div>';
						html += '<div class="h30 breadcrumb">';
							html += '<div style="float:left;width:150px;height:100%;"><div class="lml-select-wrap"><select class="J-lml-select form-control" style="width:120px;margin-left:10px;"></select></div></div>';
							html += '<div style="float:right;width:480px;height:100%;"><div class="J-s-lms-btn btn btn-sm btn-primary" style="float: right;top:-2px;margin-right: 10px;">联盟设置</div><div class="J-s-qinzhan-btn btn btn-sm btn-danger" style="float: right;top:-2px;margin-right: 10px;" data-id="' + lianmengId + '">清账</div><div class="s-lms-txt" style="right:0px;">新账单累计: <font class="J-total-zhan-dan" style="color:#ff5722;">0</font> 元</div><div class="s-lms-txt" style="right:0px;">桌子费合计: <font class="J-total-zhouzi-fee" style="color:#ff5722;">0</font> 元</div></div>';
						html += '</div>';
						html += '<div class="h10"></div>';
						html += '<div class="table-responsive" style="padding:0px 10px;">';
							html += '<table class="J-lmzddd-list-table table table-hover table-striped">';
							html += '<tr><th>牌局名</th><th>战绩</th><th>保险</th><th>桌子费</th><th>保险被抽</th><th>当局账单</th><th>更改联盟</th></tr>';
							html += '</table>';
						html += '</div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			var oHtml = $(html);
			
			function appendLianmengItemHtml(lianmengId, aDataList, aLianmengList){
				var listHtml = '';
				var lianmengSelectHtml = '';
				for(var j in aLianmengList){
					lianmengSelectHtml += '<option value="' + aLianmengList[j].id + '">' + aLianmengList[j].name + '</option>';
				}
				var totalZhouziFee = 0;
				for(var i in aDataList){
					var aData = aDataList[i];
					totalZhouziFee += parseInt(aData.paiju_fee);
					listHtml += '<tr>';
						listHtml += '<td class="J-sh-paijuname" data-id="' + aData.paiju_id + '" style="cursor:pointer;">' + aData.paiju_name + '</td>';
						listHtml += '<td>' + aData.zhanji + '</td>';
						//listHtml += '<td>' + aData.baoxian_heji + '</td>';
						listHtml += '<td>' + aData.fu_baoxian_heji + '</td>';
						listHtml += '<td>' + aData.paiju_fee + '</td>';
						listHtml += '<td>' + aData.baoxian_beichou + '</td>';
						listHtml += '<td>' + aData.zhang_dan + '</td>';
						listHtml += '<td><select class="lml-item-select form-control" data-paiju-id="' + aData.paiju_id + '" data-lianmeng-id="' + aData.lianmeng_id + '">' + lianmengSelectHtml + '</select></td>';
					listHtml += '</tr>';
				}
				var oListHtml = $(listHtml);
				oHtml.find('.J-total-zhouzi-fee').text(totalZhouziFee);
				oHtml.find('.J-lmzddd-list-table').append(oListHtml);
				oHtml.find('.J-lml-select').html(lianmengSelectHtml);
				oHtml.find('.J-lml-select').val(lianmengId);
				oHtml.find('.J-lmzddd-list-table select').each(function(){
					$(this).val($(this).attr('data-lianmeng-id'));
				});
				bindLianmengEvent(oHtml);
				
				return oListHtml;
			}
			
			function bindLianmengEvent(oHtml){
				oHtml.find('.J-lmzddd-list-table select').unbind();
				oHtml.find('.J-lmzddd-list-table select').on('change', function(){
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
							UBox.show(aResult.msg, aResult.status, function(){
								$(document).click();
								AlertWin.showLianmengZhangDanDetail(lianmengId);
							}, 1);
						}
					});
				});
				oHtml.find('.J-lml-select').unbind();
				oHtml.find('.J-lml-select').on('change', function(){
					$(document).click();
					AlertWin.showLianmengZhangDanDetail($(this).val());
					//_loadList($(this).val(), this);
				});
				oHtml.find('.J-sh-paijuname').click(function(){
					$(document).click();
					AlertWin.showPaijuDataList($(this).attr('data-id'), 1);
				});
			}
			
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
							if(aResult.data.length != 0){
								appendLianmengItemHtml(id, aResult.data.list, aResult.data.aLianmengList);
								oHtml.find('.J-total-zhan-dan').text(aResult.data.totalZhangDan);
							}
						}
					}
				});
			}
			
			showAlertWin(oHtml, function(){
				oHtml.find('.J-s-lms-btn').click(function(){
					$(document).click();
					AlertWin.showLianmengSetting();
				});
				oHtml.find('.J-s-qinzhan-btn').click(function(){
					$(document).click();
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
				_loadList(lianmengId);
				
			});	
		},
		
		showLianmengZhongZhang : function(){
			var html = '';
			html += '<div class="J-data-list-win" style="float:left;width:650px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">联盟总账</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h10"></div>';
						html += '<div class="h30 breadcrumb">';
							html += '<div style="float:left;width:300px;height:100%;">';
								html += '<div style="float:left;margin-left:20px;width:100px;height:30px;line-height:30px;color:#333;">联盟总账微调：</div>';
								html += '<input type="text" class="J-lianmeng-zhongzhang-ajust-value" style="float:left;width:70px;height:18px;line-height:18px;color:#ffffff;text-align: center;color: #f4e2a9;background:#58463d;border-radius: 5px;margin-top: 6px;" value="0" />';
							html += '</div>';
							html += '<div style="float:right;width:300px;height:100%;"><div style="line-height:30px;color:#333;padding-left:30px;">所有联盟总账：<font class="J-total-zhong-zhang" style="color:#ff5722;">0</font></div><div class="J-s-lms-btn btn btn-sm btn-primary" style="position: relative;float: right;top: -30px;right: 10px;">联盟设置</div></div>';
						html += '</div>';
						html += '<div class="h10"></div>';
						html += '<div class="table-responsive" style="padding:0px 10px;">';
							html += '<table class="J-lmzz-list-table table table-hover table-striped">';
							html += '<tr><th>联盟名称</th><th>联盟总账单</th><th>上桌人数</th><th>联盟旧账</th><th>新账单累计</th><th>操作</th><th>清账</th></tr>';
							html += '</table>';
						html += '</div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			var oHtml = $(html);
			
			function appendLianmengItemHtml(aDataList){
				var listHtml = '';
				for(var i in aDataList){
					var aData = aDataList[i];
					listHtml += '<tr>';
						listHtml += '<td>' + aData.lianmeng_name + '</td>';
						listHtml += '<td>' + aData.int_float_lianmeng_zhong_zhang + '</td>';
						listHtml += '<td>' + aData.lianmeng_shang_zhuo_ren_shu + '</td>';
						listHtml += '<td><input type="text" class="form-control" data-id="' + aData.lianmeng_id + '" data-type="qian_zhang" value="' + aData.lianmeng_qian_zhang + '" style="float:left;text-align:right;width:100px;" /><span class="i-edit"></span></td>';
						listHtml += '<td>' + aData.lianmeng_zhang_dan + '</td>';
						listHtml += '<td class="J-detail-btn" data-id="' + aData.lianmeng_id + '"><button class="btn btn-sm btn-warning" style="width:100%;">账单详情</button></td>';
						listHtml += '<td class="J-qin-zhang" data-id="' + aData.lianmeng_id + '"><button class="btn btn-sm btn-danger" style="width:100%;">清账</button></td>';
					listHtml += '</tr>';
				}
				var oListHtml = $(listHtml);
				oHtml.find('.J-lmzz-list-table').append(oListHtml);
				
				bindLianmengEvent(oHtml);
				
				return oListHtml;
			}
			
			function bindLianmengEvent(oHtml){
				oHtml.find('.i-edit').click(function(){
					var oTxt = $(this).prev();
					var txt = oTxt.val();
					oTxt.val('');
					oTxt.focus();
					oTxt.val(txt);
					//$(this).prev().focus();
				});
				oHtml.find('.J-detail-btn').click(function(){
					isCloseWinRefresh = false;
					$(document).click();
					AlertWin.showLianmengZhangDanDetail($(this).attr('data-id'));
				});
				oHtml.find('.J-lmzz-list-table .J-qin-zhang').click(function(){
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
				oHtml.find('.J-lmzz-list-table input[data-type=qian_zhang]').keyup(function(e){
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
									}, 1);
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
						isCloseWinRefresh = true;
						if(aResult.status == 1){
							if(aResult.data.length != 0){
								appendLianmengItemHtml(aResult.data.list);
								oHtml.find('.J-total-zhong-zhang').text(aResult.data.totalZhongZhang);
								oHtml.find('.J-lianmeng-zhongzhang-ajust-value').val(aResult.data.lianmengZhongzhangAjustValue);
								//oHtml.find('.ls-list-wrap').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
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
				oHtml.find('.J-s-lms-btn').click(function(){
					isCloseWinRefresh = false;
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
									//reloadList();
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
			});	
		},
		
		showLianmengSetting : function(){
			var html = '';
			html += '<div class="J-data-list-win" style="float:left;width:800px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">联盟设置</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h10"></div>';
						html += '<div class="table-responsive" style="padding:0px 10px;">';
							html += '<table class="J-lmst-list-table table table-hover table-striped">';
							html += '<tr><th>联盟名称</th><th>联盟欠账</th><th style="min-width:120px;">对账方法</th><th>上缴桌费/桌</th><th>保险被抽成</th><th>开桌人名称</th><th>操作</th></tr>';
							html += '</table>';
						html += '</div>';
					html += '</div>';
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
					if($(this).attr('data-id') == 0){
						return;
					}
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
				oHtml.find('.la-add-btn').click(function(){
					var o = this;
					ajax({
						url : Tools.url('home', 'lianmeng/add-lianmeng'),
						data : {
							name : $(o).parent().parent().find('input[data-type=name]').val(),
							qianzhang : $(o).parent().parent().find('input[data-type=qianzhang]').val(),
							duizhangfangfa : $(o).parent().parent().find('select[data-type=duizhangfangfa]').val(),
							paijuFee : $(o).parent().parent().find('input[data-type=paiju_fee]').val(),
							baoxianChoucheng : $(o).parent().parent().find('input[data-type=baoxian_choucheng]').val(),
							paijuCreater : $(o).parent().parent().find('input[data-type=paiju_creater]').val()
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
			}
			
			function appendLianmengItemHtml(aDataList){
				var listHtml = '';
				for(var i in aDataList){
					var aData = aDataList[i];
					listHtml += '<tr>';
						listHtml += '<td><input type="text" class="J-commit-input form-control" data-id="' + aData.id + '" data-type="name" value="' + aData.name + '" /></td>';
						listHtml += '<td><input type="text" class="J-commit-input form-control" data-id="' + aData.id + '" data-type="qianzhang" value="' + aData.qianzhang + '" /></td>';
						//listHtml += '<td><div class="t-type">r</div><a class="i-select"></a></td>';
						var opitonHtml = '';
						if(aData.duizhangfangfa == 1){
							opitonHtml = '<option value="1">0.975</option><option value="2">无水账单</option>';
						}else{
							opitonHtml = '<option value="2">无水账单</option><option value="1">0.975</option>';
						}
						listHtml += '<td><select class="J-commit-input ls-t-select form-control" data-id="' + aData.id + '" data-type="duizhangfangfa">' + opitonHtml + '</select></td>';
						listHtml += '<td><input type="text" class="J-commit-input form-control" data-id="' + aData.id + '" data-type="paiju_fee" value="' + aData.paiju_fee + '" /></td>';
						listHtml += '<td><input type="text" class="J-commit-input form-control" data-id="' + aData.id + '" data-type="baoxian_choucheng" value="' + aData.baoxian_choucheng + '" /></td>';
						listHtml += '<td><input type="text" class="J-commit-input form-control" data-id="' + aData.id + '" data-type="paiju_creater" value="' + aData.paiju_creater + '" /></td>';
						listHtml += '<td style="background:none;"><div class="la-delete-btn btn btn-sm btn-danger" data-id="' + aData.id + '">删除</div></td>';
					listHtml += '</tr>';
				}
				listHtml += '<tr>';
					listHtml += '<td><input type="text" class="form-control" data-type="name" /></td>';
					listHtml += '<td><input type="text" class="form-control" data-type="qianzhang" value="0" /></td>';
					listHtml += '<td style="min-width:120px;"><select class="ls-t-select form-control" data-id="0" data-type="duizhangfangfa"><option value="1">0.975</option><option value="2">无水账单</option></select></td>';
					listHtml += '<td><input type="text" class="form-control" data-type="paiju_fee" value="0" /></td>';
					listHtml += '<td><input type="text" class="form-control" data-type="baoxian_choucheng" value="0" /></td>';
					listHtml += '<td><input type="text" class="form-control" data-type="paiju_creater" value="" /></td>';
					listHtml += '<td style="background:none;"><div class="la-add-btn btn btn-sm btn-primary">添加</div></td>';
				listHtml += '</tr>';
				var oListHtml = $(listHtml);
				oHtml.find('.J-lmst-list-table').append(oListHtml);
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
							if(aResult.data.length != 0){
								appendLianmengItemHtml(aResult.data);
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
			});	
		},
		
		showPaijuDataList : function(paijuId, isAllRecordData){
			var html = '';
			html += '<div class="J-data-list-win" style="float:left;width:1200px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="J-top-pj-title panel-title" style="text-align:center;"></h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h10"></div>';
						html += '<div class="top-up"><div class="breadcrumb J-info-detail" style="margin:0 auto;width:950px;padding:0;text-align:center;"></div></div>';
						html += '<div class="h10"></div>';
						html += '<div class="table-responsive" style="padding:0px 10px;">';
							html += '<table class="J-pjdata-list-table table table-hover table-striped">';
							html += '<tr><th>玩家ID</th><th>玩家昵称</th><th>俱乐部ID</th><th>俱乐部</th><th>买入</th><th>带出</th><th>保险买入</th><th>保险收入</th><th>保险</th><th>俱乐部保险</th><th style="max-width:120px;">保险合计</th><th style="max-width:120px;">战绩</th></tr>';
							html += '</table>';
						html += '</div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			var oHtml = $(html);
			
			function _bulidItemHmtl(aData){
				var html = '';
				for(var i in aData){
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
						html += '<td><input type="text" class="form-control" data-type="baoxian_heji" data-id="' + aData[i].id + '" value="' + aData[i].baoxian_heji + '" style="max-width:120px;color:#ff0000;" /></td>';
						html += '<td><input type="text" class="form-control" data-type="zhanji" data-id="' + aData[i].id + '" value="' + aData[i].zhanji + '" style="max-width:120px;color:#ff0000;" /></td>';
					html += '</tr>';
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
							$('.J-top-pj-title').text(aRecord.paiju_type);
							oHtml.find('.J-info-detail').html('<a>牌局类型:</a><a class="val">' + aRecord.paiju_type + '</a><a>牌局名:</a><a class="val">' + aRecord.paiju_name + '</a><a>创建者:</a><a class="val">' + aRecord.paiju_creater + '</a><a>盲注:</a><a class="val">' + aRecord.mangzhu + '</a><a>牌桌:</a><a class="val">' + aRecord.paizuo + '</a><a>牌局时长:</a><a class="val">' + aRecord.paiju_duration + '</a><a>总手数:</a><a class="val">' + aRecord.zongshoushu + '</a><a>结束时间:</a><a class="val">' + aRecord.end_time_format + '</a>');
							oHtml.find('.J-pjdata-list-table').append(_bulidItemHmtl(aResult.data.list));
							//oHtml.find('.body-list').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
						}else{
							UBox.show(aResult.msg, aResult.status);
						}
					}
				});
			});
		},
		
		showAddPlayer : function(){
			$(document).click();
			var html = '';
			html += '<div class="J-data-list-win" style="float:left;width:1200px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">添加新客户</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h10"></div>';
						html += '<div class="table-responsive" style="padding:0px 10px;">';
							html += '<table class="J-keren-list-table table table-hover table-striped">';
							html += '<tr><th>客人编号</th><th>本金</th><th>游戏名字</th><th>赢抽点数</th><th>输返点数</th><th>代理人</th><th>玩家ID</th><th>操作</th></tr>';
							html += '<tr>';
								html += '<td><input type="text" class="form-control" data-type="keren_bianhao" /></td>';
								html += '<td><input type="text" class="form-control" data-type="benjin" /></td>';
								html += '<td><input type="text" class="form-control" data-type="player_name" /></td>';
								html += '<td><input type="text" class="form-control" data-type="ying_chou" /></td>';
								html += '<td><input type="text" class="form-control" data-type="shu_fan" /></td>';
								var agentListHtml = '';
								agentListHtml += '<select class="J-agent-select-change form-control" style="min-width:120px;">';
								agentListHtml += '<option value="0">请选择</option>';
								for(var k in aAgentList){
									agentListHtml += '<option value="' + aAgentList[k].id + '">' + aAgentList[k].agent_name + '</option>';
								}
								agentListHtml += '</select>';
								html += '<td>' + agentListHtml + '</td>';
								html += '<td><input type="text" class="form-control" data-type="play_id" /></td>';
								html += '<td><a class="J-add-btn btn btn-sm btn-primary">添加</a></td>';
							html += '</tr>';
							html += '</table>';
						html += '</div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			
			var oHtml = $(html);
			
			showAlertWin(oHtml, function(){
				
				oHtml.find('.J-add-btn').click(function(){
					var o = this;
					ajax({
						url : Tools.url('home', 'index/add-keren'),
						data : {
							kerenBianhao : $(o).parent().parent().find('input[data-type=keren_bianhao]').val(),
							benjin : $(o).parent().parent().find('input[data-type=benjin]').val(),
							playerName : $(o).parent().parent().find('input[data-type=player_name]').val(),
							yingChou : $(o).parent().parent().find('input[data-type=ying_chou]').val(),
							shuFan : $(o).parent().parent().find('input[data-type=shu_fan]').val(),
							agentId : $(o).parent().parent().find('.J-agent-select-change').val(),
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
								UBox.show(aResult.msg, aResult.status, function(){
									location.reload();
								}, 1);
							}else{
								UBox.show(aResult.msg, aResult.status);
							}
						}
					});
				});
			});
		},
		
		showPlayerList : function(){
			var html = '';
			html += '<div class="J-data-list-win" style="float:left;width:1200px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">客人详情</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h10"></div>';
						html += '<div class="h30">';
							html += '<button class="btn btn-primary J-add-player" onclick="AlertWin.showAddPlayer();" style="float:right;margin-right:10px;">添加新客户</button>';
							html += '<input type="text" class="J-search-krbh form-control" style="float:right;width:150px;margin-right:10px;" placeholder="请输入客人编号" />';
						html += '</div>';
						html += '<div class="h10"></div>';
						html += '<div class="table-responsive" style="padding:0px 10px;">';
							html += '<table class="J-keren-list-table table table-hover table-striped">';
							html += '<tr><th class="J-krbh-sort" style="cursor:pointer;">客人编号</th><th class="J-bj-sort" style="cursor:pointer;">本金</th><th>游戏名字</th><th>赢抽点数</th><th>输返点数</th><th>代理人</th><th>备注</th><th>操作</th></tr>';
							html += '</table>';
						html += '</div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			
			var oHtml = $(html);
			
			showAlertWin(oHtml, function(){
				oKerenListObject = new KerenList({oWrapDom : oHtml.find('.J-keren-list-table')});
				oKerenListObject.show(1);
				$('.J-alert-win-wrap').scroll(function(){
					var page = oKerenListObject.oWrapDom.attr('data-page');
					oKerenListObject.show(parseInt(page) + 1);
				});
				
				var tt = '';
				oHtml.find('.J-search-krbh').keyup('input propertychange', function(){
					var ooo = this;
					clearTimeout(tt);
					tt = setTimeout(function(){
						if($(ooo).val() == ''){
							oKerenListObject.aExtentParam.kerenBianhao = 0;
						}else{
							oKerenListObject.aExtentParam.kerenBianhao = $(ooo).val();
						}
						oKerenListObject.show(1);
					}, 500);
				});
				oHtml.find('.J-krbh-sort').click(function(){
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
				oHtml.find('.J-bj-sort').click(function(){
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
							}, 1);
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
			html += '<div class="J-data-list-win" style="float:left;width:650px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">支出类型管理</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h10"></div>';
						html += '<div class="table-responsive" style="padding:0px 10px;">';
							html += '<table class="J-lmst-list-table table table-hover table-striped">';
							html += '<tr><th>支出方式</th><th>金额</th><th>操作</th></tr>';
							for(var i in aMoneyOutPutTypeList){
								html += '<tr><td>' + aMoneyOutPutTypeList[i].out_put_type + '</td><td>' + aMoneyOutPutTypeList[i].money + '</td><td><button class="J-delete btn btn-sm btn-danger" data-id="' + aMoneyOutPutTypeList[i].id + '">删除</button></td></tr>';
							}
							html += '<tr><td><input type="text" class="form-control J-pay-type" placeholder="请输入支出方式" /></td><td><input type="text" class="form-control J-money" placeholder="请输入金额" /></td><td><button class="J-add-btn btn btn-sm btn-primary" data-id="0">添加</button></td></tr>';
							html += '</table>';
						html += '</div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			
			var oHtml = $(html);
			
			oHtml.find('.J-delete').on('click', function(){
				if(confirm('确定删除？')){
					var o = this;
					var aId = [$(o).attr('data-id')];
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
								}, 1);
							}else{
								UBox.show(aResult.msg, aResult.status);
							}
						}
					});
				}
			});
			oHtml.find('.J-add-btn').on('click', function(){
				var o = this;
				ajax({
					url : Tools.url('home', 'money-out-put-type/save'),
					data : {
						outPutType : $(o).parent().parent().find('.J-pay-type').val(),
						money : $(o).parent().parent().find('.J-money').val()
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
			});
			/*oHtml.find('.J-money').bind('input propertychange', function() {  
				setInputInterval(this);
			});*/
			showAlertWin(oHtml, function(){
				
			});
		},
		
		showMoneyTypeList : function(aMoneyTypeList){
			var html = '';
			html += '<div class="J-data-list-win" style="float:left;width:650px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">支付类型管理</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h10"></div>';
						html += '<div class="table-responsive" style="padding:0px 10px;">';
							html += '<table class="J-lmst-list-table table table-hover table-striped">';
							html += '<tr><th>支付方式</th><th>金额</th><th>操作</th></tr>';
							for(var i in aMoneyTypeList){
								html += '<tr><td>' + aMoneyTypeList[i].pay_type + '</td><td>' + aMoneyTypeList[i].money + '</td><td><button class="J-delete btn btn-sm btn-danger" data-id="' + aMoneyTypeList[i].id + '">删除</button></td></tr>';
							}
							html += '<tr><td><input type="text" class="form-control J-pay-type" placeholder="请输入支付方式" /></td><td><input type="text" class="form-control J-money" placeholder="请输入金额" /></td><td><button class="J-add-btn btn btn-sm btn-primary" data-id="0">添加</button></td></tr>';
							html += '</table>';
						html += '</div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			
			var oHtml = $(html);
			
			oHtml.find('.J-delete').on('click', function(){
				if(confirm('确定删除？')){
					var o = this;
					var aId = [$(o).attr('data-id')];
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
								}, 1);
							}else{
								UBox.show(aResult.msg, aResult.status);
							}
						}
					});
				}
			});
			oHtml.find('.J-add-btn').on('click', function(){
				var o = this;
				ajax({
					url : Tools.url('home', 'money-type/save'),
					data : {
						payType : $(o).parent().parent().find('.J-pay-type').val(),
						money : $(o).parent().parent().find('.J-money').val()
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
			});
			/*oHtml.find('.J-money').bind('input propertychange', function() {  
				setInputInterval(this);
			});*/
			showAlertWin(oHtml, function(){
				
			});
		},
		
		showAddClub : function(){
			var html = '';
			html += '<div class="J-data-list-win" style="float:left;width:650px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">添加俱乐部</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px 50px;">';
						html += '<div class="h20"></div>';
						
						html += '<div class="form-group">';
							html += '<label>俱乐部名称</label>';
							html += '<input type="text" class="J-input J-club-name form-control" value="" />';
						html += '</div>';
						
						html += '<div class="form-group">';
							html += '<label>俱乐部ID</label>';
							html += '<input type="text" class="J-input J-club-id form-control" value="" />';
						html += '</div>';
						
						html += '<div class="form-group">';
							html += '<label>登录账户</label>';
							html += '<input type="text" class="J-input J-club-login-name form-control" value="" />';
						html += '</div>';
						
						html += '<div class="form-group">';
							html += '<label>登录密码</label>';
							html += '<input type="text" class="J-input J-club-login-password form-control" value="" />';
						html += '</div>';
						html += '<div class="btn btn-sm btn-primary J-new-btn">保存</div>';
						html += '<div class="h20"></div>';
					html += '</div>';
				html += '</div>';
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
							}, 1);
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
			html += '<div class="J-data-list-win" style="float:left;width:650px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">我的俱乐部</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px 50px;">';
						html += '<div class="h20"></div>';
						
						html += '<div class="form-group">';
							html += '<label>俱乐部名称</label>';
							html += '<input type="text" class="J-input J-club-name form-control" value="' + aClub.club_name + '" />';
						html += '</div>';
						
						html += '<div class="form-group">';
							html += '<label>俱乐部ID</label>';
							html += '<input type="text" class="J-input J-club-id form-control" value="' + aClub.club_id + '" />';
						html += '</div>';
						
						html += '<div class="form-group">';
							html += '<label>登录账户</label>';
							html += '<input type="text" class="J-input J-club-login-name form-control" value="' + aClub.club_login_name + '" />';
						html += '</div>';
						
						html += '<div class="form-group">';
							html += '<label>登录密码</label>';
							html += '<input type="text" class="J-input J-club-login-password form-control" value="' + aClub.club_login_password + '" />';
						html += '</div>';
						html += '<div class="btn btn-sm btn-danger J-del-btn">删除</div>';
						html += '<div class="btn btn-sm btn-primary J-edit-btn" style="margin-left:20px;">保存</div>';
						html += '<div class="h20"></div>';
					html += '</div>';
				html += '</div>';
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
								}, 1);
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
							}, 1);
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
			html += '<div class="J-data-list-win" style="float:left;width:650px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">俱乐部设置</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px 50px;">';
						html += '<div class="h20"></div>';
						
						html += '<div class="form-group">';
							html += '<label>俱乐部账号</label>';
							html += '<input type="text" class="J-input J-login-name form-control" value="' + aUser.login_name + '" />';
						html += '</div>';
						
						html += '<div class="form-group">';
							html += '<label>俱乐部密码</label>';
							html += '<input type="text" class="J-input J-password form-control" value="' + aUser.password + '" />';
						html += '</div>';
						
						html += '<div class="form-group">';
							html += '<label>起步抽水</label>';
							html += '<input type="text" class="J-input J-qibu-choushui form-control" value="' + aUser.qibu_choushui + '" />';
						html += '</div>';
						
						html += '<div class="form-group">';
							html += '<label>抽水算法</label>';
							html += '<select class="form-control J-choushui-shuanfa" data-value="">';
								html += '<option value="1">四舍五入</option>';
								html += '<option value="2">余数抹零</option>';
							html += '</select>';
						html += '</div>';
						html += '<div class="btn btn-sm btn-primary J-save-btn">保存</div>';
						html += '<div class="h20"></div>';
					html += '</div>';
				html += '</div>';
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
						choushuiShuanfa : oHtml.find('.J-choushui-shuanfa').val()
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
			});
			showAlertWin(oHtml, function(){
				oHtml.find('.J-choushui-shuanfa').val(aUser.choushui_shuanfa);
			});
		},
		
		showPaijuList : function(aParam){
			var html = '';
			html += '<div class="J-data-list-win" style="float:left;width:1200px;min-height:423px;">';
				html += '<div class="panel panel-primary">';
					html += '<div class="panel-heading">';
						html += ' <h3 class="panel-title" style="text-align:center;">账单列表</h3>';
					html += '</div>';
					html += '<div class="panel-body" style="padding:0px;">';
						html += '<div class="h10"></div>';
						html += '<div class="h30 breadcrumb">';
							html += '<div style="float:left;width:200px;height:100%;"></div>';
							html += '<div style="float:right;width:400px;height:100%;"><div class="s-lms-txt">牌局总数: <font class="J-paiju-count" style="color:#ff5722;">0</font></div></div>';
						html += '</div>';
						html += '<div class="h10"></div>';
						html += '<div class="J-paiju-list-wrap"></div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
			
			var oHtml = $(html);
			showAlertWin(oHtml, function(){
				oPaijuListObject = new PaijuList({oWrapDom : oHtml.find('.J-paiju-list-wrap')});
				if(aParam && typeof(aParam.isHistory)){
					oPaijuListObject.aExtentParam.isHistory = 1;
				}
				oPaijuListObject.show(1);
				$('.J-alert-win-wrap').scroll(function(){
					var page = oPaijuListObject.oWrapDom.attr('data-page');
					oPaijuListObject.show(parseInt(page) + 1);
				});
				/*oPaijuListObject.oScrollBar = oHtml.find('.content-body').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
				oPaijuListObject.oScrollBar.scrollEndEventFunc = function(){
					var page = oPaijuListObject.oWrapDom.attr('data-page');
					oPaijuListObject.show(parseInt(page) + 1);
				}*/
			});
		}
				
	}
})(window, jQuery);