(function(container, $){
	container.AlertWin = {
		
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
		
		showBillList : function(aParam){
			var html = '';
			
			html += '<div class="J-bill-list-win">';
				html += '<div class="content-body">';
					html += '<div class="item-wrap">';
					
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						html += '<div class="c-b-list-item">';
							html += '<div class="c-b-l-i-title">2017-02-03</div>';
							html += '<div class="c-b-l-i-bottom">';
								html += '<a class="l-text"><span>核对数字</span><span>99</span></a>';
								html += '<a class="l-edit"></a>';
								html += '<a class="l-status"></a>';
							html += '</div>';
						html += '</div>';
						
					html += '</div>';
				html += '</div>';
				html += '<div class="content-bottom">';
					html += '<div class="l-btn"></div>';
					html += '<div class="c-txt">1/10</div>';
					html += '<div class="r-btn"></div>';
				html += '</div>';
			html += '</div>';
			
			var oHtml = $(html);
			showAlertWin(oHtml, function(){
				
			});
		}
				
	}
})(window, jQuery);