(function(container, $){
	container.AlertWin = {
		showPlayerList : function(){
			var html = '';
			html += '<div class="J-player-list-win">';
				html += '<div class="p-l-head">';
					html += '<input type="text" class="search-krbh" />';
					html += '<input type="text" class="add-player" />';
				html += '</div>';
				html += '<div class="p-l-title"></div>';
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
						oKerenListObject.show(1, {kerenBianhao : 0});
					}else{
						oKerenListObject.show(1, {kerenBianhao : $(this).val()});
					}
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
		
		showPaijuList : function(aList){
			var html = '';
			
			html += '<div class="J-bill-list-win">';
				html += '<div class="content-body">';
					html += '<div class="item-wrap">';
					for(var i in aList){
						html += '<div class="c-b-list-item ' + (aList[i].status == 0 ? 'new' : '') + '">';
							html += '<div class="c-b-l-i-title">' + aList[i].paiju_name + '</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>' + aList[i].hedui_shuzi + '</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status ' + (aList[i].status == 1 ? 'l-clean' : '') + '"></a>';
							html += '</div>';
						html += '</div>';
					}
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
				oHtml.find('.content-body').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 5});
			});
		}
				
	}
})(window, jQuery);