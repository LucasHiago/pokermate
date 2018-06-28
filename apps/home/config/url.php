<?php
/**
 * URL配置控制
 * class : 解析器
 * enablePrettyUrl : 是否开启伪静态
 * showScriptName : 生成的URL是否带入口脚本名称
 * enableStrictParsing : 是否开启严格匹配
 * baseUrl 域名
 */
return [
	'class' => 'yii\web\UrlManager',
	'enablePrettyUrl' => true,
	'showScriptName' => false,
	'enableStrictParsing' => true,
	'baseUrl' => Yii::getAlias('@url.home'),
	'rules' => [
	
		'captcha'																=> 'login/captcha',
		'login.json'															=> 'login/login',
		'logout.html'															=> 'login/logout',
		
		
		'home.html'																=> 'index/index',
		'paiju-list.json'														=> 'index/get-paiju-list',
		'keren-benjin.json'														=> 'index/get-keren-benjin',
		'search-keren-benjin.json'												=> 'index/search-keren-benjin',
		'jiaoshou-jiner.json'													=> 'index/jiaoshou-jiner',
		'update-benjin.json'													=> 'index/update-benjin',
		'keren-list.json'														=> 'index/get-keren-list',
		'update-keren-info.json'												=> 'index/update-keren-info',
		'update-keren-player-id.json'											=> 'index/update-keren-player-id',
		'update-keren-agent-id.json'											=> 'index/update-keren-agent-id',
		'delete-keren.json'														=> 'index/delete-keren',
		'add-keren.json'														=> 'index/add-keren',
		
		
		'user/save.json'														=> 'user/save',
		'user/update-user-info.json'											=> 'user/update-user-info',
		'user/chou-shui-list.json'												=> 'user/get-chou-shui-list',
		'user/bao-xian-list.json'												=> 'user/get-bao-xian-list',
		'user/shang-zhuo-ren-shu-list.json'										=> 'user/get-shang-zhuo-ren-shu-list',
		'user/jiao-ban-zhuan-chu-detail.json'									=> 'user/get-jiao-ban-zhuan-chu-detail',
		'user/do-jiao-ban-zhuan-chu.json'										=> 'user/do-jiao-ban-zhuan-chu',
		'user/un-jiao-ban-paiju-total-statistic.json'							=> 'user/get-un-jiao-ban-paiju-total-statistic',
		'user/club-and-lianmeng-list.json'										=> 'user/get-club-and-lianmeng-list',
		'user/set-active.json'													=> 'user/set-active',
		
		
		'club/save.json'														=> 'club/save',
		'club/delete.json'														=> 'club/delete',
		
		
		'money-type/save.json'													=> 'money-type/save',
		'money-type/delete.json'												=> 'money-type/delete',
		'money-type/add-money.json'												=> 'money-type/add-money',
		
		
		'money-out-put-type/save.json'											=> 'money-out-put-type/save',
		'money-out-put-type/delete.json'										=> 'money-out-put-type/delete',
		'money-out-put-type/add-money.json'										=> 'money-out-put-type/add-money',
		
		
		
		'lianmeng/add-lianmeng.json'											=> 'lianmeng/add-lianmeng',
		'lianmeng/save-lianmeng.json'											=> 'lianmeng/save-lianmeng',
		'lianmeng/list.json'													=> 'lianmeng/get-list',
		'lianmeng/update-lianmeng-info.json'									=> 'lianmeng/update-lianmeng-info',
		'lianmeng/delete.json'													=> 'lianmeng/delete',
		'lianmeng/lianmeng-host-duizhang.html'									=> 'lianmeng/lianmeng-host-duizhang',
		'lianmeng/lianmeng-zhang-dan-detail-list.json'							=> 'lianmeng/get-lianmeng-zhang-dan-detail-list',
		'lianmeng/lianmeng-zhong-zhang-list.json'								=> 'lianmeng/get-lianmeng-zhong-zhang-list',
		'lianmeng/qin-zhang.json'												=> 'lianmeng/qin-zhang',
		'lianmeng/add-lianmeng-club.json'										=> 'lianmeng/add-lianmeng-club',
		'lianmeng/club-list.json'												=> 'lianmeng/get-club-list',
		'lianmeng/update-lianmeng-club-info.json'								=> 'lianmeng/update-lianmeng-club-info',
		'lianmeng/delete-club.json'												=> 'lianmeng/delete-club',
		'lianmeng/lianmeng-club-qin-zhang.json'									=> 'lianmeng/lianmeng-club-qin-zhang',
		'lianmeng/export-lianmeng-zhangdan-detail.html'							=> 'lianmeng/export-lianmeng-zhangdan-detail',
		
		
		
		'host-lianmeng/add-lianmeng.json'										=> 'host-lianmeng/add-lianmeng',
		'host-lianmeng/save-lianmeng.json'										=> 'host-lianmeng/save-lianmeng',
		'host-lianmeng/list.json'												=> 'host-lianmeng/get-list',
		'host-lianmeng/update-lianmeng-info.json'								=> 'host-lianmeng/update-lianmeng-info',
		'host-lianmeng/delete.json'												=> 'host-lianmeng/delete',
		'host-lianmeng/lianmeng-host-duizhang.html'								=> 'host-lianmeng/lianmeng-host-duizhang',
		'host-lianmeng/lianmeng-zhang-dan-detail-list.json'						=> 'host-lianmeng/get-lianmeng-zhang-dan-detail-list',
		'host-lianmeng/lianmeng-zhong-zhang-list.json'							=> 'host-lianmeng/get-lianmeng-zhong-zhang-list',
		'host-lianmeng/qin-zhang.json'											=> 'host-lianmeng/qin-zhang',
		'host-lianmeng/add-lianmeng-club.json'									=> 'host-lianmeng/add-lianmeng-club',
		'host-lianmeng/club-list.json'											=> 'host-lianmeng/get-club-list',
		'host-lianmeng/update-lianmeng-club-info.json'							=> 'host-lianmeng/update-lianmeng-club-info',
		'host-lianmeng/delete-club.json'										=> 'host-lianmeng/delete-club',
		'host-lianmeng/lianmeng-club-qin-zhang.json'							=> 'host-lianmeng/lianmeng-club-qin-zhang',
		'host-lianmeng/refresh-lianmeng-paiju-time.json'						=> 'host-lianmeng/refresh-lianmeng-paiju-time',
		'host-lianmeng/export-club-paiju-statistic.html'						=> 'host-lianmeng/export-club-paiju-statistic',
		
		
		'paiju/chang-paiju-lianmeng.json'										=> 'paiju/chang-paiju-lianmeng',
		
		
		
		
		'agent/index.html'														=> 'agent/index',
		'agent/baoxian.html'													=> 'agent/baoxian',
		'agent/add.json'														=> 'agent/add',
		'agent/delete.json'														=> 'agent/delete',
		'agent/save-setting.json'												=> 'agent/save-setting',
		'agent/save-baoxian-setting.json'										=> 'agent/save-baoxian-setting',
		'agent/one-key-save-setting.json'										=> 'agent/one-key-save-setting',
		'agent/one-key-save-baoxian-setting.json'								=> 'agent/one-key-save-baoxian-setting',
		'agent/clean.json'														=> 'agent/clean',
		'agent/clean-baoxian.json'												=> 'agent/clean-baoxian',
		'agent/export.html'														=> 'agent/export',
		'agent/export-baoxian.html'												=> 'agent/export-baoxian',
		'agent/all-agent-total-fencheng.json'									=> 'agent/get-all-agent-total-fencheng',
		'agent/all-agent-total-baoxian-fencheng.json'							=> 'agent/get-all-agent-total-baoxian-fencheng',
		
		
		'import/index.html'														=> 'import/index',
		'import/upload-excel.json'												=> 'import/upload-excel',
		'import/paiju-data-list.json'											=> 'import/get-paiju-data-list',
		'import/save-paiju-data-info.json'										=> 'import/save-paiju-data-info',
		'import/do-jie-shuan.json'												=> 'import/do-jie-shuan',
		'import/do-jie-shuan-empty-paiju.json'									=> 'import/do-jie-shuan-empty-paiju',
		'import/download-save-code.json'										=> 'import/get-download-save-code',
		'import/download-save-code1.json'										=> 'import/get-download-save-code1',
		'import/do-import-paiju.json'											=> 'import/do-import-paiju',
		'import/do-import-paiju1.json'											=> 'import/do-import-paiju1',
		'import/import-player.html'												=> 'import/show-import-player',
		'import/upload-player-excel.json'										=> 'import/upload-player-excel',
		'import/import-all-player.html'	 										=> 'import/show-import-all-player',
		'import/upload-all-player-excel.json'									=> 'import/upload-all-player-excel',
		
		
		
		'user-manage/index.html'												=> 'user-manage/index',
		'user-manage/set-forbidden-user.json'									=> 'user-manage/set-forbidden-user',
		'user-manage/edit/<id:.*>.html'											=> 'user-manage/show-edit',
		'user-manage/edit.json'													=> 'user-manage/edit',
		'user-manage/clear-user-data.json'										=> 'user-manage/clear-user-data',
		'user-manage/clear-save-code-limit.json'								=> 'user-manage/clear-save-code-limit',
		'user-manage/set-is-show-get-paiju-time-select.json'					=> 'user-manage/set-is-show-get-paiju-time-select',
		
		
		
		'club-manage/index.html'												=> 'club-manage/index',
		'club-manage/set-delete.json'											=> 'club-manage/set-delete',
		'club-manage/edit/<id:.*>.html'											=> 'club-manage/show-edit',
		'club-manage/edit.json'													=> 'club-manage/edit',
		
		'keren-benjin-manage/index.html'										=> 'keren-benjin-manage/index',
		'keren-benjin-manage/player-list.html'									=> 'keren-benjin-manage/player-list',
		'keren-benjin-manage/player-list.json'									=> 'keren-benjin-manage/get-player-list',
		'keren-benjin-manage/set-delete.json'									=> 'keren-benjin-manage/set-delete',
		'keren-benjin-manage/edit/<id:.*>.html'									=> 'keren-benjin-manage/show-edit',
		'keren-benjin-manage/edit.json'											=> 'keren-benjin-manage/edit',
		'keren-benjin-manage/edit-player.json'									=> 'keren-benjin-manage/edit-player',
		'keren-benjin-manage/export-list.html'									=> 'keren-benjin-manage/export-list',
		'keren-benjin-manage/export-player-list.html'							=> 'keren-benjin-manage/export-player-list',
		'keren-benjin-manage/delete-player.json'								=> 'keren-benjin-manage/delete-player',
		'keren-benjin-manage/export-player-last-paiju-data.html'				=> 'keren-benjin-manage/export-player-last-paiju-data',
		'keren-benjin-manage/export-keren-last-paiju-data.html'					=> 'keren-benjin-manage/export-keren-last-paiju-data',
		'keren-benjin-manage/set-all-benjin-zero.json'							=> 'keren-benjin-manage/set-all-benjin-zero',
		
		
		'operate-log-manage/index.html'											=> 'operate-log-manage/index',
		
		
		'test.html'																=> 'site/test',
		''																		=> 'site/index',
		'<lang:.*>'																=> 'site/index',
		
	],
];