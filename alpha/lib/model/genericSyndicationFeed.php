<?php
/**
 * Subclass for representing a row from the 'syndication_feed' table for type generic syndication.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class genericSyndicationFeed extends syndicationFeed implements ISyncableFile
{
	const FILE_SYNC_MRSS_XSL = 1;
	
	private $xslt;
		
	/* (non-PHPdoc)
	 * @see BasesyndicationFeed::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(syndicationFeedType::KALTURA);
	}
		
	/**
	 * @var FileSync
	 */
	private $m_file_sync;
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#getFileSync()
	 */
	public function getFileSync ( )
	{
		return $this->m_file_sync; 
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#setFileSync()
	 */
	public function setFileSync ( FileSync $file_sync )
	{
		 $this->m_file_sync = $file_sync;
	}
	
	/**
	 * @param int $sub_type
	 * @throws FileSyncException
	 */
	private static function validateFileSyncSubType($sub_type)
	{
		$valid_sub_types = array(
			self::FILE_SYNC_SYNDICATION_FEED_XSLT,
		);
		
		if(! in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(FileSyncObjectType::SYNDICATION_FEED, $sub_type, $valid_sub_types);
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#getSyncKey()
	 */
	public function getSyncKey($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		if(!$version)
			$version = $this->getVersion();
		
		$key = new FileSyncKey();
		$key->object_type = FileSyncObjectType::SYNDICATION_FEED;
		$key->object_sub_type = $sub_type;
		$key->object_id = $this->getId();
		$key->version = $version;
		$key->partner_id = $this->getPartnerId();
		KalturaLog::debug("syndication key version : ".$version);
		return $key;
	}
	
	public function incrementVersion()
	{
		$this->setVersion($this->getVersion() + 1);
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFileName()
	 */
	public function generateFileName($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getVersion();
			
		return $this->getId(). "_" . "version_$version.xml";
	}
	
	public function getVersion()
	{
		return $this->getFromCustomData("version",null,0);
	}
	
	public function setVersion($value)
	{
		$this->putInCustomData("version",$value);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFilePathArr()
	 */
	public function generateFilePathArr($sub_type, $version = null)
	{
		self::validateFileSyncSubType ( $sub_type );
		
		if(!$version)
			$version = $this->getVersion();
		
		$dir = (intval($this->getId() / 1000000)) . '/' . (intval($this->getId() / 1000) % 1000);
		$path =  "/content/syndication/data/$dir/" . $this->generateFileName($sub_type, $version);

		return array(myContentStorage::getFSContentRootPath(), $path); 
	}

	/*
	 * @return string
	 */
	public function getXslt()
	{
		if (!is_null($this->xslt))
			return $this->xslt;

		$key = $this->getSyncKey(self::FILE_SYNC_SYNDICATION_FEED_XSLT);
		$this->xslt = kFileSyncUtils::file_get_contents($key, true, false);
		return $this->xslt;
	}
	
	
}