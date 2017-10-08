<?php
return [
	'menu_config' => [
		[
			'title' => '账号管理',
			'en_title' => 'user_manage',
			'url' => ['user-manage/index'],
			'permission' => ['manager'],
			'icon_class' => 'user',	
			'child' => [],
		],
		[
			'title' => '俱乐部管理',
			'en_title' => 'club_manage',
			'url' => ['club-manage/index'],
			'permission' => ['user', 'manager'],
			'icon_class' => 'joomla',	
			'child' => [],
		],
		[
			'title' => '会员管理',
			'en_title' => 'vip_manage',
			'url' => ['keren-benjin-manage/player-list'],
			'permission' => ['user', 'manager'],
			'icon_class' => 'money',	
			'child' => [],
		],
		[
			'title' => '操作日志',
			'en_title' => 'vip_manage',
			'url' => ['operate-log-manage/index'],
			'permission' => ['user', 'manager'],
			'icon_class' => 'file',	
			'child' => [],
		],
		[
			'title' => '清除数据',
			'en_title' => 'vip_manage',
			'url' => ['user-manage/clear-user-data'],
			'permission' => ['user', 'manager'],
			'icon_class' => 'trash',	
			'child' => [],
		],
		
		/*[
			'title' => '一级标题',
			'en_title' => 'user_list',
			'url' => ['user-manage/index'],
			'permission' => ['manager'],
			'icon_class' => 'user',	
			'child' => [
				[
					'title' => '二级标题',
					'en_title' => 'user_list',
					'url' => ['user-manage/index'],
					'permission' => ['manager'],
					'icon_class' => 'user',	
					'child' => [],
				],
			],
		],*/
		
	],
];
