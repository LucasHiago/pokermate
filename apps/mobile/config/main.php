<?php
$params = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../common/config/params.php'),
    require(__DIR__ . '/../../../common/config/params-local.php'),
    require(__DIR__ . '/params.php')
    //require(__DIR__ . '/params-local.php')
);
return [
    'id' => 'mobile',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'mobile\controllers',
    'runtimePath' => PROJECT_PATH . '/runtime/mobile',
    'components' => [
		'view' => [
			'commonTitle' => 'XXX-X-XX-XC',
			'baseTitle' => 'XXX-X-XX-XC-basetitle',
		],
    ],
	'layout' => 'main',
	'urlManagerName' => 'urlManagerMobile',
//	'catchAll' => [
//        'remind/close-website-remind',
//		'words' => '',
//		'start_time' => 0,
//		'end_time' => 0,
//    ],
    'params' => $params,
];
