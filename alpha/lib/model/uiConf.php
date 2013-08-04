<?php

/**
 * Subclass for representing a row from the 'ui_conf' table.
 *
 *
 *
 * @package Core
 * @subpackage model
 */
class uiConf extends BaseuiConf implements ISyncableFile
{
	const MYSQL_CODE_DUPLICATE_KEY = 23000;
	
	const UI_CONF_TYPE_GENERIC = 0;
	const UI_CONF_TYPE_WIDGET = 1;
	const UI_CONF_TYPE_CW = 2;
	const UI_CONF_TYPE_EDITOR = 3;
	const UI_CONF_TYPE_ADVANCED_EDITOR = 4;
	const UI_CONF_TYPE_PLAYLIST = 5;			// maybe this is in fact type WIDGET with some flags switched on ?!?
	const UI_CONF_TYPE_KMC_APP_STUDIO = 6;
	const UI_CONF_TYPE_KRECORD = 7;
	const UI_CONF_TYPE_KDP3 = 8;
	const UI_CONF_TYPE_KMC_ACCOUNT = 9;
	const UI_CONF_TYPE_KMC_ANALYTICS = 10;
	const UI_CONF_TYPE_KMC_CONTENT = 11;
	const UI_CONF_TYPE_KMC_DASHBOARD = 12;
	const UI_CONF_TYPE_KMC_LOGIN = 13;
	const UI_CONF_TYPE_SLP = 14;
	const UI_CONF_CLIENTSIDE_ENCODER = 15;
	const UI_CONF_KMC_GENERAL = 16;
	const UI_CONF_KMC_ROLES_AND_PERMISSIONS = 17;
	const UI_CONF_CLIPPER = 18;
	const UI_CONF_TYPE_KSR = 19;
	const UI_CONF_TYPE_KUPLOAD = 20;
	const UI_CONF_TYPE_HTML5 = 21;


	const UI_CONF_CREATION_MODE_MANUAL = 1;
	const UI_CONF_CREATION_MODE_WIZARD = 2;
	const UI_CONF_CREATION_MODE_ADVANCED = 3;

	// status
	const UI_CONF_STATUS_PENDING = 1;
	const UI_CONF_STATUS_READY = 2;
	const UI_CONF_STATUS_DELETED = 3;

	const FILE_NAME_FEATURES = "features";
	const FILE_NAME_CONFIG = "config";

	const FILE_SYNC_UICONF_SUB_TYPE_DATA = 1;
	const FILE_SYNC_UICONF_SUB_TYPE_FEATURES = 2;
	const FILE_SYNC_UICONF_SUB_TYPE_CONFIG = 3;


	private static $UI_CONF_OBJ_TYPE_MAP = null;
	private static $REQUIRE_UI_CONF_FILE_FOR_TYPE = null;

	private $should_call_set_data_content = false;
	private $should_call_set_data_content2 = false;
	private $should_call_set_data_content_config = false;
	private $data_content = null;
	private $data_content_2 = null;
	private $data_content_config = null;

	private $swf_url_version = null;

	private static $swf_names = array ( self::UI_CONF_TYPE_WIDGET => "kdp.swf" ,
										self::UI_CONF_TYPE_CW => "ContributionWizard.swf" ,
										self::UI_CONF_TYPE_EDITOR => "simpleeditor.swf" ,
										self::UI_CONF_TYPE_ADVANCED_EDITOR => "KalturaAdvancedVideoEditor.swf" ,
										self::UI_CONF_TYPE_PLAYLIST => "kdp.swf" ,
										self::UI_CONF_TYPE_KMC_APP_STUDIO => "applicationstudio.swf",
										self::UI_CONF_TYPE_KDP3 => "kdp3.swf",
										self::UI_CONF_TYPE_KMC_ACCOUNT => "account.swf",
										self::UI_CONF_TYPE_KMC_ANALYTICS => "ReportsAndAnalytics.swf",
										self::UI_CONF_TYPE_KMC_CONTENT => "content.swf",
										self::UI_CONF_TYPE_KMC_DASHBOARD => "dashboard.swf",
										self::UI_CONF_TYPE_KMC_LOGIN => "login.swf",
										self::UI_CONF_TYPE_SLP => "KalturaPlayer.xap",
										self::UI_CONF_CLIENTSIDE_ENCODER => "KEU_0.8_win.msi",
										self::UI_CONF_KMC_GENERAL => "kmc.swf",
										self::UI_CONF_KMC_ROLES_AND_PERMISSIONS => "",
										self::UI_CONF_CLIPPER => "",
										self::UI_CONF_TYPE_KSR => "ScreencastOMaticRun-1.0.32.jar",
										self::UI_CONF_TYPE_KRECORD => "KRecord.swf",
										self::UI_CONF_TYPE_KUPLOAD => "KUpload.swf",
										self::UI_CONF_TYPE_HTML5 => "kdp3.swf",
									);

	private static $swf_directory_map = array (
		self::UI_CONF_TYPE_WIDGET => "kdp",
		self::UI_CONF_TYPE_CW => "kcw",
		self::UI_CONF_TYPE_EDITOR => "kse",
		self::UI_CONF_TYPE_ADVANCED_EDITOR => "kae",
		self::UI_CONF_TYPE_PLAYLIST => "kdp",
		self::UI_CONF_TYPE_KMC_APP_STUDIO => "kmc/appstudio",
		self::UI_CONF_TYPE_KDP3 => "kdp3",
		self::UI_CONF_TYPE_KMC_ACCOUNT => "kmc/account",
		self::UI_CONF_TYPE_KMC_ANALYTICS => "kmc/analytics",
		self::UI_CONF_TYPE_KMC_CONTENT => "kmc/content",
		self::UI_CONF_TYPE_KMC_DASHBOARD => "kmc/dashboard",
		self::UI_CONF_TYPE_KMC_LOGIN => "kmc/login",
		self::UI_CONF_TYPE_SLP => "slp",
		self::UI_CONF_CLIENTSIDE_ENCODER => "expressUploader",
		self::UI_CONF_KMC_GENERAL => "kmc",
		self::UI_CONF_KMC_ROLES_AND_PERMISSIONS => "",
		self::UI_CONF_CLIPPER => "kclip",
		self::UI_CONF_TYPE_KSR => "ksr",
		self::UI_CONF_TYPE_KRECORD => 'krecord',
		self::UI_CONF_TYPE_KUPLOAD => "kupload",
		self::UI_CONF_TYPE_HTML5 => "kdp3",
	);

	public function save(PropelPDO $con = null, $isClone = false)
	{
		$this->validateConfFilesExistance();
			
		try
		{
			$res = parent::save( $con );
		}
		catch (PropelException $e)
		{
			/**
			 * Because many ui-conf objects have hard-coded id, the auto-incremented id of new ui-conf could exist in the db.
			 * Just retry to save the ui-conf with a different auto-inceremented id.
			 */
			
			if($e->getCause()->getCode() == self::MYSQL_CODE_DUPLICATE_KEY) //unique constraint
				$res = parent::save( $con );
		}
		
		if($this->should_call_set_data_content2 || $this->should_call_set_data_content || $this->should_call_set_data_content_config)
		{
			$confFile = $this->getConfFile();
			$confFile2 = $this->getConfFile2();
			$confFileConfig = $this->getConfFileConfig();

			if($isClone)
			{
				$this->setVersion(1);
			}
			else
			{
				$version = $this->getVersion();
				if ( ! is_numeric( $version ) ) $this->setVersion(1);
				else $this->setVersion($version+1);
			}

			if ($confFile)
				$this->saveConfFileToDisk($confFile, null, $isClone); // save uiConf.xml

			if ($confFile2)
				$this->saveConfFileToDisk($confFile2, self::FILE_NAME_FEATURES, $isClone); // save uiConf.xml.features
				
			if ($confFileConfig)
				$this->saveConfFileToDisk($confFileConfig, self::FILE_NAME_CONFIG, $isClone); // save uiConf.xml.config

			$this->should_call_set_data_content = false; // clear dirty flag
			$this->should_call_set_data_content2 = false; // clear dirty flag
			$this->should_call_set_data_content_config = false; // clear dirty flag
			$res = parent::save( $con );
		}
		$this->getConfFilePath();
		return $res;
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/BaseuiConf#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);

		$objectDeleted = false;
		if($this->isColumnModified(uiConfPeer::STATUS) && $this->getStatus() == self::UI_CONF_STATUS_DELETED)
			$objectDeleted = true;

		$ret = parent::postUpdate($con);

		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));

		return $ret;
	}

	private function validateConfFilesExistance()
	{
		if($this->requireFileForUiConfType() && $this->isNew())
		{
			if(!$this->data_content)
				$this->setConfFile('');
			if($this->getCreationMode() == self::UI_CONF_CREATION_MODE_WIZARD && !$this->data_content_2)
				$this->setConfFileFeatures('');
			if(!$this->data_content_config)
				$this->setConfFileConfig('');
		}
	}

	private static function initUiConfTypeMap()
	{
		if ( self::$UI_CONF_OBJ_TYPE_MAP == null )
		{
			self::$UI_CONF_OBJ_TYPE_MAP = array (
				self::UI_CONF_TYPE_GENERIC => "Generic",
				self::UI_CONF_TYPE_WIDGET => "Widget",
				self::UI_CONF_TYPE_CW => "Contribution Wizard",
				self::UI_CONF_TYPE_EDITOR => "Simple Editor",
				self::UI_CONF_TYPE_ADVANCED_EDITOR => "Advanced Editor",
				self::UI_CONF_TYPE_PLAYLIST => "Playlist",
				self::UI_CONF_TYPE_KDP3 => "KDP3",
				self::UI_CONF_TYPE_KMC_APP_STUDIO => "KMC AppStudio",
				self::UI_CONF_TYPE_KMC_ACCOUNT => "KMC Account",
				self::UI_CONF_TYPE_KMC_ANALYTICS => "KMC Analytics",
				self::UI_CONF_TYPE_KMC_CONTENT => "KMC Content",
				self::UI_CONF_TYPE_KMC_DASHBOARD => "KMC Dashboard",
				self::UI_CONF_TYPE_KMC_LOGIN => "KMC Login",
				self::UI_CONF_TYPE_SLP => "SLP",
				self::UI_CONF_CLIENTSIDE_ENCODER => "Express Uploader",
				self::UI_CONF_KMC_GENERAL => "KMC",
				self::UI_CONF_KMC_ROLES_AND_PERMISSIONS => "KMC Roles and Permissions",
				self::UI_CONF_CLIPPER => "Kaltura Clipper",
				self::UI_CONF_TYPE_KSR => "Kaltura Screen Recorder",
				self::UI_CONF_TYPE_KUPLOAD => "Kaltura Simple Uploader",
				self::UI_CONF_TYPE_HTML5 => "HTML5",
			);
		}
	}

	private static function initUiConfRequiredFile()
	{
		if ( self::$REQUIRE_UI_CONF_FILE_FOR_TYPE == null )
		{
			self::$REQUIRE_UI_CONF_FILE_FOR_TYPE = array (
				self::UI_CONF_TYPE_GENERIC => false,
				self::UI_CONF_TYPE_WIDGET => true,
				self::UI_CONF_TYPE_CW => true,
				self::UI_CONF_TYPE_EDITOR => true,
				self::UI_CONF_TYPE_ADVANCED_EDITOR => true,
				self::UI_CONF_TYPE_PLAYLIST => true,
				self::UI_CONF_TYPE_KMC_APP_STUDIO => true,
				self::UI_CONF_TYPE_KRECORD => false,
				self::UI_CONF_TYPE_KDP3 => true,
				self::UI_CONF_TYPE_KMC_ACCOUNT => true,
				self::UI_CONF_TYPE_KMC_ANALYTICS => true,
				self::UI_CONF_TYPE_KMC_CONTENT => true,
				self::UI_CONF_TYPE_KMC_DASHBOARD => true,
				self::UI_CONF_TYPE_KMC_LOGIN => true,
				self::UI_CONF_TYPE_SLP => true,
				self::UI_CONF_CLIENTSIDE_ENCODER => true,
				self::UI_CONF_KMC_GENERAL => true,
				self::UI_CONF_KMC_ROLES_AND_PERMISSIONS => false,
				self::UI_CONF_CLIPPER => false,
				self::UI_CONF_TYPE_KSR => true,
				self::UI_CONF_TYPE_KUPLOAD => false,
				self::UI_CONF_TYPE_HTML5 => true,
			);
		}
	}

	public function isValid()
	{
		if($this->requireFileForUiConfType())
		{
			try{
				$content = $this->getConfFile(true);
				if($this->getCreationMode() == self::UI_CONF_CREATION_MODE_WIZARD)
					$content2 = $this->getConfFile2(true);
				$contentConfig = $this->getConfFileConfig(true);
			}
			catch(Exception $ex)
			{
				return false;
			}
		}
		return true;
	}

	public function getUiConfTypeMap()
	{
		self::initUiConfTypeMap();
		return self::$UI_CONF_OBJ_TYPE_MAP;
	}

	public function getObjTypeAsString ( )
	{
		self::initUiConfTypeMap();
		return self::$UI_CONF_OBJ_TYPE_MAP[$this->getType()];
	}

	public function getType()
	{
		$t = parent::getObjType();
		if ( empty ( $t ) ) $t = self::UI_CONF_TYPE_WIDGET;
		return $t;
	}

	public function requireFileForUiConfType( )
	{
		self::initUiConfRequiredFile();
		return self::$REQUIRE_UI_CONF_FILE_FOR_TYPE[$this->getType()];
	}

	/**
	 * (non-PHPdoc)
	 * @see lib/model/ISyncableFile#getSyncKey()
	 */
	public function getSyncKey ( $sub_type , $version = null )
	{
		self::validateFileSyncSubType ( $sub_type );
		$key = new FileSyncKey();
		$key->object_type = FileSyncObjectType::UICONF;
		$key->object_sub_type = $sub_type;
		$key->object_id = $this->getId();
//		if ( $sub_type == self::FILE_SYNC_UICONF_SUB_TYPE_DATA )
		// TODO - add version to the DB
		$key->version = $this->getVersion();

		$key->partner_id=$this->getPartnerId();
		return $key;
	}



	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFileName()
	 */
	public function generateFileName( $sub_type, $version = null)
	{
		self::validateFileSyncSubType ( $sub_type );

		if ( $sub_type == self::FILE_SYNC_UICONF_SUB_TYPE_DATA )
			return "ui_conf{$version}.xml";

		if ( $sub_type == self::FILE_SYNC_UICONF_SUB_TYPE_FEATURES )
			return "ui_conf.features{$version}.xml";
			
		if ( $sub_type == self::FILE_SYNC_UICONF_SUB_TYPE_CONFIG )
			return "ui_conf.config{$version}.xml";

		return null;
	}

	/**
	 * (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFilePathArr()
	 */
	public function generateFilePathArr ( $sub_type, $version = null  )
	{
		// TODO - implement field version
		self::validateFileSyncSubType ( $sub_type );
		if ( $sub_type == self::FILE_SYNC_UICONF_SUB_TYPE_DATA )
			$res =$this->getConfFilePathImpl( null , true , $version);
		elseif ( $sub_type == self::FILE_SYNC_UICONF_SUB_TYPE_FEATURES )
			$res =$this->getConfFilePathImpl( self::FILE_NAME_FEATURES, false, $version );
		elseif ( $sub_type == self::FILE_SYNC_UICONF_SUB_TYPE_CONFIG )
			$res =$this->getConfFilePathImpl( self::FILE_NAME_CONFIG, false, $version );

		$file_root = myContentStorage::getFSContentRootPath( );
		$file_path = str_replace ( myContentStorage::getFSContentRootPath( ) , "" , $res );
		return array ( $file_root , $file_path )	;
	}


	/**
	 * Enter description here...
	 *
	 * @var FileSync
	 */
	private $m_file_sync;

	/**
	 * @return FileSync
	 */
	public function getFileSync ( )
	{
		return $this->m_file_sync;
	}

	public function setFileSync ( FileSync $file_sync )
	{
		 $this->m_file_sync = $file_sync;
	}

	private static function validateFileSyncSubType ( $sub_type )
	{
		$validSubTypes = array ( self::FILE_SYNC_UICONF_SUB_TYPE_DATA ,  self::FILE_SYNC_UICONF_SUB_TYPE_FEATURES, self::FILE_SYNC_UICONF_SUB_TYPE_CONFIG );
		if ( !in_array($sub_type, $validSubTypes))
			throw new FileSyncException ( FileSyncObjectType::UICONF ,$sub_type , $validSubTypes );
	}

	private function saveConfFileToDisk($v , $file_suffix = null , $isClone = false)
	{
		$this->setConfFileImpl($v, $file_suffix, $isClone);
	}

	private function setConfFileImpl ( $v , $file_suffix = null , $isClone = false )
	{
//		$file_name = $this->getConfFilePath($file_suffix );
		if ( $this->getCreationMode() == self::UI_CONF_CREATION_MODE_MANUAL )
		{
			throw new Exception ( "Should not edit MANUAL ui_confs via the API!! Only via the SVN" );
		}

		if ( $file_suffix == self::FILE_NAME_FEATURES)
		{
			$sync_key = $this->getSyncKey( self::FILE_SYNC_UICONF_SUB_TYPE_FEATURES );
		}
		elseif ($file_suffix == self::FILE_NAME_CONFIG)
		{
			$sync_key = $this->getSyncKey( self::FILE_SYNC_UICONF_SUB_TYPE_CONFIG );
		}
		else
		{
			$sync_key = $this->getSyncKey( self::FILE_SYNC_UICONF_SUB_TYPE_DATA );
		}

		$this->setUpdatedAt( time() ); // make sure will be updated in the DB

		if (version_compare($this->getSwfUrlVersion(), "2.5", ">="))
		{
			$v = str_replace('.entryName}', '.name}', $v);
		}

		// This is only called on Save, after parent::save(), so ID is present.
		kFileSyncUtils::file_put_contents( $sync_key , $v ); //replaced__setFileContent
	}

	public function setConfFile ( $v /*, $increment_version = true */ )
	{
		if ( $v !== null )
		{
			$this->data_content = $v;
			$this->should_call_set_data_content = true;
		}
		elseif($this->isNew() && $this->requireFileForUiConfType())
		{
			$this->data_content = "";
			$this->should_call_set_data_content = true;
		}
	}

	private function getContentFileImpl ( $file_suffix = null , $strict = true)
	{
		if ( $file_suffix == self::FILE_NAME_FEATURES )
		{
			$sync_key = $this->getSyncKey( self::FILE_SYNC_UICONF_SUB_TYPE_FEATURES );
			$strict = false; // this file is optional
		}
		elseif ($file_suffix == self::FILE_NAME_CONFIG)
		{
			$sync_key = $this->getSyncKey( self::FILE_SYNC_UICONF_SUB_TYPE_CONFIG );
			$strict = false; // this file is optional
		}		
		else
		{
			$sync_key = $this->getSyncKey( self::FILE_SYNC_UICONF_SUB_TYPE_DATA );
			if(!$this->getId() || !$strict) // when doing autoFillObjectFromObject, ID might be missing, and in that case file is probably not mandatory
				$strict = false; // object has no ID or requested not to be strict at all
			else
				$strict = true; // object has ID or strict specified, so no file will be found for incomplete key
		}

		return kFileSyncUtils::file_get_contents( $sync_key , true , $strict );
	}

	public function getConfFile( $force_fetch = false , $strict = true )
	{
		$contents = "";

		if ( $this->data_content !== null && ! $force_fetch ) return $this->data_content;
		if(!$this->requireFileForUiConfType())
			$strict = false;

		$contents = $this->getContentFileImpl( null , $strict );
		return $contents;
	}

	public function setConfFile2 ( $v /*, $increment_version = true */ )
	{
		if ( $v !== null )
		{
			$this->data_content_2 = $v;
			$this->should_call_set_data_content2 = true;
		}
		elseif($this->isNew() && $this->requireFileForUiConfType())
		{
			$this->data_content_2 = "";
			$this->should_call_set_data_content2 = true;
		}
	}

	// will fetch
	public function getConfFile2 ( $force_fetch = false , $strict = true )
	{
		$contents = "";

		if ( $this->data_content_2 !== null  && ! $force_fetch ) return $this->data_content_2;

		if(!$this->requireFileForUiConfType())
			$strict = false;

		$contents = $this->getContentFileImpl( self::FILE_NAME_FEATURES , $strict);
		return $contents;
	}

	public function setConfFileFeatures ( $v )
	{
		return $this->setConfFile2( $v );
	}

	// check this !
	public function getConfFileFeatures ( $force_fetch = false , $strict = true )
	{
		return $this->getConfFile2( $force_fetch = false , $strict = true );
	}

	public function setConfFileConfig ( $v /*, $increment_version = true */ )
	{
		if ( $v !== null )
		{
			$this->data_content_config = $v;
			$this->should_call_set_data_content_config = true;
		}
		elseif($this->isNew() && $this->requireFileForUiConfType())
		{
			$this->data_content_config = "";
			$this->should_call_set_data_content_config = true;
		}
	}

	// will fetch
	public function getConfFileConfig ( $force_fetch = false , $strict = true )
	{
		$contents = "";

		if ( $this->data_content_config !== null  && ! $force_fetch ) return $this->data_content_config;

		if(!$this->requireFileForUiConfType())
			$strict = false;

		$contents = $this->getContentFileImpl( self::FILE_NAME_CONFIG , $strict);
		return $contents;
	}
	
	private $m_file_time;
	private function getFileTime()
	{
		if ( ! $this->m_file_time )
			$this->m_file_time = strftime( "%Y-%m-%d_%H-%M-%S" , time() );
		return $this->m_file_time;
	}

	public function getSwfUrl ( $raw_only = false )
	{
		$raw = parent::getSwfUrl();
		if ( $raw_only ) return $raw;
		$root_url = kConf::get ( "flash_root_url");
		if ( ! $root_url )
			return $raw;
		if ( strpos ( $raw , $root_url) === 0 )
		{
			// if the raw url already has the exact prefix of root_url - return the raw - no need to re-append it
			return 	$raw;
		}

		if ( strpos ( $raw , "http://" ) === 0 )
		{
			// if the raw url starts with http - don't append to it
			return 	$raw;
		}

		return $root_url . $raw;
	}

	// use this field only if the version is not empty
	public function setSwfUrlVersion($version)
	{
		$flashUrl = myContentStorage::getFSFlashRootPath();
		$swfName = $this->getSwfNameFromType();
		$dir = $this->getDirectoryFromType();
	
		if($version)
		{
			if (strpos($this->swf_url, "kdp3") !== false)
				$this->setSwfUrl("$flashUrl/kdp3/v{$version}/kdp3.swf");
			else
				$this->setSwfUrl("$flashUrl/$dir/v{$version}/$swfName");
		}
	}

	public function getSwfUrlVersion ()
	{
		$swf_url = $this->getSwfUrl();
		$flash_url = myContentStorage::getFSFlashRootPath ();
		$match = preg_match ( '/\/v([\w\d\.]+)/' , $swf_url , $version );
		if ( $match )
		{
			return $version[1];
		}
		return null;
	}

	private function getCachedContent ( $kaltura_config , $confFilePath )
	{
		if ( ! file_exists ( $confFilePath ) ) return null;
		if ( strpos ( $confFilePath , "://" ) != FALSE )
		{
			// remote file (http:// or ftp://) - store the cache in a directory near the base file
			//$cache_path = dirname( $kaltura_config ) . "cache/" . $confFilePath  . "_cache.xml" ;
			// for now - don't cache for remote files
			$cache_path = null;
		}
		else
		{
			// this is a local file - store the cache file in the same directory
			$cache_path = str_replace ( "/uiconf/" , "/cacheuiconf/" ,$confFilePath ) . "_cache.xml";
			kFile::fullMkdir( $cache_path );
		}
		try
		{
			$s_time = microtime( true );
			$config = new kXmlConfig( $kaltura_config , $confFilePath );
			$content = $config->getConfig( $cache_path );
			$e_time = microtime( true );

			if ( $config->createdCache() )
				KalturaLog::log( __METHOD__ . " created config cache file [$kaltura_config]+[$confFilePath]->[$cache_path].\ntook [" . ($e_time - $s_time) . "] seconds" );

			return $content;
		}
		catch ( Exception $ex )
		{
			KalturaLog::log( __METHOD__ . " Error creating config [$kaltura_config]+[$confFilePath]:" . $ex->getMessage() );
			return null;
		}
	}

	// TODO fix when add creation_mode to the DB
	public function getCreationModeAsStr()
	{
		return self::UI_CONF_CREATION_MODE_WIZARD;
	}

	// TODO - remove this function after Andromeda deployment is stable
	public function internalGetParentConfFilePath()
	{
		return parent::getConfFilePath();
	}

	public function getConfFilePath( $file_suffix = null , $inc_version = false )
	{
		return $this->getConfFilePathImpl( $file_suffix ,$inc_version );
	}

	private function getConfFilePathImpl( $file_suffix = null , $inc_version = false, $version = null )
	{
		$conf_file_path = parent::getConfFilePath();

		if ( $this->getCreationMode() != self::UI_CONF_CREATION_MODE_MANUAL )
		{
			if( ! $conf_file_path || $inc_version || $version)
			{
				if ( ! $this->getId() ) return null;

				$conf_file_path = $this->createConfFilePath($version);
				$this->setConfFilePath( $conf_file_path );
			}
		}

		// will fix the current problem in the DB- we hold the root in the conf_file_path
		$conf_file_path = myContentStorage::getFSContentRootPath( ).str_replace ( "/web/" , "" , $conf_file_path )  ;

		if ( $file_suffix )
		{
			// use the file_suffix before the extension
			$extension = pathinfo ( $conf_file_path , PATHINFO_EXTENSION );
			$conf_file_path = str_replace ( $extension , "$file_suffix.$extension" , $conf_file_path );
		}

		return $conf_file_path;
	}

	/*
	 * Should not be used as updateable field until the paths on disk are safe to set
	 */
	public function setConfFilePath( $v )
	{
		if ( kString::beginsWith( $v , ".." ) )
		{
			$err = "Error in " . __METHOD__ . ": attmpting to set ConfFilePath to [$v]";
			KalturaLog::log( $err );
			throw new APIException ( APIErrors::ERROR_SETTING_FILE_PATH_FOR_UI_CONF , $v );
		}

		if ( $this->getCreationMode() == self::UI_CONF_CREATION_MODE_MANUAL )
		{
			if ( ! kString::beginsWith( $v , $this->getUiConfRootDirectory() . "uiconf/" ) )
			{
				$v =  $this->getUiConfRootDirectory() . "uiconf/" . $v ;
			}

			$real_v = realpath( dirname( $v ) ) . "/" . pathinfo( $v , PATHINFO_BASENAME );

			if ( $v )
			{
				if ( $real_v )
				{
/*
 * TODO - add this id the service IS externally use via the API
					// the file exists - make sure we're not overiding someone elses file
					$ui_confs_with_same_path = uiConfPeer::retrieveByConfFilePath ( $real_v , $this->getId() );
					foreach ( $ui_confs_with_same_path as $ui_conf  )
					{
						if ( $ui_conf->getPartnerId ( ) != $this->getPartnerId() )
						{
							$err = "Error in " . __METHOD__ . ": attmpting to set ConfFilePath to [$v]";
							KalturaLog::log( $err );
							throw new APIException ( APIErrors::ERROR_SETTING_FILE_PATH_FOR_UI_CONF , $v );
						}
					}
*/
					$v = $real_v;
				}
			}
			parent::setConfFilePath( $v );
		}
		else
		{
			parent::setConfFilePath( $v );
//			throw new APIException ( APIErrors::ERROR_SETTING_FILE_PATH_FOR_UI_CONF , $v );
		}
	}

	private function createConfFilePath ($version = null)
	{
		if ( $this->getVersion() || $version)
			$version = "_" . ($version ? $version : $this->getVersion());
		else
			$version = "";

		// html5 player uses JSON instead of XML
		$ext = "xml";
		if( $this->getType() === self::UI_CONF_TYPE_HTML5 ) {
			$ext = "json";
		}

		$dir = (intval($this->getId() / 1000000)).'/'.	(intval($this->getId() / 1000) % 1000);
		$file_name = "/content/generatedUiConf/$dir/ui_conf_{$this->getId()}_{$version}.$ext";
		return $file_name;
	}

	// IMPORTANT : WILL NOT include the uiconf or generatedUiconf part of the path
	private function getUiConfRootDirectory ()
	{
		$content_path = myContentStorage::getFSContentRootPath();
		return 	$content_path . "content/";
	}

	/*
	 * will create a new uiConf object in the DB from this object while using fields from
	 */
	public function cloneToNew ( $new_ui_conf_obj , $new_name = null )
	{
		$cloned = new uiConf();

		$all_fields = uiConfPeer::getFieldNames ();
		$ignore_list = array ( "Id" , "ConfFilePath" );
		// clone from current
		baseObjectUtils::fillObjectFromObject( $all_fields ,
			$this ,
			$cloned ,
			baseObjectUtils::CLONE_POLICY_PREFER_NEW , $ignore_list , BasePeer::TYPE_PHPNAME );

//		$cloned->setNew(true);
		// override with data from the $new_ui_conf_obj - the name can be chosen to override
		if ( $new_ui_conf_obj )
		{
			baseObjectUtils::fillObjectFromObject( $all_fields ,		// assume the new_ui_conf_obj can be fully copied to the cloned
				$new_ui_conf_obj ,
				$cloned ,
				baseObjectUtils::CLONE_POLICY_PREFER_NEW , null , BasePeer::TYPE_PHPNAME );
		}

		if ($new_name) {
			$cloned->setName( $new_name );
		}
		$cloned->setConfFile( $this->getConfFile());
		$cloned->setConfFile2( $this->getConfFile2());
		$cloned->setConfFileConfig($this->getConfFileConfig());
		$cloned->save(null, true);

		return $cloned;
	}

	public function getSwfNameFromType ()
	{
		$name = @self::$swf_names [ $this->getObjType()];
		if($name)
			return $name;
		return "";
	}
	
	public function getDirectoryFromType()
	{
		if(isset(self::$swf_directory_map[$this->getObjType()]))
			return self::$swf_directory_map[$this->getObjType()];
			
		return "";
	}

	public function getDirectoryMap ()
	{
		return self::$swf_directory_map;
	}

	public function getSwfNames()
	{
		return self::$swf_names;
	}



	public function getAutoplay ()	{		return $this->getFromCustomData( "autoplay" , null , false );	}
	public function setAutoplay( $v )	{		return $this->putInCustomData( "autoplay", $v );	}

	public function getAutomuted ()	{		return $this->getFromCustomData( "automuted" , null , false );	}
	public function setAutomuted( $v )	{		return $this->putInCustomData( "automuted", $v );	}

	public function getCacheInvalidationKeys()
	{
		return array("uiConf:id=".$this->getId(), "uiConf:partnerId=".$this->getPartnerId());
	}
}
