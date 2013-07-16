<?php
/**
 * @package plugins.exampleDistribution
 * @subpackage lib
 */
class ExampleDistributionEngine extends DistributionEngine implements 
	IDistributionEngineUpdate,
	IDistributionEngineSubmit,
	IDistributionEngineReport,
	IDistributionEngineDelete,
	IDistributionEngineCloseSubmit
{
	const FTP_SERVER_URL = 'example.ftp.com';
	
	/**
	 * Demonstrate using batch configuration
	 * Contains the path to the update XML template file
	 * @var string
	 */
	private $updateXmlTemplate;
	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure()
	{
		parent::configure();
		
		// set default value
		$this->updateXmlTemplate = dirname(__FILE__) . '/../xml/update.template.xml';
		
		// load value from batch configuration
		if(KBatchBase::$taskConfig->params->updateXmlTemplate)
			$this->updateXmlTemplate = KBatchBase::$taskConfig->params->updateXmlTemplate;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 * 
	 * Demonstrate asynchronous external API usage
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		// validates received object types
				
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaExampleDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaExampleDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaExampleDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaExampleDistributionJobProviderData");
		
		// call the actual submit action
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		// always return false to be closed asynchronously by the closer
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		return ExampleExternalApiService::wasSubmitSucceed($data->remoteId);
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		// demonstrate asynchronous XML delivery usage from XSL
		
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaExampleDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaExampleDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaExampleDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaExampleDistributionJobProviderData");
		
		$this->handleDelete($data, $data->distributionProfile, $data->providerData);
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 * 
	 * demonstrate asynchronous XML delivery usage from template and uploading the media
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaExampleDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaExampleDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaExampleDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaExampleDistributionJobProviderData");
		
		$this->handleUpdate($data, $data->distributionProfile, $data->providerData);
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineReport::fetchReport()
	 * 
	 * Demonstrate asynchronous http url parsing
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data)
	{
		// TODO
		return false;
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaExampleDistributionProfile $distributionProfile
	 * @param KalturaExampleDistributionJobProviderData $providerData
	 */
	protected function handleSubmit(KalturaDistributionJobData $data, KalturaExampleDistributionProfile $distributionProfile, KalturaExampleDistributionJobProviderData $providerData)
	{
		$entryId = $data->entryDistribution->entryId;
		$partnerId = $distributionProfile->partnerId;
		$entry = $this->getEntry($partnerId, $entryId);

		// populate the external API object with the Kaltura entry data
		$exampleExternalApiMediaItem = new ExampleExternalApiMediaItem();
		$exampleExternalApiMediaItem->resourceId = $entry->id;
		$exampleExternalApiMediaItem->title = $entry->name;
		$exampleExternalApiMediaItem->description = $entry->description;
		$exampleExternalApiMediaItem->width = $entry->width;
		$exampleExternalApiMediaItem->height = $entry->height;
				
		// loads ftp manager
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
		$ftpManager = kFileTransferMgr::getInstance(kFileTransferMgrType::FTP, $engineOptions);
		$ftpManager->login(self::FTP_SERVER_URL, $distributionProfile->username, $distributionProfile->password);
		
		// put the thumbnail on the FTP with the entry id as naming convention
		$remoteFile = $entry->id . '.jpg';
		$ftpManager->putFile($remoteFile, $providerData->thumbAssetFilePath);
		
		// put the video files on the FTP with the entry id as naming convention and index
		foreach($providerData->videoAssetFilePaths as $index => $videoAssetFilePath)
		{
			$localPath = $videoAssetFilePath->path;
			$pathInfo = pathinfo($localPath);
    		$fileExtension = $pathInfo['extension'];
    		
			$remoteFile = "{$entry->id}-{$index}.{$fileExtension}";
			$ftpManager->putFile($remoteFile, $localPath);
		}
		
		$remoteId = ExampleExternalApiService::submit($exampleExternalApiMediaItem);
		$data->remoteId = $remoteId;
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaExampleDistributionProfile $distributionProfile
	 * @param KalturaExampleDistributionJobProviderData $providerData
	 */
	protected function handleDelete(KalturaDistributionJobData $data, KalturaExampleDistributionProfile $distributionProfile, KalturaExampleDistributionJobProviderData $providerData)
	{
		// TODO
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaExampleDistributionProfile $distributionProfile
	 * @param KalturaExampleDistributionJobProviderData $providerData
	 */
	protected function handleUpdate(KalturaDistributionJobData $data, KalturaExampleDistributionProfile $distributionProfile, KalturaExampleDistributionJobProviderData $providerData)
	{
		$entryId = $data->entryDistribution->entryId;
		$partnerId = $distributionProfile->partnerId;
		$entry = $this->getEntry($partnerId, $entryId);
		
		$feed = new KDOMDocument();
		$feed->load($this->updateXmlTemplate);
		$feed->documentElement->setAttribute('mediaId', $data->remoteId);
		
		$nodes = array(
			'title' => 'name',
			'description' => 'description',
			'width' => 'width',
			'height' => 'height',
		);
		foreach($nodes as $nodeName => $entryAttribute)
		{
			$nodeElements = $feed->getElementsByTagName($nodeName);
			foreach($nodeElements as $nodeElement)
				$nodeElement->textContent = $entry->$entryAttribute;
		}
	
		// get the first asset id
		$thumbAssetIds = explode(',', $data->entryDistribution->thumbAssetIds);
		$thumbAssetId = reset($thumbAssetIds);
		$thumbElements = $feed->getElementsByTagName('thumb');
		$thumbElement = reset($thumbElements);
		$thumbElement->textContent = $this->getThumbAssetUrl($thumbAssetId);
			
		$videosElements = $feed->getElementsByTagName('videos');
		$videosElement = reset($videosElements);
	
		$flavorAssets = $this->getFlavorAssets($partnerId, $data->entryDistribution->flavorAssetIds);
		KBatchBase::impersonate($partnerId);
		foreach($flavorAssets as $flavorAsset)
		{
			$url = $this->getFlavorAssetUrl($flavorAsset->id);
			
			$videoElement = $feed->createElement('video');
			$videoElement->textContent = $url;
			$videosElement->appendChild($videoElement);
		}
		KBatchBase::unimpersonate();
			
			
		$localFile = tempnam(sys_get_temp_dir(), 'example-update-');
		$feed->save($localFile);
		
		// loads ftp manager
		$engineOptions = isset(kBatchBase::$taskConfig->engineOptions) ? kBatchBase::$taskConfig->engineOptions->toArray() : array();
		$ftpManager = kFileTransferMgr::getInstance(kFileTransferMgrType::FTP, $engineOptions);
		$ftpManager->login(self::FTP_SERVER_URL, $distributionProfile->username, $distributionProfile->password);
		
		// put the XML file on the FTP
		$remoteFile = $entryId . '.xml';
		$ftpManager->putFile($remoteFile, $localFile);
		
		return true;
	}
}