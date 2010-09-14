<?php

class createEntryObject {
	private $partner;
	private $partner_id;
	
	function __construct($partner_id) {
		$this->partner_id = $partner_id;
		
		ini_set ( "memory_limit", "1256M" );
		
		define ( 'ROOT_DIR', realpath ( dirname ( __FILE__ ) . '/../../' ) );
		require_once (ROOT_DIR . '/infra/bootstrap_base.php');
		require_once (ROOT_DIR . '/infra/KAutoloader.php');
		
		KAutoloader::addClassPath ( KAutoloader::buildPath ( KALTURA_ROOT_PATH, "vendor", "propel", "*" ) );
		KAutoloader::addClassPath ( KAutoloader::buildPath ( KALTURA_ROOT_PATH, "plugins", "metadata", "*" ) );
		KAutoloader::setClassMapFilePath ( '../cache/classMap.cache' );
		KAutoloader::register ();
		
		error_reporting ( E_ALL );
		//KalturaLog::setLogger(new KalturaStdoutLogger());
		

		$dbConf = kConf::getDB ();
		DbManager::setConfig ( $dbConf );
		DbManager::initialize ();
		
		$this->partner = PartnerPeer::retrieveByPK ( $this->partner_id );
		if(!$this->partner) throw new Exception("could not load partner $partner_id");
		
		myPartnerUtils::addPartnerToCriteria ( new categoryPeer() , $this->partner_id  );
	}
	
	public static function createThumbnailFromUrl($url, $entry) {
		if(!$url) return;
		$entry->setThumbnail ( ".jpg" ); // this will increase the thumbnail version
		$entry->save ();
		
		$fileSyncKey = $entry->getSyncKey ( entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB );
		$fileSync = FileSync::createForFileSyncKey ( $fileSyncKey );
		kFileSyncUtils::file_put_contents ( $fileSyncKey, file_get_contents ( $url ) );

		/* make sure folders and files have correct permissions */
		$file_path = kFileSyncUtils::getLocalFilePathForKey($fileSyncKey);
		echo PHP_EOL.'     fixed permissions on file: '.$file_path;
		@chmod($file_path, 0777);
		@chmod(pathinfo($file_path,PATHINFO_DIRNAME), 0777);
		$back_folder = substr($file_path,0,strrpos(pathinfo($file_path,PATHINFO_DIRNAME),'/'));
		echo PHP_EOL.'     fixed permissions on folder: '.$back_folder;
		@chmod(pathinfo($back_folder,PATHINFO_DIRNAME), 0777);
		
		$wrapper = objectWrapperBase::getWrapperClass ( $entry );
		$wrapper->removeFromCache ( "entry", $entry->getId () );
	}

	function createEntry($name, $description, $file_name, $convartionProfile, $tags, $categories, $thumb_url, $width, $height, $duration)
	{
		if(!$file_name || !file_exists($file_name)) 
		{
			echo PHP_EOL.'file missing ['.$file_name.'] - not creating entry ['.$name.']'.PHP_EOL;
			return;
		}
		$entry = new entry ();
		// static
		$entry->setType ( 1 );
		$entry->setMediaType ( 1 );
		$entry->setStatus ( 2 );
		$entry->setAccessControlId ( $this->partner->getDefaultAccessControlId () );
		
		// dynamic
		$entry->setPartnerId ( $this->partner_id );
//		$entry->setWidth($width);
//		$entry->setHeight($height);
		$entry->setDimensions($width,$height);
//		$entry->setDuration($duration);	
		$entry->setLengthInMsecs($duration);
		$entry->setCategories($categories);
		$entry->setName ( $name );
		$entry->setDescription ( $description );
		$entry->setSourceLink ( $file_name );
		$entry->setConversionQuality ( $convartionProfile );
		$entry->setTags ( $tags );
		
		$entry->save ();
		
		echo PHP_EOL.'entry created ID ['.$entry->getId ().']';
		$changed_entry = self::createThumbnailFromUrl ( $thumb_url, $entry );
		
		return $entry;
		
	}
	
	function createFlavorAsset($file_name, $isOriginal, entry $entry, $flavor_params_id, $width, $height, $bitrate, $frame_rate, $flavorTags, $container, $codec)
	{
		$fa = new flavorAsset ();
		$fa->setStatus ( 2 );
		$fa->setVersion ( 1 );
		$fa->setIsOriginal ( $isOriginal );
		$fa->setPartnerId ( $this->partner_id );
		$fa->setEntryId ( $entry->getId () );
		$fa->setFlavorParamsId ( $flavor_params_id );
		
		$fa->setTags ( $flavorTags ); // make sure OK
		$fa->setFileExt ( pathinfo($file_name, PATHINFO_EXTENSION) ); // will be set by extractMedia
		$fa->setContainerFormat ( $container ); // will be set by extractMedia
		$fa->setVideoCodecId ( $codec ); // will be set by extractMedia
		$fa->setWidth($width);
		$fa->setHeight($height);
		$fa->setBitrate($bitrate);
		$fa->setFrameRate($frame_rate);
		$fa->save ();
		
		echo PHP_EOL . '     flavor asset created ID ['.$fa->getId ().']';
		
		$fa_fs_key = $fa->getSyncKey ( flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET );
		$res = kFileSyncUtils::moveFromFile ( $file_name, $fa_fs_key, true, false ); // eran shpould approve the use of moveFromFile
		/* make sure folders and files have correct permissions */
		$file_path = kFileSyncUtils::getLocalFilePathForKey($fa_fs_key);
		echo PHP_EOL.'          fixed permissions on file: '.$file_path;
		@chmod($file_path, 0777);
		@chmod(pathinfo($file_path,PATHINFO_DIRNAME), 0777);
		$back_folder = substr($file_path,0,strrpos(pathinfo($file_path,PATHINFO_DIRNAME),'/'));
		echo PHP_EOL.'          fixed permissions on folder: '.$back_folder;
		@chmod(pathinfo($back_folder,PATHINFO_DIRNAME), 0777);
	}

}
