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
	
		'login.json'															=> 'login/login',
		'logout.html'															=> 'login/logout',
		
		
		'home.html'																=> 'index/index',
		'paiju-list.json'														=> 'index/get-paiju-list',
		'keren-benjin.json'														=> 'index/get-keren-benjin',
		'jiaoshou-jiner.json'													=> 'index/jiaoshou-jiner',
		'update-benjin.json'													=> 'index/update-benjin',
		'keren-list.json'														=> 'index/get-keren-list',
		'update-keren-info.json'												=> 'index/update-keren-info',
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
		
		
		'club/save.json'														=> 'club/save',
		'club/delete.json'														=> 'club/delete',
		
		
		'money-type/save.json'													=> 'money-type/save',
		'money-type/delete.json'												=> 'money-type/delete',
		
		
		'money-out-put-type/save.json'											=> 'money-out-put-type/save',
		'money-out-put-type/delete.json'										=> 'money-out-put-type/delete',
		
		
		
		'lianmeng/add-lianmeng.json'											=> 'lianmeng/add-lianmeng',
		'lianmeng/list.json'													=> 'lianmeng/get-list',
		'lianmeng/update-lianmeng-info.json'									=> 'lianmeng/update-lianmeng-info',
		'lianmeng/delete.json'													=> 'lianmeng/delete',
		'lianmeng/lianmeng-host-duizhang.html'									=> 'lianmeng/lianmeng-host-duizhang',
		'lianmeng/lianmeng-zhang-dan-detail-list.json'							=> 'lianmeng/get-lianmeng-zhang-dan-detail-list',
		'lianmeng/lianmeng-zhong-zhang-list.json'								=> 'lianmeng/get-lianmeng-zhong-zhang-list',
		'lianmeng/qin-zhang.json'												=> 'lianmeng/qin-zhang',
		
		
		'paiju/chang-paiju-lianmeng.json'										=> 'paiju/chang-paiju-lianmeng',
		
		
		
		
		'agent/index.html'														=> 'agent/index',
		'agent/add.json'														=> 'agent/add',
		'agent/delete.json'														=> 'agent/delete',
		'agent/save-setting.json'												=> 'agent/save-setting',
		'agent/one-key-save-setting.json'										=> 'agent/one-key-save-setting',
		'agent/clean.json'														=> 'agent/clean',
		
		
		'import/index.html'														=> 'import/index',
		'import/paiju-data-list.json'											=> 'import/get-paiju-data-list',
		'import/save-paiju-data-info.json'										=> 'import/save-paiju-data-info',
		'import/do-jie-shuan.json'												=> 'import/do-jie-shuan',
		
		
		''																		=> 'site/index',
		'<lang:.*>'																=> 'site/index',
		
	],
];