(function(container, $){
	container.AlertWin = {
		showPaijuDataList : function(paijuId){
			var html = '';
			html += '<div class="J-paiju-data-list-win">';
				html += '<div class="top-title"></div>';
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
							html += '<td style="color:#ff0000;"><input type="text" data-type="zhanji" data-id="' + aData[i].id + '" value="' + aData[i].zhanji + '" style="text-align:center;height:50px;line-height:50px;max-width:80px;float:left;display:block;" /><a class="edit-icn"></a></td>';
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
				ajax({
					url : Tools.url('home', 'import/get-paiju-data-list'),
					data : {
						paijuId : paijuId
					},
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
							oHtml.find('.body-list').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 5});
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
					$(this).tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 5});
					$(this).parent().hide();
				});
				oHtml.find('.add-btn').click(function(){
					console.log($(this).parent().parent().find('input[data-type=keren_bianhao]').val());
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
				oKerenListObject.oScrollBar = oHtml.find('.p-l-body').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 5});
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
					success : function(aResult){console.log(1);
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
				oHtml.find('.m-t-l-list').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 5});
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
				oHtml.find('.m-t-l-list').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 5});
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
				oPaijuListObject.oScrollBar = oHtml.find('.content-body').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 5});
				oPaijuListObject.oScrollBar.scrollEndEventFunc = function(){
					var page = oPaijuListObject.oWrapDom.attr('data-page');
					oPaijuListObject.show(parseInt(page) + 1);
				}
			});
		}
				
	}
})(window, jQuery);