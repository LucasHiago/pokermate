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
							oThis.oWrapDom.find('.J-kr-row').remove();
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
				var hasAgent = false;
				var fontColorGreen = '';
				if(aData[i].agent_id != 0){
					hasAgent = true;
					fontColorGreen = ' color:#0000ff; ';
				}
				html += '<tr class="J-kr-row">';
					html += '<td><input type="text" class="form-control" data-type="keren_bianhao" data-record-id="' + aData[i].id + '" value="' + aData[i].keren_bianhao + '" style="' + fontColorGreen + '" placeholder="客人编号" /></td>';
					html += '<td><input type="text" class="form-control" data-type="benjin" data-record-id="' + aData[i].id + '" value="' + aData[i].benjin + '" style="' + fontColorGreen + '" placeholder="本金" /></td>';
					var playerListHtml = '';
					playerListHtml += '<select class="J-player-select-change form-control" data-record-id="' + aData[i].id + '" style="min-width:160px;' + fontColorGreen + '">';
					for(var j in aData[i].player_list){
						playerListHtml += '<option value="' + aData[i].player_list[j].id + '">' + aData[i].player_list[j].player_name + '</option>';
					}
					playerListHtml += '</select>';
					html += '<td style="cursor:pointer;" class="J-select-play" data-id="' + (aData[i].player_list.length != 0 ? aData[i].player_list[0].id : 0) + '">' + playerListHtml + '</td>';
					html += '<td><div style="float:left;height:32px;"><input type="text" class="form-control" style="max-width: 100px;' + fontColorGreen + '" data-record-id="' + aData[i].id + '" data-type="ying_chou" value="' + aData[i].ying_chou + '" placeholder="赢抽点数" /><span style="float: right;position: relative;top: -26px;right: 6px;">%</span></div></td>';
					html += '<td><div style="float:left;height:32px;"><input type="text" class="form-control" style="max-width: 100px;' + fontColorGreen + '" data-record-id="' + aData[i].id + '" data-type="shu_fan" value="' + aData[i].shu_fan + '" placeholder="输返点数" /><span style="float: right;position: relative;top: -26px;right: 6px;">%</span></div></td>';
					html += '<td><input type="text" class="form-control" style="max-width: 100px;' + fontColorGreen + '" data-record-id="' + aData[i].id + '" data-type="ying_fee" value="' + aData[i].ying_fee + '" placeholder="赢收台费" /></td>';
					html += '<td><input type="text" class="form-control" style="max-width: 100px;' + fontColorGreen + '" data-record-id="' + aData[i].id + '" data-type="shu_fee" value="' + aData[i].shu_fee + '" placeholder="输返台费" /></td>';
					var agentListHtml = '';
					agentListHtml += '<select class="J-agent-select-change form-control" data-init-id="' + aData[i].agent_id + '" data-record-id="' + aData[i].id + '" style="min-width:130px;' + fontColorGreen + '">';
					agentListHtml += '<option value="0">请选择</option>';
					for(var k in aAgentList){
						agentListHtml += '<option value="' + aAgentList[k].id + '">' + aAgentList[k].agent_name + '</option>';
					}
					agentListHtml += '</select>';
					html += '<td class="J-select-play" data-id="' + aData[i].agent_id + '">' + agentListHtml + '</td>';
					html += '<td><input type="text" class="form-control" data-type="remark" data-record-id="' + aData[i].id + '" value="' + aData[i].remark + '" style="' + fontColorGreen + '" placeholder="备注" /></td>';
					html += '<td style="width:190px;"><a class="J-merge-btn btn btn-sm btn-primary" data-record-id="' + aData[i].id + '" data-keren-bianhao="' + aData[i].keren_bianhao + '">合并</a>&nbsp;<a class="J-del-btn btn btn-sm btn-danger" data-record-id="' + aData[i].id + '">删除</a>&nbsp;<a href="' + Tools.url('home', 'keren-benjin-manage/export-keren-last-paiju-data') + '?kerenBianhao=' + aData[i].keren_bianhao + '" class="btn btn-sm btn-primary">导出记录</a></td>';
				html += '</tr>';
			}
			var oHtml = $(html);
			oList.oWrapDom.append(oHtml);
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
			
			oHtml.find('.J-del-btn').click(function(){
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
			oHtml.find('.J-merge-btn').click(function(){
				var o = this;
				AlertWin.showMergeKeren(o, function(o){
					_updateRecordValue(o, false, 1);
				});
			});
		}
		
		function _updateRecordValue(o, isMerge, isMergeKerenBianhao){
			var aData = {
				id : $(o).attr('data-record-id'),
				type : $(o).attr('data-type'),
				value : $(o).val()
			};
			if(isMerge){
				aData.isMerge = 1;
			}
			if(isMergeKerenBianhao){
				aData.isMergeKerenBianhao = 1;
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
							_updateRecordValue(o, 1, isMergeKerenBianhao);
						}
						return;
					}
					if(aResult.status == 1){
						if(isMergeKerenBianhao){
							$(document).click();
							AlertWin.showPlayerList();
							return;
						}
						if(aResult.data == 'reload'){
							UBox.show(aResult.msg, aResult.status, function(){
								location.reload();
							}, 1);
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
			if(confirm('删除用户将清空该用户在代理中的结算记录，是否确认删除？')){
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