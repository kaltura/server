<?php
class ComcastDistributionEngine extends DistributionEngine implements 
	IDistributionEngineSubmit,
	IDistributionEngineCloseSubmit, 
	IDistributionEngineUpdate,
	IDistributionEngineDelete,
	IDistributionEngineReport
{
	private static $containerFormatMap = array(
		'flash video' => ComcastFormat::_FLV,
		'mpeg audio' => ComcastFormat::_MP3,
		'windows media' => ComcastFormat::_WM,
		'avi' => ComcastFormat::_AVI,
		'qt' => ComcastFormat::_QT,
		'wave' => ComcastFormat::_WAV,
		'mpeg-ps' => ComcastFormat::_MPEG,
		'm4v' => ComcastFormat::_MPEG4,
		'zip' => ComcastFormat::_ZIP,
		'quicktime' => ComcastFormat::_QT,
		'mpeg video' => ComcastFormat::_MPEG,
		'mp42' => ComcastFormat::_MPEG4,   
		'jpeg' => ComcastFormat::_JPEG,
		'png' => ComcastFormat::_PNG,
		'isom' => ComcastFormat::_MPEG4,
		'3gp4' => ComcastFormat::_3GPP,
		'gif' => ComcastFormat::_GIF,
		'm4vp' => ComcastFormat::_MPEG4, 
		'm4vh' => ComcastFormat::_MPEG4,
		'm4a' => ComcastFormat::_MPEG4,
		'shockwave' => ComcastFormat::_FLV,
		'msnv' => ComcastFormat::_MPEG4,
		'bitmap' => ComcastFormat::_BMP,
		'bdav' => ComcastFormat::_MPEG,
		'3gp6' => ComcastFormat::_3GPP,
		'3g2a' => ComcastFormat::_3GPP2,
		'mpeg-ts' => ComcastFormat::_MPEG,
		'ogg' => ComcastFormat::_OGG,
		'divx' => ComcastFormat::_AVI,
		'3gp5' => ComcastFormat::_3GPP,
		'digital video' => ComcastFormat::_DV,
		'f4v' => ComcastFormat::_FLASH,
		'mpeg-4 visual' => ComcastFormat::_MPEG4,
		'avc1' => ComcastFormat::_MPEG4,
		'cdxa/mpeg-ps' => ComcastFormat::_MPEG,
		'mmp4' => ComcastFormat::_MPEG4,
		'ndss' => ComcastFormat::_MPEG4,
		'mp41' => ComcastFormat::_MPEG4,
		'realmedia' => ComcastFormat::_REAL,
		'adts' => ComcastFormat::_MPEG,
		'gzip' => ComcastFormat::_ZIP,
		'ndsh' => ComcastFormat::_MPEG4,
		'mqt' => ComcastFormat::_QT,
		'ndxp' => ComcastFormat::_MPEG4,
		'3gp7' => ComcastFormat::_3GPP,
		'mxf' => ComcastFormat::_MXF,
		'ndsc' => ComcastFormat::_MPEG4,
		'mpeg-4' => ComcastFormat::_MPEG4,
		'vc-1' => ComcastFormat::_WM,
		'm4vw' => ComcastFormat::_MPEG4,
		'3gr6' => ComcastFormat::_3GPP,
		'3gs6' => ComcastFormat::_3GPP,
		'windows media / windows media' => ComcastFormat::_WM,
	);

	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure(KSchedularTaskConfig $taskConfig)
	{
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaComcastDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaComcastDistributionProfile");
	
		return $this->doSubmit($data, $data->distributionProfile);
	}

	protected function newCustomDataElement($title, $value = '')
	{
		$customDataElement = new ComcastCustomDataElement();
		$customDataElement->title = $title;
		$customDataElement->value = $value;
		return $customDataElement;
	}
	
	private function getFlavorFormat($containerFormat)
	{
		$containerFormat = trim(strtolower($containerFormat));
		if(isset(self::$containerFormatMap[$containerFormat]))
			return self::$containerFormatMap[$containerFormat];
			
		return ComcastFormat::_UNKNOWN;
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaComcastDistributionProfile $distributionProfile
	 * @return ComcastMedia
	 */
	public function getComcastMedia(KalturaBaseEntry $entry, KalturaDistributionJobData $data, KalturaComcastDistributionProfile $distributionProfile)
	{	
		$metadataObjects = $this->getMetadataObjects($data->entryDistribution->partnerId, $data->entryDistribution->entryId);
		
		$media = new ComcastMedia();
		$media->contentType = ComcastContentType::_VIDEO;
		$media->language = ComcastLanguage::_ENGLISH;
		$media->rating = 'G';
		
		$media->album = $distributionProfile->album;
		$media->author = $distributionProfile->author;
		$media->keywords = $distributionProfile->keywords;
		
		$media->airdate = $data->entryDistribution->sunrise;
		$media->availableDate = $data->entryDistribution->sunrise;
		$media->expirationDate = $data->entryDistribution->sunset;
		
		$categories = $this->findMetadataValue($metadataObjects, 'ComcastCategory', true);
		$media->categories = new ComcastArrayOfstring();
		foreach($categories as $category)
			$media->categories[] = $category;
			
		$media->copyright = $this->findMetadataValue($metadataObjects, 'copyright');
		
		$media->formats = new ComcastArrayOfFormat();
		$media->formats = ComcastFormat::_FLV;
		
		$media->externalID = $entry->id;
		$media->length = $entry->duration;
		$media->title = $entry->name;
		$media->description = $entry->description;
		
		$media->customData = new ComcastCustomData();
		$media->customData[] = $this->newCustomDataElement('Headline', $this->findMetadataValue($metadataObjects, 'LongTitle'));
		$media->customData[] = $this->newCustomDataElement('Link Href');
		$media->customData[] = $this->newCustomDataElement('Link Text');
		
		return $media;
	}
	
	public function doSubmit(KalturaDistributionSubmitJobData $data, KalturaComcastDistributionProfile $distributionProfile)
	{	
		$entry = $this->getEntry($data->entryDistribution->partnerId, $data->entryDistribution->entryId);
		$media = $this->getComcastMedia($entry, $data, $distributionProfile);
		if($data->entryDistribution->remoteId)
			$media->ID = $data->entryDistribution->remoteId;
	
		$thumbAssets = $this->getThumbAssets($data->entryDistribution->partnerId, $data->entryDistribution->thumbAssetIds);
		if($thumbAssets && count($thumbAssets))
		{
			foreach($thumbAssets as $thumbAsset)
			{
				if($thumbAsset->width == 72 && $thumbAsset->height = 92)
				{				
					$media->thumbnailURL = $this->getThumbAssetUrl($thumbAsset->id);
					break;
				}
			}
		}
		
		$mediaFiles = array();
		
		$flavorAssets = $this->getFlavorAssets($data->entryDistribution->partnerId, $data->entryDistribution->flavorAssetIds);
		
		// TODO add support to update media files
		if(!$data->entryDistribution->remoteId)
		{
			$this->impersonate($data->entryDistribution->partnerId);
			foreach($flavorAssets as $flavorAsset)
			{
				$url = $this->kalturaClient->flavorAsset->getDownloadUrl($flavorAsset->id, true);
				
				$mediaFile = new ComcastMediaFile();
				$mediaFile->allowRelease = true;
				$mediaFile->bitrate = $flavorAsset->bitrate;
				$mediaFile->contentType = ComcastContentType::_VIDEO;
				$mediaFile->format = $this->getFlavorFormat($flavorAsset->containerFormat);
				$mediaFile->length = $entry->duration;
				$mediaFile->mediaFileType = ComcastMediaFileType::_INTERNAL;
				$mediaFile->originalLocation = "$url/filename/{$flavorAsset->id}";
				$mediaFile->height = $flavorAsset->width;
				$mediaFile->width = $flavorAsset->height;
				$mediaFiles[] = $mediaFile;
			}
			$this->unimpersonate();
		}
		
		$options = new ComcastAddContentOptions();
		$options->generateThumbnail = false;
		$options->publish = false;
		$options->deleteSource = false;

		$comcastMediaService = new ComcastMediaService($distributionProfile->email, $distributionProfile->password);
		
		if($data->entryDistribution->remoteId)
			$comcastAddContentResults = $comcastMediaService->setContent($media, $mediaFiles, $options);
		else
			$comcastAddContentResults = $comcastMediaService->addContent($media, $mediaFiles, $options);
		
		$data->sentData = $comcastMediaService->request;
		$data->results = $comcastMediaService->response;
		
		if($data->entryDistribution->remoteId)
			$data->remoteId = $data->entryDistribution->remoteId;
		elseif($comcastAddContentResults->mediaID)
			$data->remoteId = $comcastAddContentResults->mediaID;
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		$distributionProfile = $data->distributionProfile;
		$comcastMediaService = new ComcastMediaService($distributionProfile->email, $distributionProfile->password);
		
		$template = new ComcastMediaTemplate();
		$template->fields[] = array();
		$template->fields[] = ComcastMediaField::_ID;
		$template->fields[] = ComcastMediaField::_STATUS;
		$template->fields[] = ComcastMediaField::_STATUSDESCRIPTION;
		$template->fields[] = ComcastMediaField::_STATUSDETAIL;
		$template->fields[] = ComcastMediaField::_STATUSMESSAGE;
		
		$query = new ComcastQuery();
		$query->name = 'ByIDs';
		$query->parameterNames = array('IDs');
		$ids = new soapval('item', 'IDSet', array($data->remoteId), false, 'ns12');
		$query->parameterValues = array($ids);
		
		$sort = new ComcastMediaSort();
		$sort->field = ComcastMediaField::_ID;
		$sort->descending = true;
		
		$range = new ComcastRange();
		
		$comcastMediaList = $comcastMediaService->getMedia($template, $query, $sort, $range);
		foreach($comcastMediaList as $comcastMedia)
		{
			if($comcastMedia->ID != $data->remoteId)
				continue;
				
			switch($comcastMedia->status)
			{
				case ComcastStatus::_ERROR:
					throw new Exception("Comcast error description [$comcastMedia->statusDescription] message [$comcastMedia->statusMessage] detail [$comcastMedia->statusDetail]");
								
				case ComcastStatus::_INPROGRESS:
				case ComcastStatus::_RETAINED:
				case ComcastStatus::_UNAPPROVED:
					return false;
								
				case ComcastStatus::_DISABLED:
				case ComcastStatus::_WARNING:
				case ComcastStatus::_OK:
					return true;
			}
		}
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaComcastDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaComcastDistributionProfile");
	
		return $this->doUpdate($data, $data->distributionProfile);
	}
	
	public function doUpdate(KalturaDistributionUpdateJobData $data, KalturaComcastDistributionProfile $distributionProfile)
	{
		$entry = $this->getEntry($data->entryDistribution->partnerId, $data->entryDistribution->entryId);
		$media = $this->getComcastMedia($entry, $data, $distributionProfile);
		$media->ID = $data->remoteId;
	
		$mediaFiles = array();
		
		$options = new ComcastAddContentOptions();
		$options->generateThumbnail = false;
		$options->publish = false;
		$options->deleteSource = false;

		$comcastMediaService = new ComcastMediaService($distributionProfile->email, $distributionProfile->password);
		$comcastAddContentResults = $comcastMediaService->setContent($media, $mediaFiles, $options);
		
		$data->sentData = $comcastMediaService->request;
		$data->results = $comcastMediaService->response;
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		$distributionProfile = $data->distributionProfile;
		$comcastMediaService = new ComcastMediaService($distributionProfile->email, $distributionProfile->password);
		
		$ids = array($data->remoteId);
		$comcastMediaService->deleteMedia($ids);
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineReport::fetchReport()
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data)
	{
		// TODO
	}
}