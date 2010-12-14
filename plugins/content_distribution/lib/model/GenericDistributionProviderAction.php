<?php


/**
 * Skeleton subclass for representing a row from the 'generic_distribution_provider_action' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class GenericDistributionProviderAction extends BaseGenericDistributionProviderAction implements ISyncableFile 
{
	const FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_TRANSFORMER = 1;
	const FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_VALIDATOR = 2;
	const FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_RESULTS_TRANSFORMER = 3;

	const CUSTOM_DATA_FIELD_MRSS_TRANSFORMER_VERSION = "mrssTransformerVersion";
	const CUSTOM_DATA_FIELD_MRSS_VALIDATOR_VERSION = "mrssValidatorVersion";
	const CUSTOM_DATA_FIELD_RESULTS_TRANSFORMER_VERSION = "resultsTransformerVersion";
	const CUSTOM_DATA_FIELD_FTP_PASSIVE_MODE = "ftpPassiveMode";
	
	/**
	 * @param int $sub_type
	 * @throws string
	 */
	private function getFileSyncVersion($sub_type)
	{
		switch($sub_type)
		{
			case self::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_TRANSFORMER:
				return $this->getMrssTransformerVersion();
				
			case self::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_VALIDATOR:
				return $this->getMrssValidatorVersion();
				
			case self::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_RESULTS_TRANSFORMER:
				return $this->getResultsTransformerVersion();
		}
		return null;
	}
	
	/**
	 * @param int $sub_type
	 * @throws FileSyncException
	 */
	private static function validateFileSyncSubType($sub_type)
	{
		$valid_sub_types = array(
			self::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_TRANSFORMER,
			self::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_VALIDATOR,
			self::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_RESULTS_TRANSFORMER,
		);
		
		if(! in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(ContentDistributionFileSyncObjectType::GENERIC_DISTRIBUTION_ACTION, $sub_type, $valid_sub_types);
	}
	
	/* (non-PHPdoc)
	 * @see ISyncableFile::getSyncKey()
	 */
	public function getSyncKey($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getFileSyncversion($sub_type);
		
		$key = new FileSyncKey();
		$key->object_type = ContentDistributionFileSyncObjectType::get()->coreValue(ContentDistributionFileSyncObjectType::GENERIC_DISTRIBUTION_ACTION);
		$key->object_sub_type = $sub_type;
		$key->object_id = $this->getId();
		$key->version = $version;
		$key->partner_id = $this->getPartnerId();
		
		return $key;
	}

	/* (non-PHPdoc)
	 * @see ISyncableFile::generateFilePathArr()
	 */
	public function generateFilePathArr($sub_type, $version = null)
	{
		self::validateFileSyncSubType ( $sub_type );
		
		if(!$version)
			$version = $this->getFileSyncversion($sub_type);
		
		$dir = (intval($this->getId() / 1000000)) . '/' . (intval($this->getId() / 1000) % 1000);
		$path =  "/content/distribution/generic/$dir/" . $this->generateFileName($sub_type, $version);

		return array(myContentStorage::getFSContentRootPath(), $path); 
	}

	/* (non-PHPdoc)
	 * @see ISyncableFile::generateFileName()
	 */
	public function generateFileName($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getFileSyncversion($sub_type);
	
		$extension = 'txt';
		switch($sub_type)
		{
			case self::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_TRANSFORMER:
			case self::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_RESULTS_TRANSFORMER:
				$extension = 'xsl';
				break;
				
			case self::FILE_SYNC_DISTRIBUTION_PROVIDER_ACTION_MRSS_VALIDATOR:
				$extension = 'xsd';
				break;
				
			default:
				$extension = 'txt';
		}
		
		return $this->getId() . "_{$sub_type}_{$version}.{$extension}";	
	}
	
	/**
	 * @var FileSync
	 */
	private $m_file_sync;

	/* (non-PHPdoc)
	 * @see ISyncableFile::getFileSync()
	 */
	public function getFileSync()
	{
		return $this->m_file_sync; 
	}

	/* (non-PHPdoc)
	 * @see ISyncableFile::setFileSync()
	 */
	public function setFileSync(FileSync $file_sync)
	{
		 $this->m_file_sync = $file_sync;
	}

	public function getFtpPassiveMode()					{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_FTP_PASSIVE_MODE);}
	public function getMrssTransformerVersion()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_MRSS_TRANSFORMER_VERSION);}
	public function getMrssValidatorVersion()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_MRSS_VALIDATOR_VERSION);}
	public function getResultsTransformerVersion()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_RESULTS_TRANSFORMER_VERSION);}

	public function setFtpPassiveMode($v)					{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_FTP_PASSIVE_MODE, $v);}
	
	public function incrementMrssTransformerVersion()		{return $this->incInCustomData(self::CUSTOM_DATA_FIELD_MRSS_TRANSFORMER_VERSION);}
	public function incrementMrssValidatorVersion()			{return $this->incInCustomData(self::CUSTOM_DATA_FIELD_MRSS_VALIDATOR_VERSION);}
	public function incrementResultsTransformerVersion()	{return $this->incInCustomData(self::CUSTOM_DATA_FIELD_RESULTS_TRANSFORMER_VERSION);}
}
