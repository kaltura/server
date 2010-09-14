<?php
class kConf
{
	private static $map = null;
	
	private static function init()
	{
		if ( self::$map ) return;
		/** KALTURA-INSTALL **/
		// will be used for the instalation script to modify 
		$bin_path = "@BIN_DIR@";

		self::$map =
		array (
		// take over the symfony config (sfConfig)
		
		
			/* ----------------------------------- */
			/* ---------- USER SETTINGS ---------- */
			/* ----------------------------------- */
		
					
			/* --- Environment settings --- */
			
				"date_default_timezone" => "@TIME_ZONE@",
					
					
				
			/* --- KMC module versions */
			
				"kmc_account_version" => 'v2.1.4',
				"kmc_appstudio_version" => 'v2.0.6',
				"kmc_content_version" => 'v3.0.2.1',
				"kmc_dashboard_version" => 'v1.0.13',
				"kmc_login_version" => 'v1.0.12.1',
				"kmc_rna_version" => 'v1.1.5',
			
			
			/* --- System pages modules --- */
			
				"system_pages_login_username" => "@SYSTEM_PAGES_LOGIN_USER@ ",
				"system_pages_login_password" => "@SYSTEM_PAGES_LOGIN_PASS@",
				
				
			/* --- Service hosts --- */
			
				"cdn_host" => "@CDN_HOST@",
				"iis_host" => "@IIS_HOST@",
				"www_host" => "@WWW_HOST@",
				"rtmp_url" => "@RTMP_URL@",
				"corp_action_redirect" => "@CORP_REDIRECT@",
				"apphome_url" => "@SERVICE_URL@",
				"apphome_url_no_protocol" => "@KALTURA_VIRTUAL_HOST_NAME@ ",
				
				
			/* --- Email settings --- */
			
				"default_email" => "customer_service@@KALTURA_VIRTUAL_HOST_NAME@ ",
				"default_email_name" => "@ENVIRONMENT_NAME@ Automated Response",
				"partner_registration_confirmation_email" => "registration_confirmation@@KALTURA_VIRTUAL_HOST_NAME@ ",
				"partner_registration_confirmation_name" => "@ENVIRONMENT_NAME@",
				"partner_notification_email" => "customer_service@@KALTURA_VIRTUAL_HOST_NAME@ ",
				"partner_notification_name" => "@ENVIRONMENT_NAME@ Automated Response",
				"partner_change_email_email" => "customer_service@@KALTURA_VIRTUAL_HOST_NAME@ ",
				"partner_change_email_name" => "@ENVIRONMENT_NAME@ Automated Response",
				"purchase_package_email" => "customer_service@@KALTURA_VIRTUAL_HOST_NAME@ ",
				"purchase_package_name" => "@ENVIRONMENT_NAME@ Automated Response",
				"batch_download_video_sender_email" => "download_video@@KALTURA_VIRTUAL_HOST_NAME@ ",
				"batch_download_video_sender_name" => "@ENVIRONMENT_NAME@",
				"batch_flatten_video_sender_email" => "download_video@@KALTURA_VIRTUAL_HOST_NAME@ ",
				"batch_flatten_video_sender_name" => "@ENVIRONMENT_NAME@",
				"batch_notification_sender_email" => "notifications@@KALTURA_VIRTUAL_HOST_NAME@ " , 
				"batch_notification_sender_name" => "@ENVIRONMENT_NAME@" ,	
				"batch_alert_email" => "alert@@KALTURA_VIRTUAL_HOST_NAME@ " , 
				"batch_alert_name" => "@ENVIRONMENT_NAME@" ,
				
				
			/* --- Directories --- */
			
				"flash_root_url" => "",
				"uiconf_root_url" => "",
				"content_root_url" => "",
				"cache_root_path" => "@APP_DIR@/cache",
				"event_log_file_path" => "@LOG_DIR@/events.log",
				
				
			/* --- Data centers --- */
				
				"reports_db_config" => array (
					"host" => "@DWH_HOST@",
					"user" => "@DWH_USER@" ,
					"port" => "@DWH_PORT@",
					"password" => "@DWH_PASS@" ,
					"db_name" => "@DWH_DATABASE_NAME@" , 
				) ,
				
				"dc_config" => array (
					"current" => "0",
					"list" => array (
								"0" => array ( "name" => "DC_0" ,
											   "url" => "@SERVICE_URL@" ,
											   "external_url" => "@SERVICE_URL@" ,
											   "secret" => "@DC0_SECRET@" ,
											   "root" => "@WEB_DIR@/" )
					)
				),
				
			
			/* --- External services passwords --- */
			
				"ga_account" => '@GOOGLE_ANALYTICS_ACCOUNT@', //google analytics
				"limelight_madiavault_password" => "",
				"level3_authentication_key" => "",
				"akamai_auth_smooth_param" => "",
				"akamai_auth_smooth_salt" => "",
				
				
			/* ******** Different ******** */		
			
				"kaltura_installation_type" => "@INSTALLATION_TYPE@",
				"kaltura_activation_key" => false,
				"replace_passwords" => false,
				"kaltura_version" => "@KALTURA_VERSION@",
				"report_admin_email" => "@REPORT_ADMIN_EMAIL@",
				"memcache_host" => "@MEMCACHE_HOST@",
				"memcache_port" => "11211",
				"image_proxy_url" => "",
				"image_proxy_port" => "",	
				"image_proxy_secret" => "",
				'track_kdpwrapper' =>@TRACK_KDPWRAPPER@,
				'kdpwrapper_track_url' => "http://kalstats.kaltura.com/index.php/events/player_event",//TODO: CHANGE!
				
				"url_managers" => array(), /* should be filled up if installations supports adding CDNs */
				
				/* ------------------------------------- */
				/* ---------- SYSTEM SETTINGS ---------- */
				/* ------------------------------------- */
				/* --- Default plugins --- */
				"default_plugins" => array(
					"FileSyncPlugin",
					"StorageProfilePlugin",
					"SystemUserPlugin",
					"SystemPartnerPlugin",
					"AdminConsolePlugin",
				),
				
				
				
				"kmc_admin_login_generic_regexp" => "/__([0-9]+)__@kaltura.com/" ,			// used as generic backdoor to kmc
				"kmc_admin_login_sha1_password" => "@KMC_BACKDOR_SHA1_PASS@" ,
				
				
						

				"sf_debug" => false,
				"sf_logging_enabled" => true,
				"sf_root_dir" => @APP_DIR.'/alpha/',
								
				"enable_cache" => false,
			
				"terms_of_use_uri" => "index.php/terms",

				"server_api_v2_path" => "/api/" ,
			
				"default_flow_manager_class" => "kFlowManager",

				"default_duplication_time_frame" => 3600 ,
				"job_duplication_time_frame" => array(
					1 => 7200, //BatchJob::BATCHJOB_TYPE_IMPORT
				) ,
			
				"default_job_execution_attempt" => 3 ,
				"job_execution_attempt" => array(
					16 => 5, //BatchJob::BATCHJOB_TYPE_NOTIFICATION
					4 => 1, //BatchJob::BATCHJOB_TYPE_BULK_UPLOAD
					23 => 2, //BatchJob::BATCHJOB_TYPE_STORAGE_EXPORT
				) ,
			
				"default_job_retry_interval" => 20 ,
				"job_retry_intervals" => array(
					16 => 600, // BatchJob::BATCHJOB_TYPE_NOTIFICATION
					15 => 150, // BatchJob::BATCHJOB_TYPE_MAIL
					1 => 300, // BatchJob::BATCHJOB_TYPE_IMPORT
					23 => 300, // BatchJob::BATCHJOB_TYPE_STORAGE_EXPORT
				) ,
				
				"ignore_cdl_failure" => false,
			
				"batch_ignore_duplication" => true ,
				"priority_percent" => array(1 => 33, 2 => 27, 3 => 20, 4 => 13, 5 => 7),
				"priority_time_range" => 60,
		
				"system_allow_edit_kConf" => false,
				"testmeconsole_state" => true,

				"bin_path_ffmpeg" => $bin_path . "ffmpeg" ,
				"bin_path_mencoder" => $bin_path . "mencoder",
				"bin_path_flix" => $bin_path . "cli_encode",
				"bin_path_encoding_com" => $bin_path . "encoding_com.php",
				"bin_path_imagemagick" => $bin_path . "convert",
				"bin_path_curl" => $bin_path . "curl",
				"bin_path_mediainfo" => $bin_path . "mediainfo",
				
				/* kmc tabs rules */
				"kmc_display_customize_tab" => true,
				"kmc_display_account_tab" => true, 
				"kmc_content_enable_commercial_transcoding" => true, 
				"kmc_content_enable_live_streaming" => true,
				"kmc_login_show_signup_link" => false,
				"kmc_display_developer_tab" => false,
				"kmc_display_server_tab" => false,
				"kmc_account_show_usage" => true,

				
				"kmc_rna_allowed_partners" => array(),

				"paypal_data" => array (),

				"kaltura_partner_id" => "",	
				"template_partner_id" => 99,
				"kaltura_email_hash" => "admin",
				
		);
	}

	public static function get ( $param_name )
	{
		self::init();
		$res = self::$map [ $param_name ];
		// for now - throw an exception if now param in config - it will help prevent typos
		if ( $res === null ) throw new Exception ( "Cannot find [$param_name] in config" ) ;
		// kLog::log( "kConf [$param_name]=[$res]" );
		return $res;
	}

	public static function hasParam($param_name)
	{
		return array_key_exists($param_name, self::$map);
	}

}
?>
