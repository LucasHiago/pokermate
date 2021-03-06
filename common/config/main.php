<?php
return [
    'vendorPath' => FRAMEWORK_PATH,
    'domain' => $aLocal['domain_host_name'] . '.' . $aLocal['domain_suffix'][YII_ENV],
    'aWebAppList' => [
		'home'
	],
    'language' => 'zh-CN',
    'bootstrap' => ['log'],
	'defaultRoute' => 'site/index',
//	'catchAll' => [
//        'remind/close-website-remind',
//		'words' => '',
//		'start_time' => 0,
//		'end_time' => 0,
//    ],
    'components' => [
		//各APP的URL管理器 start
		'urlManagerHome' => require(Yii::getAlias('@home') . '/config/url.php'),
		//各APP的URL管理器 end

        'request' => [
            'cookieValidationKey' => 'EArv76QW-Dc8ngUP-qndrD0BDlodbqw-',
        ],

		'assetManager' => [
			'bundles' => [
				'yii\web\JqueryAsset' => [
					'sourcePath' => null,
					'js' => []
				],
			]
		],
		
		'lang' => [
			'class' => 'umeworld\lib\Lang',	//语言组件
        ],

		'response' => [
			'class' => 'yii\web\Response',
			'format' => 'html',
		],

        'log' => require(__DIR__ . '/log.php'),

		'errorHandler' => [
			'class' => 'common\lib\ErrorHandler',
			'errorAction' => 'site/error',	//所有站点APP统一使用site控制器的error方法处理网络可能有点慢
		],

		'view' => [
			'class' => 'umeworld\lib\View',
			'on beginPage' => function(){
				Yii::$app->view->title = \yii\helpers\Html::encode(Yii::$app->view->title);

				Yii::$app->view->registerLinkTag([
					'rel' => 'shortcut icon',
					'href' => Yii::getAlias('@r.url') . '/favicon.ico',
				]);

				Yii::$app->view->registerMetaTag([
					'name' => 'csrf-token',
					'content' => Yii::$app->request->csrfToken,
				]);
				//http转https
				/*if(YII_ENV_PROD){
					header("Content-Security-Policy: upgrade-insecure-requests");
				}*/
				//加载语言配置js
				if(isset(Yii::$app->language) && Yii::$app->language){
					$link = str_replace('lang.data', 'lang.data.' . Yii::$app->language, Yii::getAlias('@r.js.lang.data'));
					$fileName = str_replace(Yii::getAlias('@r.url'), Yii::getAlias('@p.resource'), $link);
					if(file_exists($fileName)){
						$link .= '?v=' . date('YmdHis', filemtime($fileName));
					}
					//echo '<script type="text/javascript" src="' . $link . '"></script>';
				}
			},

			'on endPage' => function(){
				// echo '<!--domainname';	//防止尾部运营商注入广告脚本,IE会显示半截标签，暂时屏蔽
			},
			'on endBody' => function(){
				// echo '<!--domainname';	//防止尾部运营商注入广告脚本,IE会显示半截标签，暂时屏蔽
				//http转https
				//echo \common\widgets\Https::widget();
			},
		],

       'db' => [
            'class' => 'umeworld\lib\Connection',
            'charset' => 'utf8mb4',
			'aTables' => [
				/**
				 * 当你要求user表不使用缓存
				 * 'user' => 'cache:0'
				 *
				 * 当你的某个表不在主库mydb,而是在财务库mydb_recharge
				 * 'recharge' => 'table:mydb_recharge.recharge'		//以recharge为别名指向具体的数据库,必须有table:
				 *
				 * 既定义数据库的具体位置又定义是否缓存
				 * 'recharge' => 'table:db2.recharge;cache:0'	//这里增加了cache控制,1/0表示是否缓存数据,其实语法就像CSS一样
				 *
				 * 以后若有更多控制需求,可以增加"CSS属性"并在 umeworld\lib\Query::from 类里做解析代码
				 */
			],

			'masterConfig' => [
				'username' => $aLocal['db']['master']['username'],
				'password' => $aLocal['db']['master']['password'],
				'attributes' => [
					// use a smaller connection timeout
					PDO::ATTR_TIMEOUT => 10,
					PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
				],
			],

			'masters' => $aLocal['db']['master']['node'],

			'slaveConfig' => [
				'username' => $aLocal['db']['slaver']['username'],
				'password' => $aLocal['db']['master']['password'],
				'attributes' => [
					// use a smaller connection timeout
					PDO::ATTR_TIMEOUT => 10,
					PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
				],
			],

			'slaves' => $aLocal['db']['slaver']['node'],
		],

        'redis' => [
            'class' => 'umeworld\lib\RedisCache',
			'serverName' => $aLocal['cache']['redis']['server_name'],
			'dataPart'	=>	[
				'index'		=>	$aLocal['cache']['redis']['part']['data'],
				'is_active'	=>	0,
			],
			'loginPart' =>	[
				'index'		=>	$aLocal['cache']['redis']['part']['login'],
				'is_active'	=>	0,
			],
			'tempPart'	=>	[
				'index'		=>	$aLocal['cache']['redis']['part']['temp'],
				'is_active'	=>	0,
			],
			'servers' => [
				'redis_1' => [
					'is_active' => 0,
					'host'		=>	$aLocal['cache']['redis']['host'],
					'port'		=>	$aLocal['cache']['redis']['port'],
					'password'	=>	$aLocal['cache']['redis']['password'],
				],
			],
		],

        'redisCache' => [
            'class' => 'umeworld\lib\RedisCache',
			'serverName' => $aLocal['cache']['redisCache']['server_name'],
			'dataPart'	=>	[
				'index'		=>	$aLocal['cache']['redisCache']['part'],
				'is_active'	=>	0,
			],
			'servers' => [
				'redis_1' => [
					'is_active' => 0,
					'host'		=>	$aLocal['cache']['redisCache']['host'],
					'port'		=>	$aLocal['cache']['redisCache']['port'],
					'password'	=>	$aLocal['cache']['redisCache']['password'],
				],
			],
		],

		'client' => [
			'class' => 'umeworld\helper\Client'
		],
		
        'authManager' => [
			'class' => 'common\role\AuthManager',
		],
		
        'user' => [
			'class' => 'common\role\UserRole',
            'identityClass' => 'common\model\User',
            'reloginOvertime' => 1800,//3600,
            'rememberLoginTime' => 86400,//3000000,
            'enableAutoLogin' => true,
			'loginUrl' => function(){
				return \umeworld\lib\Url::to('home', 'site/index');
			},
        ],

		'sms'=>[
			'class' => 'umeworld\lib\Sms',
			'username' => 'xxx2016',
			'password' => '1ab4263b0dfbff034bf6',
		],
		
		/**
			用法
			Yii::$app->mailer->compose()
				//->setFrom('from@domain.com')
				->setTo($account)
				->setSubject('验证码')
				->setTextBody($content)
				//$mail->setHtmlBody("<a>content</a>");  
				->send();
		*/
		'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
			'useFileTransport' =>false,	//这句一定有，false发送邮件，true只是生成邮件在runtime文件夹下，不发邮件
			'transport' => [
				'class' => 'Swift_SmtpTransport',
				'host' => 'smtp.qq.com',
				'username' => 'xxxxx@qq.com',
				'password' => 'owvyorxkzgsibebd',	//密码不是邮箱密码，而是登录授权码
				'encryption' => 'tls', //tls 或 ssl
				'port' => '587',	//原先是25，不行就用465 或 587
			],
			'messageConfig' => [
               'charset' => 'UTF-8',
               'from' => ['xxxx@qq.com' => 'Jay']
            ],
			'htmlLayout' => '@common/views/mail/html-layout',
			'textLayout' => '@common/views/mail/text-layout',
        ],
		
		'excel' => [
			'class' => 'umeworld\lib\PHPExcel\excel',
		],
		
		'downLoadExcel' => [
			'class' => 'common\model\DownLoadExcel',	
			'savecodeUrl' => 'http://cms.pokermanager.club/cms/servlet/safecode',//验证码
			'savecodeUrl1' => 'http://cms.pokermanager.club/cms-api/captcha',//验证码
			'loginPageUrl' => 'http://cms.pokermanager.club/cms/',//用户登录页面
			'loginUrl' => 'http://cms.pokermanager.club/cms/user/login',//用户登录
			'loginUrl1' => 'http://cms.pokermanager.club/cms-api/login',//用户登录
			'selectClubUrl' => 'http://cms.pokermanager.club/cms/club/clubInfo?clubId=',//选择俱乐部	
			'historyExportUrl' => 'http://cms.pokermanager.club/cms/club/historyExport',//导出战绩列表页面
			'exportRoomUrl' => 'http://cms.pokermanager.club/cms/club/exportRoom',//下载Excel
			'exportUrl' => 'http://cms.pokermanager.club/cms/club/export',//下载Excel
			'tokenUrl' => 'http://cms.pokermanager.club/cms-api/token/generateCaptchaToken',//token
			'selectClubUrl1' => 'http://cms.pokermanager.club/cms-web/basicMsg.html',//选择俱乐部
		],
		
    ],
];
