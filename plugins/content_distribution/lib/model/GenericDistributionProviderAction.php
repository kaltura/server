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
	
	/**
	 * @param int $sub_type
	 * @throws string
	 */
	private static function getFileSyncVersion($sub_type)
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
			$version = self::getFileSyncversion($sub_type);
		
		$key = new FileSyncKey();
		$key->object_type = FileSyncObjectType::METADATA;
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
			$version = self::getFileSyncversion($sub_type);
		
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
			$version = self::getFileSyncversion($sub_type);
	
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

	/* (non-PHPdoc)
	 * @see ISyncableFile::getFileSync()
	 */
	public function getFileSync()
	{
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see ISyncableFile::setFileSync()
	 */
	public function setFileSync(FileSync $file_sync)
	{
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see IBaseObject::getPartnerId()
	 */
	public function getPartnerId()
	{
		// TODO Auto-generated method stub
		
	}

	public function getMrssTransformerVersion()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_MRSS_TRANSFORMER_VERSION);}
	public function getMrssValidatorVersion()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_MRSS_VALIDATOR_VERSION);}
	public function getResultsTransformerVersion()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_RESULTS_TRANSFORMER_VERSION);}
	
	public function imcrementMrssTransformerVersion($v)		{return $this->incInCustomData(self::CUSTOM_DATA_FIELD_MRSS_TRANSFORMER_VERSION);}
	public function imcrementMrssValidatorVersion($v)		{return $this->incInCustomData(self::CUSTOM_DATA_FIELD_MRSS_VALIDATOR_VERSION);}
	public function imcrementResultsTransformerVersion($v)	{return $this->incInCustomData(self::CUSTOM_DATA_FIELD_RESULTS_TRANSFORMER_VERSION);}
}
