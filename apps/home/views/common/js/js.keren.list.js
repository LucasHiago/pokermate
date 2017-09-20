(function(container, $){
	container.KerenList = function(aOptions){
		var oList = this;
		
		this.oWrapDom = $('#wrapPage');
		this.url = Tools.url('home', 'index/get-keren-list');
		this.page = 1;
		this.pageSize = 20;
		this.aExtentParam = {};
		this.isNoMoreData = false;
		this.aCacheData = {};
		this.oScrollBar = {};
		
		if(aOptions){
			if(typeof(aOptions.oWrapDom) != 'undefined'){
				this.oWrapDom = aOptions.oWrapDom;
			}
			if(typeof(aOptions.url) != 'undefined'){
				this.url = aOptions.url;
			}
			if(typeof(aOptions.page) != 'undefined'){
				this.page = aOptions.page;
			}
			if(typeof(aOptions.pageSize) != 'undefined'){
				this.pageSize = aOptions.pageSize;
			}
		}
		
		this.show = function(page, aParam){
			var oThis = this;
			if(_isAjaxing()){
				return;
			}
			var aData = {page : page, pageSize : oThis.pageSize};
			if(aParam){
				oThis.aExtentParam = aParam;
			}
			$.extend(aData, oThis.aExtentParam);
			_lockAjax();
			ajax({
				url : oThis.url,
				data : aData,
				error : function(){
					_unlockAjax();
				},
				success : function(aResult){
					_unlockAjax();
					if(aResult.status == 1){
						oThis.oWrapDom.attr('data-page', aData.page);
						if(aData.page == 1){
							oThis.oWrapDom.html('');
						}
						if(aResult.data.length != 0){
							_appendList(aResult.data);
						}
					}else{
						UBox.show(aResult.msg, aResult.status);
					}
				}
			});
		};
		
		/*$(document).on('scroll', function(){
			if($(this).scrollTop() == $(document).height() - $(window).height()){
				oList.show();
			}
		});*/
		
		function _appendList(aData){
			var html = '';
			for(var i in aData){
				html += '<div class="p-l-item">';
					html += '<div style="width:58px;" class="c-td"></div>';
					html += '<div style="width:108px;" class="c-td"><input type="text" data-type="keren_bianhao" data-record-id="' + aData[i].id + '" value="' + aData[i].keren_bianhao + '" /></div>';
					html += '<div style="width:33px;" class="c-td"></div>';
					html += '<div style="width:174px;" class="c-td"><input type="text" data-type="benjin" data-record-id="' + aData[i].id + '" value="' + aData[i].benjin + '" /></div>';
					var playerListHtml = '';
					//if(aData[i].player_list.length != 0){
						/*playerListHtml += '<div class="play-select-list"><div class="p-s-wrap">';
						for(var j in aData[i].player_list){
							playerListHtml += '<div class="h10"></div>';
							playerListHtml += '<div class="play-select-list-item" data-type="player_id" data-record-id="' + aData[i].id + '" data-id="' + aData[i].player_list[j].id + '">' + aData[i].player_list[j].player_name + '</div>';
						}
						playerListHtml += '</div></div>';*/
						playerListHtml += '<select class="J-player-select-change" data-record-id="' + aData[i].id + '" style="padding: 2px; color: #ffffff; background: #221a3c; height: 35px; width: 70%; text-align: center; margin-left: 32px;">';
						for(var j in aData[i].player_list){
							playerListHtml += '<option value="' + aData[i].player_list[j].id + '">' + aData[i].player_list[j].player_name + '</option>';
						}
						playerListHtml += '</select>';
					//}
					//html += '<div style="width:170px;cursor:pointer;" class="J-select-play c-td" data-id="' + (aData[i].player_list.length != 0 ? aData[i].player_list[0].id : 0) + '"><div style="width:120px;text-align:right;">' + (aData[i].player_list.length != 0 ? aData[i].player_list[0].player_name : '') + '</div>' + playerListHtml + '</div>';
					html += '<div style="width:170px;cursor:pointer;" class="J-select-play c-td" data-id="' + (aData[i].player_list.length != 0 ? aData[i].player_list[0].id : 0) + '">' + playerListHtml + '</div>';
					html += '<div style="width:156px;" class="c-td">';
						html += '<input type="text" style="float:left;display:block;width:94px;height:100%;text-align: right;" data-record-id="' + aData[i].id + '" data-type="ying_chou" value="' + aData[i].ying_chou + '" />';
						html += '<span style="float:left;display:block;width:10px;height:100%;">%</span>';
						html += '<a class="edit-icn" style="float:left;display:block;width:43px;height:100%;cursor:pointer;"></a>';
					html += '</div>';
					html += '<div style="width:157px;" class="c-td">';
						html += '<input type="text" style="float:left;display:block;width:102px;height:100%;text-align: right;" data-record-id="' + aData[i].id + '" data-type="shu_fan" value="' + aData[i].shu_fan + '" />';
						html += '<span style="float:left;display:block;width:10px;height:100%;">%</span>';
						html += '<a class="edit-icn" style="float:left;display:block;width:43px;height:100%;cursor:pointer;"></a>';
					html += '</div>';
					var agentListHtml = '';
					//var agentName = '请选择';
					//if(aAgentList.length != 0){
						/*agentListHtml += '<div class="play-select-list"><div class="p-s-wrap">';
						for(var k in aAgentList){
							if(aAgentList[k].id == aData[i].agent_id){
								agentName = aAgentList[k].agent_name;
							}
							agentListHtml += '<div class="h10"></div>';
							agentListHtml += '<div class="play-select-list-item" data-type="agent_id" data-record-id="' + aData[i].id + '" data-id="' + aAgentList[k].id + '">' + aAgentList[k].agent_name + '</div>';
						}
						agentListHtml += '</div></div>';*/
						agentListHtml += '<select class="J-agent-select-change" data-init-id="' + aData[i].agent_id + '" data-record-id="' + aData[i].id + '" style="padding: 2px; color: #ffffff; background: #221a3c; height: 35px; width: 70%; text-align: center; margin-left: 32px;">';
						agentListHtml += '<option value="0">请选择</option>';
						for(var k in aAgentList){
							agentListHtml += '<option value="' + aAgentList[k].id + '">' + aAgentList[k].agent_name + '</option>';
						}
						agentListHtml += '</select>';
					//}
					html += '<div style="width:154px;cursor:pointer;" class="J-select-play c-td" data-id="' + aData[i].agent_id + '">' + agentListHtml + '</div>';
					html += '<div style="width:157px;" class="c-td"><input type="text" data-type="remark" data-record-id="' + aData[i].id + '" value="' + aData[i].remark + '" /></div>';
					html += '<div style="width:116px;" class="c-td"><a class="del-btn" data-record-id="' + aData[i].id + '" style="position: relative;left: 16px;top: 8px;display: block;width: 78px;height: 33px;cursor:pointer;"></a></div>';
				html += '</div>';
			}
			var oHtml = $(html);
			oList.oWrapDom.append(oHtml);
			//oList.oScrollBar.update('relative');
			_bindHtmlEvent(oHtml);
		}
		
		function _bindHtmlEvent(oHtml){
			oHtml.find('.J-player-select-change').on('change', function(){
				_updateRecordPlayerIdValue($(this).attr('data-record-id'), $(this).val());
			});
			oHtml.find('.J-agent-select-change').on('change', function(){
				_updateRecordAgentIdValue($(this).attr('data-record-id'), $(this).val());
			});
			oHtml.find('.J-agent-select-change').each(function(){
				$(this).val($(this).attr('data-init-id'));
			});
			/*oHtml.find('.J-select-play').click(function(){
				$('.J-alert-win-wrap').find('.play-select-list').hide();
				$(this).find('.play-select-list').show();
			});
			oHtml.find('.play-select-list').each(function(){
				$(this).show();
				$(this).css({left : 120});
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
				if($(this).attr('data-type') == 'player_id'){
					_updateRecordPlayerIdValue($(this).attr('data-record-id'), $(this).attr('data-id'));
				}
				if($(this).attr('data-type') == 'agent_id'){
					_updateRecordAgentIdValue($(this).attr('data-record-id'), $(this).attr('data-id'));
				}
			});
			oHtml.find('.play-select-list .p-s-wrap').each(function(){
				$(this).parent().show();
				$(this).tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 10});
				$(this).parent().hide();
			});*/
			oHtml.find('.del-btn').click(function(){
				_deleteRecord(this);
			});
			oHtml.find('.edit-icn').click(function(){
				$(this).parent().find('input').focus();
			});
			oHtml.find('input').keyup(function(e){
				if(e.keyCode == 13){
					_updateRecordValue(this);
				}
			});
		}
		
		function _updateRecordValue(o, isMerge){
			var aData = {
				id : $(o).attr('data-record-id'),
				type : $(o).attr('data-type'),
				value : $(o).val()
			};
			if(isMerge){
				aData.isMerge = 1;
			}
			ajax({
				url : Tools.url('home', 'index/update-keren-info'),
				data : aData,
				beforeSend : function(){
					$(o).attr('disabled', 'disabled');
				},
				complete : function(){
					$(o).attr('disabled', false);
				},
				success : function(aResult){
					if(aResult.status == 2){
						if(confirm(aResult.msg)){
							_updateRecordValue(o, 1);
						}
						return;
					}
					if(aResult.status == 1){
						if(aResult.data == 'reload'){
							UBox.show(aResult.msg, aResult.status, function(){
								location.reload();
							}, 3);
							return;
						}
						$(o).val(aResult.data);
					}
					UBox.show(aResult.msg, aResult.status);
				}
			});
		}
			
		function _updateRecordAgentIdValue(id, value){
			ajax({
				url : Tools.url('home', 'index/update-keren-agent-id'),
				data : {
					id : id,
					value : value
				},
				beforeSend : function(){
					//$(o).attr('disabled', 'disabled');
				},
				complete : function(){
					//$(o).attr('disabled', false);
				},
				success : function(aResult){
					UBox.show(aResult.msg, aResult.status);
				}
			});
		}
			
		function _updateRecordPlayerIdValue(id, playerId){
			ajax({
				url : Tools.url('home', 'index/update-keren-player-id'),
				data : {
					id : id,
					playerId : playerId
				},
				beforeSend : function(){
					//$(o).attr('disabled', 'disabled');
				},
				complete : function(){
					//$(o).attr('disabled', false);
				},
				success : function(aResult){
					
				}
			});
		}
			
		function _deleteRecord(o){
			if(confirm('确定删除？')){
				ajax({
					url : Tools.url('home', 'index/delete-keren'),
					data : {
						id : $(o).attr('data-record-id')
					},
					beforeSend : function(){
						$(o).attr('disabled', 'disabled');
					},
					complete : function(){
						$(o).attr('disabled', false);
					},
					success : function(aResult){
						if(aResult.status == 1){
							$(o).parent().parent().remove();
						}
						UBox.show(aResult.msg, aResult.status);
					}
				});
			}
		}
		
		function _isAjaxing(){
			return oList.oWrapDom.attr('isAjaxing') == 1 ? true : false;
		}
		
		function _lockAjax(){
			oList.oWrapDom.attr('isAjaxing', 1);
		}
		
		function _unlockAjax(){
			oList.oWrapDom.removeAttr('isAjaxing');
		}
		
	}
})(window, jQuery);