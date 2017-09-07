<?php
use umeworld\lib\Url;
$this->setTitle('结账台');
?>
<div class="c-body-wrap">
	<div class="ag-bg">
		<div class="ag-left">
			<div class="ag-l-r1">
				<div class="r1-select-all"></div>
				<div class="r1-delete" onclick="deleteAgent(this);"></div>
				<div class="r1-add" onclick="AlertWin.showAddAgent();"></div>
			</div>
			<div class="ag-l-list">
			<?php foreach($aAgentList as $aAgent){ ?>
				<div class="ag-l-list-item">
					<a href="javascript:;" class="agi-chk" data-id="<?php echo $aAgent['id']; ?>"></a>
					<a class="agi-txt"><?php echo $aAgent['agent_name']; ?></a>
				</div>
			<?php } ?>
			</div>
		</div>
		<div class="ag-center">
			<div class="J-ag-c-list ag-c-list">
			<?php foreach($aFenchengListSetting as $aFenchengSetting){ ?>
				<div class="ag-c-list-item" data-id="<?php echo $aFenchengSetting['id']; ?>">
					<a class="tx1"><?php echo $aFenchengSetting['zhuozi_jibie']; ?></a>
					<a class="tx2"><input type="text" value="<?php echo $aFenchengSetting['yingfan']; ?>%" /></a>
					<a class="ebt1"></a>
					<a class="tx3"><input type="text" value="<?php echo $aFenchengSetting['shufan']; ?>%" /></a>
					<a class="ebt2"></a>
				</div>
			<?php } ?>
			</div>
			<div class="ag-c-bottom">
				<div class="ag-c-bottom-tx1"><input type="text" value="0.00%" /></div>
				<div class="ag-c-bottom-btn1"></div>
				<div class="ag-c-bottom-tx2"><input type="text" value="0.00%" /></div>
				<div class="ag-c-bottom-btn2"></div>
			</div>
		</div>
		<div class="ag-right">
			<div class="ag-r-head">
				<a class="zfc-txt">10000</a>
				<a class="qz-btn"></a>
			</div>
			<div class="ag-r-title">
				<div class="ag-r-select-all"></div>
			</div>
			<div class="ag-r-list">
				<div class="ag-r-list-item">
					<a class="agr-li-chk"></a>
					<a class="agr-li-name">20170224</a>
					<a class="agr-li-level">10/20</a>
					<a class="agr-li-uanme">一只白兔</a>
					<a class="agr-li-score">10</a>
					<a class="agr-li-fc">10</a>
				</div>
				<div class="ag-r-list-item">
					<a class="agr-li-chk active"></a>
					<a class="agr-li-name">20170224</a>
					<a class="agr-li-level">10/20</a>
					<a class="agr-li-uanme">一只白兔</a>
					<a class="agr-li-score">10</a>
					<a class="agr-li-fc">10</a>
				</div>
				<div class="ag-r-list-item">
					<a class="agr-li-chk"></a>
					<a class="agr-li-name">20170224</a>
					<a class="agr-li-level">10/20</a>
					<a class="agr-li-uanme">一只白兔</a>
					<a class="agr-li-score">10</a>
					<a class="agr-li-fc">10</a>
				</div>
				<div class="ag-r-list-item">
					<a class="agr-li-chk"></a>
					<a class="agr-li-name">20170224</a>
					<a class="agr-li-level">10/20</a>
					<a class="agr-li-uanme">一只白兔</a>
					<a class="agr-li-score">10</a>
					<a class="agr-li-fc">10</a>
				</div>
				<div class="ag-r-list-item">
					<a class="agr-li-chk"></a>
					<a class="agr-li-name">20170224</a>
					<a class="agr-li-level">10/20</a>
					<a class="agr-li-uanme">一只白兔</a>
					<a class="agr-li-score">10</a>
					<a class="agr-li-fc">10</a>
				</div>
				<div class="ag-r-list-item">
					<a class="agr-li-chk"></a>
					<a class="agr-li-name">20170224</a>
					<a class="agr-li-level">10/20</a>
					<a class="agr-li-uanme">一只白兔</a>
					<a class="agr-li-score">10</a>
					<a class="agr-li-fc">10</a>
				</div>
				<div class="ag-r-list-item">
					<a class="agr-li-chk"></a>
					<a class="agr-li-name">20170224</a>
					<a class="agr-li-level">10/20</a>
					<a class="agr-li-uanme">一只白兔</a>
					<a class="agr-li-score">10</a>
					<a class="agr-li-fc">10</a>
				</div>
				<div class="ag-r-list-item">
					<a class="agr-li-chk"></a>
					<a class="agr-li-name">20170224</a>
					<a class="agr-li-level">10/20</a>
					<a class="agr-li-uanme">一只白兔</a>
					<a class="agr-li-score">10</a>
					<a class="agr-li-fc">10</a>
				</div>
				<div class="ag-r-list-item">
					<a class="agr-li-chk"></a>
					<a class="agr-li-name">20170224</a>
					<a class="agr-li-level">10/20</a>
					<a class="agr-li-uanme">一只白兔</a>
					<a class="agr-li-score">10</a>
					<a class="agr-li-fc">10</a>
				</div>
				<div class="ag-r-list-item">
					<a class="agr-li-chk"></a>
					<a class="agr-li-name">20170224</a>
					<a class="agr-li-level">10/20</a>
					<a class="agr-li-uanme">一只白兔</a>
					<a class="agr-li-score">10</a>
					<a class="agr-li-fc">10</a>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">	
	function deleteAgent(o){
		var aAgentId = [];
		$('.ag-l-list-item .agi-chk.active').each(function(){
			aAgentId.push($(this).attr('data-id'));
		});
		if(aAgentId.length == 0){
			UBox.show('请选择要删除的代理', -1);
			return;
		}
		if(confirm('确定删除？')){
			ajax({
				url : Tools.url('home', 'agent/delete'),
				data : {aAgentId : aAgentId},
				beforeSend : function(){
					$(o).attr('disabled', 'disabled');
				},
				complete : function(){
					$(o).attr('disabled', false);
				},
				success : function(aResult){
					if(aResult.status == 1){
						$('.ag-l-list-item .agi-chk.active').parent().remove();
					}
					UBox.show(aResult.msg, aResult.status);
				}
			});
		}
	}
	
	function initAgentList(){
		$('.ag-l-list').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 5});
		$('.ag-left .r1-select-all').on('click', function(){
			if($(this).hasClass('active')){
				$(this).removeClass('active');
				$('.ag-l-list-item .agi-chk').removeClass('active');
			}else{
				$(this).addClass('active');
				$('.ag-l-list-item .agi-chk').addClass('active');
			}
		});
		$('.ag-l-list-item .agi-chk').on('click', function(){
			if($(this).hasClass('active')){
				$(this).removeClass('active');
				$('.ag-left .r1-select-all').removeClass('active');
			}else{
				$(this).addClass('active');
			}
		});
	}
	function initAgentSetting(){
		$('.J-ag-c-list').tinyscrollbar({axis : 'y', scrollbarVisable : false, wheelSpeed : 5});
		function delPercent(o){
			var oTxt = $(o).prev().find('input');
			var txt = oTxt.val();
			oTxt.val('');
			oTxt.focus();
			oTxt.val(parseFloat(txt));
		}
		$('.J-ag-c-list .ag-c-list-item .ebt1, .J-ag-c-list .ag-c-list-item .ebt2').click(function(){
			delPercent(this);
		});
		$('.J-ag-c-list .ag-c-list-item .ebt1, .J-ag-c-list .ag-c-list-item .ebt2, .ag-c-bottom-tx1 input, .ag-c-bottom-tx2 input').prev().find('input').click(function(){
			$(this).parent().next().click();
		});
		function ajustValue(o){
			var oTxt = $(o);
			var txt = oTxt.val();
			if(txt == ''){
				txt = '0.00';
			}
			if(txt.indexOf('%') === -1){
				oTxt.val(parseFloat(txt) + '%');
			}else{
				oTxt.val(txt);
			}
		}
		$('.ag-c-bottom-tx1 input, .ag-c-bottom-tx2 input').click(function(){
			var oTxt = $(this);
			var txt = oTxt.val();
			oTxt.val('');
			oTxt.focus();
			oTxt.val(parseFloat(txt));
		});
		$('.ag-c-bottom-tx1 input, .ag-c-bottom-tx2 input').blur(function(){
			ajustValue(this);
		});
		$('.J-ag-c-list .ag-c-list-item .ebt1, .J-ag-c-list .ag-c-list-item .ebt2').prev().find('input').blur(function(){
			ajustValue(this);
		});
		$('.J-ag-c-list .ag-c-list-item').find('input').keyup(function(e){
			var o = this;
			var id = $(this).parent().parent().attr('data-id');
			var yingfan = $(this).parent().parent().find('.tx2 input').val();
			var shufan = $(this).parent().parent().find('.tx3 input').val();
			if(e.keyCode == 13){
				ajax({
					url : Tools.url('home', 'agent/save-setting'),
					data : {
						id : id,
						yingfan : parseFloat(yingfan),
						shufan : parseFloat(shufan)
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
		
		function oneKeySaveSetting(o, aData){
			ajax({
				url : Tools.url('home', 'agent/one-key-save-setting'),
				data : aData,
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
		$('.ag-c-bottom-btn1').click(function(){
			oneKeySaveSetting(this, {type : 'yingfan', yingfan : parseFloat($(this).prev().find('input').val())});
		});
		$('.ag-c-bottom-btn2').click(function(){
			oneKeySaveSetting(this, {type : 'shufan', shufan : parseFloat($(this).prev().find('input').val())});
		});
	}
	
	$(function(){
		$('.c-h-t-menu.m2').addClass('active');
		initAgentList();
		initAgentSetting();
	});
</script>