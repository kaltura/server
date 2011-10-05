<?php

class kConfLocal
{
	protected static $map = null;
	
	// this will overwrite existing keys in self::$map
	protected static function addConfig()
	{
		self::$map = array_merge(
			self::$map,
			array(
			
				"cdn_host" => "local.trunk",
				"iis_host" => "local.trunk",
				"www_host" => "local.trunk",
				"rtmp_url" => "local.trunk",
			
				"kaltura_installation_type" => "@INSTALLATION_TYPE@",
				
				"corp_action_redirect" => "@CORP_REDIRECT@",
			
				"terms_of_use_uri" => "index.php/terms",

				"memcache_host" => "localhost",
				"memcache_port" => "11211",
			
			
				"apphome_url" => "http://local.trunk",
				"apphome_url_no_protocol" => "local.trunk",
				"admin_console_url" => "http://local.trunk/admin_console",
				"contact_url" => "@CONTACT_URL@",
				"contact_phone_number" => "@CONTACT_PHONE_NUMBER@",
				"beginners_tutorial_url" => "@BEGINNERS_TUTORIAL_URL@",
				"quick_start_guide_url" => "@QUICK_START_GUIDE_URL@",
				"forum_url" => "@FORUMS_URLS@",
				"unsubscribe_mail_url" => "@UNSUBSCRIBE_EMAIL_URL@", // actual user email will be added at the end of this string
				"default_email" => "customer_service@@KALTURA_VIRTUAL_HOST_NAME@",
				"default_email_name" => "@ENVIRONMENT_NAME@ Automated Response",
				"partner_registration_confirmation_email" => "registration_confirmation@@KALTURA_VIRTUAL_HOST_NAME@",
				"partner_registration_confirmation_name" => "@ENVIRONMENT_NAME@",
				"partner_notification_email" => "customer_service@@KALTURA_VIRTUAL_HOST_NAME@",
				"partner_notification_name" => "@ENVIRONMENT_NAME@ Automated Response",
				"partner_change_email_email" => "customer_service@@KALTURA_VIRTUAL_HOST_NAME@",
				"partner_change_email_name" => "@ENVIRONMENT_NAME@ Automated Response",
				"purchase_package_email" => "customer_service@@KALTURA_VIRTUAL_HOST_NAME@",
				"purchase_package_name" => "@ENVIRONMENT_NAME@ Automated Response",
				"batch_download_video_sender_email" => "download_video@@KALTURA_VIRTUAL_HOST_NAME@",
				"batch_download_video_sender_name" => "@ENVIRONMENT_NAME@",
				"batch_flatten_video_sender_email" => "download_video@@KALTURA_VIRTUAL_HOST_NAME@",
				"batch_flatten_video_sender_name" => "@ENVIRONMENT_NAME@",
				"batch_notification_sender_email" => "notifications@@KALTURA_VIRTUAL_HOST_NAME@" , 
				"batch_notification_sender_name" => "@ENVIRONMENT_NAME@" ,	
				"batch_alert_email" => "alert@@KALTURA_VIRTUAL_HOST_NAME@" , 
				"batch_alert_name" => "@ENVIRONMENT_NAME@",
			
				"system_pages_login_username" => "kaltura",
				"system_pages_login_password" => "30d390fb24c8e80a880e4f8bfce7a3a06757f1c7",				
				
				"bin_path_ffmpeg" => 'c:/opt/bin/ffmpeg',
				"bin_path_mencoder" => 'c:/opt/mencoder',
				"bin_path_flix" => 'c:/opt/cli_encode',
				"bin_path_encoding_com" => 'c:/opt/encoding_com.php',
				"bin_path_imagemagick" => 'c:/opt/convert',
				"bin_path_curl" => 'c:/opt/curl',
				"bin_path_mediainfo" => 'c:/opt/mediainfo',
			
				"image_proxy_url" => "",
				"image_proxy_port" => "",	
				"image_proxy_secret" => "",
			
				"ga_account" => 'UA-7714780-1', //google analytics
				
				'track_kdpwrapper' =>false,
			
				"exec_sphinx" => true, // Should be set to false in multiple data centers environments

				"reports_db_config" => array (
					"host" => "localhost",
					"user" => "kaltura",
					"port" => "3306",
					"password" => "kaltura" ,
					"db_name" => "kalturadw" , 
				),
				
				"event_log_file_path" => "c:/opt/kaltura/log/events.log",
				
				"dc_config" => array (
					"current" => "0",
					"list" => array (
								"0" => array ( "name" => "DC_0" ,
											   "url" => "http://local.trunk" ,
											   "external_url" => "http://local.trunk" ,
											   "secret" => "@DC0_SECRET@" ,
											   "root" => "/web/" )
					)
				),
				
				"date_default_timezone" => "Asia/Jerusalem",
				
				// ce only settings
				"kaltura_activation_key" => false,
				"replace_passwords" => false,
				"kaltura_version" => "@KALTURA_VERSION@",
				"report_admin_email" => "@REPORT_ADMIN_EMAIL@",
				"usage_tracking_optin" => false,
				"installation_id" => "@INSTALLATION_UID@",
                		// end of ce only settings
			)
		);
		
		// password reset links
		self::$map['password_reset_links']  = array (
			'default' => self::$map['apphome_url'].'/index.php/kmc/kmc/setpasshashkey/',
			'admin_console' => self::$map['admin_console_url'].'/index.php/user/reset-password-link/token/',
		);	
		
		
		// additional plugins
		self::$map['default_plugins'][] = "FileSyncPlugin"; // Should be enabled only on servers that run admin console
		self::$map['default_plugins'][] = "SystemUserPlugin"; // Should be enabled only on servers that run admin console
		self::$map['default_plugins'][] = "SystemPartnerPlugin"; // Should be enabled only on servers that run admin console
		self::$map['default_plugins'][] = "AdminConsolePlugin"; // Should be enabled only on servers that run admin console
		//self::$map['default_plugins'][] = "MultiCentersPlugin"; // Should be enabled on multiple data centers
		//self::$map['default_plugins'][] = "AuditPlugin"; // Should be enabled only if audit trail support required
		//self::$map['default_plugins'][] = "VirusScanPlugin";
		//self::$map['default_plugins'][] = "SymantecScanEnginePlugin";
		//self::$map['default_plugins'][] = "QuickTimeToolsPlugin";
		//self::$map['default_plugins'][] = "FastStartPlugin"
		
}

	
	protected static function getDB()
	{
		return array (
		  'datasources' => 
		  array (
		    'default' => 'propel',
		  
		    'propel' => 
		    array (
		      'adapter' => 'mysql',
		      'connection' => 
		      array (
		      	'classname' => 'KalturaPDO',
		        'phptype' => 'mysql',
		        'database' => 'kaltura',
		        'hostspec' => 'localhost',
		        'user' => 'root',
		        'password' => '',
				'dsn' => 'mysql:host=localhost;port=3306;dbname=kaltura;user=root;password=;',
		      ),
		    ),
		    
		  
		    'propel2' => 
		    array (
		      'adapter' => 'mysql',
		      'connection' => 
		      array (
		      	'classname' => 'KalturaPDO',
		        'phptype' => 'mysql',
		         'database' => 'kaltura',
		        'hostspec' => 'localhost',
		        'user' => 'root',
		        'password' => '',
				'dsn' => 'mysql:host=localhost;port=3306;dbname=kaltura;user=root;password=;',
		      ),
		    ),
		    
		  
		    'propel3' => 
		    array (
		      'adapter' => 'mysql',
		      'connection' => 
		      array (
		      	'classname' => 'KalturaPDO',
		        'phptype' => 'mysql',
		         'database' => 'kaltura',
		        'hostspec' => 'localhost',
		        'user' => 'root',
		        'password' => '',
				'dsn' => 'mysql:host=localhost;port=3306;dbname=kaltura;user=root;password=;',
		      ),
		    ),
		    
		  
		    'thumbs_sql' => 
		    array (
		      'adapter' => 'mysql',
		      'connection' => 
		      array (
		      	'classname' => 'KalturaPDO',
		        'phptype' => 'mysql',
		         'database' => 'kaltura',
		        'hostspec' => 'localhost',
		        'user' => 'root',
		        'password' => '',
				'dsn' => 'mysql:host=localhost;port=3306;dbname=kaltura;user=root;password=;',
		      ),
		    ),
		    
		  
		    'sphinx_log' => 
		    array (
		      'adapter' => 'mysql',
		      'connection' => 
		      array (
		      	'classname' => 'KalturaPDO',
		        'phptype' => 'mysql',
		        'database' => 'kaltura',
		        'hostspec' => 'localhost',
		        'user' => 'root',
		        'password' => '',
				'dsn' => 'mysql:host=localhost;port=3306;dbname=kaltura;user=root;password=;',
		      ),
		    ),
		    
		  
		    'sphinx_log_read' => 
		    array (
		      'adapter' => 'mysql',
		      'connection' => 
		      array (
		      	'classname' => 'KalturaPDO',
		        'phptype' => 'mysql',
		        'database' => 'kaltura',
		        'hostspec' => 'localhost',
		        'user' => 'root',
		        'password' => '',
				'dsn' => 'mysql:host=localhost;port=3306;dbname=kaltura;user=root;password=;',
		      ),
		    ),
		    
		  
		    'sphinx' => 
		    array (
		      'adapter' => 'mysql',
		      'connection' => 
		      array (
				'dsn' => 'mysql:host=127.0.0.1;port=9312;',
		      ),
		    ),
			
		    'dwh' => 
		    array (
		      'adapter' => 'mysql',
		      'connection' => 
		      array (
				'dsn' => 'mysql:host=devtests.kaltura.dev;port=3306;dbname=kalturadw;user=kaltura;password=kaltura;',
		      ),
		    ),			
		    
		  ),
		  'log' => 
		  array (
		    'ident' => 'kaltura',
		    'level' => '7',
		  ),
		);
	}
	
}