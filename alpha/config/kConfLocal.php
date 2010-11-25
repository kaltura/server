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
				
				"corp_action_redirect" => "local.trunk",
			
				"memcache_host" => "localhost",
				"memcache_port" => "11211",
			
				"apphome_url" => "http://local.trunk",
				"apphome_url_no_protocol" => "local.trunk",
				"default_email" => "customer_service@local.trunk",
				"default_email_name" => "local kaltura Automated Response",
				"partner_registration_confirmation_email" => "registration_confirmation@local.trunk",
				"partner_registration_confirmation_name" => "local kaltura",
				"partner_notification_email" => "customer_service@local.trunk",
				"partner_notification_name" => "local kaltura Automated Response",
				"partner_change_email_email" => "customer_service@local.trunk",
				"partner_change_email_name" => "local kaltura Automated Response",
				"purchase_package_email" => "customer_service@local.trunk",
				"purchase_package_name" => "local kaltura Automated Response",
				"batch_download_video_sender_email" => "download_video@local.trunk",
				"batch_download_video_sender_name" => "local kaltura",
				"batch_flatten_video_sender_email" => "download_video@local.trunk",
				"batch_flatten_video_sender_name" => "local kaltura",
				"batch_notification_sender_email" => "notifications@local.trunk" , 
				"batch_notification_sender_name" => "local kaltura" ,	
				"batch_alert_email" => "alert@local.trunk" , 
				"batch_alert_name" => "local kaltura",
			
				"system_pages_login_username" => "root",
				"system_pages_login_password" => sha1('Abigail13'),				
				
				"bin_path_ffmpeg" => '/opt/bin//ffmpeg',
				"bin_path_mencoder" => '/opt/bin//mencoder',
				"bin_path_flix" => '/opt/bin//cli_encode',
				"bin_path_encoding_com" => '/opt/bin//encoding_com.php',
				"bin_path_imagemagick" => '/opt/bin//convert',
				"bin_path_curl" => '/opt/bin//curl',
				"bin_path_mediainfo" => '/opt/bin//mediainfo',
			
				"image_proxy_url" => "",
				"image_proxy_port" => "",	
				"image_proxy_secret" => "",
			
				"ga_account" => 'UA-7714780-1', //google analytics
				
				'track_kdpwrapper' => false,
			
				"reports_db_config" => array (
					"host" => "localhost",
					"user" => "root",
					"port" => "3306",
					"password" => "root" ,
					"db_name" => "kalturadw" , 
				),
				
				"event_log_file_path" => "/opt/Kaltura_Trunk/log/events.log",
				
				"dc_config" => array (
					"current" => "0",
					"list" => array (
								"0" => array ( "name" => "DC_0" ,
											   "url" => "http://local.trunk" ,
											   "external_url" => "http://local.trunk" ,
											   "secret" => "abc" ,
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
		
		
		// additional plugins
		self::$map['default_plugins'][] = "FileSyncPlugin"; // Should be enabled only on servers that run admin console
		self::$map['default_plugins'][] = "SystemUserPlugin"; // Should be enabled only on servers that run admin console
		self::$map['default_plugins'][] = "SystemPartnerPlugin"; // Should be enabled only on servers that run admin console
		self::$map['default_plugins'][] = "AdminConsolePlugin"; // Should be enabled only on servers that run admin console
		self::$map['default_plugins'][] = "MultiCentersPlugin"; // Should be enabled on multiple data centers
		self::$map['default_plugins'][] = "AuditPlugin"; // Should be enabled only if audit trail support required
		self::$map['default_plugins'][] = "VirusScanPlugin";
		self::$map['default_plugins'][] = "SymantecScanEnginePlugin";
		
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
		        'database' => 'kaltura_trunk',
		        'hostspec' => 'localhost',
		        'user' => 'root',
		        'password' => 'root',
				'dsn' => 'mysql:host=localhost;port=3306;dbname=kaltura_trunk;user=root;password=root;',
		      ),
		    ),
		    
		  
		    'propel2' => 
		    array (
		      'adapter' => 'mysql',
		      'connection' => 
		      array (
		      	'classname' => 'KalturaPDO',
		        'phptype' => 'mysql',
		        'database' => 'kaltura_trunk',
		        'hostspec' => 'localhost',
		        'user' => 'root',
		        'password' => 'root',
				'dsn' => 'mysql:host=localhost;port=3306;dbname=kaltura_trunk;user=root;password=root;',
		      ),
		    ),
		    
		  
		    'propel3' => 
		    array (
		      'adapter' => 'mysql',
		      'connection' => 
		      array (
		      	'classname' => 'KalturaPDO',
		        'phptype' => 'mysql',
		        'database' => 'kaltura_trunk',
		        'hostspec' => 'localhost',
		        'user' => 'root',
		        'password' => 'root',
				'dsn' => 'mysql:host=localhost;port=3306;dbname=kaltura_trunk;user=root;password=root;',
		      ),
		    ),
		    
		  
		    'thumbs_sql' => 
		    array (
		      'adapter' => 'mysql',
		      'connection' => 
		      array (
		      	'classname' => 'KalturaPDO',
		        'phptype' => 'mysql',
		        'database' => 'kaltura_trunk',
		        'hostspec' => 'localhost',
		        'user' => 'root',
		        'password' => 'root',
				'dsn' => 'mysql:host=localhost;port=3306;dbname=kaltura_trunk;user=root;password=root;',
		      ),
		    ),
		    
		  
		    'sphinx_log' => 
		    array (
		      'adapter' => 'mysql',
		      'connection' => 
		      array (
		      	'classname' => 'KalturaPDO',
		        'phptype' => 'mysql',
		        'database' => 'kaltura_trunk',
		        'hostspec' => 'localhost',
		        'user' => 'root',
		        'password' => 'root',
				'dsn' => 'mysql:host=localhost;port=3306;dbname=kaltura_trunk;user=root;password=root;',
		      ),
		    ),
		    
		  
		    'sphinx_log_read' => 
		    array (
		      'adapter' => 'mysql',
		      'connection' => 
		      array (
		      	'classname' => 'KalturaPDO',
		        'phptype' => 'mysql',
		        'database' => 'kaltura_trunk',
		        'hostspec' => 'localhost',
		        'user' => 'root',
		        'password' => 'root',
				'dsn' => 'mysql:host=localhost;port=3306;dbname=kaltura_trunk;user=root;password=root;',
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
				'dsn' => 'mysql:host=localhost;port=3306;dbname=kaltura_trunkdw;user=root;password=root;',
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