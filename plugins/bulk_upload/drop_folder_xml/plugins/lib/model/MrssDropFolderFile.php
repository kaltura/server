<?php
/**
 * @package plugins.DropFolderMrss
 * @subpackage model
 */
class MrssDropFolderFile extends DropFolderFile implements ISyncableFile
{
	const FILE_SYNC_SUB_TYPE_MRSS_XML = 1;
	const CUSTOM_DATA_MRSS_CONTENT_VERSION = 'mrssContentVersion';
	
	/**
	 * @var string
	 */
	protected $hash;
	
	/**
	 * @var string
	 */
	protected $mrssContent;
	
	/**
	 * @var string
	 */
	protected $setMrssContent;
	
	/**
	 * @return the $mrssContent
	 */
	public function getMrssContent() {
		if($this->mrssContent)
			return $this->mrssContent;
			
		$key = $this->getSyncKey(self::FILE_SYNC_SUB_TYPE_MRSS_XML);
		$this->mrssContent = kFileSyncUtils::file_get_contents($key, true, false);
		return $this->mrssContent;
	}

	/**
	 * @param string $mrssContent
	 */
	public function setMrssContent($mrssContent) {
		$this->getMrssContent();
		if($mrssContent != $this->mrssContent)
			$this->setMrssContent = $mrssContent;
	}
	
	public function incrementMrssContentVersion()
	{
		$this->mrssContentPreviousVersion = $this->getMrssContentVersion();
		$version = kDataCenterMgr::incrementVersion($this->getMrssContentVersion());
		return $this->putInCustomData(self::CUSTOM_DATA_MRSS_CONTENT_VERSION, $version);
	}
	
	public function getMrssContentVersion ()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_MRSS_CONTENT_VERSION);
	}

	/**
	 * @return string
	 */
	public function getHash() {
		return $this->getFromCustomData('hash');
	}

	/**
	 * @param string $hash
	 */
	public function setHash($hash) {
		$this->putInCustomData('hash', $hash);
	}
	
	/**
	 * @var int
	 */
	protected $mrssContentPreviousVersion = null;
	
	/* (non-PHPdoc)
	 * @see ISyncableFile::getSyncKey()
	 */
	public function getSyncKey($sub_type, $version = null) {
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getFileSyncVersion($sub_type);
		
		$key = new FileSyncKey();
		$key->object_type = DropFolderMrssPlugin::getDropFolderFileFileSyncObjectTypeCoreValue(MrssDropFolderFileFileSyncObjectType::MRSS_DROP_FOLDER_FILE);
		$key->object_sub_type = $sub_type;
		$key->object_id = $this->getId();
		$key->version = $version;
		$key->partner_id = $this->getPartnerId();
		
		return $key;
		
	}

	/* (non-PHPdoc)
	 * @see ISyncableFile::generateFilePathArr()
	 */
	public function generateFilePathArr($sub_type, $version = null) {
		self::validateFileSyncSubType ( $sub_type );
		
		if(!$version)
			$version = $this->getFileSyncVersion($sub_type);
		
		$dir = (intval($this->getId() / 1000000)) . '/' . (intval($this->getId() / 1000) % 1000);
		$path =  "/content/dropFolderFile/$dir/" . $this->generateFileName($sub_type, $version);

		return array(myContentStorage::getFSContentRootPath(), $path); 
	}

	/* (non-PHPdoc)
	 * @see ISyncableFile::generateFileName()
	 */
	public function generateFileName($sub_type, $version = null) {
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getVersion();
			
		return $this->getId(). "_{$version}.xml";
		
	}
	
	/**
	 * @param int $sub_type
	 * @throws FileSyncException
	 */
	private static function validateFileSyncSubType($sub_type)
	{
		$valid_sub_types = array(
			self::FILE_SYNC_SUB_TYPE_MRSS_XML,
		);
		
		if(! in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(MrssDropFolderFileFileSyncObjectType::MRSS_DROP_FOLDER_FILE, $sub_type, $valid_sub_types);
	}

	/**
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
	
/**
	 * @param int $sub_type
	 * @throws string
	 */
	private function getFileSyncVersion($sub_type)
	{
		switch($sub_type)
		{
			case self::FILE_SYNC_SUB_TYPE_MRSS_XML:
				return $this->getMrssContentVersion();
		}
		return null;
	}
	
/* (non-PHPdoc)
	 * @see BaseEventNotificationTemplate::preSave()
	 */
	public function preSave(PropelPDO $con = null)
	{
		if($this->setMrssContent)
			$this->incrementMrssContentVersion();
			
		return parent::preSave($con);
	}

	/* (non-PHPdoc)
	 * @see BaseEventNotificationTemplate::postSave()
	 */
	public function postSave(PropelPDO $con = null)
	{
		if($this->wasObjectSaved() && $this->setMrssContent)
		{
			$key = $this->getSyncKey(self::FILE_SYNC_SUB_TYPE_MRSS_XML);
			kFileSyncUtils::file_put_contents($key, $this->setMrssContent);
			$this->mrssContent = $this->setMrssContent;
			$this->setMrssContent = null;
			
			kEventsManager::raiseEvent(new kObjectDataChangedEvent($this, $this->mrssContentPreviousVersion));	
		}
		
		return parent::postSave($con);
	}

}