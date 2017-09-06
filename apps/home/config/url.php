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
		
		
		'user/save.json'														=> 'user/save',
		
		
		'club/save.json'														=> 'club/save',
		'club/delete.json'														=> 'club/delete',
		
		
		'money-type/save.json'													=> 'money-type/save',
		'money-type/delete.json'												=> 'money-type/delete',
		
		
		'money-out-put-type/save.json'											=> 'money-out-put-type/save',
		'money-out-put-type/delete.json'										=> 'money-out-put-type/delete',
		
		
		
		'agent/index.html'														=> 'agent/index',
		
		
		''																		=> 'site/index',
		'<lang:.*>'																=> 'site/index',
		
	],
];