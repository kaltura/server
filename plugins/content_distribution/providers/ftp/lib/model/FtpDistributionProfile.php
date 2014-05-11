<?php
/**
 * @package plugins.ftpDistribution
 * @subpackage model
 */
class FtpDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_PROTOCOL = 'protocol';
	const CUSTOM_DATA_HOST = 'host';
	const CUSTOM_DATA_PORT = 'port';
	const CUSTOM_DATA_BASE_PATH = 'basePath';
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_SFTP_PUBLIC_KEY = 'sftpPublicKey';
	const CUSTOM_DATA_SFTP_PRIVATE_KEY = 'sftpPrivateKey';
    const CUSTOM_DATA_PASSPHRASE = 'passphrase';
	const CUSTOM_DATA_DISABLE_METADATA = 'disableMetadata';
	const CUSTOM_DATA_METADATA_XSLT = 'metadataXslt';
	const CUSTOM_DATA_METADATA_FILENAME_XSLT = 'metadataFilenameXslt';
	const CUSTOM_DATA_FLAVOR_ASSET_FILENAME_XSLT = 'flavorAssetFilenameXslt';
	const CUSTOM_DATA_THUMBNAIL_ASSET_FILENAME_XSLT = 'thumbnailAssetFilenameXslt';
	const CUSTOM_DATA_ASSET_FILENAME_XSLT = 'assetFilenameXslt';
	const CUSTOM_DATA_ASPERA_PUBLIC_KEY = 'asperaPublicKey';
	const CUSTOM_DATA_ASPERA_PRIVATE_KEY = 'asperaPrivateKey';
	const CUSTOM_DATA_SEND_METADATA_AFTER_ASSETS = 'sendMetadataAfterAssets';
	
	protected $maxLengthValidation= array (
	);
	
	protected $inListOrNullValidation = array (
	);
	
	protected function getDefaultFieldConfigArray()
	{
		return parent::getDefaultFieldConfigArray();
	}

	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);
		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!is_array($allFieldValues))
			$allFieldValues = array();
		$validationErrors = array_merge($validationErrors, $this->validateMaxLength($this->maxLengthValidation, $allFieldValues, $action));
		$validationErrors = array_merge($validationErrors, $this->validateInListOrNull($this->inListOrNullValidation, $allFieldValues, $action));

		return $validationErrors;
	}
	
	public function getFlavorAssetFilename(EntryDistribution $entryDistribution, $defaultFilename, $flavorAssetId)
	{
		if ($this->getFlavorAssetFilenameXslt())
			return trim($this->transformXslForEntry($entryDistribution, $this->getFlavorAssetFilenameXslt(), array('flavorAssetId' => $flavorAssetId)));
		else
			return $defaultFilename;
	}
	
	public function getThumbnailAssetFilename(EntryDistribution $entryDistribution, $defaultFilename, $thumbnailAssetId)
	{
		if ($this->getThumbnailAssetFilenameXslt())
			return trim($this->transformXslForEntry($entryDistribution, $this->getThumbnailAssetFilenameXslt(), array('thumbnailAssetId' => $thumbnailAssetId)));
		else
			return $defaultFilename;
	}
	
	public function getAssetFilename(EntryDistribution $entryDistribution, $defaultFilename, $thumbnailAssetId)
	{
		if ($this->getAssetFilenameXslt())
			return trim($this->transformXslForEntry($entryDistribution, $this->getAssetFilenameXslt(), array('assetId' => $thumbnailAssetId)));
		else
			return $defaultFilename;
	}
	
	public function getMetadataFilename(EntryDistribution $entryDistribution)
	{
		if ($this->getMetadataFilenameXslt())
			return trim($this->transformXslForEntry($entryDistribution, $this->getMetadataFilenameXslt()));
		else
			return $entryDistribution->getEntryId() . '_metadata.xml';
	}
	
	public function getMetadataXml(EntryDistribution $entryDistribution)
	{
		if ($this->getMetadataXslt())
		{
			return $this->transformXslForEntry($entryDistribution, $this->getMetadataXslt());
		}
		else 
		{
			$mrssDoc = $this->getEntryMrssDoc($entryDistribution);
			return $mrssDoc->saveXML();
		}
	}
	
	public function transformXslForEntry(EntryDistribution $entryDistribution, $xsl, $xslParams = array())
	{
		$xslParams['entryDistributionId'] = $entryDistribution->getId();
		$xslParams['distributionProfileId'] = $entryDistribution->getDistributionProfileId();
		
		$mrssDoc = $this->getEntryMrssDoc($entryDistribution);
		
		$xslDoc = new DOMDocument();
		$xslDoc->loadXML($xsl);
		
		$xslt = new XSLTProcessor;
		$xslt->registerPHPFunctions(); // it is safe to register all php fuctions here
		$xslt->setParameter('', $xslParams);
		$xslt->importStyleSheet($xslDoc);
		
		return $xslt->transformToXml($mrssDoc);
	}
	
	public function getEntryMrssDoc(EntryDistribution $entryDistribution)
	{
		$entry = entryPeer::retrieveByPK($entryDistribution->getEntryId());
				
		// set the default criteria to use the current entry distribution partner id (it is restored later)
		// this is needed for related entries under kMetadataMrssManager which is using retrieveByPK without the correct partner id filter
		$oldEntryCriteria = entryPeer::getCriteriaFilter()->getFilter();
		myPartnerUtils::resetPartnerFilter('entry');
		myPartnerUtils::addPartnerToCriteria('entry', $this->getPartnerId(), true);
		
		try
		{
    		$mrss = null;
    		$mrssParams = new kMrssParameters();
    		if ($this->getItemXpathsToExtend())
    			$mrssParams->setItemXpathsToExtend($this->getItemXpathsToExtend());
    		$mrss = kMrssManager::getEntryMrssXml($entry, $mrss, $mrssParams);
    		$mrssStr = $mrss->asXML();
		}
		catch (Exception $e)
		{
		    // restore the original criteria so it will not get stuck due to the exception
		    entryPeer::getCriteriaFilter()->setFilter($oldEntryCriteria);
		    throw $e;
		}
		
		// restore the original criteria
		entryPeer::getCriteriaFilter()->setFilter($oldEntryCriteria);
		
		$mrssObj = new DOMDocument();
        if(!$mrssObj->loadXML($mrssStr))
		    throw new Exception('Entry mrss xml is not valid');
		    
		return $mrssObj;
	}
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		if ($this->getProviderType() == FtpDistributionPlugin::getDistributionProviderTypeCoreValue(FtpDistributionProviderType::FTP_SCHEDULED))
			return FtpScheduledDistributionPlugin::getProvider();
		else
			return FtpDistributionPlugin::getProvider();
	}
	
	public function getProtocol()						{return $this->getFromCustomData(self::CUSTOM_DATA_PROTOCOL);}
	public function getHost()							{return $this->getFromCustomData(self::CUSTOM_DATA_HOST);}
	public function getPort()							{return $this->getFromCustomData(self::CUSTOM_DATA_PORT);}
	public function getBasePath()						{return $this->getFromCustomData(self::CUSTOM_DATA_BASE_PATH);}
	public function getUsername()						{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()						{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
    public function getPassphrase()					    {return $this->getFromCustomData(self::CUSTOM_DATA_PASSPHRASE);}
	public function getSftpPublicKey()					{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_PUBLIC_KEY);}
	public function getSftpPrivateKey()					{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_PRIVATE_KEY);}
	public function getDisableMetadata()				{return $this->getFromCustomData(self::CUSTOM_DATA_DISABLE_METADATA);}
	public function getMetadataXslt()					{return $this->getFromCustomData(self::CUSTOM_DATA_METADATA_XSLT);}
	public function getMetadataFilenameXslt()			{return $this->getFromCustomData(self::CUSTOM_DATA_METADATA_FILENAME_XSLT);}
	public function getFlavorAssetFilenameXslt()		{return $this->getFromCustomData(self::CUSTOM_DATA_FLAVOR_ASSET_FILENAME_XSLT);}
	public function getThumbnailAssetFilenameXslt()		{return $this->getFromCustomData(self::CUSTOM_DATA_THUMBNAIL_ASSET_FILENAME_XSLT);}
	public function getAssetFilenameXslt()				{return $this->getFromCustomData(self::CUSTOM_DATA_ASSET_FILENAME_XSLT);}
	public function getAsperaPublicKey()				{return $this->getFromCustomData(self::CUSTOM_DATA_ASPERA_PUBLIC_KEY);}
	public function getAsperaPrivateKey()				{return $this->getFromCustomData(self::CUSTOM_DATA_ASPERA_PRIVATE_KEY);}
	public function getSendMetadataAfterAssets()		{return $this->getFromCustomData(self::CUSTOM_DATA_SEND_METADATA_AFTER_ASSETS);}
	
	public function setProtocol($v)						{$this->putInCustomData(self::CUSTOM_DATA_PROTOCOL, $v);}
	public function setHost($v)							{$this->putInCustomData(self::CUSTOM_DATA_HOST, $v);}
	public function setPort($v)							{$this->putInCustomData(self::CUSTOM_DATA_PORT, $v);}
	public function setBasePath($v)						{$this->putInCustomData(self::CUSTOM_DATA_BASE_PATH, $v);}
	public function setUsername($v)						{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
    public function setPassword($v)						{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
    public function setPassphrase($v)				    {$this->putInCustomData(self::CUSTOM_DATA_PASSPHRASE, $v);}
    public function setSftpPublicKey($v)				{$this->putInCustomData(self::CUSTOM_DATA_SFTP_PUBLIC_KEY, $v);}
    public function setSftpPrivateKey($v)				{$this->putInCustomData(self::CUSTOM_DATA_SFTP_PRIVATE_KEY, $v);}
	public function setDisableMetadata($v)				{$this->putInCustomData(self::CUSTOM_DATA_DISABLE_METADATA, $v);}
	public function setMetadataXslt($v)					{$this->putInCustomData(self::CUSTOM_DATA_METADATA_XSLT, $v);}
	public function setMetadataFilenameXslt($v)			{$this->putInCustomData(self::CUSTOM_DATA_METADATA_FILENAME_XSLT, $v);}
	public function setFlavorAssetFilenameXslt($v)		{$this->putInCustomData(self::CUSTOM_DATA_FLAVOR_ASSET_FILENAME_XSLT, $v);}
	public function setThumbnailAssetFilenameXslt($v)	{$this->putInCustomData(self::CUSTOM_DATA_THUMBNAIL_ASSET_FILENAME_XSLT, $v);}
	public function setAssetFilenameXslt($v)			{$this->putInCustomData(self::CUSTOM_DATA_ASSET_FILENAME_XSLT, $v);}
 	public function setAsperaPublicKey($v)				{$this->putInCustomData(self::CUSTOM_DATA_ASPERA_PUBLIC_KEY, $v);}
    public function setAsperaPrivateKey($v)				{$this->putInCustomData(self::CUSTOM_DATA_ASPERA_PRIVATE_KEY, $v);}
	public function setSendMetadataAfterAssets($v)		{$this->putInCustomData(self::CUSTOM_DATA_SEND_METADATA_AFTER_ASSETS, $v);}
}