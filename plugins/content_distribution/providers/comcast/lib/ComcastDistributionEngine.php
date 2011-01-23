<?php
class ComcastDistributionEngine extends DistributionEngine implements 
	IDistributionEngineSubmit,
	IDistributionEngineCloseSubmit
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
	
		if(!$data->providerData || !($data->providerData instanceof KalturaComcastDistributionJobProviderData))
			throw new Exception("Provider data must be of type KalturaComcastDistributionJobProviderData");
		
		return false;
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
	
	public function doSubmit(KalturaDistributionSubmitJobData $data, KalturaComcastDistributionProfile $distributionProfile, KalturaComcastDistributionJobProviderData $providerData)
	{	
		$entry = $this->getEntry($data->entryDistribution->entryId);
		$metadataObjects = $this->getMetadataObjects($data->entryDistribution->entryId);
		
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
		
		$thumbAssets = $this->getThumbAssets($data->entryDistribution->thumbAssetIds);
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
		
		$mediaFiles = new ComcastMediaFileList();
		
		$flavorAssets = $this->getFlavorAssets($data->entryDistribution->flavorAssetIds);
		foreach($flavorAssets as $flavorAsset)
		{
			$url = $this->kalturaClient->flavorAsset->getDownloadUrl($flavorAsset->id);
			
			$mediaFile = new ComcastMediaFile();
			$mediaFile->allowRelease = true;
			$mediaFile->bitrate = $flavorAsset->bitrate;
			$mediaFile->contentType = ComcastContentType::_VIDEO;
			$mediaFile->format = $this->getFlavorFormat($flavorAsset->containerFormat);
			$mediaFile->length = $entry->duration;
			$mediaFile->mediaFileType = ComcastMediaFileType::_INTERNAL;
			$mediaFile->originalLocation = $url;
			$mediaFile->height = $flavorAsset->width;
			$mediaFile->width = $flavorAsset->height;
			$mediaFiles[] = $mediaFile;
		}
		
		$options = new ComcastAddContentOptions();
		$options->generateThumbnail = false;
		$options->publish = false;
		$options->deleteSource = false;

		$comcastMediaService = new ComcastMediaService($distributionProfile->email, $distributionProfile->password);
		$comcastAddContentResults = $comcastMediaService->addContent($media, $mediaFiles, $options);
		
		$data->sentData = $comcastMediaService->request;
		$data->results = $comcastMediaService->response;
		
		if($comcastAddContentResults->mediaID)
		{
			$data->remoteId = $comcastAddContentResults->mediaID;
		}
		
		if(isset($comcastAddContentResults->faultcode) || isset($comcastAddContentResults->faultstring))
		{
			$err = "addContent failed with code [$comcastAddContentResults->faultcode] and message [$comcastAddContentResults->faultstring]";
			KalturaLog::err($err);
			throw new Exception($err);
		}
		
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
		$query = new ComcastQuery();
		$query->parameterNames = new ComcastArrayOfstring();
		$query->parameterNames[] = 'ID';
		$query->parameterValues = new ComcastArrayOfstring();
		$query->parameterValues[] = $data->remoteId;
		$sort = new ComcastMediaSort();
		$range = new ComcastRange();
		
		$comcastMediaList = $comcastMediaService->getMedia($template, $query, $sort, $range);
		
	}
}