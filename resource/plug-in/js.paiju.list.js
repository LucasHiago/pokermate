(function(container, $){
	container.PaijuList = function(aOptions){
		var oList = this;
		
		this.oWrapDom = $('#wrapPage');
		this.url = Tools.url('home', 'index/get-paiju-list');
		this.page = 1;
		this.pageSize = 50;
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
							_appendList(aResult.data.list);
							$('.J-paiju-count').text(aResult.data.count);
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
				html += '<div class="panel panel-' + (aData[i].status == 0 ? 'green' : 'yellow') + ' paiju-item">';
					html += '<div class="panel-heading">';
						html += '<h3 class="panel-title" onclick="AlertWin.showPaijuDataList(' + aData[i].id + ', 1);">' + aData[i].paiju_name + '</h3>';
					html += '</div>';
					html += '<div class="panel-body">';
						html += '<div class="pj-cell"><span>核对数字</span><span ' + (aData[i].hedui_shuzi != 0 ? 'style="color:#ff0000;"' : '') + '>' + aData[i].hedui_shuzi + '</span></div>';
						if(aData[i].status == 0){
							html += '<div class="pj-cell"><button class="btn btn-sm btn-default" onclick="AlertWin.showPaijuDataList(' + aData[i].id + ', 1);">修改</button></div>';
						}else{
							html += '<div class="pj-cell"></div>';
						}
						if(parseInt(aData[i].status) > 0){
							html += '<div class="pj-cell"><a class="btn btn-sm btn-default">已结算</a></div>';
						}else{
							html += '<div class="pj-cell"><a href="' + Tools.url('home', 'index/index') + '?paijuId=' + aData[i].id + '" class="btn btn-sm btn-default">结算</a></div>';
						}
					html += '</div>';
				html += '</div>';
			}
			var oHtml = $(html);
			oList.oWrapDom.append(oHtml);
			//oList.oScrollBar.update('relative');
			_bindHtmlEvent(oHtml);
		}
		
		function _bindHtmlEvent(oHtml){
			
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