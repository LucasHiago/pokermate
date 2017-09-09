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
				html += '<div class="c-b-list-item ' + (aData[i].status == 0 ? 'new' : '') + '">';
					html += '<div class="c-b-l-i-title">' + aData[i].paiju_name + '</div>';
					html += '<div class="c-b-l-i-bottom">';
						html += '<a class="l-text"><span>核对数字</span><span>' + aData[i].hedui_shuzi + '</span></a>';
						html += '<a class="l-edit" onclick="AlertWin.showPaijuDataList(' + aData[i].id + ');"></a>';
						html += '<a href="' + Tools.url('home', 'index/index') + '?paijuId=' + aData[i].id + '" class="l-status ' + (aData[i].status == 1 ? 'l-clean' : '') + '"></a>';
					html += '</div>';
				html += '</div>';
			}
			var oHtml = $(html);
			oList.oWrapDom.append(oHtml);
			oList.oScrollBar.update('relative');
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